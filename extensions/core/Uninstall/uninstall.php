<?php
/**
 * @brief		Uninstall callback
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	Discord Integration
 * @since		28 Jan 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\discord\extensions\core\Uninstall;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Uninstall callback
 */
class _uninstall
{
    /**
     * Code to execute before the application has been uninstalled
     *
     * @param	string	$application	Application directory
     * @return	array
     */
    public function preUninstall( $application )
    {
    }

    /**
     * Code to execute after the application has been uninstalled.
     * Make sure we remove our login handler.
     *
     * @param	string	$application	Application directory
     * @return	array
     */
    public function postUninstall( $application )
    {
        try {
            \IPS\Db::i()->delete( 'core_login_handlers', [
                'login_key=?', 'Discord'
            ] );

            /* Remove /applications/core/sources/ProfileSync/Discord.php */
            \unlink(
                \IPS\ROOT_PATH . '/applications/core/sources/ProfileSync/Discord.php'
            );

            /* Remove /system/Login/Discord.php */
            \unlink(
                \IPS\ROOT_PATH . '/system/Login/Discord.php'
            );

        } catch ( \Exception $e ) {}
    }

    /**
     * Code to execute when other applications are uninstalled
     *
     * @param	string	$application	Application directory
     * @return	void
     * @deprecated	This is here for backwards-compatibility - all new code should go in onOtherUninstall
     */
    public function onOtherAppUninstall( $application )
    {
        return $this->onOtherUninstall( $application );
    }

    /**
     * Code to execute when other applications or plugins are uninstalled
     *
     * @param	string	$application	Application directory
     * @param	int		$plugin			Plugin ID
     * @return	void
     */
    public function onOtherUninstall( $application=NULL, $plugin=NULL )
    {
    }
}