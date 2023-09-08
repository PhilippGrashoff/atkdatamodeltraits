<?php

declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits\Tests\Testclasses;

use Atk4\Data\Model;
use PhilippR\Atk4\ModelTraits\CreatedDateAndLastUpdatedTrait;

class ModelWithCreatedDateAndLastUpdatedTrait extends Model
{

    use CreatedDateAndLastUpdatedTrait;

    public $table = 'ModelWithCreatedDateAndLastUpdatedTrait';

    protected function init(): void
    {
        parent::init();
        $this->addField('name');
        $this->addCreatedDateFieldAndHook();
        $this->addLastUpdatedFieldAndHook();
    }
}