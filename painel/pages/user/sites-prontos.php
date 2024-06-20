<style>
    .owl-carousel
    {
        position: relative;
    }
    .owl-nav
    {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
    }
    .owl-nav button
    {
        position: absolute;
        height: 30px;
        width: 30px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: none;
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .owl-nav button.owl-prev
    {
        left: -30px;
    }
    .owl-nav button.owl-next
    {
        right: 30px;
    }

    .owl-dots
    {
        display: none;
    }
</style>

<div class="page__header center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title">
                <h2 class="title">Sites Prontos</h2>
            </div>
        </div>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/sites-prontos" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1' ></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_ready_sites';

    $sql = "SELECT * FROM $tabela WHERE status = :status AND emphasis = :emphasis ORDER BY id DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':status', 1);
    $stmt->bindValue(':emphasis', 1);
    $stmt->execute();

    // Recuperar os resultados
    $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sites) {
?>
<h3 class="title h5 fw-normal mt-2 mb-2">Destaque</h3>
<div class="owl-carousel g-3 mb-2" id="readySitesCarousel">
<?php
    // Loop através dos resultados e exibir todas as colunas
    foreach ($sites as $site) {
        $image = INCLUDE_PATH_DASHBOARD . "back-end/admin/ready-website/" . $site['id'] . "/card-image/" . $site['card_image'];

        // Shop
        // Nome da tabela para a busca
        $tabela = 'tb_shop';

        $sql = "SELECT * FROM $tabela WHERE id = :id ORDER BY id DESC LIMIT 1";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $site['shop_id']);
        $stmt->execute();

        // Recuperar os resultados
        $shop = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($shop['segment'] == 0) {
            $segment = "Dropshipping Infoproduto";
        } elseif ($shop['segment'] == 1) {
            $segment = "Dropshipping produto físico";
        } elseif ($shop['segment'] == 2) {
            $segment = "Site divulgação de serviços";
        } elseif ($shop['segment'] == 3) {
            $segment = "Site comércio físico";
        } else {
            $segment = "Site para agendamento";
        }

        // Preco
        // Transforma o número no formato "R$ 149,90"
        $price = "R$ " . number_format($site['price'], 2, ",", ".");
        $discount = "R$ " . number_format($site['discount'], 2, ",", ".");

        // Calcula a porcentagem de desconto
        if ($site['price'] != 0) {
            $porcentagemDesconto = (($site['price'] - $site['discount']) / $site['price']) * 100;
        } else {
            // Lógica para lidar com o caso em que $site['price'] é zero
            $porcentagemDesconto = 0; // Ou outro valor padrão
        }

        // Arredonda o resultado para duas casas decimais
        $porcentagemDesconto = round($porcentagemDesconto, 0);

        if ($site['discount'] == "0.00") {
            $activeDiscount = "d-none";

            $priceAfterDiscount = $price;

            $installment = $site['price'] / 12;
        } else {
            $activeDiscount = "";

            $priceAfterDiscount = $discount;
            $discount = $price;

            $installment = $site['discount'] / 12;
        }

        $installmentValue = "R$ " . number_format($installment, 2, ",", ".");

        // Domain
        // Nome da tabela para a busca
        $tabela = 'tb_domains';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id AND domain = :domain ORDER BY id DESC LIMIT 1";
    
        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $site['shop_id']);
        $stmt->bindValue(':domain', 'dropidigital.com.br');
        $stmt->execute();

        // Recuperar os resultados
        $domain = $stmt->fetch(PDO::FETCH_ASSOC);

        // URL
        $url = "https://" . $domain['subdomain'] . "." . $domain['domain'];

        // Link
        $link = INCLUDE_PATH_DASHBOARD . "site-pronto/" . $site['link'];
?>
    <style>
        .card-subtitle.segment
        {
            display: inline;
            align-items: center;
            justify-content: center;
            font-size: .875rem;
        }
        .card-subtitle.segment i
        {
            font-size: .875rem;
        }
        .card-discount
        {
            position: absolute;
            left: 15px;
            top: 10px;
            width: 100px;
            height: 32px;
            font-weight: 600;
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            color: #fff;
        }
    </style>

    <div class="d-grid">
        <div class="card p-0">
            <div class="product-image">
                <span class="card-discount small <?= $activeDiscount; ?>"><?= $porcentagemDesconto; ?>% OFF</span>
                <img src="<?= $image; ?>" class="card-img-top" alt="Imagem Site Pronto">
            </div>
            <div class="card-body">
                <p class="card-subtitle segment bg-secondary-subtle border border-0 rounded-1 px-1 py-0"><i class='bx bx-purchase-tag-alt me-1' ></i><?= $segment; ?></p>
                <p class="card-title mb-3"><?= $site['name']; ?></p>
                <small class="fw-semibold text-body-secondary text-decoration-line-through mb-0 <?= $activeDiscount; ?>"><?= $discount; ?></small>
                <h5 class="card-text mb-0"><?= $priceAfterDiscount; ?></h5>
                <small class="fw-semibold text-body-secondary">12x de <?= $installmentValue; ?> sem juros</small>
                <div class="buttons d-flex mt-4">
                    <a href="<?= $url; ?>" target="_blank" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-3 py-2 me-2 small">
                        <i class='bx bx-show-alt'></i>
                    </a>
                    <a href="<?= $link; ?>" class="btn btn-success fw-semibold px-4 py-2 small w-100">
                        Detalhes
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php
    }
?>
</div>
<?php
    }
?>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_ready_sites';

    $sql = "SELECT * FROM $tabela WHERE status = :status ORDER BY (emphasis = :emphasis) DESC";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':status', 1);
    $stmt->bindValue(':emphasis', 1);
    $stmt->execute();

    // Recuperar os resultados
    $sites = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sites) {
?>
<h3 class="title h5 fw-normal mt-2 mb-2">Sites Prontos</h3>
<div class="row g-3 mb-3">
<?php
    // Loop através dos resultados e exibir todas as colunas
    foreach ($sites as $site) {
        $image = INCLUDE_PATH_DASHBOARD . "back-end/admin/ready-website/" . $site['id'] . "/card-image/" . $site['card_image'];

        // Shop
        // Nome da tabela para a busca
        $tabela = 'tb_shop';

        $sql = "SELECT * FROM $tabela WHERE id = :id ORDER BY id DESC LIMIT 1";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':id', $site['shop_id']);
        $stmt->execute();

        // Recuperar os resultados
        $shop = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($shop['segment'] == 0) {
            $segment = "Dropshipping Infoproduto";
        } elseif ($shop['segment'] == 1) {
            $segment = "Dropshipping produto físico";
        } elseif ($shop['segment'] == 2) {
            $segment = "Site divulgação de serviços";
        } elseif ($shop['segment'] == 3) {
            $segment = "Site comércio físico";
        } else {
            $segment = "Site para agendamento";
        }

        // Preco
        // Transforma o número no formato "R$ 149,90"
        $price = "R$ " . number_format($site['price'], 2, ",", ".");
        $discount = "R$ " . number_format($site['discount'], 2, ",", ".");

        // Calcula a porcentagem de desconto
        if ($site['price'] != 0) {
            $porcentagemDesconto = (($site['price'] - $site['discount']) / $site['price']) * 100;
        } else {
            // Lógica para lidar com o caso em que $site['price'] é zero
            $porcentagemDesconto = 0; // Ou outro valor padrão
        }

        // Arredonda o resultado para duas casas decimais
        $porcentagemDesconto = round($porcentagemDesconto, 0);

        if ($site['discount'] == "0.00") {
            $activeDiscount = "d-none";

            $priceAfterDiscount = $price;

            $installment = $site['price'] / 12;
        } else {
            $activeDiscount = "";

            $priceAfterDiscount = $discount;
            $discount = $price;

            $installment = $site['discount'] / 12;
        }

        $installmentValue = "R$ " . number_format($installment, 2, ",", ".");

        // Domain
        // Nome da tabela para a busca
        $tabela = 'tb_domains';

        $sql = "SELECT * FROM $tabela WHERE shop_id = :shop_id ORDER BY id DESC LIMIT 1";

        // Preparar e executar a consulta
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindParam(':shop_id', $site['shop_id']);
        $stmt->execute();

        // Recuperar os resultados
        $domain = $stmt->fetch(PDO::FETCH_ASSOC);

        // URL
        $url = "https://" . $domain['subdomain'] . "." . $domain['domain'];

        // Link
        $link = INCLUDE_PATH_DASHBOARD . "site-pronto/" . $site['link'];
?>
    <style>
        .card-subtitle.segment
        {
            display: inline;
            align-items: center;
            justify-content: center;
            font-size: .875rem;
        }
        .card-subtitle.segment i
        {
            font-size: .875rem;
        }
        .card-discount
        {
            position: absolute;
            left: 15px;
            top: 10px;
            width: 100px;
            height: 32px;
            font-weight: 600;
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            color: #fff;
        }
    </style>

    <div class="col-sm-3 numBanner d-grid">
        <div class="card p-0">
            <div class="product-image">
                <span class="card-discount small <?= $activeDiscount; ?>"><?= $porcentagemDesconto; ?>% OFF</span>
                <img src="<?= $image; ?>" class="card-img-top" alt="Imagem Site Pronto">
            </div>
            <div class="card-body">
                <p class="card-subtitle segment bg-secondary-subtle border border-0 rounded-1 px-1 py-0"><i class='bx bx-purchase-tag-alt me-1' ></i><?= $segment; ?></p>
                <p class="card-title mb-3"><?= $site['name']; ?></p>
                <small class="fw-semibold text-body-secondary text-decoration-line-through mb-0 <?= $activeDiscount; ?>"><?= $discount; ?></small>
                <h5 class="card-text mb-0"><?= $priceAfterDiscount; ?></h5>
                <small class="fw-semibold text-body-secondary">12x de <?= $installmentValue; ?> sem juros</small>
                <div class="buttons d-flex mt-4">
                    <a href="<?= $url; ?>" target="_blank" class="btn btn-outline-light border border-secondary-subtle text-secondary fw-semibold px-3 py-2 me-2 small">
                        <i class='bx bx-show-alt'></i>
                    </a>
                    <a href="<?= $link; ?>" class="btn btn-success fw-semibold px-4 py-2 small w-100">
                        Detalhes
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php
    }
?>
</div>
<?php
    }
?>

<!-- Owl Carousel -->
<!-- Inclua o script do Owl Carousel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<script>
    // Inicialize o carrossel
    $('#readySitesCarousel').owlCarousel({
        loop: false, // Loop infinito
        margin: 15, // Espaçamento entre os itens
        nav: true, // Navegação (setas)
        responsive: { // Configurações responsivas
            0: { // Quando a largura da tela for menor que 600px
                items: 2 // Mostrar apenas 1 item por vez
            },
            600: { // Quando a largura da tela for igual ou maior que 600px
                items: 2 // Mostrar 2 itens por vez
            },
            768: { // Quando a largura da tela for igual ou maior que 768px
                items: 4 // Mostrar 3 itens por vez
            }
        }
    });
</script>