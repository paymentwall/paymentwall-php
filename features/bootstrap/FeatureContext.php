<?php

namespace Paymentwall;

use Behat\Behat\Context\Context;

/**
 * Features context.
 */
class FeatureContext implements Context
{
    public int $apiType;

    /**
     * @Given /^Public key "([^"]*)"$/
     */
    public function publicKey($publicKey): void
    {
        Config::getInstance()->setPublicKey($publicKey);
    }

    /**
     * @Given /^Secret key "([^"]*)"$/
     */
    public function secretKey($secretKey): void
    {
        Config::getInstance()->setPrivateKey($secretKey);
    }

    /**
     * @Given /^Private key "([^"]*)"$/
     */
    public function privateKey($privateKey): void
    {
        Config::getInstance()->set([
            'private_key' => $privateKey,
        ]);
    }

    /**
     * @Given /^API type "([^"]*)"$/
     */
    public function apiType($apiType): void
    {
        Config::getInstance()->setLocalApiType($apiType);
        $this->apiType = $apiType;
    }
}
