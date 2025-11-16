<?php

namespace Paymentwall\Signature;

class Pingback extends Signature
{
    public function process(array $params = [], int $version = 0): string
    {
        $baseString = '';

        unset($params['sig']);

        if ($version == self::VERSION_TWO || $version == self::VERSION_THREE) {
            self::ksortMultiDimensional($params);
        }

        $baseString = $this->prepareParams($params, $baseString);

        $baseString .= $this->getConfig()->getPrivateKey();

        if ($version == self::VERSION_THREE) {
            return hash('sha256', $baseString);
        }

        return md5($baseString);
    }

    public function prepareParams(array $params = [], string $baseString = ''): string
    {
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $baseString .= $key . '[' . $k . ']' . '=' . $v;
                }
            } else {
                $baseString .= $key . '=' . $value;
            }
        }
        return $baseString;
    }
}
