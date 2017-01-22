<?php

namespace IPS\discord\Api\Exception;

/**
 * Class NotFoundException
 *
 * @package IPS\discord\Api\Exception
 */
class _NotFoundException extends \IPS\discord\Api\Exception\BaseException
{
    /**
     * @var integer $httpErrorCode
     */
    public static $httpErrorCode = 404;
}
