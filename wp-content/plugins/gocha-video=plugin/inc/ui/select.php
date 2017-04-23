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

class Gocha_Video_Control_Select extends Gocha_Video_Control {
    public $description = '';
    public $css_classes = 'settings-field settings-select';
    public $options = array();
    public $attrs = array();
    public $dynamic_content = false;

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
        <select
            name="<?php echo esc_attr($this->name); ?>"
            id="settings-<?php echo esc_attr($this->name); ?>"
            class="<?php echo esc_attr($this->css_classes); ?>"
            <?php if($this->dynamic_content) : ?>
            data-default="<?php echo esc_attr($this->value); ?>"
            <?php else : ?>
            data-default="<?php echo esc_attr($this->default_value); ?>"
            <?php endif; ?>
            <?php if(count($this->attrs)) : ?>
                <?php foreach($this->attrs as $attr_name => $attr_value) : ?>
                 <?php echo esc_html($attr_name); ?>="<?php echo esc_attr($attr_value); ?>"
                <?php endforeach; ?>
            <?php endif; ?>
        >
            <?php if(count($this->options)) : ?>
                <?php foreach($this->options as $option_value => $option_label) : ?>
                <option value="<?php echo esc_attr($option_value); ?>" <?php selected($this->value, $option_value); ?>>
                    <?php echo esc_html($option_label); ?>
                </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>

        <?php if($this->description != '') : ?>
        <p class="description">
            <?php echo $this->description; ?>
        </p>
        <?php endif; ?>

        <?php
    }
}
