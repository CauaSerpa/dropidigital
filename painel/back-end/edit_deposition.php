<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_depositions';

        // Edita a pagina no banco de dados da loja
        $sql = "UPDATE $tabela SET name = :name, testimony = :testimony, qualification = :qualification WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindValue(':name', $dados['name']);
        $stmt->bindValue(':testimony', $dados['testimony']);
        $stmt->bindValue(':qualification', $dados['qualification']);

        $stmt->bindValue(':id', $dados['id']);

        $stmt->execute();
        
        // Recupere o ID da loja
        $ultimo_id = $dados['shop_id'];
        
        // Recupere o ID da categoria
        $id = $dados['id'];

        // Consulta para obter o nome das imagens
        $query = "SELECT img FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Obtenha o nome das imagens
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Diretório para salvar as imagens de 'image'
        $diretorioImage = "./depositions/";

        // Certifique-se de que os diretórios de destino existam
        if (!is_dir($diretorioImage)) {
            mkdir($diretorioImage, 0755, true);
        }

        // Verifique se o campo de upload de imagens não está vazio para 'img'
        if ($_FILES['img']['error'] !== 4) {
            // Nome da imagem antiga
            $imagemAntiga = $row['img'];

            // Diretório para deletar as imagens antigas de 'img'
            $caminhoImagemAntiga = $diretorioImage . basename($imagemAntiga);

            // Excluir a imagem existente de 'img'
            if (file_exists($caminhoImagemAntiga)) {
                unlink($caminhoImagemAntiga);
            }

            // Cadastra nova imagem para 'img'
            $fileName = time() . '.jpg';
            $tmp_name = $_FILES['img']['tmp_name'];
            $uploadFile = $diretorioImage . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados de 'img', associando-a ao registro principal
                $sqlInsertImagem = "UPDATE $tabela SET img = :img WHERE id = :id";
                $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                $stmtInsertImagem->bindParam(':img', $fileName);

                // Id que será editado
                $stmtInsertImagem->bindValue(':id', $dados['id']);

                $stmtInsertImagem->execute();
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Depoimento editado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
        exit;
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao editar o depoimento!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "depoimentos");
        exit;
    }