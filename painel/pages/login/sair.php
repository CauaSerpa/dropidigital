<?php
    include_once('../../../config.php');
    session_start();
    clearRememberLoginCookie($conn_pdo);
    session_destroy();
    session_start();
    ob_start();
    $_SESSION['msgcad'] = "Deslogado com sucesso!";
    header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
    exit();