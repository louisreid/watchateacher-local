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

// Short handler
$controls = $this->controls_manager;

?>
<div id="gocha-video-plugin" class="wrap">
	<h1>
		<?php _e('GOCHA Video Plugin', 'gocha-video-plugin'); ?>
	</h1>

    <table class="form-table">
		<tbody>
			<?php
                $controls->render_control('Separator', array(
                    'label' => __('Basic settings', 'gocha-video-plugin'),
                    'description' => __('Basic configuration options for the plugin', 'gocha-video-plugin')
                ));

                $controls->render_control('Select', array(
					'name' => 'mode',
					'label' => __('Comments mode', 'gocha-video-plugin'),
                    'default_value' => 'range',
					'options' => array(
						'range' => __('Comments for a specific time range of video', 'gocha-video-plugin'),
                        'point' => __('Comments for a specific point of video', 'gocha-video-plugin')
					)
				));

                $controls->render_control('Input', array(
					'name' => 'mintimediff',
					'label' => __('Maximum time for comment grouping', 'gocha-video-plugin'),
                    'default_value' => '5',
                    "attrs" => array(
                        "step" => '1',
                        'min' => '1',
                        'max' => '1000'
                    ),
                    'css_classes' => 'settings-field small-text',
                    'description' => __('Set the maximum video time period where comments are grouped. Thanks to this, it is convenient to dicuss certain and crucial moments in one place.', 'gocha-video-plugin')
				));

                $controls->render_control('Checkbox', array(
					'name' => 'hidecommentform',
					'label' => __('Hide default form', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to hide WordPress default comment form (it depends on theme preferences).', 'gocha-video-plugin')
				));

                $controls->render_control('Checkbox', array(
					'name' => 'hidetimeline',
					'label' => __('Hide timeline', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to hide comments timeline.', 'gocha-video-plugin')
				));

                $controls->render_control('Checkbox', array(
					'name' => 'commentdisplay',
					'label' => __('Dynamic comments', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to show comments based on current player time.', 'gocha-video-plugin'),
                    'attrs' => array(
                        'data-show-option' => 'settings-mode',
                        'data-show-values' => 'range'
                    )
				));

                $controls->render_control('Select', array(
					'name' => 'commentdisplaymode',
					'label' => __('Comments highlight display', 'gocha-geo'),
                    'default_value' => 'opacity',
                    'options' => array(
						'opacity' => __('Decrease opacity of inactive comments', 'gocha-video-plugin'),
                        'hide' => __('Hide inactive comments', 'gocha-video-plugin')
					),
                    'attrs' => array(
                        'data-show-option' => 'settings-mode;settings-commentdisplay',
                        'data-show-values' => 'range;!:checked'
                    )
				));

                $controls->render_control('Checkbox', array(
					'name' => 'commentopen',
					'label' => __('Open comments', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to open comments after each video as default.', 'gocha-video-plugin')
				));

                $controls->render_control('Select', array(
					'name' => 'order',
					'label' => __('Comments order', 'gocha-video-plugin'),
                    'default_value' => 'ASC',
					'options' => array(
						'ASC' => __('Ascending', 'gocha-video-plugin'),
                        'DESC' => __('Descending', 'gocha-video-plugin')
					)
				));

                $controls->render_control('Select', array(
					'name' => 'include_mode',
					'label' => __('Include or exclude posts/pages?', 'gocha-video-plugin'),
                    'default_value' => 'exclude',
					'options' => array(
						'exclude' => __('Exclude', 'gocha-video-plugin'),
                        'include' => __('Include', 'gocha-video-plugin')
					),
                    'description' =>  __('In the include mode - comments for videos will be available only on the selected posts/pages. Exclude mode will disable comments for videos on the selected posts/pages. Changes will be visible after saving the settings.', 'gocha-video-plugin')
				));

                $controls->render_control('Input', array(
					'name' => 'excludedposts',
					'label' => __('Included/excluded posts/pages', 'gocha-video-plugin'),
                    'default_value' => '',
                    'description' => __('Posts/Pages IDs separated by comma. Changes will be visible after saving the settings.', 'gocha-video-plugin')
				));




                $controls->render_control('Separator', array(
                    'label' => __('Services settings', 'gocha-video-plugin'),
                    'description' => __('Services configuration options for the plugin', 'gocha-video-plugin')
                ));

                $controls->render_control('Select', array(
					'name' => 'parse_mode',
					'label' => __('Mode', 'gocha-video-plugin'),
                    'default_value' => 'both',
					'options' => array(
						'both' => __('Parse embed videos and shortcodes', 'gocha-video-plugin'),
                        'shortcodes' => __('Only shortcodes', 'gocha-video-plugin')
					)
				));

                $controls->render_control('Checkbox', array(
					'name' => 'hide_youtube',
					'label' => __('Skip YouTube Videos', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to skip YouTube videos by plugin', 'gocha-video-plugin'),
                    'attrs' => array(
                        'data-show-option' => 'settings-parse_mode',
                        'data-show-values' => 'both'
                    )
				));

                $controls->render_control('Checkbox', array(
					'name' => 'hide_vimeo',
					'label' => __('Skip Vimeo Videos', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to skip Vimeo videos by plugin', 'gocha-video-plugin'),
                    'attrs' => array(
                        'data-show-option' => 'settings-parse_mode',
                        'data-show-values' => 'both'
                    )
				));

                $controls->render_control('Checkbox', array(
					'name' => 'hide_dailymotion',
					'label' => __('Skip dailymotion Videos', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to skip dailymotion videos by plugin', 'gocha-video-plugin'),
                    'attrs' => array(
                        'data-show-option' => 'settings-parse_mode',
                        'data-show-values' => 'both'
                    )
				));

                $controls->render_control('Checkbox', array(
					'name' => 'hide_fb',
					'label' => __('Skip FB Videos', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to skip FB videos by plugin', 'gocha-video-plugin'),
                    'attrs' => array(
                        'data-show-option' => 'settings-parse_mode',
                        'data-show-values' => 'both'
                    )
				));

                $controls->render_control('Checkbox', array(
					'name' => 'hide_google_drive',
					'label' => __('Skip Google Drive Videos', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to skip Google Drive videos by plugin', 'gocha-video-plugin'),
                    'attrs' => array(
                        'data-show-option' => 'settings-parse_mode',
                        'data-show-values' => 'both'
                    )
				));

                $controls->render_control('Checkbox', array(
					'name' => 'hide_media_element',
					'label' => __('Skip embed Videos', 'gocha-geo'),
                    'default_value' => '0',
					'helper_description' => __('Check this to skip embed videos by plugin', 'gocha-video-plugin'),
                    'attrs' => array(
                        'data-show-option' => 'settings-parse_mode',
                        'data-show-values' => 'both'
                    )
				));




                $controls->render_control('Separator', array(
                    'label' => __('Visual settings', 'gocha-video-plugin'),
                    'description' => __('Visual configuration options for the plugin', 'gocha-video-plugin')
                ));

                $controls->render_control('Input', array(
					'name' => 'color1',
					'label' => __('Color 1', 'gocha-video-plugin'),
                    'default_value' => '#f4f5f9',
                    'css_classes' => 'settings-field settings-color-picker',
				));

                $controls->render_control('Input', array(
					'name' => 'color2',
					'label' => __('Color 2', 'gocha-video-plugin'),
                    'default_value' => '#818c9e',
                    'css_classes' => 'settings-field settings-color-picker',
				));

                $controls->render_control('Input', array(
					'name' => 'color3',
					'label' => __('Color 3', 'gocha-video-plugin'),
                    'default_value' => '#1c9ad5',
                    'css_classes' => 'settings-field settings-color-picker',
				));




                $controls->render_control('Separator', array(
                    'label' => __('Advanced settings', 'gocha-video-plugin'),
                    'description' => __('Advanced configuration options for the plugin', 'gocha-video-plugin')
                ));

                $controls->render_control('Button', array(
					'name' => 'main-restore-defaults',
					'label' => __('Restore default settings', 'gocha-geo'),
					'description' => __('<strong>Warning!</strong> You will lose all current settings and presets by clicking above button.', 'gocha-geo')
				));
            ?>
        </tbody>
    </table>

    <p class="submit">
		<button
			name="submit"
			id="settings-submit"
			class="button button-primary button-submit">
			<?php _e('Save Changes', 'gocha-video-plugin'); ?>
		</button>

		<span class="spinner"></span>

		<span class="error-message hidden">
			<span class="dashicons dashicons-warning"></span>
			<?php _e('An error occured during saving settings - please try again.', 'gocha-video-plugin'); ?>
		</span>
	</p>
</div>
