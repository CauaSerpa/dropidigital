<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    // Nome da tabela para a busca
    $tabela = 'tb_subscriptions';

    // Consulta SQL para contar os produtos na tabela
    $sql = "SELECT plan_id FROM $tabela WHERE (status = :status OR status = :status1) AND shop_id = :shop_id ORDER BY id DESC LIMIT 1";
    $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
    $stmt->bindValue(':status', 'ACTIVE');
    $stmt->bindValue(':status1', 'RECEIVED');
    $stmt->bindParam(':shop_id', $dados['shop_id']);
    $stmt->execute();

    // Recupere o resultado da consulta
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    $plan_id = (isset($plan['plan_id'])) ? $plan['plan_id'] : 1;

    // Pesquisar plano da Loja
    $tabela = "tb_plans_interval";

    // Consulta SQL para obter o plano da loja
    $sql = "SELECT plan_id FROM $tabela WHERE id = :id";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':id', $plan_id, PDO::PARAM_INT);
    $stmt->execute();
    $shop = $stmt->fetch(PDO::FETCH_ASSOC);

    // Pesquisar produtos
    $tabela = "tb_products";

    // Conta o número de produtos ativos
    $sql = "SELECT COUNT(*) AS total_produtos FROM $tabela
                    WHERE shop_id = :shop_id AND status = :status";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $dados['shop_id']);
    $stmt->bindValue(':status', 1);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Define os limites de produtos com base no plano
    $limitProductsMap = [
        1 => 10,
        2 => 50,
        3 => 250,
        4 => 900,
        5 => 5000,
    ];

    $limitProducts = $limitProductsMap[$shop['plan_id']] ?? "ilimitado";

    // Define o status com base nos limites de produtos
    $status = ($limitProducts <= $product['total_produtos']) ? 0 : (isset($_POST['status']) && $_POST['status'] == '1' ? $_POST['status'] : 0);

    if (isset($_POST['emphasis']) && $_POST['emphasis'] == '1') {
        $emphasis = $_POST['emphasis'];
    } else {
        $emphasis = 0;
    }

    if ($_POST['button_type'] == 2) {
        $redirect_link = $dados['redirect_link_whatsapp_standard'];
    } else if ($_POST['button_type'] == 3) {
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

    // Acessa o IF quando o usuário clicar no botão
    if (empty($dados['SendAddProduct'])) {
        $sql = "INSERT INTO tb_products (shop_id, status, emphasis, name, price, without_price, discount, video, description, sku, button_type, redirect_link, seo_name, link, seo_description) VALUES 
                                    (:shop_id, :status, :emphasis, :name, :price, :without_price, :discount, :video, :description, :sku, :button_type, :redirect_link, :seo_name, :link, :seo_description)";
        $stmt = $conn_pdo->prepare($sql);

        // Substituir os links pelos valores do formulário
        $stmt->bindParam(':shop_id', $dados['shop_id']);
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

        $stmt->execute();

        // Recupere o ID do último registro inserido
        $ultimo_id = $conn_pdo->lastInsertId();

        // Categorias
        // Recupera o valor do input hidden com o ID das categorias
        $categoriasInputValue = $_POST['categoriasSelecionadas']; // Substitua 'categoriasInput' pelo nome do seu input

        // Certifique-se de que $categoriasInputValue é uma string
        if (is_array($categoriasInputValue)) {
            // Lógica para converter o array em uma string (se aplicável)
            // Isso pode variar dependendo de como os dados estão sendo enviados
            $categoriasInputValue = implode(',', $categoriasInputValue);
        }

        // Separa os IDs das categorias em um array
        $categoriasIds = explode(',', $categoriasInputValue);

        // Loop para inserir categorias no banco de dados
        foreach ($categoriasIds as $categoriaId) {
            // Certifique-se de validar e escapar os dados para evitar injeção de SQL
            $categoriaId = (int)$categoriaId;

            $main = ($dados['inputMainCategory'] == $categoriaId) ? 1 : 0;

            // Consulta SQL para inserir a associação entre produto e categoria
            $tabela = "tb_product_categories";
            $sql = "INSERT INTO $tabela (shop_id, product_id, category_id, main) VALUES (:shop_id, :product_id, :category_id, :main)";
            $stmt = $conn_pdo->prepare($sql);

            $stmt->bindParam(':shop_id', $dados['shop_id']);
            $stmt->bindParam(':product_id', $ultimo_id);
            $stmt->bindParam(':category_id', $categoriaId);
            $stmt->bindParam(':main', $main);

            $stmt->execute();

            echo "sucesso";
        }

        // Imagens
        $total = count($_FILES['imagens']['name']);

        // Loop através de cada arquivo
        for ($i = 0; $i < $total; $i++) {
            // Certifique-se de que a pasta para as imagens exista
            $uploadDir = "imagens/$ultimo_id/";
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = $_FILES['imagens']['name'][$i];
            $tmp_name = $_FILES['imagens']['tmp_name'][$i];
            $uploadFile = $uploadDir . basename($fileName);

            if (move_uploaded_file($tmp_name, $uploadFile)) {
                // Inserir informações da imagem no banco de dados, associando-a ao registro principal
                $sqlInsertImagem = "INSERT INTO imagens (usuario_id, nome_imagem) VALUES (:usuario_id, :nome_imagem)";
                $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                $stmtInsertImagem->bindParam(':usuario_id', $ultimo_id);
                $stmtInsertImagem->bindParam(':nome_imagem', $fileName);

                if ($stmtInsertImagem->execute()) {
                    $_SESSION['msgcad'] = "<p class='green'>Usuário cadastrado com sucesso!</p>";
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar a imagem!</p>";
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
                }
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar a imagem!</p>";
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "produtos");
            }
        }
    }