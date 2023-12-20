<?php
    session_start();
    session_destroy();
    session_start();
    ob_start();
    $_SESSION['msgcad'] = "Deslogado com sucesso!";
    header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
    exit();