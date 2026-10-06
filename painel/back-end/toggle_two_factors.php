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
        $active2fa = $_POST['active2fa'];
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

        if ($user['active_email'] == 1)
        {
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
                $mail->addAddress($user['email'], $user['name']);
    
                $mail->isHTML(true);
                $mail->Subject = ($active2fa == 1) ? 'Ativar autenticação dois fatores' : 'Desativar autenticação dois fatores';
                $mail->Body = "Olá " . $user['name'] . ", bem-vindo ao nosso site!</br>" . 
                              (($active2fa == 1) ? 'Ative a autenticação de dois fatores aqui!' : 'Desative a autenticação de dois fatores aqui!') . 
                              "</br></br>Para continuar <a href='" . INCLUDE_PATH_DASHBOARD . "alterar-dois-fatores?token=" . $token . "'>Clique Aqui!</a>";
                $mail->AltBody = "Olá " . $user['name'] . ", bem-vindo ao nosso site!\n" . 
                                (($active2fa == 1) ? 'Ative a autenticação de dois fatores aqui!' : 'Desative a autenticação de dois fatores aqui!') . 
                                "\n\nPara continuar <a href='" . INCLUDE_PATH_DASHBOARD . "alterar-dois-fatores?token=" . $token . "'>Clique Aqui!</a>";

                if ($mail->send()) {
                    $tabela = 'tb_users';

                    $sql = "UPDATE $tabela SET two_factors_token = :two_factors_token, two_factors = :two_factors WHERE id = :id";
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindValue(':two_factors_token', $token);
                    $stmt->bindValue(':two_factors', ($active2fa == 1) ? 0 : 1);
                    $stmt->bindValue(':id', $user_id);

                    if ($stmt->execute()) {
                        $_SESSION['msgcad'] = "<p class='green'>E-mail para " . (($active2fa == 1) ? 'ativação' : 'desativação') . " enviado com sucesso!</p>";
                        header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
                    } else {
                        $_SESSION['msg'] = "<p class='red'>Erro ao cadastrar o token</p>";
                        header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
                    }
                } else {
                    $_SESSION['msg'] = "<p class='red'>Erro ao enviar o e-mail: " . $mail->ErrorInfo . "</p>";
                    header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
                }
            } catch (Exception $e) {
                $_SESSION['msg'] = "<p class='red'>Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}</p>";
                header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
            }
        } else {
            $_SESSION['msg'] = "<p class='red'>Erro: É necessário ativar o e-mail!</p>";
            header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
        }
    } else {
        $_SESSION['msg'] = "<p class='red'>Erro: Usuário não encontrado!</p>";
        header("Location: ".INCLUDE_PATH_DASHBOARD."configuracoes/seguranca");
    }
    exit;