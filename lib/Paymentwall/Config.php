<?php

class Paymentwall_Config
{
	const VERSION = '2.2.2';
	const BASE_URL = 'https://api.paymentwall.com';

	/**
	 * @deprecated 2.2.2 
	 */
	const API_BASE_URL = self::BASE_URL . '/api';
	const API_VC	= 1;
	const API_GOODS	= 2;
	const API_CART	= 3;
	const API_CHECKOUT = 4;

	protected $baseUrl = self::BASE_URL;
	protected $apiType = self::API_GOODS;
	protected $publicKey;
	protected $privateKey;
	protected $apiBaseUrl = self::API_BASE_URL;

	private static $instance;

	public function setBaseUrl($url) 
	{
		$this->baseUrl = $url;
	}

	public function getBaseUrl() 
	{
		return $this->baseUrl;
	}

	public function getApiBaseUrl()
	{
		return $this->apiBaseUrl ? $this->apiBaseUrl : $this->getBaseUrl() . '/api';
	}

	/**
	 * @deprecated 2.2.2 Should use setBaseUrl instead
	 */
	public function setApiBaseUrl($url = '')
	{
		$this->apiBaseUrl = $url;
	}

	public function getLocalApiType()
	{
		return $this->apiType;
	}

	public function setLocalApiType($apiType = 0)
	{
		$this->apiType = $apiType;
	}

	public function getPublicKey()
	{
		return $this->publicKey;
	}

	public function setPublicKey($key = '')
	{
		$this->publicKey = $key;
	}

	public function getPrivateKey()
	{
		return $this->privateKey;
	}

	public function setPrivateKey($key = '')
	{
		$this->privateKey = $key;
	}

	public function getVersion()
	{
		return self::VERSION;
	}

	public function isTest()
	{
		return strpos($this->getPublicKey(), 't_') === 0;
	}

	public function set($config = array())
	{
		/**
		 * @deprecated 2.2.2
		 */
		if (isset($config['api_base_url'])) {
			$this->setApiBaseUrl($config['api_base_url']);
		}
		if (isset($config['base_url'])) {
			$this->setBaseUrl($config['base_url']);
		}
		if (isset($config['api_type'])) {
			$this->setLocalApiType($config['api_type']);
		}
		if (isset($config['public_key'])) {
			$this->setPublicKey($config['public_key']);
		}
		if (isset($config['private_key'])) {
			$this->setPrivateKey($config['private_key']);
		}
	}

	/**
	* @return $this Returns class instance.
	*/
	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	protected function __construct()
	{
	}

	private function __clone()
	{
	}
}
