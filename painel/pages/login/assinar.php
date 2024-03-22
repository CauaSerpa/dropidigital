<?php
    if(isset($_COOKIE['reLoginBusiness'])){
        $email = $_COOKIE['email'];
        $password = $_COOKIE['password'];
        $result_usuario = "SELECT * FROM `tb_users` WHERE email='$email' LIMIT 1";
        $resultado_usuario = mysqli_query($conn, $result_usuario);
        if($resultado_usuario){
            $row_usuario = mysqli_fetch_assoc($resultado_usuario);
            if(password_verify($password, $row_usuario['password'])){
                $_SESSION['id'] = $row_usuario['id'];
                $_SESSION['email'] = $row_usuario['email'];
                $_SESSION['phone'] = $row_usuario['phone'];
                $_SESSION['cargo'] = $row_usuario['cargo'];
                header("Location: ".INCLUDE_PATH_DASHBOARD);
            }
        }
    }
?>
<div class="box__container login">
    <nav class="nav">
        <a href="<?php echo INCLUDE_PATH; ?>" class="form__logo">
            <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo">
        </a>
    </nav>
    <form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/signup.php" method="POST" class="form business" id="form" name="form">
        <div class="container__title login">
            <h3 class="title">Crie seu Site Grátis</h3>
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
            <label for="name" class="labelInput">Nome Completo <span class="danger">*</span></label>
            <input type="text" name="name" id="name" class="inputUser" value="<?php echo (isset($_SESSION['name']) == '') ? '' : $_SESSION['name']; ?>">
            <span id="name-error" class="error-message"></span>
        </div>
        <div class="inputBox">
            <label for="email" class="labelInput">E-mail <span class="danger">*</span></label>
            <input type="email" name="email" id="email" class="inputUser <?php echo (isset($_SESSION['email-error']) == '') ? '' : 'input-error'; ?>" value="<?php echo (isset($_SESSION['email']) == '') ? '' : $_SESSION['email']; ?>">
            <span id="email-error" class="error-message">
				<?php
					if(isset($_SESSION['email-error'])){
						echo $_SESSION['email-error'];
						unset($_SESSION['email-error']);
					}
				?>
			</span>
        </div>
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
        <div class="container__button">
            <button type="submit" name="next" class="button button--flex submit next">Continuar</button>
        </div>
    </form>
    <div class="bottom__text signup">
        <p>Já possui um site na DropiDigital?</p>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>login">Acesse sua conta agora.</a>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('#email').on('blur', function() {
      const email = $(this).val();
      const emailField = $(this);

      if (email !== '') {
        $.ajax({
          url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/check_email.php', // Arquivo PHP para verificar o email
          method: 'POST',
          data: { email: email },
          success: function(response) {
            $('#email-error').html(response);

            if (response.includes('Email já cadastrado!')) {
              emailField.addClass('input-error'); // Adiciona classe em caso de erro
            } else {
              emailField.removeClass('input-error'); // Remove classe em caso de sucesso
            }
          }
        });
      }
    });
  });
</script>



<script>
	const form = document.getElementById('form');
	const nameInput = document.getElementById('name');
	const emailInput = document.getElementById('email');
	const passwordInput = document.getElementById('password');
	const confirmPasswordInput = document.getElementById('cPassword');
	
	const nameError = document.getElementById('name-error');
	const emailError = document.getElementById('email-error');
	const passwordError = document.getElementById('password-error');
	const confirmPasswordError = document.getElementById('confirm-password-error');

    passwordError.textContent = '';
    confirmPasswordError.textContent = '';

    nameInput.addEventListener('input', function() {
      	const nameField = $(this);

		nameError.textContent = '';

		if (!validateName(nameInput.value)) {
			nameError.textContent = 'Preencha o campo Nome!';
			nameField.addClass('input-error'); // Adiciona classe em caso de erro
		} else {
			nameField.removeClass('input-error'); // Remove classe em caso de sucesso
		}
	});

	function validateName(name) {
		return name.length > 0;
	}

	emailInput.addEventListener('input', function() {
		const emailField = $(this);

		emailError.textContent = '';

		if (!validateEmail(emailInput.value)) {
			emailError.textContent = 'Preencha o campo E-mail!';
			emailField.addClass('input-error'); // Adiciona classe em caso de erro
		} else {
			emailField.removeClass('input-error'); // Remove classe em caso de sucesso
		}
	});

	function validateEmail(email) {
		return email.length > 0;
	}

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
		} else {
			passwordField.removeClass('input-error'); // Remove classe em caso de sucesso
		}

		if (password !== confirmPassword) {
			confirmPasswordError.textContent = 'As senhas não coincidem.';
			confirmPasswordField.addClass('input-error'); // Adiciona classe em caso de erro
		} else {
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