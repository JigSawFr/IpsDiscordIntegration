<?php


namespace IPS\discord\setup\upg_10007;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
	exit;
}

/**
 * 1.0.0 Beta 5 Upgrade Code
 */
class _Upgrade
{
	public function step1()
	{
        /**
         * Fix: "Permission too open" error.
         * Chmod files that need to be directly called to 644.
         * Because on some server configurations those are set to 666 by default and thus error out.
         */
        \chmod(
            \IPS\ROOT_PATH . '/applications/discord/interface/oauth/auth.php',
            \IPS\FILE_PERMISSION_NO_WRITE
        );

		return TRUE;
	}
}