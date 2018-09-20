<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 20.09.18
 * Time: 16:04
 */

namespace TS\Web\Resource\Entity;

use Doctrine\ORM\Mapping as ORM;
use TS\Web\Resource\ResourceInterface;


/**
 * @ORM\Entity()
 */
class TestEntity
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * Initial value -1: @see https://github.com/doctrine/doctrine2/issues/4584
     */
    private $id = -1;


    /**
     * @ORM\Embedded(class = EmbeddedResource::class )
     */
    private $file;


    /**
     * @ORM\Embedded(class = EmbeddedResource::class )
     */
    private $other;


    public function getId(): int
    {
        return $this->id;
    }


    public function getFile(): ?ResourceInterface
    {
        return $this->file;
    }

    public function setFile(?ResourceInterface $resource): void
    {
        $this->file = EmbeddedResource::create($resource);
    }


    public function getOther(): ?ResourceInterface
    {
        return $this->other;
    }


    public function setOther(?ResourceInterface $other): void
    {
        $this->other = EmbeddedResource::create($other);
    }


}