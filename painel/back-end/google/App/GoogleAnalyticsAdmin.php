<?php

class GoogleAnalyticsAdmin
{
    private Google\Client $client;

    public function __construct(
        Google\Client $client
    ) {
        $this->client = $client;
    }

    public function listAccounts(): array
    {
        $response = $this->request(
            'GET',
            'https://analyticsadmin.googleapis.com/v1beta/accounts'
        );

        return $response['accounts'] ?? [];
    }

    public function listProperties(
        string $accountName
    ): array {
        $response = $this->request(
            'GET',
            'https://analyticsadmin.googleapis.com/v1beta/properties',
            [
                'filter' => 'parent:' . $accountName
            ]
        );

        return $response['properties'] ?? [];
    }

    public function listDataStreams(
        string $propertyName
    ): array {
        $response = $this->request(
            'GET',
            'https://analyticsadmin.googleapis.com/v1beta/'
            . $propertyName
            . '/dataStreams'
        );

        return $response['dataStreams'] ?? [];
    }

    private function request(
        string $method,
        string $url,
        array $query = []
    ): array {
        $accessToken =
            $this->client->getAccessToken();

        if (
            empty($accessToken['access_token'])
        ) {
            throw new Exception(
                'Access token do Google não está disponível.'
            );
        }

        if (!empty($query)) {
            $url .= '?' . http_build_query(
                $query
            );
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,

            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '
                    . $accessToken['access_token'],

                'Content-Type: application/json',

                'Accept: application/json'
            ],

            CURLOPT_TIMEOUT => 30,
        ]);

        $body = curl_exec($ch);

        if ($body === false) {

            $error = curl_error($ch);

            curl_close($ch);

            throw new Exception(
                'Erro ao comunicar com Google Analytics: '
                . $error
            );
        }

        $httpCode = curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );

        curl_close($ch);

        $data = json_decode(
            $body,
            true
        );

        if (
            $httpCode < 200 ||
            $httpCode >= 300
        ) {
            $message =
                $data['error']['message']
                ?? 'Erro desconhecido na Google Analytics Admin API.';

            throw new Exception(
                'Google Analytics API ['
                . $httpCode
                . ']: '
                . $message
            );
        }

        if (!is_array($data)) {
            throw new Exception(
                'Resposta inválida da Google Analytics Admin API.'
            );
        }

        return $data;
    }
}