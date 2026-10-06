<?php
    session_start();
    ob_start();
    include_once('../../config.php');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Pega o id do usuario que criou a loja
        $user_id = $_SESSION['user_id_for_create_shop'];

        // Recebe os dados do formulário
        $name = $_POST['name'];

        // Criar subdminio do site
        // Pega post url
        $url = $_POST['url'];

        if ($url == '')
        {
            // Url não preenchida utiliza o post name para criar
            // Transforma o texto em minúsculas
            $texto = strtolower($name);

            // Remove pontos e vírgulas
            $texto = str_replace(['.', ','], '', $texto);

            // Separa o texto em um array de palavras
            $palavras = explode(' ', $texto);

            // Junta as palavras com "-"
            $url = implode('-', $palavras);
        }

        //Tabela que será solicitada
        $tabela = 'tb_domains';
        
        // Verifica se a Url já existe
        $sql = "SELECT id FROM $tabela WHERE subdomain = :subdomain";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':subdomain', $url);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Mostra formulario de url
            $_SESSION['input_url'] = "show";

            // Mensagem de erro
            $_SESSION['msg_url'] = "<p class='danger'>A URL já está sendo utilizado. Escolha outra URL.</p>";
            
            //Link de redirecionamento
            $redirect_url = INCLUDE_PATH_DASHBOARD . 'criar-loja';
            header('Location: ' . $redirect_url);

            //Mata o processo
            die();
        }
        
        $segment = $_POST['segment'];
        $detailed_segment = $_POST['detailed_segment'];
        
        $person = $_POST['person'];
        // Verifica se e pessoa fisica ou pessoa juridica
        if ($person == 'pj')
        {
            $cpf_cnpj = $_POST['cnpj'];
            $razao_social = $_POST['razao_social'];
        } else {
            $cpf_cnpj = $_POST['cpf'];
        }

        $razao_social = $_POST['razao_social'];

        $cep = $_POST['cep'];
        $endereco = $_POST['endereco'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $estado = $_POST['estado'];

        $phone = $_POST['phone'];

        // Faça a validação dos campos, evitando SQL injection e outros ataques
        // Por exemplo, use a função filter_input() e hash para a senha:

        //Tabela que será solicitada
        $tabela = 'tb_shop';
        
        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (user_id, name, segment, detailed_segment, cpf_cnpj, razao_social, phone) VALUES 
                                    (:user_id, :name, :segment, :detailed_segment, :cpf_cnpj, :razao_social, :phone)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':segment', $segment);
        $stmt->bindValue(':detailed_segment', $detailed_segment);
        $stmt->bindValue(':cpf_cnpj', $cpf_cnpj);
        $stmt->bindValue(':razao_social', $razao_social);
        $stmt->bindValue(':phone', $phone);
        $stmt->execute();

        // Recebendo id da loja
        $shop_id = $conn_pdo->lastInsertId();

        //Tabela que será solicitada
        $tabela = 'tb_shop_users';
        
        // Insere o usuário no banco de dados
        $sql = "INSERT INTO $tabela (user_id, shop_id) VALUES (:user_id, :shop_id)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_domains';

        $domain = "dropidigital.com.br";

        // Obtem a data e hora atual
        date_default_timezone_set('America/Sao_Paulo');
        $current_date = date('Y-m-d H:i:s');
    
        // Insere o dominio no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, subdomain, domain, register_date) VALUES 
                                (:shop_id, :subdomain, :domain, :register_date)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':subdomain', $url);
        $stmt->bindValue(':domain', $domain);
        $stmt->bindValue(':register_date', $current_date);
        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_address';

        // Inserindo informações da fatura no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, cep, endereco, numero, complemento, bairro, cidade, estado) VALUES 
                                    (:shop_id, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':cep', $cep);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->bindValue(':numero', $numero);
        $stmt->bindValue(':complemento', $complemento);
        $stmt->bindValue(':bairro', $bairro);
        $stmt->bindValue(':cidade', $cidade);
        $stmt->bindValue(':estado', $estado);
        $stmt->execute();

        // Nome da tabela para a busca
        $tabela = 'tb_users';

        // Consulta SQL para contar os produtos na tabela
        $sql = "SELECT name FROM $tabela WHERE id = :id LIMIT 1";
        $stmt = $conn_pdo->prepare($sql);  // Use prepare para consultas preparadas
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        // Recupere o resultado da consulta
        $name = $stmt->fetch(PDO::FETCH_ASSOC)['name'];

        if ($person == 'pj')
        {
            $name = $razao_social;
            $docType = "cnpj";
            $docNumber = $cpf_cnpj;
        } else {
            $name = $name;
            $docType = "cpf";
            $docNumber = $cpf_cnpj;
        }

        //Tabela que será solicitada
        $tabela = 'tb_invoice_info';

        // Passando valores
        $email = $_POST['email'];

        // Inserindo informações da fatura no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, name, email, phone, docType, docNumber, cep, endereco, numero, complemento, bairro, cidade, estado) VALUES 
                                    (:shop_id, :name, :email, :phone, :docType, :docNumber, :cep, :endereco, :numero, :complemento, :bairro, :cidade, :estado)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':phone', $phone);
        $stmt->bindValue(':docType', $docType);
        $stmt->bindValue(':docNumber', $docNumber);
        $stmt->bindValue(':cep', $cep);
        $stmt->bindValue(':endereco', $endereco);
        $stmt->bindValue(':numero', $numero);
        $stmt->bindValue(':complemento', $complemento);
        $stmt->bindValue(':bairro', $bairro);
        $stmt->bindValue(':cidade', $cidade);
        $stmt->bindValue(':estado', $estado);
        $stmt->execute();

        //Tabela que será solicitada
        $tabela = 'tb_subscriptions';
        
        // Passando valores
        $plan_id = 1;
        $value = 0;
        $status = "RECEIVED";
        $cycle = "MONTHLY";
        
        // Obtém a data atual
        $today = new DateTime();

        // Adiciona um mês à data atual para obter a data de vencimento
        $due_date = clone $today;
        $due_date->add(new DateInterval('P1M'));

        // Formata as datas conforme necessário
        $start_date_formatted = $today->format('Y-m-d H:i:s');
        $due_date_formatted = $due_date->format('Y-m-d H:i:s');

        // Criado plano para o cliente no banco de dados
        $sql = "INSERT INTO $tabela (shop_id, plan_id, value, status, start_date, due_date, cycle) VALUES 
                                    (:shop_id, :plan_id, :value, :status, :start_date, :due_date, :cycle)";
        $stmt = $conn_pdo->prepare($sql);
        $stmt->bindValue(':shop_id', $shop_id);
        $stmt->bindValue(':plan_id', $plan_id);
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':status', $status);
        $stmt->bindParam(':start_date', $start_date_formatted);
        $stmt->bindParam(':due_date', $due_date_formatted);
        $stmt->bindValue(':cycle', $cycle);
        $stmt->execute();

        // Pagina de conteudo padrao
        $defaultName    = 'Política de Privacidade';
        $defaultLink    = 'politica-privacidade';
        $defaultSeoName = 'Política de Privacidade - ' . $name;
        $defaultSeoLink = 'politica-privacidade';
        $defaultSeoDesc = 'Seja bem-vindo à nossa Política de Privacidade. Nesta página, explicamos como coletamos, armazenamos e utilizamos as informações dos visitantes e clientes de nossa loja, que trabalha majoritariamente com infoprodutos e produtos de afiliados, sem processo de checkout próprio.';

        // Conteúdo HTML rico para a página
        $defaultContent = '
        <section class="privacy-policy">
            <header>
                <h1>Política de Privacidade</h1>
                <p>Última atualização: ' . date('d/m/Y') . '</p>
            </header>

            <article>
                <h2>1. Introdução</h2>
                <p>Seja bem-vindo à nossa Política de Privacidade. Nesta página, explicamos como coletamos, armazenamos e utilizamos as informações dos visitantes e clientes de nossa loja, que trabalha majoritariamente com infoprodutos e produtos de afiliados, sem processo de checkout próprio.</p>
            </article>

            <article>
                <h2>2. Dados que Coletamos</h2>
                <ul>
                <li><strong>Informações fornecidas diretamente:</strong> e-mail, nome e informações de contato quando você opta por receber newsletters ou materiais gratuitos.</li>
                <li><strong>Dados de navegação:</strong> IP, tipo de navegador, tempo de acesso, páginas visitadas, origem do acesso (Google, redes sociais etc.).</li>
                <li><strong>Cookies e tecnologias similares:</strong> para melhorar sua experiência, personalizar conteúdo e analisar tráfego.</li>
                </ul>
            </article>

            <article>
                <h2>3. Finalidade do Tratamento</h2>
                <p>Utilizamos seus dados para:</p>
                <ul>
                <li>Enviar newsletters, atualizações e conteúdo gratuito de interesse.</li>
                <li>Personalizar recomendações de infoprodutos e ofertas de afiliados.</li>
                <li>Analisar estatísticas de navegação para aprimorar nosso site e conteúdos.</li>
                </ul>
            </article>

            <article>
                <h2>4. Uso de Cookies</h2>
                <p>Implementamos cookies para:</p>
                <ul>
                <li>Lembrar suas preferências de idioma e layout.</li>
                <li>Monitorar desempenho do site e corrigir erros.</li>
                <li>Veicular anúncios personalizados de parceiros afiliados.</li>
                </ul>
                <p>Você pode desativar cookies nos ajustes de seu navegador, mas algumas funcionalidades podem ficar comprometidas.</p>
            </article>

            <article>
                <h2>5. Links para Sites de Terceiros</h2>
                <p>Nossa loja pode conter links para sites de afiliados ou parceiros. Não nos responsabilizamos pelas práticas de privacidade desses sites. Recomendamos que você leia as políticas de privacidade de cada site de destino.</p>
            </article>

            <article>
                <h2>6. Segurança dos Dados</h2>
                <p>Tomamos medidas técnicas e organizacionais para proteger suas informações contra acesso não autorizado, perda ou alteração. Utilizamos conexões criptografadas (HTTPS) em todo o site.</p>
            </article>

            <article>
                <h2>7. Compartilhamento de Informações</h2>
                <p>Podemos compartilhar dados pessoais com:</p>
                <ul>
                <li>Plataformas de e-mail marketing para disparo de newsletters.</li>
                <li>Redes de afiliados para contabilização de comissões.</li>
                <li>Fornecedores de análise de dados (Google Analytics, Hotjar etc.).</li>
                </ul>
                <p>Em nenhuma hipótese vendemos ou alugamos suas informações a terceiros.</p>
            </article>

            <article>
                <h2>8. Retenção dos Dados</h2>
                <p>Armazenamos suas informações enquanto você se mantiver inscrito em nossa newsletter ou enquanto houver finalidade legítima para o uso. Após isso, os dados serão anonimizados ou excluídos.</p>
            </article>

            <article>
                <h2>9. Direitos dos Titulares</h2>
                <p>Você tem direito a:</p>
                <ul>
                <li>Acessar os dados que armazenamos sobre você.</li>
                <li>Corrigir informações incorretas.</li>
                <li>Solicitar a exclusão ou portabilidade dos dados.</li>
                <li>Revogar seu consentimento a qualquer momento.</li>
                </ul>
                <p>Para exercer seus direitos, entre em contato pelo e-mail <a href="mailto:' . $email . '">' . $email . '</a>.</p>
            </article>

            <article>
                <h2>10. Alterações nesta Política</h2>
                <p>Podemos atualizar esta Política de Privacidade a qualquer momento. Publicaremos a nova versão com a data de atualização no topo desta página. Recomendamos que consulte periodicamente para se manter informado.</p>
            </article>

            <footer>
                <p>Se tiver dúvidas sobre esta política, entre em contato conosco em <a href="mailto:' . $email . '">' . $email . '</a>.</p>
            </footer>
        </section>
        ';

        $sqlPage = "
            INSERT INTO tb_pages
                (shop_id, status, name, link, content, seo_name, seo_link, seo_description)
            VALUES
                (:shop_id, 1, :name, :link, :content, :seo_name, :seo_link, :seo_description)
        ";
        $stmtPage = $conn_pdo->prepare($sqlPage);
        $stmtPage->bindValue(':shop_id', $shop_id, PDO::PARAM_INT);
        $stmtPage->bindValue(':name', $defaultName, PDO::PARAM_STR);
        $stmtPage->bindValue(':link', $defaultLink, PDO::PARAM_STR);
        $stmtPage->bindValue(':content', $defaultContent, PDO::PARAM_STR);
        $stmtPage->bindValue(':seo_name', $defaultSeoName, PDO::PARAM_STR);
        $stmtPage->bindValue(':seo_link', $defaultSeoLink, PDO::PARAM_STR);
        $stmtPage->bindValue(':seo_description', $defaultSeoDesc, PDO::PARAM_STR);
        $stmtPage->execute();

        // Cria a sessao com o id do usuario para login
        $_SESSION['user_id'] = $user_id;

        // Destroi a sessao com o id do usuario para criar a loja
        unset($_SESSION['user_id_for_create_shop']);

        // Redireciona para a página de login ou exibe uma mensagem de sucesso
        header("Location: ".INCLUDE_PATH_DASHBOARD);
        exit;
    }
?>