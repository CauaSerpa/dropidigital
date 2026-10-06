<?php

session_start();
ob_start();

include_once('../../config.php');

$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

$status = (isset($_POST['status']) && $_POST['status'] == '1') ? 1 : 0;
$emphasis = (isset($_POST['emphasis']) && $_POST['emphasis'] == '1') ? 1 : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tabela = 'tb_articles';

    try {

        $conn_pdo->beginTransaction();

        /*
         * Cria o artigo
         */
        $sql = "INSERT INTO $tabela (
                    shop_id,
                    status,
                    emphasis,
                    name,
                    content,
                    seo_name,
                    link,
                    seo_description
                ) VALUES (
                    :shop_id,
                    :status,
                    :emphasis,
                    :name,
                    :content,
                    :seo_name,
                    :link,
                    :seo_description
                )";

        $stmt = $conn_pdo->prepare($sql);

        $stmt->bindValue(':shop_id', $dados['id']);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':emphasis', $emphasis);
        $stmt->bindValue(':name', $dados['name']);
        $stmt->bindValue(':content', $dados['content']);
        $stmt->bindValue(':seo_name', $dados['seo_name']);
        $stmt->bindValue(':link', $dados['seo_link']);
        $stmt->bindValue(':seo_description', $dados['seo_description']);

        $stmt->execute();

        /*
         * ID do artigo criado
         */
        $ultimo_id = $conn_pdo->lastInsertId();

        /*
         * Produtos associados ao artigo
         */
        $product_ids = $_POST['product_id'] ?? [];

        if (!is_array($product_ids)) {
            $product_ids = [$product_ids];
        }

        /*
         * Remove valores vazios e duplicados
         */
        $product_ids = array_filter(
            array_unique($product_ids),
            fn($id) => filter_var($id, FILTER_VALIDATE_INT) !== false
        );

        /*
         * Cria os vínculos artigo x produto
         */
        if (!empty($product_ids)) {

            $sqlProduct = "
                INSERT INTO tb_article_products (
                    article_id,
                    product_id
                ) VALUES (
                    :article_id,
                    :product_id
                )
            ";

            $stmtProduct = $conn_pdo->prepare($sqlProduct);

            foreach ($product_ids as $product_id) {

                $stmtProduct->execute([
                    ':article_id' => $ultimo_id,
                    ':product_id' => (int) $product_id
                ]);
            }
        }

        /*
         * Diretório das imagens
         */
        $diretorioImage = "./articles/$ultimo_id/";

        if (!is_dir($diretorioImage)) {
            mkdir($diretorioImage, 0755, true);
        }

        /*
         * Upload da imagem principal
         */
        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            $fileName = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];

            $uploadFile = $diretorioImage . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {

                $sql = "
                    UPDATE $tabela
                    SET image = :image
                    WHERE id = :id
                ";

                $stmt = $conn_pdo->prepare($sql);

                $stmt->execute([
                    ':image' => $fileName,
                    ':id' => $ultimo_id
                ]);
            }
        }

        /*
         * Confirma todas as alterações
         */
        $conn_pdo->commit();

        $_SESSION['msgcad'] = "<p class='green'>Artigo criado com sucesso!</p>";

        header(
            "Location: " . INCLUDE_PATH_DASHBOARD . "artigos"
        );

        exit;

    } catch (Throwable $e) {

        /*
         * Desfaz tudo caso alguma etapa falhe
         */
        if ($conn_pdo->inTransaction()) {
            $conn_pdo->rollBack();
        }

        $_SESSION['msg'] = "<p class='red'>Erro ao criar o artigo!</p>";

        /*
         * Idealmente registrar o erro no log
         */
        error_log($e->getMessage());

        header(
            "Location: " . INCLUDE_PATH_DASHBOARD . "artigos"
        );

        exit;
    }
}