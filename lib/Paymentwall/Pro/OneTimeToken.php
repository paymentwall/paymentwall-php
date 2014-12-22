<?php

class Paymentwall_Pro_OneTimeToken
{

	public function __construct($params = array()) {
		$result = new Paymentwall_Pro_HttpWrapper($params);
		$this->properties = $result->tokenize();
	}

	public function getToken() {
		return $this->token;
	}

	public function __get($property) {
		return (isset($this->properties[$property])) ? $this->properties[$property] : null;
	}

}