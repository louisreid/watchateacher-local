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
 * Base class used for creating controls
 */

abstract class Gocha_Video_Control {
    public $value = '';
    public $name = '';
    public $label = '';
    public $default_value = '';
    protected $mode;
    protected $settings;
    protected $text_domain;

    function __construct($manager_mode, $manager_settings, $settings, $text_domain) {
        $this->mode = $manager_mode;
        $this->settings = $manager_settings;
        $this->text_domain = $text_domain;
        $keys = array_keys(get_object_vars($this));

        foreach ($keys as $key) {
            if (isset($settings[$key])) {
                $this->$key = $settings[$key];
            }
        }
        // In the popup mode we do not need the values from the plugin settings
        if($this->mode == 'popup') {
            $this->value = '';

            if($this->default_value != '') {
                $this->value = $this->default_value;
            }
        } else {
            // Support multiple input fields
            if(is_array($this->name)) {
                if(count($this->name)) {
                    $this->value = array();

                    foreach($this->name as $name) {
                        $this->value[$name] = $this->settings[$name];
                    }
                }
            } else {
                if (isset($this->settings[$this->name])) {
                    $this->value = $this->settings[$this->name];
                } else {
                    $this->value = $this->default_value;
                }
            }
        }
    }

    /*
     * Method used to create a control label
     */
    abstract protected function render_label();

    /*
     * Method used to create a control output
     */
    abstract protected function render_control();

    /*
     * Method used to render the control in the popup
     */
    protected function render_for_popup() {
        ?>
        <div class="gocha-video-popup-control">
            <?php $this->render_label(); ?>
            <div>
                <?php $this->render_control(); ?>
            </div>
        </div>
        <?php
    }

    /*
     * Method used to render the control in the options page
     */
    protected function render_for_options_page() {
        ?>
        <tr>
            <th scope="row">
                <?php $this->render_label(); ?>
            </th>
            <td>
                <?php $this->render_control(); ?>
            </td>
        </tr>
        <?php
    }

    /*
     * Method used to render the control using the selected mode
     */
    public function render() {
        if($this->mode == 'popup') {
            $this->render_for_popup();
        } elseif($this->mode == 'options_page') {
            $this->render_for_options_page();
        }
    }
}
