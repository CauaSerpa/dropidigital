<?php
    $shop_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    if (!empty($shop_id)) {
        // Nome da tabela para a busca
        $tabela = 'tb_shop';

        $sql = "SELECT * FROM $tabela WHERE id = :id";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $shop_id);
        $stmt->execute();

        // Obter o resultado como um array associativo
        $shop = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($shop) {

            // Nome da tabela para a busca
            $tabela = 'tb_users';
    
            $sql = "SELECT * FROM $tabela WHERE id = :id";
    
            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':id', $shop['user_id']);
            $stmt->execute();
    
            // Recuperar os resultados
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Nome da tabela para a busca
            $tabela = 'tb_address';
    
            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id";
    
            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':shop_id', $shop['id']);
            $stmt->execute();
    
            // Obter o resultado como um array associativo
            $address = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Nome da tabela para a busca
            $tabela = 'tb_subscriptions';
    
            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id ASC LIMIT 1";
    
            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':shop_id', $shop['id']);
            $stmt->execute();
    
            // Obter o resultado como um array associativo
            $subs = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Nome da tabela para a busca
            $tabela = 'tb_login';
    
            $sql = "SELECT * FROM $tabela WHERE user_id = :user_id ORDER BY id ASC LIMIT 1";
    
            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user['id']);
            $stmt->execute();
    
            // Obter o resultado como um array associativo
            $login = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Saber o nome do plano
            // Nome da tabela para a busca
            $tabelaInterval = 'tb_plans_interval';
            $tabelaPlans = 'tb_plans';
    
            $sql = "SELECT p.name
                    FROM $tabelaInterval i
                    JOIN $tabelaPlans p ON i.plan_id = p.id
                    WHERE i.id = :id
                    ORDER BY i.id DESC";
    
            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':id', $shop['plan_id']);
            $stmt->execute();
    
            // Recuperar os resultados
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($plan) {
                $plan = $plan['name'];
            }
?>

<style>
    /* Tab nav */
    .nav-underline .nav-link.active,
    .nav-underline .show>.nav-link
    {
        font-weight: 500 !important;
        color: var(--bs-nav-underline-link-active-color) !important;
    }
    .nav-link
    {
        color: var(--text-color-light) !important;
    }
    .nav-link:focus, .nav-link:hover
    {
        color: var(--bs-body-color) !important;
    }

    /* Placeholder email */
    .form-control#email::placeholder
    {
        color: var(--bs-body-color);
        opacity: 1; /* Firefox */
    }

    .form-control#email::-ms-input-placeholder /* Edge 12 -18 */
    {
        color: var(--bs-body-color);
    }

    /* Password */
    #editPassword .is-invalid
    {
        background-image: none !important;
    }
    .toggle-password
    {
        position: absolute;
        right: 0;
        top: 0;
        height: 38px;
        color: var(--bs-body-color) !important;
        background: transparent !important;
        border: none;
    }

    /* Btn */
    .btn.btn-success
    {
        background: var(--green-color) !important;
        border: none !important;
    }
    .btn.btn-success:hover
    {
        background: var(--dark-green-color) !important;
        border: none !important;
    }
    
    .btn.btn-secondary
    {
        background: var(--fourth-color) !important;
        border: none !important;
    }
    .btn.btn-secondary:hover
    {
        background: #989898 !important;
        border: none !important;
    }
</style>

<div class="modal fade" id="export" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<form id="deleteShop" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/delete_shop.php" method="post">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Deletar Loja
                </div>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="py-3">
                    <p class="fs-5 mb-2">Você tem certeza de que deseja excluir a loja? Esta ação é irreversível e não poderá ser desfeita!</p>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="password" class="form-label small">Para confirmar a exclusão, por favor, insira sua senha de administrador abaixo.</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" name="password" id="password" aria-describedby="passwordHelp">
                                <button type="button" class="btn toggle-password" data-target="#password">
                                    <i class='bx bx-show-alt' ></i>
                                </button>
                                <small id="password-error" class="invalid-feedback"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
            <div class="modal-footer fw-semibold px-4">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger border-danger d-flex align-items-center fw-semibold px-4 py-2 small disabled" disabled>
                    <i class='bx bxs-trash me-2' ></i>
                    Deletar
                </a>
            </div>
        </div>
    </div>
</form>
</div>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>lojas" class="fs-5 text-decoration-none text-reset">Lojas</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page"><?php echo $shop['name']; ?></li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button d-flex align-items-center">
            <div class="container__button">
                <?php
                    if (strpos($link, "https://") === 0) {
                        $shop_url = $link;
                    } else {
                        $dominio_completo = $_SERVER['HTTP_HOST'];

                        // Remove o protocolo (http:// ou https://) se presente
                        $dominio = preg_replace('#^https?://#', '', $dominio_completo);

                        $shop_url = "https://$link.$dominio";
                    }
                ?>
                <a href="<?php echo $shop_url; ?>" class="button button--flex new text-decoration-none d-flex align-items-center">
                    Ver Loja
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" class="ms-1" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3z"></path><path d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z"></path></svg>
                </a>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-underline">
    <li class="nav-item">
        <a class="nav-link <?php echo ($tab == "" || $tab == "loja") ? "active" : ""; ?>" id="loja" onclick="changeTab('loja')" data-bs-toggle="tab" data-bs-target="#shop-tab-pane" type="button" role="tab" aria-controls="shop-tab-pane" aria-selected="<?php echo ($tab == "" || $tab == "loja") ? "true" : "false"; ?>">Dados da Loja</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($tab == "perfil") ? "active" : ""; ?>" id="perfil" onclick="changeTab('perfil')" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="<?php echo ($tab == "perfil") ? "true" : "false"; ?>">Perfil</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($tab == "tema") ? "active" : ""; ?>" id="tema" onclick="changeTab('tema')" data-bs-toggle="tab" data-bs-target="#theme-tab-pane" type="button" role="tab" aria-controls="theme-tab-pane" aria-selected="<?php echo ($tab == "tema") ? "true" : "false"; ?>">Tema</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($tab == "seguranca") ? "active" : ""; ?>" id="seguranca" onclick="changeTab('seguranca')" data-bs-toggle="tab" data-bs-target="#security-tab-pane" type="button" role="tab" aria-controls="security-tab-pane" aria-selected="<?php echo ($tab == "seguranca") ? "true" : "false"; ?>">Segurança</a>
    </li>
</ul>

<div class="tab-content" id="myTabContent">

<div class="tab-pane fade <?php echo ($tab == "" || $tab == "loja") ? "show active" : ""; ?>" id="shop-tab-pane" role="tabpanel" aria-labelledby="shop-tab" tabindex="0">
    <div class="row">
        <div class="col-md-6">
            <div class="card p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
                <div class="card-body row px-4 py-3">
                    <ul class="mb-0">
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">ID:</small>
                            <small><?php echo $shop['id']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Nome da Loja:</small>
                            <small><?php echo $shop['name']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">E-mail:</small>
                            <small><?php echo $user['email']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Responsável:</small>
                            <small><?php echo $user['name']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Número do documento:</small>
                            <small><?php echo $shop['cpf_cnpj']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Plano atual:</small>
                            <small><?php echo $plan; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Vencimento:</small>
                            <small><?php echo date("d/m/Y", strtotime($subs['due_date'])); ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Endereço:</small>
                            <small><?php echo $address['endereco'] . ", " . $address['numero'] . " - " . $address['bairro'] . ", " . $address['cidade'] . " - " . $address['estado'] . ", " . $address['cep']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">IP:</small>
                            <small><?php echo $login['ip_address']; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Engressou em:</small>
                            <small><?php echo date("d/m/Y H:i:s", strtotime($user['date_create'])); ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Último login:</small>
                            <small><?php echo date("d/m/Y H:i:s", strtotime($login['first_used_at'])); ?></small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Informações básicas</div>
                <div class="card-body row px-4 py-3">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Acessar</h6>
                            <small>Clique em acessar para acessar a loja</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/admin/access_shop.php?id=<?php echo $user['id']; ?>" class="btn btn-success fw-semibold px-4 py-2 small">Acessar</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <form id="resetPassword" action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/admin/reset_password.php" method="post">
                            <div class="mb-2">
                                <h6 class="fs-6 fw-semibold mb-0">Redefinir senha</h6>
                                <p class="small lh-sm mb-2">Coloque o e-mail que será enviado o código de redefinição de senha para alterar a senha da loja e o usuário responsável</p>
                                <input type="text" class="form-control" name="email" id="email" aria-describedby="emailHelp">
                            </div>

                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">

                            <button type="submit" name="SendEmail" class="btn btn-success fw-semibold px-4 py-2 small">Enviar E-mail</button>
                        </form>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Copiar Loja</h6>
                            <small>Clique em copiar para fazer uma cópia da loja atual em outra desejada</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-secondary d-flex align-items-center fw-semibold px-4 py-2 small">
                                <i class='bx bxs-copy me-2'></i>
                                Copiar
                            </a>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Enviar Aviso</h6>
                            <small>Clique em aviso para enviar um aviso para a loja.</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-warning text-white d-flex align-items-center fw-semibold px-4 py-2 small">
                                <i class='bx bx-error-circle me-2' ></i>
                                Aviso
                            </a>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Deletar Loja</h6>
                            <small>Clique em deletar para apagar a loja. Não é possível restaurar a loja!</small>
                        </div>
                        <button class="d-flex align-items-center border-0" data-bs-toggle="modal" data-bs-target="#export">
                            <a href="#" class="btn btn-danger d-flex align-items-center fw-semibold px-4 py-2 small">
                                <i class='bx bxs-trash me-2' ></i>
                                Deletar
                            </a>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tab-pane fade <?php echo ($tab == "perfil") ? "show active" : ""; ?>" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
<form id="editProfile" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_profile.php" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3 p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Dados Pessoais</div>
                <div class="card-body row px-4 py-3">
                    
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">

</form>
</div>

<div class="tab-pane fade <?php echo ($tab == "tema") ? "show active" : ""; ?>" id="theme-tab-pane" role="tabpanel" aria-labelledby="theme-tab" tabindex="0">
<form id="editTheme" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_theme.php" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3 p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Tema</div>
                <div class="card-body row px-4 py-3">
                    <div>
                        <label for="active2fa" class="form-label small">Ativar tema escuro?</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activeTheme" role="switch" id="activeTheme" value="1">
                            <label class="form-check-label" id="textTheme" for="activeTheme">Não</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
</form>
</div>

<div class="tab-pane fade <?php echo ($tab == "seguranca") ? "show active" : ""; ?>" id="security-tab-pane" role="tabpanel" aria-labelledby="security-tab" tabindex="0">
<form id="editPassword" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_password.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Alterar Senha</div>
        <div class="card-body row px-4 py-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password" class="form-label small">Senha atual</label>
                    <div class="position-relative">
                        <input type="password" class="form-control" name="currentPassword" id="password" aria-describedby="passwordHelp">
                        <button type="button" class="btn toggle-password" data-target="#password">
                            <i class='bx bx-show-alt' ></i>
                        </button>
                        <small id="password-error" class="invalid-feedback"></small>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="newPassword" class="form-label small">Nova senha</label>
                    <div class="position-relative">
                        <input type="password" class="form-control" name="newPassword" id="newPassword" aria-describedby="passwordHelp">
                        <button type="button" class="btn toggle-password" data-target="#newPassword">
                            <i class='bx bx-show-alt' ></i>
                        </button>
                        <small id="new-password-error" class="invalid-feedback"></small>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirmNewPassword" class="form-label small">Confirmar nova senha</label>
                    <div class="position-relative">
                        <input type="password" class="form-control" name="confirmNewPassword" id="confirmNewPassword" aria-describedby="passwordHelp">
                        <button type="button" class="btn toggle-password" data-target="#confirmNewPassword">
                            <i class='bx bx-show-alt' ></i>
                        </button>
                        <small id="confirm-new-password-error" class="invalid-feedback"></small>
                    </div>
                </div>

                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
    
                <button type="submit" name="buttonUpdPassword" id="buttonUpdPassword" class="btn btn-success fw-semibold px-4 py-2 small disabled">Salvar</button>
            </div>
        </div>
    </div>
</form>

<form id="toggleTwoFactors" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/toggle_two_factors.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Autenticação de dois fatores</div>
        <div class="card-body row px-4 py-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="activeTheme" class="form-label small">Ativar autenticação de dois fatores?</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="active2fa" role="switch" id="active2fa" value="1" <?php echo ($user['two_factors'] == 1) ? "checked" : ""; ?> <?php echo ($user['active_email'] !== 1) ? "disabled" : ""; ?>>
                        <label class="form-check-label" id="text2fa" for="active2fa"><?php echo ($user['two_factors'] == 1) ? "Sim" : "Não"; ?></label>
                    </div>
                    <small class="<?php echo ($user['active_email'] == 1) ? "d-none" : ""; ?>">É necessário verificar seu e-mail antes! <a href="#" class="link">Reenviar E-mail</a></small>
                </div>

                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                <button type="submit" name="SendEmail" class="btn btn-success fw-semibold px-4 py-2 small <?php echo ($user['active_email'] !== 1) ? "disabled" : ""; ?>">Salvar</button>
            </div>
        </div>
    </div>
</form>
</div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- Passsword -->
<!-- <script>
    const form = document.getElementById('editPassword');
    const passwordInput = document.getElementById('password');
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmNewPassword');
    const button = document.getElementById('buttonUpdPassword');

    const passwordError = document.getElementById('password-error');
    const newPasswordError = document.getElementById('new-password-error');
    const confirmPasswordError = document.getElementById('confirm-new-password-error');

    passwordError.textContent = '';
    newPasswordError.textContent = '';
    confirmPasswordError.textContent = '';

    passwordInput.addEventListener('input', function() {
        const passwordField = $(this);

        passwordInput.textContent = '';

        if (passwordInput.value == '') {
            passwordError.textContent = 'Por favor insira sua senha atual.';
            passwordField.addClass('is-invalid');
        } else {
            passwordField.removeClass('is-invalid');
        }
    });

    newPasswordInput.addEventListener('input', function() {
        const newPasswordField = $(this);

        newPasswordError.textContent = '';

        if (!validatePassword(newPasswordInput.value)) {
            newPasswordField.addClass('is-invalid');
        } else {
            newPasswordField.removeClass('is-invalid');
        }
    });

    confirmPasswordInput.addEventListener('input', validatePassword);

    function validatePassword() {
        const password = passwordInput.value;
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        const passwordField = $('#password');
        const newPasswordField = $('#newPassword');
        const confirmPasswordField = $('#confirmNewPassword');
        const button = $('#buttonUpdPassword');

        passwordError.textContent = '';
        newPasswordError.textContent = '';
        confirmPasswordError.textContent = '';

        if (newPassword.length < 7) {
            newPasswordError.textContent = 'A senha deve ter pelo menos 8 caracteres.';
            newPasswordField.addClass('is-invalid');
            button.addClass('disabled');
        } else if (!containsUpperCase(newPassword)) {
            newPasswordError.textContent = 'A senha deve conter pelo menos uma letra maiúscula.';
            newPasswordField.addClass('is-invalid');
            button.addClass('disabled');
        } else if (!containsLowerCase(newPassword)) {
            newPasswordError.textContent = 'A senha deve conter pelo menos uma letra minúscula.';
            newPasswordField.addClass('is-invalid');
            button.addClass('disabled');
        } else if (!containsNumber(newPassword)) {
            newPasswordError.textContent = 'A senha deve conter pelo menos um número.';
            newPasswordField.addClass('is-invalid');
            button.addClass('disabled');
        } else if (password === "") {
            passwordError.textContent = 'Por favor insira sua senha atual.';
            passwordField.addClass('is-invalid');
            button.addClass('disabled');
        } else if (newPassword !== confirmPassword) {
            confirmPasswordError.textContent = 'As senhas não coincidem.';
            confirmPasswordField.addClass('is-invalid');
            button.addClass('disabled');
        } else {
            newPasswordField.removeClass('is-invalid');
            confirmPasswordField.removeClass('is-invalid');
            button.removeClass('disabled');
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
</script> -->

<script>
    $(document).ready(function () {
        // Seleciona o input de senha
        var passwordInput = $('#password');

        // Seleciona o botão de deletar
        var deleteButton = $('.btn-danger');

        // Adiciona um ouvinte de evento de input ao input de senha
        passwordInput.on('input', function () {
            // Verifica se a senha tem pelo menos 8 caracteres
            if ($(this).val().length >= 8) {
                // Remove o atributo 'disabled' do botão de deletar
                deleteButton.removeAttr('disabled');
                deleteButton.removeClass('disabled');
            } else {
                // Adiciona o atributo 'disabled' ao botão de deletar
                deleteButton.attr('disabled', 'disabled');
                deleteButton.addClass('disabled');
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('.toggle-password').click(function () {
            const targetId = $(this).data('target');
            const passwordInput = $(targetId);
            const eyeIcon = $(this).find('i');

            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);

            // Alterna entre as classes do ícone
            eyeIcon.toggleClass('bx-show-alt bx-hide');
        });
    });
</script>

<!-- Funcao para alterar url conforme a tab selecionada -->
<script>
    function changeTab(tab) {
        // Obtenha a parte da URL após 'configuracoes/'
        var path = window.location.pathname.split('configuracoes/')[1];

        // Altere o perfil na URL para a guia clicada
        var newUrl = window.location.origin + '/dropidigital/app/painel/configuracoes/' + tab;
        history.pushState(null, null, newUrl);

        // Exemplo de lógica para alterar 'aria-selected'
        const allTabs = document.querySelectorAll('.nav-link');
        allTabs.forEach((tabElement) => {
            const isSelected = tabElement.getAttribute('aria-selected') === 'true';
            tabElement.setAttribute('aria-selected', tabElement.id === tab ? 'true' : 'false');

            // Exemplo de lógica para alterar classes
            if (tabElement.id === tab) {
                tabElement.classList.add('active');
            } else {
                tabElement.classList.remove('active');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Adicione um ouvinte de evento a cada link
        const allTabs = document.querySelectorAll('.nav-link');
        allTabs.forEach((tabElement) => {
            tabElement.addEventListener('click', function (event) {
                event.preventDefault();
                const tab = tabElement.getAttribute('id');
                changeTab(tab);
            });
        });
    });
</script>

<?php
        } else {
            $_SESSION['msg'] = "<p class='red'>Loja não encontrada!</p>";
            // Redireciona para a página de login ou exibe uma mensagem de sucesso
            header("Location: " . INCLUDE_PATH_DASHBOARD . "lojas");
        }
    } else {
        $_SESSION['msg'] = "<p class='red'>É necessário selecionar um produto!</p>";
        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: " . INCLUDE_PATH_DASHBOARD . "lojas");
    }
?>