<?php
// Obtém a URL atual
$urlPath = $_GET['url'];

// Remove qualquer string de consulta, se houver
$urlPath = parse_url($urlPath, PHP_URL_PATH);

// Divide a URL em partes
$urlParts = explode('/', trim($urlPath, '/'));

// Verifica se há partes suficientes na URL
if (count($urlParts) >= 2) {
    $link = $urlParts[1]; // O nome do arquivo é a segunda parte
} else {
    $_SESSION['msg'] = "<p class='red'>Nenhum Site Pronto encontrado!</p>";
    header('Location: ' . INCLUDE_PATH_DASHBOARD . 'sites-prontos');
    exit();
}

// Tabela que sera feita a consulta
$tabela = "tb_ready_sites";

// Consulta SQL
$sql = "SELECT * FROM $tabela WHERE link = :link";

// Preparar a consulta
$stmt = $conn_pdo->prepare($sql);

// Vincular o valor do parâmetro
$stmt->bindParam(':link', $link, PDO::PARAM_INT);

// Executar a consulta
$stmt->execute();

// Obter o resultado como um array associativo
$site = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar se o resultado foi encontrado
if ($site) {
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

        $priceNoFormat = $site['price'];

        $installment = $site['price'] / 12;
    } else {
        $activeDiscount = "";

        $priceAfterDiscount = $discount;
        $discount = $price;

        $priceNoFormat = $site['discount'];

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
    $link = INCLUDE_PATH_DASHBOARD . "site-pronto?id=" . $site['id'];
?>

<style>
    .description ul
    {
        list-style-type: disc;
        padding-left: 2rem !important;
    }
</style>

<div class="page__header center">
    <div class="header__title">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb align-items-center mb-3">
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>sites-prontos" class="fs-5 text-decoration-none text-reset">Sites Prontos</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page"><?= $site['name']; ?></li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/dados-para-pagamento" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1' ></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<style>
    .description
    {
        overflow-wrap: anywhere;
    }

    /* Estilo para tornar o iframe do YouTube responsivo */
    .video-wrapper
    {
        overflow: hidden;
        padding-top: 56.20%; /* Mantém a proporção de 16:9 */
        position: relative;
        width: 100%;
    }

    .video-wrapper iframe
    {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD; ?>checkout" method="post">
    <div class="card mb-3 p-0">
        <div class="row px-4 py-3">
            <div class="col-md-8">
                <h4 class="mb-3"><?= $site['name']; ?></h4>
                <div class="row mb-3">
                    <div class="col-md-2 small">Versão <?= $site['version']; ?></div>
                    <div class="col-md-3 small">Suporte <?= $site['support']; ?></div>
                    <div class="col-md-3 small">Instalação Imediata</div>
                    <div class="col-md-4 small">Segmento <?= $segment; ?></div>
                </div>
                <hr>
                <div class="description mt-3">
                    <?php
                        if (isset($site['image'])) {
                            $image = INCLUDE_PATH_DASHBOARD . "back-end/admin/ready-website/" . $site['id'] . "/image/" . $site['image'];

                            echo "<img src='$image' alt='Ready Site Image' class='mb-3'>";
                        } elseif (isset($site['video'])) {
                    ?>
                    <div id="video-display" class="d-flex justify-content-center mb-3">
                        <div class="video-wrapper d-flex justify-content-center">
                            <?php
                                // Função para extrair o código do vídeo do URL do YouTube
                                function getYoutubeEmbedCode($url) {
                                    // Verifica se o URL é um link válido do YouTube
                                    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
                                        $videoCode = $matches[1];

                                        // Gera o código de incorporação
                                        $embedCode = '<iframe src="https://www.youtube.com/embed/' . $videoCode . '" frameborder="0" allowfullscreen></iframe>';

                                        return $embedCode;
                                    } else {
                                        // URL inválido do YouTube
                                        return 'URL do YouTube inválido.';
                                    }
                                }

                                // Exemplo de uso:
                                $youtubeURL = $site['video'];
                                $embedCode = getYoutubeEmbedCode($youtubeURL);

                                if ($embedCode !== 'URL do YouTube inválido.') {
                                    echo $embedCode;
                                }
                            ?>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                    <?= $site['description']; ?>
                </div>
            </div>

            <style>
                #itemsIncluded .icon
                {
                    color: var(--green-color);
                }
            </style>
            <div class="col-md-4">
                <a href="<?= $url; ?>" class="btn btn-outline-success fw-semibold px-4 py-2 small w-100 mb-3">Ver Site Pronto</a>
                <div class="card p-0 mb-3 <?php echo ($site['items_included'] == "") ? "d-none" : ""; ?>">
                    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Benefícios</div>
                    <div class="card-body row px-4 py-3">
                        <ul id="itemsIncluded" class="mb-0">
                            <?php
                                $json_encoded = $site['items_included'];
                                
                                // Supondo que $site['items_included'] seja uma string JSON
                                $items_included = json_decode($json_encoded, true); // Decodifica a string JSON para um array associativo

                                // Verifica se $items_included é um array antes de usar foreach
                                if(is_array($items_included)) {
                                    foreach ($items_included as $item) {
                                        // Decodifica as entidades HTML antes de exibir
                                        $item = htmlspecialchars_decode($item);
                            ?>
                                        <li><i class='bx bx-check icon me-1'></i> <?= $item; ?></li>
                            <?php
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
                    // Sua consulta SQL com JOIN para obter os serviços associados a um site
                    $sql = "SELECT ss.*, s.* FROM tb_ready_site_services ss JOIN tb_services s ON ss.service_id = s.id WHERE ss.ready_site_id = :ready_site_id";

                    // Prepara a consulta
                    $stmt = $conn_pdo->prepare($sql);

                    // Binde o parâmetro
                    $stmt->bindParam(':ready_site_id', $site['id'], PDO::PARAM_INT);

                    // Executa a consulta
                    $stmt->execute();

                    // Obtém os resultados
                    $siteServices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Obtém o numero de resultados
                    $countSiteServices = $stmt->rowCount();
                ?>
                <div class="card p-0 mb-3 <?php echo ($site['plan_id'] == 1 && $countSiteServices == 0) ? "d-none" : ""; ?>">
                    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Ofertas</div>
                    <div class="card-body row px-4 py-3">
                        <ul class="mb-0">
                            <?php
                                if ($site['plan_id'] != 1) {
                                    // Tabela que sera feita a consulta
                                    $tabela = "tb_plans_interval";

                                    // Sua consulta SQL com a cláusula WHERE para filtrar pelo ID
                                    $sql = "SELECT id, plan_id, billing_interval, price FROM $tabela WHERE id = :id";

                                    // Prepara a consulta
                                    $stmt = $conn_pdo->prepare($sql);

                                    // Binde o parâmetro
                                    $stmt->bindParam(':id', $site['plan_id'], PDO::PARAM_INT);

                                    // Executa a consulta
                                    $stmt->execute();

                                    // Obtém os resultados
                                    $plan_interval = $stmt->fetch(PDO::FETCH_ASSOC);

                                    // Verificar se o resultado foi encontrado
                                    if ($plan_interval) {
                                        // Tabela que sera feita a consulta
                                        $tabela = "tb_plans";

                                        // Sua consulta SQL com a cláusula WHERE para filtrar pelo ID
                                        $sql = "SELECT id, name, sub_name, resources FROM $tabela WHERE id = :id";

                                        // Prepara a consulta
                                        $stmt = $conn_pdo->prepare($sql);

                                        // Binde o parâmetro
                                        $stmt->bindParam(':id', $plan_interval['plan_id'], PDO::PARAM_INT);

                                        // Executa a consulta
                                        $stmt->execute();

                                        // Obtém os resultados
                                        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

                                        // Verificar se o resultado foi encontrado
                                        if ($plan) {
                                            if ($plan_interval['billing_interval'] == "monthly") {
                                                $billing_interval = "(mensal)";
                                                $price = $plan_interval['price'];
                                            } else {
                                                $totalPrice = $plan_interval['price'];
                                                $billing_interval = "($totalPrice/anual)";
                                                $price = $plan_interval['price'] / 12;
                                            }

                                            if ($plan_id >= $plan['id'] || $plan['id'] <= 1) {
                            ?>

                            <li class="d-flex align-items-center justify-content-between mb-1">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" class="form-check-input me-2" data-type="subscrition" data-plan-id="<?= $plan_interval['id']; ?>" data-value="<?= $plan_interval['price']; ?>" checked>
                                    <p>
                                        Plano <?= $plan['name']; ?>
                                        <i class="bx bx-help-circle edited" data-toggle="tooltip" data-placement="top" data-bs-html="true" aria-label="Assinatura do Plano <?= $plan['name']; ?> com pagamento mensal para usufruir de todos os benefícios do Site Pronto." data-bs-original-title="Assinatura do Plano <?= $plan['name']; ?> com pagamento mensal para usufruir de todos os benefícios do Site Pronto."></i>
                                    </p>
                                </div>
                                <p>
                                    R$ 
                                    <?= number_format($price, 2, ",", "."); ?> 
                                    <small><?= $billing_interval; ?></small>
                                </p>
                            </li>

                            <?php
                                            }
                                        }
                                    }
                                }
                            ?>
                            <?php
                                // Verificar se o resultado foi encontrado
                                if ($siteServices) {
                                    foreach ($siteServices as $siteService) {
                                        // Formatação preço
                                        $servicePreco = $siteService['price'];

                                        // Transforma o número no formato "R$ 149,90"
                                        $servicePrice = "R$ " . number_format($servicePreco, 2, ",", ".");

                                        // Formatação preço com desconto
                                        $serviceDesconto = $siteService['discount'];

                                        // Transforma o número no formato "R$ 149,90"
                                        $serviceDiscount = "R$ " . number_format($serviceDesconto, 2, ",", ".");

                                        // Calcula a porcentagem de desconto
                                        if ($siteService['price'] != 0) {
                                            $servicePorcentagemDesconto = (($siteService['price'] - $siteService['discount']) / $siteService['price']) * 100;
                                        } else {
                                            // Lógica para lidar com o caso em que $siteService['price'] é zero
                                            $servicePorcentagemDesconto = 0; // Ou outro valor padrão
                                        }

                                        // Arredonda o resultado para duas casas decimais
                                        $servicePorcentagemDesconto = round($servicePorcentagemDesconto, 0);

                                        if ($siteService['discount'] == "0.00") {
                                            $serviceActiveDiscount = "d-none";

                                            $servicePriceAfterDiscount = $servicePrice;

                                            $servicePriceNoFormat = $siteService['price'];
                                        } else {
                                            $serviceActiveDiscount = "";

                                            $servicePriceAfterDiscount = $serviceDiscount;
                                            $serviceDiscount = $servicePrice;

                                            $servicePriceNoFormat = $siteService['discount'];
                                        }

                                        if ($siteService['without_price'] == 1) {
                                            $serviceActiveDiscount = "d-none";
                                            $servicePriceAfterDiscount = "";
                                        }
                            ?>
                                <li class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input me-2" data-type="service" data-service-id="<?= $siteService['id']; ?>" data-value="<?= $servicePriceNoFormat; ?>">
                                        <p>
                                            <?= $siteService['name']; ?>
                                            <i class="bx bx-help-circle edited" data-toggle="tooltip" data-placement="top" data-bs-html="true" aria-label="<?= $siteService['seo_description']; ?>" data-bs-original-title="<?= $siteService['seo_description']; ?>"></i>
                                        </p>
                                    </div>
                                    <p>
                                        <small class="text-secondary text-decoration-line-through <?= $serviceActiveDiscount; ?>"><?= $serviceDiscount; ?></small>
                                        <?= $servicePriceAfterDiscount; ?>
                                    </p>
                                </li>
                            <?php
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mb-3">
                    <p class="fs-5 fw-semibold">Total:</p>
                    <div class="d-flex flex-column align-items-end">
                        <div class="d-flex align-items-end">
                            <p class="text-secondary fw-semibold text-decoration-line-through me-2 mb-0 <?= $activeDiscount; ?>"><?= $discount; ?></p>
                            <p class="fs-5 fw-semibold mb-0" id="total"><?= $priceAfterDiscount; ?></p>
                        </div>
                        <p class="text-secondary fw-semibold mb-0">12x de <?= $installmentValue; ?> sem juros</p>
                    </div>
                </div>

                <input type="hidden" name="ready_site_id" id="ready_site_id" value="<?= $site['id']; ?>">
                <input type="hidden" name="ready_site_price" id="ready_site_price" value="<?= $priceNoFormat; ?>">
                <input type="hidden" name="price" id="price" value="<?= $priceNoFormat; ?>">
                <input type="hidden" id="selectedServices" name="selectedServices" value="">

                <!-- Aqui está o seu botão. Eu adicionei um ID para poder referenciá-lo no script JavaScript -->
                <button type="submit" class="btn btn-success fw-semibold px-4 py-2 small w-100 mb-3" id="submitButton">
                    Comprar/Instalar
                </button>

                <style>
                    #loaderButton {
                        display: flex;
                        justify-content: center;
                    }

                    .loader {
                        width: 24px;
                        height: 24px;
                        border: 2.5px solid #FFF;
                        border-bottom-color: transparent;
                        border-radius: 50%;
                        display: inline-block;
                        box-sizing: border-box;
                        animation: rotation 1s linear infinite;
                    }

                    @keyframes rotation {
                        0% {
                            transform: rotate(0deg);
                        }
                        100% {
                            transform: rotate(360deg);
                        }
                    }

                </style>

                <button class="btn btn-success fw-semibold px-4 py-2 small w-100 mb-3 d-none" id="loaderButton">
                    <div class="loader"></div>
                </button>
            </div>
        </div>
    </div>
</form>

<!-- Ofertas -->
<script>
    function atualizarTotal() {
        var total = <?= $priceNoFormat; ?>;

        // Iterar sobre os checkboxes selecionados
        $('.form-check-input:checked').each(function() {
            var value = parseFloat($(this).data('value'));
            total += value;
        });

        // Converter para número
        var numero = parseFloat(total);

        // Formatar com separador de milhares e decimal
        var valorFormatado = numero.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

        // Atualizar o valor total na página
        $('#total').text("R$ " + valorFormatado);
        $('#price').val(numero);
    }

    $(document).ready(function() {
        // Adicionar evento de mudança para os checkboxes
        $('.form-check-input').change(function() {
            atualizarTotal();
        });

        // Chamar a função quando a página recarregar
        atualizarTotal();
    });
</script>

<script>
    $(document).ready(function() {
        var selectedServices = [];

        // Atualizar a lista de serviços selecionados e preparar para enviar
        function updateSelectedServices() {
            selectedServices = []; // Resetar o array

            // Adicionar informações do ready-site
            selectedServices.push({
                type: "ready-site",
                id: <?= $site['id']; ?>,
                value: <?= $priceNoFormat; ?>
            });

            // Adicionar informações dos serviços selecionados
            $('input[type="checkbox"]:checked').each(function() {
                selectedServices.push({
                    type: $(this).data('type'),
                    id: $(this).data('type') === 'subscrition' ? $(this).data('plan-id') : $(this).data('service-id'),
                    value: $(this).data('value')
                });
            });

            $('#selectedServices').val(JSON.stringify(selectedServices)); // Codificar array em JSON e setar no input oculto
        }

        // Evento de mudança para os checkboxes
        $('input[type="checkbox"]').change(function() {
            updateSelectedServices();
        });

        // Submeter o formulário
        $('#submitBtn').click(function() {
            $('#servicesForm').submit();
        });

        // Chame updateSelectedServices() para incluir as informações do ready-site
        updateSelectedServices();
    });
</script>

<?php
}
?>