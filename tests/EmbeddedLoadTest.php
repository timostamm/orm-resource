<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 20.09.18
 * Time: 16:07
 */

namespace TS\Web\Resource;


use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\ToolsException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use TS\Web\Resource\Entity\TestEntity;


class EmbeddedLoadTest extends TestCase
{

    use DatabaseSetupTrait {
        setUp as setUpDb;
    }


    /**
     * @throws MappingException
     * @throws ORMException
     * @throws ToolsException
     * @throws ReflectionException
     */
    protected function setUp(): void
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
     * @throws ORMException
     */
    public function testEmpty()
    {
        /** @var TestEntity $a */
        $a = $this->em->find(TestEntity::class, 1);

        $this->assertNull($a->getOther());
    }


    /**
     * @throws ORMException
     */
    public function testRemove()
    {
        /** @var TestEntity $a */
        $a = $this->em->find(TestEntity::class, 1);

        $this->assertNotNull($a->getFile());
    }


}
