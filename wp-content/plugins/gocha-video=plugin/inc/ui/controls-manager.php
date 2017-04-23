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

class Gocha_Video_Controls_Manager {
    public $mode;
    public $settings;

    function __construct($mode, $parent) {
        $this->mode = $mode;
        $this->settings = $parent->options;
        $this->text_domain = 'gocha-video-plugin';
    }

    /*
     * Method used to render a control
     */
    function render_control($control_type, $control_settings) {
        $control_class_name = 'Gocha_Video_Control_' . $control_type;
        $control_instance = new $control_class_name($this->mode, $this->settings, $control_settings, $this->text_domain);
        $control_instance->render();
    }
}
