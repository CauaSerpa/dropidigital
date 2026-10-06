<?php

class GoogleCrypto
{
    private const PREFIX = 'enc:v1:';

    private string $key;

    public function __construct()
    {
        $encodedKey =
            $_ENV['GOOGLE_ANALYTICS_ENCRYPTION_KEY']
            ?? '';

        if ($encodedKey === '') {
            throw new Exception(
                'Chave de criptografia do Google Analytics não configurada.'
            );
        }

        $key = base64_decode(
            $encodedKey,
            true
        );

        if (
            $key === false ||
            strlen($key) !==
                SODIUM_CRYPTO_SECRETBOX_KEYBYTES
        ) {
            throw new Exception(
                'A chave de criptografia do Google Analytics é inválida.'
            );
        }

        $this->key = $key;
    }

    public function encrypt(
        ?string $value
    ): ?string {
        if (
            $value === null ||
            $value === ''
        ) {
            return $value;
        }

        $nonce = random_bytes(
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
        );

        $ciphertext =
            sodium_crypto_secretbox(
                $value,
                $nonce,
                $this->key
            );

        return self::PREFIX
            . base64_encode(
                $nonce . $ciphertext
            );
    }

    public function decrypt(
        ?string $value
    ): ?string {
        if (
            $value === null ||
            $value === ''
        ) {
            return $value;
        }

        /*
         * Compatibilidade com tokens antigos
         * que ainda estejam em texto puro.
         */
        if (
            !str_starts_with(
                $value,
                self::PREFIX
            )
        ) {
            return $value;
        }

        $encoded = substr(
            $value,
            strlen(self::PREFIX)
        );

        $data = base64_decode(
            $encoded,
            true
        );

        if ($data === false) {
            throw new Exception(
                'Dados criptografados inválidos.'
            );
        }

        $nonceLength =
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;

        if (
            strlen($data) <= $nonceLength
        ) {
            throw new Exception(
                'Dados criptografados incompletos.'
            );
        }

        $nonce = substr(
            $data,
            0,
            $nonceLength
        );

        $ciphertext = substr(
            $data,
            $nonceLength
        );

        $plaintext =
            sodium_crypto_secretbox_open(
                $ciphertext,
                $nonce,
                $this->key
            );

        if ($plaintext === false) {
            throw new Exception(
                'Não foi possível descriptografar os dados.'
            );
        }

        return $plaintext;
    }
}