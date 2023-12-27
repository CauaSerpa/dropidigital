<?php
    // Verifica se existe um codigo 2fa
    if (!isset($_SESSION['two_factors'])) {
        $_SESSION['msg'] = "Por favor gere um código para acessar essa página!";
        header('Location: ' . INCLUDE_PATH_DASHBOARD . 'login');
        exit();
    }
?>
<style>
    .code-inputs {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }

    .code-digit {
        width: 33.33%;
        padding: 12px 0;
        font-size: 1rem;
        text-align: center;
        margin: 0 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .code-digit:first-child
    {
        margin-right: 5px;
        margin-left: 0;
    }
    .code-digit:last-child
    {
        margin-left: 5px;
        margin-right: 0;
    }
</style>
<div class="box__container login">
    <nav class="nav">
        <a href="<?php echo INCLUDE_PATH; ?>" class="form__logo">
            <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo">
        </a>
    </nav>
    <form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/two_factors.php" method="POST" class="form business" name="form">
        <div class="container__title login">
            <h3 class="title">Autenticação de dois fatores</h3>
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
            <label for="email" class="labelInput">
                Por segurança, digite o código enviado para o seu e-mail:</br>
                <strong><?php echo $_SESSION['email']; ?></strong>
            </label>
            <div class="code-inputs">
                <input type="text" maxlength="1" class="code-digit" autofocus>
                <input type="text" maxlength="1" class="code-digit">
                <input type="text" maxlength="1" class="code-digit">
                <input type="text" maxlength="1" class="code-digit">
                <input type="text" maxlength="1" class="code-digit">
                <input type="text" maxlength="1" class="code-digit">
            </div>
            <div class="content__recup-password">
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/resend_code_twofactors.php" class="link">Reenviar um código para o e-mail</a>
            </div>
        </div>
        <input type="hidden" name="two_factors" id="twoFactors">
        <div class="container__button">
            <input type="submit" name="btnLogin" id="btnLogin" value="Confirmar" class="button button--flex submit login">
        </div>
    </form>
    <div class="bottom__text signup">
        <p>Ainda não possui uma loja na DropiDigital?</p>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar">Crie sua loja virtual grátis agora.</a>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const codeDigits = document.querySelectorAll('.code-digit');

    codeDigits.forEach((digitInput, index) => {
        digitInput.addEventListener('input', function() {
            if (this.value.length >= 1) {
                if (index < codeDigits.length - 1) {
                    codeDigits[index + 1].focus();
                } else {
                    var twoFactors = document.getElementById('twoFactors');

                    // Todos os dígitos foram inseridos
                    const code = Array.from(codeDigits).map(input => input.value).join('');
                    console.log('Código de autenticação:', code);
                    // Aqui você pode adicionar a lógica de verificação do código
                    twoFactors.value = code;
                }
            }
        });

        digitInput.addEventListener('keydown', function(event) {
            if (event.key === 'Backspace' && this.value.length === 0 && index > 0) {
                codeDigits[index - 1].focus();
            }
        });
    });

    // Adiciona o evento para preencher automaticamente ao colar
    document.addEventListener('paste', function(event) {
        const clipboardData = event.clipboardData || window.clipboardData;
        const pastedText = clipboardData.getData('text');

        if (/^\d{6}$/.test(pastedText)) {
            // Verifica se o texto colado é um código de 6 dígitos
            pastedText.split('').forEach((char, index) => {
                if (index < codeDigits.length) {
                    codeDigits[index].value = char;
                }
            });

            // Move o foco para o último input após colar
            codeDigits[codeDigits.length - 1].focus();

            // Atualiza o valor do campo oculto
            var twoFactors = document.getElementById('twoFactors');
            twoFactors.value = pastedText;
        }
    });
});
</script>