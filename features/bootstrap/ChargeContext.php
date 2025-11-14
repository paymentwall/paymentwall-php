<?php

use Behat\Behat\Context\Context;

class ChargeContext implements Context
{
    private $token = null;
    private $chargeId = null;
    private $cvv = '123';

    /**
     * @Given /^CVV code "([^"]*)"$/
     */
    public function cvvCode($cvvCode)
    {
        $this->cvv = $cvvCode;
    }

    /**
     * @Given /^charge ID "([^"]*)"$/
     */
    public function chargeId($chargeId)
    {
        $this->chargeId = $chargeId;
    }

    /**
    * @When /^test token is retrieved$/
    */
    public function testTokenIsRetrieved()
    {
        $tokenModel = new Paymentwall_OneTimeToken();
        $this->token = $tokenModel->create($this->getTestDetailsForOneTimeToken())->getToken();
        if (strpos($this->token, 'ot_') === false) {
            throw new Exception($this->token->getPublicData());
        }
    }

    /**
    * @Then /^charge should be successful$/
    */
    public function chargeShouldBeSuccessful()
    {
        $charge = $this->getChargeObject();
        if (!$charge->isSuccessful()) {
            throw new Exception($charge->getPublicData());
        }
    }

    /**
    * @Then /^charge should be refunded$/
    */
    public function chargeShouldBeRefunded()
    {
        $chargeToBeRefunded = new Paymentwall_Charge($this->chargeId);
        if (!$chargeToBeRefunded->refund()->isRefunded()) {
            throw new Exception($chargeToBeRefunded->getPublicData());
        }
    }

    /**
    * @Then /^I see this error message "([^"]*)"$/
    */
    public function iSeeThisErrorMessage($errorMessage = '')
    {
        $charge = $this->getChargeObject();
        $errors = json_decode($charge->getPublicData(), true);
        if (strpos($errorMessage, $errors['error']['message']) === false) {
            throw new Exception($charge->getPublicData());
        }
    }

    protected function getChargeObject()
    {
        $chargeModel = new Paymentwall_Charge();
        return $chargeModel->create($this->getTestDetailsForCharge());
    }

    protected function getTestDetailsForCharge()
    {
        return [
            'token' => $this->token,
            'email' => 'test@user.com',
            'currency' => 'USD',
            'amount' => 9.99,
            'browser_domain' => 'https://www.paymentwall.com',
            'browser_ip' => '72.229.28.185',
            'description' => 'Test Charge',
        ];
    }

    protected function getTestDetailsForOneTimeToken()
    {
        return array_merge(
            ['public_key' => Paymentwall_Config::getInstance()->getPublicKey()],
            $this->getTestCardDetails()
        );
    }

    protected function getTestCardDetails()
    {
        return [
            'card[number]' => '4242424242424242',
            'card[exp_month]' => '11',
            'card[exp_year]' => '19',
            'card[cvv]' => $this->cvv,
        ];
    }
}
