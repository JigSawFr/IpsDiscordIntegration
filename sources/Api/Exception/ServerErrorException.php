<?php

namespace IPS\discord\Api\Exception;

/**
 * Class ServerErrorException
 *
 * @package IPS\discord\Api\Exception
 */
class _ServerErrorException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode;
}
