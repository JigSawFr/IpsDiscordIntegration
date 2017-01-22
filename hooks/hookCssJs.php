//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

abstract class discord_hook_hookCssJs extends _HOOK_CLASS_
{
    public static function baseCss()
    {
        parent::baseCss();
        \IPS\Output::i()->cssFiles = array_merge(
            \IPS\Output::i()->cssFiles,
            \IPS\Theme::i()->css( 'login/discord.css', 'discord', 'global' )
        );
    }
}
