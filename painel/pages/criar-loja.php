<div class="box__container login">
    <nav class="nav">
        <a href="<?php echo INCLUDE_PATH; ?>" class="form__logo">
            <img src="<?php echo INCLUDE_PATH; ?>assets/images/logos/logo-one.png" alt="Logo">
        </a>
    </nav>
    <form action="<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/create-shop.php" method="POST" class="form business" id="form" name="form" enctype="multipart/form-data">
        <ul id="progress">
            <li class="step active"></li>
            <li class="step"></li>
            <li class="step"></li>
        </ul>

        <?php
            if(isset($_SESSION['msg'])){
                echo $_SESSION['msg'];
                unset($_SESSION['msg']);
                echo "<br>";
            }
            if(isset($_SESSION['msgcad'])){
                echo $_SESSION['msgcad'];
                unset($_SESSION['msgcad']);
                echo "<br>";
            }
        ?>
        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

        <fieldset class="step-form form-one active" data-step="1">
            <div class="container__title login">
                <h3 class="title">Qual o nome e o segmento de sua loja?</h3>
                <p class="subtitle">Essas informações poderão ser alteradas a qualquer momento.</p>
            </div>
            <div class="inputBox">
                <label for="name" class="labelInput">Nome da Loja <span class="danger">*</span></label>
                <input type="text" name="name" id="name" class="inputUser">
                <span id="name-error" class="error-message"></span>
            </div>
            <div class="inputBox" id="urlForm">
                <label for="url" class="labelInput">URL <span class="danger">*</span></label>
                <input type="text" name="url" id="url" class="inputUser">
                <label for="url" class="textInput">.dropidigital.com.br</label>
                <span id="url-error" class="error-message">
                    <?php
                        if(isset($_SESSION['msg_url'])){
                            echo $_SESSION['msg_url'];
                            unset($_SESSION['msg_url']);
                        }
                    ?>
                </span>
            </div>
            <div class="inputBox select">
                <label for="segment" class="labelInput">Segmento <span class="danger">*</span></label>
                <select name="segment" id="segment" class="inputUser">
                    <option value="" disabled selected>-- Nos fale qual o segmento da sua loja --</option>
                    <option value="0">Dropshipping Infoproduto</option>
                    <option value="1">Dropshipping produto físico</option>
                    <option value="2">Site divulgação de serviços</option>
                    <option value="3">Site comércio físico</option>
                    <option value="4">Site para agendamento</option>
                </select>
                <span id="segment-error" class="error-message"></span>
            </div>
            <div class="container__button">
                <button type="button" name="next" class="button button--flex next" onclick="validarEtapa(1)">Continuar</button>
            </div>
        </fieldset>

        <fieldset class="step-form" data-step="2">
            <div class="options">
                <div class="container__title login">
                    <h3 class="title">Quem é você?</h3>
                </div>
                <div class="option__select">
                    <label for="pf" class="card mostrar-formulario" onclick="mostrarFormulario('formulario1')">
                        <img src="<?php echo INCLUDE_PATH_DASHBOARD; ?>assets/images/pessoa-fisica.svg" alt="Pessoa Física">
                        Pessoa Física
                    </label>
                    <label for="pj" class="card mostrar-formulario" onclick="mostrarFormulario('formulario2')">
                        <img src="<?php echo INCLUDE_PATH_DASHBOARD; ?>assets/images/pessoa-juridica.svg" alt="Pessoa Jurídica">
                        Pessoa Jurídica
                    </label>
                </div>
            </div>
            <input type="radio" name="person" id="pf" value="pf" onclick="mostrarFormulario('formulario1')">
            <input type="radio" name="person" id="pj" value="pj" onclick="mostrarFormulario('formulario2')">
            <div class="form__container" id="formulario1" style="display: none;">
                <div class="container__title login">
                    <h3 class="title">Pessoa Física</h3>
                </div>
                <div class="inputBox">
                    <label for="cpf" class="labelInput">CPF <span class="danger">*</span></label>
                    <input type="text" name="cpf" id="cpf" class="inputUser">
                    <span id="cpf-error" class="error-message"></span>
                </div>
                <div class="container__button cpf">
                    <button type="button" name="prev" class="button button--flex prev" onclick="mostrarOption()">Voltar</button>
                    <button type="button" name="next" class="button button--flex next">Continuar</button>
                </div>
            </div>
            <div class="form__container" id="formulario2" style="display: none;">
                <div class="container__title login">
                    <h3 class="title">Pessoa Jurídica</h3>
                </div>
                <div class="inputBox">
                    <label for="cnpj" class="labelInput">CNPJ <span class="danger">*</span></label>
                    <input type="text" name="cnpj" id="cnpj" class="inputUser">
                </div>
                <div class="inputBox">
                    <label for="razao_social" class="labelInput">Razão Social <span class="danger">*</span></label>
                    <input type="text" name="razao_social" id="razao_social" class="inputUser">
                </div>
                <div class="container__button">
                    <button type="button" name="prev" class="button button--flex prev" onclick="mostrarOption()">Voltar</button>
                    <button type="button" name="next" class="button button--flex next">Continuar</button>
                </div>
            </div>
        </fieldset>

        <fieldset class="step-form" data-step="3">
        <div class="container__title login">
                <h3 class="title">Endereço</h3>
                <p class="subtitle">Essas informações poderão ser alteradas a qualquer momento.</p>
            </div>
            <div class="inputBox">
                <label for="cep" class="labelInput">CEP <span class="danger">*</span></label>
                <input type="text" name="cep" id="cep" class="inputUser" onblur="getCepData()">
                <span id="cep-error" class="error-message"></span>
            </div>
            <div id="addressContent" style="overflow: hidden; height: 0; transition: all .3s;">
                <div class="inputBox">
                    <label for="endereco" class="labelInput">Endereço <span class="danger">*</span></label>
                    <input type="text" name="endereco" id="endereco" class="inputUser">
                </div>
                <div class="grid two">
                    <div class="inputBox">
                        <label for="numero" class="labelInput">Número <span class="danger">*</span></label>
                        <input type="text" name="numero" id="numero" class="inputUser">
                    </div>
                    <div class="inputBox">
                        <label for="complemento" class="labelInput">Complemento</label>
                        <input type="text" name="complemento" id="complemento" class="inputUser">
                    </div>
                </div>
                <div class="grid two">
                    <div class="inputBox">
                        <label for="bairro" class="labelInput">Bairro <span class="danger">*</span></label>
                        <input type="text" name="bairro" id="bairro" class="inputUser">
                    </div>
                    <div class="inputBox">
                        <label for="cidade" class="labelInput">Cidade <span class="danger">*</span></label>
                        <input type="text" name="cidade" id="cidade" class="inputUser">
                    </div>
                </div>
                <div class="grid two">
                    <div class="inputBox">
                        <label for="estado" class="labelInput">Estado <span class="danger">*</span></label>
                        <input type="text" name="estado" id="estado" class="inputUser">
                    </div>
                    <div class="inputBox">
                        <label for="phone" class="labelInput">Telefone <span class="danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="inputUser">
                    </div>
                </div>
            </div>
            <div class="container__button">
                <button type="button" name="prev" class="button button--flex prev">Voltar</button>
                <button type="submit" name="next" class="button button--flex next" onclick="validarEtapa4()">Finalizar</button>
            </div>
        </fieldset>
    </form>
    <div class="bottom__text signup">
        <p>Já possui uma loja na DropiDigital?</p>
        <a href="<?php echo INCLUDE_PATH_DASHBOARD; ?>login">Acesse sua conta agora.</a>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('#name').on('blur', function() {
      const name = $(this).val();
      
      const urlField = $('#urlForm');
      const urlInput = $('#url');

      if (name !== '') {
        $.ajax({
          url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/check_url.php', // Arquivo PHP para verificar o url
          method: 'POST',
          data: { name: name },
          success: function(response) {
            $('#url-error').html(response);

            if (response.includes('URL já cadastrada!')) {
                urlField.addClass('active'); // Adiciona classe em caso de erro
                urlInput.addClass('urlActive'); // Adiciona classe em caso de erro
                urlInput.addClass('input-error'); // Adiciona classe em caso de erro
            } else {
                urlField.removeClass('active'); // Adiciona classe em caso de erro
                urlInput.removeClass('urlActive'); // Adiciona classe em caso de erro
                urlInput.removeClass('input-error'); // Remove classe em caso de sucesso
            }
          }
        });
      }
    });
  });
</script>
<script>
  $(document).ready(function() {
    $('#url').on('blur', function() {
      const url = $(this).val();
      const urlField = $(this);

      if (url !== '') {
        $.ajax({
          url: '<?php echo INCLUDE_PATH_DASHBOARD; ?>back-end/check_url.php', // Arquivo PHP para verificar o url
          method: 'POST',
          data: { url: url },
          success: function(response) {
            $('#url-error').html(response);

            if (response.includes('URL já cadastrada!')) {
              urlField.addClass('input-error'); // Adiciona classe em caso de erro
            } else {
              urlField.removeClass('input-error'); // Remove classe em caso de sucesso
            }
          }
        });
      }
    });
  });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var steps = document.querySelectorAll('.step');
        var stepForms = document.querySelectorAll('.step-form');
        var currentStep = 0;

        updateStep();

        function updateStep() {
            steps.forEach(function (step, index) {
                if (index <= currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            stepForms.forEach(function (stepForm, index) {
                if (index === currentStep) {
                    stepForm.classList.add('active');
                } else {
                    stepForm.classList.remove('active');
                }
            });
        }

        document.querySelectorAll('.next').forEach(function (nextBtn) {
            nextBtn.addEventListener('click', function (e) {
                if (validateCurrentStep(currentStep + 1)) {
                    currentStep++;
                    updateStep();
                }
            });
        });

        document.querySelectorAll('.prev').forEach(function (prevBtn) {
            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                currentStep--;
                updateStep();
            });
        });

        function validateCurrentStep(etapa) {
            if (etapa === 1) {
                const form = document.getElementById('form');
                const nameInput = document.getElementById('name');
                const segmentInput = document.getElementById('segment');

                const nameError = document.getElementById('name-error');
                const segmentError = document.getElementById('segment-error');

                const nameField = $(nameInput);
                const segmentField = $(segmentInput);

                nameError.textContent = '';

                if (!validateName(nameInput.value)) {
                    nameError.textContent = 'Preencha o campo Nome!';
                    nameField.addClass('input-error');
                    return false;
                }

                segmentError.textContent = '';

                if (!validateSegment(segmentInput.value)) {
                    segmentError.textContent = 'Selecione um segmento!';
                    segmentField.addClass('input-error');
                    return false;
                }
            }
            // Adicione validações para as outras etapas, se necessário

            return true;
        }
        
        function validateCurrentStep(etapa) {
            if (etapa === 2) {
                const form = document.getElementById('form');
                const cpfInput = document.getElementById('cpf');

                const cpfError = document.getElementById('cpf-error');

                const cpfField = $(cpfInput);

                cpfError.textContent = '';

                if (!validateCpf(cpfInput.value)) {
                    cpfError.textContent = 'Preencha o campo Nome!';
                    cpfField.addClass('input-error');
                    return false;
                }
            }
            // Adicione validações para as outras etapas, se necessário

            return true;
        }

        function validateName(name) {
            return name.length > 0;
        }
        
        function validateSegment(segment) {
            return segment !== '';
        }

        function validateCpf(cpf) {
            return cpf.length == 14;
        }
    });
</script>
<script>
	const form = document.getElementById('form');
    const nameInput = document.getElementById('name');
    const segmentInput = document.getElementById('segment');
    const urlInput = document.getElementById('name');
    const cpfInput = document.getElementById('cpf');

    const nameError = document.getElementById('name-error');
    const segmentError = document.getElementById('segment-error');
    const urlError = document.getElementById('url-error');
    const cpfError = document.getElementById('cpf-error');

    nameInput.addEventListener('input', function() {
        const nameField = $(this);

        nameError.textContent = '';

        if (!validateName(nameInput.value)) {
            nameError.textContent = 'Preencha o campo Nome!';
            nameField.addClass('input-error');
        } else {
            nameField.removeClass('input-error');
        }
    });

    segmentInput.addEventListener('change', function() {
        const segmentField = $(this);

        segmentError.textContent = '';

        if (!validateSegment(segmentInput.value)) {
            segmentError.textContent = 'Selecione um segmento válido.';
            segmentField.addClass('input-error');
        } else {
            segmentField.removeClass('input-error');
        }
    });

    cpfInput.addEventListener('input', function() {
        const cpfField = $(this);

        cpfError.textContent = '';

        if (!validateCpf(cpfInput.value)) {
            cpfError.textContent = 'Preencha o campo Nome!';
            cpfField.addClass('input-error');
        } else {
            cpfField.removeClass('input-error');
        }
    });

    // urlInput.addEventListener('input', function() {
    //     const urlField = $(this);

    //     urlError.textContent = '';

    //     if (urlField.hasClass('urlActive')) {
    //         if (!validateUrl(urlInput.value)) {
    //             urlError.textContent = 'Preencha o campo URL!';
    //             urlField.addClass('input-error');
    //         } else {
    //             urlField.removeClass('input-error');
    //         }
    //     }
    // });

    function validateName(name) {
        return name.length > 0;
    }

    function validateSegment(segment) {
        return segment !== '';
    }

    // function validateUrl(url) {
    //     return url.length > 0;
    // }

    function validateCpf(cpf) {
        return cpf !== '';
    }
</script>
<script>
    function mostrarFormulario(formularioId) {
        var formulario = document.getElementById(formularioId);
        var divs = document.querySelectorAll('.card');

        var todosFormularios = document.querySelectorAll('[id^="formulario"]');
        todosFormularios.forEach(function(form) {
            form.style.display = 'none';
        });

        formulario.style.display = 'block';
        apagarOption();
    }

    function mostrarOption() {
        var divsParaMostrar = document.querySelectorAll('.options');
        var removerForms = document.querySelectorAll('.form__container');

        divsParaMostrar.forEach(function(div) {
            div.style.display = 'block';
        });

        removerForms.forEach(function(form) {
            form.style.display = 'none';
        });
    }

    function apagarOption() {
        var divsParaRemover = document.querySelectorAll('.options');

        divsParaRemover.forEach(function(div) {
            div.style.display = 'none';
        });
    }
</script>
<!-- Mascara de input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.0.2/cleave.min.js"></script>
<script>
    //Mask
    new Cleave('#phone', {
        delimiters: ['(', ')', ' ', '-'],
        blocks: [0, 2, 0, 5, 4],
        numericOnly: true
    });
</script>
<script>
    //Mask
    new Cleave('#cpf', {
        delimiters: ['.', '.', '-'],
        blocks: [3, 3, 3, 2],
        numericOnly: true
    });
</script>
<script>
    //Mask
    new Cleave('#cnpj', {
        delimiters: ['.', '.', '/', '-'],
        blocks: [2, 3, 3, 4, 2],
        numericOnly: true
    });
</script>
<script>
    //Mask
    new Cleave('#cep', {
        delimiters: ['-'],
        blocks: [5, 3],
        numericOnly: true
    });
</script>
<script>
    function getCepData() {
        let cep = $('#cep').val();
        cep = cep.replace(/\D/g, "");
        if(cep.length < 8) {
            $("#cep-error").html("O CEP deve conter no mínimo 8 dígitos");
            $("#cep").addClass('input-error').focus();
            $("#addressContent").css('height', '0');
            return;
        }

        $("#cep").removeClass('input-error');
        $("#cep-error").html('');

        if(cep != "") {
            $("#addressContent").css('height', '356px');
            
            $("#endereco").val("Carregando...");
            $("#bairro").val("Carregando...");
            $("#cidade").val("Carregando...");
            $("#estado").val("Carregando...");
            $.getJSON( "https://viacep.com.br/ws/"+cep+"/json/", function( data ) {
                $("#endereco").val(data.logradouro);
                $("#bairro").val(data.bairro);
                $("#cidade").val(data.localidade);
                $("#estado").val(data.uf);
                $("#numero").focus();
            }).fail(function() {
                $("#endereco").val("");
                $("#bairro").val("");
                $("#cidade").val("");
                $("#estado").val("");
            });
        }
    }
</script>