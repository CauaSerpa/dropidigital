<?php
    require dirname(dirname(dirname(__DIR__))) . '/vendor/autoload.php';

    use Aws\S3\S3Client;
    use Aws\Exception\AwsException;

    $s3 = new S3Client([
        'version' => 'latest',
        'region'  => $_ENV['AWS_REGION'],
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ],
    ]);

    $bucket = $_ENV['AWS_BUCKET'];

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['msg'] = "<p class='red'>Produto inválido.</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
        exit;
    }

    try {
        // Inicia transação
        $conn_pdo->beginTransaction();

        // 1️⃣ Busca imagens do produto
        $stmt = $conn_pdo->prepare("
            SELECT id, s3_path
            FROM imagens
            WHERE usuario_id = :product_id
        ");
        $stmt->execute([':product_id' => $id]);
        $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2️⃣ Remove imagens do S3
        foreach ($imagens as $img) {
            try {
                $s3->deleteObject([
                    'Bucket' => $bucket,
                    'Key'    => $img['s3_path']
                ]);
            } catch (AwsException $e) {
                // Log opcional — não quebra o fluxo
            }
        }

        // 3️⃣ Remove imagens do banco
        $stmt = $conn_pdo->prepare("
            DELETE FROM imagens
            WHERE usuario_id = :product_id
        ");
        $stmt->execute([':product_id' => $id]);

        // 4️⃣ Remove produto
        $stmt = $conn_pdo->prepare("
            DELETE FROM tb_products
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('Produto não encontrado.');
        }

        // 5️⃣ Commit
        $conn_pdo->commit();

        $_SESSION['msg'] = "<p class='green'>Produto e imagens removidos com sucesso!</p>";

    } catch (Exception $e) {

        // Rollback se algo falhar
        if ($conn_pdo->inTransaction()) {
            $conn_pdo->rollBack();
        }

        $_SESSION['msg'] = "<p class='red'>Erro ao remover produto.</p>";
    }

    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
    exit;