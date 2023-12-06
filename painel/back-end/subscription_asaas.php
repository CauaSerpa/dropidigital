<?php
include_once('../../config.php');

// Acessa as variáveis de ambiente
$config['asaas_api_url'] = $asaas_url;
$config['asaas_api_key'] = $asaas_key;

//Decodificando base64 e passando para $dataForm
$dataForm = [];
parse_str(base64_decode($_POST['params']), $dataForm);

makeDonation($dataForm, $config);

$response = array(
    'status' => 200,
    'message' => 'Requisição processada com sucesso.'
);

return json_encode($response);

function makeDonation($dataForm, $config){

    if(isset($_POST)) {

        if ($dataForm["period"] == "mensal") {
            $dataForm["period"] = "MONTHLY";
        } else {
            $dataForm["period"] = "YEARLY";
        }

        // Iniciando variavel "$subscription_id"
        $subscription_id = null;

        include_once('./asaas/criar_cliente.php');
        include_once('./asaas/assinatura_cartao.php');
        include_once('./asaas/assinatura_pix.php');
        include_once('./asaas/cancelar_antigas_cobrancas.php');
        include_once('./asaas/listar_cobranca_assinatura.php');
        include_once('./asaas/qr_code.php');

        switch($_POST["method"]) {
            case 'creditCard':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $payment_id = asaas_CriarAssinaturaCartao($customer_id, $dataForm, $config);
                asaas_CancelarAntigasAssinaturas($dataForm, $payment_id, $config);
                echo json_encode(["status"=>200, "code"=>$payment_id, "id"=>$customer_id]);
                break;
            case 'pix':
                $customer_id = asaas_CriarCliente($dataForm, $config);
                $subscription_id = asaas_CriarAssinaturaPix($customer_id, $dataForm, $config);
                $payment_id = asaas_ObterIdPagamento($subscription_id, $config);
                asaas_ObterQRCodePix($subscription_id, $payment_id, $config);
                asaas_CancelarAntigasAssinaturas($dataForm, $subscription_id, $config);
                echo json_encode(["status"=>200, "code"=>$subscription_id, "id"=>$customer_id]);
                break;
            default:
                echo json_encode(['status' => 404, 'message' => 'Método de pagamento inválido!']);
                break;
        }
    
    }
}