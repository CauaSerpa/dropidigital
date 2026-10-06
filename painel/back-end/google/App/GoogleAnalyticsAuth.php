<?php

class GoogleAnalyticsAuth
{
    private PDO $pdo;

    private GoogleAnalyticsRepository $repository;

    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;

        $this->repository =
            new GoogleAnalyticsRepository(
                $pdo
            );
    }

    public function getAuthenticatedClient(
        int $shopId
    ): Google\Client {

        $connection =
            $this->repository->getConnection(
                $shopId
            );

        if (!$connection) {
            throw new Exception(
                'Google Analytics não está conectado para esta loja.'
            );
        }

        if (
            empty($connection['access_token']) ||
            empty($connection['refresh_token'])
        ) {
            throw new Exception(
                'Credenciais do Google Analytics estão incompletas.'
            );
        }

        $client = new Google\Client();

        $client->setClientId(
            GOOGLE_OAUTH_CLIENT_ID
        );

        $client->setClientSecret(
            GOOGLE_OAUTH_CLIENT_SECRET
        );

        $client->setAccessType('offline');

        $client->setScopes([
            GOOGLE_ANALYTICS_SCOPE
        ]);

        $expiresAt =
            !empty($connection['token_expires_at'])
                ? strtotime(
                    $connection['token_expires_at']
                )
                : 0;

        $shouldRefresh =
            $expiresAt <= (time() + 60);

        if (!$shouldRefresh) {

            $client->setAccessToken([
                'access_token' =>
                    $connection['access_token'],

                'expires_in' =>
                    max(
                        0,
                        $expiresAt - time()
                    ),

                'created' =>
                    time()
            ]);

            return $client;
        }

        $token =
            $client->fetchAccessTokenWithRefreshToken(
                $connection['refresh_token']
            );

        if (!empty($token['error'])) {

            $error =
                $token['error'];

            if ($error === 'invalid_grant') {

                $this->markAsDisconnected(
                    (int) $connection['id']
                );

            } else {

                $this->markAsError(
                    (int) $connection['id']
                );
            }

            throw new Exception(
                $token['error_description']
                ?? $token['error']
                ?? 'Não foi possível renovar o token do Google Analytics.'
            );
        }

        if (
            empty($token['access_token'])
        ) {

            $this->markAsError(
                (int) $connection['id']
            );

            throw new Exception(
                'O Google não retornou um novo access token.'
            );
        }

        $newExpiresIn =
            (int) (
                $token['expires_in']
                ?? 3600
            );

        $newExpiresAt =
            date(
                'Y-m-d H:i:s',
                time() + $newExpiresIn
            );

        $this->repository->updateAccessToken(
            (int) $connection['id'],
            $token['access_token'],
            $newExpiresAt
        );

        $client->setAccessToken([
            'access_token' =>
                $token['access_token'],

            'expires_in' =>
                $newExpiresIn,

            'created' =>
                time()
        ]);

        return $client;
    }

    private function markAsError(
        int $connectionId
    ): void {

        $stmt = $this->pdo->prepare("
            UPDATE tb_shop_google_analytics
            SET
                status = 'error',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            ':id' => $connectionId
        ]);
    }

    private function markAsDisconnected(
        int $connectionId
    ): void {

        $stmt = $this->pdo->prepare("
            UPDATE tb_shop_google_analytics
            SET
                status = 'disconnected',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute([
            ':id' => $connectionId
        ]);
    }
}