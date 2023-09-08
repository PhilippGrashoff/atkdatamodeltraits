<?php

declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits\Tests\Testclasses;

use Atk4\Data\Model;
use PhilippR\Atk4\ModelTraits\CryptIdTrait;

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