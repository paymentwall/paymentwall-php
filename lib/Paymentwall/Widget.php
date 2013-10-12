<?php

class Paymentwall_Widget extends Paymentwall_Base
{
	/**
	 * Widget call URL
	 */
	const BASE_URL = 'https://api.paymentwall.com/api';

	/**
	 * Get default signature version for this API type
	 * 
	 * @return int
	 */
	public function getDefaultSignatureVersion() {
		return self::getApiType() != self::API_CART ? self::DEFAULT_SIGNATURE_VERSION : self::SIGNATURE_VERSION_2;
	}

	/**
	 * Return URL for the widget based on params supplied
	 *
	 * @param string $userId identifier of the end-user who is viewing the widget
	 * @param string $widgetCode e.g. p1 or p1_1, can be found inside of your Paymentwall Merchant account in the Widgets section
	 * @param array $extraParams array of additional params that will be included into the widget URL, e.g. 'sign_version' or 'email'
	 * @param array $products array that consists of Paymentwall_Product entities; for flexible widget call use array of 1 product
	 * @return string
	 */
	public function getUrl($userId, $widgetCode, $extraParams = array(), $products = array())
	{
		$params = array(
			'key' => self::getAppKey(),
			'uid' => $userId,
			'widget' => $widgetCode
		);

		$productsNumber = count($products);

		if (self::getApiType() == self::API_GOODS) {

			if (!empty($products)) {

				if ($productsNumber == 1) {

					$product = current($products);

					$params['amount'] = $product->getAmount();
					$params['currencyCode'] = $product->getCurrencyCode();
					$params['ag_name'] = $product->getName();
					$params['ag_external_id'] = $product->getId();
					$params['ag_type'] = $product->getType();

					if ($product->getType() == Paymentwall_Product::TYPE_SUBSCRIPTION) {
						$params['ag_period_length'] = $product->getPeriodLength();
						$params['ag_period_type'] = $product->getPeriodType();
						if ($product->isRecurring()) {
							$params['ag_recurring'] = intval($product->isRecurring());
						}
					}

				} else {
					//TODO: $this->appendToErrors('Only 1 product is allowed in flexible widget call');
				}

			}

		} else if (self::getApiType() == self::API_CART) {

			$index = 0;
			foreach ($products as $product) {
				$params['external_ids[' . $index . ']'] = $product->getId();

				if (isset($product->amount)) {
					$params['prices[' . $index . ']'] = $product->getAmount();
				}
				if (isset($product->currencyCode)) {
					$params['currencies[' . $index . ']'] = $product->getCurrencyCode();
				}

				$index++;
			}
			unset($index);
		}

		$params['sign_version'] = $signatureVersion = self::getDefaultSignatureVersion();

		if (!empty($extraParams['sign_version'])) {
			$signatureVersion = $params['sign_version'] = $extraParams['sign_version'];
		}

		$params = array_merge($params, $extraParams);
		$params['sign'] = $this->calculateSignature($params, self::getSecretKey(), $signatureVersion);

		return self::BASE_URL . '/' . self::buildController($widgetCode) . '?' . http_build_query($params);
	}

	/**
	 * Return HTML code for the widget based on params supplied
	 *
	 * @param string $userId
	 * @param string $widgetCode
	 * @param array $extraParams array that consists of addition params, e.g. 'sign_version'
	 * @param array $products array that consists of PWProduct entities
	 * @param array $options
	 * @return string
	 */
	public function getHtmlCode($userId, $widgetCode, $extraParams = array(), $products = array(), $options = array())
	{

		$defaultOptions = array(
			'frameborder' => '0',
			'width' => '750',
			'height' => '800'
		);

		$options = array_merge($defaultOptions, $options);

		$optionsQuery = '';
		foreach ($options as $attr => $value) {
			$optionsQuery .= ' ' . $attr . '="' . $value . '"';
		}

		return '<iframe src="' . $this->getUrl($userId, $widgetCode, $extraParams, $products) . '" ' . $optionsQuery . '></iframe>';

	}

	/**
	 * Build controller URL depending on API type
	 *
	 * @param string $widget code of the widget
	 * @param bool $flexibleCall
	 * @return string
	 */
	protected function buildController($widget, $flexibleCall = false)
	{
		if (self::getApiType() == self::API_VC) {

			if (!preg_match('/^w|s|mw/', $widget)) {
				return self::CONTROLLER_PAYMENT_VIRTUAL_CURRENCY;
			}

		} else if (self::getApiType() == self::API_GOODS) {

			if (!$flexibleCall) {
				if (!preg_match('/^w|s|mw/', $widget)) {
					return self::CONTROLLER_PAYMENT_DIGITAL_GOODS;
				}
			} else {
				return self::CONTROLLER_PAYMENT_DIGITAL_GOODS;
			}

		} else {

			return self::CONTROLLER_PAYMENT_CART;

		}
	}

	/**
	 * Build signature for the widget specified
	 *
	 * @param array $params
	 * @param string $secret Paymentwall Secret Key
	 * @param int $version Paymentwall Signature Version
	 * @return string
	 */
	protected function calculateSignature($params, $secret, $version)
	{
		$baseString = '';

		if ($version == self::SIGNATURE_VERSION_1) {
			// TODO: throw exception if no uid parameter is present
			
			$baseString .= isset($params['uid']) ? $params['uid'] : '';
			$baseString .= $secret;

			return md5($baseString);

		} else {

			ksort($params);

			foreach ($params as $key => $value) {
				$baseString .= $key . '=' . $value;
			}
			$baseString .= $secret;

			if ($version == self::SIGNATURE_VERSION_2) {
				return md5($baseString);
			}
			return hash('sha256', $baseString);
		}
	}
}
