<?php

namespace Paymentwall;

class GenerericApiObject extends ApiObject
{
    protected HttpAction $httpAction;

    /**
     * @see \ApiObject
     */
    public function getEndpointName(): string
    {
        return $this->api;
    }

    public function __construct(protected string $api)
    {
        $this->httpAction = new HttpAction($this);
    }

    public function post(array $params = [], array $headers = []): ?array
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
