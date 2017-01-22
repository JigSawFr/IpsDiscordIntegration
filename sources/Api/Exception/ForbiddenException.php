<?php

namespace IPS\discord\Api\Exception;

/**
 * Class ForbiddenException
 *
 * @package IPS\discord\Api\Exception
 */
class _ForbiddenException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode = 403;
}
