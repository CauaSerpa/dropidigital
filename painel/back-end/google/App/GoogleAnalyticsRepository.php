<?php

class GoogleAnalyticsRepository
{
    private GoogleCrypto $crypto;

    public function __construct(
        private PDO $pdo
    ) {
        $this->crypto = new GoogleCrypto();
    }

    public function saveConnection(
        int $shopId,
        array $data
    ): void {

        $sql = "
            INSERT INTO tb_shop_google_analytics (
                shop_id,
                google_user_id,
                google_email,
                access_token,
                refresh_token,
                token_expires_at,
                property_id,
                property_name,
                property_account,
                measurement_id,
                stream_name,
                status,
                last_sync_at
            ) VALUES (
                :shop_id,
                :google_user_id,
                :google_email,
                :access_token,
                :refresh_token,
                :token_expires_at,
                :property_id,
                :property_name,
                :property_account,
                :measurement_id,
                :stream_name,
                'connected',
                NULL
            )
            ON DUPLICATE KEY UPDATE

                access_token =
                    VALUES(access_token),

                refresh_token =
                    COALESCE(
                        VALUES(refresh_token),
                        refresh_token
                    ),

                token_expires_at =
                    VALUES(token_expires_at),

                property_id =
                    VALUES(property_id),

                property_name =
                    VALUES(property_name),

                property_account =
                    VALUES(property_account),

                measurement_id =
                    VALUES(measurement_id),

                stream_name =
                    VALUES(stream_name),

                status = 'connected',

                updated_at =
                    CURRENT_TIMESTAMP
        ";

        $stmt = $this->pdo->prepare(
            $sql
        );

        $stmt->execute([
            ':shop_id' =>
                $shopId,

            ':google_user_id' =>
                $data['google_user_id']
                ?? null,

            ':google_email' =>
                $data['google_email']
                ?? null,

            ':access_token' =>
                $this->crypto->encrypt(
                    $data['access_token']
                ),

            ':refresh_token' =>
                $this->crypto->encrypt(
                    $data['refresh_token']
                    ?? null
                ),

            ':token_expires_at' =>
                $data['token_expires_at']
                ?? null,

            ':property_id' =>
                $data['property_id'],

            ':property_name' =>
                $data['property_name'],

            ':property_account' =>
                $data['property_account'],

            ':measurement_id' =>
                $data['measurement_id'],

            ':stream_name' =>
                $data['stream_name'],
        ]);
    }

    public function getConnection(
        int $shopId
    ): ?array {

        $stmt = $this->pdo->prepare("
            SELECT
                id,
                shop_id,
                google_user_id,
                google_email,
                access_token,
                refresh_token,
                token_expires_at,
                property_id,
                property_name,
                property_account,
                measurement_id,
                stream_name,
                status,
                last_sync_at,
                created_at,
                updated_at
            FROM tb_shop_google_analytics
            WHERE shop_id = :shop_id
            LIMIT 1
        ");

        $stmt->execute([
            ':shop_id' => $shopId
        ]);

        $connection =
            $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$connection) {
            return null;
        }

        $connection['access_token'] =
            $this->crypto->decrypt(
                $connection['access_token']
            );

        $connection['refresh_token'] =
            $this->crypto->decrypt(
                $connection['refresh_token']
            );

        return $connection;
    }

    public function updateAccessToken(
        int $connectionId,
        string $accessToken,
        string $tokenExpiresAt
    ): void {

        $stmt = $this->pdo->prepare("
            UPDATE tb_shop_google_analytics
            SET
                access_token = :access_token,
                token_expires_at = :token_expires_at,
                status = 'connected',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            ':access_token' =>
                $this->crypto->encrypt(
                    $accessToken
                ),

            ':token_expires_at' =>
                $tokenExpiresAt,

            ':id' =>
                $connectionId
        ]);
    }
}