<?php

use Behat\Behat\Context\Context;

class PingbackContext implements Context
{
    private array $pingbackParameters = [];
    private string $pingbackIpAddress = '';
    private \Paymentwall\Pingback $pingback;

    #[\Behat\Step\Given('Pingback GET parameters ":parameters"')]
    public function pingbackGetParameters($parameters): void
    {
        parse_str($parameters, $this->pingbackParameters);
    }

    #[\Behat\Step\Given('Pingback IP address ":ipAddress"')]
    public function pingbackIpAddress($ipAddress): void
    {
        $this->pingbackIpAddress = $ipAddress;
    }

    #[\Behat\Step\When('Pingback is constructed')]
    public function pingbackIsConstructed(): void
    {
        $this->pingback = new \Paymentwall\Pingback($this->pingbackParameters, $this->pingbackIpAddress);
    }

    #[\Behat\Step\Then('Pingback validation result should be ":value"')]
    public function pingbackValidationResultShouldBe($value): void
    {
        $validate = $this->pingback->validate();
        if ($validate !== $value) {
            throw new \Exception(
                'Pingback Validation returns ' . var_export($validate, true) . (!$validate ? ("\r\nErrors:" . $this->pingback->getErrorSummary()) : '')
            );
        }
    }

    #[\Behat\Step\Given('Pingback method ":method" should return ":value"')]
    public function pingbackMethodShouldReturn($method, $value): void
    {
        if ($this->pingback->$method() !== $value) {
            throw new \Exception(
                'Pingback method ' . $method . ' returned ' . var_export($value, true)
            );
        }
    }

    #[\Behat\Transformation\Transform('/^(true|false)$/')]
    public function castStringToBoolean($string): mixed
    {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }
}
