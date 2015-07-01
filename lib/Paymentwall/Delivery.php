<?php

class Paymentwall_Delivery extends Paymentwall_GenerericApiObject
{
	/**
	 * @see Paymentwall_ApiObject::getEndpointName()
	 */
	public function getEndpointName()
	{
		return self::API_OBJECT_DELIVERY;
	}

	/**
	 * Check is request was successful
	 *
	 * @return bool
	 */
	public function isSuccessful()
	{
		return (bool) $this->success;
	}

	/**
	 * Check is request was unsuccessful
	 *
	 * @return bool
	 */
	public function isUnsuccessful()
	{
		return (bool) $this->error_code;
	}

	/**
	 * Get request error
	 *
	 * @return string|null
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * Get request notices
	 *
	 * @return array
	 */
	public function getNotices()
	{
		return $this->notices;
	}

}