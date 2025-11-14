<?php

use Behat\Behat\Context\Context;

require_once('lib/paymentwall.php');

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

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
        Paymentwall_Base::setAppKey($publicKey);
    }

    /**
     * @Given /^Secret key "([^"]*)"$/
     */
    public function secretKey($secretKey)
    {
        Paymentwall_Base::setSecretKey($secretKey);
    }

    /**
     * @Given /^Private key "([^"]*)"$/
     */
    public function privateKey($privateKey)
    {
        Paymentwall_Config::getInstance()->set([
            'private_key' => $privateKey,
        ]);
    }

    /**
     * @Given /^API type "([^"]*)"$/
     */
    public function apiType($apiType)
    {
        Paymentwall_Base::setApiType($apiType);
        $this->apiType = $apiType;
    }
}
