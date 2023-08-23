<?php
    // Se ja estiver logado redirecionar para o painel
    // if (isset($_SESSION['user_id'])) {
    //     header("Location: " . INCLUDE_PATH_DASHBOARD);
    //     exit();
    // }
?>
<div class="box__container login">
    <nav class="nav">
        <a href="<?php echo INCLUDE_PATH; ?>" class="form__logo">
            <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo">
        </a>
    </nav>
    <form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/login.php" method="POST" class="form business" name="form">
        <div class="container__title login">
            <h3 class="title">Bem vindo de volta!</h3>
        </div>
        <p class="error-message">
            <?php
                if(isset($_SESSION['msg'])){
                    echo $_SESSION['msg'];
                    unset($_SESSION['msg']);
                    echo "<br><br>";
                }
                if(isset($_SESSION['msgcad'])){
                    echo $_SESSION['msgcad'];
                    unset($_SESSION['msgcad']);
                    echo "<br><br>";
                }
            ?>
        </p>
        <div class="inputBox">
            <label for="email" class="labelInput">E-mail</label>
            <input type="text" name="email" id="email" class="inputUser" required>
        </div>
        <div class="inputBox password">
            <label for="password" class="labelInput">Senha</label>
            <input type="password" name="password" id="password" class="inputUser" autocomplete="off" required>
            <button type="button" class="button__show">
                <i class='bx bx-show' id="icon" onclick="showHide()"></i>
            </button>
            <div class="content__recup-password">
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>login/recuperar-senha" class="link">Esqueci minha senha</a>
            </div>
        </div>
        <div class="container__button">
            <input type="submit" name="btnLogin" id="btnLogin" value="Entrar" class="button button--flex submit login">
        </div>
        <div class="line">
            <span class="or">ou</span>
        </div>
        <button class="google-login">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 186.69 190.5" xmlns:v="https://vecta.io/nano"><g transform="translate(1184.583 765.171)"><path clip-path="none" mask="none" d="M-1089.333-687.239v36.888h51.262c-2.251 11.863-9.006 21.908-19.137 28.662l30.913 23.986c18.011-16.625 28.402-41.044 28.402-70.052 0-6.754-.606-13.249-1.732-19.483z" fill="#4285f4"/><path clip-path="none" mask="none" d="M-1142.714-651.791l-6.972 5.337-24.679 19.223h0c15.673 31.086 47.796 52.561 85.03 52.561 25.717 0 47.278-8.486 63.038-23.033l-30.913-23.986c-8.486 5.715-19.31 9.179-32.125 9.179-24.765 0-45.806-16.712-53.34-39.226z" fill="#34a853"/><path clip-path="none" mask="none" d="M-1174.365-712.61c-6.494 12.815-10.217 27.276-10.217 42.689s3.723 29.874 10.217 42.689c0 .086 31.693-24.592 31.693-24.592-1.905-5.715-3.031-11.776-3.031-18.098s1.126-12.383 3.031-18.098z" fill="#fbbc05"/><path d="M-1089.333-727.244c14.028 0 26.497 4.849 36.455 14.201l27.276-27.276c-16.539-15.413-38.013-24.852-63.731-24.852-37.234 0-69.359 21.388-85.032 52.561l31.692 24.592c7.533-22.514 28.575-39.226 53.34-39.226z" fill="#ea4335" clip-path="none" mask="none"/></g></svg>
            <span>Entrar com o Google</span>
        </button>
    </form>
    <div class="bottom__text signup">
        <p>Ainda não possui uma loja na DropiDigital?</p>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar">Crie sua loja virtual grátis agora.</a>
    </div>
</div>