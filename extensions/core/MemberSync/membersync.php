<?php
/**
 * @brief		Member Sync
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	Discord Integration
 * @since		29 Jan 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\discord\extensions\core\MemberSync;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Member Sync
 */
class _membersync
{
    /**
     * Member account has been updated
     *
     * @param	$member		\IPS\Member	Member updating profile
     * @param	$changes	array		The changes
     * @return	void
     */
    public function onProfileUpdate( \IPS\Member $member, $changes )
    {
        /* Determine whether group(s) has been changed */
        if ( isset( $changes['member_group_id'] ) || isset( $changes['mgroup_others'] ) )
        {
            try {
                $guildMember = new \IPS\discord\Api\GuildMember;
                $guildMember->updateRoles( $member, $changes );
            } catch ( \Exception $e ) {
                /* Ignore, can be re-synced. */
            }
        }
    }

    /**
     * Member is flagged as spammer
     *
     * @param	$member	\IPS\Member	The member
     * @return	void
     */
    public function onSetAsSpammer( $member )
    {
        if ( \IPS\Settings::i()->discord_sync_bans )
        {
            $guildMember = new \IPS\discord\Api\GuildMember;
            $guildMember->modifyBanState( $member );
        }
    }

    /**
     * Member is merged with another member
     *
     * @param	\IPS\Member	$member		Member being kept
     * @param	\IPS\Member	$member2	Member being removed
     * @return	void
     */
    public function onMerge( $member, $member2 )
    {
        $guildMember = new \IPS\discord\Api\GuildMember;
        $guildMember->remove( $member2 );
    }

    /**
     * Member is deleted
     *
     * @param	$member	\IPS\Member	The member
     * @return	void
     */
    public function onDelete( $member )
    {
        $guildMember = new \IPS\discord\Api\GuildMember;
        $guildMember->remove( $member );
    }
}