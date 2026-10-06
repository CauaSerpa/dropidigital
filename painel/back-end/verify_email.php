<?php
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

    session_start();
    ob_start();
    include_once('../../config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require './lib/vendor/autoload.php';
    $mail = new PHPMailer(true);

    function gerarCodigoUnico() {
        // Gera um ID único baseado no timestamp atual e mais alguma informação aleatória
        $codigoUnico = uniqid(mt_rand(), true);
    
        // Aplica uma função de hash (md5 neste exemplo) para obter uma string mais curta
        $codigoUnico = md5($codigoUnico);
    
        return $codigoUnico;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_users';

        // Recebe os dados do formulário
        $email = $_POST['email'];
        $user_id = $_POST['user_id'];

        // Gerando token para ativacao
        $token = gerarCodigoUnico();

        // Faça a validação dos campos, evitando SQL injection e outros ataques
        // Por exemplo, use a função filter_input() e hash para a senha:

        // Verifica se o usuário já existe
        $sql = "SELECT name, email, active_email FROM $tabela WHERE id = :id";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':id', $user_id);
        $stmt->execute();

        // Obter o resultado como um array associativo
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($_POST['email'] == "")
        {
            $email = $user['email'];
        } else {
            $email = $_POST['email'];
        }

        // Verifica se o email e igual e se ja foi ativo
        if ($user['email'] !== $email && $user['active_email'] == 0) {
            // Atualiza o email se for diferente do cadastrado
            //Tabela que será solicitada
            $tabela = 'tb_users';

            // Insere a categoria no banco de dados do endereco da loja
            $sql = "UPDATE $tabela SET email = :email WHERE id = :id";
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindValue(':email', $email);

            $stmt->bindValue(':id', $user_id);

            $stmt->execute();
        }

        if ($user['active_email'] == 0) {
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
                $mail->addAddress($email, $user['name']);

                $mail->isHTML(true); //Set email format to HTML
                $mail->Subject = 'Ativar e-mail';
                $mail->Body = "Olá " . $user['name'] . ", bem-vindo ao nosso site!</br>Termine o cadastro da sua loja na Dropi Digital!</br></br>Para continuar a ativação seu email <a href='" . INCLUDE_PATH_DASHBOARD . "ativar-email?token=" . $token . "'>Clique Aqui!</a>";
                $mail->AltBody = "Olá " . $user['name'] . ", bem-vindo ao nosso site!\nTermine o cadastro da sua loja na Dropi Digital!\n\nPara continuar a ativação seu email <a href='" . INCLUDE_PATH_DASHBOARD . "ativar-email?token=" . $token . "'>Clique Aqui!</a>";

                // Enviar o e-mail
                if ($mail->send()) {
                    //Tabela que será solicitada
                    $tabela = 'tb_users';

                    // Adiciona o token a tabela
                    $sql = "UPDATE $tabela SET token = :token WHERE id = :id";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindValue(':token', $token);

                    // Id que sera editado
                    $stmt->bindValue(':id', $user_id);

                    if ($stmt->execute()) {
                        $_SESSION['msgcad'] = "<p class='green'>E-mail de confirmação enviado com sucesso!</p>";

                        // Redireciona para a página de configuracoes e exibe uma mensagem de sucesso
                        header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
                    } else {
                        $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar o token</p>";

                        // Redireciona para a página de login ou exibe uma mensagem de sucesso
                        header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
                    }
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao enviar o e-mail: " . $mail->ErrorInfo . "</p>";

                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
                }
            } catch (Exception $e) {
                $_SESSION['msg'] = "<p class='red'>Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}</p>";
                header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
            }
        } else {
            $_SESSION['msg'] = "<p class='red'>Não foi possível enviar o E-mail</p>";

            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
        }
        
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro: Usuário não encontrado!</p>";
        header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
    }
    exit;