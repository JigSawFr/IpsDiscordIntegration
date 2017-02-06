//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

class discord_hook_topic extends _HOOK_CLASS_
{
    /**
     * Process created object AFTER the object has been created.
     *
     * @param \IPS\Content\Comment|NULL $comment The first comment
     * @param array $values Values from form
     * @return mixed
     */
    protected function processAfterCreate( $comment, $values )
    {
        $return = call_user_func_array( 'parent::processAfterCreate', func_get_args() );

        if ( \IPS\Settings::i()->discord_post_topics || ( $this->hidden() && \IPS\Settings::i()->discord_post_unapproved_topics ) )
        {
            $channel = new \IPS\discord\Api\Channel;
            $channel->post( $this );
        }

        return $return;
    }

    /**
     * Syncing to run when unhiding.
     *
     * @param bool $approving If true, is being approved for the first time
     * @param \IPS\Member|NULL|FALSE $member The member doing the action (NULL for currently logged in member, FALSE for no member)
     * @return mixed
     */
    public function onUnhide( $approving, $member )
    {
        $return = call_user_func_array( 'parent::onUnhide', func_get_args() );

        if ( $approving && \IPS\Settings::i()->discord_post_topics )
        {
            $channel = new \IPS\discord\Api\Channel;
            $channel->post( $this );
        }

        return $return;
    }
}
