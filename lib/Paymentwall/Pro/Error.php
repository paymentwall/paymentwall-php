<?php

class Paymentwall_Pro_Error
{
	const ERROR = 'error';
	const ERROR_MESSAGE = 'error';
	const ERROR_CODE = 'code';

	const RISK = 'risk';
	const RISK_PENDING = 'pending';
	const CLICK_ID = 'id';
	const SUPPORT_LINK = 'support_link';

	/**
	 * Error codes
	 */
	const CHARGE_CARD_NUMBER_ERROR = 3101;
	const CHARGE_WRONG_CARD_NUMBER = 3003;
	const CHARGE_WRONG_EXP_MONTH = 3004;
	const CHARGE_WRONG_EXP_DATE = 3006;

	const CHARGE_WRONG_AMOUNT = 3002;

	const USER_BANNED = 5000;

	/**
	 * Messages with fields to highlight in JavaScript library corresponding to error codes
	 */
	static $messages = array(
		self::CHARGE_CARD_NUMBER_ERROR => array('field' => 'cc-number'),
		self::CHARGE_WRONG_CARD_NUMBER => array('field' => 'cc-number'),
		self::CHARGE_WRONG_EXP_MONTH => array('field' => 'cc-expiry'),
		self::CHARGE_WRONG_EXP_DATE => array('field' => 'cc-expiry'),
		self::CHARGE_WRONG_AMOUNT => array('field' => ''),
		self::USER_BANNED => array('field' => '')
	);

	public static function getFieldFromMessages($errorCode) {
		return (array_key_exists($errorCode, self::$messages)) ? self::$messages[$errorCode]['field'] : null;
	}

	public static function isError($response) {
		return isset($response['type']) ? $response['type'] === 'Error' : null;
	}

	public static function wrapError($response) {
		$result = array(
			'error' => $response
		);
		return $result;
	}

	public static function wrapInternalError($response) {
		$result = array(
			'success' => 0,
			'error' => array(
				'message' => 'Sorry, internal error occurred'
			)
		);
		return $result;
	}

	public static function getPublicData($properties) {
		if (isset($properties[self::ERROR])) {
			return array(
				'success' => 0,
				'error' => array(
					'message' => $properties[self::ERROR][self::ERROR_MESSAGE],
					'field' => self::getFieldFromMessages($properties[self::ERROR][self::ERROR_CODE])
				)
			);
		} else if (isset($properties[self::RISK]) && $properties[self::RISK] == self::RISK_PENDING) {
			return array(
				'risk' => 1,
				'support_link' => $properties[self::SUPPORT_LINK],
				'click_id' => $properties[self::CLICK_ID]
			);
		} else {
			return array(
				'success' => 1
			);
		}
	}
}