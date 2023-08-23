<?php
    $dbHost = 'Localhost';
    $dbUsername = 'root';
    $dbPassword = '';
    $dbName = 'habilide';
    $port = 3306;

    $conn = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);

    try{
        //Conexão com a porta
        $conn_pdo = new PDO("mysql:host=$dbHost;port=$port;dbname=" . $dbName, $dbUsername, $dbPassword);

        //Conexão sem a porta
        //$conn = new PDO("mysql:host=$host;dbname=" . $dbname, $user, $pass);
        //echo "Conexão com banco de dados realizado com sucesso!";
    }catch(PDOException $err){
        //echo "Erro: Conexão com banco de dados não realizado com sucesso. Erro gerado " . $err->getMessage();
    }

    define('INCLUDE_PATH','http://localhost/Habilide/landing-page/');
    define('INCLUDE_PATH_DASHBOARD',INCLUDE_PATH.'dashboard/');
    
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
            echo 'class="active"';
        }
    }

    //Funcao '.bx => bxs' Icon
    function activeSidebarIcon($par) {
        $url = explode('/',@$_GET['url'])[0];
        if ($url == $par)
        {
            echo 'bxs';
        }
        else
        {
            echo 'bx';
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