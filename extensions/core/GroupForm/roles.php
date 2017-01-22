<?php
/**
 * @brief		Admin CP Group Form
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	Discord Integration
 * @since		29 Jan 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\discord\extensions\core\GroupForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Admin CP Group Form
 */
class _roles
{
    /**
     * Process Form
     *
     * @param	\IPS\Helpers\Form		$form	The form
     * @param	\IPS\Member\Group		$group	Existing Group
     * @return	void
     */
    public function process( &$form, $group )
    {
        try
        {
            $guild = new \IPS\discord\Api\Guild;
            $roles = $guild->getRolesOnlyName();

            /** @noinspection PhpUndefinedFieldInspection */
            $form->add(
                new \IPS\Helpers\Form\Select( 'discord_role', $group->discord_role ?: 0, TRUE, [
                    'options' => $roles
                ] )
            );
        }
        catch ( \Exception $e )
        {
            \IPS\Log::log( $e, 'discord_roles' );
            $form->add(
                new \IPS\Helpers\Form\TextArea( 'discord_error', 'Error occurred while retrieving discord roles, check the logs for more information.' )
            );
        }
    }

    /**
     * Save
     *
     * @param array $values Values from form
     * @param \IPS\Member\Group $group The group
     * @return void
     */
    public function save( $values, &$group )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $group->discord_role = $values['discord_role'];
    }
}
