# atkdatamodeltraits

[![codecov](https://codecov.io/gh/PhilippGrashoff/atkdatamodeltraits/branch/main/graph/badge.svg)](https://codecov.io/gh/PhilippGrashoff/atkdatamodeltraits)

A small collection of traits to be used with [Atk4\Data\Model](https://github.com/atk4/data/blob/develop/src/Model.php).

# CreatedDateAndLastUpdatedTrait
This trait can be used to add a `created_date` and/or a `last_updated` field to a model. The corresponding hooks are automatically added to save `created_date` on insert and `last_updated` on update.
```php
class SomeModel extends Model
{
    use CreatedDateAndLastUpdatedTrait;

    protected function init(): void
    {
        parent::init();
        $this->addCreatedDateFieldAndHook();
        $this->addLastUpdatedFieldAndHook();
    }
}
```

# UniqueFieldTrait
This trait provides a function `isFieldUnique()` to check if the field's current value is unique among all records in persistence.
This functionality can also be achieved by adding a UNIQUE index to an SQL persistence. Having this functionality in the application layer
can be sensible to be independent of the persistence's features - or to avoid getting back an exception from the persistence if a non-unique value is being tried to save.
```php
class SomeModel extends Model
{
    use UniqueFieldTrait;

    protected function init(): void
    {
        parent::init();
        $this->addField('unique_field');
    }
    
    $this->onHook(
        Model::HOOK_BEFORE_SAVE,
        function(self $entity, bool $isUpdate) {
            while(!$entity->isFieldUnique('unique_field')) {
                //some function which recalculates field value.
            }
        }
    );
}
```
# CryptIdTrait
This trait is used to generate cryptic IDs like `D6f2-a395Jskv2`. You can freely define the format the cryptic ID shall have.
The functionality can for example be used to create coupon codes or create unguessable, yet human-readable codes/identifiers.
The characters I,l,0 and O are removed as they can be easily be mistaken by humans.
To use this trait, you just need to add the cryptic ID field in `Model::init()` using `addCryptIdFieldAndHooks` and implement a custom method
`generateCryptId()` that returns a random string in the format of your choice:
```php
class SomeModel extends Model
{  
    use CryptIdTrait;

    protected function init(): void
    {
        parent::init();
        $this->addCryptIdFieldAndHooks('crypt_id');
    }

    /**
     * Custom implementation if cryptic ID format, In this case XXXXX-XXXXX-XXXXX
     */
    protected function generateCryptId(): string
    {
        $cryptId = '';
        for ($i = 0; $i < 3; $i++) {
            if($i > 0) {
                $cryptId .= '-';
            }
            for ($j = 0; $j < 5; $j++) {
                $cryptId .= $this->getRandomChar();
            }
        }

        return $cryptId;
    }
}
```