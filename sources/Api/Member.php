<?php

namespace IPS\discord\Api;

/**
 * Class Member
 *
 * @package IPS\discord\Api
 */
class _Member extends \IPS\discord\Api\AbstractResponse
{
    /**
     * Get member object (not guild related) by token.
     *
     * @param string $token
     * @return array
     */
    public function getDiscordUser( $token )
    {
        $this->api->setUrl( \IPS\discord\Api::API_URL )
            ->setUri( 'users/@me' )
            ->setMethod( 'get' )
            ->setParams( [] )
            ->setAuthType( \IPS\discord\Api::AUTH_TYPE_OAUTH )
            ->setToken( $token );

        return $this->handleApi();
    }
}
