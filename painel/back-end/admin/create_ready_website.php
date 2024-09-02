<?php
session_start();
ob_start();
include_once('../../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $name = $_POST['name'];

    // Pega post url
    $url = $_POST['url'];

    // Tabela que será solicitada
    $tabela = 'tb_domains';

    // Verifica se a Url já existe
    $sql = "SELECT id FROM $tabela WHERE subdomain = :subdomain";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':subdomain', $url);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Mensagem de erro
        $_SESSION['msg_url'] = "<p class='danger'>A URL já está sendo utilizada. Escolha outra URL.</p>";

        // Link de redirecionamento
        header('Location: ' . INCLUDE_PATH_DASHBOARD . 'criar-site-pronto');
        exit;
    }

    $status = isset($_POST['status']) ? 1 : 0;
    $emphasis = isset($_POST['emphasis']) ? 1 : 0;
    $plan_id = $_POST['plan'];
    $version = $_POST['version'];
    $support = $_POST['support'];
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
    $cycle = $_POST['cycle'];

    // Checkbox sem preco
    if (isset($_POST['without_price'])) {
        $price = 0;
        $discount = 0;
        $without_price = 1;
    } else {
        $price = $_POST['price'];
        $discount = $_POST['discount'];
        $without_price = 0;
    }

    // Radio select
    if ($_POST['select'] == 'image') {
        $image = $_POST['image'];
        $video = null;
    } else {
        $video = $_POST['video'];
        $image = null;
    }

    // Tabela que será solicitada
    $tabela = 'tb_shop';

    // Insere o usuário no banco de dados
    $sql = "INSERT INTO $tabela (plan_id, name, segment, cpf_cnpj, razao_social, phone) VALUES 
                                (:plan_id, :name, :segment, :cpf_cnpj, :razao_social, :phone)";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':plan_id', $plan_id);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':segment', $segment);
    $stmt->bindValue(':cpf_cnpj', $docNumber);
    $stmt->bindValue(':razao_social', $razaoSocial);
    $stmt->bindValue(':phone', $phone);
    $stmt->execute();

    // Recebendo id da loja
    $shop_id = $conn_pdo->lastInsertId();

    // Tabela que será solicitada
    $tabela = 'tb_ready_sites';

    // Insere o usuário no banco de dados
    $sql = "INSERT INTO $tabela (shop_id, plan_id, status, emphasis, name, version, support, cycle, price, without_price, discount, image, video, description, items_included, sku, seo_name, link, seo_description) VALUES 
                                (:shop_id, :plan_id, :status, :emphasis, :name, :version, :support, :cycle, :price, :without_price, :discount, :image, :video, :description, :items_included, :sku, :seo_name, :link, :seo_description)";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':shop_id', $shop_id);
    $stmt->bindValue(':plan_id', $plan_id);
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':emphasis', $emphasis);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':version', $version);
    $stmt->bindValue(':support', $support);
    $stmt->bindValue(':cycle', $cycle);
    $stmt->bindValue(':price', $price);
    $stmt->bindValue(':without_price', $without_price);
    $stmt->bindValue(':discount', $discount);
    $stmt->bindValue(':image', $image);
    $stmt->bindValue(':video', $video);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':items_included', $items_included);
    $stmt->bindValue(':sku', $sku);
    $stmt->bindValue(':seo_name', $seo_name);
    $stmt->bindValue(':link', $link);
    $stmt->bindValue(':seo_description', $seo_description);
    $stmt->execute();

    // Recebendo id da loja
    $ready_site_id = $conn_pdo->lastInsertId();

    // Tabela que será solicitada
    $tabela = 'tb_domains';

    $domain = "dropidigital.com.br";

    // Obtem a data e hora atual
    date_default_timezone_set('America/Sao_Paulo');
    $current_date = date('Y-m-d H:i:s');

    // Insere o dominio no banco de dados
    $sql = "INSERT INTO $tabela (shop_id, subdomain, domain, register_date) VALUES 
                            (:shop_id, :subdomain, :domain, :register_date)";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':shop_id', $shop_id);
    $stmt->bindValue(':subdomain', $url);
    $stmt->bindValue(':domain', $domain);
    $stmt->bindValue(':register_date', $current_date);
    $stmt->execute();

    // Tabela que será solicitada
    $tabela = 'tb_users';
    $sql = "SELECT id FROM $tabela WHERE permissions IN (:perm1, :perm2)";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':perm1', '1');
    $stmt->bindValue(':perm2', '2');
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        $user_id = $user['id'];

        // Tabela que será solicitada
        $tabela = 'tb_shop_users';

        // Insere o domínio no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, user_id) VALUES (:shop_id, :user_id)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':user_id', $user_id);

        $stmt->execute();
    }

    // Categorias
    // Recupera o valor do input hidden com o ID das categorias
    $categoriasInputValue = $_POST['categoriasSelecionadas'];

    // Separa os IDs das categorias em um array
    $categoriasIds = explode(',', $categoriasInputValue);

    // Loop para inserir categorias no banco de dados
    foreach ($categoriasIds as $categoriaId) {
        // Certifique-se de validar e escapar os dados para evitar injeção de SQL
        $categoriaId = (int)$categoriaId;

        $main = ($_POST['inputMainCategory'] == $categoriaId) ? 1 : 0;

        // Consulta SQL para inserir a associação entre produto e categoria
        $tabela = "tb_ready_site_services";
        $sql = "INSERT INTO $tabela (ready_site_id, service_id, main) VALUES (:ready_site_id, :service_id, :main)";
        $stmt = $conn_pdo->prepare($sql);

        $stmt->bindValue(':ready_site_id', $ready_site_id);
        $stmt->bindValue(':service_id', $categoriaId);
        $stmt->bindValue(':main', $main);

        $stmt->execute();
    }

    // Card Image
    // Diretório para salvar as imagens de 'image'
    $diretorioCardImage = "./ready-website/$ready_site_id/card-image/";

    // Certifique-se de que os diretórios de destino existam
    if (!is_dir($diretorioCardImage)) {
        mkdir($diretorioCardImage, 0755, true);
    }

    // Verifique se o campo de upload de imagens não está vazio para 'image'
    if ($_FILES['card_image']['error'] !== 4) {
        $fileName = time() . '.jpg';
        $uploadFile = $diretorioCardImage . basename($fileName);

        if (move_uploaded_file($_FILES['card_image']['tmp_name'], $uploadFile)) {
            $tabela = "tb_ready_sites";
            $sql = "UPDATE $tabela SET card_image = :card_image WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);

            $stmt->bindValue(':card_image', $fileName);
            $stmt->bindValue(':id', $ready_site_id);

            $stmt->execute();
        }
    }

    // Imagem
    // Radio select
    if ($_POST['select'] == 'image') {
        // Imagem
        // Diretório para salvar as imagens de 'image'
        $diretorioImage = "./ready-website/$ready_site_id/image/";

        // Certifique-se de que os diretórios de destino existam
        if (!is_dir($diretorioImage)) {
            mkdir($diretorioImage, 0755, true);
        }

        // Verifique se o campo de upload de imagens não está vazio para 'image'
        if ($_FILES['image']['error'] !== 4) {
            $fileName = time() . '.jpg';
            $uploadFile = $diretorioImage . basename($fileName);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $tabela = "tb_ready_sites";
                $sql = "UPDATE $tabela SET image = :image WHERE id = :id";
                $stmt = $conn_pdo->prepare($sql);

                $stmt->bindValue(':image', $fileName);
                $stmt->bindValue(':id', $ready_site_id);

                $stmt->execute();
            }
        }
    }

    // // Passa as informações do Site Pronto
    // $_SESSION['user_id'] = $id;
    // $_SESSION['shop_id'] = $shop_id;
    // $_SESSION['admin_id'] = $id;
    // $_SESSION['ready_site'] = 1;

    $_SESSION['msgcad'] = "<p class='green'>Site Pronto criado com sucesso!</p>";
    // Redireciona para a página de login ou exibe uma mensagem de sucesso
    header("Location: " . INCLUDE_PATH_DASHBOARD);
    exit;
}