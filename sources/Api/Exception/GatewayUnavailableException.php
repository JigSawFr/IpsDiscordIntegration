<?php

namespace IPS\discord\Api\Exception;

/**
 * Class GatewayUnavailableException
 *
 * @package IPS\discord\Api\Exception
 */
class _GatewayUnavailableException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode = 502;
}
