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