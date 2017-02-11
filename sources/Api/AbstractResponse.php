<?php

namespace IPS\discord\Api;

/**
 * Class AbstractResponse
 *
 * @package IPS\discord\Api
 */
abstract class _AbstractResponse
{
    /**
     * @var \IPS\discord\Api $api
     */
    protected $api;

    /**
     * @var array $successCodes
     */
    protected $successCodes = [
        200, 201, 204, 304
    ];

    /**
     * @var array $errorCodes
     */
    protected $errorCodes;

    /**
     * AbstractResponse constructor.
     */
    public function __construct()
    {
        $this->api = \IPS\discord\Api::i();

        $this->errorCodes = [
            Exception\BadRequestException::$httpErrorCode => Exception\BadRequestException::class,
            Exception\UnauthorizedException::$httpErrorCode => Exception\UnauthorizedException::class,
            Exception\ForbiddenException::$httpErrorCode => Exception\ForbiddenException::class,
            Exception\NotFoundException::$httpErrorCode => Exception\NotFoundException::class,
            Exception\MethodNotAllowedException::$httpErrorCode => Exception\MethodNotAllowedException::class,
            Exception\TooManyRequestsException::$httpErrorCode => Exception\TooManyRequestsException::class,
            Exception\GatewayUnavailableException::$httpErrorCode => Exception\GatewayUnavailableException::class
        ];
    }

    /**
     * Handle the api response, always call this instead of api->send();
     *
     * @return array|NULL
     */
    protected function handleApi()
    {
        $response = $this->api->send();

        $statusCode = $response->httpResponseCode;

        if ( in_array( $statusCode, $this->successCodes ) ) {
            return $response->decodeJson();
        }

        try {
            $this->throwException( $statusCode );
        } catch ( Exception\NotFoundException $e ) {
            /* Ignore not found exceptions as members can leave discord any time etc. */
        }

        return $response->decodeJson();
    }

    /**
     * Throw correct exception according to the http response code.
     *
     * @param int $statusCode
     *
     * @throws Exception\BadRequestException
     * @throws Exception\ForbiddenException
     * @throws Exception\GatewayUnavailableException
     * @throws Exception\MethodNotAllowedException
     * @throws Exception\NotFoundException
     * @throws Exception\TooManyRequestsException
     * @throws Exception\UnauthorizedException
     * @throws Exception\UnknownErrorException
     */
    protected function throwException( $statusCode )
    {
        $this->checkIfServerError( $statusCode );

        if ( array_key_exists( $statusCode, $this->errorCodes ) ) {
            throw new $this->errorCodes[$statusCode];
        }

        throw new \IPS\discord\Api\Exception\UnknownErrorException();
    }

    /**
     * Throw server exception if response code is 5xx but not 502 (Bad Gateway).
     *
     * @param int $statusCode
     *
     * @return void
     * @throws Exception\ServerErrorException
     */
    protected function checkIfServerError( $statusCode )
    {
        $stringStatusCode = (string) $statusCode;

        if ( mb_substr( $stringStatusCode, 0, 1 ) !== '5' || $statusCode === 502 )
        {
            return;
        }

        throw new \IPS\discord\Api\Exception\ServerErrorException();
    }
}
