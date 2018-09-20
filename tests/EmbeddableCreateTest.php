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


class EmbeddableCreateTest extends TestCase
{

    use DatabaseSetupTrait {
        setUp as setUpDb;
    }


    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testStore()
    {
        $e = new TestEntity();
        $e->setFile(new Resource([
            'content' => 'dummy content',
            'filename' => 'dummy-file.txt',
            'mimetype' => 'text/plain'
        ]));
        $this->em->persist($e);
        $this->em->flush();


        $this->em->clear();


        /** @var TestEntity[] $results */
        $results = $this->em->createQueryBuilder()
            ->select('e')
            ->from(TestEntity::class, 'e')
            ->getQuery()
            ->getResult();

        $this->assertCount(1, $results);

        $res = $results[0]->getFile();
        $this->assertEquals('dummy-file.txt', $res->getFilename());

        $this->assertCount(1, iterator_to_array($this->storage->listHashes()));


    }


}