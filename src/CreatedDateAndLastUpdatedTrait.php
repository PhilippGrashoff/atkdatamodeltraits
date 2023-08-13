<?php declare(strict_types=1);

namespace atkdatamodeltraits;

use Atk4\Data\Model;

/**
 * @extends Model<Model>
 */
trait CreatedDateAndLastUpdatedTrait
{

    /**
     * Adds created_date and created_by fields to a model
     *
     * @param array $additionalFieldSettings
     * @return void
     */
    protected function addCreatedDateAndLastUpdateFields(array $additionalFieldSettings = []): void
    {
        $fieldSettings = array_merge(
            ['type' => 'datetime', 'system' => true],
            $additionalFieldSettings
        );

        $this->addField(
            'created_date',
            $fieldSettings
        );

        $this->addField(
            'last_updated',
            $fieldSettings
        );
    }

    /**
     * Adds hooks to the model that set created_date and last_updated
     *
     * @return void
     */
    protected function addCreatedDateAndLastUpdatedHook(): void
    {
        $this->onHook(
            Model::HOOK_BEFORE_INSERT,
            function (self $model, array &$data) {
                //if for some reason created_date is already set, leave it as is. This way created_date can be manually
                //adjusted e.g. when importing records from another system.
                if ($data['created_date']) {
                    return;
                }
                $data['created_date'] = new \DateTime();
            }
        );
        $this->onHook(
            Model::HOOK_BEFORE_UPDATE,
            function (self $model, array &$data) {
                $data['last_updated'] = new \DateTime();
            }
        );
    }
}