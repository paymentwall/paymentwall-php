<?php

namespace Paymentwall;

class Charge extends ApiObject implements ApiObjectInterface
{
    public $card;

    public function getId()
    {
        return $this->id;
    }

    public function isTest()
    {
        return $this->test;
    }

    public function isSuccessful(): bool
    {
        return $this->object == ApiObject::API_OBJECT_CHARGE;
    }

    public function isCaptured()
    {
        return $this->captured;
    }

    public function isUnderReview(): bool
    {
        return $this->risk == 'pending';
    }

    public function isRefunded()
    {
        return $this->refunded;
    }

    public function setPropertiesFromResponse(string $response = ''): void
    {
        parent::setPropertiesFromResponse($response);
        $this->card = new Card($this->card);
    }

    public function getEndpointName(): string
    {
        return ApiObject::API_OBJECT_CHARGE;
    }

    public function getCard(): Card
    {
        return new Card($this->card);
    }

    public function get(): self
    {
        return $this->doApiAction('', 'get');
    }

    public function refund(): self
    {
        return $this->doApiAction('refund');
    }

    public function capture(): self
    {
        return $this->doApiAction('capture');
    }

    public function void(): self
    {
        return $this->doApiAction('void');
    }
}
