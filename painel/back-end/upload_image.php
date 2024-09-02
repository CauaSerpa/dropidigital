<?php
    session_start();
    include_once('../../config.php');

    $shop_id = $_POST['shop_id'];

    // Verifica se o arquivo foi enviado
    if ($_FILES['file']['name']) {
        // Diretório para salvar as imagens
        $diretorioImage = "./uploads/$shop_id/";

        // Cria o diretório se não existir
        if (!is_dir($diretorioImage)) {
            mkdir($diretorioImage, 0755, true);
        }

        // Nome do arquivo
        $fileName = time() . '.jpg';
        $tmp_name = $_FILES['file']['tmp_name'];
        $uploadFile = $diretorioImage . basename($fileName);

        // Move o arquivo para o diretório de upload
        if (move_uploaded_file($tmp_name, $uploadFile)) {
            // Retorna a URL da imagem
            $location = INCLUDE_PATH_DASHBOARD . "back-end/uploads/$shop_id/$fileName";
            echo json_encode(['location' => $location]);
        } else {
            // Retorna erro
            header("HTTP/1.1 500 Internal Server Error");
        }
    }