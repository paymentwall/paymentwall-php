<?php

use Behat\Behat\Context\Context;

/**
 * Features context.
 */
class FeatureContext implements Context
{
    public int $apiType;

    #[\Behat\Step\Given('/^Public key "([^"]*)"$/')]
    public function publicKey($publicKey): void
    {
        \Paymentwall\Config::getInstance()->setPublicKey($publicKey);
    }

    #[\Behat\Step\Given('/^Secret key "([^"]*)"$/')]
    public function secretKey($secretKey): void
    {
        \Paymentwall\Config::getInstance()->setPrivateKey($secretKey);
    }

    #[\Behat\Step\Given('/^Private key "([^"]*)"$/')]
    public function privateKey($privateKey): void
    {
        \Paymentwall\Config::getInstance()->set([
            'private_key' => $privateKey,
        ]);
    }

    #[\Behat\Step\Given('/^API type "([^"]*)"$/')]
    public function apiType($apiType): void
    {
        \Paymentwall\Config::getInstance()->setLocalApiType($apiType);
        $this->apiType = $apiType;
    }
}
