<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (isset($_POST['status']) && $_POST['status'] == '1') {
        $status = $_POST['status'];
    } else {
        $status = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_pages';

        // Edita o produto no banco de dados da loja
        $sql = "UPDATE $tabela SET status = :status, name = :name, link = :link, content = :content, status = :status, seo_name = :seo_name, seo_link = :seo_link, seo_description = :seo_description WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':name', $dados['name']);
        $stmt->bindValue(':link', $dados['link']);
        $stmt->bindValue(':content', $dados['content']);
        $stmt->bindValue(':seo_name', $dados['seo_name']);
        $stmt->bindValue(':seo_link', $dados['seo_link']);
        $stmt->bindValue(':seo_description', $dados['seo_description']);

        // Id que sera editado
        $stmt->bindValue(':id', $dados['id']);

        $stmt->execute();

        $_SESSION['msgcad'] = "<p class='green'>Página editada com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "paginas");
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao atualizar a página!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "paginas");
    }