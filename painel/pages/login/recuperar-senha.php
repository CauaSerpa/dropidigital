<div class="box__container login">
    <nav class="nav">
        <a href="<?php echo INCLUDE_PATH; ?>" class="form__logo">
            <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo">
        </a>
    </nav>
    <form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/reset_password.php" method="POST" class="form business" name="form">
        <div class="container__title login">
            <h3 class="title">Esqueceu a senha?</h3>
            <p>Digite seu e-mail abaixo para recuperar sua senha.</p>
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
            <label for="email" class="labelInput">E-mail</label>
            <input type="text" name="email" id="email" class="inputUser" required>
        </div>
        <div class="container__button">
            <input type="submit" name="btnLogin" id="btnLogin" value="Recuperar Senha" class="button button--flex submit login">
        </div>
    </form>
    <div class="bottom__text signup">
        <p>Lembrou?</p>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>login">Acesse sua conta agora.</a>
    </div>
</div>