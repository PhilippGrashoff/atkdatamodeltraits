<?php

declare(strict_types=1);

namespace atkdatamodeltraits\tests\testclasses;

use Atk4\Data\Model;
use atkdatamodeltraits\CryptIdTrait;

class ModelWithCryptIdTrait extends Model
{
    use CryptIdTrait;

    protected bool $generateStaticCryptIdOnFirstRun = false;

    protected int $runCounter = 0;

    public $table = 'ModelWithCryptIdTrait';

    protected function init(): void
    {
        parent::init();
        $this->addCryptIdFieldAndHooks('crypt_id');
    }

    protected function generateCryptId(): string
    {
        $cryptId = '';
        if ($this->generateStaticCryptIdOnFirstRun && $this->runCounter === 0) {
            $this->runCounter++;
            return "abcdefghijkl";
        }
        for ($i = 0; $i < 12; $i++) {
            $cryptId .= $this->getRandomChar();
        }

        return $cryptId;
    }
}