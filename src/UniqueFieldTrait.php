<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits;

use Atk4\Data\Exception;
use Atk4\Data\Model;

/**
 * @extends Model<Model>
 *
 * Use the function provided by this trait to check if no other record in the same table has the same value.
 * Example: Your model has a field which must be unique. Before inserting into Database you want to make sure
 * no DB error will occur due to duplicate value for the field which must be unique.
 * $this->onHook(
 *    Model::HOOK_BEFORE_SAVE,
 *    function(self $entity, bool $isUpdate) {
 *        if($isUpdate) {
 *            return;
 *        }
 *        while(!$entity->isFieldUnique('some_field_which_must_be_unique')) {
 *            //some function which recalculates field value.
 *        }
 *    }
 * );
 */
trait UniqueFieldTrait
{

    /**
     * @param string $fieldName
     * @return bool
     * @throws Exception
     */
    public function isFieldUnique(string $fieldName): bool
    {
        $this->assertIsEntity();
        if (empty($this->get($fieldName))) {
            throw new Exception(
                'The value for a unique field may not be empty. Field name: ' . $fieldName . ' in ' . __FUNCTION__
            );
        }
        $checkModel = new static($this->getModel()->getPersistence());
        $checkModel->addCondition($fieldName, '=', $this->get($fieldName));
        if ($this->isLoaded()) {
            $checkModel->addCondition($this->idField, '!=', $this->get($this->idField));
        }

        return $checkModel->tryLoadAny() === null;
    }
}
