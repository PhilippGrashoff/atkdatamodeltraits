<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits\Tests;

use Atk4\Data\Persistence;
use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use PhilippR\Atk4\ModelTraits\Tests\Testclasses\ModelWithUniqueFieldTrait;


class UniqueFieldTraitTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new Sql('sqlite::memory:');
        $this->createMigrator(new ModelWithUniqueFieldTrait($this->db))->create();
    }

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
        $entity = $this->getTestEntity();
        $entity->set('unique_field', 'ABC');
        $entity->save();
        self::assertTrue($entity->isFieldUnique('unique_field'));

        $entity2 = $this->getTestEntity();
        $entity2->save();
        $entity2->set('unique_field', 'DEF');
        self::assertTrue($entity2->isFieldUnique('unique_field'));
        $entity2->set('unique_field', 'ABC');
        self::assertFalse($entity2->isFieldUnique('unique_field'));
    }

    protected function getTestEntity(): ModelWithUniqueFieldTrait
    {
        return (new ModelWithUniqueFieldTrait($this->db))->createEntity();
    }
}
