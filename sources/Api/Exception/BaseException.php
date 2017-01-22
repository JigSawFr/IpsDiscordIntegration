<?php

namespace IPS\discord\Api\Exception;

/**
 * Class BaseException
 *
 * @package IPS\discord\Api\Exception
 */
class _BaseException extends \Exception
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode;

    /**
     * @return string
     */
    public function getErrorLangString()
    {
        return get_called_class();
    }
}
