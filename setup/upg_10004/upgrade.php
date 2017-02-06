<?php


namespace IPS\discord\setup\upg_10004;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * 1.0.0 Beta 2 Upgrade Code
 */
class _Upgrade
{
    /**
     * Fix: "Permission too open" error.
     * Chmod files that need to be directly called to 644.
     * Because on some server configurations those are set to 666 by default and thus error out.
     */
    public function step1()
    {
        \chmod(
            \IPS\ROOT_PATH . '/applications/discord/interface/oauth/auth.php',
            644
        );

        return TRUE;
    }
}