<?php

class GoogleOAuth
{
    private Google\Client $client;

    public function __construct()
    {
        $this->client = new Google\Client();

        $this->client->setClientId(
            GOOGLE_OAUTH_CLIENT_ID
        );

        $this->client->setClientSecret(
            GOOGLE_OAUTH_CLIENT_SECRET
        );

        $this->client->setRedirectUri(
            GOOGLE_OAUTH_REDIRECT_URI
        );

        $this->client->setAccessType('offline');

        $this->client->setPrompt('consent');

        $this->client->setIncludeGrantedScopes(true);

        $this->client->setScopes([
            GOOGLE_ANALYTICS_SCOPE
        ]);
    }

    public function getAuthorizationUrl(
        string $state
    ): string {
        $this->client->setState($state);

        return $this->client->createAuthUrl();
    }

    public function authenticate(
        string $code
    ): array {
        $token = $this->client->fetchAccessTokenWithAuthCode(
            $code
        );

        if (!empty($token['error'])) {
            throw new Exception(
                $token['error_description']
                ?? $token['error']
                ?? 'Erro ao obter token do Google.'
            );
        }

        if (empty($token['access_token'])) {
            throw new Exception(
                'O Google não retornou um access_token.'
            );
        }

        $this->client->setAccessToken($token);

        return $token;
    }

    public function setAccessToken(
        array $token
    ): void {
        $this->client->setAccessToken($token);
    }

    public function getAccessToken(): ?array
    {
        $token = $this->client->getAccessToken();

        return is_array($token)
            ? $token
            : null;
    }

    public function getClient(): Google\Client
    {
        return $this->client;
    }
}