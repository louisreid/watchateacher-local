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

class Gocha_Video_Control_Info extends Gocha_Video_Control {
    public $description = '';
    public $attrs = array();

    /*
     * Overrided render method for options page
     */
    protected function render_for_options_page() {
        ?>
        <tr class="separator">
            <th scope="row">
                <?php $this->render_label(); ?>
            </th>
            <td>
                <?php $this->render_control(); ?>
            </td>
        </tr><!-- separator -->
        <?php
    }

    /*
     * Overrided label rendering
     */
    protected function render_label() {
        return false;
    }

    /*
     * Overrided control rendering
     */
    protected function render_control() {
        ?>
            <?php if($this->description != '') : ?>
            <span
                class="gocha-video-info"
                <?php if(count($this->attrs)) : ?>
                    <?php foreach($this->attrs as $attr_name => $attr_value) : ?>
                        <?php echo esc_html($attr_name); ?>="<?php echo esc_attr($attr_value); ?>"
                    <?php endforeach; ?>
                <?php endif; ?>
            >
                <?php echo $this->description; ?>
            </span>
            <?php endif; ?>
        <?php
    }
}
