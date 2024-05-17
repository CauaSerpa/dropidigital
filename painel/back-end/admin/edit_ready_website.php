<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recebe os dados do formulário
        $id = $_POST['id'];
        $shop_id = $_POST['shop_id'];
        $name = $_POST['name'];

        // Criar subdminio do site
        // Pega post url
        $url = $_POST['url'];

        //Tabela que será solicitada
        $tabela = 'tb_domains';
        
        // Verifica se a Url já existe
        $sql = "SELECT id FROM $tabela WHERE subdomain = :subdomain AND shop_id != :shop_id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':subdomain', $url);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Mensagem de erro
            $_SESSION['msg_url'] = "<p class='danger'>A URL já está sendo utilizado. Escolha outra URL.</p>";

            //Link de redirecionamento
            header('Location: ' . INCLUDE_PATH_DASHBOARD . 'editar-site-pronto?id=' . $id);

            //Mata o processo
            die();
        }

        if (isset($_POST['status'])) {
            $status = 1;
        } else {
            $status = 0;
        }
        
        if (isset($_POST['emphasis'])) {
            $emphasis = 0;
        } else {
            $emphasis = 0;
        }

        $plan_id = $_POST['plan'];
        $version = $_POST['version'];
        $support = $_POST['support'];

        // Checkbox sem preco
        if (isset($_POST["without_price"]))
        {
            $price = 0;
            $discount = 0;
            $without_price = 1;
        } else {
            $price = $_POST['price'];
            $discount = $_POST['discount'];
            $without_price = 0;
        }

        $video = $_POST['video'];
        $description = $_POST['description'];
        $items_included = $_POST['itemsIncludedArray'];
        $sku = $_POST['sku'];
        $seo_name = $_POST['seo_name'];
        $link = $_POST['seo_link'];
        $seo_description = $_POST['seo_description'];
        
        $segment = $_POST['segment'];
        $docNumber = $_POST['docNumber'];
        $razaoSocial = $_POST['razaoSocial'];
        $phone = $_POST['phone'];

        //Tabela que será solicitada
        $tabela = 'tb_shop';
        
        // Insere o usuário no banco de dados
        $sql = "UPDATE $tabela SET plan_id = :plan_id, name = :name, segment = :segment,
                                    cpf_cnpj = :cpf_cnpj, razao_social = :razao_social, phone = :phone
                WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':plan_id', $plan_id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':segment', $segment);
        $stmt->bindValue(':cpf_cnpj', $docNumber);
        $stmt->bindValue(':razao_social', $razaoSocial);
        $stmt->bindValue(':phone', $phone);

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_ready_sites';
        
        // Insere o usuário no banco de dados
        $sql = "UPDATE $tabela SET plan_id = :plan_id, status = :status, emphasis = :emphasis, name = :name, version = :version, support = :support, price = :price,
                                    without_price = :without_price, discount = :discount, video = :video, description = :description, items_included = :items_included,
                                    sku = :sku, seo_name = :seo_name, link = :link, seo_description = :seo_description
                WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':plan_id', $plan_id);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':emphasis', $emphasis);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':version', $version);
        $stmt->bindParam(':support', $support);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':without_price', $without_price);
        $stmt->bindParam(':discount', $discount);
        $stmt->bindParam(':video', $video);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':items_included', $items_included);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':seo_name', $seo_name);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':seo_description', $seo_description);

        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $url = $_POST['url'];
        $domain = "dropidigital.com.br";

        //Tabela que será solicitada
        $tabela = 'tb_domains';
    
        // Insere o dominio no banco de dados
        $sql = "UPDATE $tabela SET subdomain = :subdomain WHERE shop_id = :shop_id AND domain = :domain";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':subdomain', $url);

        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':domain', $domain);
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
        $sqlExistingCategories = "SELECT service_id FROM tb_ready_site_services WHERE ready_site_id = :ready_site_id";
        $stmtExistingCategories = $conn_pdo->prepare($sqlExistingCategories);
        $stmtExistingCategories->bindParam(':ready_site_id', $_POST['id']);
        $stmtExistingCategories->execute();

        // Recupera os IDs das categorias existentes
        $existingCategoryIds = $stmtExistingCategories->fetchAll(PDO::FETCH_COLUMN);

        // Insere as categorias que não estão presentes no banco de dados
        foreach ($categoriasIds as $categoriaId) {
            // Certifique-se de validar e escapar os dados para evitar injeção de SQL
            $categoriaId = (int)$categoriaId;

            if (!in_array($categoriaId, $existingCategoryIds)) {
                // Categoria não está presente no banco de dados, então insira
                $main = ($_POST['inputMainCategory'] == $categoriaId) ? 1 : 0;

                $tabela = "tb_ready_site_services";
                $sqlInsertCategory = "INSERT INTO $tabela (ready_site_id, service_id, main) VALUES (:ready_site_id, :service_id, :main)";
                $stmtInsertCategory = $conn_pdo->prepare($sqlInsertCategory);
                $stmtInsertCategory->bindParam(':ready_site_id', $_POST['id']);
                $stmtInsertCategory->bindParam(':service_id', $categoriaId);
                $stmtInsertCategory->bindParam(':main', $main);
                $stmtInsertCategory->execute();
            }
        }

        // Deleta as categorias que não estão mais presentes no input
        foreach ($existingCategoryIds as $existingCategoryId) {
            if (!in_array($existingCategoryId, $categoriasIds)) {
                // Categoria não está presente no input, então delete
                $tabela = "tb_ready_site_services";
                $sqlDeleteCategory = "DELETE FROM $tabela WHERE ready_site_id = :ready_site_id AND service_id = :service_id";
                $stmtDeleteCategory = $conn_pdo->prepare($sqlDeleteCategory);
                $stmtDeleteCategory->bindParam(':ready_site_id', $_POST['id']);
                $stmtDeleteCategory->bindParam(':service_id', $existingCategoryId);
                $stmtDeleteCategory->execute();
            }
        }

        // Imagens
        // Deletar imagens
        if (isset($_POST['delete_images'])) {
            $postString = $_POST['delete_images']; // Sua string post com valores separados por vírgula
            $array = explode(", ", $postString); // Divida a string em um array

            // Loop através dos IDs selecionados e exclua as linhas correspondentes
            foreach ($array as $selectedId) {
                // Consulta para obter o diretório da imagem
                $query = "SELECT id, image, ready_site_id FROM tb_ready_site_img WHERE id = :id";
                $stmt = $conn_pdo->prepare($query);
                $stmt->bindParam(':id', $selectedId);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    // Obtenha o ID do usuário
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    $image = $row['image'];
                    $ready_site_id = $row['ready_site_id'];

                    // Diretório das imagens
                    $diretorio = "./ready-website/$ready_site_id/";

                    // Consulta para excluir as imagens do banco de dados
                    $query = "DELETE FROM tb_ready_site_img WHERE id = :id";
                    $stmt = $conn_pdo->prepare($query);
                    $stmt->bindParam(':id', $selectedId);
                    $stmt->execute();

                    // Agora, exclua as imagens no diretório
                    $files = glob($diretorio . $image);
                    foreach ($files as $file) {
                        unlink($file);
                    }
                }
            }
        }

        // Recupere o ID do último registro inserido
        $ready_site_id = $_POST['id'];

        if (isset($_FILES['imagens'])) {
            // Diretório para salvar as imagens (substitua pelo caminho real)
            $diretorio = "./ready-website/$ready_site_id/";

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
                        $sqlInsertImagem = "INSERT INTO tb_ready_site_img (ready_site_id, image) VALUES (:ready_site_id, :image)";
                        $stmtInsertImagem = $conn_pdo->prepare($sqlInsertImagem);
                        $stmtInsertImagem->bindParam(':ready_site_id', $ready_site_id);
                        $stmtInsertImagem->bindParam(':image', $fileName);

                        $stmtInsertImagem->execute();
                    }
                }
            }
        }

        $_SESSION['msgcad'] = "<p class='green'>Site Pronto criado com sucesso!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "sites-prontos");
        exit;
    }
?>