<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Recuperando two_factors gerado
    if ($_SESSION['two_factors'] == $_POST['two_factors'])
    {
        unset($_SESSION['two_factors']);

        $_SESSION['2fa'] = true;
        $_SESSION['user_id'] = $_SESSION['user_id_for_2fa'];

        header("Location: ".INCLUDE_PATH_DASHBOARD);
    } else {
        $_SESSION['msgcad'] = 'Código inválido!';
        header("Location: ".INCLUDE_PATH_DASHBOARD."dois-fatores");
    }