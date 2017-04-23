<?php

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function gocha_video_vimeo_player($player_code, $parent) {
    $regexstr = '~
        # Match Vimeo link and embed code
        (?:<iframe [^>]*src=")?         # If iframe match up to first quote of src
        (?:                             # Group vimeo url
                https?:\/\/             # Either http or https
                (?:[\w]+\.)*            # Optional subdomains
                vimeo\.com              # Match vimeo.com
                (?:[\/\w]*\/videos?)?   # Optional video sub directory this handles groups links also
                \/                      # Slash before Id
                ([0-9]+)                # $1: VIDEO_ID is numeric
                [^\s]*                  # Not a space
        )                               # End group
        "?                              # Match end quote if part of src
        (?:[^>]*></iframe>)?            # Match the end of the iframe
        (?:<p>.*</p>)?                  # Match any title information stuff
        ~ix';

    $player_code = preg_replace_callback($regexstr, array($parent, 'showplayer_vm'), $player_code);

    return $player_code;
}
