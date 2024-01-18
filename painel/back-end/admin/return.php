<?php
    include_once('../../../config.php');

    session_start();

    $user_id = $_SESSION['user_id'];
    $admin_id = $_SESSION['admin_id'];

    if (!empty($admin_id)) {
        session_destroy();
        session_start();
        ob_start();

        $_SESSION['user_id'] = $admin_id;

        header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $user_id);
        exit();
    }