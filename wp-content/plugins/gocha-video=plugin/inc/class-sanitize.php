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
if (!defined('WPINC')) {
    die;
}

/*
 * This class is used for sanitizing the option values
 */
class Gocha_Video_Sanitize {
    /*
     * Sanitize the specific option
     */
    static public function sanitize_setting($name, $value) {
        /*
         * List of the option filters - put here settings which should
         * use something more than sanitize_text_field
         */
        $option_filters = array(
            // Main settings
            'mode' => array('one-of', array('point', 'range')),
            'parse_mode' => array('one-of', array('both', 'shortcodes')),
            'color1' => array('callback', array('Gocha_Video_Sanitize', 'esc_color')),
            'color2' => array('callback', array('Gocha_Video_Sanitize', 'esc_color')),
            'color3' => array('callback', array('Gocha_Video_Sanitize', 'esc_color')),
            'mintimediff' => array('callback', 'absint'),
            'hidecommentform' => array('one-of', array('0', '1', 'true')),
    		'hidetimeline' => array('one-of', array('0', '1', 'true')),
    	    'commentdisplay' => array('one-of', array('0', '1', 'true')),
            'commentdisplaymode' => array('one-of', array('opacity', 'hide')),
    		'commentopen' => array('one-of', array('0', '1', 'true')),
    		'order' => array('one-of', array('ASC', 'DESC')),
            'include_mode' => array('one-of', array('include', 'exclude')),
            'excludedposts' => array('callback', array('Gocha_Video_Sanitize', 'esc_posts')),
            'hide_youtube' => array('one-of', array('0', '1', 'true')),
            'hide_vimeo' => array('one-of', array('0', '1', 'true')),
            'hide_dailymotion' => array('one-of', array('0', '1', 'true')),
            'hide_fb' => array('one-of', array('0', '1', 'true')),
            'hide_google_drive' => array('one-of', array('0', '1', 'true')),
            'hide_media_element' => array('one-of', array('0', '1', 'true')),
            // Shortcode params
            'url' => array('callback', 'esc_url')
        );

        // Check if the filter exists
        if(isset($option_filters[$name])) {
            $filter = $option_filters[$name];

            if($filter[0] === 'one-of' && !in_array($value, $filter[1])) {
                $value = $filter[1][0];
            } else if($filter[0] === 'callback') {
                $value = (string)call_user_func($filter[1], $value);
            } else if($filter[0] === 'regexp' && !preg_match($filter[1], $value)) {
                $value = '';
            }
        }

        $value = sanitize_text_field($value);

        return $value;
    }

    /*
     * Sanitize the settings array
     */
    static public function sanitize_settings($settings) {
        if(!$settings || !count($settings)) {
            return false;
        }

        foreach($settings as $setting_key => $setting_value) {
            $settings[$setting_key] = Gocha_Video_Sanitize::sanitize_setting($setting_key, $setting_value);
        }

        return $settings;
    }

    /*
     * Sanitize the shortcode attributes
     */
    static public function sanitize_shortcode_atts($attrs) {
       if(!count($attrs)) {
           return false;
       }

       foreach($attrs as $attr_key => $attr_value) {
           $attrs[$attr_key] = Gocha_Video_Sanitize::sanitize_setting($attr_key, $attr_value);
       }

       return $attrs;
    }

    /*
     * Sanitize posts
     */
    static public function esc_posts($value) {
        return preg_replace('@[^0-9,]@mi', '', $value);
    }

    /*
     * Sanitize color
     */
    static public function esc_color($value) {
        return preg_replace('@[^#a-f0-9]@mi', '', $value);
    }
}
