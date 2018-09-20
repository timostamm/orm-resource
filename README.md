# ORM Resources

[![Build Status](https://api.travis-ci.org/timostamm/orm-resource.png)](https://travis-ci.org/timostamm/orm-resource)


Doesn't actually store the files in the database, but puts them in a storage directory and references them in the database.

This package uses timostamm/web-resource for file representation. 

Files in the file system are never deleted.


#### Example

```PHP

/** @ORM\Entity() */
class TestEntity
{

    /**
     * @ORM\Embedded(class = EmbeddedResource::class )
     */
    private $file;


    public function getFile(): ?ResourceInterface
    {
        return $this->file;
    }

    public function setFile(?ResourceInterface $resource): void
    {
        $this->file = EmbeddedResource::create($resource);
    }

}


$em->getEventManager()
    ->addEventSubscriber(new ORMResourceHandler(new HashStorage($storageDir)));

$e = new TestEntity();
$e->setFile(Resource::fromFile(__FILE__));

$em->persist($e);
$em->flush($e);


```
