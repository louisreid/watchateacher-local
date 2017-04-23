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

class Gocha_Video_Control_Input extends Gocha_Video_Control {
    public $css_classes = 'settings-field';
    public $type = 'text';
    public $description = '';
    public $attrs = array();
    public $before = '';
    public $after = '';

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

        <?php echo $this->before; ?>

        <input
            name="<?php echo esc_attr($this->name); ?>"
            id="settings-<?php echo esc_attr($this->name); ?>"
            value="<?php echo esc_attr($this->value); ?>"
            data-default="<?php echo esc_attr($this->default_value); ?>"
            placeholder=""
            type="<?php echo esc_attr($this->type); ?>"
            class="<?php echo esc_attr($this->css_classes); ?>"
            <?php if(count($this->attrs)) : ?>
                <?php foreach($this->attrs as $attr_name => $attr_value) : ?>
                    <?php echo esc_html($attr_name); ?>="<?php echo esc_attr($attr_value); ?>"
                <?php endforeach; ?>
            <?php endif; ?>
        >

        <?php echo $this->after; ?>

        <?php if($this->description != '') : ?>
        <p class="description">
            <?php echo $this->description; ?>
        </p>
        <?php endif; ?>

        <?php
    }
}
