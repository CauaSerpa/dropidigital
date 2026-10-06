<?php
    // Pega o dominio da pagina
    $dominioCompleto = $_SERVER['HTTP_HOST'];
    
    function temSubdominio($dominio) {
        // Divide o domínio em partes usando o ponto como delimitador
        $partes = explode('.', $dominio);
    
        // Verifica se há mais de uma parte (subdomínios)
        return count($partes) > 2;
    }
    
    function separarDominioSubdominio($url) {
        // Divide a URL em partes usando o ponto como delimitador
        $partes = explode('.', $url);
    
        // Verifica se há mais de uma parte (subdomínios presentes)
        if (count($partes) > 2) {
            // Subdomínio é a primeira parte
            $subdominio = $partes[0];
    
            // Domínio é a parte a partir da segunda até a última
            $dominio = implode('.', array_slice($partes, 1));
        } else {
            // Não há subdomínios, o subdomínio é vazio
            $subdominio = 'www';
            
            // Domínio é a URL completa
            $dominio = $url;
        }
    
        return [
            'subdominio' => $subdominio,
            'dominio' => $dominio,
        ];
    }

    // Verifica se a URL atual contém /en/ ou /de/ ou /pt/
    $requestUri = $_SERVER['REQUEST_URI'];
    $languageSegment = '';
    if (strpos($requestUri, '/en/') !== false) {
        $languageSegment = 'en/';
        $urlLang = 'en';
    } elseif (strpos($requestUri, '/de/') !== false) {
        $languageSegment = 'de/';
        $urlLang = 'de';
    } elseif (strpos($requestUri, '/es/') !== false) {
        $languageSegment = 'es/';
        $urlLang = 'es';
    } elseif (strpos($requestUri, '/pt/') !== false) {
        $languageSegment = 'pt/';
        $urlLang = 'pt';
    }
    
    if (temSubdominio($dominioCompleto)) {
        $resultadoComSubdominio = separarDominioSubdominio($dominioCompleto);
        $subdomain = $resultadoComSubdominio['subdominio'];
        $domain = $resultadoComSubdominio['dominio'];

        // Combina todas as partes para obter a URL completa
        $urlCompleta = "https://$subdomain.$domain/" . $languageSegment;
        $urlLimpa = "https://$subdomain.$domain/";
    } else {
        $resultadoSemSubdominio = separarDominioSubdominio($dominioCompleto);
        $subdomain = $resultadoSemSubdominio['subdominio'];
        $domain = $resultadoSemSubdominio['dominio'];

        // Combina todas as partes para obter a URL completa
        $urlCompleta = "https://$domain/" . $languageSegment;
        $urlLimpa = "https://$domain/";
    }

    // echo $subdomain;
    // echo $domain;

    define('INCLUDE_PATH_LOJA', $urlCompleta);

    $isSitemap = preg_match('/(sitemap.*\.xml|robots\.txt)$/', $_SERVER['REQUEST_URI']);
    if (!$isSitemap) {
        session_start();
    }

    include('../config.php');


    use GeoIp2\Database\Reader;

    function getCountryCode() {
        static $country = null;
        if ($country !== null) return $country;

        try {
            $reader = new Reader(__DIR__ . '/geoip/GeoLite2-Country.mmdb');
            $ip = $_SERVER['REMOTE_ADDR'];
            $record = $reader->country($ip);
            $country = $record->country->isoCode;
        } catch (Exception $e) {
            $country = 'US';
        }

        return $country;
    }

    function detectLanguage($country) {
        global $urlLang;
        if (!empty($urlLang)) return $urlLang;

        $spanish = ['AR','CL','CO','MX','PE','VE','UY','PY','BO','EC','GT','HN','SV','NI','CR','PA','DO','CU'];
        $english = ['US','GB','CA','AU','NZ','IE'];

        if ($country === 'BR') return 'pt';
        if ($country === 'DE') return 'de';
        if (in_array($country, $spanish)) return 'es';
        if (in_array($country, $english)) return 'en';

        return 'pt'; // fallback SEO-safe
    }

    $country = getCountryCode();
    $currentLang = detectLanguage($country);
    $suggestedLang = detectLanguage($country);

    require __DIR__ . '/includes/lang.php';


    // Sitemap
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    
    $requestUri = $_SERVER['REQUEST_URI'];

    // ================================
    // SITEMAPS
    // ================================
    // if ($requestUri === '/sitemap.xml') {
    //     include './sitemap/sitemap.php';
    //     exit;
    // }
    
    if ($requestUri === '/sitemap_index.xml') {
        include './sitemap/sitemap_index.php';
        exit;
    }
    
    if ($requestUri === '/sitemap_pages.xml') {
        include './sitemap/sitemap_pages.php';
        exit;
    }
    
    if ($requestUri === '/sitemap_categories.xml') {
        include './sitemap/sitemap_categories.php';
        exit;
    }
    
    if ($requestUri === '/sitemap_blog.xml') {
        include './sitemap/sitemap_blog.php';
        exit;
    }

    // // sitemap_products.xml (página 1)
    // if ($requestUri === '/sitemap_products.xml') {
    //     $_GET['page'] = 1;
    //     include './sitemap/sitemap_products.php';
    //     exit;
    // }

    // sitemap_products_2.xml, sitemap_products_3.xml ...
    if (preg_match('/^\/sitemap_products_(\d+)\.xml$/', $requestUri, $matches)) {
        $_GET['page'] = max(1, (int) $matches[1]);
        include './sitemap/sitemap_products.php';
        exit;
    }

    // ================================
    // ROBOTS.TXT
    // ================================
    if ($requestUri === '/robots.txt') {
        include('./robots.txt.php');
        exit;
    }

    // ================================
    // ADS.TXT
    // ================================
    if ($requestUri === '/ads.txt') {
        include('./ads.txt');
        exit;
    }

    // Pesquisar dominio
    // Tabela que sera feita a consulta
    $tabela = "tb_domains";

    // Consulta SQL
    $sql = "SELECT shop_id FROM $tabela WHERE subdomain = :subdomain AND domain = :domain";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':subdomain', $subdomain, PDO::PARAM_STR);
    $stmt->bindParam(':domain', $domain, PDO::PARAM_STR);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $shop_id = $resultado['shop_id'];
    }

    // Pesquisar Loja
    // Tabela que sera feita a consulta
    $tabela = "tb_shop";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $shop_id, PDO::PARAM_STR);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $shop_id = $resultado['id'];
        $user_id = $resultado['user_id'];
        $loja = $resultado['name'];
        $title = $resultado['title'];
        $description = $resultado['description'];
        $logo = $resultado['logo'];
        $logo_mobile = $resultado['logo_mobile'];
        $favicon = $resultado['favicon'];
        $video = $resultado['video'];
        $facebook = $resultado['facebook'];
        $x = $resultado['x'];
        $pinterest = $resultado['pinterest'];
        $instagram = $resultado['instagram'];
        $youtube = $resultado['youtube'];
        $tiktok = $resultado['tiktok'];
        $token_instagram = $resultado['token_instagram'];

        $cpf_cnpj = $resultado['cpf_cnpj'];
        $razao_social = $resultado['razao_social'];

        $phone = $resultado['phone'];

        if (isset($resultado['whatsapp'])) {
            $whatsapp = $resultado['whatsapp'];
            // Fomatando celular
            $formatted_whatsapp = preg_replace('/\D/', '', $resultado['whatsapp']);
        } else {
            $whatsapp = "+55 " . $resultado['phone'];
            // Fomatando celular
            $formatted_whatsapp = preg_replace('/\D/', '', $resultado['phone']);
        }

        $email = $resultado['email'];

        $whatsapp_group = $resultado['whatsapp_group'];
        $top_highlight_bar = $resultado['top_highlight_bar'];
        $top_highlight_bar_location = $resultado['top_highlight_bar_location'];
        $top_highlight_bar_text = $resultado['top_highlight_bar_text'];
        $center_highlight_images = $resultado['center_highlight_images'];
    } else {
        // Página de error 404
        include_once('pages/404.php');
        die;
    }

    // Tabela que sera feita a consulta
    $tabela = "tb_users";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $user_id = $resultado['id'];
        $name = $resultado['name'];
        $email = $resultado['email'];
    }

    //Pesquisa endereco
    // Tabela que sera feita a consulta
    $tabela = "tb_address";

    // Consulta SQL
    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";

    // Preparar a consulta
    $stmt = $conn_pdo->prepare($sql);

    // Vincular o valor do parâmetro
    $stmt->bindParam(':shop_id', $shop_id, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado como um array associativo
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se o resultado foi encontrado
    if ($resultado) {
        // Atribuir o valor da coluna "name" à variável $name
        $cep = $resultado['cep'];
        $endereco = $resultado['endereco'];
        $numero = $resultado['numero'];
        $complemento = $resultado['complemento'];
        $bairro = $resultado['bairro'];
        $cidade = $resultado['cidade'];
        $estado = $resultado['estado'];
    }


    // Idiomas da loja
    $languageCookie = 'shop_languages_' . (int) $shop_id;
    $languages = null;

    if (!empty($_COOKIE[$languageCookie])) {
        $decodedLanguages = json_decode($_COOKIE[$languageCookie], true);

        if (is_array($decodedLanguages)) {
            $languages = array_values(
                array_intersect(
                    ['pt', 'en', 'es', 'de'],
                    $decodedLanguages
                )
            );
        }
    }

    if ($languages === null) {

        // $sql = "
        //     SELECT DISTINCT language
        //     FROM tb_products
        //     WHERE shop_id = :shop_id
        //     AND status = 1
        //     AND language IS NOT NULL
        // ";

        // $stmt = $conn_pdo->prepare($sql);
        // $stmt->execute([
        //     ':shop_id' => (int) $shop_id
        // ]);

        // $languages = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // $languages = array_values(
        //     array_intersect(
        //         ['pt', 'en', 'es', 'de'],
        //         $languages
        //     )
        // );

        $languages = ['pt', 'en', 'es', 'de'];

        setcookie(
            $languageCookie,
            json_encode($languages),
            [
                'expires'  => time() + 86400,
                'path'     => '/',
                'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => false,
                'samesite' => 'Lax'
            ]
        );
    }

    $hreflangMap = [
        'pt' => 'pt-br',
        'en' => 'en',
        'es' => 'es',
        'de' => 'de'
    ];

    $htmlLangMap = [
        'pt' => 'pt-BR',
        'en' => 'en',
        'es' => 'es',
        'de' => 'de',
    ];

    // Modal idioma
    $uriParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $supportedLangs = ['pt','en','es','de'];

    $urlLang = in_array($uriParts[0] ?? '', $supportedLangs)
        ? $uriParts[0]
        : 'pt';

    $showLangModal = false;

    if (
        empty($_COOKIE['lang_choice']) &&
        $urlLang === 'pt' &&                    // só sugere se estiver no padrão
        $suggestedLang !== 'pt' &&              // não sugere PT
        in_array($suggestedLang, $languages)    // idioma existe na loja
    ) {
        $showLangModal = true;
    }
?>
<?php
    // Função para adicionar um registro na tabela tb_visits
    function addVisitToDatabase($conn_pdo, $shop_id) {
        // Fuso horario Sao Paulo
        date_default_timezone_set('America/Sao_Paulo');
        // Data atual
        $dataAtual = date("Y-m-d");

        // Consulta para verificar se já há uma entrada para a data atual
        $sql = "SELECT * FROM tb_visits WHERE shop_id = :shop_id AND page = :page AND data = :data";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindValue(':page', 'shop');
        $stmt->bindParam(':data', $dataAtual);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // Se não houver entrada para a data atual, insira uma nova entrada
            $sql = "INSERT INTO tb_visits (shop_id, page, data, contagem) VALUES (:shop_id, :page, :data, 1)";
        } else {
            // Se houver entrada para a data atual, apenas atualize a contagem
            $sql = "UPDATE tb_visits SET contagem = contagem + 1 WHERE shop_id = :shop_id AND page = :page AND data = :data";
        }
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindValue(':page', 'shop');
        $stmt->bindParam(':data', $dataAtual);
        $stmt->execute();
    }

    // Fuso horario Sao Paulo
    date_default_timezone_set('America/Sao_Paulo');

    // Verifica se a sessão "access_page" existe
    if (!isset($_SESSION['access_page'])) {
        // Se não existir, cria a sessão e adiciona um registro na tabela tb_visits
        $_SESSION['access_page'] = ["date" => date('Y-m-d H:i')]; // Salva a data, hora e minuto atuais na sessão

        addVisitToDatabase($conn_pdo, $shop_id);
    } else {
        // Session access
        $access = $_SESSION['access_page'];

        // Se a sessão existir, verifica se já passaram 10 minutos desde o último acesso
        // Data fornecida
        $date = $access['date'];
        $currentDate = date('Y-m-d H:i');

        // Converte as datas para objetos DateTime
        $dateObj = new DateTime($date);
        $currentDateObj = new DateTime($currentDate);

        // Calcula a diferença entre as duas datas
        $interval = $currentDateObj->diff($dateObj);

        // Verifica se a diferença é de pelo menos 10 minutos
        if ($interval->i >= 10 || $interval->h > 0 || $interval->d > 0 || $interval->m > 0 || $interval->y > 0) {
            // Se já passaram 10 minutos, adiciona mais um registro na tabela tb_visits

            $access['date'] = date('Y-m-d H:i'); // Atualiza a sessão com a nova data, hora e minuto
            $_SESSION['access_page'] = $access;

            addVisitToDatabase($conn_pdo, $shop_id);
        }
    }
?>
<?php
    $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

    $supportedLangs = ['pt','en','es','de'];

    $lang = in_array($uri[0], $supportedLangs) ? $uri[0] : 'pt';
?>
<?php
    $uri = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

    $supportedLangs = ['pt', 'en', 'es', 'de'];

    $language = 'pt';
    $link = '';

    if (isset($uri[0]) && in_array($uri[0], $supportedLangs, true)) {
        $language = $uri[0];
        $link = $uri[1] ?? '';
    } else {
        $link = $uri[0] ?? '';
    }

    $isProductPage = false;
    $product = null;

    if ($link !== '') {

        $sql = "
            SELECT
                id,
                link,
                seo_name,
                seo_description,
                language
            FROM tb_products
            WHERE shop_id = :shop_id
            AND status = 1
            AND link = :link
            LIMIT 1
        ";

        $stmt = $conn_pdo->prepare($sql);

        $stmt->execute([
            ':shop_id' => (int) $shop_id,
            ':link'    => $link
        ]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $isProductPage = true;

            $productSlug = $product['link'];
            $productLanguage = $product['language'];

            $title = $product['seo_name'] ?? $title;
            $description = $product['seo_description'] ?? $description;
        }
    }
?>
<?php
    function limit_text($text, $limit) {
        $text = trim(strip_tags($text));

        if (mb_strlen($text, 'UTF-8') <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit, 'UTF-8');
        $cut = mb_substr($cut, 0, mb_strrpos($cut, ' '), 'UTF-8');

        return $cut;
    }

    function seo_description($text, $limit = 160) {
        // Remove HTML
        $text = strip_tags($text);

        // Decodifica entidades HTML
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove quebras de linha e espaços extras
        $text = preg_replace('/\s+/', ' ', $text);

        // Limpa aspas soltas e caracteres problemáticos
        $text = trim($text);

        // Limita tamanho sem cortar palavras
        if (mb_strlen($text, 'UTF-8') > $limit) {
            $text = mb_substr($text, 0, $limit, 'UTF-8');
            $text = mb_substr($text, 0, mb_strrpos($text, ' '), 'UTF-8');
        }

        // ESCAPA para HTML (ESSENCIAL)
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
?>
<!DOCTYPE html>
<html lang="<?= $htmlLangMap[$lang]; ?>">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="index, follow">

    <?php if ($isProductPage): ?>

        <?php
            // Canonical do produto (Versao com liguas, com erro de canonical diferente no google search console)
            if ($productLanguage === 'pt') {
                $canonicalUrl = $urlLimpa . $productSlug;
            } else {
                $canonicalUrl = $urlLimpa . $productLanguage . '/' . $productSlug;
            }

            echo '<link rel="canonical" href="' . $canonicalUrl . '">' . PHP_EOL;
        ?>

    <?php else: ?>

        <?php
            if (count($languages) > 1) {
                $uri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                $currentPath = $uri[1] ?? '';

                foreach ($languages as $lang) {
                    if (!isset($hreflangMap[$lang])) {
                        continue;
                    }

                    if ($lang == 'pt') {
                        echo '<link rel="alternate" hreflang="' . $hreflangMap[$lang] . '" href="' . $urlLimpa . $currentPath . '">' . PHP_EOL;
                    } else {
                        echo '<link rel="alternate" hreflang="' . $hreflangMap[$lang] . '" href="' . $urlLimpa . $lang . '/' . $currentPath . '">' . PHP_EOL;
                    }
                }

                // x-default = idioma principal da loja (primeiro da lista)
                echo '<link rel="alternate" hreflang="x-default" href="' . $urlLimpa . $currentPath . '">' . PHP_EOL;
            }
        ?>

    <?php endif; ?>

    <!-- Title -->
    <title><?php echo limit_text((!empty($title) ? $title : $loja), 60); ?></title>

    <!-- Description -->
    <meta name="description" content="<?php echo seo_description($description); ?>">

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $favicon; ?>" type="image/x-icon">

    <!-- ================= PERFORMANCE ================= -->

    <!-- Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://unpkg.com" crossorigin>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- ================= CSS ================= -->

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9"
          crossorigin="anonymous">

    <!-- Owl Carousel (mantido apenas CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Plugins locais -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/magnific-popup.min.css">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/nice-select.min.css">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/animate.min.css">
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/slick.min.css">

    <!-- Main Style -->
    <link rel="stylesheet" href="<?php echo INCLUDE_PATH; ?>assets/css/style.css">

    <!-- Box Icons -->
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />

    <!-- jQuery (defer para não bloquear renderização) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>

    <?php
        // Nome da tabela para a busca
        $tabela = 'tb_scripts';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status ORDER BY id DESC";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $shop_id);
        $stmt->bindValue(':status', 1);
        $stmt->execute();

        // Recuperar os resultados
        $scripts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loop através dos resultados e exibir todas as colunas
        foreach ($scripts as $script) {
            echo "<!-- {$script['name']} -->";
            echo $script['script'];
        }
    ?>

</head>

<style>
    a:hover
    {
        color: var(--heading-color) !important;
        text-decoration: underline;
    }
    a.btn.btn-dark:hover,
    a.btn.btn-dark:hover i
    {
        color: white !important;
    }
    .nav-link a:hover,
    a.nav-link:hover,
    a.btn:hover
    {
        text-decoration: none !important;
        color: var(--heading-color) !important;
    }

    .show-categories
    {
        padding: var(--bs-navbar-toggler-padding-y) var(--bs-navbar-toggler-padding-x);
        font-size: var(--bs-navbar-toggler-font-size);
        line-height: 1;
        color: var(--bs-navbar-color);
        background-color: transparent;
        border: var(--bs-border-width) solid var(--bs-navbar-toggler-border-color);
        border-radius: var(--bs-navbar-toggler-border-radius);
        transition: var(--bs-navbar-toggler-transition);
    }
    .categories.nav-show-categories
    {
        transform: translateY(0px) !important;
    }

    /* Navbar */
    #navbarSupportedContent.navActions #search
    {
        display: flex;
    }
    .navbar.categories .close
    {
        display: none !important;
    }
    .mobile-nav
    {
        display: none;
    }
    .nav-arrow
    {
        display: none !important;
    }

    /* Carrossel */
    .carousel-control-next:not(.banner-full),
    .carousel-control-prev:not(.banner-full)
    {
        width: initial;
    }
    .carousel-control-prev-icon:not(.banner-full span)
    {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
    }
    .carousel-control-next-icon:not(.banner-full span)
    {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
    }

    .container.highlight-center .highlight
    {
        position: relative;
    }
    .container.highlight-center .highlight::before
    {
        content: "";
        position: absolute;
        right: 0;
        top: 50%;
        transform: translate(50%, -50%);
        width: 10px;
        height: 10px;
        background: #e0e0e0;
        border-radius: 50%;
    }
    .container.highlight-center .highlight:last-child:before
    {
        display: none;
    }

    a.category-link,
    a.product-link
    {
        text-decoration: none;
    }

    /* Categories */
    .category-link img
    {
        width: 182px;
        height: 182px;
        object-fit: cover;
    }

    /* Product */
    .product-image
    {
        position: relative;
    }
    .product-image .card-discount
    {
        position: absolute;
        left: 15px;
        top: 10px;
        width: 100px;
        header: 5px;
        font-weight: 600;
        border-radius: .6rem;
        text-align: center;
        background: #000;
        color: #fff;
    }
    .product-image .card-img-top
    {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .social-medias li a
    {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Form */
    #whatsappGroup
    {
        align-items: center;
    }
    #whatsappGroup input.form-control,
    #whatsappGroup .btn.btn-dark
    {
        height: 48px;
    }
    
    .categories
    {
        transition: .3s all;
    }
    .categories.scroll
    {
        transform: translateY(-65px);
    }

    /* Estilo para tornar o iframe do YouTube responsivo */
    .video-wrapper
    {
        overflow: hidden;
        padding-top: 56.20%; /* Mantém a proporção de 16:9 */
        position: relative;
        width: 100%;
    }

    .video-wrapper iframe
    {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .to-top
    {
        position: fixed;
        right: 20px;
        bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;

        opacity: 0;
        pointer-events: none;
        transition: .3s all;
    }
    .to-top.scroll
    {
        opacity: 1;
        pointer-events: all;
    }
    .whatsapp-button
    {
        position: fixed;
        left: 20px;
        bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
    }

    /* Responsive */
    @media screen and (max-width: 991px) {
        /* Header */
        .categories.scroll
        {
            transform: translateY(0) !important;
        }
        .show-categories
        {
            display: none !important;
        }
        .navbar.categories .close
        {
            position: fixed;
            top: 20px;
            right: 20px;
        }
        .navbar.categories
        {
            display: none !important;
            position: fixed;
            left: 0;
            top: 99px;
            width: 100%;
            height: calc(100% - 99px);
            z-index: 999999999 !important;
        }
        .navbar-nav.categorias
        {
            width: 100%;
        }
        #navCategories.showCategories .navCategories
        {
            height: 100%;
            display: flex !important;
            flex-direction: column;
            justify-content: space-between !important;
        }
        #navCategories.showCategories,
        #navCategories.showCategories .close
        {
            display: flex !important;
        }

        .mobile-nav
        {
            display: flex;
            align-items: center;
        }
        .mobile-nav .navActionModalButton.active,
        .mobile-nav .navSearchButton.active,
        .mobile-nav .navCategoriesButton.active
        {
            background: #f8f9fa !important;
            border-radius: .375rem;
        }

        .blog
        {
            width: 100%;
            justify-content: center !important;
        }

        .nav-arrow
        {
            display: flex !important;
        }

        .categories .container.container-fluid
        {
            padding: 0 1rem 1rem 1rem !important;
            height: 100%;
            align-items: flex-start;
        }
        .categories .container.container-fluid .nav-link
        {
            padding: .625rem 1rem;
        }
        .categories .container.container-fluid li
        {
            display: block !important;
            margin: 0;
        }
        .todas-categorias,
        .subcategorias
        {
            top: 0 !important;
            padding: .625rem 1rem !important;
        }
        .categorias li.showSubcategories .todas-categorias,
        .categorias li.showSubcategories .subcategorias
        {
            display: flex !important;
        }
        .categories .container.container-fluid li.showSubcategories .nav-link
        {
            background: #f8f9fa;
            border-radius: .375rem;
        }
        .categories .container.container-fluid li.showSubcategories .nav-link .nav-arrow
        {
            transform: rotate(180deg);
        }
        .categories .container.container-fluid li ul
        {
            position: relative;
            width: 100%;
            box-shadow: none;
        }

        /* Services */
        .logo img
        {
            width: auto !important;
            height: 40px;
        }

        #navbarSupportedContent.navActions
        {
            display: block !important;
        }

        #navbarSupportedContent.navActions #search,
        #navbarSupportedContent.navActions .service
        {
            display: none;
        }

        .mobile-nav button
        {
            padding: .3rem;
        }
        .mobile-nav button i
        {
            color: black;
        }
        .mobile-nav .line
        {
            width: 1px;
            height: calc(1.375rem + 1.5vw) !important;
            background: #c4c4c4;
        }
        .navActions #search.showSearch
        {
            display: flex !important;
            position: absolute;
            top: 67px;
            left: 0;
            width: 100%;
            background: #fff;
            padding: 1rem 2.4375rem;
        }

        /* Nav contact */
        #actionContainer.showActionContainer
        {
            display: flex !important;
            position: fixed;
        }
        #actionContainer.showActionContainer .nav-link
        {
            display: none !important;
        }
        #actionModal.showActionModal
        {
            display: flex;
            position: fixed;
            left: 0;
            bottom: 0;
            top: auto;
            width: 100%;
            padding: 1rem;
            border-radius: 1rem 1rem 0 0;
        }
        #actionModal.showActionModal ul
        {
            width: 100%;
        }

        .closeActionModal
        {
            background: none;
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        /* Tarjas centrais */
        .container.highlight-center .highlight::before
        {
            display: none;
        }

        /* Carrossel */
        #bannerCarousel
        {
            margin-top: <?php echo ($top_highlight_bar == 1) ? "99px" : "67px"; ?> !important;
        }
    }

    @media screen and (min-width: 1024px) {
        .categorias li:hover .todas-categorias,
        .categorias li:hover .subcategorias {
            display: flex;
        }
    }
</style>
<style>
    /* Adiciona uma seta após as categorias que têm subcategorias */
    .navCategories > ul > li > .nav-link {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        text-wrap: nowrap;
    }
    .navCategories > ul > li > .nav-link .bx.bx-chevron-down {
        cursor: pointer;
    }
</style>
<style>
    .owl-nav button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 4rem !important;
    }
    .owl-nav button.owl-prev {
        left: 4px;
    }
    .owl-nav button.owl-next {
        right: 4px;
    }
</style>
<body>

    <?php if ($showLangModal): ?>
    <?php
        $languageNames = [
            'pt' => 'Português',
            'en' => 'English',
            'es' => 'Español',
            'de' => 'Deutsch',
        ];

        $langModalTexts = [
            'pt' => [
                'title' => 'Escolha o idioma',
                'message' => 'Detectamos que você está no Brasil. Deseja acessar o site em <strong>Português</strong>?',
                'stay' => 'Continuar em Português',
                'change' => 'Ir para Português',
            ],
            'en' => [
                'title' => 'Choose your language',
                'message' => 'We detected that you are in your country. Would you like to view the site in <strong>English</strong>?',
                'stay' => 'Continue in Portuguese',
                'change' => 'Go to English',
            ],
            'es' => [
                'title' => 'Elige el idioma',
                'message' => 'Detectamos que te encuentras en tu país. ¿Deseas ver el sitio en <strong>Español</strong>?',
                'stay' => 'Continuar en Portugués',
                'change' => 'Ir a Español',
            ],
            'de' => [
                'title' => 'Sprache auswählen',
                'message' => 'Wir haben erkannt, dass Sie sich in Ihrem Land befinden. Möchten Sie die Website auf <strong>Deutsch</strong> ansehen?',
                'stay' => 'Auf Portugiesisch bleiben',
                'change' => 'Zu Deutsch wechseln',
            ],
        ];

        $modalLang = $suggestedLang;

        if (!isset($langModalTexts[$modalLang])) {
            $modalLang = 'en';
        }

        $modalText = $langModalTexts[$modalLang];
        $languageFullName = $languageNames[$suggestedLang];
    ?>
    <div class="modal fade" id="langModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?= $modalText['title']; ?>
                    </h5>
                </div>
                <div class="modal-body">
                    <p><?= $modalText['message']; ?></p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-secondary" id="keepLang">
                        Continuar em Português
                    </button>
                    <button class="btn btn-dark" id="changeLang">
                        <?= $modalText['change']; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($showLangModal): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('langModal'));
            modal.show();

            document.getElementById('keepLang').onclick = function () {
                document.cookie = "lang_choice=pt; path=/; max-age=31536000";
                modal.hide();
            };

            document.getElementById('changeLang').onclick = function () {
                document.cookie = "lang_choice=<?= $suggestedLang ?>; path=/; max-age=31536000";
                window.location.href = "<?= INCLUDE_PATH_LOJA . $suggestedLang . $currentPath ?>/";
            };
        });
    </script>
    <?php endif; ?>

    <header class="header fixed-top">
        <div class="stripe text-center text-light <?php echo ($top_highlight_bar == 0) ? "d-none" : ""; ?> <?php echo ($top_highlight_bar_location == 1) ? "" : "d-none"; ?>" style="background-color: rgb(35, 35, 35);"><?php echo $top_highlight_bar_text; ?></div>
        <nav class="navbar bg-white navbar-expand-lg border-bottom border-body z-3" id="navbar" data-bs-theme="white">
            <div class="container container-fluid" style="padding-right: calc(1.5rem + 15px); padding-left: calc(1.5rem + 15px);">
                <a class="navbar-brand logo" href="<?php echo INCLUDE_PATH_LOJA; ?>"><?php echo (isset($logo)) ? '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $logo . '" id="logo" alt="Logo ' . $loja . '" style="width: 150px;">' : $loja; ?></a>
                <button class="show-categories me-3 d-none" id="show-categories" type="button">
                    <i class='bx bx-menu fs-3' id="toggle-icon"></i>
                </button>
                <div class="mobile-nav">
                    <button class="navActionModalButton d-flex align-items-center bg-transparent me-2" type="button">
                        <i class="bx bxl-whatsapp fs-1"></i>
                    </button>
                    <button class="navSearchButton d-flex align-items-center bg-transparent me-2" type="button">
                        <i class="bx bx-search-alt-2 fs-1"></i>
                    </button>
                    <div class="line"></div>
                    <button class="navCategoriesButton d-flex align-items-center bg-transparent ms-2" type="button">
                        <i class="bx bx-menu fs-1"></i>
                    </button>
                </div>
                <div class="navActions collapse navbar-collapse" id="navbarSupportedContent">
                    <form class="me-3" id="search" role="search" action="<?php echo INCLUDE_PATH_LOJA; ?>busca" method="GET">
                        <input class="form-control py-1 px-3" type="search" name="q" placeholder="<?= __('search_product') ?>" aria-label="Search" style="border-radius: var(--bs-border-radius) 0 0 var(--bs-border-radius);" value="<?php echo @$_GET['q']; ?>">
                        <button class="btn btn-dark" type="submit" style="border-radius: 0 var(--bs-border-radius) var(--bs-border-radius) 0;">
                            <i class='bx bx-search-alt-2'></i>
                        </button>
                    </form>
                    <style>
                        /* Tooltip */
                        .service {
                            position: relative;
                        }

                        .service-tooltip {
                            width: 220px;
                            display: none;
                            position: absolute;
                            top: 100%;
                            right: 0;
                            background-color: #fff;
                            text-align: center;
                            line-height: 1.5;
                            padding: 10px;
                            border-radius: .3rem;
                            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                            z-index: 1;
                        }

                        .service-tooltip {
                            width: max-content;
                            text-align: start;
                        }

                        .service:hover .service-tooltip {
                            display: block;
                        }

                        .closeActionModal {
                            display: none;
                        }
                    </style>
                    <div class="navbar-nav service ms-auto mb-lg-0" id="actionContainer">
                        <a class="nav-link d-flex align-items-center active" aria-current="page" href="#">
                            <i class='bx bxl-whatsapp fs-1 me-2' ></i>
                            <small class="fw-semibold lh-sm" style="max-width: 65px;"><?= __('support_center') ?></small>
                        </a>
                        <div class="service-tooltip" id="actionModal">
                            <ul class="p-2">
                                <div class="service-header d-flex align-items-center justify-content-between">
                                    <span class="text-black fw-semibold"><?= __('service') ?></span>
                                    <button class="navActionModalButton closeActionModal" type="button">
                                        <i class="bx bx-x fs-1"></i>
                                    </button>
                                </div>
                                <li class="text-secondary mt-2 mb-2 <?php echo ($phone == "") ? "d-none" : ""; ?>">
                                    <i class='bx bxs-phone' ></i> <?= __('phone') ?>: 
                                    <a href="tel:<?php echo $phone; ?>">
                                        <?php echo $phone; ?>
                                    </a>
                                </li>
                                <li class="text-secondary tel-whatsapp mb-2 <?php echo ($whatsapp == "") ? "d-none" : ""; ?>">
                                    <i class="bx bxl-whatsapp"></i> <?= __('whatsapp') ?>: 
                                    <a href="https://api.whatsapp.com/send?phone=<?php echo $formatted_whatsapp; ?>" target="_blank">
                                        <?php echo $whatsapp; ?>
                                    </a>
                                </li>
                                <li class="text-secondary <?php echo ($email == "") ? "d-none" : ""; ?>">
                                    <i class="bx bxs-envelope"></i> <?= __('email') ?>: 
                                    <a href="mailto:<?php echo $email; ?>">
                                        <?php echo $email; ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        <nav class="categories navbar bg-white navbar-expand-lg border-bottom border-body z-2" id="navCategories" data-bs-theme="white">
            <div class="container container-fluid" style="padding-right: calc(1.5rem + 15px); padding-left: calc(1.5rem + 15px);">
                <div class="navCategories collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav categorias me-auto mb-2 mb-lg-0">
                        <style>
                            .categorias {
                                list-style: none;
                            }

                            .categorias li {
                                position: relative;
                                margin-right: 20px;
                            }

                            .nav-link i.bx#toggle-categories
                            {
                                line-height: inherit;
                                display: flex;
                            }

                            .subcategorias {
                                width: max-content;
                                display: none;
                                position: absolute;
                                top: 100%;
                                left: 0;
                                background-color: #fff;
                                padding: 10px;
                                border-radius: .3rem;
                                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                                z-index: 1;
                            }

                            /* Todas Categorias */
                            .todas-categorias {
                                width: max-content;
                                display: none;
                                flex-direction: column;
                                position: absolute;
                                max-height: 70vh;
                                top: 100%;
                                left: 0;
                                background-color: #fff;
                                padding: 10px;
                                border-radius: .3rem;
                                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                                overflow-y: auto;
                                z-index: 999999;
                            }

                            /* Estilo do tooltip */
                            .todas-categorias li,
                            .subcategorias li {
                                margin: 5px;
                            }

                            /* Para tooltip todas as categorias */
                            /* .todas-categorias
                            {
                                width: 300px;
                            } */
                            /* .todas-categorias li a
                            {
                                font-weight: 500;
                            } */
                        </style>
                        <li class="nav-item">
                            <div class="nav-link">
                                <a href="#" class="d-flex">
                                    <i class='bx bx-menu fs-3 me-2' id="toggle-categories"></i>
                                    <?= __('all_categories') ?>
                                </a>
                                <i class="bx bx-chevron-down nav-arrow"></i>
                            </div>
                            <ul class="todas-categorias">
                                <?php
                                    // Busca todas as categorias ativas que possuem pelo menos 1 produto ativo
                                    $sql = "
                                        SELECT
                                            c.id,
                                            c.name,
                                            c.link,
                                            c.parent_category
                                        FROM tb_categories c
                                        WHERE
                                            c.shop_id = :shop_id
                                            AND c.status = 1
                                        GROUP BY
                                            c.id
                                        ORDER BY
                                            c.name ASC
                                    ";

                                    $stmt = $conn_pdo->prepare($sql);
                                    $stmt->bindValue(':shop_id', $shop_id);
                                    $stmt->execute();

                                    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    // Organiza em árvore
                                    $arvore = [];

                                    foreach ($categorias as $categoria) {

                                        if ((int)$categoria['parent_category'] <= 1) {

                                            $arvore[$categoria['id']] = [
                                                'dados' => $categoria,
                                                'filhos' => []
                                            ];

                                        }

                                    }

                                    foreach ($categorias as $categoria) {

                                        if ((int)$categoria['parent_category'] > 1) {

                                            if (isset($arvore[$categoria['parent_category']])) {

                                                $arvore[$categoria['parent_category']]['filhos'][] = $categoria;

                                            }

                                        }

                                    }

                                    foreach ($arvore as $categoria) {

                                        echo '<li>';

                                        echo '<a href="' . INCLUDE_PATH_LOJA . 'c/' . $categoria['dados']['link'] . '">';
                                        echo '<strong>' . htmlspecialchars($categoria['dados']['name']) . '</strong>';
                                        echo '</a>';

                                        if (!empty($categoria['filhos'])) {

                                            echo '<ul class="ms-3 mt-1">';

                                            foreach ($categoria['filhos'] as $subcategoria) {

                                                echo '<li>';

                                                echo '<a href="' . INCLUDE_PATH_LOJA . 'c/' . $subcategoria['link'] . '">';
                                                echo htmlspecialchars($subcategoria['name']);
                                                echo '</a>';

                                                echo '</li>';

                                            }

                                            echo '</ul>';

                                        }

                                        echo '</li>';

                                    }
                                ?>
                            </ul>
                        </li>
                        <?php
                            // Aqui você pode popular a tabela com dados do banco de dados
                            // Vamos supor que cada linha tem um ID único
                            
                            // Nome da tabela para a busca
                            $tabela = 'tb_categories';

                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND emphasis = :emphasis AND parent_category = :parent_category ORDER BY position ASC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $shop_id);
                            $stmt->bindValue(':status', 1);
                            $stmt->bindValue(':emphasis', 1);
                            $stmt->bindValue(':parent_category', 1);
                            $stmt->execute();

                            // Recuperar os resultados
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Loop através dos resultados e exibir todas as colunas
                            $contador = 0; // Inicializa o contador
                            foreach ($categories as $category) {
                                // Consulta SQL para selecionar todas as colunas com base no ID
                                $sql = "SELECT * FROM tb_categories WHERE shop_id = :shop_id AND status = :status AND parent_category = :parent_category";
        
                                // Preparar e executar a consulta
                                $stmt = $conn_pdo->prepare($sql);
                                $stmt->bindParam(':shop_id', $shop_id);
                                $stmt->bindValue(':status', 1);
                                $stmt->bindParam(':parent_category', $category['id']);
                                $stmt->execute();
        
                                // Recuperar os resultados
                                $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                                echo "<li class='nav-item d-flex'>";
                                echo "<div class='nav-link'>";
                                echo "<a href='" . INCLUDE_PATH_LOJA . "c/" . $category['link'] . "'>";
                                echo "<img src='" . INCLUDE_PATH_DASHBOARD . "back-end/category/" . $shop_id . "/icon/" . $category['icon'] . "' alt='Ícone " . $category['name'] . "' class='me-2' style='width: 32px; height: 32px;'>";
                                echo $category['name'];
                                echo "</a>";
                                echo "</div>";
                                if (!empty($subcategories)) { // Verifica se há resultados em $subcategories
                                    echo "<ul class='subcategorias'>"; // Abre a lista apenas se houver resultados
                                
                                    $contador = 0; // Inicializa o contador
                                
                                    // Loop através dos resultados e exibir todas as colunas
                                    foreach ($subcategories as $subcategory) {
                                        if ($contador % 6 == 0) {
                                            echo "<div style='display: flex; flex-direction: column; margin-right: 5px;'>";
                                        }
                                        echo "<li>";
                                        echo "<a href='" . INCLUDE_PATH_LOJA . $subcategory['link'] . "'>" . $subcategory['name'] . "</a>";
                                        echo "</li>";
                                        if (($contador + 1) % 6 == 0 || ($contador == count($subcategories) - 1)) {
                                            echo "</div>"; // Fecha a <div> a cada 6 elementos ou no último elemento
                                        }
                                        $contador++;
                                    }
                                
                                    echo "</ul>"; // Fecha a lista
                                }
                                echo "</li>";
                            }
                        ?>
                    </ul>

                    <?php
                        $sql = "SELECT COUNT(*) FROM tb_articles WHERE shop_id = :shop_id AND status = 1";

                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
                        $stmt->execute();

                        $countArticles = (int) $stmt->fetchColumn();
                    ?>
                    <a href="<?php echo INCLUDE_PATH_LOJA; ?>blog/" class="blog btn btn-light d-flex justify-content-end align-items-center <?php echo ($countArticles == 0) ? "d-none" : ""; ?>">
                        <img style="height: 28px; margin-right: 7px;" src="<?php echo INCLUDE_PATH; ?>assets/loja/icon/blog.png" alt="Icon">
                        <strong class="titulo text-dark"><?= __('blog') ?></strong>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
$(document).ready(function() {
    // Adiciona uma seta após as categorias que têm subcategorias
    $('.categorias li:has(.subcategorias)').each(function() {
        $(this).find('.nav-link').append('<i class="bx bx-chevron-down nav-arrow"></i>');
    });

    // Quando a seta é clicada, adiciona ou remove a classe showSubcategories ao li
    $('.categorias li .nav-arrow').click(function(e) {
        e.preventDefault();
        var $li = $(this).closest('li');
        if ($li.hasClass('showSubcategories')) {
            $li.removeClass('showSubcategories');
        } else {
            $('.categorias li').removeClass('showSubcategories');
            $li.addClass('showSubcategories');
        }
    });
});
</script>
    <main>
        <?php if (!$isProductPage): ?>
        <div id="bannerCarousel" class="carousel slide mb-4" data-bs-ride="carousel" style="margin-top: <?php echo ($top_highlight_bar == 1) ? "186.39px" : "154.39px"; ?>;">
            <!-- Indicators (pontos de navegação) -->
            <ol class="carousel-indicators">
            <?php
                // Nome da tabela para a busca
                $tabela = 'tb_banner_info';

                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND location = :location ORDER BY id DESC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':location', 'full-banner');
                $stmt->execute();

                $countBanners = $stmt->rowCount();

                // Inicializa uma variável para contar os IDs
                $contador = 0;

                // Verifica se há IDs para evitar um loop vazio
                if ($stmt->rowCount() > 0) {
                    // Faça um loop para contar os IDs sequencialmente
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $currentId = $row['id'];

                        $active = ($contador == 0) ? 'active' : ''; // Adicione a classe active ao ID 1
                        echo "<li data-bs-target='#bannerCarousel' data-bs-slide-to='" . $contador . "' class='" . $active . "'></li>";

                        $contador++;
                    }
                }
            ?>
            </ol>

            <script>
                function switchImage(responsiveImage, desktopBanner, mobileBanner) {
                    if (window.innerWidth < 768 && mobileBanner !== 'null') {
                        responsiveImage.src = mobileBanner;
                    } else {
                        responsiveImage.src = desktopBanner;
                    }
                }

                // Chame a função com os parâmetros corretos ao carregar a página e redimensionar
                window.onload = function () {
                    var responsiveImage = document.querySelector('#bannerCarousel .carousel-item.active img');
                    var desktopBanner = responsiveImage.getAttribute('data-desktop-banner');
                    var mobileBanner = responsiveImage.getAttribute('data-mobile-banner');

                    switchImage(responsiveImage, desktopBanner, mobileBanner);
                };

                window.onresize = function () {
                    var responsiveImage = document.querySelector('#bannerCarousel .carousel-item.active img');
                    var desktopBanner = responsiveImage.getAttribute('data-desktop-banner');
                    var mobileBanner = responsiveImage.getAttribute('data-mobile-banner');

                    switchImage(responsiveImage, desktopBanner, mobileBanner);
                };
            </script>

            <!-- Slides (itens do carrossel) -->
            <div class="carousel-inner">
                <?php
                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND location = :location ORDER BY id DESC";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':location', 'full-banner');
                $stmt->execute();

                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $primeiroElemento = true;

                // Loop através dos resultados e exibir todas as colunas
                foreach ($resultados as $banner) {
                    // Consulta SQL para selecionar todas as colunas com base no ID
                    $sql = "SELECT * FROM tb_banner_img WHERE banner_id = :banner_id ORDER BY id ASC LIMIT 1";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':banner_id', $banner['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $imagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop através dos resultados e exibir todas as colunas
                    foreach ($imagens as $imagem) {
                        // Adicione a classe especial apenas ao primeiro elemento
                        $active = ($primeiroElemento) ? 'active' : '';
                        $mobileBannerSrc = (isset($imagem['mobile_banner'])) ? INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['mobile_banner'] : 'null';

                        ?>
                        <a href="<?= $banner['link'] ?>" target="<?= $banner['target'] ?>">
                            <div class="carousel-item <?= $active ?>">
                                <img
                                    src="<?= INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['image_name'] ?>"
                                    alt="<?= $banner['name'] ?>"
                                    data-desktop-banner="<?= INCLUDE_PATH_DASHBOARD . 'back-end/banners/' . $imagem['banner_id'] . '/' . $imagem['image_name'] ?>"
                                    data-mobile-banner="<?= $mobileBannerSrc ?>"
                                    class="w-100"
                                    style="object-fit: cover;"
                                >
                            </div>
                        </a>
                        <?php

                        // Marque que o primeiro elemento foi processado
                        $primeiroElemento = false;
                    }
                }
                ?>
            </div>

            <!-- Controles (setas de navegação) -->
            <a class="carousel-control-prev banner-full" href="#bannerCarousel" role="button" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </a>
            <a class="carousel-control-next banner-full" href="#bannerCarousel" role="button" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Próximo</span>
            </a>
        </div>
        <?php endif; ?>

        <?php
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $uri = $_SERVER['REQUEST_URI'];
            
            $url = $protocol . "://" . $host . $uri;

            $substring_category = "categoria/";
            $substring_category_slug = "c/";
            $substring_page = "atendimento/";
            $substring_article = "blog/";
            $substring_search = "busca";
            
            // Obtenha a rota da URL
            $route = isset($_GET['url']) ? $_GET['url'] : '';

            // Remove "en/" ou "de/" do início da rota, se existir
            $route = preg_replace('#^(en|es|de|pt)/#', '', $route);
            
            $page301 = null;
            $categoria = null; // Inicializa $categoria fora do bloco condicional
            
            if (strpos($url, $substring_category) !== false) {
                // Removendo "categoria/" da URL
                $link = preg_replace("/^categoria\//", "", $route);
            
                // Redireciona para pagina 301 para redirecionamento
                $page301 = true;
            } elseif (strpos($url, $substring_category_slug) !== false) {
                // Remove "c/" do início do route
                $link = preg_replace("/^c\//", "", $route);
            
                // Tabela que será pesquisada
                $tabela = "tb_categories";
            
                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE link = :link AND shop_id = :shop_id AND status = :status";
            
                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);
            
                // Vincular o valor do parâmetro
                $stmt->bindParam(':link', $link, PDO::PARAM_STR);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
            
                // Executar a consulta
                $stmt->execute();
            
                // Obter o categoria como um array associativo
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($category) {
                    $category_id = $category['id'];
                }
            } elseif (strpos($url, $substring_page) !== false) {
                // Removendo "atendimento/" da URL
                $link = preg_replace("/^atendimento\//", "", $route);
            
                // Tabela que será pesquisada
                $tabela = "tb_pages";
            
                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE link = :link AND shop_id = :shop_id AND status = :status";
            
                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);
            
                // Vincular o valor do parâmetro
                $stmt->bindParam(':link', $link, PDO::PARAM_STR);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
            
                // Executar a consulta
                $stmt->execute();
            
                // Obter o atendimento como um array associativo
                $page = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($page) {
                    $page_id = $page['id'];
                }
            } elseif (strpos($url, $substring_article) !== false) {
                // Removendo "atendimento/" da URL
                $link = preg_replace("/^blog\//", "", $route);
            
                // Tabela que será pesquisada
                $tabela = "tb_articles";
            
                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE link = :link AND shop_id = :shop_id AND status = :status LIMIT 1";
            
                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);
            
                // Vincular o valor do parâmetro
                $stmt->bindParam(':link', $link, PDO::PARAM_STR);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
            
                // Executar a consulta
                $stmt->execute();
            
                // Obter o atendimento como um array associativo
                $article = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($article) {
                    $article_id = $article['id'];
                }
            } elseif (strpos($url, $substring_search) !== false) {
                $search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

                $limite = 48;
                $paginaAtual = isset($_GET['page']) && (int)$_GET['page'] > 0
                    ? (int)$_GET['page']
                    : 1;

                if ($search_query === '') {
                    $search_results = [];
                    $totalRegistros = 0;
                    $totalPaginas = 0;
                } else {
                    $search_query_like = '%' . $search_query . '%';

                    $sqlTotal = "
                        SELECT COUNT(*)
                        FROM tb_products
                        WHERE name LIKE :search_query
                        AND shop_id = :shop_id
                        AND status = 1
                    ";

                    $stmtTotal = $conn_pdo->prepare($sqlTotal);
                    $stmtTotal->bindValue(':search_query', $search_query_like, PDO::PARAM_STR);
                    $stmtTotal->bindValue(':shop_id', $shop_id);
                    $stmtTotal->execute();

                    $totalRegistros = (int)$stmtTotal->fetchColumn();
                    $totalPaginas = $totalRegistros > 0
                        ? (int)ceil($totalRegistros / $limite)
                        : 0;

                    if ($totalPaginas > 0 && $paginaAtual > $totalPaginas) {
                        $paginaAtual = $totalPaginas;
                    }

                    $offset = ($paginaAtual - 1) * $limite;

                    $sql = "
                        SELECT id, name, price, discount, link, without_price
                        FROM tb_products
                        WHERE name LIKE :search_query
                        AND shop_id = :shop_id
                        AND status = 1
                        ORDER BY id ASC
                        LIMIT :limite OFFSET :offset
                    ";

                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindValue(':search_query', $search_query_like, PDO::PARAM_STR);
                    $stmt->bindValue(':shop_id', $shop_id);
                    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->execute();

                    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            } else {
                // Tabela que será pesquisada
                $tabela = "tb_products";
            
                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE link = :link AND shop_id = :shop_id AND status = :status LIMIT 1";
            
                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);
            
                // Vincular o valor do parâmetro
                $stmt->bindParam(':link', $route, PDO::PARAM_STR);
                $stmt->bindParam(':shop_id', $shop_id);
                $stmt->bindValue(':status', 1);
            
                // Executar a consulta
                $stmt->execute();
            
                // Obter o produto como um array associativo
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
                if ($product) {
                    $product_id = $product['id'];
                    $price = $product['price'] ?: $product['without_price'];
                    
                    $sql = "SELECT * FROM imagens WHERE usuario_id = :usuario_id ORDER BY id ASC LIMIT 1";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':usuario_id', $product['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $imagem = $stmt->fetch(PDO::FETCH_ASSOC);
                    
        ?>

        <script type="application/ld+json">
        {
          "@context": "https://schema.org/",
          "@type": "Product",
          "name": "<?= htmlspecialchars($product['name']); ?>",
          "image": "<?= ($imagem) ? CDN_BASE_URL . 'products/' . $product['id'] . '/' . $imagem['nome_imagem'] : INCLUDE_PATH_DASHBOARD . 'back-end/imagens/no-image.jpg'; ?>",
          "description": "<?= htmlspecialchars($product['description']); ?>",
          "sku": "<?= $product['sku']; ?>",
          "offers": {
            "@type": "Offer",
            "url": "<?= $url; ?>",
            "priceCurrency": "BRL",
            "price": "<?= $price; ?>",
            "priceValidUntil": "<?= date('Y-m-d', strtotime('+1 year')); ?>",
            "availability": "https://schema.org/InStock"
          }
        }
        </script>

        <?php
                }
            }
            
            // Analise a rota e determine qual página carregar
            if (empty($route)) {
                // Página inicial da loja
                include_once('pages/loja.php');
            } elseif (@$product) {
                // Página de detalhes do produto
                include_once('pages/produto.php');
            } elseif (strpos($url, $substring_search) !== false) {
                // Página de detalhes do produto
                include_once('pages/busca.php');
            } elseif (@$page) {
                // Página de detalhes da página
                include_once('pages/pagina.php');
            } elseif ($route === "blog" || $route === "blog/") {
                // Remove o banner padrao da loja
                echo '<script>document.getElementById("bannerCarousel").classList.add("d-none");</script>';

                // Página de detalhes da página
                include_once('pages/blog.php');
            } elseif (@$article) {
                // Remove o banner padrao da loja
                echo '<script>document.getElementById("bannerCarousel").classList.add("d-none");</script>';

                // Página de detalhes da página
                include_once('pages/artigo.php');
            } elseif ($page301) {
                // Página de detalhes da categoria
                include_once('pages/301.php');
            } elseif ($stmt->rowCount() > 0) {
                // Página de detalhes da categoria
                include_once('pages/categoria.php');
            } else {
                // Página de erro 404 para rotas não encontradas
                include_once('pages/404.php');
            }
        ?>

        <div class="justify-content-center <?php echo ($token_instagram == "") ? "d-none" : "mb-3"; ?>" style="text-align: -webkit-center;">
            <div class="d-flex align-items-center justify-content-center">
                <p class="d-flex align-items-center fs-4 mb-3 me-2">
                    <i class='bx bxl-instagram fs-1 me-2' ></i>
                    Siga nosso instagram
                </p>
                <h4 class="mb-3" id="username"></h4>
            </div>
            <div style="width: 100px; height: 5px; background: #000;"></div>
        </div>

        <!-- Feed Instagram -->
        <style>
            #instafeed.item::before
            {
                content: '';
                position: absolute;
                width: 100%;
                height: 100%;
                background: black;
                opacity: 0;
                transition: .6s;
                pointer-events: none;
            }
            #instafeed.item .bxl-instagram
            {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%,-50%);
                font-size: 2.5rem;
                color: white;
                opacity: 0;
                transition: .3s;
                pointer-events: none;
            }
            #instafeed.item:hover .bxl-instagram
            {
                opacity: 1;
            }
            #instafeed.item:hover::before
            {
                opacity: .5;
            }
        </style>

        <!-- Div para mostrar as imagens do feed do Instagram -->
        <div id="instafeed" class="owl-carousel owl-theme owl-loaded owl-drag carousel-inner mb-4"></div>
    
        <?php if (!empty($whatsapp_group)) { ?>
        <div class="container">
            <div class="row p-4" id="whatsappGroupContainer">
                <p class="col-md-6 d-flex align-items-center fs-4">
                    <i class='bx bxl-whatsapp fs-2 me-2'></i>
                    Quero participar do canal do WhatsApp ou grupo fechado e receber as promoções
                </p>
                <form class="col-md-6 d-flex justify-content-end" role="text" id="whatsappGroup">
                    <a href="<?php echo $whatsapp_group; ?>" target="_blank" class="btn btn-dark d-flex align-items-center justify-content-center" id="btn-whatsapp" type="submit" style="width: 270px;">
                        Entrar no grupo
                    </a>
                </form>
            </div>
        </div>
        <?php } ?>

        <a href="#" class="to-top btn btn-dark p-2 rounded-1">
            <i class='bx bx-chevron-up fs-2' ></i>
        </a>

        <a href="https://api.whatsapp.com/send?phone=<?php echo $formatted_whatsapp; ?>" target="_blank" class="whatsapp-button btn btn-dark p-2 rounded-1 <?php echo ($whatsapp == "") ? "d-none" : ""; ?>">
            <i class='bx bxl-whatsapp fs-2'></i>
        </a>
    </main>

    <!-- Footer -->
    <footer id="rodape" style="background-color: #fff; border-top: 1px solid #ddd; position: relative; z-index: 10; display: block !important; margin-bottom: 2rem;">
        <div class="container">
            <div class="logo text-primary">
                <a href="<?php echo INCLUDE_PATH_LOJA; ?>" title="<?php echo $loja; ?>">
                    <?php echo (isset($logo)) ? '<img src="' . INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $logo . '" alt="Logo ' . $loja . '" style="width: 250px;">' : $loja; ?>
                </a>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3" style="margin-bottom: 4rem;">
                    <span class="titulo fw-semibold"><?= __('about_store') ?></span>
                    <p class="mt-3" id="meuParagrafo"></p>
                    <button class="btn btn-dark more mb-4" id="verMaisBotao"><?= __('see_more') ?></button>

                    <script>
                        function limitarTexto(texto, limite) {
                            if (texto.length > limite) {
                                return {
                                    texto: texto.slice(0, limite),
                                    cortado: true
                                };
                            } else {
                                return {
                                    texto: texto,
                                    cortado: false
                                };
                            }
                        }

                        function toggleTexto() {
                            var paragrafo = document.getElementById("meuParagrafo");
                            var textoCompleto = paragrafo.getAttribute("data-texto-completo");
                            var estadoAtual = paragrafo.getAttribute("data-estado");

                            if (estadoAtual === "colapsado") {
                                // Expandir o texto
                                paragrafo.textContent = textoCompleto;
                                paragrafo.setAttribute("data-estado", "expandido");
                                document.getElementById("verMaisBotao").textContent = "Ver Menos";
                            } else {
                                // Colapsar o texto
                                var limiteInicial = 200; // Limite de caracteres inicial
                                var resultado = limitarTexto(textoCompleto, limiteInicial);
                                paragrafo.textContent = resultado.texto + "...";
                                paragrafo.setAttribute("data-estado", "colapsado");
                                document.getElementById("verMaisBotao").textContent = "Ver Mais";
                            }
                        }

                        // Exemplo de uso:
                        var textoCompleto = "<?php echo $description; ?>";
                        var limiteInicial = 200; // Limite de caracteres inicial
                        var resultado = limitarTexto(textoCompleto, limiteInicial);

                        // Selecione o elemento <p> pelo ID
                        var paragrafo = document.getElementById("meuParagrafo");

                        if (resultado.cortado) {
                            paragrafo.textContent = resultado.texto + "...";
                            paragrafo.setAttribute("data-texto-completo", textoCompleto);
                            paragrafo.setAttribute("data-estado", "colapsado");

                            // Adicione um botão "Ver Mais"
                            var verMaisBotao = document.getElementById("verMaisBotao");
                            verMaisBotao.addEventListener("click", toggleTexto);
                        } else {
                            paragrafo.textContent = resultado.texto;
                            document.getElementById("verMaisBotao").style.display = "none"; // Esconde o botão se o texto não for cortado
                        }
                    </script>

                    <div class="lista-redes">
                        <?php if (!empty($facebook) || !empty($x) || !empty($pinterest) || !empty($instagram) || !empty($youtube)) : ?>
                            <h5 class="fw-semibold"><?= __('follow_us') ?></h5>
                        <?php endif; ?>

                        <ul class="social-medias d-flex">
                            <?php if (!empty($facebook)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $facebook; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no Facebook">
                                        <i class='bx bxl-facebook'></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($x)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $x; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no Twitter">
                                        <i class="fa-brands fa-x-twitter" style="font-size: 14px;"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($pinterest)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $pinterest; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no Pinterest">
                                        <i class='bx bxl-pinterest'></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($instagram)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $instagram; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no Instagram">
                                        <i class='bx bxl-instagram'></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($youtube)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $youtube; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no YouTube">
                                        <i class='bx bxl-youtube'></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($tiktok)) : ?>
                                <li class="me-2">
                                    <a href="<?php echo $tiktok; ?>" class="btn btn-dark fs-6" target="_blank" aria-label="Siga-nos no TikTok">
                                        <i class='bx bxl-tiktok'></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div class="col-md-2 mb-3">
                    <span class="titulo fw-semibold"><?= __('categories') ?></span>
                    <ul class="total-itens_8 mt-3">

                        <?php
                            // Aqui você pode popular a tabela com dados do banco de dados
                            // Vamos supor que cada linha tem um ID único
                            
                            // Nome da tabela para a busca
                            $tabela = 'tb_categories';

                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status AND parent_category = :parent_category ORDER BY position ASC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $shop_id);
                            $stmt->bindValue(':status', 1);
                            $stmt->bindValue(':parent_category', 1);
                            $stmt->execute();

                            // Recuperar os categories
                            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($stmt->rowCount() > 0) {
                                // Loop através dos resultados e exibir todas as colunas
                                foreach ($categories as $category) {
                                    echo "<li>";
                                    echo "<a href='" . INCLUDE_PATH_LOJA . "c/" . $category['link'] . "'>" . $category['name'] . "</a>";
                                    echo "</li>";
                                }
                            }
                        ?>

                        <li id="menu_blog" class="mt-2 <?php echo ($countArticles == 0) ? "d-none" : ""; ?>">
                            <a href="<?php echo INCLUDE_PATH_LOJA; ?>blog/" class="btn btn-light">
                                <img style="height: 28px; margin-right: 7px;" src="https://cdn.awsli.com.br/2544/2544943/arquivos/blog.svg" alt="Blog">
                                <strong class="titulo text-dark"><?= __('blog') ?></strong>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-3 mb-3">
                    <span class="titulo fw-semibold"><?= __('institutional') ?></span>
                    <ul class="mt-3">
                        <?php
                            // Aqui você pode popular a tabela com dados do banco de dados
                            // Vamos supor que cada linha tem um ID único
                            
                            // Nome da tabela para a busca
                            $tabela = 'tb_pages';

                            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND status = :status ORDER BY id DESC";

                            // Preparar e executar a consulta
                            $stmt = $conn_pdo->prepare($sql);
                            $stmt->bindParam(':shop_id', $shop_id);
                            $stmt->bindValue(':status', 1);
                            $stmt->execute();

                            // Recuperar os pages
                            $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if ($stmt->rowCount() > 0) {
                                // Loop através dos pages e exibir todas as colunas
                                foreach ($pages as $page) {
                                    echo "<li>";
                                    echo "<a href='" . INCLUDE_PATH_LOJA . "atendimento/" . $page['link'] . "'>" . $page['name'] . "</a>";
                                    echo "</li>";
                                }
                            }
                        ?>
                    </ul>
                </div>

                <div class="col-md-3 mb-3">
                    <span class="titulo fw-semibold"><?= __('service') ?></span>
                    <ul class="contact mt-3">
                        <li class="<?php echo ($phone == "") ? "d-none" : ""; ?>">
                            <i class='bx bxs-phone' ></i> <?= __('phone') ?>: 
                            <a href="tel:<?php echo $phone; ?>">
                                <?php echo $phone; ?>
                            </a>
                        </li>
                        <li class="tel-whatsapp <?php echo ($whatsapp == "") ? "d-none" : ""; ?>">
                            <i class="bx bxl-whatsapp"></i> <?= __('whatsapp') ?>: 
                            <a href="https://api.whatsapp.com/send?phone=<?php echo $formatted_whatsapp; ?>" target="_blank">
                                <?php echo $whatsapp; ?>
                            </a>
                        </li>
                        <li class="<?php echo ($email == "") ? "d-none" : ""; ?>">
                            <i class="bx bxs-envelope"></i> <?= __('email') ?>: 
                            <a href="mailto:<?php echo $email; ?>">
                                <?php echo $email; ?>
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="location">
                        <div class="title-location mb-2">
                            <svg class="me-2" xmlns="http://www.w3.org/2000/svg" width="14.593" height="20.008" viewBox="0 0 14.593 20.008">
                                <g id="placeholder-for-map" transform="translate(0.5 0.5)">
                                    <path id="Path_6073" data-name="Path 6073" d="M80.353,0A6.8,6.8,0,0,0,73.323,6.8c0,4.347,4.172,7.5,6.511,12.04a.321.321,0,0,0,.57,0c2.115-4.083,5.731-6.821,6.4-10.754A6.893,6.893,0,0,0,80.353,0Zm-.235,10.35A3.559,3.559,0,1,1,83.677,6.8,3.559,3.559,0,0,1,80.118,10.354Z" transform="translate(-73.323 0)" fill="none" stroke="#838694" stroke-width="1.5"></path>
                                </g>
                            </svg>
                            <span class="fw-semibold me-1"><?= __('address') ?></span>
                            <a href="https://www.google.com/maps/place/<?php echo $endereco . ", " . $numero . " - " . $bairro . ", " . $cidade . " - " . $estado . ", " . $cep; ?>" target="_blank"><?= __('see_map') ?></a>
                        </div>
                        <p class="small lh-sm">
                            <?php echo $endereco . ", " . $numero . " - " . $bairro . ", " . $cidade . " - " . $estado . ", " . $cep; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="payment-seals mb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-9 seals">
                    <span class="titulo text-dark"><?= __('stamps') ?></span>
                    <ul>
                        <li>
                            <img loading="lazy" src="https://cdn.awsli.com.br/production/static/img/struct/stamp_encryptssl.png" alt="Site Seguro">
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div style="background-color: #fff; border-top: 1px solid #ddd; position: relative; z-index: 10; font-size: 11px; display: block !important; margin-bottom: 2rem;">
        <div class="container">
            <div class="row">
                <div class="col-md-9 col-12 text-center" style="min-height: 20px; width: 100%;">
                    <p style="margin-bottom: 0;">
                        <?php echo ($razao_social !== "") ? $razao_social : $loja; ?> - <?php echo ($razao_social !== "") ? "CNPJ: " : "CPF: "; ?> <?php echo $cpf_cnpj; ?> &#169; <?= __('all_rights_reserved') ?>. <?php echo date("Y") ?>
                    </p>
                    <a href="https://dropidigital.com.br" target="_blank">
                        <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo DropiDigital" style="width: 150px;">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <!-- Logo -->
    <script>
        function obterTamanhoDaTela() {
            // Obtém a largura da pagina
            var largura = window.innerWidth;

            // Obtém o elemento de imagem pelo ID
            var imagemElement = document.getElementById('logo');

            // Mobile Logo
            var mobileLogo = '<?php echo $logo_mobile; ?>';

            if (largura <= 768 && mobileLogo !== "") {
                imagemElement.src = '<?php echo INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $logo_mobile; ?>';
            }
            else
            {
                imagemElement.src = '<?php echo INCLUDE_PATH_DASHBOARD . 'back-end/logos/' . $shop_id . '/' . $logo; ?>';
            }
        }

        // Adiciona um ouvinte de evento para o redimensionamento da janela
        window.addEventListener("resize", obterTamanhoDaTela);

        // Chama a função para obter o tamanho da tela inicial
        obterTamanhoDaTela();
    </script>

    <!-- Mostrar menu categorias -->
    <script>
        $(document).ready(function() {
            // Variável para controlar o estado do botão
            let botaoAtivo = false;

            // Função para alternar a classe do botão e a classe "minha-classe"
            function alternarClasse() {
                botaoAtivo = !botaoAtivo; // Inverte o estado do botão
                $("#show-categories i").toggleClass("bx-menu bx-x"); // Alterna entre as classes
                $(".categories").toggleClass("nav-show-categories"); // Alterna a classe "minha-classe"
            }

            // Adiciona um ouvinte de evento de clique ao botão
            $("#show-categories").click(function() {
                alternarClasse();
            });
        });
    </script>

    <!-- Mostrar/Fechar modais mobile -->
    <script>
        $(document).ready(function() {
            // Mostrar ou fechar modal action mobile
            $('.navActionModalButton').on('click', function() {
                $('body').toggleClass('overflow-hidden');
                $('#actionContainer').toggleClass('showActionContainer');
                $('#actionModal').toggleClass('showActionModal');
                $('.navActionModalButton').toggleClass('active');

                // Fechar outros elementos
                $('#search').removeClass('showSearch');
                $('.navSearchButton').removeClass('active');
                $('#navCategories').removeClass('showCategories');
                $('.navCategoriesButton i').removeClass('bx-x').addClass('bx-menu');
            });

            // Mostrar ou fechar search mobile
            $('.navSearchButton').on('click', function() {
                $('#search').toggleClass('showSearch');
                $('.navSearchButton').toggleClass('active');

                // Fechar outros elementos
                $('body').removeClass('overflow-hidden');
                $('#actionContainer').removeClass('showActionContainer');
                $('#actionModal').removeClass('showActionModal');
                $('.navActionModalButton').removeClass('active');
                $('#navCategories').removeClass('showCategories');
                $('.navCategoriesButton i').removeClass('bx-x').addClass('bx-menu');
            });

            // Mostrar ou fechar menu categorias mobile
            $('.navCategoriesButton').on('click', function() {
                $('body').toggleClass('overflow-hidden');
                $('#navCategories').toggleClass('showCategories');
                $('.navCategoriesButton i').toggleClass('bx-menu bx-x');

                // Fechar outros elementos
                $('#search').removeClass('showSearch');
                $('.navSearchButton').removeClass('active');
                $('#actionContainer').removeClass('showActionContainer');
                $('#actionModal').removeClass('showActionModal');
                $('.navActionModalButton').removeClass('active');
            });
        });
    </script>

    <?php if (strpos($url, $substring_article) == false) { ?>
    <script>
        // Ative o carrossel
        var bannerCarousel = new bootstrap.Carousel(document.getElementById('bannerCarousel'), {
            interval: 3000, // Tempo de exibição de cada slide em milissegundos (opcional)
            wrap: true // Se o carrossel deve voltar ao primeiro slide após o último (opcional)
        });
    </script>
    <?php } ?>

    <!-- Responsive Carrossel -->
    <script>
        // Obtem o tamanho da imagem do produto  
        function obterTamanhoDaImagem() {
            // Obter a largura do elemento com id "meuDiv"
            var larguraDoDiv = $('.product-image').width();

            $('.product-image .card-img-top').css('height', larguraDoDiv);
        }

        // Função para verificar o tamanho da tela
        function verificarTamanhoDaTela() {
            // Obter a largura da tela
            var larguraDaTela = $(window).width();

            // Fazer algo com a largura, por exemplo, exibir um alerta
            if (larguraDaTela < 576) {
                $('.numBanner').removeClass('col-sm-3');
                $('.numBanner').removeClass('col-sm-4');
                $('.numBanner').removeClass('col-sm-6');

                $('.numBanner').addClass('col-sm-12');
            } else if (larguraDaTela < 992) {
                $('.numBanner').removeClass('col-sm-3');
                $('.numBanner').removeClass('col-sm-4');
                $('.numBanner').removeClass('col-sm-12');

                $('.numBanner').addClass('col-sm-6');
            } else if (larguraDaTela < 1024) {
                $('.numBanner').removeClass('col-sm-3');
                $('.numBanner').removeClass('col-sm-6');
                $('.numBanner').removeClass('col-sm-12');

                $('.numBanner').addClass('col-sm-4');
            } else {
                $('.numBanner').removeClass('col-sm-4');
                $('.numBanner').removeClass('col-sm-6');
                $('.numBanner').removeClass('col-sm-12');

                $('.numBanner').addClass('col-sm-3');
            }
        }

        // Chamar a função ao carregar a página
        $(document).ready(function() {
            obterTamanhoDaImagem();
            verificarTamanhoDaTela();
        });

        // Chamar a função ao redimensionar a tela
        $(window).resize(function() {
            obterTamanhoDaImagem();
            verificarTamanhoDaTela();
        });
    </script>

    <!-- Owl Carousel -->
    <!-- Inclua o script do Owl Carousel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <script>
        // Inicialize o carrossel
        $('#categoriesCarousel').owlCarousel({
            loop: false, // Loop infinito
            margin: 15, // Espaçamento entre os itens
            nav: true, // Navegação (setas)
            responsive: { // Configurações responsivas
                0: { // Quando a largura da tela for menor que 600px
                    items: 2 // Mostrar apenas 1 item por vez
                },
                600: { // Quando a largura da tela for igual ou maior que 600px
                    items: 2 // Mostrar 2 itens por vez
                },
                768: { // Quando a largura da tela for igual ou maior que 768px
                    items: 4 // Mostrar 3 itens por vez
                },
                1200: { // Quando a largura da tela for igual ou maior que 1200px
                    items: 6 // Mostrar 4 itens por vez
                }
            }
        });
    </script>

    <script>
        // Inicialize o carrossel
        $('#testimonyCarousel').owlCarousel({
            loop: true, // Loop infinito
            margin: 15, // Espaçamento entre os itens
            nav: true, // Navegação (setas)
            autoplay: true, // Ativar autoplay
            autoplayTimeout: 3000, // Tempo em milissegundos entre cada transição de slide
            responsive: { // Configurações responsivas
                0: { // Quando a largura da tela for menor que 768px
                    items: 2 // Mostrar apenas 2 item por vez
                },
                768: { // Quando a largura da tela for igual ou maior que 768px
                    items: 3 // Mostrar 3 itens por vez
                }
            }
        });
    </script>

    <!-- Tooltips -->
    <script>
        // Ative todos os tooltips na página
        $(document).ready(function(){
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>

    <!-- Nav Categorias -->
    <!-- <script>
        $(document).ready(function () {
            $('.categorias li').hover(
                function () {
                    $(this).find('.subcategorias').fadeIn(200);
                },
                function () {
                    $(this).find('.subcategorias').fadeOut(200);
                }
            );
        });
    </script> -->

    <!-- Feed Instagram -->
    <?php if (!empty($token_instagram)) { ?>

    <script type="text/javascript" src="js/instafeed.min.js"></script>

    <script src="js/jquery.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>

    <script type="text/javascript">
        var feed = new Instafeed({
            accessToken: "<?php echo $token_instagram; ?>",
            limit: 8,
            template: '<div class="item"><i class="bx bxl-instagram"></i><a href="{{link}}" target="_blank"><img title="{{caption}}" src="{{image}}" /></a></div>',
            after: function () {
                $('#instafeed').owlCarousel({
                    loop: true,
                    nav: true,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 1
                        },
                        600: {
                            items: 3
                        },
                        1000: {
                            items: 5
                        }
                    }
                });
            }
        });

        feed.run();

        // Função para obter o nome de usuário e exibi-lo
        function getUsername() {
            const accessToken = "<?php echo $token_instagram; ?>";
            const profileUrl = `https://graph.instagram.com/v12.0/me?fields=username&access_token=${accessToken}`;

            fetch(profileUrl)
                .then(response => response.json())
                .then(data => {
                    const username = data.username;
                    // Exiba o nome de usuário na div com o id "username"
                    document.getElementById('username').innerHTML = "<a href='https://www.instagram.com/" + username + "/' target='_blank'>@" + username + "</a>";
                })
        }

        // Chame a função para obter e exibir o nome de usuário
        getUsername();
    </script>

    <?php } ?>

    <!-- Ocultar categorias -->
    <script>
        const categories = document.querySelector(".categories");
        const showButton = document.querySelector("#show-categories");

        window.addEventListener("scroll", () => {
            if (window.pageYOffset > 100) {
                categories.classList.add("scroll");

                showButton.classList.remove("d-none");
                showButton.classList.add("d-flex");
            } else {
                categories.classList.remove("scroll");
                
                showButton.classList.remove("d-flex");
                showButton.classList.add("d-none");
            }
        });
    </script>

    <!-- Back to top -->
    <script>
        const toTop = document.querySelector(".to-top");

        window.addEventListener("scroll", () => {
            if (window.pageYOffset > 100) {
                toTop.classList.add("scroll");
            } else {
                toTop.classList.remove("scroll");
            }
        });
    </script>
</body>
</html>
