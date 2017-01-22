<?php

namespace IPS\discord\Api\Exception;

/**
 * Class BadRequestException
 *
 * @package IPS\discord\Api\Exception
 */
class _BadRequestException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode = 400;
}
