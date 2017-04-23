<?php
/**
 * GOCHA Geo Plugin
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
 * This class is used to create plugin button in the TinyMCE editor
 */
class Gocha_Video_Tinymce_Button {
    private $parent;
    private $controls_manager;

    public function __construct($parent) {
        // Parent handler
        $this->parent = $parent;
        // Controls manager for the popups
        $this->controls_manager = new Gocha_Video_Controls_Manager('popup', $this->parent);
        // Add actions
        add_action('admin_head', array($this, 'add_button'));
        add_action('admin_footer', array($this, 'add_popup_html'));
    }

    /*
     * Method used to add button to the editor
     */
    public function add_button() {
        // check user permissions
        if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
            return;
        }
        // check if user has enabled WYSIWYG
        if (get_user_option('rich_editing') == 'true') {
            add_filter("mce_external_plugins", array($this, "add_tinymce_plugin"));
            add_filter('mce_buttons', array($this, 'register_button'));
        }
    }

    /*
     * Method to add the plugin JS code
     */
    public function add_tinymce_plugin($plugin_array) {
        $plugin_array['gocha_video_button'] = plugins_url('/js/admin-button.js', dirname(__FILE__));
        return $plugin_array;
    }

    /*
     * Method used to add button to the list
     */
    public function register_button($buttons) {
        array_push($buttons, "gocha_video_button");
        return $buttons;
    }

    /*
     * Method used to generate the HTML code for the specific popup
     */
    public function add_popup_html() {
        $current_screen = get_current_screen();

        if($current_screen->id !== "post" && $current_screen->id !== "page") {
            return false;
        }

        $controls = $this->controls_manager;

        include(plugin_dir_path(__FILE__) . '/views/popup.php');
    }
}
