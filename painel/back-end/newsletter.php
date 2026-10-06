<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Verifique se o checkbox foi marcado
        if (isset($_POST['modal'])) { 
            $modal = $_POST['modal'];
            
            $modal_time = $_POST['modal_time'];
            if ($modal_time == 1) {
                $modal_time_seconds = $_POST['modal_time_seconds'];
            } else if ($modal_time == 2) {
                $modal_time_seconds = 0;
            } else if ($modal_time == 3) {
                $modal_time_seconds = "exit";
            }
            $modal_location = $_POST['modal_location'];

            $modal_title = $_POST['modal_title'];
            $modal_text = $_POST['modal_text'];
            $modal_success_text = $_POST['modal_success_text'];
        } else {
            $modal = 0;
        }

        // Verifique se o checkbox foi marcado
        if (isset($_POST['footer'])) { 
            $footer = $_POST['footer'];

            $footer_text = $_POST['footer_text'];
        } else {
            $footer = 0;
        }

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET newsletter_modal = :newsletter_modal, newsletter_modal_title = :newsletter_modal_title, newsletter_modal_text = :newsletter_modal_text,
                            newsletter_modal_success_text = :newsletter_modal_success_text, newsletter_modal_time = :newsletter_modal_time, newsletter_modal_time_seconds = :newsletter_modal_time_seconds,
                            newsletter_modal_location = :newsletter_modal_location, newsletter_footer = :newsletter_footer, newsletter_footer_text = :newsletter_footer_text
                            WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':newsletter_modal', $modal);
        $stmt->bindValue(':newsletter_modal_title', $modal_title);
        $stmt->bindValue(':newsletter_modal_text', $modal_text);
        $stmt->bindValue(':newsletter_modal_success_text', $modal_success_text);
        $stmt->bindValue(':newsletter_modal_time', $modal_time);
        $stmt->bindValue(':newsletter_modal_time_seconds', $modal_time_seconds);
        $stmt->bindValue(':newsletter_modal_location', $modal_location);
        $stmt->bindValue(':newsletter_footer', $footer);
        $stmt->bindValue(':newsletter_footer_text', $footer_text);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Newsletter editada com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "newsletter");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar a newsletter!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "newsletter");
            exit;
        }
    }