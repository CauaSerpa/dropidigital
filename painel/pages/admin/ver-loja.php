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
    
            $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC LIMIT 1";
    
            // Preparar e executar a consulta
            $stmt = $conn_pdo->prepare($sql);
            $stmt->bindParam(':shop_id', $shop['id']);
            $stmt->execute();
    
            // Obter o resultado como um array associativo
            $sub = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
    
            $sql = "SELECT p.id, p.name
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
                $shopPlan = $plan['name'];
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

<div class="modal fade" id="warningShop" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<form id="warningShopForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/warning_shop.php" method="post">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Criar Aviso
                </div>
            </div>
            <div class="modal-body row px-4 py-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="level" class="form-label small">Nível de Relevância *</label>
                        <div class="input-group">
                            <select class="form-select" name="level" id="level" required>
                                <option value="" selected disabled>Selecione o nível de relevância</option>
                                <option value="1">Baixo</option>
                                <option value="2">Médio</option>
                                <option value="3">Alto</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label small">Tipo *</label>
                        <div class="input-group">
                            <select class="form-select" name="type" id="type" required>
                                <option value="" selected disabled>Selecione o tipo</option>
                                <option value="1">Modal</option>
                                <option value="2">Texto fixo</option>
                                <option value="3">Notificação</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="title" class="form-label small">Título *</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label small">Conteúdo *</label>
                        <textarea class="form-control" name="content" id="content" rows="3" required></textarea>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
            <div class="modal-footer fw-semibold px-4">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success border-danger d-flex align-items-center fw-semibold px-4 py-2 small" id="warningButton">
                    <i class='bx bx-error-circle me-2' ></i>
                    Avisar
                </button>
            </div>
        </div>
    </div>
</form>
</div>

<div class="modal fade" id="updateWarningShop" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<form id="updateWarningShopForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/edit_warning_shop.php" method="post">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Editar Aviso
                </div>
            </div>
            <div class="modal-body row px-4 py-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="level" class="form-label small">Nível de Relevância *</label>
                        <div class="input-group">
                            <select class="form-select" name="level" id="level" required>
                                <option value="" selected disabled>Selecione o nível de relevância</option>
                                <option value="1">Baixo</option>
                                <option value="2">Médio</option>
                                <option value="3">Alto</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="type" class="form-label small">Tipo *</label>
                        <div class="input-group">
                            <select class="form-select" name="type" id="type" required>
                                <option value="" selected disabled>Selecione o tipo</option>
                                <option value="1">Modal</option>
                                <option value="2">Texto fixo</option>
                                <option value="3">Notificação</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="title" class="form-label small">Título *</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label small">Conteúdo *</label>
                        <textarea class="form-control" name="content" id="content" rows="3" required></textarea>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
            <div class="modal-footer fw-semibold px-4">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success d-flex align-items-center fw-semibold px-4 py-2 small" id="warningButton">
                    <i class='bx bx-error-circle me-2' ></i>
                    Editar Aviso
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

<?php
    date_default_timezone_set('America/Sao_Paulo');

    $today = new DateTime();
    $date = $today->format("Y-m-d");
?>

<div class="modal fade" id="editShopPlan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<form id="editShopPlanForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/admin/edit_shop_plan.php" method="post">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4 py-3 bg-transparent">
                <div class="fw-semibold py-2">
                    Editar Plano
                </div>
            </div>
            <div class="modal-body row px-4 py-3">
                <div class="mb-3">
                    <label for="plan_id" class="form-label small">Plano *</label>
                    <div class="input-group">
                        <select class="form-select" name="plan_id" id="plan_id" required>
                            <option value="" disabled>Selecione o plano</option>
                            <?php
                                // Aqui você pode popular a tabela com dados do banco de dados
                                // Vamos supor que cada linha tem um ID únic

                                // Nome da tabela para a busca
                                $tabelaInterval = 'tb_plans_interval';
                                $tabelaPlans = 'tb_plans';

                                // id do plano
                                $shop['plan_id'] = ($sub['cycle'] == "MONTHLY") ? $shop['plan_id'] : ($shop['plan_id'] - 1);

                                $sql = "SELECT i.id, p.name
                                    FROM $tabelaInterval i
                                    JOIN $tabelaPlans p ON i.plan_id = p.id
                                    WHERE i.billing_interval = :billing_interval
                                    ORDER BY p.id ASC";

                                // Preparar e executar a consulta
                                $stmt = $conn_pdo->prepare($sql);
                                $stmt->bindValue(':billing_interval', 'monthly');
                                $stmt->execute();

                                // Recuperar os resultados
                                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($stmt->rowCount() > 0) {
                                    // Loop através dos resultados e exibir todas as colunas
                                    foreach ($resultados as $plan) {
                                        $selected = ($shop['plan_id'] == $plan['id']) ? "selected" : "";

                                        echo "<option value='" . $plan['id'] . "' $selected>" . $plan['name'] . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="cycle" class="form-label small">Tipo de Cobrança *</label>
                    <div class="input-group">
                        <select class="form-select" name="cycle" id="cycle" required>
                            <option value="" disabled>Selecione o tipo</option>
                            <option value="MONTHLY">Mensal</option>
                            <option value="YEARLY">Anual</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <h6 class="mb-3">Duração</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label small">Início *</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="start_date" id="start_date" min="<?php echo $date; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label small">Fim *</label>
                            <div class="input-group mb-2">
                                <input type="date" class="form-control" name="due_date" id="due_date" min="<?php echo $date; ?>" required>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="undefined" id="undefined">
                                <label class="form-check-label" for="undefined">Indefinido</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
            <div class="modal-footer fw-semibold px-4">
                <button type="button" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-4 py-2 small" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success border-danger d-flex align-items-center fw-semibold px-4 py-2 small" id="updShopPlan">Salvar</button>
            </div>
        </div>
    </div>
</form>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function() {
      $('#start_date').on('change', updateDueDate);
      $('#cycle').on('change', updateDueDate);
      $('#undefined').on('change', updateDueDate);

      function updateDueDate() {
        var startDateValue = $('#start_date').val();
        var billingTypeValue = $('#cycle').val();
        var undefinedCheckbox = $('#undefined');

        if (undefinedCheckbox.is(':checked')) {
          // Se o checkbox estiver marcado, desabilita o input e remove o valor
          $('#due_date').prop('disabled', true).prop('required', false).val('');
          $('#undefined').prop('required', true);
        } else {
          // Se o checkbox não estiver marcado, habilita o input e atualiza o valor mínimo
          $('#due_date').prop('disabled', false).prop('required', true);
          $('#undefined').prop('required', false);

          if (startDateValue && billingTypeValue) {
            var nextDate = new Date(startDateValue);

            if (billingTypeValue === 'MONTHLY') {
              nextDate.setMonth(nextDate.getMonth() + 1);
            } else if (billingTypeValue === 'YEARLY') {
              nextDate.setFullYear(nextDate.getFullYear() + 1);
            }

            var nextDateFormatted = nextDate.toISOString().split('T')[0];
            $('#due_date').attr('min', nextDateFormatted);
            $('#due_date').val(nextDateFormatted);
          }
        }
      }
    });
</script>

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
                    // Nome da tabela para a busca
                    $tabela = 'tb_domains';

                    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain <> 'dropidigital.com.br' ORDER BY (domain = 'dropidigital.com.br') DESC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':shop_id', $shop['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $domain = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Se não houver resultados, realizar outra consulta
                    if (empty($domain)) {
                        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain = 'dropidigital.com.br'";
                        $stmt = $conn_pdo->prepare($sql);
                        $stmt->bindParam(':shop_id', $shop['id']);
                        $stmt->execute();
                        $domain = $stmt->fetch(PDO::FETCH_ASSOC);
                    }

                    $subdomain = ($domain['subdomain'] !== "www") ? $domain['subdomain'] . "." : "";
                    $domain_url = "https://" . $subdomain . $domain['domain'];
                ?>
                <a href="<?php echo $domain_url; ?>" target="_black" class="button button--flex new text-decoration-none d-flex align-items-center">
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
        <a class="nav-link <?php echo ($tab == "historico-de-faturas") ? "active" : ""; ?>" id="historico-de-faturas" onclick="changeTab('historico-de-faturas')" data-bs-toggle="tab" data-bs-target="#invoice-tab-pane" type="button" role="tab" aria-controls="invoice-tab-pane" aria-selected="<?php echo ($tab == "historico-de-faturas") ? "true" : "false"; ?>">Histórico de Faturas</a>
    </li>
</ul>

<div class="tab-content" id="myTabContent">

<div class="tab-pane fade <?php echo ($tab == "" || $tab == "loja") ? "show active" : ""; ?>" id="shop-tab-pane" role="tabpanel" aria-labelledby="shop-tab" tabindex="0">
    <div class="row">
        <div class="col-md-6">
            <div class="card p-0 mb-3">
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
                            <small><?php echo $shopPlan; ?></small>
                        </li>
                        <li class="d-flex justify-content-between">
                            <small class="fw-semibold">Vencimento:</small>
                            <small><?php echo date("d/m/Y", strtotime($sub['due_date'])); ?></small>
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
            <?php
                // Tabela que sera feita a consulta
                $tabela = "tb_warning";

                // Consulta SQL
                $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                // Preparar a consulta
                $stmt = $conn_pdo->prepare($sql);

                // Vincular o valor do parâmetro
                $stmt->bindParam(':shop_id', $shop['id'], PDO::PARAM_INT);

                // Executar a consulta
                $stmt->execute();

                // Obter o resultado como um array associativo
                $warnings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <style>
                table#shopWarnings thead tr th,
                table#shopWarnings tbody tr td
                {
                    padding: 0.5rem 1rem !important;
                    font-size: .875rem;
                }
                table#shopWarnings tbody tr td
                {
                    max-width: 150px;
                }
                #shopWarnings .btn
                {
                    padding: 0;
                    width: 30px;
                    height: 30px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                }
            </style>

            <div class="card p-0 <?= (!$warnings) ? "d-none" : ""; ?>">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Avisos</div>
                <div class="card-body row px-4 py-3">
                    <table id="shopWarnings">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="checkAll">
                                </th>
                                <th class="small">Título</th>
                                <th class="small">Conteúdo</th>
                                <th class="small">Data de Criação</th>
                                <th class="small">Eventos</th>
                            </tr>
                        </thead>
                        <?php
                        // Loop através dos resultados e exibir todas as colunas
                        foreach ($warnings as $warning) {
                            $date_create = date("d/m/Y", strtotime($warning['date_create']));
                        ?>
                            <tbody>
                                <tr>
                                    <td scope="row">
                                        <input class="form-check-input itemCheckbox" type="checkbox" name="selected_ids[]" value="<?= $warning['id']; ?>" id="defaultCheck2">
                                    </td>
                                    <td title="<?= $warning['title']; ?>"><?= $warning['title']; ?></td>
                                    <td title="<?= $warning['content']; ?>"><?= $warning['content']; ?></td>
                                    <td title="<?= $warning['date_create']; ?>"><?= $date_create; ?></td>
                                    <td>
                                        <button type="button" class="btn btn-primary editBtn" data-id="<?= $warning['id']; ?>" data-title="<?= htmlspecialchars($warning['title'], ENT_QUOTES); ?>" data-content="<?= htmlspecialchars($warning['content'], ENT_QUOTES); ?>" data-level="<?= $warning['level']; ?>" data-type="<?= $warning['type']; ?>">
                                            <i class="bx bxs-edit"></i>
                                        </button>
                                        <a href="<?= INCLUDE_PATH_DASHBOARD; ?>excluir-aviso?id=<?= $warning['id']; ?>&shop=<?= $shop['id']; ?>" class="btn btn-danger">
                                            <i class="bx bxs-trash" ></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        <?php
                        }
                    ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Ações</div>
                <div class="card-body row px-4 py-3">
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            <h6 class="fs-6 fw-semibold mb-0">Acessar</h6>
                            <small>Clique em acessar para acessar a loja</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/admin/access_shop.php?user_id=<?php echo $user['id']; ?>&shop_id=<?php echo $shop['id']; ?>" class="btn btn-success fw-semibold px-4 py-2 small">Acessar</a>
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
                        <button class="d-flex align-items-center border-0" data-bs-toggle="modal" data-bs-target="#warningShop">
                            <a href="#" class="btn btn-warning text-white d-flex align-items-center fw-semibold px-4 py-2 small">
                                <i class='bx bx-error-circle me-2' ></i>
                                Aviso
                            </a>
                        </button>
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

    // Consulta para obter o domínio que não seja "dropidigital.com.br"
    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain != :domain";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop['id']);
    $stmt->bindValue(':domain', "dropidigital.com.br");
    $stmt->execute();
    $domainWithoutDropi = $stmt->fetch(PDO::FETCH_ASSOC);

    // Consulta para obter o domínio que seja "dropidigital.com.br"
    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain = :domain";
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':shop_id', $shop['id']);
    $stmt->bindValue(':domain', "dropidigital.com.br");
    $stmt->execute();
    $domainWithDropi = $stmt->fetch(PDO::FETCH_ASSOC);

    // Definir o domínio final com base nos resultados
    if ($domainWithoutDropi) {
        $subdomain = ($domainWithoutDropi['subdomain'] !== "www") ? $domainWithoutDropi['subdomain'] . "." : "";
        $domain_url = $subdomain . $domainWithoutDropi['domain'];
        $subdomain = $domainWithDropi['subdomain'] . "." . $domainWithDropi['domain'];

        $domain = $domainWithoutDropi;
    } else {
        $domain_url = $domainWithDropi['subdomain'] . "." . $domainWithDropi['domain'];
        $subdomain = $domain_url;

        $domain = $domainWithDropi;

        $domain['configure_date'] = $domain['register_date'];
        $domain['active_date'] = $domain['register_date'];

        $domain['configure'] = 1;
        $domain['status'] = 1;
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
                    
                    <div class="line my-3 <?php echo (!$domainWithoutDropi) ? "d-none" : ""; ?>"></div>

                    <div class="d-flex align-items-center mb-3 <?php echo (!$domainWithoutDropi) ? "d-none" : ""; ?>">
                        <a href="<?php echo "https://" . $subdomain; ?>" target="_black" class="domain d-inline-flex align-items-center fs-5 fw-semibold" style="color: var(--bs-body-color);"><?php echo $subdomain; ?></a>
                        <i class='bx bxs-copy fs-4 ms-1' id="copySubdomain"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>

    <input type="hidden" id="domain" value="<?php echo $domain_url; ?>">
    <input type="hidden" id="subdomain" value="<?php echo $subdomain; ?>">

</form>
</div>

<?php
    if ($sub['status'] == "RECEIVED") {
        $bullet = "<span class='bullet success me-2'></span>";
        $status = "Paga";
    } else if ($sub['status'] == "ACTIVE" && $sub['billing_type'] == "CREDIT_CARD") {
        $bullet = "<span class='bullet success me-2'></span>";
        $status = "Paga";
    } else if ($sub['status'] == "ACTIVE") {
        $bullet = "<span class='bullet warning me-2'></span>";
        $status = "Aguardando pagamento";
    } else if ($sub['status'] == "OVERDUE" || $sub['status'] == "INACTIVE") {
        $bullet = "<span class='bullet danger me-2'></span>";
        $status = "Cancelada";
    }

    // Verificação se a consulta retornou algum resultado
    if ($sub) {
        if ($sub['cycle'] == "MONTHLY")
        {
            $cycle = "Mensal";
        } else {
            $cycle = "Anual";
        }

        if ($sub['billing_type'] == "CREDIT_CARD")
        {
            $billing_type = "Cartão de crédito";
        } else {
            $billing_type = "Pix";
        }

        // Formatando datas
        $startDate = new DateTime($sub['start_date']);
        $start_date = $startDate->format("d/m/Y");

        $dueDate = new DateTime($sub['due_date']);
        $due_date = $dueDate->format("d/m/Y");
    }
?>

<div class="tab-pane fade <?php echo ($tab == "historico-de-faturas") ? "show active" : ""; ?>" id="invoice-tab-pane" role="tabpanel" aria-labelledby="invoice-tab" tabindex="0">
<form id="editTheme" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_theme.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Plano Atual</div>
        <div class="card-body row px-4 py-3">
            <div class="card col-md-6 px-4 py-3">
                <ul class="mb-0">
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">ID:</small>
                        <small><?php echo $sub['id']; ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Status:</small>
                        <small><?php echo $bullet . $status; ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Valor:</small>
                        <small>R$ <?php echo number_format($sub['value'], 2, ',', '.'); ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Forma de Pagamento:</small>
                        <small><?php echo $billing_type; ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Ciclo:</small>
                        <small><?php echo $cycle; ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">E-mail para NFe:</small>
                        <small><?php echo $email; ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Data da Emissão:</small>
                        <small><?php echo $start_date; ?></small>
                    </li>
                    <li class="d-flex justify-content-between">
                        <small class="fw-semibold">Próximo Pagamento:</small>
                        <small><?php echo $due_date; ?></small>
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <h5 class="fs-5 mb-3">Ações</h5>
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h6 class="fs-6 fw-semibold mb-0">Alterar Plano</h6>
                        <small>Clique em alterar plano para alterar o plano da loja</small>
                    </div>
                    <button class="d-flex align-items-center border-0" data-bs-toggle="modal" data-bs-target="#editShopPlan">
                        <a href="#" class="btn btn-success d-flex align-items-center fw-semibold px-4 py-2 small">
                            Alterar Plano
                        </a>
                    </button>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <h6 class="fs-6 fw-semibold mb-0">Listar NFS-e</h6>
                        <small>Clique em listar para alterar o plano da loja</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/admin/access_shop.php?id=<?php echo $user['id']; ?>" class="btn btn-success fw-semibold px-4 py-2 small">Alterar Plano</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="shop_id" value="<?php echo $shop['id']; ?>">
</form>

    <div class="card__container grid one tabPanel" style="display: grid;">
        <div class="card__box grid">
            <div class="card table">

            <?php
                if ($stmt->rowCount() > 0) {
            ?>
                <table>
                    <thead>
                        <tr>
                            <th class="small">ID</th>
                            <th class="small">Vencimento</th>
                            <th class="small">Valor</th>
                            <th class="small">Status</th>
                            <th class="small">Eventos</th>
                        </tr>
                    </thead>
                    <?php
                    // Nome da tabela para a busca
                    $tabela = 'tb_subscriptions';

                    $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC";

                    // Preparar e executar a consulta
                    $stmt = $conn_pdo->prepare($sql);
                    $stmt->bindParam(':shop_id', $shop['id']);
                    $stmt->execute();

                    // Recuperar os resultados
                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Loop através dos resultados e exibir todas as colunas
                    foreach ($resultados as $usuario) {
                        //Formatacao preco
                        // $price = str_replace(',', '.', str_replace('.', '', $usuario['price']));
                        $valor = $usuario['value'];

                        // Transforma o número no formato "R$ 149,90"
                        $value = "R$ " . number_format($valor, 2, ",", ".");

                        //Formatacao para data
                        $due_date = date("d/m/Y", strtotime($usuario['due_date']));

                        if ($usuario['status'] == "RECEIVED") {
                            $bullet = "<span class='bullet success me-2'></span>";
                            $status = "Paga";
                        } else if ($usuario['status'] == "ACTIVE" && $usuario['billing_type'] == "CREDIT_CARD") {
                            $bullet = "<span class='bullet success me-2'></span>";
                            $status = "Paga";
                        } else if ($usuario['status'] == "ACTIVE") {
                            $bullet = "<span class='bullet warning me-2'></span>";
                            $status = "Aguardando pagamento";
                        } else if ($usuario['status'] == "OVERDUE" || $usuario['status'] == "INACTIVE") {
                            $bullet = "<span class='bullet danger me-2'></span>";
                            $status = "Cancelada";
                        }

                        echo '
                            <tbody>
                                <tr>
                                    <td>' . $usuario['id'] . '</td>
                                    <td>' . $due_date . '</td>
                                    <td>' . $value . '</td>
                                    <td><div class="d-flex align-items-center">' . $bullet . $status . '</div></td>
                                    <td>
                                        <a href="' . INCLUDE_PATH_DASHBOARD . 'fatura?id=' . $usuario['id'] . '" class="btn btn-secondary">
                                            <i class="bx bx-show"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        ';
                    }
                ?>
                </table>
            </div>
            <?php
                } else {
                    echo '
                            <div class="p-5 text-center">
                                <i class="bx bx-receipt" style="font-size: 3.5rem;"></i>
                                <p class="fw-semibold mb-4">Você não possui nenhum plano ativo!</p>
                            </div>
                        ';
                }
            ?>
        </div>
    </div>
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

<!-- Update Warning -->
<script>
    $(document).ready(function() {
        // Função para abrir o modal de edição
        $('.editBtn').click(function() {
            // Obter dados do botão clicado
            var id = $(this).data('id');
            var title = $(this).data('title');
            var content = $(this).data('content');
            var level = $(this).data('level');
            var type = $(this).data('type');

            // Preencher o formulário do modal com os dados
            $('#updateWarningShop #id').val(id);
            $('#updateWarningShop #title').val(title);
            $('#updateWarningShop #content').val(content);
            $('#updateWarningShop #level').val(level);
            $('#updateWarningShop #type').val(type);

            // Abrir o modal
            $('#updateWarningShop').modal('show');
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