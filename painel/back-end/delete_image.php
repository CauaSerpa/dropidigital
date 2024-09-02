<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['src'])) {
            $src = $_POST['src'];

            // Extrair o caminho relativo da URL da imagem
            $relativePath = str_replace(INCLUDE_PATH, '../../', $src);

            // Verifique se o arquivo existe e exclua-o
            if (file_exists($relativePath)) {
                if (unlink($relativePath)) {
                    echo json_encode(['status' => 'success']);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete image']);
                }
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Image not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }