<?php
namespace Paymentwall\Response;

class Success extends Response implements ResponseInterface
{
    public function process(): false|string
    {
        if (!isset($this->response)) {
            return $this->wrapInternalError();
        }

        $response = [
            'success' => 1,
        ];

        return json_encode($response);
    }
}
