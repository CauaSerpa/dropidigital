<?php
    if (isset($_GET["token"])) {
        $token = $_GET['token'];

        // Tabela que sera feita a consulta
        $tabela = "tb_users";

        $query_usuario = "SELECT id FROM $tabela WHERE recup_password = :recup_password LIMIT 1";
        $result_usuario = $conn_pdo->prepare($query_usuario);
        $result_usuario->bindParam(':recup_password', $token, PDO::PARAM_STR);
        $result_usuario->execute();

        if (!$result_usuario->fetch(PDO::FETCH_ASSOC)) {
            $_SESSION['msg'] = "Erro: Link inválido, solicite novo link para atualizar a senha!";
            header("Location: " . INCLUDE_PATH_DASHBOARD . "recuperar-senha");
            exit;
        }
    } else {
        $_SESSION['msg'] = "Erro: Link inválido, solicite novo link para atualizar a senha!";
        header("Location: " . INCLUDE_PATH_DASHBOARD . "recuperar-senha");
        exit;
    }
?>
<div class="box__container login">
    <nav class="nav">
        <a href="<?php echo INCLUDE_PATH; ?>" class="form__logo">
            <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo">
        </a>
    </nav>
    <form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/update_password.php" method="POST" class="form business" id="form" name="form">
        <div class="container__title login">
            <h3 class="title">Atualizar Senha</h3>
        </div>
        <p class="success-message">
            <?php
                if(isset($_SESSION['msgcad'])){
                    echo $_SESSION['msgcad'];
                    unset($_SESSION['msgcad']);
                    echo "<br><br>";
                }
            ?>
        </p>
        <p class="error-message">
            <?php
                if(isset($_SESSION['msg'])){
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                    echo "<br><br>";
                }
            ?>
        </p>
        <div class="inputBox">
            <label for="password" class="labelInput">Senha <span class="danger">*</span></label>
            <input type="password" name="password" id="password" class="inputUser">
            <button type="button" class="button__show">
                <i class='bx bx-show' id="icon"></i>
            </button>
            <span id="password-error" class="error-message"></span>
        </div>
        <div class="inputBox">
            <label for="cPassword" class="labelInput">Confirmar Senha <span class="danger">*</span></label>
            <input type="password" name="cPassword" id="cPassword" class="inputUser">
            <button type="button" class="button__show">
                <i class='bx bx-show' id="icon"></i>
            </button>
            <span id="confirm-password-error" class="error-message"></span>
        </div>
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <div class="container__button">
            <button type="submit" name="next" class="button button--flex submit next">Continuar</button>
        </div>
    </form>
    <div class="bottom__text signup">
        <p>Lembrou?</p>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>login">Acesse sua conta agora.</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Password -->
<script>
	const form = document.getElementById('form');
	const passwordInput = document.getElementById('password');
	const confirmPasswordInput = document.getElementById('cPassword');
	
	const passwordError = document.getElementById('password-error');
	const confirmPasswordError = document.getElementById('confirm-password-error');

    passwordError.textContent = '';
    confirmPasswordError.textContent = '';

	passwordInput.addEventListener('input', function() {
		const passwordField = $(this);

		passwordError.textContent = '';

		if (!validatePassword(passwordInput.value)) {
			passwordError.textContent = 'Senha inválida. Deve conter pelo menos 8 caracteres.';
			passwordField.addClass('input-error'); // Adiciona classe em caso de erro
		} else {
			passwordField.removeClass('input-error'); // Remove classe em caso de sucesso
		}
	});

	function validatePassword(password) {
		return password.length >= 8;
	}
	
	passwordInput.addEventListener('input', validatePassword);
	confirmPasswordInput.addEventListener('input', validatePassword);

	function validatePassword() {
		const password = passwordInput.value;
		const confirmPassword = confirmPasswordInput.value;

		const passwordField = $('#password');
		const confirmPasswordField = $('#cPassword');

		passwordError.textContent = '';
		confirmPasswordError.textContent = '';

		if (password.length < 8) {
			passwordError.textContent = 'A senha deve ter pelo menos 8 caracteres.';
			passwordField.addClass('input-error'); // Adiciona classe em caso de erro
		} else if (!containsUpperCase(password)) {
			passwordError.textContent = 'A senha deve conter pelo menos uma letra maiúscula.';
			passwordField.addClass('input-error'); // Adiciona classe em caso de erro
		} else if (!containsLowerCase(password)) {
			passwordError.textContent = 'A senha deve conter pelo menos uma letra minúscula.';
			passwordField.addClass('input-error'); // Adiciona classe em caso de erro
		} else if (!containsNumber(password)) {
			passwordError.textContent = 'A senha deve conter pelo menos um número.';
			passwordField.addClass('input-error'); // Adiciona classe em caso de erro
		} else if (password !== confirmPassword) {
			confirmPasswordError.textContent = 'As senhas não coincidem.';
			confirmPasswordField.addClass('input-error'); // Adiciona classe em caso de erro
		} else {
			passwordField.removeClass('input-error'); // Remove classe em caso de sucesso
			confirmPasswordField.removeClass('input-error'); // Remove classe em caso de sucesso
		}
	}

	function containsUpperCase(str) {
		return /[A-Z]/.test(str);
	}

	function containsLowerCase(str) {
		return /[a-z]/.test(str);
	}

	function containsNumber(str) {
		return /\d/.test(str);
	}
</script>

<script>
    $(document).ready(function() {
        $('.button__show').on('click', function() {
            var passwordInput = $(this).prev('.inputUser');
            var icon = $(this).find('.bx');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('bx-show');
                icon.addClass('bx-hide');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('bx-hide');
                icon.addClass('bx-show');
            }
        });
    });
</script>