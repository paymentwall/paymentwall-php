<?php
namespace Paymentwall;

class OneTimeToken extends ApiObject
{
    public const GATEWAY_TOKENIZATION_URL = 'https://pwgateway.com/api/token';

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function isTest(): ?bool
    {
        return $this->test;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getExpirationTime(): ?int
    {
        return $this->expires_in;
    }

    public function getEndpointName(): string
    {
        return self::API_OBJECT_ONE_TIME_TOKEN;
    }
}
