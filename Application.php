<?php
/**
 * @brief		Discord Integration Application Class
 * @author		<a href=''>Ahmad E.</a>
 * @copyright	(c) 2017 Ahmad E.
 * @package		IPS Community Suite
 * @subpackage	Discord Integration
 * @since		01 Jan 2017
 */

namespace IPS\discord;

/**
 * Discord Integration Application Class
 * @TODO: Feature: Notifications for PMs.
 * @TODO: Feature: Notifications for watched topics.
 * @TODO: (User)Setting: Send notifications on Discord?
 * @TODO: (User)Setting: Send notifications for approved posts.
 */
class _Application extends \IPS\Application
{
    /**
     * Make sure we have our login handler in the correct table.
     * Make sure we move our login handler files.
     */
    public function installOther()
    {
        $maxLoginOrder = \IPS\Db::i()->select( 'MAX(login_order)', 'core_login_handlers' )->first();

        \IPS\Db::i()->insert('core_login_handlers', [
            'login_settings' => '',
            'login_key' => 'Discord',
            'login_enabled' => 1,
            'login_order' => $maxLoginOrder + 1,
            'login_acp' => 0
        ]);

        /* Copy to /applications/core/sources/ProfileSync/ */
        $profileSync = \copy(
            \IPS\ROOT_PATH . '/applications/discord/sources/MoveOnInstall/ProfileSync/Discord.php',
            \IPS\ROOT_PATH . '/applications/core/sources/ProfileSync/Discord.php'
        );

        /* Copy to /system/Login/ */
        $systemLogin = \copy(
            \IPS\ROOT_PATH . '/applications/discord/sources/MoveOnInstall/Login/Discord.php',
            \IPS\ROOT_PATH . '/system/Login/Discord.php'
        );

        if ( !$profileSync || !$systemLogin )
        {
            throw new \OutOfRangeException( 'Copying required file failed.' );
        }
    }
}
