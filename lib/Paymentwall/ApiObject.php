<?php

abstract class Paymentwall_ApiObject extends Paymentwall_Instance
{
	const API_OBJECT_CHARGE 		= 'charge';
	const API_OBJECT_SUBSCRIPTION 	= 'subscription';
	const API_OBJECT_ONE_TIME_TOKEN = 'token';

	protected $properties = array();
	protected $_id;

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
			return $this->getApiBaseUrl() . '/brick/' . $this->getEndpointName();
		}
	}

	public function getPublicData()
	{
		$responseModel = Paymentwall_Response_Factory::get($this->getPropertiesFromResponse());
		return $responseModel instanceof Paymentwall_Response_Interface ? $responseModel->process() : '';
	}

	protected function setPropertiesFromResponse($response = '')
	{
		if (!empty($response)) {
			$this->properties = $this->preparePropertiesFromResponse($response);
		} else {
			throw new Exception('Empty response');
		}
	}

	protected function getPropertiesFromResponse()
	{
		return $this->properties;
	}

	protected function preparePropertiesFromResponse($string = '')
	{
		return json_decode($string, true);
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
		$this->setPropertiesFromResponse(
			$method == 'get' ? $httpAction->get($actionUrl) : $httpAction->post($actionUrl)
		);
		return $this;
	}
}