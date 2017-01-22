<?php

require_once str_replace( 'applications/discord/interface/oauth/auth.php', '', str_replace( '\\', '/', __FILE__ ) ) . 'init.php';
\IPS\Session\Front::i();

$error = \IPS\Request::i()->error;

if ( !empty( $error ) )
{
    \IPS\Output::i()->redirect(
        \IPS\Http\Url::internal( 'app=core&module=system&controller=login', 'front', 'login' )
    );
}

$base = explode( '-', \IPS\Request::i()->state );
if ( $base[0] == 'ucp' )
{
    $destination = (string) \IPS\Http\Url::internal( "app=core&module=system&controller=settings&area=profilesync&service=Discord&loginProcess=discord&base=ucp", 'front'
    )->setQueryString( array( 'state' => $base[1], 'code' => \IPS\Request::i()->code ) );

    \IPS\Output::i()->redirect($destination);
}
else
{
    /* Verify this handler is acceptable for the base we are logging in to */
    $loginHandlers	= \IPS\Login::handlers( TRUE );

    if( !isset( $loginHandlers['Discord'] ) OR ( $base[0] == 'admin' AND !$loginHandlers['Discord']->acp ) )
    {
        \IPS\Output::i()->redirect( \IPS\Http\Url::internal( NULL ) );
    }

    $destination = \IPS\Http\Url::internal( "app=core&module=system&controller=login&loginProcess=discord&base={$base[0]}", $base[0], NULL, NULL, \IPS\Settings::i()->logins_over_https )->setQueryString( array( 'state' => $base[1], 'code' => \IPS\Request::i()->code ) );
    if ( !empty( $base[2] ) )
    {
        $destination = $destination->setQueryString( 'ref', $base[2] );
    }

    \IPS\Output::i()->redirect( $destination );
}