<?php
    session_start();
    ob_start();

    include_once('../../config.php');

    if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
        foreach ($_POST['selected_ids'] as $selectedId) {
            // Certifique-se de que o ID seja um número inteiro válido
            $articleId = (int) $selectedId;

            // Verifique se o ID é maior que zero (um ID válido)
            if ($articleId > 0) {
                $tabela = "tb_articles";

                // Consulta para obter o diretório da imagem
                $query = "SELECT image FROM $tabela WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $articleId);
                $stmt->execute();

                // Obtenha o ID do usuário
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Nome da imagem antiga
                $image = $row['image'];

                echo $articleId;
                echo $image;

                // Diretório do banner
                $diretorio = "./articles/$articleId/";
                
                // Diretório para deletar as imagens antigas de 'image'
                $caminhoImagem = $diretorio . basename($image);
                
                // Excluir a imagem existente de 'image'
                if (file_exists($caminhoImagem)) {
                    unlink($caminhoImagem);
                }

                // Exclua o diretório do usuário
                rmdir($diretorio);

                // Consulta para excluir o banner do banco de dados
                $query = "DELETE FROM $tabela WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $articleId);
                $stmt->execute();
            }
        }

        // Redirecionar após a exclusão
        $_SESSION['msg'] = "<p class='green'>Artigo(s) deletado(s) com sucesso!</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "artigos");
    } else {
        $_SESSION['msg'] = "<p class='red'>Nenhum artigo selecionado para exclusão.</p>";
        // Redirecionar se nenhum ID de categoria foi passado
        header("Location: " . INCLUDE_PATH_DASHBOARD . "artigos");
        exit;
    }