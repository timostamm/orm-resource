# ORM Resources

[![build](https://github.com/timostamm/orm-resource/workflows/CI/badge.svg)](https://github.com/timostamm/orm-resource/actions?query=workflow:"CI")
![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/timostamm/orm-resource/php)
[![GitHub tag](https://img.shields.io/github/tag/timostamm/orm-resource?include_prereleases=&sort=semver&color=blue)](https://github.com/timostamm/orm-resource/releases/)
[![License](https://img.shields.io/badge/License-MIT-blue)](#license)

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
