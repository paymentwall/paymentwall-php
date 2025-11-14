<?php

class Paymentwall_Product
{
    public const TYPE_SUBSCRIPTION = 'subscription';
    public const TYPE_FIXED = 'fixed';

    public const PERIOD_TYPE_DAY = 'day';
    public const PERIOD_TYPE_WEEK = 'week';
    public const PERIOD_TYPE_MONTH = 'month';
    public const PERIOD_TYPE_YEAR = 'year';

    public $productId;
    public $amount;
    public $currencyCode;
    public $name;
    public $productType;
    public $periodLength;
    public $periodType;
    public $recurring;
    public $trialProduct;

    public function __construct($productId, $amount = 0.0, $currencyCode = null, $name = null, $productType = self::TYPE_FIXED, $periodLength = 0, $periodType = null, $recurring = false, Paymentwall_Product $trialProduct = null)
    {
        $this->productId = $productId;
        $this->amount = round($amount, 2);
        $this->currencyCode = $currencyCode;
        $this->name = $name;
        $this->productType = $productType;
        $this->periodLength = $periodLength;
        $this->periodType = $periodType;
        $this->recurring = $recurring;
        $this->trialProduct = ($productType == Paymentwall_Product::TYPE_SUBSCRIPTION && $recurring) ? $trialProduct : null;
    }

    public function getId()
    {
        return $this->productId;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->productType;
    }

    public function getPeriodType()
    {
        return $this->periodType;
    }

    public function getPeriodLength()
    {
        return $this->periodLength;
    }

    public function isRecurring()
    {
        return $this->recurring;
    }

    public function getTrialProduct()
    {
        return $this->trialProduct;
    }
}
