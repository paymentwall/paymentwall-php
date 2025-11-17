<?php

use Behat\Behat\Context\Context;

class ChargeContext implements Context
{
    private $token = null;
    private $chargeId = null;
    private $cvv = '123';

    #[\Behat\Step\Given('CVV code ":cvvCode"')]
    public function cvvCode($cvvCode): void
    {
        $this->cvv = $cvvCode;
    }

    #[\Behat\Step\Given('charge ID ":chargeId"')]
    public function chargeId($chargeId): void
    {
        $this->chargeId = $chargeId;
    }

    #[\Behat\Step\When('test token is retrieved')]
    public function testTokenIsRetrieved(): void
    {
        $tokenModel = new \Paymentwall\OneTimeToken();
        $this->token = $tokenModel->create($this->getTestDetailsForOneTimeToken())->getToken();
        if (!str_contains($this->token, 'ot_')) {
            throw new \Exception($this->token->getPublicData());
        }
    }

    #[\Behat\Step\Then('charge should be successful')]
    public function chargeShouldBeSuccessful(): void
    {
        $charge = $this->getChargeObject();
        if (!$charge->isSuccessful()) {
            throw new \Exception($charge->getPublicData());
        }
    }

    #[\Behat\Step\Then('charge should be refunded')]
    public function chargeShouldBeRefunded(): void
    {
        $chargeToBeRefunded = new \Paymentwall\Charge($this->chargeId);
        if (!$chargeToBeRefunded->refund()->isRefunded()) {
            throw new \Exception($chargeToBeRefunded->getPublicData());
        }
    }

    #[\Behat\Step\Then('I see this error message ":errorMessage"')]
    public function iSeeThisErrorMessage($errorMessage = ''): void
    {
        $charge = $this->getChargeObject();
        $errors = json_decode($charge->getPublicData(), true);
        if (!str_contains($errorMessage, $errors['error']['message'])) {
            throw new \Exception($charge->getPublicData());
        }
    }

    protected function getChargeObject(): \Paymentwall\ApiObject
    {
        $chargeModel = new \Paymentwall\Charge();
        return $chargeModel->create($this->getTestDetailsForCharge());
    }

    protected function getTestDetailsForCharge(): array
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

    protected function getTestDetailsForOneTimeToken(): array
    {
        return array_merge(
            ['public_key' => \Paymentwall\Config::getInstance()->getPublicKey()],
            $this->getTestCardDetails()
        );
    }

    protected function getTestCardDetails(): array
    {
        return [
            'card[number]' => '4242424242424242',
            'card[exp_month]' => '11',
            'card[exp_year]' => '19',
            'card[cvv]' => $this->cvv,
        ];
    }
}
