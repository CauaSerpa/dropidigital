<?php
    session_start();
    ob_start();

    include_once('../../config.php');

    if (isset($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
        foreach ($_POST['selected_ids'] as $selectedId) {
            // Certifique-se de que o ID seja um número inteiro válido
            $categoryId = (int) $selectedId;

            // Verifique se o ID é maior que zero (um ID válido)
            if ($categoryId > 0) {
                // Consulta para excluir a categoria do banco de dados
                $query = "DELETE FROM tb_categories WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $categoryId);

                if ($stmt->execute()) {
                    $_SESSION['msg'] = "<p class='green'>Categoria deletada com sucesso!</p>";
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao deletar categoria.</p>";
                }
            }
        }

        // Redirecionar após a exclusão
        header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
        exit;
    } else {
        $_SESSION['msg'] = "<p class='red'>Nenhuma categoria selecionada para exclusão.</p>";
        // Redirecionar se nenhum ID de categoria foi passado
        header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
        exit;
    }