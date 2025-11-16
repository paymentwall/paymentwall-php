<?php

namespace Paymentwall;

class Config
{
    public const VERSION = '2.0.0';

    public const API_BASE_URL = 'https://api.paymentwall.com/api';

    public const API_VC = 1;
    public const API_GOODS  = 2;
    public const API_CART   = 3;

    protected int $apiType = self::API_GOODS;
    protected string $publicKey;
    protected string $privateKey;
    protected string $apiBaseUrl = self::API_BASE_URL;

    private static $instance;

    public function getApiBaseUrl(): string
    {
        return $this->apiBaseUrl;
    }

    public function setApiBaseUrl(string $url = ''): void
    {
        $this->apiBaseUrl = $url;
    }

    public function getLocalApiType(): int
    {
        return $this->apiType;
    }

    public function setLocalApiType(int $apiType = 0): void
    {
        $this->apiType = $apiType;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function setPublicKey(string $key = ''): void
    {
        $this->publicKey = $key;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(string $key = ''): void
    {
        $this->privateKey = $key;
    }

    public function getVersion(): string
    {
        return self::VERSION;
    }

    public function isTest(): bool
    {
        return str_starts_with($this->getPublicKey(), 't_');
    }

    public function set(array $config = []): void
    {
        if (isset($config['api_base_url'])) {
            $this->setApiBaseUrl($config['api_base_url']);
        }
        if (isset($config['api_type'])) {
            $this->setLocalApiType($config['api_type']);
        }
        if (isset($config['public_key'])) {
            $this->setPublicKey($config['public_key']);
        }
        if (isset($config['private_key'])) {
            $this->setPrivateKey($config['private_key']);
        }
    }

    /**
        * @return $this Returns class instance.
        */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            $className = self::class;
            self::$instance = new $className();
        }
        return self::$instance;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }
}
