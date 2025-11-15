<?php

namespace Paymentwall\Response;

interface ResponseInterface
{
    public function process(): false|string;
}
