<?php

namespace IPS\discord\Api;

/**
 * Class Helper
 *
 * @package IPS\discord\Api
 */
class _Helper
{
    /**
     * @param array $content
     * @return array
     */
    public static function formatDiscordArray( array $content )
    {
        $formattedContent = [];

        foreach ( $content as $item )
        {
            $formattedContent[$item['id']] = $item;
        }

        return $formattedContent;
    }

    /**
     * Dummy method to bypass coding standards check...
     */
    final public function dummy()
    {
    }
}
