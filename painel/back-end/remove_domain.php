<?php
    session_start();
    ob_start();

    include_once('../../config.php');

    // Certifique-se de que o ID seja um número inteiro válido
    $id = $_POST['id'];

    $query = "DELETE FROM tb_domains WHERE id = :id";
    $stmt = $conn_pdo->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Redirecionar após a exclusão
        $_SESSION['msg'] = "<p class='green'>Domínio removido com sucesso.</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
        exit;
    } else {
        $_SESSION['msg'] = "<p class='red'>Não foi possível remover o domínio.</p>";
        // Redirecionar se nenhum ID de código HTML foi passado
        header("Location: " . INCLUDE_PATH_DASHBOARD . "configurar-dominio");
        exit;
    }