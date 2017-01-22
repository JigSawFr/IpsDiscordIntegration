<?php

namespace IPS\discord\Api;

/**
 * Class _Role
 *
 * @package IPS\discord\Api
 */
class _Role extends \IPS\discord\Api\AbstractResponse
{
    /**
     * Get roles that are not mapped.
     *
     * @param array $currentRoles
     * @return array
     */
    public function getNotMappedRoles( array $currentRoles )
    {
        $mappedRoles = $this->getMappedRoles();

        $notMapped = array_diff( array_column( $currentRoles, 'id' ), $mappedRoles );

        return $notMapped;
    }

    /**
     * Get all roles that are mapped.
     *
     * @return array
     */
    protected function getMappedRoles()
    {
        $mappedGroups = [];
        $iterator = new \IPS\Patterns\ActiveRecordIterator(
            \IPS\Db::i()->select( '*', 'core_groups', ['discord_role != ? AND discord_role != ?', 0, ''] ),
            \IPS\Member\Group::class
        );

        /** @var \IPS\Member\Group $group */
        foreach ( $iterator as $group )
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $mappedGroups[] = $group->discord_role;
        }

        return $mappedGroups;
    }
}
