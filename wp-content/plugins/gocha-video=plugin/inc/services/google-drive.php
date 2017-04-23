<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function gocha_video_google_drive_player($player_code, $parent) {
    // Google Drive - embed code
    $regexstr = '~
        # Match Google Drive link and embed code
        (?:<iframe [^>]*src=")?         # If iframe match up to first quote of src
        (?:
                https?:\/\/             # Either http or https
                (?:[\w]+\.)*            # Optional subdomains
                google\.com             # Match google.com
                .*?                     # Added support for a third way of adding Google Drive Video
                \/file\/d\/             # Slashes and strings before Id
                ([0-9a-zA-Z]+)          # $1: VIDEO_ID is aplha-numeric
                \/preview               # Preview text
        )                               # End group
        "?                              # Match end quote if part of src
        (?:[^>]*></iframe>)?            # Match the end of the iframe
        ~ix';

    $player_code = preg_replace_callback($regexstr, array($parent, 'showplayer_gd'), $player_code);

    return $player_code;
}
