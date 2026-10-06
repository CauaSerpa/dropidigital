<?php

class GoogleAnalyticsCron
{
    private PDO $pdo;

    private GoogleAnalyticsSync $sync;

    public function __construct(
        PDO $pdo
    ) {
        $this->pdo = $pdo;

        $this->sync =
            new GoogleAnalyticsSync(
                $pdo
            );
    }

    public function run(
        string $startDate = '30daysAgo',
        string $endDate = 'yesterday'
    ): array {

        $connections =
            $this->getConnectedShops();

        $results = [];

        foreach (
            $connections
            as $connection
        ) {

            $shopId =
                (int) $connection['shop_id'];

            try {

                $result =
                    $this->sync->syncShop(
                        $shopId,
                        $startDate,
                        $endDate
                    );

                $results[] = [

                    'shop_id' =>
                        $shopId,

                    'status' =>
                        'success',

                    'rows_received' =>
                        $result['rows_received'],

                    'rows_saved' =>
                        $result['rows_saved'],

                    'error' =>
                        null
                ];

            } catch (Throwable $e) {

                $this->markAsError(
                    $shopId
                );

                $results[] = [

                    'shop_id' =>
                        $shopId,

                    'status' =>
                        'error',

                    'rows_received' =>
                        0,

                    'rows_saved' =>
                        0,

                    'error' =>
                        $e->getMessage()
                ];
            }
        }

        return [

            'shops_found' =>
                count($connections),

            'shops_processed' =>
                count($results),

            'results' =>
                $results
        ];
    }

    private function getConnectedShops(): array
    {
        $stmt =
            $this->pdo->query("
                SELECT
                    shop_id,
                    property_id,
                    property_name
                FROM tb_shop_google_analytics
                WHERE status IN (
                    'connected',
                    'error'
                )
                ORDER BY shop_id ASC
            ");

        return $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );
    }

    private function markAsError(
        int $shopId
    ): void {

        $stmt =
            $this->pdo->prepare("
                UPDATE tb_shop_google_analytics
                SET
                    status = 'error',
                    updated_at = CURRENT_TIMESTAMP
                WHERE shop_id = :shop_id
                LIMIT 1
            ");

        $stmt->execute([
            ':shop_id' =>
                $shopId
        ]);
    }
}