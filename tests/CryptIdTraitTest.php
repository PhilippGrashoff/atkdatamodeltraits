<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits\Tests;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use PhilippR\Atk4\ModelTraits\CryptIdTrait;
use PhilippR\Atk4\ModelTraits\Tests\Testclasses\ModelWithCryptIdTrait;


class CryptIdTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new Sql('sqlite::memory:');
        $this->createMigrator(new ModelWithCryptIdTrait($this->db))->create();
    }

    public function testExceptionOverwriteGenerate(): void
    {
        $modelClass = new class() extends Model {
            use CryptIdTrait;

            public $table = 'sometable';

        };
        $model = new $modelClass(new Persistence\Array_());
        self::expectExceptionMessage('generateCryptId must be extended in child Model');
        $this->callProtected($model, 'generateCryptId');
    }

    public function testsetCryptId(): void
    {
        $entity = (new ModelWithCryptIdTrait($this->db))->createEntity();
        $entity->save();
        self::assertSame(
            12,
            strlen($entity->get('crypt_id'))
        );
    }

    public function testFieldSetToReadOnlyIfCryptIdNotEmpty(): void
    {
        $entity = (new ModelWithCryptIdTrait($this->db))->createEntity();
        $entity->save(); //save automatically reloads by default
        self::assertTrue($entity->getField('crypt_id')->readOnly);
    }

    public function testNewCryptIdIsGeneratedIfGeneratedOneAlreadyExists(): void
    {
        
        $entity = (new ModelWithCryptIdTrait(
            $this->db,
            ['generateStaticCryptIdOnFirstRun' => true]
        ))->createEntity();
        $entity->save();
        self::assertSame(
            'abcdefghijkl',
            $entity->get('crypt_id')
        );

        //this entity will create the very same ID on the first run of setCryptId(), thus the corresponding line
        //within setCryptId is executed
        $entity2 = (new ModelWithCryptIdTrait(
            $this->db,
            ['generateStaticCryptIdOnFirstRun' => true]
        ))->createEntity();
        $entity2->save();
        self::assertNotSame(
            'abcdefghijkl',
            $entity2->get('crypt_id')
        );
    }
}
