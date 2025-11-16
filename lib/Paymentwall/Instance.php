<?php

namespace Paymentwall;

abstract class Instance
{
    protected Config $config;
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

    protected function getApiBaseUrl(): string
    {
        return $this->getConfig()->getApiBaseUrl();
    }

    protected function getApiType(): int
    {
        return $this->getConfig()->getLocalApiType();
    }

    protected function getPublicKey(): string
    {
        return $this->getConfig()->getPublicKey();
    }

    protected function getPrivateKey(): string
    {
        return $this->getConfig()->getPrivateKey();
    }

    protected function appendToErrors($error = ''): void
    {
        $this->errors[] = $error;
    }

    protected function getErrors(): array
    {
        return $this->errors;
    }
}
