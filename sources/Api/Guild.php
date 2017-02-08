<?php

namespace IPS\discord\Api;

/**
 * Class Guild
 *
 * @package IPS\discord\Api
 */
class _Guild extends \IPS\discord\Api\AbstractResponse
{
    /**
     * Get information from guild, which information depends on the passed $uri.
     *
     * @param string $uri
     * @return array
     */
    public function getInformation( $uri )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->api->setUrl( \IPS\discord\Api::API_URL )
            ->setAuthType( \IPS\discord\Api::AUTH_TYPE_BOT )
            ->setUri( 'guilds/{guild.id}/' . $uri )
            ->setMethod( 'get' )
            ->setParams([
                'limit' => 500
            ]);

        return $this->handleApi();
    }

    /**
     * Get guild member for the according IPS member.
     *
     * @param \IPS\Member $member
     * @return array|NULL
     */
    public function getMember( \IPS\Member $member )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if ( !$member->discord_id )
        {
            return NULL;
        }

        /** @noinspection PhpUndefinedFieldInspection */
        return $this->getInformation( "members/{$member->discord_id}" );
    }

    /**
     * Get guild roles.
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->getInformation( 'roles' );

        return $this->formatRoles( $roles );
    }

    /**
     * Get roles in the format: 'id' => 'name'.
     * Used in group settings.
     *
     * @return array
     */
    public function getRolesOnlyName()
    {
        $roles = $this->getRoles();

        foreach ( $roles as $id => &$role )
        {
            if ( $role['name'] !== '@everyone' )
            {
                $role = $role['name'];
            }
        }
        unset( $role );

        $roles[0] = '';
        asort( $roles, SORT_ASC );

        return $roles;
    }

    /**
     * Get guild channels.
     *
     * @return array
     */
    public function getChannels()
    {
        $channels = $this->getInformation( 'channels' );

        return $this->formatChannels( $channels );
    }

    /**
     * Get channels in the format: 'id' => 'name'.
     * Used in forum settings.
     *
     * @return array
     */
    public function getChannelsOnlyName()
    {
        $channels = $this->getChannels();

        foreach ( $channels as $id => &$channel )
        {
            $channel = $channel['name'];
        }
        unset( $channel );

        $channels[0] = '';
        asort( $channels, SORT_ASC );

        return $channels;
    }

    /**
     * Format guild roles to have the role id as array key and remove the @everyone role.
     *
     * @param array $roles
     * @return array
     */
    protected function formatRoles( array $roles )
    {
        foreach ( $roles as $key => $role )
        {
            if ( $role['name'] === '@everyone' || $role['managed'] === TRUE )
            {
                unset( $roles[$key] );
            }
        }

        return \IPS\discord\Api\Helper::formatDiscordArray( $roles );
    }

    /**
     * Format guild roles to have the role id as array key and remove the @everyone role.
     *
     * @param array $channels
     * @return array
     */
    protected function formatChannels( array $channels )
    {
        foreach ( $channels as $key => $channel )
        {
            if ( $channel['type'] === 'voice' )
            {
                unset( $channels[$key] );
            }
        }

        return \IPS\discord\Api\Helper::formatDiscordArray( $channels );
    }
}
