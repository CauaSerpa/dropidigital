<?php
function copyReadySiteToShop($dataForm) {
    include('../asaas/config.php');

    function copiarPastaImg($src, $dst) {
        // Verifica se o diretório de origem existe
        if (!file_exists($src) || !is_dir($src)) {
            error_log("Erro: O diretório de origem $src não existe ou não é um diretório.");
            return;
        }
    
        // Cria o diretório de destino se ele não existir
        if (!file_exists($dst)) {
            mkdir($dst, 0777, true);
        }
    
        // Abre o diretório e lê seu conteúdo
        $dir = opendir($src);
        if ($dir === false) {
            error_log("Erro: Não foi possível abrir o diretório $src.");
            return;
        }
    
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcFile = $src . '/' . $file;
                $dstFile = $dst . '/' . $file;
                // Se for um diretório, recursivamente copia seu conteúdo
                if (is_dir($srcFile)) {
                    copiarPastaImg($srcFile, $dstFile);
                } else {
                    // Se for um arquivo, copia-o para o novo destino
                    if (!copy($srcFile, $dstFile)) {
                        error_log("Erro: Não foi possível copiar o arquivo $srcFile para $dstFile.");
                    }
                }
            }
        }
    
        closedir($dir);
    }

    // Site que sera a origem da copia
    $copied_shop_id = $dataForm['copied_shop_id'];

    // Site que recebera a copia
    $pasted_site_id = $dataForm['pasted_site_id'];

    // Array associativo para mapear os produtos e categorias
    $product_map = [];
    $category_map = [];

    $tabela = 'tb_shop';
    $query = "SELECT * FROM $tabela WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $pasted_site_id);
    $stmt->execute();

    $site = $stmt->fetch(PDO::FETCH_ASSOC);

    $tabela = 'tb_shop';
    $query = "SELECT * FROM $tabela WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $copied_shop_id);
    $stmt->execute();

    $ready_site = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {
        $tabela = 'tb_shop';
        $sql = "UPDATE $tabela SET
                    title = :title,
                    description = :description,
                    logo = :logo,
                    logo_mobile = :logo_mobile,
                    favicon = :favicon,
                    segment = :segment,
                    top_highlight_bar = :top_highlight_bar,
                    top_highlight_bar_location = :top_highlight_bar_location,
                    top_highlight_bar_text = :top_highlight_bar_text,
                    center_highlight_images = :center_highlight_images
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':title', $ready_site['title']);
        $stmt->bindValue(':description', $ready_site['description']);
        $stmt->bindValue(':logo', $ready_site['logo']);
        $stmt->bindValue(':logo_mobile', $ready_site['logo_mobile']);
        $stmt->bindValue(':favicon', $ready_site['favicon']);
        $stmt->bindValue(':segment', $ready_site['segment']);
        $stmt->bindValue(':top_highlight_bar', $ready_site['top_highlight_bar']);
        $stmt->bindValue(':top_highlight_bar_location', $ready_site['top_highlight_bar_location']);
        $stmt->bindValue(':top_highlight_bar_text', $ready_site['top_highlight_bar_text']);
        $stmt->bindValue(':center_highlight_images', $ready_site['center_highlight_images']);

        $stmt->bindValue(':id', $pasted_site_id);

        if ($stmt->execute()) {
            // Copiar Logo
            // Definindo os caminhos de origem e destino
            $origem = "../logos/$copied_shop_id";
            $destino = "../logos/$pasted_site_id";

            // Chamando a função para copiar a pasta
            copiarPastaImg($origem, $destino);

            // Nome da tabela para a busca
            $tabela = 'tb_products';
            $query = "SELECT * FROM $tabela WHERE shop_id = :shop_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':shop_id', $copied_shop_id);
            $stmt->execute();

            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($products as $product) {
                // Nome da tabela para a busca
                $tabela = 'tb_products';
                
                // Consulta SQL para contar os produtos na tabela
                $sql = "SELECT COUNT(*) AS total_produtos FROM $tabela WHERE shop_id = :shop_id AND status = :status";
                $stmt = $conn->prepare($sql);  // Use prepare para consultas preparadas
                $stmt->execute([':shop_id' => $pasted_site_id, ':status' => 1]);
                
                // Recupere o resultado da consulta
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // O resultado contém o total de produtos na chave 'total_produtos'
                $totalProdutos = $resultado['total_produtos'];
                
                // Definir o limite de produtos baseado no plano
                $planLimits = [
                    1 => 10,
                    2 => 50,
                    3 => 250,
                    4 => 900,
                    5 => 5000,
                ];
                
                $limitProducts = $planLimits[$site['plan_id']] ?? 10;
                
                // Definir o status baseado no total de produtos
                $status = ($limitProducts <= $totalProdutos) ? 0 : 1;

                // Verificar duplicidade de link e adicionar valor adicional se necessário
                $link = $product['link'];
                $originalLink = $link;
                $counter = 1;

                $sql = "SELECT COUNT(*) AS count FROM $tabela WHERE link = :link AND shop_id = :shop_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':link', $link);
                $stmt->bindParam(':shop_id', $pasted_site_id);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                while ($result['count'] > 0) {
                    $link = $originalLink . '-' . $counter;
                    $stmt->bindParam(':link', $link);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $counter++;
                }

                $query = "INSERT INTO $tabela
                            (shop_id, status, emphasis, name, price, without_price, discount, video, description, sku, checkout, button_type, redirect_link, seo_name, link, seo_description)
                        VALUES
                            (:shop_id, :status, :emphasis, :name, :price, :without_price, :discount, :video, :description, :sku, :checkout, :button_type, :redirect_link, :seo_name, :link, :seo_description)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':shop_id', $pasted_site_id);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':emphasis', $product['emphasis']);
                $stmt->bindParam(':name', $product['name']);
                $stmt->bindParam(':price', $product['price']);
                $stmt->bindParam(':without_price', $product['without_price']);
                $stmt->bindParam(':discount', $product['discount']);
                $stmt->bindParam(':video', $product['video']);
                $stmt->bindParam(':description', $product['description']);
                $stmt->bindParam(':sku', $product['sku']);
                $stmt->bindParam(':checkout', $product['checkout']);
                $stmt->bindParam(':button_type', $product['button_type']);
                $stmt->bindParam(':redirect_link', $product['redirect_link']);
                $stmt->bindParam(':seo_name', $product['seo_name']);
                $stmt->bindParam(':link', $link);
                $stmt->bindParam(':seo_description', $product['seo_description']);
                $stmt->execute();

                $product_id = $conn->lastInsertId();
                $product_map[$product['id']] = $product_id; // Mapeia o ID original para o novo ID

                $tabela = 'imagens';
                $query = "SELECT * FROM $tabela WHERE usuario_id = :usuario_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':usuario_id', $product['id']);
                $stmt->execute();

                $product_imgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($product_imgs as $product_img) {
                    // Copiar Imagem do produto
                    // Definindo os caminhos de origem e destino
                    $origem = "../imagens/" . $product['id'];
                    $destino = "../imagens/$product_id";

                    $tabela = 'imagens';
                    $query = "INSERT INTO $tabela
                                (usuario_id, nome_imagem)
                            VALUES
                                (:usuario_id, :nome_imagem)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':usuario_id', $product_id);
                    $stmt->bindParam(':nome_imagem', $product_img['nome_imagem']);
                    $stmt->execute();
        
                    // Chamando a função para copiar a pasta
                    copiarPastaImg($origem, $destino);
                }
            }

            $tabela = 'tb_banner_info';
            $query = "SELECT * FROM $tabela WHERE shop_id = :shop_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':shop_id', $copied_shop_id);
            $stmt->execute();

            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($banners as $banner) {
                $tabela = 'tb_banner_info';
                $query = "INSERT INTO $tabela
                            (shop_id, name, location, category, link, target, title, status)
                        VALUES
                            (:shop_id, :name, :location, :category, :link, :target, :title, :status)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':shop_id', $pasted_site_id);
                $stmt->bindParam(':name', $banner['name']);
                $stmt->bindParam(':location', $banner['location']);
                $stmt->bindParam(':category', $banner['category']);
                $stmt->bindParam(':link', $banner['link']);
                $stmt->bindParam(':target', $banner['target']);
                $stmt->bindParam(':title', $banner['title']);
                $stmt->bindParam(':status', $banner['status']);
                $stmt->execute();

                $banner_id = $conn->lastInsertId();

                // Copiar Imagem do produto
                // Definindo os caminhos de origem e destino
                $origem = "../banners/" . $banner['id'];
                $destino = "../banners/$banner_id";

                $tabela = 'tb_banner_img';
                $query = "SELECT * FROM $tabela WHERE banner_id = :banner_id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':banner_id', $banner['id']);
                $stmt->execute();

                $banner_imgs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($banner_imgs as $banner_img) {
                    $tabela = 'tb_banner_img';
                    $query = "INSERT INTO $tabela
                                (banner_id, image_name, mobile_banner)
                            VALUES
                                (:banner_id, :image_name, :mobile_banner)";
                    $stmt = $conn->prepare($query);
                    $stmt->bindParam(':banner_id', $banner_id);
                    $stmt->bindParam(':image_name', $banner_img['image_name']);
                    $stmt->bindParam(':mobile_banner', $banner_img['mobile_banner']);
                    $stmt->execute();

                    // Chamando a função para copiar a pasta
                    copiarPastaImg($origem, $destino);
                }
            }

            $tabela = 'tb_categories';
            $query = "SELECT * FROM $tabela WHERE shop_id = :shop_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':shop_id', $copied_shop_id);
            $stmt->execute();

            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($categories as $category) {
                $tabela = 'tb_categories';
                $query = "INSERT INTO $tabela
                            (shop_id, name, icon, image, description, link, parent_category, status, emphasis, seo_name, seo_link, seo_description)
                        VALUES
                            (:shop_id, :name, :icon, :image, :description, :link, :parent_category, :status, :emphasis, :seo_name, :seo_link, :seo_description)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':shop_id', $pasted_site_id);
                $stmt->bindParam(':name', $category['name']);
                $stmt->bindParam(':icon', $category['icon']);
                $stmt->bindParam(':image', $category['image']);
                $stmt->bindParam(':description', $category['description']);
                $stmt->bindParam(':link', $category['link']);
                $stmt->bindParam(':parent_category', $category['parent_category']);
                $stmt->bindParam(':status', $category['status']);
                $stmt->bindParam(':emphasis', $category['emphasis']);
                $stmt->bindParam(':seo_name', $category['seo_name']);
                $stmt->bindParam(':seo_link', $category['seo_link']);
                $stmt->bindParam(':seo_description', $category['seo_description']);
                $stmt->execute();

                $category_id = $conn->lastInsertId();
                $category_map[$category['id']] = $category_id; // Mapeia o ID original para o novo ID
            }

            // Copiar Imagem da categoria
            // Definindo os caminhos de origem e destino
            $origem = "../category/$copied_shop_id/image/";
            $destino = "../category/$pasted_site_id/image/";

            // Chamando a função para copiar a pasta
            copiarPastaImg($origem, $destino);

            // Definindo os caminhos de origem e destino
            $origem = "../category/$copied_shop_id/icon/";
            $destino = "../category/$pasted_site_id/icon/";

            // Chamando a função para copiar a pasta
            copiarPastaImg($origem, $destino);
            
            $tabela = 'tb_product_categories';
            $query = "SELECT * FROM $tabela WHERE shop_id = :shop_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':shop_id', $ready_site_id);
            $stmt->execute();

            $productsCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($productsCategories as $productCategory) {
                $product_id = $product_map[$productCategory['product_id']];
                $category_id = $category_map[$productCategory['category_id']];

                $tabela = 'tb_product_categories';
                $query = "INSERT INTO $tabela
                            (shop_id, product_id, category_id, main)
                        VALUES
                            (:shop_id, :product_id, :category_id, :main)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':shop_id', $site_id);
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':category_id', $category_id);
                $stmt->bindParam(':main', $productCategory['main']);
                $stmt->execute();
            }
        } else {
            echo "Não foi possivel copiar o site!";
        }
    } else {
        echo "Site Pronto não encontrado!";
    }
}