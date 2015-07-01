<?php

abstract class Paymentwall_GenerericApiObject extends Paymentwall_ApiObject
{
	/**
	 * Build new Api Object
	 *
	 * @param string $api
	 * @param array $params
	 *
	 * @return \Paymentwall_GenerericApiObject|null
	 */
	public static function factory($api, $params = array())
	{
		$allowedApis = array(
			'delivery'
		);

		if (!in_array($api, $allowedApis)) {
			return null;
		}

		$class = 'Paymentwall_' . ucfirst($api);

		return new $class($params);
	}
}