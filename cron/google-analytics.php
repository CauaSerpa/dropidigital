<?php

require_once dirname(__DIR__, 1) . '/config.php';

require_once dirname(__DIR__, 1) . 'painel/back-end/google/App/GoogleCrypto.php';
require_once dirname(__DIR__, 1) . 'painel/back-end/google/App/GoogleAnalyticsRepository.php';
require_once dirname(__DIR__, 1) . 'painel/back-end/google/App/GoogleAnalyticsAuth.php';
require_once dirname(__DIR__, 1) . 'painel/back-end/google/App/GoogleAnalyticsData.php';
require_once dirname(__DIR__, 1) . 'painel/back-end/google/App/GoogleAnalyticsSync.php';
require_once dirname(__DIR__, 1) . 'painel/back-end/google/App/GoogleAnalyticsCron.php';


$startedAt =
    microtime(true);


echo "========================================\n";

echo "Google Analytics Sync\n";

echo "Início: "
    . date('Y-m-d H:i:s')
    . "\n";

echo "========================================\n\n";


try {

    $googleAnalyticsCron =
        new GoogleAnalyticsCron(
            $conn_pdo
        );

    $result =
        $googleAnalyticsCron->run(
            '30daysAgo',
            'yesterday'
        );

    echo "Lojas encontradas: "
        . $result['shops_found']
        . "\n";

    echo "Lojas processadas: "
        . $result['shops_processed']
        . "\n\n";

    foreach (
        $result['results']
        as $shopResult
    ) {

        echo "Shop ID: "
            . $shopResult['shop_id']
            . "\n";

        echo "Status: "
            . strtoupper(
                $shopResult['status']
            )
            . "\n";

        echo "Linhas recebidas: "
            . $shopResult['rows_received']
            . "\n";

        echo "Linhas salvas: "
            . $shopResult['rows_saved']
            . "\n";

        if (
            !empty(
                $shopResult['error']
            )
        ) {

            echo "Erro: "
                . $shopResult['error']
                . "\n";
        }

        echo "\n";
    }

} catch (Throwable $e) {

    fwrite(
        STDERR,

        "Erro fatal: "
        . $e->getMessage()
        . "\n"
    );

    exit(1);
}


$duration =
    microtime(true)
    - $startedAt;


echo "========================================\n";

echo "Finalizado em "
    . number_format(
        $duration,
        2
    )
    . " segundos\n";

echo "Fim: "
    . date('Y-m-d H:i:s')
    . "\n";

echo "========================================\n";