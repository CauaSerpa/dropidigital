<?php
    function noticeLogin($name, $email, $datetime, $ip, $browser) {
        // Envia um email para avisar que foi feito um login na conta do usuario
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
            $mail->Subject = 'Sua conta Dropi Digital foi acessada a partir de um novo endereço IP';
            $mail->Body = "Olá " . $name . ",<br><br>Detectamos um login no seu painel da Dropi Digital a partir de um novo endereço IP:<br><br>
            ------------------------------------------<br>
            Email: $email<br>
            Data e horário: $datetime<br>
            Endereço de IP: $ip<br>
            Navegador: $browser<br>
            ------------------------------------------<br><br>Se foi você, não é necessário nenhuma ação.<br><br>Se não foi você por favor entre em contato com nosso suporte em <a href='mailto:suporte@dropidigital.com.br'>suporte@dropidigital.com.br</a>";
            $mail->AltBody = "Olá " . $name . ",\n\nDetectamos um login no seu painel da Dropi Digital a partir de um novo endereço IP:\n\n
            ------------------------------------------\n
            Email: $email\n
            Endereço de IP: $ip\n
            Navegador: $browser\n
            ------------------------------------------\n\nSe foi você, não é necessário nenhuma ação.\n\nSe não foi você por favor entre em contato com nosso suporte em <a href='mailto:suporte@dropidigital.com.br'>suporte@dropidigital.com.br</a>";

            // Enviar o e-mail
            if ($mail->send()) {
                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: ".INCLUDE_PATH_DASHBOARD."dois-fatores");
            } else {
                $_SESSION['msgcad'] = 'Erro ao enviar o e-mail: ' . $mail->ErrorInfo;

                // Redireciona para a página de login ou exibe uma mensagem de sucesso
                header("Location: ".INCLUDE_PATH_DASHBOARD."login");
            }
        } catch (Exception $e) {
            $_SESSION['msgcad'] = "<p class='red'>Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}</p>";
            header("Location: ".INCLUDE_PATH_DASHBOARD."login");
        }
    }