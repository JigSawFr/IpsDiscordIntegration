//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

class discord_hook_forum extends _HOOK_CLASS_
{
    /**
     * [Node] Add/Edit Form
     *
     * @param	\IPS\Helpers\Form	$form	The form
     * @return	void
     */
    public function form( &$form )
    {
        $args = func_get_args();
        /**
         * Need to use a hacky way to support PHP5 and 7
         * call_user_func_array( 'parent::form', func_get_args() ) errors out on PHP7 so cannot use that.
         * @see http://stackoverflow.com/questions/1905800/php-call-user-func-array-pass-by-reference-issue
         */
        $Args = array();
        foreach( $args as $k => &$arg )
        {
            $Args[$k] = &$arg;
        }
        call_user_func_array( 'parent::form', $Args );

        $guild = new \IPS\discord\Api\Guild;
        $channels = $guild->getChannelsOnlyName();

        $form->addHeader( 'discord_channels' );
        $form->add(
            new \IPS\Helpers\Form\Select( 'discord_channel_approved', $this->discord_channel_approved ?: 0, TRUE, [
                'options' => $channels
            ] )
        );
        $form->add(
            new \IPS\Helpers\Form\Select( 'discord_channel_unapproved', $this->discord_channel_unapproved ?: 0, TRUE, [
                'options' => $channels
            ] )
        );
    }
}