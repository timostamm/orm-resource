<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 20.09.18
 * Time: 15:10
 */

namespace TS\Web\Resource;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use TS\Web\Resource\Entity\EmbeddedResource;


class ORMResourceHandler implements EventSubscriber
{

    /** @var HashStorage */
    private $storage;

    private $embeddedFieldNamesByClass = [];


    public function __construct(HashStorage $storage)
    {
        $this->storage = $storage;
    }


    /**
     * @return HashStorage
     */
    public function getStorage(): HashStorage
    {
        return $this->storage;
    }


    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
        ];
    }


    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $obj = $eventArgs->getObject();
        $em = $eventArgs->getEntityManager();
        $metadata = $em->getClassMetadata(get_class($obj));
        foreach ($this->findEmbeddedResources($obj, $em) as $fieldName => $embedded) {
            if ($embedded->isEmpty()) {
                // there is no content, clear the field
                // https://github.com/doctrine/doctrine2/issues/4568
                $metadata->setFieldValue($obj, $fieldName, null);
            } else {
                $embedded->setStorage($this->storage);
            }
        }
    }


    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $this->findAndStoreEmbedded($eventArgs);
    }


    public function preUpdate(LifecycleEventArgs $eventArgs): void
    {
        $this->findAndStoreEmbedded($eventArgs);
    }


    private function findAndStoreEmbedded(LifecycleEventArgs $eventArgs): void
    {
        $obj = $eventArgs->getObject();
        $em = $eventArgs->getEntityManager();

        foreach ($this->findEmbeddedResources($obj, $em) as $fieldName => $embedded) {
            if ($embedded->isStored()) {
                continue;
            }
            if (!$this->storage->has($embedded->getHash())) {
                $this->storage->put($embedded);
            }
            $embedded->setStorage($this->storage);
            $embedded->setStored();
        }
    }


    private function findEmbeddedResources($entity, EntityManagerInterface $em): array
    {
        $className = get_class($entity);
        $metadata = $em->getClassMetadata($className);

        $known = array_key_exists($className, $this->embeddedFieldNamesByClass);

        if ($known) {

            $fieldNames = $this->embeddedFieldNamesByClass[$className];

        } else {

            $fieldNames = [];

            foreach ($metadata->embeddedClasses as $fieldName => $mapping) {
                if ($mapping['class'] === EmbeddedResource::class) {
                    $fieldNames[] = $fieldName;
                }
            }

            $this->embeddedFieldNamesByClass[$className] = $fieldNames;

        }


        $embeddedByFieldName = [];
        foreach ($fieldNames as $name) {
            $embedded = $metadata->getFieldValue($entity, $name);
            if ($embedded) {
                $embeddedByFieldName[$name] = $embedded;
            }
        }
        return $embeddedByFieldName;
    }


}
