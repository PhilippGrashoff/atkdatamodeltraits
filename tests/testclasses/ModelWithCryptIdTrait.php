<?php

declare(strict_types=1);

namespace atkdatamodeltraits\tests\testclasses;

use Atk4\Data\Model;
use atkdatamodeltraits\CryptIdTrait;

class ModelWithCryptIdTrait extends Model
{
    use CryptIdTrait;

    public $table = 'ModelWithCryptIdTrait';

    protected function init(): void
    {
        parent::init();
        $this->addField('crypt_id');
        $this->onHook(
            Model::HOOK_BEFORE_SAVE,
            function (self $model, bool $isUpdate) {
                $model->setCryptId();
            }
        );
    }

    protected function generateCryptId(): string
    {
        $cryptId = '';
        for ($i = 0; $i < 10; $i++) {
            $cryptId .= $this->getRandomChar();
        }

        return $cryptId;
    }
}