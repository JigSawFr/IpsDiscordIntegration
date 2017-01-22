<?php

namespace IPS\discord\modules\front\register;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * link
 */
class _link extends \IPS\Dispatcher\Controller
{
    /**
     * Execute
     *
     * @return	void
     */
    public function execute()
    {
        parent::execute();
    }

    /**
     * @return	void
     */
    protected function manage()
    {
    }

    /**
     * Link the given guild.
     */
    protected function admin()
    {
        if ( !\IPS\Member::loggedIn()->isAdmin() )
        {
            \IPS\Output::i()->error( 'discord_not_admin', 'TODO', 403 );
        }

        $code = (string) \IPS\Request::i()->code;
        $guildId = (string) \IPS\Request::i()->guild_id;

        if ( !empty( $code ) && !empty( $guildId ) )
        {
            $key = 'discord_guild_id';

            \IPS\Db::i()->update( 'core_sys_conf_settings', [ 'conf_value' => $guildId ], [ 'conf_key=?', $key ] );
            \IPS\Settings::i()->$key = $guildId;

            unset( \IPS\Data\Store::i()->settings );
        }

        \IPS\Output::i()->redirect(
            \IPS\Http\Url::internal( 'app=discord&module=settings&controller=settings', 'admin' )
        );
    }
}