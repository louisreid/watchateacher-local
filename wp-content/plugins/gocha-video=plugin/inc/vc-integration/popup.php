<?php
/**
 * GOCHA Video Plugin
 *
 * @package   Gocha_Video_Plugin
 * @author    MGocha <info@gochadesign.com>
 * @link      http://gochadesign.com
 * @copyright 2016 gochadesign.com
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
/*
 * Creating Visual Composer shortocde for simple mode
 */
function gocha_video_vc_shortcode($parent) {
    if(!function_exists('vc_map')) {
        return;
    }

    vc_map(array(
        "name" => __("GOCHA Video", 'gocha-video-plugin'),
        "base" => "gocha_video",
        "category" => __('GOCHA', 'gocha-video-plugin'),
        'icon' => 'icon-wpb-film-youtube',
        "params" => array(
            array(
                "type" => "textfield",
                "heading" => __('Video URL', 'gocha-video-plugin'),
                "param_name" => "url",
                'value' => '',
                'save_always' => true,
                'group' => __('Video', 'gocha-video-plugin')
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Comments mode", 'gocha-video-plugin'),
                "param_name" => "mode",
                'std' => $parent->options['mode'],
                'save_always' => true,
                'value' => array_flip(array(
                    'range' => __('Comments for a specific time range of video', 'gocha-video-plugin'),
                    'point' => __('Comments for a specific point of video', 'gocha-video-plugin')
                )),
                'group' => __('Comments', 'gocha-video-plugin')
            ),
            array(
                "type" => "textfield",
                "heading" => __('Maximum time for comment grouping', 'gocha-video-plugin'),
                "param_name" => "mintimediff",
                'value' => $parent->options['mintimediff'],
                'save_always' => true,
                'group' => __('Comments', 'gocha-video-plugin')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'hidecommentform',
                'heading' => __('Hide default form', 'gocha-video-plugin'),
                'value' => $parent->options['hidecommentform'],
                'save_always' => true,
                'group' => __('Comments', 'gocha-video-plugin')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'hidetimeline',
                'heading' => __('Hide timeline', 'gocha-video-plugin'),
                'value' => $parent->options['hidetimeline'],
                'save_always' => true,
                'group' => __('Comments', 'gocha-video-plugin')
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'commentdisplay',
                'heading' => __('Dynamic comments', 'gocha-video-plugin'),
                'value' => $parent->options['commentdisplay'],
                'save_always' => true,
                'group' => __('Comments', 'gocha-video-plugin'),
                'dependency' => array(
                    'element' => 'mode',
                    'value' => array('range')
                )
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'commentdisplaymode',
                'heading' => __('Comments highlight display', 'gocha-video-plugin'),
                'std' => $parent->options['commentdisplaymode'],
                'save_always' => true,
                'value' => array_flip(array(
                    'opacity' => __('Decrease opacity of inactive comments', 'gocha-video-plugin'),
                    'hide' => __('Hide inactive comments', 'gocha-video-plugin')
                )),
                'group' => __('Comments', 'gocha-video-plugin'),
                'dependency' => array(
                    'element' => 'mode',
                    'value' => array('range')
                )
            ),
            array(
                'type' => 'checkbox',
                'param_name' => 'commentopen',
                'heading' => __('Open comments', 'gocha-video-plugin'),
                'value' => $parent->options['commentopen'],
                'save_always' => true,
                'group' => __('Comments', 'gocha-video-plugin')
            ),
            array(
                'type' => 'dropdown',
                'param_name' => 'order',
                'heading' => __('Comments order', 'gocha-video-plugin'),
                'std' => $parent->options['order'],
                'value' => array_flip(array(
                    'ASC' => __('Ascending', 'gocha-video-plugin'),
                    'DESC' => __('Descending', 'gocha-video-plugin')
                )),
                'save_always' => true,
                'group' => __('Comments', 'gocha-video-plugin')
            )
        )
    ));
}
