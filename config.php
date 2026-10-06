<?php
    // Caso prefira o .env apenas descomente o codigo e comente o "include('parameters.php');" acima
	// Carrega as variáveis de ambiente do arquivo .env

    // Caminho para o diretório pai
    $parentDir = __DIR__;

	require $parentDir . '/vendor/autoload.php';
	$dotenv = Dotenv\Dotenv::createImmutable($parentDir);
	$dotenv->load();

	// Acessa as variáveis de ambiente
	$dbHost = $_ENV['DB_HOST'];
	$dbUsername = $_ENV['DB_USERNAME'];
	$dbPassword = $_ENV['DB_PASSWORD'];
	$dbName = $_ENV['DB_NAME'];
	$port = $_ENV['DB_PORT'];

    try{
        //Conexão com a porta
        $conn_pdo = new PDO("mysql:host=$dbHost;port=$port;dbname=" . $dbName, $dbUsername, $dbPassword);

        //Conexão sem a porta
        //$conn = new PDO("mysql:host=$host;dbname=" . $dbname, $user, $pass);
        // echo "Conexão com banco de dados realizado com sucesso!";
    }catch(PDOException $err){
        // echo "Erro: Conexão com banco de dados não realizado com sucesso. Erro gerado " . $err->getMessage();
    }

    // Timezone
    date_default_timezone_set('America/Sao_Paulo');

    // URL's
    // Sistema
    define('INCLUDE_PATH', $_ENV['URL']);
    define('INCLUDE_PATH_DASHBOARD',INCLUDE_PATH.'painel/');

    // Marketplace
    define('INCLUDE_PATH_MARKETPLACE', $_ENV['URL_MARKETPLACE']);

    // Google AdSense
    define('ADSENSE_CLIENT', $_ENV['ADSENSE_CLIENT'] ?? '');
    define('ADSENSE_ARTICLE_TOP_SLOT', $_ENV['ADSENSE_ARTICLE_TOP_SLOT'] ?? '');
    define('ADSENSE_ARTICLE_BOTTOM_SLOT', $_ENV['ADSENSE_ARTICLE_BOTTOM_SLOT'] ?? '');

    // Google Analytics
    define('GOOGLE_OAUTH_CLIENT_ID', $_ENV['GOOGLE_OAUTH_CLIENT_ID'] ?? '');
    define('GOOGLE_OAUTH_CLIENT_SECRET', $_ENV['GOOGLE_OAUTH_CLIENT_SECRET'] ?? '');
    define('GOOGLE_OAUTH_REDIRECT_URI', $_ENV['GOOGLE_OAUTH_REDIRECT_URI'] ?? '');
    define('GOOGLE_ANALYTICS_SCOPE', 'https://www.googleapis.com/auth/analytics.readonly');

    define('REMEMBER_LOGIN_COOKIE', 'dropi_remember_login');
    define('REMEMBER_LOGIN_DAYS', 30);

    function setRememberLoginCookie(PDO $conn_pdo, int $userId): void {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + (REMEMBER_LOGIN_DAYS * 86400));

        $stmt = $conn_pdo->prepare(
            'UPDATE tb_users SET remember_token_hash = :token_hash, remember_token_expires_at = :expires_at WHERE id = :user_id'
        );
        $stmt->execute([
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt,
            ':user_id' => $userId
        ]);

        setcookie(REMEMBER_LOGIN_COOKIE, $userId . ':' . $token, [
            'expires' => time() + (REMEMBER_LOGIN_DAYS * 86400),
            'path' => '/painel/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    function clearRememberLoginCookie(PDO $conn_pdo): void {
        if (!empty($_COOKIE[REMEMBER_LOGIN_COOKIE])) {
            $cookieParts = explode(':', $_COOKIE[REMEMBER_LOGIN_COOKIE], 2);
            if (count($cookieParts) === 2 && ctype_digit($cookieParts[0])) {
                $stmt = $conn_pdo->prepare(
                    'UPDATE tb_users SET remember_token_hash = NULL, remember_token_expires_at = NULL WHERE id = :user_id'
                );
                $stmt->execute([':user_id' => (int) $cookieParts[0]]);
            }
        }

        setcookie(REMEMBER_LOGIN_COOKIE, '', [
            'expires' => time() - 3600,
            'path' => '/painel/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    function restoreRememberedLogin(PDO $conn_pdo): void {
        if (isset($_SESSION['user_id']) || empty($_COOKIE[REMEMBER_LOGIN_COOKIE])) {
            return;
        }

        $cookieParts = explode(':', $_COOKIE[REMEMBER_LOGIN_COOKIE], 2);
        if (count($cookieParts) !== 2 || !ctype_digit($cookieParts[0]) || !ctype_xdigit($cookieParts[1]) || strlen($cookieParts[1]) !== 64) {
            clearRememberLoginCookie($conn_pdo);
            return;
        }

        $stmt = $conn_pdo->prepare(
            'SELECT id FROM tb_users WHERE id = :user_id AND remember_token_hash = :token_hash AND remember_token_expires_at > NOW() LIMIT 1'
        );
        $stmt->execute([
            ':user_id' => (int) $cookieParts[0],
            ':token_hash' => hash('sha256', $cookieParts[1])
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['user_id'] = (int) $user['id'];
        } else {
            clearRememberLoginCookie($conn_pdo);
        }
    }

    // Imagens
    define('CDN_BASE_URL', $_ENV['CDN_BASE_URL']);

    // Limites para produtos
    define('MAX_PRODUCT_IMAGES', (int) ($_ENV['MAX_PRODUCT_IMAGES'] ?? 5));
    define('MAX_IMAGE_SIZE', ((int) ($_ENV['MAX_IMAGE_SIZE_MB'] ?? 3)) * 1024 * 1024);

    // Tiny key
    $tinyKey = $_ENV['TINY_API_KEY'];

    // Asaas
	$asaas_url = $_ENV['ASAAS_API_URL'];
	$asaas_key = $_ENV['ASAAS_API_KEY'];

    //Pega cargo
    function pegaCargo($cargo) {
        $arr = [
            '0' => 'Pessoa',
            '1' => 'Empresa',
            '2' => 'Administrador'
        ];

        return $arr[$cargo];
    }

    //Funcao '.active' Sidebar
    function activeSidebarLink($par) {
        $url = explode('/',@$_GET['url'])[0];
        if ($url == $par)
        {
            echo 'active';
        }
    }

    //Funcao '.showMenu' Sidebar
    function showSidebarLinks($par) {
        $url = explode('/',@$_GET['url'])[0];
        if ($url == $par)
        {
            echo 'showMenu';
        }
    }

    function verificaPermissaoMenu($permissao) {
        if ($permissao == 1) {
            return;
        } else {
            echo 'style="display: none;"';
        }
    }

    function verificaPermissaoPagina($permissao) {
        if ($permissao == 1) {
            return;
        } else {
            include('pages/admin/permissao-negada.php');
            die();
        }
    }
?>