<?php

declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits\Tests\Testclasses;

use Atk4\Data\Model;
use PhilippR\Atk4\ModelTraits\EncryptedFieldTrait;

class ModelWithEncryptedFieldTrait extends Model
{
    use EncryptedFieldTrait;

    public $table = 'ModelWithEncryptedFieldTrait';

    protected bool $addEncryptionHooks = true;

    protected function init(): void
    {
        parent::init();
        $this->addField('encrypted_value');
        if ($this->addEncryptionHooks) {
            $this->addEncryptionHooksForField('encrypted_value');
        }
    }
}