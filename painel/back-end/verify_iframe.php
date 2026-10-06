<?php
    if (isset($_POST['url'])) {
        $url = filter_var($_POST['url'], FILTER_VALIDATE_URL);

        if (!$url) {
            echo json_encode(["status" => "error", "message" => "URL inválida"]);
            exit;
        }

        // Faz a requisição HTTP para verificar os headers
        $headers = @get_headers($url, 1);

        if ($headers === false) {
            echo json_encode(["status" => "error", "message" => "Não foi possível acessar a URL"]);
            exit;
        }

        // Verifica o X-Frame-Options
        if (isset($headers['X-Frame-Options'])) {
            $xFrame = strtoupper($headers['X-Frame-Options']);
            if ($xFrame === "DENY" || $xFrame === "SAMEORIGIN") {
                echo json_encode(["status" => "deny", "message" => "X-Frame-Options bloqueia o iframe"]);
                exit;
            }
        }

        // Verifica Content-Security-Policy
        if (isset($headers['Content-Security-Policy'])) {
            $csp = strtolower($headers['Content-Security-Policy']);
            if (strpos($csp, "frame-ancestors") !== false && strpos($csp, "none") !== false) {
                echo json_encode(["status" => "deny", "message" => "CSP bloqueia o iframe"]);
                exit;
            }
        }

        // Se passou nos testes, o link pode ser embutido
        echo json_encode(["status" => "allow", "message" => "Pode ser embutido"]);
    }