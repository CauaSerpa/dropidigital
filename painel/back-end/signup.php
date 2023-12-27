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

        // Gerando token para ativacao
        $token = gerarCodigoUnico();

        // Recebe os dados do formulário
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Obtem a data e hora atual
        $current_date = date('Y-m-d H:i:s');

        // Faça a validação dos campos, evitando SQL injection e outros ataques
        // Por exemplo, use a função filter_input() e hash para a senha:

        // Verifica se o usuário já existe
        $sql = "SELECT id FROM $tabela WHERE email = :email";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            //Mensagem de erro
            $_SESSION['email-error'] = "O E-mail já está sendo utilizado. Escolha outro E-mail.";

            //Passar os inputs ja informados novamente
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            
            //Link de redirecionamento
            $redirect_url = INCLUDE_PATH_DASHBOARD . 'assinar';
            header('Location: ' . $redirect_url);

            //Mata o processo
            die();
        }

        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (name, email, password, date_create, last_login) VALUES (:name, :email, :password, :date_create, :last_login)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', password_hash($password, PASSWORD_BCRYPT));
        $stmt->bindValue(':date_create', $current_date);
        $stmt->bindValue(':last_login', $current_date);

        if ($stmt->execute()) {
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
                $mail->Subject = 'Ativar e-mail';
                $mail->Body = "Olá " . $name . ", bem-vindo ao nosso site!</br>Termine o cadastro da sua loja na Dropi Digital!</br></br>Para continuar a ativação seu email <a href='" . INCLUDE_PATH_DASHBOARD . "ativar-email?token=" . $token . "'>Clique Aqui!</a>";
                $mail->AltBody = "Olá " . $name . ", bem-vindo ao nosso site!\nTermine o cadastro da sua loja na Dropi Digital!\n\nPara continuar a ativação seu email <a href='" . INCLUDE_PATH_DASHBOARD . "ativar-email?token=" . $token . "'>Clique Aqui!</a>";
    
                // Enviar o e-mail
                if ($mail->send()) {
                    // Obtém o ID do novo usuário e passa pelo metodo session
                    $_SESSION['user_id'] = $conn_pdo->lastInsertId();
                    $_SESSION['email'] = $email;
                    
                    // Cadastra token de ativacao na tabela do usuario
                    $user_id = $conn_pdo->lastInsertId();

                    //Tabela que será solicitada
                    $tabela = 'tb_users';

                    // Adiciona o token a tabela
                    $sql = "UPDATE $tabela SET token = :token WHERE id = :id";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindValue(':token', $token);

                    // Id que sera editado
                    $stmt->bindValue(':id', $user_id);

                    $stmt->execute();

                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: ".INCLUDE_PATH_DASHBOARD."criar-loja");
                } else {
                    $_SESSION['msgcad'] = 'Erro ao enviar o e-mail: ' . $mail->ErrorInfo;

                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: ".INCLUDE_PATH_DASHBOARD."assinar");
                }
            } catch (Exception $e) {
                $_SESSION['msgcad'] = "<p class='red'>Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}</p>";
                header("Location: ".INCLUDE_PATH_DASHBOARD."login");
            }
        } else {
            $_SESSION['msgcad'] = "<p class='red'>Erro: Tente novamente!</p>";
            header("Location: ".INCLUDE_PATH_DASHBOARD."login");
        }
    } else {
        $_SESSION['msgcad'] = "<p class='red'>Erro: Usuário não encontrado!</p>";
        header("Location: ".INCLUDE_PATH_DASHBOARD."login");
    }
    exit;