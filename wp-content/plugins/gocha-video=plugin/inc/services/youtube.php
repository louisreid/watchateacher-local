<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function gocha_video_youtube_player($player_code, $parent) {
    $regexstr = '~
        # Match Youtube link and embed code
        (?:                             # Group to match embed codes
            (?:<iframe [^>]*src=")?     # If iframe match up to first quote of src
            |(?:                        # Group to match if older embed
                (?:<object .*>)?        # Match opening Object tag
                (?:<param .*</param>)*  # Match all param tags
                (?:<embed [^>]*src=")?  # Match embed tag to the first quote of src
            )?                          # End older embed code group
        )?                              # End embed code groups
        (?:                             # Group youtube url
            https?:\/\/                 # Either http or https
            (?:[\w]+\.)*                # Optional subdomains
            (?:                         # Group host alternatives.
            youtu\.be/                  # Either youtu.be,
            | youtube\.com              # or youtube.com
            | youtube-nocookie\.com     # or youtube-nocookie.com
            )                           # End Host Group
            (?:\S*[^\w\-\s])?           # Extra stuff up to VIDEO_ID
            ([\w\-]{11})                # $1: VIDEO_ID is numeric
            [^\s]*                      # Not a space
        )                               # End group
        "?                              # Match end quote if part of src
        (?:[^>]*>)?                     # Match any extra stuff up to close brace
        (?:                             # Group to match last embed code
            </iframe>                   # Match the end of the iframe
            |</embed></object>          # or Match the end of the older embed
        )?                              # End Group of last bit of embed code
        ~ix';

    $player_code = preg_replace_callback($regexstr, array($parent, 'showplayer_yt'), $player_code);

    return $player_code;
}
