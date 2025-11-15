<?php

namespace Paymentwall\Response;

class Error extends Response implements ResponseInterface
{
    public function process(): false|string
    {
        if (!isset($this->response)) {
            return $this->wrapInternalError();
        }

        $response = [
            'success' => 0,
            'error' => $this->getErrorMessageAndCode($this->response),
        ];

        return json_encode($response);
    }

    public function getErrorMessageAndCode(array $response): array
    {
        return [
            'message' => $response['error'],
            'code' => $response['code'],
        ];
    }
}
