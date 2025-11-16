<?php

namespace Paymentwall;

class HttpAction extends Instance
{
    protected array $responseLogInformation = [];

    public function __construct(protected ApiObject $apiObject, protected array $apiParams = [], protected array $apiHeaders = [])
    {
    }

    public function getApiObject(): ApiObject
    {
        return $this->apiObject;
    }

    public function setApiObject(ApiObject $apiObject): void
    {
        $this->apiObject = $apiObject;
    }

    public function getApiParams(): array
    {
        return $this->apiParams;
    }

    public function setApiParams(array $params = []): void
    {
        $this->apiParams = $params;
    }

    public function getApiHeaders(): array
    {
        return $this->apiHeaders;
    }

    public function setApiHeaders(array $headers = []): void
    {
        $this->apiHeaders = $headers;
    }

    public function run(): ?string
    {
        $result = null;

        if ($this->getApiObject() instanceof ApiObject) {
            $result = $this->apiObjectPostRequest($this->getApiObject());
        }

        return $result;
    }

    public function apiObjectPostRequest(ApiObject $object): string
    {
        return $this->request('POST', $object->getApiUrl(), $this->getApiParams(), $this->getApiHeaders());
    }

    public function post(string $url = ''): string
    {
        return $this->request('POST', $url, $this->getApiParams(), $this->getApiHeaders());
    }

    public function get(string $url = ''): string
    {
        return $this->request('GET', $url, $this->getApiParams(), $this->getApiHeaders());
    }

    protected function request(string $httpVerb = '', string $url = '', array $params = [], array $customHeaders = []): string
    {
        $curl = curl_init();

        $headers = [
            $this->getLibraryDefaultRequestHeader(),
        ];

        if (!empty($customHeaders)) {
            $headers = array_merge($headers, $customHeaders);
        }

        if (!empty($params)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        // CURL_SSLVERSION_TLSv1_2 is defined in libcurl version 7.34 or later
        // but unless PHP has been compiled with the correct libcurl headers it
        // won't be defined in your PHP instance.  PHP > 5.5.19 or > 5.6.3
        if (! defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }

        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $httpVerb);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, true);

        $response = curl_exec($curl);

        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        $this->responseLogInformation = [
            'header' => $header,
            'body' => $body,
            'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
        ];

        curl_close($curl);

        return $this->prepareResponse($body);
    }

    protected function getLibraryDefaultRequestHeader(): string
    {
        return 'User-Agent: Paymentwall PHP Library v. ' . $this->getConfig()->getVersion();
    }

    protected function prepareResponse(string $string = ''): string
    {
        return preg_replace('/\x{FEFF}/u', '', $string);
    }

    public function getResponseLogInformation(): array
    {
        return $this->responseLogInformation;
    }
}
