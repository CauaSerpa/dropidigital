<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    include_once('notice_login.php');

    // Recuperando two_factors gerado
    if ($_SESSION['two_factors'] == $_POST['two_factors'])
    {
        unset($_SESSION['two_factors']);

        // Obtém o nome da conta
        $name = $_SESSION['name'];

        // Obtém o email da conta
        $email = $_SESSION['email'];

        // Configuração do fuso horário e Obtém a data e hora atual
        date_default_timezone_set('America/Sao_Paulo');
        $datetime = date('Y-m-d H:i:s');

        // Obtém o endereço IP do usuário
        $ip = $_SERVER['REMOTE_ADDR'];

        // Obtém a string do agente do usuário
        $browser = $_SERVER['HTTP_USER_AGENT'];

        // Verificar se o IP já foi usado
        $sql = "SELECT * FROM tb_login WHERE user_id = :user_id AND ip_address = :ip_address";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id_for_2fa']);
        $stmt->bindParam(':ip_address', $ip);
        $stmt->execute();

        // Se o IP não foi encontrado, salvar no banco de dados
        if ($stmt->rowCount() == 0) {
            noticeLogin($name, $email, $datetime, $ip, $browser);
        }

        $sql = "INSERT INTO tb_login (user_id, ip_address, first_used_at) VALUES (:user_id, :ip_address, :first_used_at)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':user_id', $_SESSION['user_id_for_2fa']);
        $stmt->bindParam(':ip_address', $ip);
        $stmt->bindParam(':first_used_at', $datetime);
        $stmt->execute();

        $_SESSION['2fa'] = true;
        $_SESSION['user_id'] = $_SESSION['user_id_for_2fa'];

        header("Location: ".INCLUDE_PATH_DASHBOARD);
    } else {
        $_SESSION['msgcad'] = 'Código inválido!';
        header("Location: ".INCLUDE_PATH_DASHBOARD."dois-fatores");
    }