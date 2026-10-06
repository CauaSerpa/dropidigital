<?php

session_start();
ob_start();

include_once('../../config.php');

// Receber os dados do formulário
$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

$status = (isset($_POST['status']) && $_POST['status'] == '1') ? 1 : 0;
$emphasis = (isset($_POST['emphasis']) && $_POST['emphasis'] == '1') ? 1 : 0;

// Define o fuso horário para São Paulo
date_default_timezone_set('America/Sao_Paulo');

// Data/hora atual
$datetime = new DateTime();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tabela = 'tb_articles';

    $id = filter_var($dados['id'] ?? null, FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['msg'] = "<p class='red'>Artigo inválido!</p>";

        header(
            "Location: " . INCLUDE_PATH_DASHBOARD . "artigos"
        );

        exit;
    }

    /*
     * Produtos associados ao artigo
     */
    $product_ids = $_POST['product_id'] ?? [];

    // Garante que sempre trabalharemos com array
    if (!is_array($product_ids)) {
        $product_ids = [$product_ids];
    }

    /*
     * Remove valores inválidos, vazios e duplicados
     */
    $product_ids = array_filter(
        array_unique($product_ids),
        function ($product_id) {
            return filter_var($product_id, FILTER_VALIDATE_INT) !== false;
        }
    );

    try {

        /*
         * Inicia uma transação.
         *
         * Assim, caso alguma etapa falhe,
         * nenhuma alteração fica parcialmente salva.
         */
        $conn_pdo->beginTransaction();

        /*
         * Atualiza o artigo
         */
        $sql = "
            UPDATE $tabela
            SET
                status = :status,
                emphasis = :emphasis,
                name = :name,
                content = :content,
                seo_name = :seo_name,
                link = :link,
                seo_description = :seo_description,
                last_modification = :last_modification
            WHERE id = :id
        ";

        $stmt = $conn_pdo->prepare($sql);

        $stmt->bindValue(':status', $status, PDO::PARAM_INT);
        $stmt->bindValue(':emphasis', $emphasis, PDO::PARAM_INT);
        $stmt->bindValue(':name', $dados['name'] ?? '');
        $stmt->bindValue(':content', $dados['content'] ?? '');
        $stmt->bindValue(':seo_name', $dados['seo_name'] ?? '');
        $stmt->bindValue(':link', $dados['seo_link'] ?? '');
        $stmt->bindValue(':seo_description', $dados['seo_description'] ?? '');
        $stmt->bindValue(':last_modification', $datetime->format('Y-m-d H:i:s'));
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();


        /*
         * ============================================================
         * ATUALIZA OS PRODUTOS ASSOCIADOS
         * ============================================================
         */

        /*
         * Primeiro remove todos os vínculos atuais
         */
        $sqlDeleteProducts = "
            DELETE FROM tb_article_products
            WHERE article_id = :article_id
        ";

        $stmtDeleteProducts = $conn_pdo->prepare($sqlDeleteProducts);

        $stmtDeleteProducts->execute([
            ':article_id' => $id
        ]);


        /*
         * Depois recria os vínculos enviados pelo formulário
         */
        if (!empty($product_ids)) {

            $sqlInsertProduct = "
                INSERT INTO tb_article_products (
                    article_id,
                    product_id
                ) VALUES (
                    :article_id,
                    :product_id
                )
            ";

            $stmtInsertProduct = $conn_pdo->prepare($sqlInsertProduct);

            foreach ($product_ids as $product_id) {

                $stmtInsertProduct->execute([
                    ':article_id' => $id,
                    ':product_id' => (int) $product_id
                ]);
            }
        }


        /*
         * ============================================================
         * IMAGEM
         * ============================================================
         */

        // Consulta para obter a imagem atual
        $query = "
            SELECT image
            FROM $tabela
            WHERE id = :id
        ";

        $stmt = $conn_pdo->prepare($query);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);


        /*
         * Diretório das imagens
         */
        $diretorioImage = "./articles/$id/";

        if (!is_dir($diretorioImage)) {
            mkdir($diretorioImage, 0755, true);
        }


        /*
         * Verifica se foi enviada uma nova imagem
         */
        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            /*
             * Nome da imagem antiga
             */
            $imagemAntiga = $row['image'] ?? '';

            /*
             * Exclui a imagem antiga
             */
            if (!empty($imagemAntiga)) {

                $caminhoImagemAntiga =
                    $diretorioImage . basename($imagemAntiga);

                if (file_exists($caminhoImagemAntiga)) {
                    unlink($caminhoImagemAntiga);
                }
            }


            /*
             * Nova imagem
             */
            $fileName = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];

            $uploadFile =
                $diretorioImage . basename($fileName);


            if (move_uploaded_file($tmp_name, $uploadFile)) {

                $sql = "
                    UPDATE $tabela
                    SET image = :image
                    WHERE id = :id
                ";

                $stmt = $conn_pdo->prepare($sql);

                $stmt->bindValue(':image', $fileName);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);

                $stmt->execute();
            }
        }


        /*
         * Confirma todas as alterações
         */
        $conn_pdo->commit();


        $_SESSION['msgcad'] =
            "<p class='green'>Artigo editado com sucesso!</p>";

        header(
            "Location: " . INCLUDE_PATH_DASHBOARD . "artigos"
        );

        exit;


    } catch (Throwable $e) {

        /*
         * Caso alguma operação falhe,
         * desfaz todas as alterações.
         */
        if ($conn_pdo->inTransaction()) {
            $conn_pdo->rollBack();
        }

        error_log(
            'Erro ao editar artigo ' . $id . ': ' . $e->getMessage()
        );

        $_SESSION['msg'] =
            "<p class='red'>Erro ao atualizar o artigo!</p>";

        header(
            "Location: " . INCLUDE_PATH_DASHBOARD . "artigos"
        );

        exit;
    }

} else {

    $_SESSION['msg'] =
        "<p class='red'>Erro ao atualizar o artigo!</p>";

    header(
        "Location: " . INCLUDE_PATH_DASHBOARD . "artigos"
    );

    exit;
}