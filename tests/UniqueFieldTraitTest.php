<?php declare(strict_types=1);

namespace atkdatamodeltraits\tests;

use Atk4\Data\Persistence;
use atkdatamodeltraits\tests\testclasses\ModelWithUniqueFieldTrait;
use atkextendedtestcase\TestCase;


class UniqueFieldTraitTest extends TestCase
{

    protected array $sqlitePersistenceModels = [ModelWithUniqueFieldTrait::class];

    public function testExceptionOnEmptyValue(): void
    {
        $entity = $this->getTestEntity();
        self::expectExceptionMessage(
            'The value for a unique field may not be empty. Field name: unique_field in isFieldUnique'
        );
        $entity->isFieldUnique('unique_field');
    }

    public function testReturnFalseIfOtherRecordWithSameUniqueFieldValueExists(): void
    {
        $persistence = $this->getSqliteTestPersistence();
        $entity = $this->getTestEntity($persistence);
        $entity->set('unique_field', 'ABC');
        $entity->save();
        self::assertTrue($entity->isFieldUnique('unique_field'));

        $entity2 = $this->getTestEntity($persistence);
        $entity2->save();
        $entity2->set('unique_field', 'DEF');
        self::assertTrue($entity2->isFieldUnique('unique_field'));
        $entity2->set('unique_field', 'ABC');
        self::assertFalse($entity2->isFieldUnique('unique_field'));
    }

    protected function getTestEntity(Persistence $persistence = null): ModelWithUniqueFieldTrait
    {
        return (new ModelWithUniqueFieldTrait($persistence ?: $this->getSqliteTestPersistence()))->createEntity();
    }
}
