<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Caminho para o diretório pai
    $parentDir = dirname(dirname(__DIR__));

    require $parentDir . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($parentDir);
    $dotenv->load();

    // Informacoes para PHPMailer
    $smtp_host = $_ENV['SMTP_HOST'];
    $smtp_username = $_ENV['SMTP_USERNAME'];
    $smtp_password = $_ENV['SMTP_PASSWORD'];
    $smtp_secure = $_ENV['SMTP_SECURE'];
    $smtp_port = $_ENV['SMTP_PORT'];

    require './lib/vendor/autoload.php';
    $mail = new PHPMailer(true);

    // Gerar número randômico entre 100000 e 999999
    $twofactors_code = mt_rand(100000, 999999);

    // Passa o codigo por session
    $_SESSION['two_factors'] = $twofactors_code;

    // Consulta SQL
    $tabela = 'tb_users';
    $sql = "SELECT name, email FROM $tabela WHERE email = :email";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':email', $_SESSION['email']);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    // Verificar se o resultado foi encontrado
    if ($resultado) {
        $name = $resultado['name'];
        $email = $resultado['email'];
    } else {
        $_SESSION['msgcad'] = "Nenhum usuário encontrado!";
        header("Location: ".INCLUDE_PATH_DASHBOARD."login");
    }

    try {
        /*$mail->SMTPDebug = SMTP::DEBUG_SERVER;*/
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port       = $smtp_port;

        $mail->setFrom('no-reply@dropidigital.com.br', 'Não Responda');
        $mail->addAddress($email, $name);

        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = 'Bem-vindo a Dropi Digital';
        $mail->Body = "Olá " . $name . ", Autenticação multifator.<br><br>Seu novo código de verificação de 6 dígitos é $twofactors_code<br><br>Esse código foi enviado para verificar seu login.<br><br>";
        $mail->AltBody = "Olá " . $name . ", Autenticação multifator.\n\nSeu novo código de verificação de 6 dígitos é $twofactors_code\n\nEsse código foi enviado para verificar seu login.\n\n";

        // Enviar o e-mail
        if ($mail->send()) {
            $_SESSION['msgcad'] = 'E-mail reenviado com sucesso!';
            
            // Redireciona para a página de dois fatores ou exibe uma mensagem de sucesso
            header("Location: ".INCLUDE_PATH_DASHBOARD."dois-fatores");
        } else {
            $_SESSION['msg'] = 'Erro ao enviar o e-mail: ' . $mail->ErrorInfo;

            // Redireciona para a página de login ou exibe uma mensagem de erro
            header("Location: ".INCLUDE_PATH_DASHBOARD."login");
        }
    } catch (Exception $e) {
        $_SESSION['msg'] = "<p class='red'>Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}</p>";
        header("Location: ".INCLUDE_PATH_DASHBOARD."login");
    }