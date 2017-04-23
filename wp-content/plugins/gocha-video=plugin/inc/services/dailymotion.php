<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function gocha_video_dailymotion_player($player_code, $parent) {
    // dailymotion - embed code
    $regexstr = '~
        # Match dailymotion link and embed code
        (?:<iframe [^>]*src=")?         # If iframe match up to first quote of src
        (?:
            (?:https?:\/\/)*            # Either http or https
            (?:\/\/)*                   # or protocol-less
            (?:[\w]+\.)*                # Optional subdomains
            dailymotion\.com            # Match dailymotion.com
            \/embed\/video\/            # Slashes and strings before Id
            ([0-9a-zA-Z]+)              # $1: VIDEO_ID is aplha-numeric
            \?*                         # Params separator
            .*                          # Params
        )                               # End group
        "?                              # Match end quote if part of src
        (?:[^>]*></iframe>)?            # Match the end of the iframe
        ~ix';

    $player_code = preg_replace_callback($regexstr, array($parent, 'showplayer_dm'), $player_code);

    return $player_code;
}
