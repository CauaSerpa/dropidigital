<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    
    // Acessa o IF quando o usuário clicar no botão
    if (empty($dados['SendAddProduct'])) {
        // Função para gerar um nome de arquivo aleatório
        function generateRandomFileName($prefix = '', $extension = '') {
            $uniquePart = uniqid('', true);  // Remova o prefixo da chamada uniqid
            $randomFileName = $prefix . $uniquePart . $extension;
            return $randomFileName;
        }

        //Tabela que será solicitada
        $tabela = 'tb_shop';
        
        // Verifica se o campo de upload de imagens não está vazio para 'logo'
        if ($_FILES['logo']['error'] !== 4) {
            // Obtém o nome do arquivo existente
            $sql = "SELECT logo FROM $tabela WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':id', $dados['shop_id']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $existingLogo = $result['logo'];

            // Remove o arquivo antigo, se existir
            if (!empty($existingLogo)) {
                $existingLogoPath = "logos/{$dados['shop_id']}/$existingLogo";
                if (file_exists($existingLogoPath)) {
                    unlink($existingLogoPath);
                }
            }

            // Processar o upload de imagens
            $uploadDir = "logos/{$dados['shop_id']}/";

            // Certifique-se de que os diretórios de destino existam
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = generateRandomFileName('logo_', '.png');
            $tmp_name = $_FILES['logo']['tmp_name'];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Atualizar o banco de dados com o novo nome do arquivo
                $sql = "UPDATE $tabela SET logo = :logo WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':logo', $fileName);
                $stmt->bindValue(':id', $dados['shop_id']);
                $stmt->execute();
            }
        }

        // Verifica se o campo de upload de imagens não está vazio para 'logo_mobile'
        if ($_FILES['logo_mobile']['error'] !== 4) {
            // Obtém o nome do arquivo existente
            $sql = "SELECT logo_mobile FROM $tabela WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':id', $dados['shop_id']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $existingLogo = $result['logo_mobile'];

            // Remove o arquivo antigo, se existir
            if (!empty($existingLogo)) {
                $existingLogoPath = "logos/{$dados['shop_id']}/$existingLogo";
                if (file_exists($existingLogoPath)) {
                    unlink($existingLogoPath);
                }
            }

            // Processar o upload de imagens
            $uploadDir = "logos/{$dados['shop_id']}/";

            // Certifique-se de que os diretórios de destino existam
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = generateRandomFileName('logo_mobile_', '.png');
            $tmp_name = $_FILES['logo_mobile']['tmp_name'];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Atualizar o banco de dados com o novo nome do arquivo
                $sql = "UPDATE $tabela SET logo_mobile = :logo_mobile WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':logo_mobile', $fileName);
                $stmt->bindValue(':id', $dados['shop_id']);
                $stmt->execute();
            }
        }

        // Verifica se o campo de upload de imagens não está vazio para 'favicon'
        if ($_FILES['favicon']['error'] !== 4) {
            // Obtém o nome do arquivo existente
            $sql = "SELECT favicon FROM $tabela WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':id', $dados['shop_id']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $existingLogo = $result['favicon'];

            // Remove o arquivo antigo, se existir
            if (!empty($existingLogo)) {
                $existingLogoPath = "logos/{$dados['shop_id']}/$existingLogo";
                if (file_exists($existingLogoPath)) {
                    unlink($existingLogoPath);
                }
            }

            // Processar o upload de imagens
            $uploadDir = "logos/{$dados['shop_id']}/";

            // Certifique-se de que os diretórios de destino existam
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = generateRandomFileName('favicon_', '.png');
            $tmp_name = $_FILES['favicon']['tmp_name'];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Atualizar o banco de dados com o novo nome do arquivo
                $sql = "UPDATE $tabela SET favicon = :favicon WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':favicon', $fileName);
                $stmt->bindValue(':id', $dados['shop_id']);
                $stmt->execute();
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Logo adicionada com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "logo");
        exit;
    }