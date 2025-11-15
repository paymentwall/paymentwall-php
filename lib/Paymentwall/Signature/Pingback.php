<?php
namespace Paymentwall\Signature;

class Pingback extends Signature
{
    public function process($params = [], $version = 0)
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

    public function prepareParams($params = [], $baseString = '')
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
