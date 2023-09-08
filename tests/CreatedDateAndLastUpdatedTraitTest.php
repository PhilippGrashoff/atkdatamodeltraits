<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits\Tests;

use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use DateTime;
use DateTimeInterface;
use PhilippR\Atk4\ModelTraits\Tests\Testclasses\ModelWithCreatedDateAndLastUpdatedTrait;


class CreatedDateAndLastUpdatedTraitTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new Sql('sqlite::memory:');
        $this->createMigrator(new ModelWithCreatedDateAndLastUpdatedTrait($this->db))->create();
    }

    public function testCreatedDateAndLastUpdated(): void
    {
        $currentDateTime = new DateTime();
        $model = new ModelWithCreatedDateAndLastUpdatedTrait($this->db);
        $entity = $model->createEntity()->save();

        self::assertEquals(
            $currentDateTime->format(DATE_ATOM),
            $entity->get('created_date')->format(DATE_ATOM)
        );
        self::assertNull($entity->get('last_updated'));

        sleep(1);

        $entity->set('name', 'someName');
        $entity->save();

        $newDateTime = new DateTime();
        self::assertNotEquals(
            $newDateTime->format(DATE_ATOM),
            $entity->get('created_date')->format(DATE_ATOM)
        );
        $newDateTime = new DateTime();
        self::assertEquals(
            $newDateTime->format(DATE_ATOM),
            $entity->get('last_updated')->format(DATE_ATOM)
        );
    }

    /**
     * before, last_updated was set in before update hook. That caused models to be always saved even if
     * there was nothing to save. This was changed, this test ensures that this stays.
     */
    public function testNoFieldsDirtyNothingIsSaved(): void
    {
        $entity = (new ModelWithCreatedDateAndLastUpdatedTrait($this->db))->createEntity();
        $entity->save();
        self::assertNull($entity->get('last_updated'));
        $entity->save();
        self::assertNull($entity->get('last_updated'));

        $entity->set('name', 'somename');
        $entity->save();
        self::assertSame(
            'somename',
            $entity->get('name')
        );
        self::assertInstanceOf(
            DateTimeInterface::class,
            $entity->get('last_updated')
        );
        $lastUpdated = $entity->get('last_updated');
        sleep(1);
        $entity->save();
        self::assertSame(
            $lastUpdated->format(DATE_ATOM),
            $entity->get('last_updated')->format(DATE_ATOM)
        );
    }

    public function testSetCreatedDateNotOverwritten(): void
    {
        $entity = (new ModelWithCreatedDateAndLastUpdatedTrait($this->db))->createEntity();
        $entity->set('created_date', (new DateTime())->modify('-1 Month'));
        $entity->save();

        self::assertEquals(
            (new DateTime())->modify('-1 Month')->getTimestamp(),
            $entity->get('created_date')->getTimestamp()
        );
    }
}