//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

class discord_hook_nodecontroller extends _HOOK_CLASS_
{
    /**
     * Toggle Enabled/Disable
     *
     * @return	void
     */
    protected function enableToggle()
    {
        /** @var \IPS\Application $nodeClass */
        $nodeClass = $this->nodeClass;
        if ( $nodeClass === \IPS\Application::class )
        {
            try
            {
                $node = $nodeClass::load( \IPS\Request::i()->id );
            }
            catch ( \OutOfRangeException $e )
            {}

            $enable = (bool) \IPS\Request::i()->status;

            if ( $node instanceof \IPS\discord\Application && $enable !== TRUE )
            {
                $set = [
                    'login_enabled' => 0
                ];
                $where = [
                    'login_key=?', 'Discord'
                ];

                \IPS\Db::i()->update( 'core_login_handlers', $set, $where );
            }
        }

        call_user_func_array( 'parent::enableToggle', func_get_args() );
    }
}
