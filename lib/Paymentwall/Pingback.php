<?php

namespace Paymentwall;

class Pingback extends Instance
{
    public const PINGBACK_TYPE_REGULAR = 0;
    public const PINGBACK_TYPE_GOODWILL = 1;
    public const PINGBACK_TYPE_NEGATIVE = 2;

    public const PINGBACK_TYPE_RISK_UNDER_REVIEW = 200;
    public const PINGBACK_TYPE_RISK_REVIEWED_ACCEPTED = 201;
    public const PINGBACK_TYPE_RISK_REVIEWED_DECLINED = 202;

    public const PINGBACK_TYPE_RISK_AUTHORIZATION_VOIDED = 203;

    public const PINGBACK_TYPE_SUBSCRIPTION_CANCELLATION = 12;
    public const PINGBACK_TYPE_SUBSCRIPTION_EXPIRED = 13;
    public const PINGBACK_TYPE_SUBSCRIPTION_PAYMENT_FAILED = 14;

    public function __construct(protected array $parameters, protected string $ipAddress)
    {
    }

    public function validate(bool $skipIpWhitelistCheck = false): bool
    {
        $validated = false;

        if ($this->isParametersValid()) {
            if ($skipIpWhitelistCheck || $this->isIpAddressValid()) {
                if ($this->isSignatureValid()) {
                    $validated = true;
                } else {
                    $this->appendToErrors('Wrong signature');
                }
            } else {
                $this->appendToErrors('IP address is not whitelisted');
            }
        } else {
            $this->appendToErrors('Missing parameters');
        }

        return $validated;
    }

    public function isSignatureValid(): bool
    {
        $signatureParamsToSign = [];

        if ($this->getApiType() == Config::API_VC) {
            $signatureParams = ['uid', 'currency', 'type', 'ref'];
        } elseif ($this->getApiType() == Config::API_GOODS) {
            $signatureParams = ['uid', 'goodsid', 'slength', 'speriod', 'type', 'ref'];
        } else { // API_CART
            $signatureParams = ['uid', 'goodsid', 'type', 'ref'];

            $this->parameters['sign_version'] = Signature\Signature::VERSION_TWO;
        }

        if (empty($this->parameters['sign_version']) || $this->parameters['sign_version'] == Signature\Signature::VERSION_ONE) {
            foreach ($signatureParams as $field) {
                $signatureParamsToSign[$field] = $this->parameters[$field] ?? null;
            }

            $this->parameters['sign_version'] = Signature\Signature::VERSION_ONE;
        } else {
            $signatureParamsToSign = $this->parameters;
        }

        $pingbackSignatureModel = new Signature\Pingback();
        $signatureCalculated = $pingbackSignatureModel->calculate(
            $signatureParamsToSign,
            $this->parameters['sign_version']
        );

        $signature = $this->parameters['sig'] ?? null;

        return $signature == $signatureCalculated;
    }

    public function isIpAddressValid(): bool
    {
        $ipsWhitelist = [
            '174.36.92.186',
            '174.36.96.66',
            '174.36.92.187',
            '174.36.92.192',
            '174.37.14.28',
        ];

        $rangesWhitelist = [
            '216.127.71.0/24',
        ];

        if (in_array($this->ipAddress, $ipsWhitelist)) {
            return true;
        }

        foreach ($rangesWhitelist as $range) {
            if ($this->isCidrMatched($this->ipAddress, $range)) {
                return true;
            }
        }

        return false;
    }

    public function isCidrMatched($ip, $range): bool
    {
        [$subnet, $bits] = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        return ($ip & $mask) == $subnet;
    }

    public function isParametersValid(): bool
    {
        $errorsNumber = 0;

        if ($this->getApiType() == Config::API_VC) {
            $requiredParams = ['uid', 'currency', 'type', 'ref', 'sig'];
        } elseif ($this->getApiType() == Config::API_GOODS) {
            $requiredParams = ['uid', 'goodsid', 'type', 'ref', 'sig'];
        } else { // Cart API
            $requiredParams = ['uid', 'goodsid', 'type', 'ref', 'sig'];
        }

        foreach ($requiredParams as $field) {
            if (!isset($this->parameters[$field]) || $this->parameters[$field] === '') {
                $this->appendToErrors('Parameter ' . $field . ' is missing');
                $errorsNumber++;
            }
        }

        return $errorsNumber == 0;
    }

    public function getParameter($param): mixed
    {
        return $this->parameters[$param] ?? null;
    }

    public function getType(): ?int
    {
        return isset($this->parameters['type']) ? intval($this->parameters['type']) : null;
    }

    public function getTypeVerbal(): string
    {
        $typeVerbal = '';
        $pingbackTypes = [
            self::PINGBACK_TYPE_SUBSCRIPTION_CANCELLATION => 'user_subscription_cancellation',
            self::PINGBACK_TYPE_SUBSCRIPTION_EXPIRED => 'user_subscription_expired',
            self::PINGBACK_TYPE_SUBSCRIPTION_PAYMENT_FAILED => 'user_subscription_payment_failed',
        ];

        if (!empty($this->parameters['type'])) {
            if (array_key_exists($this->parameters['type'], $pingbackTypes)) {
                $typeVerbal = $pingbackTypes[$this->parameters['type']];
            }
        }

        return $typeVerbal;
    }

    public function getUserId(): string
    {
        return $this->getParameter('uid');
    }

    public function getVirtualCurrencyAmount(): string
    {
        return $this->getParameter('currency');
    }

    public function getProductId(): string
    {
        return $this->getParameter('goodsid');
    }

    public function getProductPeriodLength(): string
    {
        return $this->getParameter('slength');
    }

    public function getProductPeriodType(): string
    {
        return $this->getParameter('speriod');
    }

    public function getProduct(): Product
    {
        return new Product(
            $this->getProductId(),
            0,
            null,
            null,
            $this->getProductPeriodLength() > 0 ? Product::TYPE_SUBSCRIPTION : Product::TYPE_FIXED,
            $this->getProductPeriodLength(),
            $this->getProductPeriodType()
        );
    }

    public function getProducts(): array
    {
        $result = [];
        $productIds = $this->getParameter('goodsid');

        if (!empty($productIds) && is_array($productIds)) {
            foreach ($productIds as $Id) {
                $result[] = new Product($Id);
            }
        }

        return $result;
    }

    public function getReferenceId(): string
    {
        return $this->getParameter('ref');
    }

    public function getPingbackUniqueId(): string
    {
        return $this->getReferenceId() . '_' . $this->getType();
    }

    public function isDeliverable(): bool
    {
        return (
            $this->getType() === self::PINGBACK_TYPE_REGULAR ||
            $this->getType() === self::PINGBACK_TYPE_GOODWILL ||
            $this->getType() === self::PINGBACK_TYPE_RISK_REVIEWED_ACCEPTED
        );
    }

    public function isCancelable(): bool
    {
        return (
            $this->getType() === self::PINGBACK_TYPE_NEGATIVE
            || $this->getType() === self::PINGBACK_TYPE_RISK_REVIEWED_DECLINED
        );
    }

    public function isUnderReview(): bool
    {
        return $this->getType() === self::PINGBACK_TYPE_RISK_UNDER_REVIEW;
    }
}
