<?php
namespace Paymentwall;

class GenerericApiObject extends ApiObject
{
    /**
     * API type
     *
     * @var string
     */
    protected $api;

    /**
     * HttpAction object
     *
     * @var \Paymentwall\HttpAction
     */
    protected $httpAction;

    /**
     * @see \ApiObject
     */
    public function getEndpointName()
    {
        return $this->api;
    }

    public function __construct($type)
    {
        $this->api = $type;
        $this->httpAction = new HttpAction($this);
    }

    /**
     * Make post request
     *
     * @param array $params
     * @param array $headers
     *
     * @return array
     */
    public function post($params = [], $headers = [])
    {
        if (empty($params)) {
            return null;
        }

        $this->httpAction->setApiParams($params);

        $this->httpAction->setApiHeaders(array_merge([$this->getApiBaseHeader()], $headers));

        return (array) $this->preparePropertiesFromResponse(
            $this->httpAction->post(
                $this->getApiUrl()
            )
        );
    }
}
