<?php

/**
 * Corrige produtos onde:
 *
 * discount > price
 *
 * Invertendo:
 *
 * price    = discount
 * discount = price
 *
 * Exemplo:
 *
 * ANTES:
 * price    = 500
 * discount = 1000
 *
 * DEPOIS:
 * price    = 1000
 * discount = 500
 */

// =====================================================
// CONFIGURAÇÃO
// =====================================================

$batchSize = 5000;

// =====================================================
// CONEXÃO
// =====================================================

include_once('config.php');

// =====================================================
// TOTAL
// =====================================================

$totalStmt = $conn_pdo->query("
    SELECT COUNT(*) AS total
    FROM tb_products
    WHERE price > 0
      AND discount > 0
      AND discount > price
");

$total = (int) $totalStmt->fetch()['total'];

echo PHP_EOL;
echo "==============================================" . PHP_EOL;
echo " CORREÇÃO DE PREÇOS" . PHP_EOL;
echo "==============================================" . PHP_EOL;
echo "Produtos a corrigir: {$total}" . PHP_EOL;
echo "Tamanho do lote: {$batchSize}" . PHP_EOL;
echo "==============================================" . PHP_EOL;
echo PHP_EOL;

if ($total === 0) {
    echo "Nenhum produto precisa de correção." . PHP_EOL;
    exit;
}

// =====================================================
// PREPARED STATEMENTS
// =====================================================

$selectStmt = $conn_pdo->prepare("
    SELECT
        id,
        price,
        discount
    FROM tb_products
    WHERE id > :last_id
      AND price > 0
      AND discount > 0
      AND discount > price
    ORDER BY id ASC
    LIMIT {$batchSize}
");

$updateStmt = $conn_pdo->prepare("
    UPDATE tb_products
    SET
        price = :new_price,
        discount = :new_discount
    WHERE id = :id
      AND discount > price
");

// =====================================================
// PROCESSAMENTO
// =====================================================

$processed = 0;
$lastId = 0;

$startTime = microtime(true);

while (true) {

    $selectStmt->execute([
        ':last_id' => $lastId
    ]);

    $products = $selectStmt->fetchAll();

    if (!$products) {
        break;
    }

    $conn_pdo->beginTransaction();

    try {

        foreach ($products as $product) {

            $id = (int) $product['id'];

            $price = $product['price'];
            $discount = $product['discount'];

            // Inverte os valores
            $newPrice = $discount;
            $newDiscount = $price;

            $updateStmt->execute([
                ':new_price'    => $newPrice,
                ':new_discount' => $newDiscount,
                ':id'           => $id,
            ]);

            $processed++;

            $lastId = $id;
        }

        $conn_pdo->commit();

    } catch (Throwable $e) {

        if ($conn_pdo->inTransaction()) {
            $conn_pdo->rollBack();
        }

        echo PHP_EOL;
        echo "ERRO NO LOTE." . PHP_EOL;
        echo $e->getMessage() . PHP_EOL;

        exit(1);
    }

    // =================================================
    // PROGRESSO
    // =================================================

    $percent = $total > 0
        ? ($processed / $total) * 100
        : 100;

    $elapsed = microtime(true) - $startTime;

    $rate = $elapsed > 0
        ? $processed / $elapsed
        : 0;

    $remaining = $total - $processed;

    $eta = $rate > 0
        ? $remaining / $rate
        : 0;

    echo sprintf(
        "\rProcessados: %d/%d (%.2f%%) | %.0f produtos/s | ETA: %s",
        $processed,
        $total,
        $percent,
        $rate,
        gmdate('H:i:s', (int) $eta)
    );

    // Pequena pausa para não pressionar o banco
    usleep(100000);
}

$elapsed = microtime(true) - $startTime;

echo PHP_EOL;
echo PHP_EOL;
echo "==============================================" . PHP_EOL;
echo " CORREÇÃO FINALIZADA" . PHP_EOL;
echo "==============================================" . PHP_EOL;
echo "Produtos processados: {$processed}" . PHP_EOL;
echo "Tempo total: " . gmdate('H:i:s', (int) $elapsed) . PHP_EOL;
echo "==============================================" . PHP_EOL;