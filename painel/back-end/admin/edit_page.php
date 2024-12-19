<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    // Verificar se os dados foram enviados
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $help_page = filter_input(INPUT_POST, 'help_page', FILTER_SANITIZE_URL);
        $video_location = filter_input(INPUT_POST, 'video_location', FILTER_SANITIZE_URL);
        $tutorial_video = filter_input(INPUT_POST, 'tutorial_video', FILTER_SANITIZE_URL);

        // Definir como NULL se estiver vazio
        $description = !empty($description) ? $description : null;
        $help_page = !empty($help_page) ? $help_page : null;
        $video_location = ($video_location != 'disabled') ? $video_location : null;
        $tutorial_video = ($video_location != 'disabled') ? $tutorial_video : null;

        // Verificar se o ID é válido
        if (!empty($id)) {
            // Query para atualizar os dados
            $sql = "UPDATE tb_routes SET name = :name, title = :title, description = :description, help_page = :help_page, video_location = :video_location, tutorial_video = :tutorial_video WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);

            // Bind dos parâmetros
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':help_page', $help_page);
            $stmt->bindParam(':video_location', $video_location);
            $stmt->bindParam(':tutorial_video', $tutorial_video);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Executar a query
            if ($stmt->execute()) {
                $_SESSION['msgcad'] = "<p class='green'>Dados atualizados com sucesso!</p>";
                header("Location: ".INCLUDE_PATH_DASHBOARD."editar-pagina?id=".$id);
            } else {
                $_SESSION['msgcad'] = "<p class='red'>Erro ao atualizar os dados.</p>";
                header("Location: ".INCLUDE_PATH_DASHBOARD."paginas");
            }
        } else {
            $_SESSION['msgcad'] = "<p class='red'>ID inválido.</p>";
            header("Location: ".INCLUDE_PATH_DASHBOARD."paginas");
        }
    }
?>