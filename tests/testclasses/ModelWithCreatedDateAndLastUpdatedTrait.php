<?php

declare(strict_types=1);

namespace traitsforatkdata\tests\testclasses;

use Atk4\Data\Model;
use traitsforatkdata\CreatedDateAndLastUpdatedTrait;

class ModelWithCreatedDateAndLastUpdatedTrait extends Model
{

    use CreatedDateAndLastUpdatedTrait;

    public $table = 'ModelWithCreatedDateAndLastUpdatedTrait';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');
        $this->addCreatedDateAndLastUpdateFields();
        $this->addCreatedDateAndLastUpdatedHook();
    }
}