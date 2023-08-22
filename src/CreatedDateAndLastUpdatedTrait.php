<?php declare(strict_types=1);

namespace atkdatamodeltraits;

use Atk4\Data\Model;
use DateTime;

/**
 * @extends Model<Model>
 */
trait CreatedDateAndLastUpdatedTrait
{

    /**
     * Adds created_date field and a fitting hook
     *
     * @param array<string, mixed> $additionalFieldSettings
     * @param bool $addHook
     * @return void
     */
    protected function addCreatedDateFieldAndHook(array $additionalFieldSettings = [], bool $addHook = true): void
    {
        $fieldSettings = array_merge(
            ['type' => 'datetime', 'system' => true],
            $additionalFieldSettings
        );

        $this->addField(
            'created_date',
            $fieldSettings
        );

        if ($addHook) {
            $this->onHook(
                Model::HOOK_BEFORE_INSERT,
                function (self $model, array &$data) {
                    //if for some reason created_date is already set, leave it as is. This way created_date can be manually
                    //adjusted e.g. when importing records from another system.
                    if ($data['created_date']) {
                        return;
                    }
                    $data['created_date'] = new DateTime();
                }
            );
        }
    }

    /**
     * Adds last_updated field and a fitting hook
     *
     * @param array<string, mixed> $additionalFieldSettings
     * @param bool $addHook
     * @return void
     */
    protected function addLastUpdatedFieldAndHook(array $additionalFieldSettings = [], bool $addHook = true): void
    {
        $fieldSettings = array_merge(
            ['type' => 'datetime', 'system' => true],
            $additionalFieldSettings
        );

        $this->addField(
            'last_updated',
            $fieldSettings
        );

        if ($addHook) {
            $this->onHook(
                Model::HOOK_BEFORE_UPDATE,
                function (self $model, array &$data) {
                    $data['last_updated'] = new DateTime();
                }
            );
        }
    }
}