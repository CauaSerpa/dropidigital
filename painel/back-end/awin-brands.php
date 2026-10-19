<?php

declare(strict_types=1);

include_once('../../config.php');

header('Content-Type: application/json; charset=utf-8');


try {

    $language = trim(
        (string) (
            $_GET['language']
            ?? ''
        )
    );


    if ($language === '') {

        throw new Exception(
            'Idioma não informado.'
        );
    }


    if (
        !isset($conn_pdo)
        || !$conn_pdo instanceof PDO
    ) {

        throw new RuntimeException(
            'Conexão PDO não encontrada.'
        );
    }


    $stmt = $conn_pdo->prepare(
        "
        SELECT
            brand_id,
            brand_name,
            product_count
        FROM tb_awin_brands
        WHERE language = :language
          AND active = 1
        ORDER BY
            product_count DESC,
            brand_name ASC
        "
    );


    $stmt->execute([
        ':language' => $language,
    ]);


    $brands = [];


    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $brands[] = [

            'brand_id' =>
                $row['brand_id'],

            'name' =>
                $row['brand_name'],

            'products' =>
                (int) $row['product_count'],

        ];
    }


    echo json_encode([
        'success' => true,
        'brands' => $brands,
    ], JSON_UNESCAPED_UNICODE);


} catch (Throwable $e) {

    http_response_code(400);

    echo json_encode([

        'success' => false,

        'message' =>
            $e->getMessage(),

    ], JSON_UNESCAPED_UNICODE);
}