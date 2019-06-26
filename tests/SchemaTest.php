<?php

namespace TS\Web\Resource\Entity;


use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Tools\SchemaValidator;
use PHPUnit\Framework\TestCase;
use TS\Web\Resource\DatabaseSetupTrait;


/**
 * General checks on the database schema.
 * Same as callling vendor/bin/doctrine orm:validate-schema.
 *
 */
class SchemaTest extends TestCase
{

    use DatabaseSetupTrait;


    public function testValidateSchema()
    {
        $validator = new SchemaValidator($this->em);
        $errors = $validator->validateMapping();
        $message = PHP_EOL;
        if (count($errors) > 0) {
            foreach ($errors as $class => $classErrors) {
                $message .= "- " . $class . ":" . PHP_EOL . implode(PHP_EOL, $classErrors) . PHP_EOL . PHP_EOL;
            }
        }
        $this->assertEmpty($errors, $message);
    }


    /**
     * @throws DBALException
     */
    public function testSchemaInSyncWithMetadata()
    {
        $platform = $this->em->getConnection()->getDatabasePlatform();
        $this->assertInstanceOf(AbstractPlatform::class, $platform);

        // Skip check if Platform == Sqlite
        if (!in_array(get_class($platform), array(
            SqlitePlatform::class
        ))) {
            $validator = new SchemaValidator($this->em);
            $this->assertTrue($validator->schemaInSyncWithMetadata(), 'The database schema is not in sync with the current mapping file.');
        }
    }

}
