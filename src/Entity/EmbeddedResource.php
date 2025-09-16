<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 20.09.18
 * Time: 15:10
 */

namespace TS\Web\Resource\Entity;


use Doctrine\ORM\Mapping as ORM;
use TS\Web\Resource\Exception\StorageLogicException;
use TS\Web\Resource\HashStorage;
use TS\Web\Resource\ORMResourceHandler;
use TS\Web\Resource\ResourceInterface;


#[ORM\Embeddable]
class EmbeddedResource implements ResourceInterface
{


    public static function create(?ResourceInterface $resource): ?EmbeddedResource
    {
        if (is_null($resource)) {
            return null;
        }
        if ($resource instanceof EmbeddedResource) {
            return $resource;
        }
        return new EmbeddedResource($resource);
    }


    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    protected $hash;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $filename;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $mimetype;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected $length;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $lastmodfied;

    #[ORM\Column(type: 'array', name: 'attributes', nullable: true)]
    protected $attributes;


    /** @var bool */
    private $stored = false;

    /** @var ResourceInterface | null */
    protected $volatile;

    /** @var HashStorage */
    protected $storage;


    public function __construct(ResourceInterface $resource)
    {
        $this->volatile = $resource;
        $this->hash = $resource->getHash();
        $this->filename = $resource->getFilename();
        $this->lastmodfied = $resource->getLastModified();
        $this->length = $resource->getLength();
        $this->mimetype = $resource->getMimetype();
        $this->attributes = $resource->getAttributes();
    }


    public function setStorage(HashStorage $storage): void
    {
        $this->storage = $storage;
    }

    public function isStored(): bool
    {
        return $this->stored;
    }

    public function setStored(): void
    {
        $this->stored = true;
    }

    public function isEmpty(): bool
    {
        return is_null($this->hash);
    }


    public function getHash(): string
    {
        return $this->hash;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getLastModified()
    {
        return $this->lastmodfied;
    }

    /**
     * @param null $context
     * @return resource
     */
    public function getStream($context = null)
    {
        if (!$this->volatile) {
            if (!$this->storage) {
                $msg = sprintf("Missing content storage. %s not installed?", ORMResourceHandler::class);
                throw new StorageLogicException($msg);
            }
            $this->volatile = $this->storage->get($this->getHash());
        }
        return $this->volatile->getStream($context);
    }


    public function getAttributes(): array
    {
        return $this->attributes ?? [];
    }


}
