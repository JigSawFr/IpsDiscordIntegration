//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

class discord_hook_topic extends _HOOK_CLASS_
{
    /**
     * Process after the object has been edited or created on the front-end
     *
     * @param	array	$values		Values from form
     * @return	void
     */
    protected function processAfterCreateOrEdit( $values )
    {
        $channel = new \IPS\discord\Api\Channel;
        $channel->post( $this );

        return call_user_func_array( 'parent::processAfterCreateOrEdit', func_get_args() );
    }

    /**
     * Syncing to run when unhiding
     *
     * @param	bool					$approving	If true, is being approved for the first time
     * @param	\IPS\Member|NULL|FALSE	$member	The member doing the action (NULL for currently logged in member, FALSE for no member)
     * @return	void
     */
    public function onUnhide( $approving, $member )
    {
        $return = call_user_func_array( 'parent::onUnhide', func_get_args() );

        if ( $approving )
        {
            $channel = new \IPS\discord\Api\Channel;
            $channel->post( $this );
        }

        return $return;
    }
}
