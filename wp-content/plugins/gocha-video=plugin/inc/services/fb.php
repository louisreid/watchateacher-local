<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function gocha_video_fb_player($player_code, $parent) {
    // Facebook videos - embed code
    $regexstr = '~
        # Match FB link and embed code
        (?:<div [^>]*data-href=")?      # If div match up to first quote of data-href
        (?:<iframe [^>]*src=")?         # If iframe match up to first quote of src
        (?:
                https?:\/\/             # Either http or https
                .*?
                facebook\.com           # Match facebook.com
                .*?
                ([0-9]{12,24})          # $1: VIDEO_ID is numeric
                .*?\/?
        )                               # End group
        "?                              # Match end quote if part of src or data-href
        (?:[^>]*></iframe>)?            # Match the end of the iframe
        (?:[^>]*></div>)?               # Match the end of the div
        ~ix';

    $player_code = preg_replace_callback($regexstr, array($parent, 'showplayer_fb'), $player_code);

    return $player_code;
}
