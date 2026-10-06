<?php
    // Nome da tabela para a busca
    $tabela = 'tb_users';

    $sql = "SELECT * FROM $tabela WHERE id = :id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id);
    $stmt->execute();

    // Obter o resultado como um array associativo
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!-- Codigo da Imagem dos produtos -->
<style>
    label.image-container {
        background-color: #f9f9f9;
        width: 100%;
        padding: 3.12em 1.87em;
        border: 2px dashed #c4c4c4;
        border-radius: 0.5em;
        cursor: pointer;
    }
    input[type="file"] {
        display: none;
    }
    .dropzone {
        min-height: 0px;
        display: block;
        position: relative;
        color: #000;
        background: none;
        font-size: 1.1em;
        text-align: center;
        padding: 1em 0;
        border: none;
        border-radius: 0.3em;
        margin: 0 auto;
        cursor: pointer;
    }
    .sortable-container {
        overflow: hidden;
    }
    #image-display {
        position: relative;
        width: 100%;
        display: flex;
        gap: 1.25em;
        flex-wrap: wrap;
    }
    #image-display figure {
        position: relative;
        width: 118px;
        height: 118px;
        background: #f9f9f9;
    }
    #image-display img {
        width: 118px;
        height: 118px;
        object-fit: cover;
        border: 1px solid #c4c4c4;
        border-radius: .5rem;
    }
    #image-display img:hover
    {
        cursor: -webkit-grab;
        cursor: grab;
    }
    #image-display button {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 30px;
        height: 30px;
        border: 1px solid #c4c4c4;
        border-radius: 0.3rem;
        background: #f9f9f9;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: .3s;
    }
    #image-display button::before {
        -webkit-text-stroke: 2px #f4f6f8;
        align-items: center;
        border-radius: 8px;
        color: #666;
        content: "\f00d";
        display: flex;
        font-family: Font Awesome\ 5 Free;
        font-size: 22px;
        font-weight: 700;
        height: 32px;
        justify-content: center;
    }
    #image-display figcaption {
        font-size: 0.8em;
        text-align: center;
        color: #5a5861;
    }
    .sortable-image:first-of-type::before {
        content: 'Capa';
        position: absolute;
        left: 10px;
        bottom: 10px;
        color: #c4c4c4;
        font-size: .875rem;
        background: #f9f9f9;
        padding: 0 0.5rem;
        border: 1px solid #c4c4c4;
        border-radius: 0.3rem;
    }
    .dropzone-active {
        border: 0.2em dashed #025bee;
    }
    #error {
        text-align: center;
        color: #ff3030;
    }
</style>

<!-- Codigo do site -->
<style>
    #imagePreviews {
        display: flex;
        flex-wrap: wrap;
    }

    .image-preview {
        position: relative;
        margin-right: 10px;
    }

    .image-preview img {
        max-width: 100px;
        height: auto;
        cursor: pointer;
    }

    .remove-image {
        position: absolute;
        top: 0;
        right: 0;
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        padding: 2px 5px;
        cursor: pointer;
    }

    /* Image */
    #imageInput
    {
        display: none;
    }
    /* .dropzone
    {
        height: 100px;
        background: #c4c4c4;
    } */
    
    /* SEO Preview */
    .seo-preview
    {
        border: 2px dashed #ddd;
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

    /* Links */
    .links .btn
    {
        color: #fff !important;
    }
    .btn-whatsapp
    {
        background: #2cd46b !important;
        border-color: #2cd46b !important;
    }
    .btn-whatsapp:hover,
    .btn-whatsapp:active
    {
        background: #21b157 !important;
        border-color: #21b157 !important;
    }
    .btn-facebook
    {
        background: #106bff !important;
        border-color: #106bff !important;
    }
    .btn-facebook:hover,
    .btn-facebook:active
    {
        background: #0c4fbb !important;
        border-color: #0c4fbb !important;
    }
    .btn-linkedin
    {
        background: #0a78b5 !important;
        border-color: #0a78b5 !important;
    }
    .btn-linkedin:hover,
    .btn-linkedin:active
    {
        background: #065581 !important;
        border-color: #065581 !important;
    }
</style>

<style>
    #loaderButton {
        display: flex;
        justify-content: center;
    }

    .loader {
        width: 24px;
        height: 24px;
        border: 2.5px solid #FFF;
        border-bottom-color: transparent !important;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
    }

    #send-emails .loader {
        width: 16px;
        height: 16px;
        border: 2px solid #212529;
    }
    
    #send-emails:hover .loader,
    #send-emails:active .loader {
        border-color: #fff;
    }

    .resend-email .loader {
        width: 16px;
        height: 16px;
        border: 2px solid #FFF;
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

<div class="page__header center">
    <div class="header__title">
        <div class="page__header center">
            <div class="header__title">
                <h2 class="title">Indique e Ganhe</h2>
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

<div class="card mb-3 p-0">
    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Indique e Ganhe desconto progressivo</div>
    <div class="card-body row px-4 py-3">
        <p class="fw-semibold fs-5">Como funciona</p>
        <p class="fw-semibold mb-2">Indique a Dropi Digital e ganhe até 100% do seu plano de mensalidade.</p>
        <ul class="list-style-one ms-2 mb-2">
            <li><i class="bx bx-check"></i>Ganhe 10% de desconto para cada amigo que fizer a contratação de um dos nossos serviços, através de indicação.</li>
            <li><i class="bx bx-check"></i>Quato mais indicar, mais você ganha.</li>
            <li><i class="bx bx-check"></i>Aqueles que mais indicarem no mês, ganharão uma melhoria exclusica para seu projeto.</li>
        </ul>
        <p class="text-secondary small">*A indicação é considerada válida assim que o indicado tiver a conta aprovada e fizer o primeiro uso de produto (criação de cobrança, emissão de nota fiscal de serviço ou consulta Serasa). O crédito promocional é válido por 3 meses, a contar da data de recebimento do crédito. O crédito promocional não pode ser sacado ou transferido.</p>
    </div>
</div>

<?php
    // Nome da tabela para a busca
    $tabela = 'tb_indication';

    $sql = "SELECT COUNT(*) as total_pending FROM $tabela WHERE status != :status AND indicator_id = :indicator_id";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindValue(':status', 'bought-something');
    $stmt->bindParam(':indicator_id', $user['id']);
    $stmt->execute();

    // Recuperar os resultados
    $indication['total_pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_pending'];

    $dueDate = date('Y-m-d');

    // Nome da tabela para a busca
    $tabela = 'tb_rewards';

    // Obter a data atual no formato YYYY-MM-DD
    $dueDate = date('Y-m-d');

    $sql = "SELECT COUNT(*) as total_purchases FROM $tabela WHERE indicator_id = :indicator_id AND due_date >= :due_date";

    // Preparar e executar a consulta
    $stmt = $conn_pdo->prepare($sql);
    $stmt->bindParam(':indicator_id', $user['id']);
    $stmt->bindParam(':due_date', $dueDate);
    $stmt->execute();

    // Recuperar os resultados
    $indication['total_purchases'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_purchases'];

    $link = INCLUDE_PATH . "r/" . $user['referral_code'];
?>

<div class="card mb-3 p-0">
    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Indique para seus amigo</div>
    <div class="card-body px-4 py-3">
        <p class="fw-semibold fs-5 mb-2">Links</p>
        <!-- Mensagem de feedback -->
        <div id="feedback" class="mt-2"></div>
        <div class="links d-flex mb-3">
            <a href="https://api.whatsapp.com/send?text=Confira está plataforma site catálogo <?= $link; ?>" target="_blank" class="btn btn-whatsapp me-2">
                <i class='bx bxl-whatsapp' ></i>
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $link; ?>" target="_blank" class="btn btn-facebook me-2">
                <i class='bx bxl-facebook' ></i>
            </a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $link; ?>" target="_blank" class="btn btn-linkedin me-2">
                <i class='bx bxl-linkedin' ></i>
            </a>
            <button type="button" class="btn btn-secondary" id="copy-link-btn" data-link="<?= $link; ?>">
                <i class='bx bx-link' ></i>
            </button>
        </div>
        <label for="email" class="form-label small">Convide seus amigos por e-mail</label>
        <div id="response-message" class="mb-2"></div>
        <div class="input-group">
            <input type="text" class="form-control" name="email"  id="email-input" placeholder="Informe o e-mail" aria-label="Informe o e-mail" aria-describedby="email" style="max-width: 400px;">
            <button class="btn btn-outline-dark d-flex align-items-center fw-semibold px-4" type="button" id="send-emails"><div class="loader me-2 d-none"></div>Enviar e-mail</button>
        </div>
        <small class="text-secondary mb-3">Se desejar adicionar mais de um e-mail, separe-os com um espaço.</small>
        <input type="hidden" id="email-list-input" name="emails">
        <div id="email-list" class="mb-3"></div>
        <p class="fw-semibold fs-5 mb-2">Amigos indicados</p>
        <div class="d-flex">
            <div class="card py-3 me-2">
                <div class="d-flex align-items-center mb-2">
                    <i class='bx bx-user me-1' ></i>
                    <p>Total Ativo</p>
                </div>
                <p class="fw-semibold"><?= $indication['total_purchases']; ?></p>
            </div>
            <div class="card py-3 me-2">
                <div class="d-flex align-items-center mb-2">
                    <i class='bx bx-time-five me-1' ></i>
                    <p>Aguardando Compra</p>
                </div>
                <p class="fw-semibold"><?= $indication['total_pending']; ?></p>
            </div>
        </div>
        <div class="container overflow-x-auto mt-3" style="top: 0;">
            <div id="resend-message" class="mb-2"></div>
            <table>
                <thead>
                    <tr>
                        <th class="small">Nome</th>
                        <th class="small">Contato</th>
                        <th class="small">Situação</th>
                        <th class="small">Data</th>
                        <th class="small">Eventos</th>
                    </tr>
                </thead>
                <?php
                // Nome da tabela para a busca
                $tabela = 'tb_indication';

                // Configuração para paginação
                $limite = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
                $paginaAtual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
                $inicioConsulta = ($paginaAtual - 1) * $limite;

                // Preparar a consulta com base na pesquisa (se houver)
                $sql = "SELECT * FROM $tabela WHERE indicator_id = :indicator_id";
                if (!empty($_GET['search'])) {
                    $searchTerm = '%' . $_GET['search'] . '%';
                    $sql .= " AND (name LIKE :searchTerm OR sku LIKE :searchTerm)"; // Substitua campo1 e campo2 pelos campos que deseja pesquisar
                }

                $sql .= " ORDER BY id DESC LIMIT :inicioConsulta, :limite";

                // Preparar e executar a consulta
                $stmt = $conn_pdo->prepare($sql);
                $stmt->bindParam(':indicator_id', $user['id']);
                if (!empty($_GET['search'])) {
                    $stmt->bindParam(':searchTerm', $searchTerm);
                }
                $stmt->bindParam(':inicioConsulta', $inicioConsulta, PDO::PARAM_INT);
                $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
                $stmt->execute();

                // Recuperar os resultados
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Loop através dos resultados e exibir todas as colunas
                foreach ($resultados as $indication) {
                    $tabela = 'tb_users';
                    $query = "SELECT name, email FROM $tabela WHERE id = :id LIMIT 1";
                    $stmt = $conn_pdo->prepare($query);
                    $stmt->bindParam(':id', $indication['guest_id']);
                    $stmt->execute();
                    
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user) {
                        $indication['guest_name'] = $user['name'];
                        $indication['guest_email'] = $user['email'];
                    } else {
                        $indication['guest_name'] = "-- Aguardando Cadastro --";
                        $indication['guest_email'] = (!empty($indication['guest_email'])) ? $indication['guest_email'] : "-- Não Informado --";
                    }

                    //Formatacao para data
                    $indication['date_create'] = date("d/m/Y", strtotime($indication['date_create']));

                    // Status
                    // Array com os textos dos status em português
                    $statusTranslations = array(
                        'pending' => 'Pendente',
                        'sending' => 'Enviado',
                        'account-created' => 'Conta Criada',
                        'bought-something' => 'Comprou Algo'
                    );
                    
                    $status = $indication['status'];
                    $indication['translated_status'] = $statusTranslations[$status];

                    echo '
                        <tbody style="overflow: auto;">
                            <tr>
                                <td>
                                    ' . $indication['guest_name'] . '
                                </td>
                                <td>' . $indication['guest_email'] . '</td>
                                <td>' . $indication['translated_status'] . '</td>
                                <td>' . $indication['date_create'] . '</td>
                                <td>
                    ';

                                if ($indication['status'] == "sending") {
                                    echo '
                                        <button type="button" class="resend-email btn btn-secondary" data-email="' . $indication['guest_email'] . '">
                                            <div class="loader d-none"></div>
                                            <i class="bx bx-rotate-left" ></i>
                                        </button>
                                    ';
                                }

                    echo '
                                </td>
                            </tr>
                        </tbody>
                    ';
                }
            ?>
            </table>
        </div>
    </div>
</div>

<!-- Link para o TinyMCE CSS -->
<script src="https://cdn.tiny.cloud/1/xiqhvnpyyc1fqurimqcwiz49n6zap8glrv70bar36fbloiko/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!-- jQuery and jQuery UI -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<!-- Copy Link -->
<script>
    $(document).ready(function() {
        $('#copy-link-btn').on('click', function() {
            var link = $(this).data('link');
            
            // Criar um elemento de input temporário para copiar o link
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(link).select();
            
            try {
                document.execCommand('copy');
                $('#feedback').html('<div class="alert alert-success alert-dismissible fade show py-2">Link copiado para a área de transferência!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button></div>');
            } catch (err) {
                $('#feedback').html('<div class="alert alert-danger alert-dismissible fade show py-2">Falha ao copiar o link!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button></div>');
            }
            
            // Remover o elemento de input temporário
            tempInput.remove();
        });
    });
</script>

<!-- Tooltip -->
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<!-- Resend Email -->
<script>
    $('.resend-email').on('click', function() {
        // Encontra o loader dentro do botão clicado e remove a classe d-none para mostrá-lo
        var loader = $(this).find('.loader');
        var icon = $(this).find('i');
        loader.removeClass('d-none');
        icon.addClass('d-none');

        // Email
        var email = $(this).data('email');

        if (email !== "") {
            // Envia os emails via AJAX
            $.ajax({
                url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/refer_and_win.php', // URL para o arquivo PHP que processará o envio dos emails
                type: 'POST',
                data: {
                    action: 'resend-email',
                    user_id: <?= $user_id; ?>,
                    email: email
                },
                success: function(response) {
                    loader.addClass('d-none');
                    icon.removeClass('d-none');
                    $('#resend-message').html('<div class="alert alert-success alert-dismissible fade show py-2">Email reenviado com sucesso!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button></div>');
                },
                error: function() {
                    loader.addClass('d-none');
                    icon.removeClass('d-none');
                    $('#resend-message').html('<div class="alert alert-danger alert-dismissible fade show py-2">Ocorreu um erro ao reenviar o email.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button></div>');
                }
            });
        } else {
            loader.addClass('d-none');
            icon.removeClass('d-none');
            $('#resend-message').html('<div class="alert alert-warning alert-dismissible fade show py-2">Por favor, adicione um email válido.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button></div>');
        }
    });
</script>

<!-- Send Email -->
<script>
    $(document).ready(function() {
        $('#email-input').on('keypress', function(e) {
            if (e.which === 13 || e.which === 32) { // Código ASCII para enter e espaço
                e.preventDefault();
                let email = $(this).val().trim();
                if (validateEmail(email)) {
                    addEmailTag(email);
                    $(this).val('');
                } else {
                    alert('Por favor, insira um email válido.');
                }
            }
        });

        $('#email-list').on('click', '.remove-btn', function() {
            $(this).parent().remove();
        });

        $('#send-emails').on('click', function() {
            // Encontra o loader dentro do botão clicado e remove a classe d-none para mostrá-lo
            var loader = $('#send-emails .loader');
            loader.removeClass('d-none');

            let emails = [];

            // Coleta os emails das tags na lista
            $('#email-list .email-tag').each(function() {
                emails.push($(this).text().trim().replace(/\s*×$/, ''));
            });

            // Verifica se a lista de emails está vazia
            if (emails.length === 0) {
                // Pega o valor do campo de entrada
                let inputEmail = $('#email-input').val().trim();
                if (validateEmail(inputEmail)) {
                    emails.push(inputEmail);
                }
            }

            // Atualiza o campo oculto com os emails coletados
            $('#email-list-input').val(emails.join(','));

            if (emails.length > 0) {
                // Envia os emails via AJAX
                $.ajax({
                    url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/refer_and_win.php', // URL para o arquivo PHP que processará o envio dos emails
                    type: 'POST',
                    data: {
                        action: 'send-email',
                        user_id: <?= $user_id; ?>,
                        emails: $('#email-list-input').val()
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        loader.addClass('d-none');
                        $('#response-message').html('<div class="alert alert-danger alert-dismissible fade show py-2">Ocorreu um erro ao enviar os emails.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button></div>');
                    }
                });

                // Limpa a lista e o campo de entrada após o envio
                $('#email-list').empty();
                $('#email-input').val('');
            } else {
                loader.addClass('d-none');
                $('#response-message').html('<div class="alert alert-warning alert-dismissible fade show py-2">Por favor, adicione um email válido.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="height: 42px; padding: 0 1rem;"></button></div>');
            }
        });

        function addEmailTag(email) {
            let emailTag = `<small class="d-inline-flex me-1 mb-1 px-2 py-0 fw-semibold text-secondary-emphasis bg-secondary-subtle border border-secondary-subtle rounded-1 email-tag">${email} <span role="button" tabindex="0" class="remove-btn ms-1">&times;</span></small>`;
            $('#email-list').append(emailTag);
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
</script>