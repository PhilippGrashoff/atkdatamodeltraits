<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits;

use Atk4\Data\Exception;
use Atk4\Data\Model;

/**
 * @extends Model<Model>
 * create cryptic IDs like X3gkd9S-df29D3j in a format if your choice. To do so, implement generateCryptId() function
 * in each Model using this trait.
 */
trait CryptIdTrait
{

    use UniqueFieldTrait;

    /** @var array<string>
     * Chars I, l, O, 0 are removed as they can be easily mixed up by humans
     */
    protected array $possibleChars = [
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

    protected function addCryptIdFieldAndHooks(string $fieldName): static
    {
        $this->addField(
            $fieldName,
            [
                'type' => 'string',
                'system' => true,
                'required' => true
            ]
        );

        $this->onHook(
            Model::HOOK_BEFORE_INSERT,
            function (self $entity, array &$data) use ($fieldName) {
                if ($this->get($fieldName) === null) { //leave option to manually set crypt ID, e.g. for imports
                    $data[$fieldName] = $entity->setCryptId($fieldName);
                }
            }
        );

        $this->onHook(
            Model::HOOK_AFTER_LOAD,
            function (self $entity) use ($fieldName) {
                $entity->getField($fieldName)->readOnly = true;
            }
        );

        return $this;
    }

    /**
     * sets a cryptic ID to the fieldName passed. Only does something if the field is empty.
     * Needs to return the generated crypt ID, so it can be used in Model::HOOK_BEFORE_INSERT
     *
     * @param string $fieldName
     * @return string
     * @throws Exception
     * @throws \Atk4\Core\Exception
     */
    protected function setCryptId(string $fieldName): string
    {
        $this->set($fieldName, $this->generateCryptId());
        //check if another Record has the same crypt_id, if so generate a new one
        while (!$this->isFieldUnique($fieldName)) {
            $this->set($fieldName, $this->generateCryptId());
        }
        return $this->get($fieldName);
    }

    /**
     * Extend this to your own needs in Models using this trait
     *
     * @return string
     * @throws Exception
     */
    protected function generateCryptId(): string
    {
        throw new Exception(__FUNCTION__ . ' must be extended in child Model');
    }

    /**
     * returns a random char from possibleChars. This function is usually called by generateCryptId
     *
     * @return string
     * @throws \Exception
     */
    protected function getRandomChar(): string
    {
        return $this->possibleChars[random_int(0, count($this->possibleChars) - 1)];
    }
}
