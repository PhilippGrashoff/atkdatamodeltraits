<?php declare(strict_types=1);

namespace atkdatamodeltraits;

use Atk4\Data\Exception;

/**
 * Use the function provided by this trait to check if no other record in the same table has the same value.
 * Example: Your model has a field which must be unique. Before inserting into Database you want to make sure
 * no DB error will occur due to duplicate value for the field which must be unique.
 * $this->onHook(
 *    Model::HOOK_BEFORE_SAVE,
 *    function($model, $isUpdate) {
 *        if($isUpdate) {
 *            return;
 *        }
 *        while(!$model->isFieldUnique('some_field_which_must_be_unique) {
 *            $model->recalculateSomeFieldWhichMustBeUnique(); //some function which recalculates field value.
 *        }
 *    }
 * );
 */
trait UniqueFieldTrait
{

    public function isFieldUnique(string $fieldName, $allowEmpty = false): bool
    {
        if (
            empty($this->get($fieldName))
            && !$allowEmpty
        ) {
            throw new Exception(
                'The value for a unique field may not be empty. Field name: ' . $fieldName . ' in ' . __FUNCTION__
            );
        }
        $other = new static($this->persistence);
        //only load field to save performance
        $other->setOnlyFields([$this->id_field, $fieldName]);
        $other->addCondition($this->id_field, '!=', $this->get($this->id_field));
        try {
            $other->loadBy($fieldName, $this->get($fieldName));
            return false;
        } catch (Exception $e) {
            return true;
        }
    }
}
