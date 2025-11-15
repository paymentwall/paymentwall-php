<?php
namespace Paymentwall\Response;

class Factory
{
    public const CLASS_NAME_PREFIX = '';

    public const RESPONSE_SUCCESS = 'success';
    public const RESPONSE_ERROR = 'error';

    public static function get($response = [])
    {
        $responseModel = null;

        $responseModel = self::getClassName($response);

        return new $responseModel($response);
    }

    public static function getClassName($response = [])
    {
        $responseType = (isset($response['type']) && $response['type'] == 'Error') ? self::RESPONSE_ERROR : self::RESPONSE_SUCCESS;
        // Build fully qualified class name within this namespace
        return __NAMESPACE__ . '\\' . ucfirst($responseType);
    }
}
