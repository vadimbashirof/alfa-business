<?php

namespace App\Services\AlfaBusiness;

class AlfaBusinessIDAuthService
{
    public const ENDPOINT_REDIRECT_SET_TOKEN = '/merchant/alfa-business/set-access-token';
    public function __construct(
        private array $config,
    ) {
    }

    public function getAuthLink(): string
    {
        $url = $this->config['id_authorize_url'] . '?';
        $url .= 'response_type=code';
        $url .= '&client_id=' . $this->config['client_id'];
        $url .= '&redirect_uri=' . urlencode($this->config['redirect_uri'] . self::ENDPOINT_REDIRECT_SET_TOKEN);
        $url .= '&scope=' . urlencode('openid transactions');
        $url .= '&state=' . $this->config['code_request_state'];

        return $url;
    }
}
