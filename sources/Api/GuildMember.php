<?php

namespace IPS\discord\Api;

/**
 * Class GuildMember
 *
 * @package IPS\discord\Api
 */
class _GuildMember extends \IPS\discord\Api\AbstractResponse
{
    /**
     * @var Guild $guild
     */
    protected $guild;

    /**
     * @var Role $role
     */
    protected $role;

    /**
     * Update the roles of the given member.
     *
     * @param \IPS\Member $member
     * @param array $changes
     * @return array|NULL
     */
    public function updateRoles( \IPS\Member $member, array $changes = [] )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        if ( !$member->is_discord_connected )
        {
            return NULL;
        }

        $member = $this->handleMemberChanges( $member, $changes );
        $roles = $this->getRoleIds( $member );

        /** @noinspection PhpUndefinedFieldInspection */
        $this->api->setUrl( \IPS\discord\Api::API_URL )
            ->setMember( $member )
            ->setUri( 'guilds/{guild.id}/members/{user.id}' )
            ->setParams(json_encode([
                'roles' => $roles
            ]))
            ->setMethod( 'patch' );

        return $this->handleApi();
    }

    /**
     * Ban/Unban member from the discord guild.
     *
     * @param \IPS\Member $member
     * @param bool $unban If true: unban, else ban
     * @return array|NULL
     */
    public function modifyBanState( \IPS\Member $member, $unban = false )
    {
        $method = $unban ? 'delete' : 'put';

        $this->api->setUrl( \IPS\discord\Api::API_URL )
            ->setMember( $member )
            ->setUri( 'guilds/{guild.id}/bans/{user.id}' )
            ->setParams([])
            ->setMethod( $method );

        return $this->handleApi();
    }

    /**
     * Kick member from the discord guild.
     *
     * @param \IPS\Member $member
     * @return array|NULL
     */
    public function remove( \IPS\Member $member )
    {
        $this->api->setUrl( \IPS\discord\Api::API_URL )
            ->setMember( $member )
            ->setUri( 'guilds/{guild.id}/members/{user.id}' )
            ->setParams( [] )
            ->setMethod( 'delete' );

        return $this->handleApi();
    }

    /**
     * Get roles of the member that was passed.
     *
     * @param \IPS\Member $member
     * @return array
     */
    public function getRoles( \IPS\Member $member )
    {
        $guildMember = $this->guild()->getMember( $member );
        $roles = $this->guild()->getRoles();
        $guildMemberRoles = [];

        $roleIds = $guildMember['roles'];

        foreach ( $roleIds as $roleId )
        {
            $guildMemberRoles[] = $roles[$roleId];
        }

        return $guildMemberRoles;
    }

    /**
     * Get all IPS members that are connected to the discord guild.
     *
     * @return \IPS\Member[]|\IPS\Patterns\ActiveRecordIterator
     */
    public function getConnectedMembers()
    {
        /** @var \IPS\Member[] $members */
        $members = new \IPS\Patterns\ActiveRecordIterator(
            \IPS\Db::i()->select( '*', 'core_members', ['discord_id != ? AND discord_token != ?', '0', ''] ),
            \IPS\Member::class
        );

        return $members;
    }

    /**
     * Get array of role IDs that the member should get.
     *
     * @param \IPS\Member $member
     * @return array
     */
    protected function getRoleIds( \IPS\Member $member )
    {
        $currentRoles = $this->getRoles( $member );

        /** @noinspection PhpUndefinedFieldInspection */
        if ( !\IPS\Settings::i()->discord_remove_unmapped )
        {
            $notMappedRoles = $this->role()->getNotMappedRoles( $currentRoles );

            /** @noinspection PhpUndefinedFieldInspection */
            return array_merge( $notMappedRoles, $member->discord_roles );
        }

        /** @noinspection PhpUndefinedFieldInspection */
        return array_unique( $member->discord_roles );
    }

    /**
     * If groups were changed make sure to modify the member object to include the changes.
     *
     * @param \IPS\Member $member
     * @param array $changes
     * @return \IPS\Member
     */
    protected function handleMemberChanges( \IPS\Member $member, array $changes )
    {
        if ( count( $changes ) === 0 )
        {
            return $member;
        }

        /* Modify $member object to have correct groups */
        if ( isset( $changes['member_group_id'] ) )
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $member->member_group_id = (int) $changes['member_group_id'];
        }

        if ( isset( $changes['mgroups_others'] ) )
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $member->mgroup_others = $changes['mgroups_others'];
        }

        return $member;
    }

    /**
     * Get Guild object.
     *
     * @return Guild
     */
    protected function guild()
    {
        if ( $this->guild === NULL )
        {
            $this->guild = new \IPS\discord\Api\Guild;
        }

        return $this->guild;
    }

    /**
     * Get Role object.
     *
     * @return Role
     */
    protected function role()
    {
        if ( $this->role === NULL )
        {
            $this->role = new \IPS\discord\Api\Role;
        }

        return $this->role;
    }
}
