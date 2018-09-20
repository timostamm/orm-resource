<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 20.09.18
 * Time: 16:07
 */

namespace TS\Web\Resource;


use PHPUnit\Framework\TestCase;
use TS\Web\Resource\Entity\TestEntity;


class EntityWithEmbeddedTest extends TestCase
{

    use DatabaseSetupTrait {
        setUp as setUpDb;
    }


    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\Tools\ToolsException
     * @throws \ReflectionException
     */
    protected function setUp()
    {
        $this->setUpDb();

        $a = new TestEntity();
        $a->setFile(new Resource([
            'content' => 'a',
            'filename' => 'a.txt',
            'mimetype' => 'text/plain'
        ]));

        $this->em->persist($a);
        $this->em->flush($a);
        $this->em->clear();
    }


    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testReplace()
    {
        /** @var TestEntity $a */
        $a = $this->em->find(TestEntity::class, 1);
        $a->setFile(new Resource([
            'content' => 'b',
            'filename' => 'b.txt',
            'mimetype' => 'text/plain'
        ]));
        $this->em->flush();
        $this->em->clear();

        /** @var TestEntity $b */
        $b = $this->em->find(TestEntity::class, 1);
        $this->assertEquals('b.txt', $b->getFile()->getFilename());

        $this->assertCount(2, iterator_to_array($this->storage->listHashes()));
    }


    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testRemove()
    {
        /** @var TestEntity $a */
        $a = $this->em->find(TestEntity::class, 1);
        $a->setFile(null);
        $this->em->flush();
        $this->em->clear();

        /** @var TestEntity $b */
        $b = $this->em->find(TestEntity::class, 1);
        $this->assertNull($b->getFile());

        // files are not removed from fs
        $this->assertCount(1, iterator_to_array($this->storage->listHashes()));
    }


}