<?php declare(strict_types=1);

namespace atkdatamodeltraits\tests;

use atkdatamodeltraits\TestCase;
use Atk4\Data\Exception;
use Atk4\Data\Persistence;
use atkdatamodeltraits\tests\testclasses\ModelWithUniqueFieldTrait;


class UniqueFieldTraitTest extends TestCase
{

    protected $sqlitePersistenceModels = [ModelWithUniqueFieldTrait::class];

    public function testExceptionOnEmptyValue()
    {
        $model = $this->getTestModel();
        self::expectException(Exception::class);
        $model->isFieldUnique('unique_field');
    }

    public function testNoExceptionIfAllowEmptyIsTrue()
    {
        $model = $this->getTestModel();
        $model->isFieldUnique('unique_field', true);
        self::expectException(Exception::class);
        $model->isFieldUnique('unique_field');
    }

    public function testReturnFalseIfOtherRecordWithSameUniqueFieldValueExists()
    {
        $persistence = $this->getSqliteTestPersistence();
        $model = $this->getTestModel($persistence);
        $model->set('unique_field', 'ABC');
        $model->save();
        self::assertTrue($model->isFieldUnique('unique_field'));

        $model2 = $this->getTestModel($persistence);
        $model2->save();
        $model2->set('unique_field', 'DEF');
        self::assertTrue($model2->isFieldUnique('unique_field'));
        $model2->set('unique_field', 'ABC');
        self::assertFalse($model2->isFieldUnique('unique_field'));
    }

    protected function getTestModel(Persistence $persistence = null): ModelWithUniqueFieldTrait
    {
        return (new ModelWithUniqueFieldTrait($persistence ?: $this->getSqliteTestPersistence()))->createEntity();
    }
}
