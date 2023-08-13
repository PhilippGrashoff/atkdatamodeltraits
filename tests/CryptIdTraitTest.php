<?php declare(strict_types=1);

namespace atkdatamodeltraits\tests;

use Atk4\Data\Exception;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use atkdatamodeltraits\CryptIdTrait;
use atkdatamodeltraits\TestCase;
use atkdatamodeltraits\tests\testclasses\ModelWithCryptIdTrait;


class CryptIdTraitTest extends TestCase
{

    protected $sqlitePersistenceModels = [ModelWithCryptIdTrait::class];

    public function testExceptionOverwriteGenerate()
    {
        $modelClass = new class() extends Model {
            use CryptIdTrait;

            public $table = 'sometable';

        };
        $model = new $modelClass(new Persistence\Array_());
        self::expectException(Exception::class);
        $this->callProtected($model, 'generateCryptId');
    }

    public function testsetCryptId()
    {
        $model = (new ModelWithCryptIdTrait($this->getSqliteTestPersistence()))->createEntity();
        $model->setCryptId();
        self::assertSame(
            12,
            strlen($model->get('crypt_id'))
        );
    }


    public function testFieldSetToReadOnlyIfCryptIdNotEmpty()
    {
        $model = (new ModelWithCryptIdTrait($this->getSqliteTestPersistence()))->createEntity();
        $model->save();
        $model->setCryptId();
        self::assertTrue($model->getField('crypt_id')->read_only);
    }
}
