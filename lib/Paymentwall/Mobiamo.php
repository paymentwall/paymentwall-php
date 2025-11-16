<?php

namespace Paymentwall;

class Mobiamo extends ApiObject
{
    protected $token;
    public const API_OBJECT_MOBIAMO = 'mobiamo';

    public function getEndpointName(): string
    {
        return self::API_OBJECT_MOBIAMO;
    }

    public function getToken(array $params): array
    {
        $defaultParams = [
            'key' => $this->getConfig()->getPublicKey(),
            'ts' => time(),
            'sign_version' => \Paymentwall\Signature\Signature::VERSION_TWO,
        ];
        $params = array_merge($defaultParams, $params);
        $params['sign'] = $this->calculateSignature($params);
        $this->doApiAction('token', 'post', $params);
        return $this->getProperties();
    }

    public function initPayment($token, $params): array
    {
        $this->token = $token;
        $params['key'] = $this->getConfig()->getPublicKey();
        $this->doApiAction('init-payment', 'post', $params);
        return $this->getProperties();
    }

    public function processPayment($token, $params): array
    {
        $this->token = $token;
        $params['key'] = $this->getConfig()->getPublicKey();
        $this->doApiAction('process-payment', 'post', $params);
        return $this->getProperties();
    }

    public function getPaymentInfo($token, array $params): array
    {
        $this->token = $token;
        $params['key'] = $this->getConfig()->getPublicKey();
        $this->doApiAction('get-payment', 'post', $params);
        return $this->getProperties();
    }

    protected function calculateSignature(array $params = []): string
    {
        $this->ksortMultiDimensional($params);

        return md5($this->prepareParams($params) . $this->getConfig()->getPrivateKey());
    }

    protected function prepareParams(array $params = [], string $baseString = ''): string
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $baseString .= $key . '[' . $k . ']' . '=' . $v;
                }
            } else {
                $baseString .= $key . '=' . $value;
            }
        }
        return $baseString;
    }

    protected function ksortMultiDimensional(&$params = []): void
    {
        if (is_array($params)) {
            ksort($params);
            foreach ($params as &$p) {
                if (is_array($p)) {
                    ksort($p);
                }
            }
        }
    }

    public function getApiUrl(): string
    {
        if ($this->getEndpointName() === self::API_OBJECT_ONE_TIME_TOKEN && !$this->getConfig()->isTest()) {
            return OneTimeToken::GATEWAY_TOKENIZATION_URL;
        } else {
            return $this->getApiBaseUrl() . '/' .  $this->getEndpointName();
        }
    }

    protected function doApiAction(string $action = '', string $method = 'post', array $params = []): self
    {
        $actionUrl = $this->getApiUrl() . '/' . $action;
        $httpAction = new HttpAction($this, $params, [$this->getApiBaseHeader()]);
        $this->setPropertiesFromResponse(
            $method == 'get' ? $httpAction->get($actionUrl) : $httpAction->post($actionUrl)
        );
        return $this;
    }

    protected function getApiBaseHeader(): string
    {
        if ($this->token) {
            return 'token: ' . $this->token;
        }
        return '';
    }
}
