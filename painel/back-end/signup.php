<?php
    session_start();
    ob_start();
    include_once('../config.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require './lib/vendor/autoload.php';
    $mail = new PHPMailer(true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Tabela que será solicitada
        $tabela = 'tb_users';

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
                $mail->Host       = 'smtp.mailtrap.io';
                $mail->SMTPAuth   = true;
                $mail->Username   = '8b6afa6cf7c2eb';
                $mail->Password   = '8a525ea217cae2';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 2525;

                $mail->setFrom('no-reply@dropidigital.com.br', 'Não Responda');
                $mail->addAddress($email, $name);

                $mail->isHTML(true); //Set email format to HTML
                $mail->Subject = 'Bem-vindo a Dropi Digital';
                $mail->Body = "Olá " . $name . ", bem-vindo ao nosso site!</br>Termine o cadastro da sua loja na Dropi Digital!</br></br>Para continuar a criação da sua loja <a href='" . INCLUDE_PATH_DASHBOARD . "criar-loja'>Clique Aqui!</a>";
                $mail->AltBody = "Olá " . $name . ", bem-vindo ao nosso site!\nTermine o cadastro da sua loja na Dropi Digital!\n\nPara continuar a criação da sua loja <a href='" . INCLUDE_PATH_DASHBOARD . "criar-loja'>Clique Aqui!</a>";

                // Enviar o e-mail
                if ($mail->send()) {
                    // Obtém o ID do novo usuário e passa pelo metodo session
                    $_SESSION['user_id'] = $conn_pdo->lastInsertId();
            
                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: ".INCLUDE_PATH_DASHBOARD."criar-loja");
                } else {
                    $_SESSION['msgcad'] = 'Erro ao enviar o e-mail: ' . $mail->ErrorInfo;

                    // Redireciona para a página de login ou exibe uma mensagem de sucesso
                    header("Location: ".INCLUDE_PATH_DASHBOARD."assinar");
                }
            } catch (Exception $e) {
                $_SESSION['msgcad'] = "<p class='red'>Erro: E-mail não enviado sucesso. Mailer Error: {$mail->ErrorInfo}</p>";
                header("Location: ".INCLUDE_PATH_DASHBOARD."login/");
            }
        } else {
            $_SESSION['msgcad'] = "<p class='red'>Erro: Tente novamente!</p>";
            header("Location: ".INCLUDE_PATH_DASHBOARD."login/");
        }
    } else {
        $_SESSION['msgcad'] = "<p class='red'>Erro: Usuário não encontrado!</p>";
        header("Location: ".INCLUDE_PATH_DASHBOARD."login/");
    }
    exit;