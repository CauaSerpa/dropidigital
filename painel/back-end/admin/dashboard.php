<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    // Diretório onde as imagens serão salvas
    $upload_dir = '../dashboard/';

    // Obter o ID do registro (se existir)
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    // Verifica se o botão de remover imagem foi clicado
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        // Consulta para obter a imagem atual
        $sql = "SELECT ready_site_image FROM tb_dashboard WHERE id = :id LIMIT 1";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $dash = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dash && !empty($dash['ready_site_image'])) {
            $old_image = $upload_dir . $dash['ready_site_image'];

            // Remove a imagem do diretório
            if (file_exists($old_image)) {
                unlink($old_image);
            }

            // Atualiza o banco de dados para remover a referência da imagem
            $sql = "DELETE FROM tb_dashboard WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $_SESSION['msg'] = "<p class='green'>Imagem removida com sucesso.</p>";
            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: " . INCLUDE_PATH_DASHBOARD . "imagem-painel");
        } else {
            $_SESSION['msg'] = "<p class='red'>Nenhuma imagem encontrada para remover.</p>";
            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: " . INCLUDE_PATH_DASHBOARD . "imagem-painel");
        }

    } else {

        // Verifica se o formulário foi enviado
        if (isset($_POST['SendUpdDashboard'])) {
            // Verifica se há um arquivo de imagem sendo enviado
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['image'];

                // Verifica o tipo de arquivo
                $allowed_types = ['image/jpeg', 'image/png'];
                if (!in_array($image['type'], $allowed_types)) {
                    die('Formato de imagem inválido. Apenas JPG e PNG são permitidos.');
                }

                // Define o caminho do arquivo a ser salvo
                $image_name = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
                $image_path = $upload_dir . $image_name;

                // Mover a imagem para o diretório de upload
                if (move_uploaded_file($image['tmp_name'], $image_path)) {

                    // Consulta SQL para verificar se já existe uma linha
                    $sql = "SELECT * FROM tb_dashboard WHERE id = :id LIMIT 1";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $dash = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Se já existir uma imagem no banco de dados, remove a imagem antiga
                    if ($dash && !empty($dash['ready_site_image'])) {
                        $old_image = $upload_dir . $dash['ready_site_image'];
                        if (file_exists($old_image)) {
                            unlink($old_image); // Remove a imagem antiga
                        }

                        // Atualiza a linha existente com a nova imagem
                        $sql = "UPDATE tb_dashboard SET ready_site_image = :image WHERE id = :id";
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':image', $image_name);
                        $stmt->bindParam(':id', $id);
                        $stmt->execute();
                    } else {
                        // Se não houver uma linha, insere uma nova
                        $sql = "INSERT INTO tb_dashboard (ready_site_image) VALUES (:image)";
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':image', $image_name);
                        $stmt->execute();
                    }

                    $_SESSION['msg'] = "<p class='green'>Imagem salva com sucesso.</p>";
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "imagem-painel");
                } else {
                    $_SESSION['msg'] = "<p class='red'>Falha ao fazer upload da imagem.</p>";
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "imagem-painel");
                }
            } else {
                $_SESSION['msg'] = "<p class='red'>Nenhuma imagem enviada.</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "imagem-painel");
            }
        }

    }