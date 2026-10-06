<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function noticeLogin($name, $email, $datetime, $ip, $browser, $two_factors) {
    // Caminho para o diretório pai
    $parentDir = dirname(dirname(__DIR__));

    // Carregar autoloaders do Composer
    require $parentDir . '/vendor/autoload.php';

    // Carregar variáveis de ambiente
    $dotenv = Dotenv\Dotenv::createImmutable($parentDir);
    $dotenv->load();

    // Informações para PHPMailer
    $smtp_host = $_ENV['SMTP_HOST'];
    $smtp_username = $_ENV['SMTP_USERNAME'];
    $smtp_password = $_ENV['SMTP_PASSWORD'];
    $smtp_secure = $_ENV['SMTP_SECURE'];
    $smtp_port = $_ENV['SMTP_PORT'];

    // Criar nova instância do PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Configurações do servidor
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_username;
        $mail->Password   = $smtp_password;
        $mail->SMTPSecure = $smtp_secure;
        $mail->Port       = $smtp_port;

        // Destinatários
        $mail->setFrom('no-reply@dropidigital.com.br', 'Não Responda');
        $mail->addAddress($email, $name);

        // Conteúdo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Sua conta Dropi Digital foi acessada a partir de um novo endereço IP';
        $mail->Body    = "Olá " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ",<br><br>Detectamos um login no seu painel da Dropi Digital a partir de um novo endereço IP:<br><br>
            ------------------------------------------<br>
            Email: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "<br>
            Data e horário: " . htmlspecialchars($datetime, ENT_QUOTES, 'UTF-8') . "<br>
            Endereço de IP: " . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . "<br>
            Navegador: " . htmlspecialchars($browser, ENT_QUOTES, 'UTF-8') . "<br>
            ------------------------------------------<br><br>Se foi você, não é necessário nenhuma ação.<br><br>Se não foi você por favor entre em contato com nosso suporte em <a href='mailto:suporte@dropidigital.com.br'>suporte@dropidigital.com.br</a>";
        $mail->AltBody = "Olá " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ",\n\nDetectamos um login no seu painel da Dropi Digital a partir de um novo endereço IP:\n\n
            ------------------------------------------\n
            Email: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\n
            Data e horário: " . htmlspecialchars($datetime, ENT_QUOTES, 'UTF-8') . "\n
            Endereço de IP: " . htmlspecialchars($ip, ENT_QUOTES, 'UTF-8') . "\n
            Navegador: " . htmlspecialchars($browser, ENT_QUOTES, 'UTF-8') . "\n
            ------------------------------------------\n\nSe foi você, não é necessário nenhuma ação.\n\nSe não foi você por favor entre em contato com nosso suporte em suporte@dropidigital.com.br";

        // Enviar o e-mail
        if ($mail->send()) {
            if ($two_factors == 1) {
                // Redirecionar após sucesso
                header("Location: " . INCLUDE_PATH_DASHBOARD . "dois-fatores");
                exit();
            }
        } else {
            $_SESSION['msgcad'] = 'Erro ao enviar o e-mail: ' . $mail->ErrorInfo;
            header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['msgcad'] = "<p class='red'>Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}</p>";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
        exit();
    }
}