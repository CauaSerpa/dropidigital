<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Função para gerar um nome de arquivo aleatório
    function generateRandomFileName($prefix = '', $extension = '') {
        $uniquePart = uniqid('', true);  // Remova o prefixo da chamada uniqid
        $randomFileName = $prefix . $uniquePart . $extension;
        return $randomFileName;
    }

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if (isset($_POST['status']) && $_POST['status'] == '1') {
        $status = $_POST['status'];
    } else {
        $status = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_banner_info';

        // Edita o produto no banco de dados da loja
        $sql = "UPDATE $tabela SET name = :name, location = :location, category = :category, link = :link, target = :target, title = :title, status = :status WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':name', $dados['name']);
        $stmt->bindParam(':location', $dados['location']);
        $stmt->bindParam(':category', $dados['category']);
        $stmt->bindParam(':link', $dados['link']);
        $stmt->bindParam(':target', $dados['target']);
        $stmt->bindParam(':title', $dados['title']);
        $stmt->bindParam(':status', $status);

        // Id que sera editado
        $stmt->bindValue(':id', $dados['id']);

        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_banner_img';
        
        // Recupere o ID da loja
        $ultimo_id = $dados['shop_id'];
        
        // Recupere o ID da categoria
        $id = $dados['id'];

        // Consulta para obter o nome das imagens
        $query = "SELECT image_name, mobile_banner FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $dados['image_id']);
        $stmt->execute();
        
        // Obtenha o nome das imagens
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Diretório para salvar as imagens de 'image'
        $diretorioImage = "./banners/$id/";

        // Certifique-se de que os diretórios de destino existam
        if (!is_dir($diretorioImage)) {
            mkdir($diretorioImage, 0755, true);
        }

        // Verifique se o campo de upload de imagens não está vazio para 'image'
        if ($_FILES['image']['error'] !== 4) {
            // Nome da imagem antiga
            $imagemAntiga = $row['image_name'];

            // Diretório para deletar as imagens antigas de 'image'
            $caminhoImagemAntiga = $diretorioImage . basename($imagemAntiga);

            // Excluir a imagem existente de 'image'
            if (file_exists($caminhoImagemAntiga)) {
                unlink($caminhoImagemAntiga);
            }

            // Cadastra nova imagem para 'image'
            $fileName = generateRandomFileName('image_banner_', '.jpg');
            $tmp_name = $_FILES['image']['tmp_name'];
            $uploadFile = $diretorioImage . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sql = "UPDATE tb_banner_img SET image_name = :image_name WHERE banner_id = :banner_id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':image_name', $fileName);
                $stmt->bindParam(':banner_id', $id);

                $stmt->execute();
            }
        }

        // Verifique se o campo de upload de imagens não está vazio para 'mobile'
        if ($_FILES['mobile']['error'] !== 4 && $dados['location'] == "full-banner") {
            // Nome da imagem antiga
            $imagemAntiga = $row['mobile_banner'];

            // Diretório para deletar as imagens antigas de 'mobile'
            $caminhoImagemAntiga = $diretorioImage . basename($imagemAntiga);

            // Excluir a imagem existente de 'mobile'
            if (file_exists($caminhoImagemAntiga)) {
                unlink($caminhoImagemAntiga);
            }

            // Cadastra nova imagem para 'mobile'
            $fileName = generateRandomFileName('mobile_banner_', '.jpg');
            $tmp_name = $_FILES['mobile']['tmp_name'];
            $uploadFile = $diretorioImage . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sql = "UPDATE tb_banner_img SET mobile_banner = :mobile_banner WHERE banner_id = :banner_id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':mobile_banner', $fileName);
                $stmt->bindParam(':banner_id', $id);

                $stmt->execute();
            }
        }

        print_r($_FILES['image']);

        $_SESSION['msgcad'] = "<p class='green'>Banner editado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "banners");
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao atualizar o banner!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "banners");
    }