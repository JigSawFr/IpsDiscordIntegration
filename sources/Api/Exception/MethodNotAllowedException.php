<?php

namespace IPS\discord\Api\Exception;

/**
 * Class MethodNotAllowedException
 *
 * @package IPS\discord\Api\Exception
 */
class _MethodNotAllowedException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode = 405;
}
