<?php

namespace Paymentwall;

abstract class ApiObject extends Instance
{
    public const API_BRICK_SUBPATH = 'brick';
    public const API_OBJECT_CHARGE = 'charge';
    public const API_OBJECT_SUBSCRIPTION = 'subscription';
    public const API_OBJECT_ONE_TIME_TOKEN = 'token';

    protected array $properties = [];
    protected string $_id;
    protected string $_rawResponse = '';
    protected array $_responseLogInformation = [];
    protected array $brickSubEndpoints = [
        self::API_OBJECT_CHARGE, self::API_OBJECT_SUBSCRIPTION, self::API_OBJECT_ONE_TIME_TOKEN,
    ];

    abstract public function getEndpointName();

    public function __construct(string $id = '')
    {
        if (!empty($id)) {
            $this->_id = $id;
        }
    }

    final public function create(array $params = []): self
    {
        $httpAction = new HttpAction($this, $params, [$this->getApiBaseHeader()]);
        $this->setPropertiesFromResponse($httpAction->run());
        return $this;
    }

    public function __get($property): mixed
    {
        return $this->properties[$property] ?? null;
    }

    public function getApiUrl(): string
    {
        if ($this->getEndpointName() === self::API_OBJECT_ONE_TIME_TOKEN && !$this->getConfig()->isTest()) {
            return OneTimeToken::GATEWAY_TOKENIZATION_URL;
        } else {
            return $this->getApiBaseUrl() . $this->getSubPath() . '/' . $this->getEndpointName();
        }
    }

    /**
     * Returns raw data about the response that can be presented to the end-user:
     *  success => 0 or 1
     *  error =>
     *      message     - human-readable error message
     *      code        - error code, see https://www.paymentwall.com/us/documentation/Brick/2968#error
     *  secure =>
     *      formHTML    - needed to complete 3D Secure step, HTML of the form to be submitted to the user to redirect him to the bank page
     *
     * @return array
     *
     */
    public function _getPublicData(): array
    {
        /**
         * @todo encapsulate this into Factory better; right now it returns success=1 for 3ds case
         */
        $response = $this->getPropertiesFromResponse();
        $result = [];
        if (isset($response['type']) && $response['type'] == 'Error') {
            $result = [
                'success' => 0,
                'error' => [
                    'message' => $response['error'],
                    'code' => $response['code'],
                ],
            ];
        } elseif (!empty($response['secure'])) {
            $result = [
                'success' => 0,
                'secure' => $response['secure'],
            ];
        } elseif ($this->isSuccessful()) {
            $result['success'] = 1;
        } else {
            $result = [
                'success' => 0,
                'error' => [
                    'message' => 'Internal error',
                ],
            ];
        }
        return $result;
    }

    /**
     * @return string json encoded result of ApiObject::getPublicData()
     */
    public function getPublicData(): string
    {
        return json_encode($this->_getPublicData());
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getRawResponseData(): string
    {
        return $this->_rawResponse;
    }

    protected function setPropertiesFromResponse(string $response = ''): void
    {
        if (!empty($response)) {
            $this->_rawResponse = $response;
            $this->properties = (array) $this->preparePropertiesFromResponse($response);
        } else {
            throw new \Exception('Empty response');
        }
    }

    protected function getSubPath(): string
    {
        return (in_array($this->getEndpointName(), $this->brickSubEndpoints))
                ? '/' . self::API_BRICK_SUBPATH
                : '';
    }

    protected function getPropertiesFromResponse(): array
    {
        return $this->properties;
    }

    protected function preparePropertiesFromResponse(string $string = '')
    {
        return json_decode($string, false);
    }

    protected function getApiBaseHeader(): string
    {
        return 'X-ApiKey: ' . $this->getPrivateKey();
    }

    protected function doApiAction(string $action = '', string $method = 'post'): self
    {
        $actionUrl = $this->getApiUrl() . '/' . $this->_id . '/' . $action;
        $httpAction = new HttpAction($this, ['id' => $this->_id], [
            $this->getApiBaseHeader(),
        ]);
        $this->_responseLogInformation = $httpAction->getResponseLogInformation();
        $this->setPropertiesFromResponse(
            $method == 'get' ? $httpAction->get($actionUrl) : $httpAction->post($actionUrl)
        );

        return $this;
    }

    public function getResponseLogInformation(): array
    {
        return $this->_responseLogInformation;
    }
}
