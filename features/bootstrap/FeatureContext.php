<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

require_once('lib/paymentwall.php');

/**
 * Features context.
 */
class FeatureContext implements Context
{
    public $apiType;

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     */
    public function __construct()
    {
    }

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
        Paymentwall_Config::getInstance()->set(array(
            'private_key' => $privateKey
        ));
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
