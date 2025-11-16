<?php

namespace Paymentwall\Response;

abstract class Response
{
    public function __construct(protected array $response = [])
    {
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
