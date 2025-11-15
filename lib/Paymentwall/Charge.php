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

    public function isSuccessful()
    {
        return $this->object == self::API_OBJECT_CHARGE;
    }

    public function isCaptured()
    {
        return $this->captured;
    }

    public function isUnderReview()
    {
        return $this->risk == 'pending';
    }

    public function isRefunded()
    {
        return $this->refunded;
    }

    public function setPropertiesFromResponse($response = '')
    {
        parent::setPropertiesFromResponse($response);
        $this->card = new Card($this->card);
    }

    public function getEndpointName()
    {
        return self::API_OBJECT_CHARGE;
    }

    public function getCard()
    {
        return new Card($this->card);
    }

    public function get()
    {
        return $this->doApiAction('', 'get');
    }

    public function refund()
    {
        return $this->doApiAction('refund');
    }

    public function capture()
    {
        return $this->doApiAction('capture');
    }

    public function void()
    {
        return $this->doApiAction('void');
    }
}
