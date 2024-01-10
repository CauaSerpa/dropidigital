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

    /* Copy shop */
    #copyShop div.modal-body div
    {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #copyShop div.modal-body div.icon-content
    {
        flex-direction: column;
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

    .domain
    {
        text-decoration: none;
    }
    .domain:hover
    {
        text-decoration: underline;
    }

    /* Copy */
    #copyDomain,
    #copySubdomain
    {
        cursor: pointer;
    }

    /* Linha */
    .line
    {
        width: 100%;
        height: 1px;
        background: var(--bs-card-border-color);
    }

    /* Bullet */
    .bullet
    {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .bullet.success
    {
        background: rgb(1, 200, 155);
    }
    .bullet.warning
    {
        background: rgb(251, 188, 5);
    }
    .bullet.danger
    {
        background: rgb(229, 15, 56);
    }
</style>

<div class="modal fade" id="copyShop" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<form id="copyShopForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/delete_shop.php" method="post">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Copiar Loja
                </div>
            </div>
            <div class="modal-body row px-4 py-3">
                <div class="icon-content col-md-5 py-3">
                    <img class="mb-3" src="<?php echo INCLUDE_PATH_DASHBOARD; ?>assets/images/shop-copy.svg" alt="Ícone copiar loja">
                    <h5><?php echo $shop['cpf_cnpj']; ?></h5>
                </div>
                <div class="icon-content col-md-2 py-3">
                    <i class='bx bx-chevron-right fs-2' ></i>
                </div>
                <div class="icon-content col-md-5 py-3">
                    <img class="mb-3" src="<?php echo INCLUDE_PATH_DASHBOARD; ?>assets/images/shop-paste.svg" alt="Ícone colar loja">
                    <input type="text" class="form-control" name="docNumber" id="docNumber" maxlength="18" placeholder="CPF ou CNPJ da loja desejada" aria-describedby="docNumberHelp">
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
            <div class="modal-footer fw-semibold px-4">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success border-danger d-flex align-items-center fw-semibold px-4 py-2 small disabled" id="copyButton" disabled>
                    <i class='bx bxs-copy me-2'></i>
                    Copiar
                </button>
            </div>
        </div>
    </div>
</form>
</div>

<div class="modal fade" id="deleteShop" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<form id="deleteShopForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/delete_shop.php" method="post">
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
                <button type="submit" class="btn btn-danger border-danger d-flex align-items-center fw-semibold px-4 py-2 small disabled" id="deleteButton" disabled>
                    <i class='bx bxs-trash me-2' ></i>
                    Deletar
                </button>
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
        <a class="nav-link <?php echo ($tab == "dominio") ? "active" : ""; ?>" id="dominio" onclick="changeTab('dominio')" data-bs-toggle="tab" data-bs-target="#domain-tab-pane" type="button" role="tab" aria-controls="domain-tab-pane" aria-selected="<?php echo ($tab == "dominio") ? "true" : "false"; ?>">Domínio</a>
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
                        <button class="d-flex align-items-center border-0" data-bs-toggle="modal" data-bs-target="#copyShop">
                            <a href="#" class="btn btn-secondary d-flex align-items-center fw-semibold px-4 py-2 small">
                                <i class='bx bxs-copy me-2'></i>
                                Copiar
                            </a>
                        </button>
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
                        <button class="d-flex align-items-center border-0" data-bs-toggle="modal" data-bs-target="#deleteShop">
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

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_domains';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain != :domain";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop['id']);
    $stmt->bindValue(':domain', "dropidigital.com.br");
    $stmt->execute();

    // Recuperar os resultados
    $domain = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($domain) {
        $subdomain = ($domain['subdomain'] !== "www") ? $domain['subdomain'] . "." : "";
        $domain_url = $subdomain . $domain['domain'];
    }
?>

<div class="tab-pane fade <?php echo ($tab == "dominio") ? "show active" : ""; ?>" id="domain-tab-pane" role="tabpanel" aria-labelledby="domain-tab" tabindex="0">
<form id="editProfile" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_profile.php" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3 p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Domínio</div>
                <div class="card-body row px-4 py-3">
                    <div class="d-flex align-items-center mb-3">
                        <a href="<?php echo "https://" . $domain_url; ?>" target="_black" class="domain d-inline-flex align-items-center fs-5 fw-semibold" style="color: var(--bs-body-color);"><?php echo $domain_url; ?></a>
                        <i class='bx bxs-copy fs-4 ms-1' id="copyDomain"></i>
                    </div>
                    <small class="fw-semibold">Certificado SSL</small>
                    <small class="d-flex align-items-center mb-3">
                        <?php
                            if (empty($domain)) {
                                $status = "Desativado";
                                $bullet = "danger";
                            } else if ($domain['configure'] == 0) {
                                $status = "Desativado";
                                $bullet = "danger";
                            } else if ($domain['configure'] == 1) {
                                if ($domain['status'] == 0) {
                                    $status = "Em andamento";
                                    $bullet = "warning";
                                } else {
                                    $status = "Ativo";
                                    $bullet = "success";
                                }
                            }
                        ?>
                        <div class="bullet <?php echo $bullet; ?> me-2"></div>

                        SHA-256 bits
                    </small>

                    <?php
                        $registerDate = $domain['register_date'];
                        $registerDate = !empty($registerDate) ? date("d/m/Y H:i:s", strtotime($registerDate)) : "Em andamento";

                        $configureDate = $domain['configure_date'];
                        $configureDate = !empty($configureDate) ? date("d/m/Y H:i:s", strtotime($configureDate)) : "Em andamento";

                        $activeDate = $domain['active_date'];
                        $activeDate = !empty($activeDate) ? date("d/m/Y H:i:s", strtotime($activeDate)) : "Em andamento";
                    ?>

                    <div>
                        <ul class="mb-0">
                            <li class="small"><span class="fw-semibold">Status:</span> <div class="bullet d-inline-flex <?php echo $bullet; ?> me-2"></div><?php echo $status; ?></li>
                            <li class="small"><span class="fw-semibold">Data de Registro:</span> <?php echo $registerDate; ?></li>
                            <li class="small"><span class="fw-semibold">Data de Configuração:</span> <?php echo $configureDate; ?></li>
                            <li class="small"><span class="fw-semibold">Data de Ativação:</span> <?php echo $activeDate; ?></li>
                        </ul>
                    </div>
                    
                    <div class="line my-3"></div>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_domains';

    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain = :domain";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop['id']);
    $stmt->bindValue(':domain', "dropidigital.com.br");
    $stmt->execute();

    // Recuperar os resultados
    $domain = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($domain) {
        $subdomain = ($domain['subdomain'] !== "www") ? $domain['subdomain'] . "." : "";
        $subdomain_url = $subdomain . $domain['domain'];
    }
?>

                    <div class="d-flex align-items-center mb-3">
                        <a href="<?php echo "https://" . $subdomain_url; ?>" target="_black" class="domain d-inline-flex align-items-center fs-5 fw-semibold" style="color: var(--bs-body-color);"><?php echo $subdomain_url; ?></a>
                        <i class='bx bxs-copy fs-4 ms-1' id="copySubdomain"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>

    <input type="hidden" id="domain" value="<?php echo $domain_url; ?>">
    <input type="hidden" id="subdomain" value="<?php echo $subdomain_url; ?>">

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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<!-- Formatação de input -->
<script>
    $(document).ready(function () {
        $('#docNumber').on('input', function () {
            var inputValue = $(this).val().replace(/\D/g, ''); // Remove caracteres não numéricos

            if (inputValue.length <= 11) {
                // Se o comprimento for menor ou igual a 11, trata como CPF
                $(this).val(formatarCPF(inputValue));
            } else {
                // Se o comprimento for maior que 11, trata como CNPJ
                $(this).val(formatarCNPJ(inputValue));
            }
        });

        function formatarCPF(cpf) {
            return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        }

        function formatarCNPJ(cnpj) {
            return cnpj.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
        }
    });
</script>

<script>
    $(document).ready(function () {
        // Seleciona o input de senha
        var docNumberInput = $('#docNumber');

        // Seleciona o botão de deletar
        var copyButton = $('#copyButton');

        // Adiciona um ouvinte de evento de input ao input de senha
        docNumberInput.on('input', function () {
            // Verifica se a senha tem pelo menos 8 caracteres
            if ($(this).val().length >= 11) {
                // Remove o atributo 'disabled' do botão de deletar
                copyButton.removeAttr('disabled');
                copyButton.removeClass('disabled');
            } else {
                // Adiciona o atributo 'disabled' ao botão de deletar
                copyButton.attr('disabled', 'disabled');
                copyButton.addClass('disabled');
            }
        });
    });
</script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script>
    $(document).ready(function () {
        // Seleciona o input de senha
        var passwordInput = $('#password');

        // Seleciona o botão de deletar
        var deleteButton = $('#deleteButton');

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
        // Obtenha a parte da URL após 'ver-loja/'
        var path = window.location.pathname.split('ver-loja/')[1];

        // Id da loja
        shopId = <?php echo $shop_id; ?>;

        // Altere o perfil na URL para a guia clicada
        var newUrl = window.location.origin + '/dropidigital/app/painel/ver-loja/' + tab + '?id=' + shopId;
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js"></script>

<script>
    $(document).ready(function() {
        // Quando o botão for clicado
        $("#copyDomain").click(function() {
            // Seleciona o texto do input
            var textToCopy = $("#domain").val();

            // Cria um elemento temporário (input) para copiar o texto
            var tempInput = $("<input>");
            $("body").append(tempInput);

            // Define o valor do input temporário como o texto a ser copiado
            tempInput.val(textToCopy).select();

            // Executa o comando de cópia
            document.execCommand("copy");

            // Remove o input temporário
            tempInput.remove();

            // Exibe uma mensagem (pode ser personalizado conforme necessário)
            alert("Texto copiado para a área de transferência: " + textToCopy);
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Quando o botão for clicado
        $("#copySubdomain").click(function() {
            // Seleciona o texto do input
            var textToCopy = $("#subdomain").val();

            // Cria um elemento temporário (input) para copiar o texto
            var tempInput = $("<input>");
            $("body").append(tempInput);

            // Define o valor do input temporário como o texto a ser copiado
            tempInput.val(textToCopy).select();

            // Executa o comando de cópia
            document.execCommand("copy");

            // Remove o input temporário
            tempInput.remove();

            // Exibe uma mensagem (pode ser personalizado conforme necessário)
            alert("Texto copiado para a área de transferência: " + textToCopy);
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