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
        //echo "Conexão com banco de dados realizado com sucesso!";
    }catch(PDOException $err){
        //echo "Erro: Conexão com banco de dados não realizado com sucesso. Erro gerado " . $err->getMessage();
    }

    define('INCLUDE_PATH', $_ENV['URL']);
    define('INCLUDE_PATH_DASHBOARD',INCLUDE_PATH.'painel/');

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
        if ($_SESSION['cargo'] == $permissao) {
            return;
        } else {
            echo 'style="display: none;"';
        }
    }

    function verificaPermissaoPagina($permissao) {
        if ($_SESSION['cargo'] == $permissao) {
            return;
        } else {
            include('pages/permissao-negada.php');
            die();
        }
    }
?>