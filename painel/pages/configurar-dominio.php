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

    /* Linha */
    .line
    {
        width: 100%;
        height: 1px;
        background: var(--bs-card-border-color);
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

<form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/add_domain.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
            Configurar seu domínio
        </div>
        <div class="card-body row px-4 py-3">
            <div class="step-1">
                <p class="fw-semibold mb-3">Passo 1</p>
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
                    <input class="form-check-input itemCheckbox" type="checkbox" name="step" id="step" value="1">
                    <label for="step" class="fw-semibold">Li as instruções e estou ciente que as alterações podem levar até 48 horas para começar a funcionar.</label>
                </div>
                <p class="fw-semibold mb-3">Passo 2</p>
                <p class="small mb-3">Preencha o domínio e clique no botão Adicionar.</p>
                <div class="d-flex mb-3">
                    <div class="w-20">
                        <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" value="www" disabled>
                    </div>
                    <span class="d-flex align-items-center mx-2" style="height: 38px;">.</span>
                    <div class="w-100 me-2">
                        <input type="text" class="form-control" name="domain" id="domain" aria-describedby="domainHelp" placeholder="ex: meudominio.com.br" required>
                        <small>Não use https:// ou www</small>
                    </div>
                    <button type="submit" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center ms-2" style="height: 38px;">Adicionar</button>
                </div>
                <div>
                    <input class="form-check-input itemCheckbox" type="checkbox" name="subdomain" id="subdomain" value="1">
                    <label for="subdomain" class="d-inline-flex align-items-center">
                        Desejo definir um Subdomínio. Ex.: loja.meudominio.com.br
                        <i class='bx bx-info-circle ms-1'></i>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="card mb-3 p-0">
    <div class="card-header fw-semibold px-4 py-3 bg-transparent">Certificado Digital de Segurança</div>
    <div class="card-body row px-4 py-3">
        <small class="mb-3">Com seu domínio configurado, sua loja receberá um certificado digital, automaticamente e sem custo algum!<br>
        Com isso seus clientes navegam em um ambiente seguro, além de garantir melhor posicionamento de sua loja nos mecanismos de busca.</small>
        <small class="fw-semibold">Certificado SSL</small>
        <small class="d-flex align-items-center">
            <div class="me-2" style="width: 8px; height: 8px; border-radius: 50%; background: var(--green-color);"></div>
            SHA-256 bits
        </small>
    </div>
</div>

<form id="myForm" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/edit_subdomain.php" method="post">
    <div class="card mb-3 p-0">
        <div class="card-header fw-semibold px-4 py-3 bg-transparent">Subdomínio na Loja integrada</div>
        <div class="card-body row px-4 py-3">
            <small class="mb-3">Oferecemos gratuitamente um endereço virtual para sua loja através do subdomínio abaixo. <div class="fw-semibold">Esta é uma opção alternativa caso não tenha adquirido um domínio, portanto não é necessário editar.</div></small>
            <small class="d-flex align-items-center fw-semibold">
                <div class="me-2" style="width: 8px; height: 8px; border-radius: 50%; background: var(--green-color);"></div>
                <a href="#" class="link text-dark fw-semibold">minervabookstore.lojaintegrada.com.br</a>
                <a href="#" class="text-dark ms-2"><i class='bx bx-pencil fs-5' ></i></a>
            </small>
            
            <div class="d-flex">
                <div class="w-50">
                    <input type="text" class="form-control" name="name" id="name" aria-describedby="nameHelp" value="minha-loja" required>
                </div>
                <span class="d-flex align-items-center mx-2" style="height: 38px;">.</span>
                <div class="w-50 me-2">
                    <input type="text" class="form-control" name="domain" id="domain" aria-describedby="domainHelp" placeholder="dropidigital.com.br" disabled>
                </div>
                <button type="submit" class="btn btn-success rounded small fw-semibold d-inline-flex align-items-center ms-2" style="height: 38px;">Adicionar</button>
            </div>
        </div>
    </div>
</form>