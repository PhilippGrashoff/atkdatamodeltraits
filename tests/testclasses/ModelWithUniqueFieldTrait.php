<?php

declare(strict_types=1);

namespace atkdatamodeltraits\tests\testclasses;

use Atk4\Data\Model;
use atkdatamodeltraits\UniqueFieldTrait;

class ModelWithUniqueFieldTrait extends Model
{

    use UniqueFieldTrait;

    public $table = 'ModelWithUniqueFieldTrait';

    protected function init(): void
    {
        parent::init();
        $this->addField('unique_field');
    }
}