<?php

namespace TS\Web\Resource;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\ToolsException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use ReflectionClass;
use ReflectionException;
use TS\Web\Resource\Entity\EmbeddedResource;
use TS\Web\Resource\Entity\TestEntity;

trait DatabaseSetupTrait
{


    /** @var vfsStreamDirectory */
    protected $vfs;

    /** @var HashStorage */
    protected $storage;

    /** @var EntityManager */
    protected $em;


    protected function getEntityClasses(): array
    {
        return [EmbeddedResource::class, TestEntity::class];
    }


    /**
     * @throws ORMException
     * @throws ToolsException
     * @throws ReflectionException
     */
    protected function setUp()
    {
        $this->vfs = vfsStream::setup('root', null, [
            'hash-storage' => []
        ]);

        $this->setUpEntityManager(... $this->getEntityClasses());

        $this->createSchema();

        $this->setupORMResourceHandler($this->vfs->url() . '/hash-storage');

    }


    /**
     * Shutdown database connection.
     */
    protected function tearDown()
    {
        $this->em->close();
        $this->em = null;
    }


    /**
     * @param mixed ...$useBasedirOfThisClass
     * @throws ORMException
     * @throws ReflectionException
     */
    private function setUpEntityManager(...$useBasedirOfThisClass)
    {
        $entityDirectories = [];

        foreach ($useBasedirOfThisClass as $class) {
            $reflector = new ReflectionClass($class);
            $entityDirectories[] = dirname($reflector->getFileName());
        }

        $this->em = $this->createEntityManager($entityDirectories);
    }


    /**
     * @throws ToolsException
     */
    private function createSchema(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }


    private function setupORMResourceHandler(string $storageDir): void
    {
        $this->storage = new HashStorage($storageDir);
        $subscriber = new ORMResourceHandler($this->storage);
        $this->em->getEventManager()->addEventSubscriber($subscriber);
    }


    /**
     * @param array $entityDirectories
     * @return EntityManager
     * @throws ORMException
     */
    private function createEntityManager(array $entityDirectories): EntityManager
    {
        // Create a simple "default" Doctrine ORM configuration for Annotations
        $isDevMode = true;

        $config = Setup::createAnnotationMetadataConfiguration($entityDirectories, $isDevMode, null, null, false);

        // Database configuration parameters
        $connectionParams = array(
            'url' => $GLOBALS['DB_URL']
        );

        // Obtaining the entity manager
        return EntityManager::create($connectionParams, $config);
    }

}
