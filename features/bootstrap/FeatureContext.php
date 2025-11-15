<?php

namespace Paymentwall;

use Behat\Behat\Context\Context;

/**
 * Features context.
 */
class FeatureContext implements Context
{
    public $apiType;

    /**
     * @Given /^Public key "([^"]*)"$/
     */
    public function publicKey($publicKey)
    {
        Base::setAppKey($publicKey);
    }

    /**
     * @Given /^Secret key "([^"]*)"$/
     */
    public function secretKey($secretKey)
    {
        Base::setSecretKey($secretKey);
    }

    /**
     * @Given /^Private key "([^"]*)"$/
     */
    public function privateKey($privateKey)
    {
        Config::getInstance()->set([
            'private_key' => $privateKey,
        ]);
    }

    /**
     * @Given /^API type "([^"]*)"$/
     */
    public function apiType($apiType)
    {
        Base::setApiType($apiType);
        $this->apiType = $apiType;
    }
}
