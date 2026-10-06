<?php

class GoogleAnalyticsSync
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

    public function syncShop(
        int $shopId,
        string $startDate,
        string $endDate
    ): array {

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
            empty($connection['property_id'])
        ) {

            throw new Exception(
                'A propriedade do Google Analytics não está configurada.'
            );
        }

        $googleAuth =
            new GoogleAnalyticsAuth(
                $this->pdo
            );

        $client =
            $googleAuth->getAuthenticatedClient(
                $shopId
            );

        $googleAnalyticsData =
            new GoogleAnalyticsData(
                $client
            );

        $report =
            $googleAnalyticsData->runReport(
                $connection['property_id'],

                [
                    'date',
                    'pagePath'
                ],

                [
                    'screenPageViews',
                    'activeUsers',
                    'sessions'
                ],

                $startDate,
                $endDate,

                10000
            );

        $rows =
            $report['rows'] ?? [];

        $this->pdo->beginTransaction();

        try {

            $stmt = $this->pdo->prepare("
                INSERT INTO tb_ga4_page_views (
                    shop_id,
                    date,
                    page_path,
                    page_views,
                    active_users,
                    sessions
                ) VALUES (
                    :shop_id,
                    :date,
                    :page_path,
                    :page_views,
                    :active_users,
                    :sessions
                )
                ON DUPLICATE KEY UPDATE
                    page_views =
                        VALUES(page_views),

                    active_users =
                        VALUES(active_users),

                    sessions =
                        VALUES(sessions),

                    updated_at =
                        CURRENT_TIMESTAMP
            ");

            $saved = 0;

            foreach ($rows as $row) {

                $dimensionValues =
                    $row['dimensionValues']
                    ?? [];

                $metricValues =
                    $row['metricValues']
                    ?? [];

                $dateValue =
                    $dimensionValues[0]['value']
                    ?? null;

                $pagePath =
                    $dimensionValues[1]['value']
                    ?? null;

                if (
                    !$dateValue ||
                    !$pagePath
                ) {
                    continue;
                }

                if (
                    !preg_match(
                        '/^\d{8}$/',
                        $dateValue
                    )
                ) {
                    continue;
                }

                $date = sprintf(
                    '%s-%s-%s',

                    substr(
                        $dateValue,
                        0,
                        4
                    ),

                    substr(
                        $dateValue,
                        4,
                        2
                    ),

                    substr(
                        $dateValue,
                        6,
                        2
                    )
                );

                $pageViews =
                    (int) (
                        $metricValues[0]['value']
                        ?? 0
                    );

                $activeUsers =
                    (int) (
                        $metricValues[1]['value']
                        ?? 0
                    );

                $sessions =
                    (int) (
                        $metricValues[2]['value']
                        ?? 0
                    );

                $stmt->execute([
                    ':shop_id' =>
                        $shopId,

                    ':date' =>
                        $date,

                    ':page_path' =>
                        $pagePath,

                    ':page_views' =>
                        $pageViews,

                    ':active_users' =>
                        $activeUsers,

                    ':sessions' =>
                        $sessions
                ]);

                $saved++;
            }

            $stmt = null;

            $stmt =
                $this->pdo->prepare("
                    UPDATE tb_shop_google_analytics
                    SET
                        last_sync_at =
                            CURRENT_TIMESTAMP,

                        status =
                            'connected',

                        updated_at =
                            CURRENT_TIMESTAMP

                    WHERE shop_id =
                        :shop_id

                    LIMIT 1
                ");

            $stmt->execute([
                ':shop_id' =>
                    $shopId
            ]);

            $this->pdo->commit();

            return [
                'shop_id' =>
                    $shopId,

                'property_id' =>
                    $connection['property_id'],

                'start_date' =>
                    $startDate,

                'end_date' =>
                    $endDate,

                'rows_received' =>
                    count($rows),

                'rows_saved' =>
                    $saved
            ];

        } catch (Throwable $e) {

            if (
                $this->pdo->inTransaction()
            ) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}