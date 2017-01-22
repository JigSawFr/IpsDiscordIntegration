//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

/** @noinspection PhpUndefinedClassInspection */
class discord_hook_member extends _HOOK_CLASS_
{
    /**
     * Make sure we declare our new core_members key in $databaseIdFields.
     */
    public function __construct()
    {
        call_user_func_array( 'parent::__construct', func_get_args() );

        /** @noinspection PhpUndefinedFieldInspection */
        $databaseIdFields = static::$databaseIdFields;
        $databaseIdFields[] = 'discord_id';
        /** @noinspection PhpUndefinedFieldInspection */
        static::$databaseIdFields = $databaseIdFields;
    }

    /**
     * Get discord roles that the member should have.
     *
     * @return array
     */
    public function get_discord_roles()
    {
        /* Reset group cache so we retrieve the values to be set instead of the current values */
        /** @noinspection PhpUndefinedFieldInspection */
        $this->_groups = NULL;
        $roleIds = [];

        /** @noinspection PhpUndefinedFieldInspection */
        foreach ( $this->groups as $groupId )
        {
            try
            {
                $group = \IPS\Member\Group::load( $groupId );

                /** @noinspection PhpUndefinedFieldInspection */
                if ( $group->discord_role !== 0 )
                {
                    /** @noinspection PhpUndefinedFieldInspection */
                    $roleIds[] = $group->discord_role;
                }
            }
            catch ( \OutOfRangeException $e ) {}
        }

        return $roleIds;
    }

    /**
     * Is the member connected to discord?
     *
     * @return bool
     */
    public function get_is_discord_connected()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return ( $this->discord_id && $this->discord_token );
    }

    /**
     * Set banned
     *
     * @param	string	$value	Value
     * @return	void
     */
    public function set_temp_ban( $value )
    {
        call_user_func_array( 'parent::set_temp_ban', func_get_args() );

        $value = (int) $value;

        /** @noinspection PhpUndefinedFieldInspection */
        if ( \IPS\Settings::i()->discord_sync_bans )
        {
            $guildMember = new \IPS\discord\Api\GuildMember;

            if ( $value === 0 )
            {
                /** @noinspection PhpParamsInspection */
                $guildMember->modifyBanState( $this, true );
            }
            else
            {
                /** @noinspection PhpParamsInspection */
                $guildMember->modifyBanState( $this );
            }
        }
    }
}
