<?php

namespace IPS\discord\Api;

/**
 * Class Channel
 *
 * @package IPS\discord\Api
 */
class _Channel extends \IPS\discord\Api\AbstractResponse
{
    /**
     * Post given message to the given channel.
     *
     * @param \IPS\forums\Topic $topic
     * @param \IPS\forums\Topic\Post $post
     * @param \IPS\Member $member
     * @return array|NULL
     */
    public function post( \IPS\forums\Topic $topic, \IPS\forums\Topic\Post $post = NULL, \IPS\Member $member = NULL )
    {
        $info = $this->getPostInformation( $topic, $post, $member );

        if ( $info !== NULL )
        {
            $this->api->setUrl( \IPS\discord\Api::API_URL )
                ->setUri( 'channels/' . $info['channelId'] . '/messages' )
                ->setMethod( 'post' )
                ->setParams(json_encode([
                    'content' => $info['content']
                ]));

            return $this->handleApi();
        }

        return NULL;
    }

    /**
     * Build the message to be sent and retrieve the correct channel id.
     *
     * @param \IPS\forums\Topic $topic
     * @param \IPS\forums\Topic\Post|NULL $post
     * @param \IPS\Member|NULL $member
     * @return array|null
     */
    protected function getPostInformation( \IPS\forums\Topic $topic, \IPS\forums\Topic\Post $post = NULL, \IPS\Member $member = NULL )
    {
        $member = $member ?: \IPS\Member::load( $post ? $post->author_id : $topic->starter_id );
        $post = $post ?: $topic->firstComment();
        $isHidden = $post->new_topic ? $topic->hidden() : $post->hidden();

        if (
            ( ( $channelId = $topic->container()->discord_channel_approved ) && !$isHidden )
            ||
            ( ( $channelId = $topic->container()->discord_channel_unapproved ) && $isHidden )
        )
        {
            $link = (string) \IPS\Http\Url::internal(
                "app=forums&module=forums&controller=topic&id={$topic->tid}&do=findComment&comment={$post->pid}",
                'front',
                'forums_topic'
            );

            return [
                'content' => $this->createMessage( $member, $topic->title, $link, $post->new_topic ),
                'channelId' => $channelId
            ];
        }

        return NULL;
    }

    /**
     * Create message to be send.
     *
     * @param \IPS\Member $member
     * @param string $title
     * @param string $link
     * @param bool $isTopic
     * @return string
     */
    protected function createMessage( \IPS\Member $member, $title, $link, $isTopic )
    {
        $poster = "@{$member->name}";

        if ( $member->is_discord_connected )
        {
            $poster = "<@!{$member->discord_id}>";
        }

        $search = [
            '%poster',
            '%topicTitle',
            '%link'
        ];

        $replace = [
            $poster,
            $title,
            $link
        ];

        if ( $isTopic )
        {
            $subject = \IPS\Settings::i()->discord_new_topic;
        }
        else
        {
            $subject = \IPS\Settings::i()->discord_new_post;
        }

        $message = str_replace( $search, $replace, $subject );

        return $message;
    }
}
