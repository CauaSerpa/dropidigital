<?php
    include_once('../../../config.php');
    
    $user_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

    if (!empty($user_id)) {
        session_start();

        $admin_id = $_SESSION['user_id'];

        session_destroy();
        session_start();
        ob_start();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['admin_id'] = $admin_id;

        header("Location: " . INCLUDE_PATH_DASHBOARD);
        exit();
    }