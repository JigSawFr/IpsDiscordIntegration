<?php

namespace IPS\discord\Api\Exception;

/**
 * Class TooManyRequestsException
 *
 * @package IPS\discord\Api\Exception
 */
class _TooManyRequestsException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode = 429;
}
