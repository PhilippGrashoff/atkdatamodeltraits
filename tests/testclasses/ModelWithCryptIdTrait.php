<?php

declare(strict_types=1);

namespace atkdatamodeltraits\tests\testclasses;

use Atk4\Data\Model;
use atkdatamodeltraits\CryptIdTrait;

class ModelWithCryptIdTrait extends Model
{
    use CryptIdTrait;

    public $table = 'ModelWithCryptIdTrait';

    public $addition = '';

    protected $createSameCryptId = false;


    protected function init(): void
    {
        parent::init();
        $this->addField('crypt_id');
        $this->onHook(
            Model::HOOK_BEFORE_SAVE,
            function (self $model, $isUpdate) {
                $model->setCryptId();
            }
        );
    }
}