<?php

namespace IPS\discord;

/**
 * Class Api
 *
 * @package IPS\discord
 */
class _Api extends \IPS\Patterns\Singleton
{
    /* API URLs */
    const API_URL = 'https://discordapp.com/api/';
    const OAUTH2_URL = 'https://discordapp.com/api/oauth2/';

    /* Scopes according to https://discordapp.com/developers/docs/topics/oauth2#scopes */
    const SCOPE_BOT = 'bot';
    const SCOPE_CONNECTIONS = 'connections';
    const SCOPE_EMAIL = 'email';
    const SCOPE_IDENTIFY = 'identify';
    const SCOPE_GUILDS = 'guilds';
    const SCOPE_GUILDS_JOIN = 'guilds.join';
    const SCOPE_GDM_JOIN = 'gdm.join';
    const SCOPE_MESSAGES_READ = 'messages.read';
    const SCOPE_RPC = 'rpc';
    const SCOPE_RPC_API = 'rpc.api';
    const SCOPE_WEBHOOK_INCOMING = 'webhook.incoming';

    /* Permissions according to https://discordapp.com/developers/docs/topics/permissions#permissions */
    const PERM_CREATE_INSTANT_INVITE = 0x00000001;
    const PERM_KICK_MEMBERS = 0x00000002;
    const PERM_BAN_MEMBERS = 0x00000004;
    const PERM_ADMINISTRATOR = 0x00000008;
    const PERM_MANAGE_CHANNELS = 0x00000010;
    const PERM_MANAGE_GUILD = 0x00000020;
    const PERM_ADD_REACTIONS = 0x00000040;
    const PERM_READ_MESSAGES = 0x00000400;
    const PERM_SEND_MESSAGES = 0x00000800;
    const PERM_SEND_TTS_MESSAGES = 0x00001000;
    const PERM_MANAGE_MESSAGES = 0x00002000;
    const PERM_EMBED_LINKS = 0x00004000;
    const PERM_ATTACH_FILES = 0x00008000;
    const PERM_READ_MESSAGE_HISTORY = 0x00010000;
    const PERM_MENTION_EVERYONE = 0x00020000;
    const PERM_USE_EXTERNAL_EMOJIS = 0x00040000;
    const PERM_CONNECT = 0x00100000;
    const PERM_SPEAK = 0x00200000;
    const PERM_MUTE_MEMBERS = 0x00400000;
    const PERM_DEAFEN_MEMBERS = 0x00800000;
    const PERM_MOVE_MEMBERS = 0x01000000;
    const PERM_USE_VAD = 0x02000000;
    const PERM_CHANGE_NICKNAME = 0x04000000;
    const PERM_MANAGE_NICKNAMES = 0x08000000;
    const PERM_MANAGE_ROLES = 0x10000000;
    const PERM_MANAGE_WEBHOOKS = 0x20000000;
    const PERM_MANAGE_EMOJIS = 0x40000000;

    const AUTH_TYPE_OAUTH = 1;
    const AUTH_TYPE_BOT = 2;

    /**
     * @var $this $instance Singleton Instance
     */
    protected static $instance;

    /**
     * @var string $url
     */
    protected $url = self::API_URL;

    /**
     * @var string $uri
     */
    protected $uri;

    /**
     * @var \IPS\Member $member
     */
    protected $member;

    /**
     * @var string|array $params
     */
    protected $params;

    /**
     * @var string $contentType
     */
    protected $contentType = 'application/json';

    /**
     * @var string $method
     */
    protected $method;

    /**
     * @var int $authType
     */
    protected $authType = self::AUTH_TYPE_BOT;

    /**
     * @var string $token
     */
    protected $token;

    /**
     * @param string $uri
     * @return $this
     */
    public function setUri( $uri )
    {
        if ( mb_strpos( $uri, '{user.id}' ) !== FALSE )
        {
            if ( !$this->member instanceof \IPS\Member )
            {
                throw new \OutOfRangeException( 'No member found' );
            }

            /** @noinspection PhpUndefinedFieldInspection */
            $uri = str_replace( '{user.id}', $this->member->discord_id, $uri );
        }

        /** @noinspection PhpUndefinedFieldInspection */
        $uri = str_replace( '{guild.id}', \IPS\Settings::i()->discord_guild_id, $uri );

        $this->uri = $uri;
        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl( $url )
    {
        if ( $url !== self::API_URL && $url !== self::OAUTH2_URL )
        {
            throw new \OutOfRangeException( 'Invalid URL passed.' );
        }

        $this->url = $url;
        return $this;
    }

    /**
     * @param \IPS\Member $member
     * @return $this
     */
    public function setMember( \IPS\Member $member )
    {
        $this->member = $member;
        return $this;
    }

    /**
     * @param $contentType
     * @return $this
     */
    public function setContentType( $contentType )
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @param string|array $params
     * @return $this
     */
    public function setParams( $params )
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setAuthType( $type )
    {
        if ( $type !== static::AUTH_TYPE_OAUTH && $type !== static::AUTH_TYPE_BOT )
        {
            throw new \OutOfRangeException( 'Invalid auth type passed' );
        }

        $this->authType = $type;
        return $this;
    }

    /**
     * @param $token
     * @return $this
     */
    public function setToken( $token )
    {
        $this->token = $token;
        return $this;
    }

    /**
     * This \IPS\Http\Request object and sends it. Then returns the \IPS\Http\Response object.
     * This should not be called directly, rather use \IPS\discord\Api\AbstractResponse::handleApi().
     *
     * @return \IPS\Http\Response
     */
    public function send()
    {
        if (
            empty( $this->url ) ||
            empty( $this->uri ) ||
            empty( $this->contentType ) ||
            empty( $this->method )
        )
        {
            throw new \OutOfRangeException( 'parameter_is_missing' );
        }

        if (
            $this->token === NULL ||
            ( $this->token !== \IPS\Settings::i()->discord_bot_token && $this->authType === self::AUTH_TYPE_BOT )
        )
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->token = \IPS\Settings::i()->discord_bot_token;
        }

        $request = \IPS\Http\Url::external( $this->url . $this->uri )
            ->setQueryString( $this->params )
            ->request()
            ->setHeaders([
                'Authorization' => ($this->authType === static::AUTH_TYPE_BOT ? 'Bot ' : 'Bearer ') . $this->token,
                'User-Agent' => 'DiscordBot (ahmadel, v1)',
                'Content-Type' => $this->contentType
            ]);

        if ( mb_strtolower( $this->method ) === 'get' )
        {
            $response = $request->get();
        }
        else
        {
            $response = $request->{$this->method}( $this->params );
        }

        return $response;
    }

    /**
     * Just here for better IDE auto-complete.
     *
     * @return Api
     */
    public static function i()
    {
        return parent::i();
    }
}