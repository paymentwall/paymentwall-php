<?php

abstract class Paymentwall_ApiObject extends Paymentwall_Instance
{
	const API_BRICK_SUBPATH			= 'brick';
	const API_OBJECT_CHARGE 		= 'charge';
	const API_OBJECT_SUBSCRIPTION 	= 'subscription';
	const API_OBJECT_ONE_TIME_TOKEN = 'token';

	protected $properties = array();
	protected $_id;
	protected $_rawResponse = '';
	protected $_responseLogInformation = array();
	protected $brickSubEndpoints = array(
		self::API_OBJECT_CHARGE, self::API_OBJECT_SUBSCRIPTION, self::API_OBJECT_ONE_TIME_TOKEN
	);

	abstract function getEndpointName();

	public function __construct($id = '')
	{
		if (!empty($id)) {
			$this->_id = $id;
		}
	}

	public final function create($params = array())
	{
		$httpAction = new Paymentwall_HttpAction($this, $params, array(
			$this->getApiBaseHeader()
		));
		$this->setPropertiesFromResponse($httpAction->run());
		return $this;
	}

	public function __get($property)
	{
		return isset($this->properties[$property]) ? $this->properties[$property] : null;
	}

	public function getApiUrl()
	{
		if ($this->getEndpointName() === self::API_OBJECT_ONE_TIME_TOKEN && !$this->getConfig()->isTest()) {
			return Paymentwall_OneTimeToken::GATEWAY_TOKENIZATION_URL;
		} else {
			return $this->getApiBaseUrl() . $this->getSubPath() . '/' . $this->getEndpointName();
		}
	}

	public function getPublicData()
	{
		$responseModel = Paymentwall_Response_Factory::get($this->getPropertiesFromResponse());
		return $responseModel instanceof Paymentwall_Response_Interface ? $responseModel->process() : '';
	}

	public function getProperties() {
		return $this->properties;
	}

	public function getRawResponseData()
	{
		return $this->_rawResponse;
	}

	protected function setPropertiesFromResponse($response = '')
	{
		if (!empty($response)) {
			$this->_rawResponse = $response;
			$this->properties = (array) $this->preparePropertiesFromResponse($response);
		} else {
			throw new Exception('Empty response');
		}
	}

	protected function getSubPath()
	{
		return (in_array($this->getEndpointName(), $this->brickSubEndpoints))
				? '/' . self::API_BRICK_SUBPATH
				: '';
	}

	protected function getPropertiesFromResponse()
	{
		return $this->properties;
	}

	protected function preparePropertiesFromResponse($string = '')
	{
		return json_decode($string, false);
	}

	protected function getApiBaseHeader()
	{
		return 'X-ApiKey: ' . $this->getPrivateKey();
	}

	protected function doApiAction($action = '', $method = 'post')
	{
		$actionUrl = $this->getApiUrl() . '/' . $this->_id . '/' . $action;
		$httpAction = new Paymentwall_HttpAction($this, array('id' => $this->_id), array(
			$this->getApiBaseHeader()
		));
		$this->_responseLogInformation = $httpAction->getResponseLogInformation();
		$this->setPropertiesFromResponse(
			$method == 'get' ? $httpAction->get($actionUrl) : $httpAction->post($actionUrl)
		);

		return $this;
	}

	protected function getResponseLogInformation()
	{
		return $this->_responseLogInformation;
	}


}