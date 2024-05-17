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
    $_SESSION['msg'] = "<p class='red'>Nenhum Serviço encontrado!</p>";
    header('Location: ' . INCLUDE_PATH_DASHBOARD . 'servicos');
    exit();
}

// Tabela que sera feita a consulta
$tabela = "tb_services";

// Consulta SQL
$sql = "SELECT * FROM $tabela WHERE link = :link";

// Preparar a consulta
$stmt = $conn_pdo->prepare($sql);

// Vincular o valor do parâmetro
$stmt->bindParam(':link', $link, PDO::PARAM_INT);

// Executar a consulta
$stmt->execute();

// Obter o resultado como um array associativo
$service = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar se o resultado foi encontrado
if ($service) {
    // Nome da tabela para a busca
    $tabela = 'tb_service_img';

    $sql = "SELECT * FROM $tabela WHERE service_id = :service_id ORDER BY id DESC LIMIT 1";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':service_id', $service['id']);
    $stmt->execute();

    // Recuperar os resultados
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    $image = INCLUDE_PATH_DASHBOARD . "back-end/admin/service/" . $image['service_id'] . "/" . $image['image'];

    // Preco
    // Transforma o número no formato "R$ 149,90"
    $price = "R$ " . number_format($service['price'], 2, ",", ".");
    $discount = "R$ " . number_format($service['discount'], 2, ",", ".");

    // Calcula a porcentagem de desconto
    if ($service['price'] != 0) {
        $porcentagemDesconto = (($service['price'] - $service['discount']) / $service['price']) * 100;
    } else {
        // Lógica para lidar com o caso em que $service['price'] é zero
        $porcentagemDesconto = 0; // Ou outro valor padrão
    }

    // Arredonda o resultado para duas casas decimais
    $porcentagemDesconto = round($porcentagemDesconto, 0);

    if ($service['discount'] == "0.00") {
        $activeDiscount = "d-none";

        $priceAfterDiscount = $price;

        $priceNoFormat = $service['price'];
    } else {
        $activeDiscount = "";

        $priceAfterDiscount = $discount;
        $discount = $price;

        $priceNoFormat = $service['discount'];
    }
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
                <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD ?>sites-prontos" class="fs-5 text-decoration-none text-reset">Serviços</a></li>
                <li class="breadcrumb-item fs-4 fw-semibold active" aria-current="page"><?= $service['name']; ?></li>
            </ol>
        </nav>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/servicos" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
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
                <h4 class="mb-3"><?= $service['name']; ?></h4>
                <hr>
                <div class="description mt-3">
                    <div id="video-display" class="d-flex justify-content-center mb-3 <?php echo ($service['video'] == "") ? "d-none" : ""; ?>">
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
                                $youtubeURL = $service['video'];
                                $embedCode = getYoutubeEmbedCode($youtubeURL);

                                if ($embedCode !== 'URL do YouTube inválido.') {
                                    echo $embedCode;
                                }
                            ?>
                        </div>
                    </div>
                    <?= $service['description']; ?>
                </div>
            </div>

            <style>
                #itemsIncluded .icon
                {
                    color: var(--green-color);
                }
            </style>
            <div class="col-md-4">
                <div class="card p-0 mb-3 <?php echo ($service['items_included'] == "") ? "d-none" : ""; ?>">
                    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Benefícios</div>
                    <div class="card-body row px-4 py-3">
                        <ul id="itemsIncluded" class="mb-0">
                            <?php
                                $json_encoded = $service['items_included'];
                                
                                // Supondo que $service['items_included'] seja uma string JSON
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
                    $sql = "SELECT ss.*, s.* FROM tb_service_services ss JOIN tb_services s ON ss.associated_service_id = s.id WHERE ss.service_id = :service_id";

                    // Prepara a consulta
                    $stmt = $conn_pdo->prepare($sql);

                    // Binde o parâmetro
                    $stmt->bindParam(':service_id', $service['id'], PDO::PARAM_INT);

                    // Executa a consulta
                    $stmt->execute();

                    // Obtém os resultados
                    $serviceServices = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Obtém o numero de resultados
                    $countAssociatedServices = $stmt->rowCount();
                ?>
                <div class="card p-0 mb-3 <?php echo ($countAssociatedServices == 0) ? "d-none" : ""; ?>">
                    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Ofertas</div>
                    <div class="card-body row px-4 py-3">
                        <ul class="mb-0">
                            <?php
                                // Verificar se o resultado foi encontrado
                                if ($serviceServices) {
                                    foreach ($serviceServices as $serviceService) {
                                        // Formatação preço
                                        $servicePreco = $serviceService['price'];

                                        // Transforma o número no formato "R$ 149,90"
                                        $servicePrice = "R$ " . number_format($servicePreco, 2, ",", ".");

                                        // Formatação preço com desconto
                                        $serviceDesconto = $serviceService['discount'];

                                        // Transforma o número no formato "R$ 149,90"
                                        $serviceDiscount = "R$ " . number_format($serviceDesconto, 2, ",", ".");

                                        // Calcula a porcentagem de desconto
                                        if ($serviceService['price'] != 0) {
                                            $servicePorcentagemDesconto = (($serviceService['price'] - $serviceService['discount']) / $serviceService['price']) * 100;
                                        } else {
                                            // Lógica para lidar com o caso em que $serviceService['price'] é zero
                                            $servicePorcentagemDesconto = 0; // Ou outro valor padrão
                                        }

                                        // Arredonda o resultado para duas casas decimais
                                        $servicePorcentagemDesconto = round($servicePorcentagemDesconto, 0);

                                        if ($serviceService['discount'] == "0.00") {
                                            $serviceActiveDiscount = "d-none";

                                            $servicePriceAfterDiscount = $servicePrice;

                                            $servicePriceNoFormat = $serviceService['price'];
                                        } else {
                                            $serviceActiveDiscount = "";

                                            $servicePriceAfterDiscount = $serviceDiscount;
                                            $serviceDiscount = $servicePrice;

                                            $servicePriceNoFormat = $serviceService['discount'];
                                        }

                                        if ($serviceService['without_price'] == 1) {
                                            $serviceActiveDiscount = "d-none";
                                            $servicePriceAfterDiscount = "";
                                        }
                            ?>
                                <li class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" class="form-check-input me-2" data-type="service" data-service-id="<?= $serviceService['id']; ?>" data-value="<?= $servicePriceNoFormat; ?>">
                                        <p>
                                            <?= $serviceService['name']; ?>
                                            <i class="bx bx-help-circle edited" data-toggle="tooltip" data-placement="top" data-bs-html="true" aria-label="<?= $serviceService['seo_description']; ?>" data-bs-original-title="<?= $serviceService['seo_description']; ?>"></i>
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
                            <p class="text-secondary fw-semibold text-decoration-line-through me-2 mb-0"><?= $discount; ?></p>
                            <p class="fs-5 fw-semibold mb-0" id="total"><?= $priceAfterDiscount; ?></p>
                        </div>
                        <p class="text-secondary fw-semibold mb-0">12x de R$ 50,57 com juros</p>
                    </div>
                </div>

                <input type="hidden" name="ready_site_id" id="ready_site_id" value="<?= $id; ?>">
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
    $(document).ready(function() {
        // Adicionar evento de mudança para os checkboxes
        $('.form-check-input').change(function() {
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
        });
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
                type: "service",
                id: <?= $id; ?>,
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
} else {
    $_SESSION['msg'] = "<p class='red'>Nenhum Serviço encontrado!</p>";
    header('Location: ' . INCLUDE_PATH_DASHBOARD . 'servicos');
}
?>