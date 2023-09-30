<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['center_highlight']) && is_array($_POST['center_highlight'])) {
            $selectedValues = $_POST['center_highlight'];
            
            // Use implode para criar uma string com os valores separados por ", "
            $separatedValues = implode(', ', $selectedValues);

            echo $separatedValues;


        } else {
            echo "Nenhum checkbox foi selecionado.";
        }
    }

    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $shop_id = $_POST['shop_id'];

        // Verifique se o checkbox foi marcado
        if (isset($_POST['top_highlight_bar'])) { 
            $top_highlight_bar = $_POST['top_highlight_bar'];
        } else {
            $top_highlight_bar = 0;
        }

        $top_highlight_bar_location = $_POST['top_highlight_bar_location'];
        $top_highlight_bar_text = $_POST['top_highlight_bar_text'];
        $top_highlight_bar_link = $_POST['top_highlight_bar_link'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';

        // Insere a categoria no banco de dados da loja
        $sql = "UPDATE $tabela SET top_highlight_bar = :top_highlight_bar, top_highlight_bar_location = :top_highlight_bar_location, top_highlight_bar_text = :top_highlight_bar_text, top_highlight_bar_link = :top_highlight_bar_link, center_highlight_images = :center_highlight_images WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':top_highlight_bar', $top_highlight_bar);
        $stmt->bindValue(':top_highlight_bar_location', $top_highlight_bar_location);
        $stmt->bindValue(':top_highlight_bar_text', $top_highlight_bar_text);
        $stmt->bindValue(':top_highlight_bar_link', $top_highlight_bar_link);
        $stmt->bindValue(':center_highlight_images', $separatedValues);

        // Id que sera editado
        $stmt->bindValue(':id', $shop_id);

        if ($stmt->execute()) {
            $_SESSION['msgcad'] = "<p class='green'>Tarja editada com sucesso!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "tarja");
            exit;
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro ao editar a tarja!</p>";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "tarja");
            exit;
        }
    }