<div class="page__header center d-flex justify-content-center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title d-block">
                <h2 class="title">Temos o plano ideal pra cada momento da sua jornada</h2>
                <p>Tenha mais recursos assinando um dos nossos planos e leve sua loja para outro nível</p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Switch */
    .switch-container
    {
        width: 210px;
        height: 40px;
        padding: 4px !important;
        background: rgb(232, 233, 235);
        border-radius: 20px;
    }
    .switch-container .nav-item
    {
        width: 50%;
        height: 100%;
        cursor: pointer;
    }
    .switch-container .nav-item a
    {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 18px;
        text-decoration: none;
        color: var(--bs-heading-color);
        font-size: .875rem;
        font-weight: 600;
    }
    .switch-container .nav-item a.active
    {
        background: white;
    }

    /* Tab container */
    .tab-pane.fade
    {
        display: none;
    }
    .tab-pane.fade.show
    {
        display: block;
    }

    .price-container
    {
        height: 51px;
    }

    .btn
    {
        background: var(--green-color);
        font-size: .875rem;
        border: none;
        padding: .75rem 1.5rem;
    }
    .btn:hover
    {
        background: var(--dark-green-color);
    }
    .btn.current
    {
        color: var(--bs-heading-color);
        background: #e8e9eb !important;
    }

    /* Check */
    .list-style-one li
    {
        display: flex;
        font-size: .875rem;
        margin-bottom: .5rem;
    }
    .list-style-one li i
    {
        color: var(--green-color);
        font-size: 1.25rem;
        margin-right: .5rem;
    }
</style>

<div class="d-flex justify-content-center">
    <ul class="switch-container nav nav-pills nav-fill mb-80 rmb-50 wow fadeInUp delay-0-4s">
        <li class="nav-item">
            <a href="#plans-tap1" class="active" data-bs-toggle="tab">Mensal</a>
        </li>
        <li class="nav-item">
            <a href="#plans-tap2" data-bs-toggle="tab">Anual</a>
        </li>
    </ul>
</div>

<div class="tab-pane fade show active" id="plans-tap1">
    <div class="row g-3">
        <?php
            // Consulta SQL para obter todos os planos
            $query = "SELECT * FROM tb_plans";
            $stmt = $conn_pdo->query($query);
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Loop através dos resultados e exibir cada plano
            foreach ($plans as $plan) {
        ?>
        <div class="col d-grid">
            <div class="card">
                <div class="title mb-3">
                    <h4 class="lh-1 mb-0"><?php echo $plan['name']; ?></h4>
                    <p><?php echo $plan['sub_name']; ?></p>
                </div>
                <div class="price-container d-flex align-items-end mb-3">
                    <div class="price">
                        <span class="fs-5 fw-semibold">R$ <?php echo $plan['price']; ?></span>
                        <small>por mês</small>
                    </div>
                </div>

                <?php
                    if ($plan_id == $plan['id'])
                    {
                        echo '<button type="button" class="btn current rounded small fw-semibold mb-3" data-toggle="tooltip" data-placement="top" aria-label="Este já é o seu plano." data-bs-original-title="Este já é o seu plano.">Assinar plano</button>';
                    } else {
                        echo '<a href="' . $plan['link_checkout'] . '" class="btn btn-success rounded small fw-semibold mb-3">Assinar plano</a>';
                    }
                ?>

                <?php
                    // Exiba os recursos na página
                    $resources = json_decode($plan['resources']);

                    if (!empty($resources)) {
                        echo "<ul class='list-style-one mb-0'>";
                        foreach ($resources as $recurso) {
                            echo "<li><i class='bx bx-check'></i>$recurso</li>";
                        }
                        echo "</ul>";
                    }
                ?>
            </div>
        </div>
        <?php
            }
        ?>
    </div>
</div>
<div class="tab-pane fade" id="plans-tap2">
    <div class="row g-3">
        <div class="col d-grid">
            <div class="card">
                <div class="title mb-3">
                    <h4 class="lh-1 mb-0">Básico</h4>
                    <p>Conhecendo</p>
                </div>
                <div class="price-container d-flex align-items-end mb-3">
                    <div class="price">
                        <span class="fs-5 fw-semibold">R$ 0</span>
                        <small>por mês</small>
                    </div>
                </div>
                <button type="button" class="btn current rounded small fw-semibold mb-3" data-toggle="tooltip" data-placement="top" aria-label="Este já é o seu plano." data-bs-original-title="Este já é o seu plano.">Assinar plano</button>
                <ul class="list-style-one mb-0">
                    <li>
                        <i class='bx bx-check'></i>
                        10 produtos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        5.000 visitas/mês
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem limite de pedidos ou orçamentos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem comissão sobre vendas
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Conta protegida
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Botão WhatsApp
                    </li>
                </ul>
            </div>
        </div>
        <div class="col d-grid">
            <div class="card">
                <div class="title mb-3">
                    <h4 class="lh-1 mb-0">Iniciante</h4>
                    <p>Já faço vendas</p>
                </div>
                <div class="price-container mb-3">
                    <p class="text-body-secondary text-decoration-line-through small">R$ 47</p>
                    <span class="fs-5 fw-semibold">R$ 39</span>
                    <small>por mês</small>
                </div>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar-plano" class="btn btn-success rounded small fw-semibold mb-3">Assinar plano</a>
                <ul class="list-style-one mb-0">
                    <li>
                        <i class='bx bx-check'></i>
                        50 produtos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        25.000 visitas/mês
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem limite de pedidos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem comissão sobre vendas
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Conta protegida
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Botão WhatsApp
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Suporte humanizado
                    </li>
                </ul>
            </div>
        </div>
        <div class="col d-grid">
            <div class="card">
                <div class="title mb-3">
                    <h4 class="lh-1 mb-0">Intermeriário</h4>
                    <p>Pedidos diários</p>
                </div>
                <div class="price-container mb-3">
                    <p class="text-body-secondary text-decoration-line-through small">R$ 79</p>
                    <span class="fs-5 fw-semibold">R$ 59</span>
                    <small>por mês</small>
                </div>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar-plano" class="btn btn-success rounded small fw-semibold mb-3">Assinar plano</a>
                <ul class="list-style-one mb-0">
                    <li>
                        <i class='bx bx-check'></i>
                        250 produtos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        50.000 visitas/mês
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem limite de pedidos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem comissão sobre vendas
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Conta protegida
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Botão WhatsApp
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Suporte humanizado
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Palavras chave do seu nicho
                    </li>
                </ul>
            </div>
        </div>
        <div class="col d-grid">
            <div class="card">
                <div class="title mb-3">
                    <h4 class="lh-1 mb-0">Avançado</h4>
                    <p>Muitas vendas</p>
                </div>
                <div class="price-container mb-3">
                    <p class="text-body-secondary text-decoration-line-through small">R$ 159</p>
                    <span class="fs-5 fw-semibold">R$ 139</span>
                    <small>por mês</small>
                </div>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar-plano" class="btn btn-success rounded small fw-semibold mb-3">Assinar plano</a>
                <ul class="list-style-one mb-0">
                    <li>
                        <i class='bx bx-check'></i>
                        750 produtos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        100.000 visitas/mês
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem limite de pedidos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem comissão sobre vendas
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Conta protegida
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Botão WhatsApp
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Suporte humanizado
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Palavras chave do seu nicho
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Atendimento prioritário
                    </li>
                </ul>
            </div>
        </div>
        <div class="col d-grid">
            <div class="card">
                <div class="title mb-3">
                    <h4 class="lh-1 mb-0">Expert</h4>
                    <p>Voando alto</p>
                </div>
                <div class="price-container mb-3">
                    <p class="text-body-secondary text-decoration-line-through small">R$ 239</p>
                    <span class="fs-5 fw-semibold">R$ 199</span>
                    <small>por mês</small>
                </div>
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>assinar-plano" class="btn btn-success rounded small fw-semibold mb-3">Assinar plano</a>
                <ul class="list-style-one mb-0">
                    <li>
                        <i class='bx bx-check'></i>
                        Produtos ilimitados
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        300.000 visitas/mês
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem limite de pedidos
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Sem comissão sobre vendas
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Conta protegida
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Botão WhatsApp
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Suporte humanizado
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Palavras chave do seu nicho
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Atendimento prioritário
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Mentoria inicial do projeto
                    </li>
                    <li>
                        <i class='bx bx-check'></i>
                        Serviço de SEO incluso
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>