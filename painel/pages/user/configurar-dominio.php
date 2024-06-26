<?php
// Nome da tabela para a busca
$tabela = 'tb_shop';

$sql = "SELECT * FROM $tabela WHERE id = :id";

// Preparar e executar a consulta
$stmt = $conn_pdo->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();

// Recuperar os resultados
$shop = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<style>
    /* Link */
    .link
    {
        text-decoration: none;
    }
    .link:hover
    {
        text-decoration: underline;
    }

    /* Botao */
    .btn.btn-success
    {
        background: var(--green-color);
        font-size: .875rem;
        border: none;
        padding: .75rem 1.5rem;
    }
    .btn.btn-success:hover
    {
        background: var(--dark-green-color);
    }
    .btn.current
    {
        color: var(--bs-heading-color);
        background: #e8e9eb !important;
    }

    /* Copy */
    #copyDestiny
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

<div class="page__header center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title">
                <h2 class="title">Domínio próprio</h2>
            </div>
        </div>
    </div>
    <div class="header__actions">
        <div class="container__button">
            <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>ajuda/criar-banner" class="link-secondary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover text-decoration-none d-flex align-items-center">
                <i class='bx bx-help-circle me-1' ></i>
                <b>Obtenha ajuda sobre</b>
            </a>
        </div>
    </div>
</div>

<?php
    if ($plan['plan_id'] <= 1) {
?>

<div class="card mb-3 p-0">
    <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
        Configurar seu domínio
    </div>
    <div class="card-body row px-4 py-3">
        <div class="notice">
            <p class="fs-4 fw-semibold mb-3">Para personalizar seu domínio, é necessário adquirir um plano superior!</p>
            <p class="mb-2">Você terá a flexibilidade de utilizar seu próprio domínio, proporcionando uma presença online mais profissional e alinhada com as necessidades específicas do seu projeto ou negócio. Desbloqueie todo o potencial do seu site com nossas opções de plano avançadas.</p>
            <div class="container-button">
                <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center mb-3" style="height: 42px;">
                    Ver planos
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="ms-1" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3z"></path><path d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z"></path></svg>
                </a>
            </div>
            <small>Caso tenha alguma dúvida, por favor entre em contato conosco pelo e-mail <a href="mailto:suporte@dropidigital.com.br" class="d-inline-flex align-items-center fw-semibold text-decoration-none" style="color: var(--bs-body-color);">suporte@dropidigital.com.br</a></small>
        </div>
    </div>
</div>

<?php
    } else {
?>

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
        $domain_url = "https://" . $subdomain . $domain['domain'];
    }

    if (empty($domain)) {
?>

<form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/add_domain.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Configurar seu domínio
        </div>
        <div class="card-body row px-4 py-3">
            <div class="step-1">
                <p class="fs-5 fw-semibold mb-3">Passo 1</p>
                <p class="small mb-3">Leia as instruções clicando no botão abaixo. É rápido e você evita problemas de configuração <span class="fw-semibold">pois o domínio e o certificado podem levar até 48 horas para entrar no ar</span>. Já imaginou descobrir que configurou incorretamente somente depois desse período?</p>
                <div class="container-button">
                    <a href="#" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center mb-3" style="height: 42px;">
                        Ver como configurar o domínio
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="ms-1" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3z"></path><path d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z"></path></svg>
                    </a>
                </div>
                <div class="line my-3"></div>
            </div>
            <div class="step-2">
                <style>
                    .bd-callout {
                        --bs-link-color-rgb: var(--bd-callout-link);
                        --bs-code-color: var(--bd-callout-code-color);
                        padding: 1rem;
                        margin-top: 1rem;
                        margin-bottom: 1.25rem;
                        color: var(--bd-callout-color, inherit);
                        background-color: var(--bd-callout-bg, var(--bs-gray-100));
                        border-left: 0.25rem solid var(--bd-callout-border, var(--bs-gray-300));
                    }
                    .bd-callout-warning
                    {
                        --bd-callout-color: var(--bs-warning-text-emphasis);
                        --bd-callout-bg: var(--bs-warning-bg-subtle);
                        --bd-callout-border: var(--bs-warning-border-subtle);
                    }
                </style>
                <div class="bd-callout bd-callout-warning mb-3">
                    <input class="form-check-input itemCheckbox" type="checkbox" name="step" id="step" value="1" required>
                    <label for="step" class="fw-semibold">Li as instruções e estou ciente que as alterações podem levar até 48 horas para começar a funcionar.</label>
                </div>
                <p class="fs-5 fw-semibold mb-3">Passo 2</p>
                <p class="small mb-3">Preencha o domínio e clique no botão Adicionar.</p>
                <div class="d-flex mb-3">
                    <div class="w-20">
                        <input type="text" class="form-control" name="subdomain" id="subdomain" aria-describedby="subdomainHelp" value="www" placeholder="ex: loja" disabled required>
                    </div>
                    <span class="d-flex align-items-center mx-2" style="height: 38px;">.</span>
                    <div class="w-100 me-2">
                        <input type="text" class="form-control" name="domain" id="domain" aria-describedby="domainHelp" placeholder="ex: meudominio.com.br" required>
                        <small>Não use https:// ou www</small>
                    </div>
                    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center ms-2" style="height: 38px;">Adicionar</button>
                </div>
                <div>
                    <input class="form-check-input itemCheckbox" type="checkbox" name="setSubdomain" id="setSubdomain" value="1">
                    <label for="setSubdomain" class="d-inline-flex align-items-center">
                        Desejo definir um Subdomínio. Ex.: loja.meudominio.com.br
                        <i class='bx bx-info-circle ms-1'></i>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
    } else if ($domain['configure'] == 0) {
?>

<div class="card mb-3 p-0">
    <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
        Configurar seu domínio
    </div>
    <div class="card-body row px-4 py-3">
        <div class="step-3">
            <form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/remove_domain.php" method="post">
                <div class="d-flex align-items-center mb-3">
                    <a href="<?php echo $domain_url; ?>" target="_black" class="d-inline-flex align-items-center fw-semibold text-decoration-none" style="color: var(--bs-body-color);"><?php echo $domain['subdomain'] . "." . $domain['domain']; ?></a>

                    <input type="hidden" name="id" value="<?php echo $domain['id']; ?>">
                    <button type="submit" class="d-flex ms-1 border-0 bg-transparent" name="removeDomain" id="removeDomain">
                        <i class='bx bx-trash fs-5' ></i>
                    </button>
                </div>
            </form>
            <form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/configure_domain.php" method="post">
                <p class="fs-5 fw-semibold mb-3">Passo 3</p>
                <p class="small mb-3">Acesse o painel de configurações do seu provedor de domínio na tela onde são criados novos apontamentos ou entradas DNS. Copie a informação de destino que está abaixo e insira no apontamento de DNS para funcionar corretamente.</p>
                <div class="container-button">
                    <a href="#" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center mb-3" style="height: 42px;">
                        Ver como configurar o domínio
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" class="ms-1" style="fill: rgba(255, 255, 255, 1);transform: ;msFilter:;"><path d="m13 3 3.293 3.293-7 7 1.414 1.414 7-7L21 11V3z"></path><path d="M19 19H5V5h7l-2-2H5c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2v-5l-2-2v7z"></path></svg>
                    </a>
                </div>
                <div class="card mb-3">
                    <p class="d-flex align-items-center fw-semibold mb-3">
                        <i class='bx bx-error fs-4 text-danger me-1'></i>
                        Entrada A
                    </p>
                    <ul class="mb-0">
                        <li><span class="fw-semibold">Tipo:</span> A</li>
                        <li><span class="fw-semibold">Nome:</span> @</li>
                        <li><span class="fw-semibold">Destino:</span> 162.241.60.59 <i class='bx bxs-copy' id="copyDestiny"></i></li>
                        <li><span class="fw-semibold">TTL:</span> 14400</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <small>Lembrando que após efetuar a configuração a propagação do DNS pode demorar até 48 horas.</small>
                    
                    <input type="hidden" id="destiny" value="162.241.60.59">
                    <input type="hidden" name="id" value="<?php echo $domain['id']; ?>">
                    <input type="hidden" name="shop_id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center ms-2" style="height: 38px;">Tudo pronto!</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
    } else if ($domain['configure'] == 1) {
        if ($domain['status'] == 0) {
?>

<div class="card mb-3 p-0">
    <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
        Configurar seu domínio
    </div>
    <div class="card-body row px-4 py-3">
        <div class="step-3">
            <form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/remove_domain.php" method="post">
                <div class="d-flex align-items-center mb-3">
                    <a href="<?php echo $domain_url; ?>" target="_black" class="d-inline-flex align-items-center fw-semibold text-decoration-none" style="color: var(--bs-body-color);">
                        <div class="bullet success me-2"></div>
                        <?php echo $domain['subdomain'] . "." . $domain['domain']; ?>
                    </a>

                    <input type="hidden" name="id" value="<?php echo $domain['id']; ?>">
                    <button type="submit" class="d-flex ms-1 border-0 bg-transparent" name="removeDomain" id="removeDomain">
                        <i class='bx bx-trash fs-5' ></i>
                    </button>
                </div>
            </form>
            
            <p class="fs-4 fw-semibold mb-3">Tudo pronto!</p>
            <p class="fs-5">Seu domínio <a href="<?php echo $domain_url; ?>" class="d-inline-flex align-items-center fw-semibold text-decoration-none" style="color: var(--bs-body-color);"><?php echo $domain['subdomain'] . "." . $domain['domain']; ?></a> já foi adiciondo com sucesso!</p>
            <p class="mb-3">Lembrando que após efetuar a configuração a propagação do DNS pode demorar até 48 horas. Por favor aguarde!</p>
            <small>Caso já tenha passado o prazo de 48 horas e seu domínio não está funcionando, por favor entre em contato conosco pelo e-mail <a href="mailto:suporte@dropidigital.com.br" class="d-inline-flex align-items-center fw-semibold text-decoration-none" style="color: var(--bs-body-color);">suporte@dropidigital.com.br</a></small>
        </div>
    </div>
</div>

<?php
        } else {
?>
    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Domínio
        </div>
        <div class="card-body row px-4 py-3">
            <div class="step-3">
                <form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/remove_domain.php" method="post">
                    <div class="d-flex align-items-center">
                        <a href="<?php echo $domain_url; ?>" target="_black" class="d-inline-flex align-items-center fw-semibold text-decoration-none" style="color: var(--bs-body-color);">
                            <div class="bullet success me-2"></div>
                            <?php echo $domain['subdomain'] . "." . $domain['domain']; ?>
                        </a>

                        <input type="hidden" name="id" value="<?php echo $domain['id']; ?>">
                        <button type="submit" class="d-flex ms-1 border-0 bg-transparent" name="removeDomain" id="removeDomain">
                            <i class='bx bx-trash fs-5' ></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
        }
    }
?>

<?php
    }
?>

<div class="card mb-3 p-0">
    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Certificado Digital de Segurança</div>
    <div class="card-body row px-4 py-3">
        <small class="mb-3">Estando tudo certo, seu site receberá um certificado SSL gratuito e a criação de um Sitemap automático no seu site.</small>
        <small class="fw-semibold">Certificado SSL</small>
        <small class="d-flex align-items-center">

            <?php
                if ($plan['plan_id'] <= 1) {
                    $bullet = "success";
                } else {
                    if (empty($domain)) {
                        $bullet = "success";
                    } else if ($domain['configure'] == 0) {
                        $bullet = "danger";
                    } else if ($domain['configure'] == 1) {
                        if ($domain['status'] == 0) {
                            $bullet = "warning";
                        } else {
                            $bullet = "success";
                        }
                    }
                }
            ?>
            <div class="bullet <?php echo $bullet; ?> me-2"></div>

            SHA-256 bits
        </small>
    </div>
</div>
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

    $subdomain_url = "https://" . $domain['subdomain'] . "." . $domain['domain'];
?>
<form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_subdomain.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Subdomínio</div>
        <div class="card-body row px-4 py-3">
            <small class="mb-3">Você terá um subdomínio, que será o endereço online do seu site, você pode também comprar um domínio no Registro BR por exemplo, para ter seu domínio próprio de endereço Web.</small>
            <small class="d-flex align-items-center fw-semibold" id="currentSubdomain">
                <div class="me-2" style="width: 8px; height: 8px; border-radius: 50%; background: var(--green-color);"></div>
                <a href="<?php echo $subdomain_url; ?>" target="_black" class="link text-dark fw-semibold"><?php echo $domain['subdomain'] . "." . $domain['domain']; ?></a>
                <div class="text-dark ms-2"><i class='bx bx-pencil fs-5' id="showCurrentSubdomain" data-toggle="tooltip" data-placement="top" title="Alterar subdomínio"></i></div>
            </small>
            
            <div class="d-none" id="editCurrentSubdomain">
                <div class="w-50">
                    <input type="text" class="form-control" name="subdomain" id="subdomain" aria-describedby="subdomainHelp" value="<?php echo $domain['subdomain']; ?>" required>
                </div>
                <span class="d-flex align-items-center mx-2" style="height: 38px;">.</span>
                <div class="w-50 me-2">
                    <input type="text" class="form-control" name="domain" id="domain" aria-describedby="domainHelp" placeholder="dropidigital.com.br" disabled>
                </div>
                <input type="hidden" name="shop_id" value="<?php echo $id; ?>">
                <input type="hidden" name="id" value="<?php echo $domain['id']; ?>">
                <button type="submit" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center ms-2" style="height: 38px;">Adicionar</button>
            </div>
        </div>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // Adiciona um ouvinte de evento ao checkbox
        $('#setSubdomain').change(function () {
            // Obtém o valor atual do input
            var inputValue = $('#subdomain').val();

            // Verifica se o checkbox está selecionado
            if (this.checked) {
                // Se selecionado, remove o valor do input
                $('#subdomain').val('');
            } else {
                // Se não selecionado, adiciona "www" como valor do input
                $('#subdomain').val('www');
            }

            // Habilita ou desabilita o input com base no estado do checkbox
            $('#subdomain').prop('disabled', !this.checked);
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Adiciona um ouvinte de evento ao elemento com ID #currentSubdomain
        $('#showCurrentSubdomain').click(function () {
            // Remove a classe d-flex e adiciona a classe d-none ao elemento com ID #currentSubdomain
            $("#currentSubdomain").addClass('d-none');

            // Remove a classe d-none e adiciona a classe d-flex ao elemento com ID #editCurrentSubdomain
            $('#editCurrentSubdomain').removeClass('d-none').addClass('d-flex');
        });
    });
</script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.10/clipboard.min.js"></script>

<script>
    $(document).ready(function() {
        // Quando o botão for clicado
        $("#copyDestiny").click(function() {
            // Seleciona o texto do input
            var textToCopy = $("#destiny").val();

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