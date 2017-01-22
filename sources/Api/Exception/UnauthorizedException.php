<?php

namespace IPS\discord\Api\Exception;

/**
 * Class UnauthorizedException
 *
 * @package IPS\discord\Api\Exception
 */
class _UnauthorizedException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode = 401;
}
