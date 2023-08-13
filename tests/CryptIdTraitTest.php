<?php declare(strict_types=1);

namespace atkdatamodeltraits\tests;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use atkdatamodeltraits\CryptIdTrait;
use atkdatamodeltraits\tests\testclasses\ModelWithCryptIdTrait;
use atkextendedtestcase\TestCase;


class CryptIdTraitTest extends TestCase
{

    protected array $sqlitePersistenceModels = [ModelWithCryptIdTrait::class];

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
        $entity = (new ModelWithCryptIdTrait($this->getSqliteTestPersistence()))->createEntity();
        $entity->save();
        self::assertSame(
            12,
            strlen($entity->get('crypt_id'))
        );
    }

    public function testFieldSetToReadOnlyIfCryptIdNotEmpty(): void
    {
        $entity = (new ModelWithCryptIdTrait($this->getSqliteTestPersistence()))->createEntity();
        $entity->save(); //save automatically reloads by default
        self::assertTrue($entity->getField('crypt_id')->readOnly);
    }

    public function testNewCryptIdIsGeneratedIfGeneratedOneAlreadyExists(): void
    {
        $persistence = $this->getSqliteTestPersistence();
        $entity = (new ModelWithCryptIdTrait(
            $persistence,
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
            $persistence,
            ['generateStaticCryptIdOnFirstRun' => true]
        ))->createEntity();
        $entity2->save();
        self::assertNotSame(
            'abcdefghijkl',
            $entity2->get('crypt_id')
        );
    }
}
