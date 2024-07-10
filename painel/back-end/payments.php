<?php
include_once('../../config.php');

// Acessa as variáveis de ambiente
$config['asaas_api_url'] = $asaas_url;
$config['asaas_api_key'] = $asaas_key;

//Decodificando base64 e passando para $dataForm
$dataForm = [];
parse_str(base64_decode($_POST['params']), $dataForm);

$dataForm['order_id'] = $_POST["order_id"];

makeDonation($dataForm, $config);

$response = array(
    'status' => 200,
    'message' => 'Requisição processada com sucesso.'
);

return json_encode($response);

function makeDonation($dataForm, $config){

    if(isset($_POST)) {

        // Iniciando variavel "$subscription_id"
        $payment_id = null;

        $subscription = false;

        // Pegar o valor da assinatura
        $selectedServices = $dataForm['selectedServices'];

        // Decodificar os dados JSON para um array PHP
        $servicesArray = json_decode($selectedServices, true);

        // Verificar se $_POST['selectedServices'] está definido e é um array
        if (isset($dataForm['selectedServices']) && is_array($servicesArray)) {
            // Iterar sobre os itens e obter os IDs dos itens selecionados
            foreach ($servicesArray as $service) {
                if ($service['type'] === 'subscrition') {
                    $subscriptionPrice = $service['value'];
                }
            }
        }

        if (@$subscriptionPrice !== null) {
            $dataForm['value'] = $dataForm['value'] - $subscriptionPrice;
        }

        include_once('./asaas/criar_cliente.php');
        include_once('./asaas/cobranca_cartao.php');
        include_once('./asaas/assinatura_cartao.php');
        include_once('./asaas/cobranca_pix.php');
        include_once('./asaas/qr_code.php');
        include_once('./asaas/cancelar_antigas_cobrancas.php');
        include_once('./copy_site_shop.php');

        switch($_POST["method"]) {
            case 'creditCard':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment_id = asaas_CriarCobrancaCartao($customer_id, $dataForm, $config);
                if ($subscriptionPrice !== null) {
                    $dataForm['value'] = $subscriptionPrice;

                    if ($dataForm['plan_period'] == "monthly") {
                        $dataForm["period"] = "MONTHLY";
                    } else {
                        $dataForm["period"] = "YEARLY";
                    }

                    $subs_payment_id = asaas_CriarAssinaturaCartao($customer_id, $dataForm, $config);
                    asaas_CancelarAntigasAssinaturas($dataForm, $subs_payment_id, $config);
                }
                copyReadySiteToShop($dataForm);
                echo json_encode(["status"=>200, "code"=>$payment_id, "id"=>$customer_id]);
                break;
            case 'pix':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment_id = asaas_CriarCobrancaPix($customer_id, $dataForm, $config);
                asaas_ObterQRCodePix($subscription, $payment_id, $config);
                echo json_encode(["status"=>200, "code"=>$payment_id, "id"=>$customer_id]);
                break;
            default:
                echo json_encode(['status' => 404, 'message' => 'Método de pagamento inválido!']);
                break;
        }
    
    }
}