<?php

namespace IPS\discord\setup\upg_10005;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * 1.0.0 Beta 3 Upgrade Code
 */
class _Upgrade
{
	public function step1()
	{
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

		return TRUE;
	}
}