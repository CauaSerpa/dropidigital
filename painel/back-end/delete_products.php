<?php
    session_start();
    ob_start();

    include_once('../../config.php');

    require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

    use Aws\S3\S3Client;

    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $_ENV['AWS_REGION'],
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ],
    ]);

    $bucket = $_ENV['AWS_BUCKET'];

    if (!isset($_POST['selected_ids']) || !is_array($_POST['selected_ids'])) {
        $_SESSION['msg'] = "<p class='red'>Nenhum produto encontrado para exclusão.</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
        exit;
    }

    foreach ($_POST['selected_ids'] as $productId) {

        // ================================
        // 1️⃣ BUSCA IMAGENS DO PRODUTO
        // ================================
        $stmt = $conn_pdo->prepare("
            SELECT id, s3_path 
            FROM imagens 
            WHERE usuario_id = :produto_id
        ");
        $stmt->execute([':produto_id' => $productId]);
        $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ================================
        // 2️⃣ DELETE IMAGENS NO S3
        // ================================
        foreach ($imagens as $img) {
            if (!empty($img['s3_path'])) {
                try {
                    $s3->deleteObject([
                        'Bucket' => $bucket,
                        'Key'    => $img['s3_path']
                    ]);
                } catch (AwsException $e) {
                    // Log opcional (não interrompe)
                    error_log("Erro ao deletar S3: " . $e->getMessage());
                }
            }
        }

        // ================================
        // 3️⃣ DELETE IMAGENS NO BANCO
        // ================================
        $stmt = $conn_pdo->prepare("
            DELETE FROM imagens 
            WHERE usuario_id = :produto_id
        ");
        $stmt->execute([':produto_id' => $productId]);

        // ================================
        // 4️⃣ DELETE PRODUTO
        // ================================
        $stmt = $conn_pdo->prepare("
            DELETE FROM tb_products 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $productId]);

        // ================================
        // 5️⃣ (OPCIONAL) LIMPA PASTA LOCAL
        // ================================
        $localDir = __DIR__ . "/imagens/$productId/";
        if (is_dir($localDir)) {
            array_map('unlink', glob("$localDir/*"));
            rmdir($localDir);
        }
    }

    $_SESSION['msg'] = "<p class='green'>Produtos e imagens deletados com sucesso!</p>";
    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
    exit;