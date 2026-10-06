<?php

class GoogleAnalyticsData
{
    private Google\Client $client;

    public function __construct(
        Google\Client $client
    ) {
        $this->client = $client;
    }

    public function runReport(
        string $propertyId,
        array $dimensions,
        array $metrics,
        string $startDate,
        string $endDate,
        int $limit = 10000
    ): array {

        $accessToken =
            $this->client->getAccessToken();

        if (
            empty($accessToken['access_token'])
        ) {
            throw new Exception(
                'Access token do Google Analytics não está disponível.'
            );
        }

        $dimensionItems = [];

        foreach ($dimensions as $dimension) {

            $dimensionItems[] = [
                'name' => $dimension
            ];
        }

        $metricItems = [];

        foreach ($metrics as $metric) {

            $metricItems[] = [
                'name' => $metric
            ];
        }

        $payload = [
            'dateRanges' => [
                [
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ]
            ],

            'dimensions' =>
                $dimensionItems,

            'metrics' =>
                $metricItems,

            'limit' =>
                $limit
        ];

        $url =
            'https://analyticsdata.googleapis.com/v1beta/properties/'
            . rawurlencode($propertyId)
            . ':runReport';

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_POST => true,

            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer '
                    . $accessToken['access_token'],

                'Content-Type: application/json',

                'Accept: application/json'
            ],

            CURLOPT_POSTFIELDS =>
                json_encode(
                    $payload,
                    JSON_UNESCAPED_UNICODE
                ),

            CURLOPT_TIMEOUT => 60
        ]);

        $body = curl_exec($ch);

        if ($body === false) {

            $error =
                curl_error($ch);

            curl_close($ch);

            throw new Exception(
                'Erro ao comunicar com Google Analytics Data API: '
                . $error
            );
        }

        $httpCode =
            curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );

        curl_close($ch);

        $data =
            json_decode(
                $body,
                true
            );

        if (
            $httpCode < 200 ||
            $httpCode >= 300
        ) {

            $message =
                $data['error']['message']
                ?? 'Erro desconhecido da Google Analytics Data API.';

            throw new Exception(
                'Google Analytics Data API ['
                . $httpCode
                . ']: '
                . $message
            );
        }

        if (!is_array($data)) {

            throw new Exception(
                'Resposta inválida da Google Analytics Data API.'
            );
        }

        return $data;
    }
}