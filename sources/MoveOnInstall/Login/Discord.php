<?php

namespace IPS\Login;

/**
 * Class Discord
 *
 * @package \IPS\discord
 */
class _Discord extends LoginAbstract
{
    /**
     * @brief Icon
     * @var string $icon
     * @TODO
     */
    public static $icon = 'lock';

    /**
     * Get Form
     *
     * @param	\IPS\Http\Url	$url			The URL for the login page
     * @param	bool			$ucp			Is UCP? (as opposed to login form)
     * @param	\IPS\Http\Url	$destination	The URL to redirect to after a successful login
     *
     * @return	string
     */
    public function loginForm( \IPS\Http\Url $url, $ucp = FALSE, \IPS\Http\Url $destination = NULL )
    {
        return \IPS\Theme::i()
            ->getTemplate( 'login', 'discord', 'global' )
            ->discord(
                (string) $this->_discordSignInUrl(
                    ( $ucp ? 'ucp' : \IPS\Dispatcher::i()->controllerLocation ),
                    $destination
                )
            );
    }

    /**
     * Authenticate
     *
     * @param	string			$url	The URL for the login page
     * @param	\IPS\Member		$member	If we want to integrate this login method with an existing member, provide the member object
     * @return	\IPS\Member
     * @throws	\IPS\Login\Exception
     */
    public function authenticate( $url, $member=NULL )
    {
        try
        {
            if ( \IPS\Request::i()->state !== \IPS\Session::i()->csrfKey )
            {
                throw new \IPS\Login\Exception( 'CSRF_FAIL', \IPS\Login\Exception::INTERNAL_ERROR );
            }

            /* Retrieve access token */
            $response = \IPS\Http\Url::external( \IPS\discord\Api::OAUTH2_URL . 'token' )
                ->request()
                ->post([
                    'client_id' => \IPS\Settings::i()->discord_client_id,
                    'client_secret' => \IPS\Settings::i()->discord_client_secret,
                    'grant_type' => 'authorization_code',
                    'redirect_uri'	=> ((string) \IPS\Http\Url::internal( 'applications/discord/interface/oauth/auth.php', 'none' )),
                    'code' => \IPS\Request::i()->code
                ])
                ->decodeJson();

            if ( isset( $response['error'] ) || !isset( $response['access_token'] ) )
            {
                throw new \IPS\Login\Exception( 'generic_error', \IPS\Login\Exception::INTERNAL_ERROR );
            }

            /* Get user data */
            $discordMember = new \IPS\discord\Api\Member;
            $userData = $discordMember->getDiscordUser( $response['access_token'] );

            if ( !$userData['verified'] )
            {
                throw new \IPS\Login\Exception( 'generic_error', \IPS\Login\Exception::INTERNAL_ERROR );
            }

            /* Set member properties */
            $memberProperties = [
                'discord_id' => $userData['id'],
                'discord_token' => $response['access_token']
            ];

            if ( isset( $response['refresh_token'] ) )
            {
                $memberProperties['discord_token'] = $response['refresh_token'];
            }

            /* Find or create member */
            $member = $this->createOrUpdateAccount(
                $member ?: \IPS\Member::load( $userData['id'], 'discord_id' ),
                $memberProperties,
                $this->settings['real_name'] ? $userData['username'] : NULL,
                $userData['email'],
                $response['access_token'],
                array(
                    'photo' => TRUE,
                    'cover' => TRUE,
                    'status' => ''
                )
            );

            /* Sync user */
            $guildMember = new \IPS\discord\Api\GuildMember;
            $guildMember->updateRoles( $member );

            /* Return */
            return $member;
        }
        catch ( \IPS\Http\Request\Exception $e )
        {
            throw new \IPS\Login\Exception( 'generic_error', \IPS\Login\Exception::INTERNAL_ERROR );
        }
    }

    /**
     * Link Account
     *
     * @param	\IPS\Member	$member		The member
     * @param	mixed		$details	Details as they were passed to the exception thrown in authenticate()
     * @return	void
     */
    public static function link( \IPS\Member $member, $details )
    {
        /* Get user data */
        $discordMember = new \IPS\discord\Api\Member;
        $userData = $discordMember->getDiscordUser( $details );
        $member->discord_id = $userData['id'];
        $member->discord_token = $details;
        $member->save();

        /* Sync member */
        $guildMember = new \IPS\discord\Api\GuildMember;
        $guildMember->updateRoles( $member );
    }

    /**
     * ACP Settings Form
     *
     * @return	array	List of settings to save - settings will be stored to core_login_handlers.login_settings DB field
     * @code
    return array( 'savekey'	=> new \IPS\Helpers\Form\[Type]( ... ), ... );
     * @endcode
     */
    public function acpForm()
    {
        /* No config is needed here, all information is retrieved from the application settings. */
        return [];
    }

    /**
     * Test Settings
     *
     * @return	bool
     * @throws	\IPS\Http\Request\Exception
     * @throws	\UnexpectedValueException	If response code is not 302
     */
    public function testSettings()
    {
        return TRUE;
    }

    /**
     * Can a member sign in with this login handler?
     * Used to ensure when a user disassociates a social login that they have some other way of logging in
     *
     * @param	\IPS\Member	$member	The member
     * @return	bool
     */
    public function canProcess( \IPS\Member $member )
    {
        return ( $member->discord_id && $member->discord_token );
    }

    /**
     * Get sign in URL
     *
     * @param	string			$base			Controls where the user is taken back to
     * @param	\IPS\Http\Url	$destination	The URL to redirect to after a successful login
     *
     * @return	\IPS\Http\Url
     */
    protected function _discordSignInUrl( $base, \IPS\Http\Url $destination = NULL )
    {
        $params = [
            'response_type'	=> 'code',
            'client_id' => \IPS\Settings::i()->discord_client_id,
            'redirect_uri'	=> ( (string) \IPS\Http\Url::internal( 'applications/discord/interface/oauth/auth.php', 'none' ) ),
            'scope' => ( \IPS\discord\Api::SCOPE_EMAIL . ' ' . \IPS\discord\Api::SCOPE_IDENTIFY ),
            'state' => ( $base . '-' . \IPS\Session::i()->csrfKey . '-' . ( $destination ? base64_encode( $destination ) : '' ) )
        ];

        return \IPS\Http\Url::external( \IPS\discord\Api::OAUTH2_URL . 'authorize' )->setQueryString( $params );
    }

    /**
     * Can a member change their email/password with this login handler?
     *
     * @param	string		$type	'email' or 'password'
     * @param	\IPS\Member	$member	The member
     * @return	bool
     */
    public function canChange( $type, \IPS\Member $member )
    {
        return FALSE;
    }
}
