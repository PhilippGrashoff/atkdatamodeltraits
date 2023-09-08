<?php

declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits\Tests\Testclasses;

use Atk4\Data\Model;
use PhilippR\Atk4\ModelTraits\UniqueFieldTrait;

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