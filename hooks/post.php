//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

class discord_hook_post extends _HOOK_CLASS_
{
    /**
     * Create comment
     *
     * @param	\IPS\Content\Item		$item				The content item just created
     * @param	\IPS\forums\Topic\Post	$comment			The comment
     * @param	bool					$first				Is the first comment?
     * @param	string					$guestName			If author is a guest, the name to use
     * @param	bool|NULL				$incrementPostCount	Increment post count? If NULL, will use static::incrementPostCount()
     * @param	\IPS\Member|NULL		$member				The author of this comment. If NULL, uses currently logged in member.
     * @param	\IPS\DateTime|NULL		$time				The time
     * @param	string|NULL				$ipAddress			The IP address or NULL to detect automatically
     * @param	int|NULL				$hiddenStatus		NULL to set automatically or override: 0 = unhidden; 1 = hidden, pending moderator approval; -1 = hidden (as if hidden by a moderator)
     * @return	static
     */
    public static function create( $item, $comment, $first=false, $guestName=NULL, $incrementPostCount=NULL, $member=NULL, \IPS\DateTime $time=NULL, $ipAddress=NULL, $hiddenStatus=NULL )
    {
        /** @var \IPS\forums\Topic\Post $comment */
        $comment = call_user_func_array( 'parent::create', func_get_args() );

        if ( !$first && $item instanceof \IPS\forums\Topic )
        {
            $channel = new \IPS\discord\Api\Channel;
            $channel->post( $item, $comment, $member );
        }

        return $comment;
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
            $channel->post( $this->item(), $this );
        }

        return $return;
    }
}
