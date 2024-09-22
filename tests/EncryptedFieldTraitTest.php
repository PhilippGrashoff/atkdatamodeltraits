<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits\Tests;

use Atk4\Data\Persistence\Sql;
use Atk4\Data\Schema\TestCase;
use PhilippR\Atk4\ModelTraits\Tests\Testclasses\ModelWithEncryptedFieldTrait;

class EncryptedFieldTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = new Sql('sqlite::memory:');
        $this->createMigrator(new ModelWithEncryptedFieldTrait($this->db))->create();
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!defined('ENCRYPTFIELD_KEY')) {
            define('ENCRYPTFIELD_KEY', '12003456789abcdef123456789abcdef');
        }
    }
    public function testValueEncryption(): void
    {
        $entity = (new ModelWithEncryptedFieldTrait($this->db))->createEntity();
        $entity->set('encrypted_value', '');
        $entity->save();

        $withoutEncryption = (new ModelWithEncryptedFieldTrait($this->db, ['addEncryptionHooks' => false]))->load($entity->getId());
        self::assertNotSame(
            $entity->get('encrypted_value'),
            $withoutEncryption->get('encrypted_value')
        );

        self::assertGreaterThan(
            30,
            strlen($withoutEncryption->get('encrypted_value'))
        );

        $entity->set('encrypted_value', 'SOMEVALUE');
        $entity->save();

        $anotherEntity = (new ModelWithEncryptedFieldTrait($this->db))->load($entity->getId());
        self::assertSame(
            'SOMEVALUE',
            $anotherEntity->get('encrypted_value')
        );

        $withoutEncryption->reload();
        self::assertNotSame(
            'SOMEVALUE',
            $withoutEncryption->get('encrypted_value')
        );
    }

}
