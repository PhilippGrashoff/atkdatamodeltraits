<?php declare(strict_types=1);

namespace traitsforatkdata;

use Atk4\Data\Exception;

/**
 * create cryptic IDs like X3gkd9S-df29D3j in a format if your choice. To do so, implement generateCryptId() function
 * in each Model using this trait.
 */
trait CryptIdTrait
{

    use UniqueFieldTrait;

    //@codeCoverageIgnore Seems some Bug in Code Coverage, this line is marked as not covered

    protected $cryptIdFieldName = 'crypt_id';

    //removed I, l, O, 0 as they can be easily mixed up by humans
    protected $possibleChars = [
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        'a',
        'b',
        'c',
        'd',
        'e',
        'f',
        'g',
        'h',
        'i',
        'j',
        'k',
        'm',
        'n',
        'o',
        'p',
        'q',
        'r',
        's',
        't',
        'u',
        'v',
        'w',
        'x',
        'y',
        'z',
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'J',
        'K',
        'L',
        'M',
        'N',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
    ];

    /**
     * sets a cryptic Id to the fieldName passed. Only does something if the field is empty.
     */
    public function setCryptId(): void
    {
        if (!$this->get($this->cryptIdFieldName)) {
            $this->set($this->cryptIdFieldName, $this->generateCryptId());
            //check if another Record has the same crypt_id, if so generate a new one
            while (!$this->isFieldUnique($this->cryptIdFieldName)) {
                $this->set($this->cryptIdFieldName, $this->generateCryptId());
            }
        } else {
            $this->getField($this->cryptIdFieldName)->read_only = true;
        }
    }

    public function getCryptId(): string
    {
        return $this->get($this->cryptIdFieldName);
    }

    /**
     * Overwrite to your own needs in Models using this trait
     */
    protected function generateCryptId(): string
    {
        throw new Exception(__FUNCTION__ . ' must be extended in child model');
    }

    /**
     * returns a random char from possibleChars. This function is usually called by generateCryptId
     */
    protected function getRandomChar(): string
    {
        return $this->possibleChars[random_int(0, count($this->possibleChars) - 1)];
    }
}
