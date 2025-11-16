<?php

namespace Paymentwall\Signature;

abstract class Signature extends \Paymentwall\Instance
{
    public const VERSION_ONE = 1;
    public const VERSION_TWO = 2;
    public const VERSION_THREE = 3;
    public const DEFAULT_VERSION = 3;

    abstract public function process(array $params = [], int $version = 0): string;

    abstract public function prepareParams(array $params = [], string $baseString = ''): string;

    final public function calculate($params = [], $version = 0): string
    {
        return $this->process($params, $version);
    }

    protected static function ksortMultiDimensional(&$params = []): void
    {
        if (is_array($params)) {
            ksort($params);
            foreach ($params as &$p) {
                if (is_array($p)) {
                    self::ksortMultiDimensional($p);
                }
            }
        }
    }
}
