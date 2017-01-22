<?php
/**
 * @brief		syncGroups Task
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	discord
 * @since		29 Jan 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\discord\tasks;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * syncGroups Task
 */
class _syncGroups extends \IPS\Task
{
    /**
     * Execute
     *
     * If ran successfully, should return anything worth logging. Only log something
     * worth mentioning (don't log "task ran successfully"). Return NULL (actual NULL, not '' or 0) to not log (which will be most cases).
     * If an error occurs which means the task could not finish running, throw an \IPS\Task\Exception - do not log an error as a normal log.
     * Tasks should execute within the time of a normal HTTP request.
     *
     * @return	mixed	Message to log or NULL
     * @throws	\IPS\Task\Exception
     */
    public function execute()
    {
        $guildMember = new \IPS\discord\Api\GuildMember;
        $members = $guildMember->getConnectedMembers();

        $count = 0;

        foreach ( $members as $key => $member )
        {
            try {
                $guildMember->updateRoles( $member );

                if ( $count % 5 === 0 )
                {
                    sleep( 10 );
                }

                ++$count;
            } catch ( \IPS\discord\Api\Exception\TooManyRequestsException $e ) {
                throw new \IPS\Task\Exception( $this, 'Hit discord API rate limit at member: ' . $member->name . ', iteration: ' . $count );
            }
            catch ( \IPS\discord\Api\Exception\NotFoundException $e ) {
                /* Ignore to not interrupt syncing process if a member left or something similar happened  */
            }
            catch ( \Exception $e ) {
                if ( $e instanceof \IPS\discord\Api\Exception\BaseException ) {
                    throw new \IPS\Task\Exception( $this, 'Error: ' . $e->getErrorLangString() . ' at member ' . $member->name . ', iteration: ' . $count );
                }
                throw new \IPS\Task\Exception( $this, $e->getMessage() );
            }
        }

        return NULL;
    }

    /**
     * Cleanup
     *
     * If your task takes longer than 15 minutes to run, this method
     * will be called before execute(). Use it to clean up anything which
     * may not have been done
     *
     * @return	void
     */
    public function cleanup()
    {}
}