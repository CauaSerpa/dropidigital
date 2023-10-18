<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_categories';

        // Edita o produto no banco de dados da loja
        $sql = "UPDATE $tabela SET name = :name, link = :link, description = :description, parent_category = :parent_category, status = :status, emphasis = :emphasis, seo_name = :seo_name, seo_link = :seo_link, seo_description = :seo_description WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':name', $dados['name']);
        $stmt->bindValue(':link', $dados['link']);
        $stmt->bindValue(':description', $dados['description']);
        $stmt->bindValue(':parent_category', $dados['parent_category']);
        $stmt->bindValue(':status', $dados['status']);
        $stmt->bindValue(':emphasis', $dados['emphasis']);
        $stmt->bindValue(':seo_name', $dados['seo_name']);
        $stmt->bindValue(':seo_link', $dados['seo_link']);
        $stmt->bindValue(':seo_description', $dados['seo_description']);

        // Id que sera editado
        $stmt->bindValue(':id', $dados['id']);

        $stmt->execute();
        
        // Recupere o ID da loja
        $ultimo_id = $dados['shop_id'];
        
        // Recupere o ID da categoria
        $id = $dados['id'];

        // Consulta para obter o nome das imagens
        $query = "SELECT icon, image FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        // Obtenha o nome das imagens
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Diretório para salvar as imagens de 'image'
        $diretorioImage = "./category/$ultimo_id/image/";

        // Diretório para salvar as imagens de 'icon'
        $diretorioIcon = "./category/$ultimo_id/icon/";

        // Certifique-se de que os diretórios de destino existam
        if (!is_dir($diretorioImage)) {
            mkdir($diretorioImage, 0755, true);
        }

        if (!is_dir($diretorioIcon)) {
            mkdir($diretorioIcon, 0755, true);
        }

        // Verifique se o campo de upload de imagens não está vazio para 'image'
        if ($_FILES['image']['error'] !== 4) {
            // Nome da imagem antiga
            $imagemAntiga = $row['image'];

            // Diretório para deletar as imagens antigas de 'image'
            $caminhoImagemAntiga = $diretorioImage . basename($imagemAntiga);

            // Excluir a imagem existente de 'image'
            if (file_exists($caminhoImagemAntiga)) {
                unlink($caminhoImagemAntiga);
            }

            // Cadastra nova imagem para 'image'
            $fileName = time() . '.jpg';
            $tmp_name = $_FILES['image']['tmp_name'];
            $uploadFile = $diretorioImage . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados de 'image', associando-a ao registro principal
                $sqlInsertImagem = "UPDATE $tabela SET image = :image WHERE id = :id";
                $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                $stmtInsertImagem->bindParam(':image', $fileName);

                // Id que será editado
                $stmtInsertImagem->bindValue(':id', $dados['id']);

                $stmtInsertImagem->execute();
            }
        }

        // Verifique se o campo de upload de imagens não está vazio para 'icon'
        if ($_FILES['icon']['error'] !== 4) {
            // Nome da imagem antiga
            $iconeAntigo = $row['icon'];

            // Diretório para deletar as imagens antigas de 'icon'
            $caminhoIconeAntigo = $diretorioIcon . basename($iconeAntigo);

            // Excluir a imagem existente de 'icon'
            if (file_exists($caminhoIconeAntigo)) {
                unlink($caminhoIconeAntigo);
            }

            // Cadastra nova imagem para 'icon'
            $iconFileName = uniqid() . '.jpg';
            $tmp_name = $_FILES['icon']['tmp_name'];
            $uploadIconFile = $diretorioIcon . basename($iconFileName);

            if (move_uploaded_file($tmp_name, $uploadIconFile)) {
                // Inserir informações da imagem no banco de dados de 'icon', associando-a ao registro principal
                $sqlInsertIcon = "UPDATE $tabela SET icon = :icon WHERE id = :id";
                $stmtInsertIcon = $conn_pdo->prepare($sqlInsertIcon);
                $stmtInsertIcon->bindParam(':icon', $iconFileName);

                // Id que será editado
                $stmtInsertIcon->bindValue(':id', $dados['id']);

                $stmtInsertIcon->execute();
            }
        }

        print_r($_FILES['image']);
        echo "<br>";
        print_r($_FILES['icon']);

        $_SESSION['msgcad'] = "<p class='green'>Categoria editada com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        // header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao atualizar a categoria!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        // header("Location: " . INCLUDE_PATH_DASHBOARD . "categorias");
    }