<?php
namespace Paymentwall\Response;

abstract class Response
{
    protected array $response;

    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    protected function wrapInternalError(): false|string
    {
        $response = [
            'success' => 0,
            'error' => [
                'message' => 'Internal error',
            ],
        ];
        return json_encode($response);
    }
}
