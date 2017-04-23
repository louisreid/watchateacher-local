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
?>
<div
    id="gocha-video-tinymce-popup"
    class="gocha-video-popup hidden"
>
    <div class="gocha-video-popup-heading">
        <h2 class="gocha-video-popup-header" data-edit="<?php _e('Edit video shortcode', 'gocha-video-plugin'); ?>" data-new="<?php _e('Insert video shortcode', 'gocha-video-plugin'); ?>">
        </h2>
        <a href="#" class="gocha-video-popup-close">&times;</a>
    </div>
    <div class="gocha-video-popup-wrapper">
        <div class="gocha-video-popup-content">
            <div class="gocha-video-popup-accordion-title" aria-expanded="true">
                <?php _e('Video settings', 'gocha-video-plugin'); ?>

                <button type="button" class="handlediv button-link">
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
            </div>
            <div class="gocha-video-popup-accordion-content" aria-expanded="true">
                <?php
                $controls->render_control('Info', array(
                    'description' => __('<h2>URL examples:</h2><strong>YouTube:</strong> <span>https://www.youtube.com/watch?v=XXXXXXXXXXX</span><br><strong>Vimeo:</strong> <span>https://player.vimeo.com/video/XXXXXXXXXX</span><br><strong>Google Drive:</strong> <span>https://drive.google.com/file/d/XXXXXXXXXXXXXXX/preview</span><br><strong>dailymotion:</strong> <span>https://www.dailymotion.com/embed/video/XXXXXXXXXXX</span><br><strong>Facebook Videos:</strong> <span>https://www.facebook.com/facebook/videos/XXXXXXXXXXXX/</span>', 'gocha-video-plugin')
                ));

                $controls->render_control('Input', array(
                    'name' => 'popup-url',
                    'label' => __('Video URL', 'gocha-video-plugin'),
                    'css_classes' => 'settings-field long-text',
                    'default_value' => ''
                ));
                ?>
            </div><!-- video settings -->

            <div class="gocha-video-popup-accordion-title" aria-expanded="false">
                <?php _e('Comments settings', 'gocha-video-plugin'); ?>

                <button type="button" class="handlediv button-link">
                    <span class="toggle-indicator" aria-hidden="true"></span>
                </button>
            </div>
            <div class="gocha-video-popup-accordion-content" aria-expanded="false">
                <?php
                    $controls->render_control('Select', array(
    					'name' => 'popup-mode',
    					'label' => __('Comments mode', 'gocha-video-plugin'),
                        'default_value' => $this->parent->options['mode'],
    					'options' => array(
    						'range' => __('Comments for a specific time range of video', 'gocha-video-plugin'),
                            'point' => __('Comments for a specific point of video', 'gocha-video-plugin')
    					)
    				));

                    $controls->render_control('Input', array(
    					'name' => 'popup-mintimediff',
    					'label' => __('Maximum time for comment grouping', 'gocha-video-plugin'),
                        'default_value' => $this->parent->options['mintimediff'],
                        "attrs" => array(
                            "step" => '1',
                            'min' => '1',
                            'max' => '1000'
                        ),
                        'css_classes' => 'settings-field small-text',
                        'description' => __('Set the maximum video time period where comments are grouped. Thanks to this, it is convenient to dicuss certain and crucial moments in one place.', 'gocha-video-plugin')
    				));

                    $controls->render_control('Checkbox', array(
    					'name' => 'popup-hidecommentform',
    					'label' => __('Hide default form', 'gocha-geo'),
                        'default_value' => $this->parent->options['hidecommentform'],
    					'helper_description' => __('Check this to hide WordPress default comment form (it depends on theme preferences).', 'gocha-video-plugin')
    				));

                    $controls->render_control('Checkbox', array(
    					'name' => 'popup-hidetimeline',
    					'label' => __('Hide timeline', 'gocha-geo'),
                        'default_value' => $this->parent->options['hidetimeline'],
    					'helper_description' => __('Check this to hide comments timeline.', 'gocha-video-plugin')
    				));

                    $controls->render_control('Checkbox', array(
    					'name' => 'popup-commentdisplay',
    					'label' => __('Dynamic comments', 'gocha-geo'),
                        'default_value' => $this->parent->options['commentdisplay'],
    					'helper_description' => __('Check this to show comments based on current player time.', 'gocha-video-plugin'),
                        'attrs' => array(
                            'data-show-option' => 'settings-popup-mode',
                            'data-show-values' => 'range'
                        )
    				));

                    $controls->render_control('Checkbox', array(
    					'name' => 'popup-commentdisplaymode',
                        'label' => __('Comments highlight display', 'gocha-geo'),
                        'default_value' => $this->parent->options['commentdisplaymode'],
                        'options' => array(
    						'opacity' => __('Decrease opacity of inactive comments', 'gocha-video-plugin'),
                            'hide' => __('Hide inactive comments', 'gocha-video-plugin')
    					),
                        'attrs' => array(
                            'data-show-option' => 'settings-popup-mode;settings-popup-commentdisplay',
                            'data-show-values' => 'range;!:checked'
                        )
    				));

                    $controls->render_control('Checkbox', array(
    					'name' => 'popup-commentopen',
    					'label' => __('Open comments', 'gocha-geo'),
                        'default_value' => $this->parent->options['commentopen'],
    					'helper_description' => __('Check this to open comments after each video as default.', 'gocha-video-plugin')
    				));

                    $controls->render_control('Select', array(
    					'name' => 'popup-order',
    					'label' => __('Comments order', 'gocha-video-plugin'),
                        'default_value' => $this->parent->options['order'],
    					'options' => array(
    						'ASC' => __('Ascending', 'gocha-video-plugin'),
                            'DESC' => __('Descending', 'gocha-video-plugin')
    					)
    				));
                ?>
            </div><!-- comments settings -->
        </div><!-- .gocha-video-popup-content -->
    </div><!-- .gocha-video-popup-wrapper -->

    <div class="gocha-video-popup-buttons">
        <a href="#" class="gocha-video-popup-cancel button">
            <?php _e('Cancel', 'gocha-video-plugin'); ?>
        </a>

        <a href="#" class="gocha-video-popup-save button button-primary" data-edit="<?php _e('Modify', 'gocha-video-plugin'); ?>" data-new="<?php _e('Insert', 'gocha-video-plugin'); ?>">
        </a>
    </div><!-- .gocha-video-popup-buttons -->
</div>
