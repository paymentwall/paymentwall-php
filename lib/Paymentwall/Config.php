<?php

namespace Paymentwall;

class Config
{
    public const VERSION = '2.0.0';

    public const API_BASE_URL = 'https://api.paymentwall.com/api';

    public const API_VC = 1;
    public const API_GOODS  = 2;
    public const API_CART   = 3;

    protected $apiType = self::API_GOODS;
    protected $publicKey;
    protected $privateKey;
    protected $apiBaseUrl = self::API_BASE_URL;

    private static $instance;

    public function getApiBaseUrl()
    {
        return $this->apiBaseUrl;
    }

    public function setApiBaseUrl($url = '')
    {
        $this->apiBaseUrl = $url;
    }

    public function getLocalApiType()
    {
        return $this->apiType;
    }

    public function setLocalApiType($apiType = 0)
    {
        $this->apiType = $apiType;
    }

    public function getPublicKey()
    {
        return $this->publicKey;
    }

    public function setPublicKey($key = '')
    {
        $this->publicKey = $key;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function setPrivateKey($key = '')
    {
        $this->privateKey = $key;
    }

    public function getVersion()
    {
        return self::VERSION;
    }

    public function isTest()
    {
        return str_starts_with($this->getPublicKey(), 't_');
    }

    public function set($config = [])
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
    public static function getInstance()
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
