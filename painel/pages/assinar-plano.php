<style>
    .frequency,
    .type
    {
        cursor: pointer;
    }
    .frequency.active,
    .type.active
    {
        color: white;
        border-color: var(--dark-green-color);
        background: var(--green-color);
    }

    .btn
    {
        background: var(--green-color);
        border: none;
    }
    .btn:hover {
        background: var(--dark-green-color);
    }
</style>

<div class="d-flex justify-content-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>planos" class="title text-reset text-decoration-none">Escolher plano</a></li>
            <li class="breadcrumb-item fw-semibold text-body-secondary active" aria-current="page">Informações de pagamento</li>
        </ol>
    </nav>
</div>

<form id="myForm" class="position-relative" action="<?php echo INCLUDE_PATH_DASHBOARD ?>back-end/create_product.php" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4 p-0">
                <div class="card-header fw-semibold px-4 py-3 bg-transparent">Escolha o período da sua assinatura Crescimento</div>
                <div class="card-body px-4 py-3">
                    <input type="radio" name="period" id="1" value="anual" class="d-none" checked>
                    <input type="radio" name="period" id="2" value="mensal" class="d-none">
                    <label class="card frequency active mb-3" for="1">
                        <div class="row">
                            <p class="d-flex align-items-center col-md-4">Assinatura anual</p>
                            <div class="pricing col-md-8">
                                <h5 class="lh-1">R$ 516,00 à vista</h5>
                                <p>ou em até 6x sem juros no cartão</p>
                            </div>
                        </div>
                    </label>
                    <label class="card frequency" for="2">
                        <div class="row" style="height: 52px;">
                            <p class="d-flex align-items-center col-md-4">Assinatura mensal</p>
                            <div class="pricing d-flex align-items-center col-md-8">
                                <h5 class="lh-1 mb-0">R$ 54,00 por mês</h5>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            <div class="card mb-4 p-0">
                <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">
                    Informações da fatura
                    <label for="upload-button" class="d-flex align-items-center" style="cursor: pointer;">
                        <i class='bx bx-pencil fs-5 me-1'></i>
                        Editar
                    </label>
                </div>
                <div class="card-body px-4 py-3">
                    <ul class="text-sm font-light text-inverted-2 pt-6 truncate">
                        <li><span>Cauã Serpa</span></li>
                        <li><span>cauaserpa092@gmail.com</span></li>
                        <li>
                            <span class="mr-1">CPF: 205.532.407-14</span>
                            <span class="ml-1">Tel: (21) 97277-5758</span>
                        </li>
                        <li>
                            <span>Rua Cardeal Sebastião Leme,</span>
                            <span>6</span>
                            <span>-</span>
                            <span>Lagoinha</span>
                        </li>
                        <li><span>Complemento: Apto 202</span></li>
                        <li><span>São Gonçalo/RJ - 24736-295</span></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4 p-0">
                <div class="card-header d-flex justify-content-between fw-semibold px-4 py-3 bg-transparent">Como deseja pagar a assinatura do seu plano?</div>
                <div class="card-body px-4 py-3">
                    <div class="row mb-3 g-3">
                        <input type="radio" name="type" id="creditCard" value="creditCard" class="d-none" checked>
                        <input type="radio" name="type" id="boleto" value="boleto" class="d-none">
                        <div class="col-md-8">
                            <label for="creditCard" class="card type d-flex align-items-center justify-content-center active">
                                <i class='bx bx-credit-card fs-3' ></i>
                                Cartão de crédito
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label for="boleto" class="card type d-flex align-items-center justify-content-center">
                                <i class='bx bx-barcode-reader fs-3' ></i>
                                Boleto bancário
                            </label>
                        </div>
                    </div>
                    
                    <div id="container_credit_card">
                        <div class="mb-3">
                            <label for="creditCardNumber" class="form-label small">Número do cartão *</label>
                            <input type="text" class="form-control" name="credit_card_number" id="creditCardNumber" aria-describedby="creditCardNumberHelp" placeholder="Ex. 0000 0000 0000 0000" required>
                        </div>
                        <div class="mb-3">
                            <label for="creditCardOwner" class="form-label small">Nome impresso no cartão *</label>
                            <input type="text" class="form-control" name="credit_card_owner" id="creditCardOwner" aria-describedby="creditCardOwnerHelp" placeholder="Nome impresso no cartão" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="creditCardOwner" class="form-label small">Vencimento do cartão *</label>
                                <input type="text" class="form-control" name="credit_card_owner" id="creditCardOwner" aria-describedby="creditCardOwnerHelp" placeholder="MM / AAAA" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="creditCardOwner" class="form-label small">Código de segurança *</label>
                                <input type="text" class="form-control" name="credit_card_owner" id="creditCardOwner" aria-describedby="creditCardOwnerHelp" placeholder="CVV" required>
                            </div>
                        </div>
                        <div class="mb-3" id="installmentContainer">
                            <label for="installment" class="form-label small">Parcelas</label>
                            <div class="input-group">
                                <select class="form-select" name="installment" id="installment" aria-label="Default select example" required>
                                    <option value="1" selected>À vista</option>
                                    <option value="2">2x de R$ 258,00 sem juros</option>
                                    <option value="3">3x de R$ 172,00 sem juros</option>
                                    <option value="4">4x de R$ 129,00 sem juros</option>
                                    <option value="5">5x de R$ 103,20 sem juros</option>
                                    <option value="6">6x de R$ 86,00 sem juros</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success fw-semibold w-100 px-4 py-2 small mb-3" id="button_text">À vista</button>

                    <small class="lh-1" id="creditCartText">Autorizo o débito anual em meu cartão no valor do plano referente a 12 meses, garantindo a continuidade dos serviços.</small>

                    <small class="lh-1" id="boletoText" style="display: none;">A confirmação do pagamento da assinatura por boleto pode levar até 3 dias úteis. Os novos recursos e limites do plano só serão liberados após a aprovação. Pagamentos por cartão de crédito e Pix têm aprovação instantânea.</small>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Inclua a biblioteca jQuery em seu HTML -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

<!-- Adiciona a class active no "label" referente ao "input:radio" -->
<script>
    $(document).ready(function() {
        // Quando um input radio com o nome "period" for alterado
        $('input[type="radio"][name="period"]').change(function() {
            // Remove a classe "active" de todos os labels
            $('label.card.frequency').removeClass('active');
            
            // Adiciona a classe "active" apenas ao label associado ao input radio marcado
            $('input[type="radio"][name="period"]:checked').each(function() {
                $('label[for="' + $(this).attr('id') + '"]').addClass('active');
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Quando um input radio com o nome "period" for alterado
        $('input[type="radio"][name="period"]').change(function() {
            // Remove a classe "active" de todos os labels
            $('label.card.frequency').removeClass('active');
            
            // Adiciona a classe "active" apenas ao label associado ao input radio marcado
            $('input[type="radio"][name="period"]:checked').each(function() {
                const $label = $('label[for="' + $(this).attr('id') + '"]');
                $label.addClass('active');
                
                // Mostra ou oculta o select e atualiza o texto do botão com base no valor do input radio
                if ($(this).val() === "anual") {
                    $('#installmentContainer').removeClass('d-none');
                    $('#button_text').text("À vista");
                } else if ($(this).val() === "mensal") {
                    $('#installmentContainer').addClass('d-none');
                    $('#button_text').text($label.find('h5').text());
                }
            });
        });
    });
</script>

<!-- Faz o toggle no metodo de pagamento -->
<script>
    $(document).ready(function() {
        // Quando um input radio com o nome "type" for alterado
        $('input[type="radio"][name="type"]').change(function() {
            // Remove a classe "active" de todos os labels
            $('label.card.type').removeClass('active');
            
            // Adiciona a classe "active" apenas ao label associado ao input radio marcado
            $('input[type="radio"][name="type"]:checked').each(function() {
                $('label[for="' + $(this).attr('id') + '"]').addClass('active');
                
                // Mostra ou oculta o conteúdo com base na seleção do método de pagamento
                if ($(this).val() === "creditCard") {
                    $('#container_credit_card').show();
                    $('#creditCartText').show();
                    $('#boletoText').hide();
                } else if ($(this).val() === "boleto") {
                    $('#container_credit_card').hide();
                    $('#creditCartText').hide();
                    $('#boletoText').show();
                }
            });
        });
    });
</script>

<script>
    //Phone Mask
    new Cleave('#creditCardNumber', {
        delimiters: [' ', ' ', ' '],
        blocks: [4, 4, 4, 4],
        numericOnly: true
    });
</script>