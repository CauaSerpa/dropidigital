<?php
    session_start();
    ob_start();
    include_once('../../../config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Caminho para o diretório pai
    $parentDir = dirname(dirname(dirname(__DIR__)));

    require $parentDir . '/vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable($parentDir);
    $dotenv->load();

    // Informacoes para PHPMailer
    $smtp_host = $_ENV['SMTP_HOST'];
    $smtp_username = $_ENV['SMTP_USERNAME'];
    $smtp_password = $_ENV['SMTP_PASSWORD'];
    $smtp_secure = $_ENV['SMTP_SECURE'];
    $smtp_port = $_ENV['SMTP_PORT'];

    require '../lib/vendor/autoload.php';

    // Crie uma nova instância do PHPMailer
    $mail = new PHPMailer(true);

    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //var_dump($dados);
        // Informacoes da instituicao

        // Tabela que sera feita a consulta
        $tabela = "tb_users";

        $sql = "SELECT id, name, email 
                    FROM $tabela 
                    WHERE id =:id  
                    LIMIT 1";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $dados['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() != 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $token = password_hash($user['id'], PASSWORD_DEFAULT);

            $email = $dados['email'];
            //echo "Chave $token <br>";

            $query_up_usuario = "UPDATE $tabela 
                        SET email = :email, recup_password = :recup_password 
                        WHERE id = :id 
                        LIMIT 1";
            $result_up_usuario = $conn_pdo->prepare($query_up_usuario);
            $result_up_usuario->bindParam(':email', $email, PDO::PARAM_STR);
            $result_up_usuario->bindParam(':recup_password', $token, PDO::PARAM_STR);
            $result_up_usuario->bindParam(':id', $user['id'], PDO::PARAM_INT);

            if ($result_up_usuario->execute()) {
                $mail = new PHPMailer(true);

                $link = INCLUDE_PATH_DASHBOARD . "atualizar-senha?token=$token";

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

                    $mail->setFrom('no-reply@dropidigital.com.br', 'Não responda');
                    $mail->addReplyTo('no-reply@dropidigital.com.br', 'Não responda');
                    $mail->addAddress($dados['email'], $user['name']);

                    $mail->isHTML(true); //Set email format to HTML
                    $mail->Subject = 'Recuperar senha';
                    $mail->Body    = 'Prezado(a) ' . $user['name'] .".<br><br>Você solicitou alteração de senha.<br><br>Para continuar o processo de recuperação de sua senha, clique no link abaixo ou cole o endereço no seu navegador: <br><br><a href='" . $link . "'>Clique Aqui Para Recuperar Sua Senha</a><br><br>Ou<br><br>Cole esse link no seu navegador:<br><p>" . $link . "</p><br><br>Se você não solicitou essa alteração, nenhuma ação é necessária. Sua senha permanecerá a mesma até que você ative este código.<br><br>";
                    $mail->AltBody = 'Prezado(a) ' . $user['name'] ."\n\nVocê solicitou alteração de senha.\n\nPara continuar o processo de recuperação de sua senha, clique no link abaixo ou cole o endereço no seu navegador: \n\n" . $link . "\n\nOu\n\nCole esse link no seu navegador:\n" . $link . "\n\nSe você não solicitou essa alteração, nenhuma ação é necessária. Sua senha permanecerá a mesma até que você ative este código.\n\n";

                    $mail->send();

                    $_SESSION['msgcad'] = "<p class='green'>E-mail enviado com sucesso!</p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $dados['shop_id']);
                } catch (Exception $e) {
                    $_SESSION['msg'] = "<p class='red'>Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}</p>";
                    header("Location: " . INCLUDE_PATH_DASHBOARD . "ver-loja?id=" . $dados['shop_id']);
                }
            } else {
                $_SESSION['msg'] = "<p class='red'>Erro: Usuário não encontrado!</p>";
                header("Location: " . INCLUDE_PATH_DASHBOARD . "lojas");
            }
        }
    }