<?php

namespace IPS\discord\modules\admin\settings;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Discord settings
 */
class _settings extends \IPS\Dispatcher\Controller
{
    /**
     * Execute
     *
     * @return	void
     */
    public function execute()
    {
        \IPS\Dispatcher::i()->checkAcpPermission( 'settings_manage' );
        parent::execute();

        \IPS\Output::i()->jsFiles = array_merge(
            \IPS\Output::i()->jsFiles,
            \IPS\Output::i()->js( 'admin_settings.js', 'discord', 'admin' )
        );
    }

    /**
     * Show settings form.
     *
     * @return	void
     */
    protected function manage()
    {
        $settings = \IPS\Settings::i();
        $redirectUris = [
            (string) \IPS\Http\Url::internal( 'app=discord&module=register&controller=link&do=admin', 'front' ),
            (string) \IPS\Http\Url::internal( 'applications/discord/interface/oauth/auth.php', 'none' )
        ];

        $form = new \IPS\Helpers\Form;

        if ( $settings->discord_bot_token )
        {
            $form->addButton( 'discord_handshake', 'button', NULL, 'ipsButton ipsButton_alternate', [
                'data-controller' => "discord.admin.settings.handshake",
                'data-token' => $settings->discord_bot_token
            ] );
        }

        $form->addTab( 'discord_connection_settings' );
        $form->addMessage(
            \IPS\Member::loggedIn()->language()->addToStack( 'discord_redirect_uris', FALSE, [
                'sprintf' => $redirectUris
            ]),
            'ipsMessage ipsMessage_info'
        );
        $form->add(
            new \IPS\Helpers\Form\Text( 'discord_client_id', $settings->discord_client_id ?: NULL, TRUE )
        );
        $form->add(
            new \IPS\Helpers\Form\Password( 'discord_client_secret', $settings->discord_client_secret ?: NULL, TRUE )
        );
        $form->add(
            new \IPS\Helpers\Form\Password( 'discord_bot_token', $settings->discord_bot_token ?: NULL, TRUE )
        );
        $form->add(
            new \IPS\Helpers\Form\Text( 'discord_guild_id', $settings->discord_guild_id ?: NULL, FALSE )
        );

        $form->addTab( 'discord_map_settings' );
        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_remove_unmapped', $settings->discord_remove_unmapped ?: FALSE )
        );
        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_sync_bans', $settings->discord_sync_bans ?: FALSE )
        );

        $form->addTab( 'discord_post_settings' );
        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_post_topics', $settings->discord_post_topics ?: FALSE, FALSE, [
                'togglesOff' => [
                    'discord_post_unapproved_topics'
                ]
            ] )
        );
        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_post_unapproved_topics', $settings->discord_post_unapproved_topics ?: FALSE,
                FALSE, [], NULL, NULL, NULL, 'discord_post_unapproved_topics'
            )
        );
        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_post_posts', $settings->discord_post_posts ?: FALSE, FALSE, [
                'togglesOff' => [
                    'discord_post_unapproved_posts'
                ]
            ] )
        );
        $form->add(
            new \IPS\Helpers\Form\YesNo( 'discord_post_unapproved_posts', $settings->discord_post_unapproved_posts ?: FALSE,
                FALSE, [], NULL, NULL, NULL, 'discord_post_unapproved_posts'
            )
        );
        $form->add(
            new \IPS\Helpers\Form\TextArea(
                'discord_new_topic',
                $settings->discord_new_topic ?: '%poster has just posted a new topic called: "%topicTitle". Read more: %link',
                TRUE, [], NULL, NULL, NULL, 'discord_new_topic'
            )
        );
        $form->add(
            new \IPS\Helpers\Form\TextArea(
                'discord_new_post',
                $settings->discord_new_post ?: '%poster has just posted a new post to the topic: "%topicTitle". Read more: %link',
                TRUE, [], NULL, NULL, NULL, 'discord_new_post'
            )
        );

        if ( $values = $form->values() )
        {
            if ( empty( $settings->discord_guild_id ) || empty( $values['discord_guild_id'] ) )
            {
                $redirect = \IPS\Http\Url::external( \IPS\discord\Api::OAUTH2_URL . 'authorize' )
                    ->setQueryString([
                        'client_id' => $values['discord_client_id'],
                        'permissions' => \IPS\discord\Api::PERM_ADMINISTRATOR,
                        'response_type' => 'code',
                        'scope' => \IPS\discord\Api::SCOPE_BOT,
                        'redirect_uri' => $redirectUris[0]
                    ]);
            }
            else
            {
                $redirect = \IPS\Http\Url::internal( 'app=discord&module=settings&controller=settings' );
            }

            $form->saveAsSettings( $values );

            \IPS\Output::i()->redirect( $redirect );
        }

        /* Output */
        \IPS\Output::i()->title = \IPS\Member::loggedIn()->language()->addToStack( 'discord_setting_title' );
        \IPS\Output::i()->output = (string) $form;
    }
}