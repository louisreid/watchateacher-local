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

class Gocha_Video_Control_Checkbox extends Gocha_Video_Control_Input {
    public $helper_description = '';
    public $checkbox_value = '1';
    public $css_classes = 'settings-checkbox settings-field';
    public $attrs = array();

    /*
     * Overrided control rendering
     */
    protected function render_control() {
        ?>
        <label for="settings-<?php echo esc_attr($this->name); ?>">
            <input
                type="checkbox"
                name="<?php echo esc_attr($this->name); ?>"
                id="settings-<?php echo esc_attr($this->name); ?>"
                value="<?php echo esc_attr($this->checkbox_value); ?>"
                data-default="<?php echo esc_attr($this->default_value); ?>"
                <?php checked($this->value, 1); ?>
                class="<?php echo esc_attr($this->css_classes); ?>"
                <?php if(count($this->attrs)) : ?>
                    <?php foreach($this->attrs as $attr_name => $attr_value) : ?>
                        <?php echo esc_html($attr_name); ?>="<?php echo esc_attr($attr_value); ?>"
                    <?php endforeach; ?>
                <?php endif; ?>
            >
            <?php echo esc_html($this->helper_description); ?>
        </label>

        <?php if($this->description != '') : ?>
        <p class="description">
            <?php echo $this->description; ?>
        </p>
        <?php endif; ?>

        <?php
    }
}
