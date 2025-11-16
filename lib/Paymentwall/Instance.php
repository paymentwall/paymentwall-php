<?php

namespace Paymentwall;

abstract class Instance
{
    protected $config;
    protected array $errors = [];

    public function getErrorSummary(): string
    {
        return implode("\n", $this->getErrors());
    }

    protected function getConfig(): Config
    {
        if (!isset($this->config)) {
            $this->config = Config::getInstance();
        }
        return $this->config;
    }

    protected function getApiBaseUrl()
    {
        return $this->getConfig()->getApiBaseUrl();
    }

    protected function getApiType()
    {
        return $this->getConfig()->getLocalApiType();
    }

    protected function getPublicKey()
    {
        return $this->getConfig()->getPublicKey();
    }

    protected function getPrivateKey()
    {
        return $this->getConfig()->getPrivateKey();
    }

    protected function appendToErrors($error = '')
    {
        $this->errors[] = $error;
    }

    protected function getErrors()
    {
        return $this->errors;
    }
}
