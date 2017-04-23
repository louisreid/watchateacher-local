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

class Gocha_Video_Control_Button extends Gocha_Video_Control {
    public $description = '';
    public $css_classes = '';

    /*
     * Overrided label rendering
     */
    protected function render_label() {
        ?>
        <label for="settings-<?php echo esc_attr($this->name); ?>">
            <?php echo esc_html($this->label); ?>
        </label>
        <?php
    }

    /*
     * Overrided control rendering
     */
    protected function render_control() {
        ?>
        <button class="button <?php echo esc_attr($this->css_classes); ?>" id="settings-<?php echo esc_attr($this->name); ?>">
            <?php echo esc_html($this->label); ?>
        </button>

        <?php if($this->description != '') : ?>
        <p class="description">
            <?php echo $this->description; ?>
        </p>
        <?php endif;
    }
}
