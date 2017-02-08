<?php

namespace IPS\core\ProfileSync;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Discord Profile Sync
 */
class _Discord extends ProfileSyncAbstract
{
    /**
     * @brief	Login handler key
     */
    public static $loginKey = 'Discord';

    /**
     * @brief	Icon
     */
    public static $icon = 'lock';

    /**
     * @brief	Authorization token
     */
    protected $authToken = NULL;

    /**
     * @brief	User data
     */
    protected $user = NULL;

    /**
     * Get user data
     *
     * @return	array
     */
    protected function user()
    {
        if ( $this->user === NULL && $this->member->discord_token )
        {
            try
            {
                $response = \IPS\Http\Url::external( \IPS\discord\Api::OAUTH2_URL . 'token' )->request()->post( [
                    'client_id'		=> \IPS\Settings::i()->discord_client_id,
                    'client_secret'	=> \IPS\Settings::i()->discord_client_secret,
                    'refresh_token'	=> $this->member->discord_token,
                    'grant_type'	=> 'refresh_token'
                ] )->decodeJson();

                if ( isset( $response['access_token'] ) )
                {
                    $this->authToken = $response['access_token'];
                    $this->user = $this->get();
                }

                /* Sync roles */
                $guildMember = new \IPS\discord\Api\GuildMember;
                $guildMember->updateRoles( $this->member );
            }
            catch ( \IPS\Http\Request\Exception $e )
            {
                $this->member->discord_token = NULL;
                $this->member->save();

                \IPS\Log::log( $e, 'discord' );
            }
        }

        return $this->user;
    }


    /**
     * Is connected?
     *
     * @return	bool
     */
    public function connected()
    {
        return (bool) ( $this->member->discord_id && $this->member->discord_token );
    }

    /**
     * Get photo
     *
     * @return	\IPS\Http\Url|\IPS\File|NULL
     */
    public function photo()
    {
        try
        {
            $user = $this->user();

            if ( isset( $user['avatar'] ) && !empty( $user['avatar'] ) )
            {
                return \IPS\Http\Url::external( \IPS\discord\Api::API_URL . "users/{$user['id']}/avatars/{$user['avatar']}.jpg" );
            }
        }
        catch ( \IPS\Http\Request\Exception $e )
        {
            \IPS\Log::log( $e, 'discord' );
        }

        return NULL;
    }

    /**
     * Get cover photo
     *
     * @return	NULL
     */
    public function cover()
    {
        return NULL;
    }

    /**
     * Get name
     *
     * @return	string
     */
    public function name()
    {
        $user = $this->user();

        if ( isset( $user['username'] ) )
        {
            return $user['username'];
        }
    }

    /**
     * Get status
     *
     * @return  NULL
     */
    public function status()
    {
        return NULL;
    }

    /**
     * Disassociate
     *
     * @return	void
     */
    protected function _disassociate()
    {
        $this->member->discord_id = 0;
        $this->member->discord_token = NULL;
        $this->member->save();
    }

    /**
     * Get API data
     *
     * @return array
     * @throws \Exception
     */
    protected function get()
    {
        $discordMember = new \IPS\discord\Api\Member;
        $userData = $discordMember->getDiscordUser( $this->authToken );

        if ( !$userData['verified'] )
        {
            //TODO:
            throw new \Exception();
        }

        return $userData;
    }
}