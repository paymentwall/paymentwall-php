<?php

namespace Paymentwall;

class Product
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

    public function __construct(
        string $productId,
        $amount = 0.0,
        ?string $currencyCode = null,
        ?string $name = null,
        string $productType = self::TYPE_FIXED,
        int $periodLength = 0,
        ?string $periodType = null,
        $recurring = false,
        ?Product $trialProduct = null
    )
    {
        $this->productId = $productId;
        $this->amount = round($amount, 2);
        $this->currencyCode = $currencyCode;
        $this->name = $name;
        $this->productType = $productType;
        $this->periodLength = $periodLength;
        $this->periodType = $periodType;
        $this->recurring = $recurring;
        $this->trialProduct = ($productType == Product::TYPE_SUBSCRIPTION && $recurring) ? $trialProduct : null;
    }

    public function getId(): string
    {
        return $this->productId;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->productType;
    }

    public function getPeriodType(): string
    {
        return $this->periodType;
    }

    public function getPeriodLength(): int
    {
        return $this->periodLength;
    }

    public function isRecurring(): bool
    {
        return $this->recurring;
    }

    public function getTrialProduct()
    {
        return $this->trialProduct;
    }
}
