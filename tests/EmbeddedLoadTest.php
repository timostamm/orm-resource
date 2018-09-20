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


class EmbeddedLoadTest extends TestCase
{

    use DatabaseSetupTrait {
        setUp as setUpDb;
    }


    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     * @throws \Doctrine\ORM\ORMException
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
     */
    public function testEmpty()
    {
        /** @var TestEntity $a */
        $a = $this->em->find(TestEntity::class, 1);

        $this->assertNull($a->getOther());
    }


    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function testRemove()
    {
        /** @var TestEntity $a */
        $a = $this->em->find(TestEntity::class, 1);

        $this->assertNotNull($a->getFile());
    }


}