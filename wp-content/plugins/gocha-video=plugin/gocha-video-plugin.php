<?php
/**
 * @package   gocha-video-plugin
 * @author    Ma Gochadesign <info@gochadesign.com>
 * @link      http://gochadesign.com
 * @copyright 2016 GochaDesign
 *
 * Plugin Name: GOCHA Video Plugin
 * Text Domain: gocha-video-plugin
 * Domain Path: /languages
 * Plugin URI:  http://gochavideo.com/
 * Description: Move your comments on to higher things! Extend your videos with interactive and innovative comment and discussion system. The most transparent one ever.
 * Author:      gochadesign
 * Author URI:  http://www.gochadesign.com
 *
 * Version:     1.2.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// UI classes
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/controls-manager.php');
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/control.php');
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/info.php');
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/input.php');
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/button.php');
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/checkbox.php');
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/inputs.php');
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/select.php');
include_once(plugin_dir_path( __FILE__ ) . '/inc/ui/separator.php');

// Services classes
require_once(plugin_dir_path( __FILE__ ) . '/inc/services/vimeo.php');
require_once(plugin_dir_path( __FILE__ ) . '/inc/services/youtube.php');
require_once(plugin_dir_path( __FILE__ ) . '/inc/services/google-drive.php');
require_once(plugin_dir_path( __FILE__ ) . '/inc/services/dailymotion.php');
require_once(plugin_dir_path( __FILE__ ) . '/inc/services/fb.php');

// Helper classes
require_once(plugin_dir_path( __FILE__ ) . '/inc/class-sanitize.php' );
require_once(plugin_dir_path( __FILE__ ) . '/inc/class-tinymce-button.php' );
require_once(plugin_dir_path( __FILE__ ) . '/inc/vc-integration/popup.php' );

// Main classes
require_once(plugin_dir_path( __FILE__ ) . '/inc/class-plugin.php' );
add_action( 'plugins_loaded', array( 'Gocha_Video_Plugin', 'get_instance' ) );

// Default settings
define('GOCHA_VIDEO_DEFAULT_SETTINGS', '{"mode":"range","parse_mode":"both","color1": "#f4f5f9", "color2": "#818c9e", "color3": "#1c9ad5", "mintimediff": "5", "hidecommentform": "0", "hidetimeline": "0", "commentdisplay": "0", "commentdisplaymode": "opacity", "commentopen": "0", "order": "ASC", "include_mode": "exclude", "excludedposts": "", "hide_youtube": "0", "hide_vimeo": "0", "hide_dailymotion": "0", "hide_fb": "0", "hide_google_drive": "0", "hide_media_element": "0"}');
