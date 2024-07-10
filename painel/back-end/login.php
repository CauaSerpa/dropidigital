<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    include_once('notice_login.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_users';

        // Recebe os dados do formulário
        $email = $_POST['email'];

        // Consulta SQL
        $sql = "SELECT id, permissions, name, email, password, two_factors FROM $tabela WHERE email = :email";

        // Preparar a consulta
        $stmt = $conn_pdo->prepare($sql);

        // Vincular o valor do parâmetro
        $stmt->bindValue(':email', $email);

        // Executar a consulta
        $stmt->execute();

        // Obter o resultado como um array associativo
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar se o resultado foi encontrado
        if ($resultado) {
            // Faz login normalmente
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($email === $resultado['email'] && password_verify($password, $resultado['password'])) {
                // Verifica se o usuario possui uma loja
                //Tabela que será solicitada
                $tabela = 'tb_shop';

                // Verifica se existe alguma loja com o id do usuario
                $sql = "SELECT id FROM $tabela WHERE user_id = :user_id";
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':user_id', $resultado['id']);
                $stmt->execute();

                if ($stmt->rowCount() == 0 && $resultado['permissions'] == 0) {
                    // Obtém o ID do novo usuário e passa pelo metodo session
                    $_SESSION['user_id_for_create_shop'] = $resultado['id'];
                    $_SESSION['email'] = $resultado['email'];

                    $_SESSION['msg'] = "Por favor termine a criação de sua loja para continuar";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "criar-loja");
                    exit;
                }

                if (empty($_SESSION['2fa']))
                {
                    $twoFactors = false;
                } else {
                    $twoFactors = $_SESSION['2fa'];
                }

                // Verifica se o two factors esta ativo e/ou o two factors ja foi feito
                if ($resultado['two_factors'] == 1 && $twoFactors == false) {
                    // Se estiver ativo envia codigo de verificacao
                    // Cria codigo e envia por email
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
                    $codigo_autenticacao = mt_rand(100000, 999999);

                    // Salvar os dados do usuário na sessão
                    $_SESSION['user_id_for_2fa'] = $resultado['id'];
                    $_SESSION['name'] = $resultado['name'];
                    $_SESSION['email'] = $resultado['email'];
                    $_SESSION['two_factors'] = $codigo_autenticacao;

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
                        $mail->Body = "Olá " . $row_usuario['nome'] . ",<br><br>Autenticação multifator.<br><br>Seu código de verificação de 6 dígitos é $codigo_autenticacao<br><br>Esse código foi enviado para verificar seu login.<br><br>";
                        $mail->AltBody = "Olá " . $row_usuario['nome'] . ",\n\nAutenticação multifator.\n\nSeu código de verificação de 6 dígitos é $codigo_autenticacao\n\nEsse código foi enviado para verificar seu login.\n\n";

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
                } else {
                    // Obtém o nome da conta
                    $name = $resultado['name'];

                    // Obtém o email da conta
                    $email = $resultado['email'];

                    // Configuração do fuso horário e Obtém a data e hora atual
                    date_default_timezone_set('America/Sao_Paulo');
                    $datetime = date('Y-m-d H:i:s');

                    // Obtém o endereço IP do usuário
                    $ip = $_SERVER['REMOTE_ADDR'];

                    // Obtém a string do agente do usuário
                    $browser = $_SERVER['HTTP_USER_AGENT'];

                    $two_factors = $resultado['two_factors'];

                    // Verificar se o IP já foi usado
                    $sql = "SELECT * FROM tb_login WHERE user_id = :user_id AND ip_address = :ip_address";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':user_id', $resultado['id']);
                    $stmt->bindParam(':ip_address', $ip);
                    $stmt->execute();

                    // Se o IP não foi encontrado, salvar no banco de dados
                    if ($stmt->rowCount() == 0) {
                        noticeLogin($name, $email, $datetime, $ip, $browser, $two_factors);
                    }

                    $sql = "INSERT INTO tb_login (user_id, ip_address, first_used_at) VALUES (:user_id, :ip_address, :first_used_at)";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':user_id', $resultado['id']);
                    $stmt->bindParam(':ip_address', $ip);
                    $stmt->bindParam(':first_used_at', $datetime);
                    $stmt->execute();

                    // Se estiver desativado entra no painel
                    $_SESSION['user_id'] = $resultado['id']; // Você pode definir informações do usuário aqui
                    header("Location: " . INCLUDE_PATH_DASHBOARD);
                    exit();
                }
            } else {
                $_SESSION['msg'] = "Credenciais inválidas.";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
            }
        } else {
            // ID não encontrado ou não existente
            $_SESSION['msg'] = "ID não encontrado.";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
        }
    } else {
        // ID não encontrado ou não existente
        $_SESSION['msg'] = "Informe seus dados de login no campo abaixo para continuar.";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "login");
    }