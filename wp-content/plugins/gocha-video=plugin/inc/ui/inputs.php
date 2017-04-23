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

class Gocha_Video_Control_Inputs extends Gocha_Video_Control {
    public $inputs = array();
    public $description = '';

    /*
     * Overrided label rendering
     */
    protected function render_label() {
        ?>
        <label for="settings-<?php echo esc_attr(is_array($this->name) ? $this->name[0] : $this->name); ?>">
            <?php echo esc_html($this->label); ?>
        </label>
        <?php
    }

    /*
     * Overrided control rendering
     */
    protected function render_control() {
        ?>
        <?php if(count($this->inputs)) : ?>
            <?php foreach($this->inputs as $input) : ?>
                <?php if(isset($input['before'])) : ?>
                    <?php echo $input['before']; ?>
                <?php endif; ?>

                <input
                    name="<?php echo esc_attr($input['name']); ?>"
                    id="settings-<?php echo esc_attr($input['name']); ?>"
                    value="<?php echo esc_attr($this->value[$input['name']]); ?>"
                    data-default="<?php echo esc_attr($this->default_value[$input['name']]); ?>"
                    type="<?php echo esc_attr($input['type']); ?>"
                    <?php if(isset($input['css_classes'])) : ?>
                    class="<?php echo esc_attr($input['css_classes']); ?>"
                    <?php endif; ?>
                    <?php if(count($input['attrs'])) : ?>
                        <?php foreach($input['attrs'] as $attr_name => $attr_value) : ?>
                            <?php echo esc_html($attr_name); ?>="<?php echo esc_attr($attr_value); ?>"
                        <?php endforeach; ?>
                    <?php endif; ?>
                >

                <?php if(isset($input['after'])) : ?>
                    <?php echo $input['after']; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if($this->description != '') : ?>
        <p class="description">
            <?php echo $this->description; ?>
        </p>
        <?php endif; ?>

        <?php
    }
}
