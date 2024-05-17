<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Pesquisar plano da Loja
    $tabelaShop = "tb_shop";
    $tabelaPlans = "tb_plans_interval";
    $tabelaProducts = "tb_products";

    // Consulta SQL para obter o plano da loja
    $sqlShop = "SELECT s.plan_id, p.id AS plan_id FROM $tabelaShop s
                JOIN $tabelaPlans p ON s.plan_id = p.id
                WHERE s.user_id = :id";
    $stmtShop = $conn_pdo->prepare($sqlShop);
    $stmtShop->bindParam(':id', $dados['shop_id'], PDO::PARAM_INT);
    $stmtShop->execute();
    $shop = $stmtShop->fetch(PDO::FETCH_ASSOC);

    // Conta o número de produtos ativos
    $sqlProducts = "SELECT COUNT(*) AS total_produtos FROM $tabelaProducts
                    WHERE shop_id = :shop_id AND status = :status";
    $stmtProducts = $conn_pdo->prepare($sqlProducts);
    $stmtProducts->bindParam(':shop_id', $dados['shop_id']);
    $stmtProducts->bindValue(':status', 1);
    $stmtProducts->execute();
    $product = $stmtProducts->fetch(PDO::FETCH_ASSOC);

    // Define os limites de produtos com base no plano
    $limitProductsMap = [
        1 => 10,
        2 => 50,
        3 => 250,
        4 => 750,
    ];

    $limitProducts = $limitProductsMap[$shop['plan_id']] ?? "ilimitado";

    // Define o status com base nos limites de produtos
    $status = ($limitProducts < $product['total_produtos']) ? 0 : (isset($_POST['status']) && $_POST['status'] == '1' ? $_POST['status'] : 0);


    if (isset($_POST['emphasis']) && $_POST['emphasis'] == '1') {
        $emphasis = $_POST['emphasis'];
    } else {
        $emphasis = 0;
    }

    if ($_POST['button_type'] == 2)
    {
        $redirect_link = $dados['redirect_link_whatsapp'];
    } else {
        $redirect_link = $dados['redirect_link'];
    }

    // Checkbox sem preco
    if (isset($_POST["without_price"]))
    {
        $price = 0;
        $discount = 0;
        $without_price = 1;
    } else {
        $price = $dados['price'];
        $discount = $dados['discount'];
        $without_price = 0;
    }

    // Define o fuso horário para São Paulo (UTC-3)
    date_default_timezone_set('America/Sao_Paulo');

    // Cria um objeto DateTime com a data e hora atual
    $datetime = new DateTime();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_products';

        // Edita o produto no banco de dados da loja
        $sql = "UPDATE $tabela SET status = :status, emphasis = :emphasis, name = :name, price = :price, without_price = :without_price, discount = :discount, video = :video, description = :description, sku = :sku, button_type = :button_type, redirect_link = :redirect_link, seo_name = :seo_name, link = :link, seo_description = :seo_description, last_modification = :last_modification WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':emphasis', $emphasis);
        $stmt->bindParam(':name', $dados['name']);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':without_price', $without_price);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':video', $dados['video']);
        $stmt->bindParam(':description', $dados['description']);
        $stmt->bindParam(':sku', $dados['sku']);
        $stmt->bindParam(':button_type', $dados['button_type']);
        $stmt->bindParam(':redirect_link', $redirect_link);
        $stmt->bindParam(':seo_name', $dados['seo_name']);
        $stmt->bindParam(':link', $dados['seo_link']);
        $stmt->bindParam(':seo_description', $dados['seo_description']);
        $stmt->bindParam(':last_modification', $datetime);

        // Id que sera editado
        $stmt->bindParam(':id', $dados['id']);

        $stmt->execute();

        // Categorias
        // Recupera o valor do input hidden com o ID das categorias
        $categoriasInputValue = $_POST['categoriasSelecionadas'];

        // Certifique-se de que $categoriasInputValue é uma string
        if (is_array($categoriasInputValue)) {
            // Lógica para converter o array em uma string (se aplicável)
            // Isso pode variar dependendo de como os dados estão sendo enviados
            $categoriasInputValue = implode(',', $categoriasInputValue);
        }

        // Separa os IDs das categorias em um array
        $categoriasIds = explode(',', $categoriasInputValue);

        // Consulta SQL para recuperar as categorias existentes para o produto específico
        $sqlExistingCategories = "SELECT category_id FROM tb_product_categories WHERE shop_id = :shop_id AND product_id = :product_id";
        $stmtExistingCategories = $conn_pdo->prepare($sqlExistingCategories);
        $stmtExistingCategories->bindParam(':shop_id', $dados['shop_id']);
        $stmtExistingCategories->bindParam(':product_id', $dados['id']);
        $stmtExistingCategories->execute();

        // Recupera os IDs das categorias existentes
        $existingCategoryIds = $stmtExistingCategories->fetchAll(PDO::FETCH_COLUMN);

        // Insere as categorias que não estão presentes no banco de dados
        foreach ($categoriasIds as $categoriaId) {
            // Certifique-se de validar e escapar os dados para evitar injeção de SQL
            $categoriaId = (int)$categoriaId;

            if (!in_array($categoriaId, $existingCategoryIds)) {
                // Categoria não está presente no banco de dados, então insira
                $main = ($dados['inputMainCategory'] == $categoriaId) ? 1 : 0;

                $tabela = "tb_product_categories";
                $sqlInsertCategory = "INSERT INTO $tabela (shop_id, product_id, category_id, main) VALUES (:shop_id, :product_id, :category_id, :main)";
                $stmtInsertCategory = $conn_pdo->prepare($sqlInsertCategory);
                $stmtInsertCategory->bindParam(':shop_id', $dados['shop_id']);
                $stmtInsertCategory->bindParam(':product_id', $dados['id']);
                $stmtInsertCategory->bindParam(':category_id', $categoriaId);
                $stmtInsertCategory->bindParam(':main', $main);
                $stmtInsertCategory->execute();
            }
        }

        // Deleta as categorias que não estão mais presentes no input
        foreach ($existingCategoryIds as $existingCategoryId) {
            if (!in_array($existingCategoryId, $categoriasIds)) {
                // Categoria não está presente no input, então delete
                $tabela = "tb_product_categories";
                $sqlDeleteCategory = "DELETE FROM $tabela WHERE shop_id = :shop_id AND product_id = :product_id AND category_id = :category_id";
                $stmtDeleteCategory = $conn_pdo->prepare($sqlDeleteCategory);
                $stmtDeleteCategory->bindParam(':shop_id', $dados['shop_id']);
                $stmtDeleteCategory->bindParam(':product_id', $dados['id']);
                $stmtDeleteCategory->bindParam(':category_id', $existingCategoryId);
                $stmtDeleteCategory->execute();
            }
        }

        echo "sucesso";

        // Deletar imagens
        if (isset($_POST['delete_images'])) {
            $postString = $_POST['delete_images']; // Sua string post com valores separados por vírgula
            $array = explode(", ", $postString); // Divida a string em um array

            // Loop através dos IDs selecionados e exclua as linhas correspondentes
            foreach ($array as $selectedId) {
                // Consulta para obter o diretório da imagem
                $query = "SELECT id, nome_imagem, usuario_id FROM imagens WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $selectedId);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // Obtenha o ID do usuário
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $image_name = $row['nome_imagem'];
                    $product_id = $row['usuario_id'];

                    // Diretório das imagens
                    $diretorio = "./imagens/$product_id/";

                    // Consulta para excluir as imagens do banco de dados
                    $query = "DELETE FROM imagens WHERE id = :id";
                    $stmt = $conn_pdo->prepare($query);
                    $stmt->bindParam(':id', $selectedId);
                    $stmt->execute();

                    // Agora, exclua as imagens no diretório
                    $files = glob($diretorio . $image_name);
                    foreach ($files as $file) {
                        unlink($file);
                    }
                }
            }
        }

        // Recupere o ID do último registro inserido
        $product_id = $dados['id'];

        if (isset($_FILES['imagens'])) {
            // Diretório para salvar as imagens (substitua pelo caminho real)
            $diretorio = "./imagens/$product_id/";

            // Certifique-se de que o diretório de destino exista
            if (!is_dir($diretorio)) {
                mkdir($diretorio, 0755, true);
            }

            // Verifique se o campo de upload de imagens não está vazio
            if (!empty($_FILES['imagens'])) {
                $total = count($_FILES['imagens']['name']);
                
                // Loop através de cada arquivo
                for ($i = 0; $i < $total; $i++) {
                    $fileName = $_FILES['imagens']['name'][$i];
                    $tmp_name = $_FILES['imagens']['tmp_name'][$i];
                    $uploadFile = $diretorio . basename($fileName);

                    if (move_uploaded_file($tmp_name, $uploadFile)) {
                        // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                        $sqlInsertImagem = "INSERT INTO imagens (usuario_id, nome_imagem) VALUES (:usuario_id, :nome_imagem)";
                        $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                        $stmtInsertImagem->bindParam(':usuario_id', $product_id);
                        $stmtInsertImagem->bindParam(':nome_imagem', $fileName);

                        $stmtInsertImagem->execute();
                    }
                }
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Produto editado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "editar-produto?id=" . $product_id);
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro ao atualizar o produto!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "editar-produto?id=" . $product_id);
    }