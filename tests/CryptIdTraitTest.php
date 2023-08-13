<?php declare(strict_types=1);

namespace atkdatamodeltraits\tests;

use Atk4\Data\Exception;
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
        $entity->setCryptId();
        self::assertSame(
            12,
            strlen($entity->get('crypt_id'))
        );
    }


    public function testFieldSetToReadOnlyIfCryptIdNotEmpty(): void
    {
        $entity = (new ModelWithCryptIdTrait($this->getSqliteTestPersistence()))->createEntity();
        $entity->setCryptId();
        $entity->save();
        self::assertTrue($entity->getField('crypt_id')->readOnly);
    }
}
