<?php declare(strict_types=1);

namespace PhilippR\Atk4\ModelTraits;

use Atk4\Data\Exception;
use Atk4\Data\Model;

/**
 * @extends Model<Model>
 * Provides functions to store the value of a field encrypted to persistence.
 * Useful for storing credentials that are needed in clear text at some point
 * like Api Tokens.
 * encryption and decryption taken from PHP manual
 */
trait EncryptedFieldTrait
{

    protected function addEncryptionHooksForField(string $fieldName): void
    {
        //decrypt value in case it was stored encrypted
        $this->onHook(
            Model::HOOK_AFTER_LOAD,
            function (self $entity) use ($fieldName) {
                $entity->decryptFieldValue($fieldName);
            },
            [],
            1
        );

        //encrypt value last thing before saving in case it shall be stored encrypted
        $this->onHook(
            Model::HOOK_BEFORE_SAVE,
            function (self $settingEntity) use ($fieldName) {
                $settingEntity->encryptFieldValue($fieldName);
            },
            [],
            999
        );
    }

    protected function decryptFieldValue(string $fieldName): void
    {
        $key = $this->getEncryptionKey();
        $decoded = base64_decode((string)$this->get($fieldName));
        if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES)) {
            throw new Exception('An error occurred decrypting the field value');  //@codeCoverageIgnore
        }
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
        if ($plain === false) {
            throw new Exception('An error occurred decrypting the field value');  //@codeCoverageIgnore
        }
        sodium_memzero($ciphertext);
        sodium_memzero($key);

        $this->set($fieldName, $plain);
    }

    protected function encryptFieldValue(string $fieldName): void
    {
        $key = $this->getEncryptionKey();
        //sodium needs string
        $value = (string)$this->get($fieldName);
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = base64_encode($nonce . sodium_crypto_secretbox($value, $nonce, $key));
        sodium_memzero($value);
        sodium_memzero($key);
        $this->set($fieldName, $cipher);
    }

    /**
     * extend this method to get the encryption/decryption key according to your needs
     */
    protected function getEncryptionKey(): string
    {
        return ENCRYPTFIELD_KEY;
    }
}

