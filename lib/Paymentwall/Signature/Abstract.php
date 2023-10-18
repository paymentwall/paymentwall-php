<?php

abstract class Paymentwall_Signature_Abstract extends Paymentwall_Instance
{
	const VERSION_ONE = 1;
	const VERSION_TWO = 2;
	const VERSION_THREE	= 3;
	const DEFAULT_VERSION = 3;

	abstract function process($params = [], $version = 0);

	abstract function prepareParams($params = [], $baseString = '');

	public final function calculate($params = [], $version = 0)
	{
		return $this->process($params, $version);
	}

	protected static function ksortMultiDimensional(&$params = [])
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