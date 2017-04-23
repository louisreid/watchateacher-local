<?php
/**
 * GOCHA Video Plugin
 *
 * @package   gocha-video-plugin
 * @author    MGocha <info@gochadesign.com>
 * @link      http://gochadesign.com
 * @copyright 2015 gochadesign.com
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/*
 * Plugin class.
 */
class Gocha_Video_Plugin {
    // Plugin version, used for cache-busting of style and script file references.
    const VERSION = '1.2.0';
    // Plugin option values and their backup for the shortcodes use
	public $options;
    public $options_copy;
	// Plugin language phrases
	private $lang;
    // Instance of this class.
    protected static $instance = null;

    /*
     * Initialize the plugin by setting localization, filters, and administration functions.
     */
    private function __construct() {
        global $pagenow;

        // Load plugin text domain
        add_action('init', array($this, 'load_plugin_textdomain'));

        // Load plugin settings
        $this->options = get_option('gocha_video_settings', "{}");

        // Detect empty settings and load then default ones
        if($this->options === '{}') {
            update_option('gocha_video_settings', GOCHA_VIDEO_DEFAULT_SETTINGS);
            $this->options = get_option('gocha_video_settings', "{}");
        }

        $this->options = json_decode($this->options, true);

        // Legacy options
        if(!isset($this->options['commentdisplaymode'])) {
            $this->options['commentdisplaymode'] = 'opacity';
        }

        // Init UI manager
        $this->controls_manager = new Gocha_Video_Controls_Manager('options_page', $this);

        // Sanitize settings
        $this->options = Gocha_Video_Sanitize::sanitize_settings($this->options);

        if(isset($this->options['excludedposts'])) {
            $this->options['excludedposts'] = explode(',', $this->options['excludedposts']);
        } else {
            $this->options['excludedposts'] = array();
        }

		// Load front-end JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ), 1000 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_colors' ), 1002 );
        add_action( 'wp_enqueue_scripts', array( $this, 'hide_comments_form_via_css'));

        // Load JavaScript and CSS files.
        if(in_array($pagenow, array('tools.php', 'post-new.php', 'post.php'))) {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_color_picker_assets'));
        }

        if(in_array($pagenow, array('post-new.php', 'post.php'))) {
            add_action('admin_enqueue_scripts', array($this, 'add_button_css'));
        }

        // Add plugin page in tools section.
        add_action('admin_menu', array( $this, 'add_plugin_page'));

        // Other actions and filters
        add_action( 'after_setup_theme',array( $this, 'init_options'), 100 );
		add_action( 'comment_post', array( $this, 'save_comment_meta_data') );
		add_filter( 'get_comment_text', array( $this, 'modify_comment'), 1001, 2 );
		add_action( 'comment_post', array( $this, 'ajax_comment'), 1001, 2 );

        if($this->options['parse_mode'] === 'both') {
    		add_filter( 'wp_video_shortcode', array( $this, 'media_element'), 100, 5 );
        }

        // AJAX actions
        add_action('wp_ajax_gocha_video_settings_save', array($this, 'ajax_settings_save'));
        add_action('wp_ajax_gocha_video_restore_defaults', array($this, 'ajax_restore_defaults'));

        // Add shortcode
    	add_shortcode('gocha_video', array($this, 'shortcode'));

		// Language phrases
		$this->lang = array(
			'start' 			=> __('Start', 'gocha-video-plugin'),
			'change' 			=> __('Set current', 'gocha-video-plugin'),
			'form_add_range' 	=> __('Set comment start time', 'gocha-video-plugin'),
            'form_add_point' 	=> __('Add comment at this point', 'gocha-video-plugin'),
			'stop' 				=> __('End', 'gocha-video-plugin'),
			'comments_show' 	=> __('Show comments', 'gocha-video-plugin'),
			'time' 				=> __('Time', 'gocha-video-plugin'),
			'comments_empty' 	=> __('No comments', 'gocha-video-plugin')
		);

        // Create TinyMCE editor button
        new Gocha_Video_Tinymce_Button($this);

        // Add Visual Composer shortcode if available
        gocha_video_vc_shortcode($this);
    }

    /*
     * Return an instance of this class.
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /*
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        $domain = 'gocha-video-plugin';
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
        load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
    }

    /**
     * Add plugin page to the Tools section.
     */
    public function add_plugin_page() {
       add_management_page(
           __( 'GOCHA Video Plugin', 'gocha-video-plugin' ),
           __( 'GOCHA Video Plugin', 'gocha-video-plugin' ),
           'manage_options',
           'gocha_video_plugin',
           array( $this, 'display_plugin_page' )
       );
    }

    /**
     * Render the settings page for this plugin.
     */
    public function display_plugin_page() {
       include(plugin_dir_path(__FILE__) . '/settings.php');
    }

    /**
     * AJAX plugin settings save
     */
    public function ajax_settings_save() {
        check_ajax_referer('gocha_video_settings_save', 'nonce');

        if(!current_user_can('manage_options')) {
            echo '-1';
            die();
        }

        $settings = $_POST['settings'];
        $settings = Gocha_Video_Sanitize::sanitize_settings($settings);
        $settings = json_encode($settings);
        $settings = sanitize_text_field($settings);
        update_option('gocha_video_settings', $settings);
        echo 'OK';

        die();
    }

    /**
     * AJAX restore defaults
     */
    public function ajax_restore_defaults() {
        check_ajax_referer('gocha_video_restore_defaults', 'nonce' );

        if(!current_user_can('manage_options')) {
            echo '-1';
            die();
        }

        update_option('gocha_video_settings', GOCHA_VIDEO_DEFAULT_SETTINGS);
        echo 'OK';

        die();
    }

	/*
     * Display comment content via AJAX
     */
    public function ajax_comment($comment_ID, $comment_status) {
		// If it's an AJAX-submitted comment
		if($this->is_ajax_request()){
			if( $commenttitle = get_comment_meta( $comment_ID, 'gocha_video_input', true )) {
				// Get the comment data
				$comment = get_comment($comment_ID);
				// Get the comment HTML from my custom comment HTML function
				$commentContent = $this->load_comment_html($comment,'block');
				// Kill the script, returning the comment HTML
				die($commentContent);
			}
		}
	}

	/*
     * Check if the request is AJAX request
     */
	public function is_ajax_request() {
		return
			isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

    /*
     * Register and enqueue admin-specific JavaScript.
     */
    public function enqueue_frontend_scripts() {
		wp_enqueue_script(
            'gocha-video-plugin-frontend-script',
            plugins_url( 'js/gocha-video-plugin.js', dirname(__FILE__)),
            array( 'jquery' ),
            self::VERSION,
            true
        );

		wp_enqueue_style(
            'gocha-video-plugin-frontend-style',
            plugins_url( 'css/gocha-video-style.css', dirname(__FILE__)),
            '',
            self::VERSION
        );

        wp_localize_script( 'gocha-video-plugin-frontend-script', 'gochavideoplugin_var', array(
			'txt_reply' 			=> __('Reply', 'gocha-video-plugin' ),
			'txt_cancel_reply' 		=> __('Cancel reply', 'gocha-video-plugin' ),
			'txt_h2_default' 		=> __('Add comment', 'gocha-video-plugin' ),
			'txt_h2_reply' 			=> __('Add reply', 'gocha-video-plugin' ),
			'txt_bb_start' 			=> __('Start', 'gocha-video-plugin' ),
			'txt_bb_stop' 			=> __('End', 'gocha-video-plugin' ),
			'txt_bb_form_add' 		=> __('Set comment start time', 'gocha-video-plugin' ),
            'txt_bb_form_add_point' => $this->lang['form_add_point'],
			'txt_bb_form_show' 		=> __('Show form', 'gocha-video-plugin' ),
			'txt_bb_form_hide' 		=> __('Hide form', 'gocha-video-plugin' ),
			'txt_bb_comments_show' 	=> __('Show comments', 'gocha-video-plugin' ),
			'txt_bb_comments_hide' 	=> __('Hide comments', 'gocha-video-plugin' ),
			'txt_error_time' 		=> __('Set comment end time', 'gocha-video-plugin' ),
			'txt_fb_dynamic' 		=> __('Dynamic', 'gocha-video-plugin' ),
			'txt_fb_all' 			=> __('All', 'gocha-video-plugin' ),
			'txt_time' 				=> __('Time', 'gocha-video-plugin' ),
			'txt_ajax_error'        => __('You might have left one of the fields blank, or be posting too quickly.', 'gocha-video-plugin' ),
			'txt_ajax_success'      => __('Thanks for your comment. We appreciate your response.', 'gocha-video-plugin' ),
			'txt_ajax_delay'        => __('Please wait a while before posting your next comment.', 'gocha-video-plugin' ),
			'txt_ajax_wait'         => __('Processing...', 'gocha-video-plugin' ),
			'txt_ajax_fillform'     => __('Check form and fills all needed fields - comment may be too short. Please also check if your session did not expired.', 'gocha-video-plugin' ),
            'txt_dm_alert_stop'     => __('Please wait when the video will start.', 'gocha-video-plugin')
        ));
    }

    /**
     * Register and enqueue admin-specific assets
     */
    public function enqueue_admin_scripts() {
        // Register and enqueue JS
        wp_register_script('gocha-video-plugin', plugins_url('js/admin.js', dirname(__FILE__)), array('jquery'), Gocha_Video_Plugin::VERSION, true);
        wp_enqueue_script('gocha-video-plugin');

        // Register and enqueue CSS
        wp_register_style('gocha-video-plugin', plugins_url( 'css/admin.css', dirname(__FILE__)), array(), Gocha_Video_Plugin::VERSION);
        wp_enqueue_style('gocha-video-plugin');

        // Translation and other data
        wp_localize_script('gocha-video-plugin', 'ajax_gocha_video_var', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce_settings_save' => wp_create_nonce( 'gocha_video_settings_save' ),
            'nonce_restore_defaults' => wp_create_nonce( 'gocha_video_restore_defaults' ),
            'restore_alert' => __('WARNING! This action will completly remove your current settings and presets. Do you want to do it?', 'gocha-video-plugin'),
            'restore_error' => __('Restoration failed. Please try again.', 'gocha-video-plugin'),
            'tinymce_button_label' => __('GOCHA Video', 'gocha-vide-plugin')
        ));
    }

    /**
     * Function used to assets for the color pickers
     */
    public function enqueue_color_picker_assets() {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('iris', admin_url('js/iris.min.js'),array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
        wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false,1);
        $colorpicker_l10n = array(
            'clear' => __('Clear', 'gocha-geo'),
            'defaultString' => __('Default', 'gocha-geo'),
            'pick' => __('Select Color', 'gocha-geo')
        );
        wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
    }

    /*
     * Register and enqueue custom colors.
     */
    public function enqueue_custom_colors() {
		$opt_color1 = $this->options['color1'];
		$opt_color2 = $this->options['color2'];
		$opt_color3 = $this->options['color3'];
		$opt_hideform = $this->options['hidecommentform'];
        $custom_colors = "
            .gocha-comment-avatar,
            .gocha-dynamic-seeker,
            .gocha-video-bar-area,
            .gocha-comment-text:before,
            .gocha-dynamic-seeker:hover .gocha-bar .gocha-bar-time {
            	border-color: {$opt_color1};
            }

            .gocha-start-button:before {
                border-color: transparent {$opt_color2} transparent transparent;
            }

            .gocha-stop-button:after {
                border-color: transparent transparent transparent {$opt_color2};
            }

            .gocha-video-comment-anchor.gvp-default-list,
            .gocha-video-comment-start,
            .gocha-video-replay-link,
            .gocha-dynamic-seeker .gocha-bar .gocha-bar-time,
            .gocha-marker.g-active span,
            .gocha-dynamic-seeker:hover,
            .gocha-video-comment:hover > .gocha-comment-avatar {
            	border-color: {$opt_color3};
            }

            .gocha-video-bar-area:after,
            .gocha-video-bar-area:before,
            .gocha-dynamic-seeker .gocha-bar .gocha-bar-time,
            .gocha-video-commentarea,
            .gocha-video-controls,
            .gocha-video-st,
            .gocha-comment-text,
            .gocha-comment-avatar img {
            	background-color: {$opt_color1};
            }

            .gocha-dynamic-seeker:hover .gocha-bar .gocha-bar-time,
            .gocha-marker.g-active span,
            .gocha-video-add-area,
            .gocha-marker,
            .gocha-video-add-area button:disabled:hover,
            .gocha-video-add-area button,
            .gocha-video-show,
            .gocha-dynamic-seeker,
            .gocha-video-commentbox {
            	background-color: {$opt_color2};
            }

            .gocha-video-st:hover .gtimechange,
            .gocha-dynamic-seeker .gocha-bar,
            .gocha-marker.g-active,
            .gocha-marker:hover,
            .gocha-video-show:hover,
            .gocha-video-add-area button:hover {
            	background-color: {$opt_color3};
            }

            .gocha-video-add-area button.g-active.gocha-stop-button:hover:enabled:after {
            	border-color: transparent transparent transparent {$opt_color3};
            }

            .gocha-video-add-area button.g-active.gocha-start-button:hover:enabled:before {
            	border-color: transparent {$opt_color3} transparent transparent;
            }

            .gocha-video-st:hover .gtimechange,
            .gocha-video-placeholder,
            .gocha-video-add-area,
            .gocha-video-show,
            .gocha-video-add-area button,
            .gocha-marker {
            	color: {$opt_color1};
            }

            .gtimechange,
            .gtimetitle,
            .gtimenumber,
            .gocha-cancel-reply,
            .gocha-video-time,
            .gocha-video-comment-all,
            .gocha-video-comment-dynamic {
                color: {$opt_color2};
            }

            .gocha-dynamic-seeker .gocha-bar .gocha-bar-time,
            .gocha-video-comment-all.g-active,
            .gocha-video-comment-dynamic.g-active {
                color: {$opt_color3};
            }";


        if ($opt_hideform) {
            wp_enqueue_style('gocha-video-plugin-hidden-form');
        }

        wp_add_inline_style( 'gocha-video-plugin-frontend-style', $custom_colors );
    }

    /*
	 * Adds the default actions and filters.
	 */
    public function init_options(){
        if($this->options['parse_mode'] === 'both') {
    		add_filter( 'the_content', array( $this,'videoautop'), 10000);
        }
    }

	/*
	 * Returns comments list
	 */
    public function load_comments_list($vId, $parent = 0) {
		$args = array(
			'post_id' => get_the_ID(),
			'status' => 'approve',
			'parent' => $parent,
			'orderby' => 'comment_date_gmt',
			'order' => $this->options['order'],
			'meta_query'     => array(
				array(
					'key'       => 'gocha_video_input',
					'value'     => $vId,
					'compare'   => 'LIKE',
					'type'      => 'CHAR',
				)
			)
		);
		$output = '';
		$comments_query = new WP_Comment_Query;
		$comments = $comments_query->query( $args );

		// Comment Loop
	    if ($comments) {
			if(0 !== $parent){
                $output .= '<ul class="children">';
            }

			foreach ( $comments as $comment ) {
				$output .= $this->load_comment_html($comment);
			}

            if(0 !== $parent){
                $output .= '</ul>';
            }

            if(0 === $parent){
                $output .= '<li class="gocha-video-placeholder">'.__('No comments at this time range', 'gocha-video-plugin' ).'</li>';
            }

            return $output;
		}

        return '';
	}

	/*
	 * Displays comments list html
	 */
	private function load_comment_html($comment,$display='none') {
		$output = '';

		if( $comment_video = get_comment_meta($comment->comment_ID, 'gocha_video_input', true ) ) {
			$comment_video = json_decode($comment_video);
            $start_date = date( 'G:i:s', $comment_video[1] );
            $end_date = date( 'G:i:s', $comment_video[2] );

            if($this->options['mode'] === 'point') {
                $comment_text =  __( 'Commented on: %s', 'gocha-video-plugin' );
            } else {
    			$comment_text =  __( 'Commented on: %s to %s', 'gocha-video-plugin' );
            }

			$output .= '<li class="gocha-video-comment gvc-show" id="gocha-div-comment-'.$comment->comment_ID.'" data-gocha-comment-video="'.$comment_video[0].'" data-gocha-start="'.number_format((float) $comment_video[1], 2, '.', '').'" data-gocha-stop="'.number_format((float) $comment_video[2], 2, '.', '').'" data-comment-id="'.$comment->comment_ID.'">';
				$output .= '<div class="gocha-comment-avatar">';
				$output .= get_avatar( $comment, $size = '75' );
				$output .= '</div>';
				$output .= '<div class="gocha-comment-text clearfix">';
					$output .= '<div class="gocha-meta clearfix">';
						$output .= '<strong class="gocha-autor" itemprop="author">'.$comment->comment_author.'</strong> <span class="gocha-dash">&ndash;</span> <small><time class="gocha-time" itemprop="datePublished" datetime="'.$comment->comment_date_gmt.'">'. $comment->comment_date.'</time></small>';
						$output .= '<a href="#replay" data-comment-id="'.$comment->comment_ID.'" class="gocha-video-replay-link">'.__( 'Reply' , 'gocha-video-plugin' ).'</a><a href="#play" class="gocha-video-comment-start">'.sprintf( $comment_text, $start_date, $end_date ).'</a>';
					$output .= '</div>';
					$output .= '<div itemprop="description" class="gocha-video-description">'.$comment->comment_content.'</div>';
				$output .= '</div>';
				$output .= $this->load_comments_list($comment_video[0], $comment->comment_ID);
			$output .= '</li>';
		}

		return $output;
	}

	/*
	 * Displays comments form
	 */
	private function load_comments_form() {
		$output = '';

		if ( get_option('comment_registration') && !is_user_logged_in() ) {
			$text_format = __('You must be <a href="%s">logged in</a> to post a comment.', 'gocha-video-plugin');
			$output .= '<p>'.sprintf( $text_format, wp_login_url( get_permalink() ) ).'</p><br/>';
		} else {
            ob_start();
            include('views/form.php');
            $output .= ob_get_clean();
		}

		return $output;
	}

	/*
	 * Save comment meta data
	 */
	public function save_comment_meta_data( $comment_id ) {
		if ((isset($_POST['gocha_video_input'])) && ('' != $_POST['gocha_video_input'])) {
			$comment = wp_filter_nohtml_kses($_POST['gocha_video_input']);
			add_comment_meta( $comment_id, 'gocha_video_input', $comment );
		}
	}

	/*
	 * Modify comment data
	 */
	public function modify_comment( $text, $comment = null ){
		if($this->is_ajax_request()){
			return $text;
		}

		if($comment == null) {
			return $text;
		}

		if( $commenttitle = get_comment_meta( $comment->comment_ID, 'gocha_video_input', true ) ) {
			$commenttitle = json_decode($commenttitle);
			$comment_text = __( 'Commented on: %s to %s', 'gocha-video-plugin' );
			$start_date = date( 'G:i:s', $commenttitle[1] );
			$end_date = date( 'G:i:s', $commenttitle[2] );

			$comment = '<a href="#play" class="gocha-video-comment-anchor gvp-default-list" data-gocha-video-id="'.$commenttitle[0].'" data-gocha-start="'.$commenttitle[1].'" data-gocha-stop="'.$commenttitle[2].'">'.sprintf( $comment_text, $start_date, $end_date ).'</a>';
			$text = $comment . $text;
		}

        return $text;
	}

	/*
	 * Displays youtube player
	 */
	public function showplayer_yt($matches) {
		return $this->showplayer($matches, 'YouTube');
	}

	/*
	 * Displays vimeo player
	 */
	public function showplayer_vm($matches) {
        if(stripos($matches[0], 'data-gocha="true"') !== FALSE) {
            return $matches[0];
        }

		return $this->showplayer($matches, 'Vimeo');
	}

    /*
	 * Displays dailymotion player
	 */
	public function showplayer_dm($matches) {
        if(stripos($matches[0], 'data-gocha="true"') !== FALSE) {
            return $matches[0];
        }

		return $this->showplayer($matches, 'dailymotion');
	}

    /*
	 * Displays Facebook player
	 */
	public function showplayer_fb($matches) {
        if(stripos($matches[0], 'data-gocha="true"') !== FALSE) {
            return $matches[0];
        }

		return $this->showplayer($matches, 'FB');
	}

	/*
	 * Displays Google Drive player
	 */
	public function showplayer_gd($matches) {
        if(stripos($matches[0], 'data-gocha="true"') !== FALSE) {
            return $matches[0];
        }

		return $this->showplayer($matches, 'GoogleDrive');
	}

	/*
	 * Displays player
	 */
	private function showplayer($matches, $type, $output = null){
        switch ($type) {
		    case 'YouTube':
		        $player = '<div class="gocha-video-youtube" id="YT-' . $matches[1] . '-player" data-gocha-video="' . $matches[1] . '"></div>';
		        $customClass = 'type-youtube';
		        $commentclass = $type . '-' . $matches[1];
		        break;
		    case 'Vimeo':
		        $player = '<iframe data-gocha="true" src="//player.vimeo.com/video/' . $matches[1] . '" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" width="640" height="390" id="VM-' . $matches[1] . '-player" class="gocha-video-vimeo" data-gocha-video="'.$matches[1].'"></iframe>';
		        $customClass = 'type-vimeo';
		        $commentclass = $type . '-' . $matches[1];
		        break;
            case 'dailymotion':
                $player = '<iframe data-gocha="true" src="//www.dailymotion.com/embed/video/' . $matches[1] . '" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen="" width="640" height="390" id="DM-' . $matches[1] . '-player" class="gocha-video-dailymotion" data-gocha-video="'.$matches[1].'"></iframe>';
                $customClass = 'type-dailymotion';
                $commentclass = $type . '-' . $matches[1];
                break;
            case 'FB':
                $player = '<div data-gocha="true" class="fb-video gocha-video-fb" id="FB-' . $matches[1] . '-player" data-href="https://www.facebook.com/facebook/videos/' . $matches[1] . '" data-gocha-video="'.$matches[1].'" data-allowfullscreen="true"></div>';
                $customClass = 'type-fb';
                $commentclass = $type . '-' . $matches[1];
                break;
		    case 'GoogleDrive':
		        $player = '<video data-gocha="true" controls id="GD-' . $matches[1] . '-player" class="wp-video-shortcode" data-gocha-video="' . $matches[1] . '"><source src="https://drive.google.com/uc?export=download&id=' . $matches[1] . '" type="video/webm" /></video>';
		        $customClass = 'type-mediaelement';
		        $commentclass = $type . '-' . $matches[1];
		        break;
		    case 'MediaElement':
		        $player = $output;
                $customClass = 'type-mediaelement';

                if($output === null) {
                    $vidID = explode("/", $matches[1]);
                    $vidic = count($vidID) - 1;
                    $vidID = $vidID[$vidic-2].'-'.$vidID[$vidic - 1].'-'.$vidID[$vidic];
                    $vidID = str_replace(".","-",$vidID);

                    $player = '<video data-gocha="true" controls id="ME-' . $vidID . '-player" class="wp-video-shortcode" data-gocha-video="' . $vidID . '"><source src="' . $matches[1] . '" type="video/webm" /></video>';
                    $commentclass = $vidID;
                } else {
		            $commentclass = $matches[0];
                }

                break;
		}

		$comments_list = $this->load_comments_list($matches[1]);
		$comments_form = $this->load_comments_form();

        // We need to inform scripts that we are in the Theme Customizer
        if (is_customize_preview()) {
            $customClass .= ' is-customize-preview';
        }

		ob_start();
        include('views/comments.php');
        $output = ob_get_clean();

		return $output;
	}

	/*
	 * Display player
	 */
   	public function media_element($output, $atts, $video, $post_id, $library) {
		if(is_admin() || $this->options['hide_media_element'] == 1){
		   return $output;
		}

		if ('' != $atts['src']) {
			$vidID = $atts['src'];
		} elseif('' != $atts['mp4']) {
			$vidID = $atts['mp4'];
		} elseif('' != $atts['ogv']) {
			$vidID = $atts['ogv'];
		} elseif('' != $atts['webm']) {
			$vidID = $atts['webm'];
		} elseif('' != $atts['m4v']) {
			$vidID = $atts['m4v'];
		}

		$vidID = explode("/", $vidID);
		$vidic = count($vidID)-1;
		$vidID = $vidID[$vidic-2].'-'.$vidID[$vidic-1].'-'.$vidID[$vidic];
		$vidID = str_replace(".","-",$vidID);

		$matches[0] = '';
		$matches[1] = $vidID;

		return $this->showplayer($matches, 'MediaElement', $output);
	}

	/*
	 * Wrap video into HTML
	 */
   	public function videoautop($player_code) {
        global $post;

        if($this->options['include_mode'] === 'exclude') {
            if($this->options['excludedposts'] && in_array($post->ID, $this->options['excludedposts'])) {
                return $player_code;
            }
        } else {
            if($this->options['excludedposts'] && !in_array($post->ID, $this->options['excludedposts'])) {
                return $player_code;
            }
        }

        if($this->options['hide_youtube'] == 0) {
            $player_code = gocha_video_youtube_player($player_code, $this);
        }

        if($this->options['hide_vimeo'] == 0) {
    		$player_code = gocha_video_vimeo_player($player_code, $this);
        }

        if($this->options['hide_dailymotion'] == 0) {
    		$player_code = gocha_video_dailymotion_player($player_code, $this);
        }

        if($this->options['hide_fb'] == 0) {
    		$player_code = gocha_video_fb_player($player_code, $this);
        }

        if($this->options['hide_google_drive'] == 0) {
            $player_code = gocha_video_google_drive_player($player_code, $this);
        }

		return $player_code;
	}

    /*
	 * Shortcode generating
	 */
	public function shortcode($shortcode_atts) {
        $shortcode_atts = shortcode_atts(array(
            'url' => '',
            'mode' => $this->options['mode'],
            'parse_mode' => $this->options['parse_mode'],
            'mintimediff' => $this->options['mintimediff'],
            'hidecommentform' => $this->options['hidecommentform'],
    		'hidetimeline' => $this->options['hidetimeline'],
    	    'commentdisplay' => $this->options['commentdisplay'],
            'commentdisplaymode' => $this->options['commentdisplaymode'],
    		'commentopen' => $this->options['commentopen'],
    		'order' => $this->options['order']
		), $shortcode_atts, 'gocha_video');

        $service = $this->get_video_service_from_url($shortcode_atts['url']);
        $videoID = $this->get_video_id_from_url($shortcode_atts['url'], $service);
		$shortcode_atts = Gocha_Video_Sanitize::sanitize_shortcode_atts($shortcode_atts);

        // Copy settings temporary for the shortcode use
        $this->options_copy = $this->options;
        $this->options = $shortcode_atts;
        // Run the shortcode code
        $output = $this->showplayer(array(1 => $videoID), $service);
        // Restore the settings
        $this->options = $this->options_copy;

        if($shortcode_atts['hidecommentform'] === 'true' || $shortcode_atts['hidecommentform'] === '1') {
            wp_enqueue_style('gocha-video-plugin-hidden-form');
        }

		return apply_filters('gocha_video_output', $output);
	}

    /**
     * Function used to add CSS for the TinyMCE button
     */
    public function add_button_css() {
        wp_enqueue_style('gocha-video-tinymce-button', plugins_url('/css/admin-button.css', dirname(__FILE__)));
    }

    /**
     * Function used to get video ID from given URL
     */
    public function get_video_id_from_url($url, $service) {
        $matches = array();

        if($service === 'YouTube') {
            $regexp = '~https?:\/\/                 # Either http or https
                        (?:[\w]+\.)*                # Optional subdomains
                        (?:                         # Group host alternatives.
                        youtu\.be/                  # Either youtu.be,
                        | youtube\.com              # or youtube.com
                        | youtube-nocookie\.com     # or youtube-nocookie.com
                        )                           # End Host Group
                        (?:\S*[^\w\-\s])?           # Extra stuff up to VIDEO_ID
                        ([\w\-]{11})                # $1: VIDEO_ID is numeric
                        [^\s]*~ix';
            preg_match_all($regexp, $url, $matches);

            if(count($matches) > 1 && isset($matches[1]) && isset($matches[1][0])) {
                return $matches[1][0];
            }

            return false;
        }

        if($service === 'Vimeo') {
            $regexp = '~https?:\/\/             # Either http or https
                        (?:[\w]+\.)*            # Optional subdomains
                        vimeo\.com              # Match vimeo.com
                        (?:[\/\w]*\/videos?)?   # Optional video sub directory this handles groups links also
                        \/                      # Slash before Id
                        ([0-9]+)                # $1: VIDEO_ID is numeric
                        [^\s]*~ix';
            preg_match_all($regexp, $url, $matches);

            if(count($matches) > 1 && isset($matches[1]) && isset($matches[1][0])) {
                return $matches[1][0];
            }

            return false;
        }

        if($service === 'FB') {
            $regexp = '~https?:\/\/             # Either http or https
                        .*?                     # Optional subdomains
                        facebook\.com           # Match facebook.com
                        .*?
                        ([0-9]{12,24})          # $1: VIDEO_ID is numeric
                        .*?\/?~ix';
            preg_match_all($regexp, $url, $matches);

            if(count($matches) > 1 && isset($matches[1]) && isset($matches[1][0])) {
                return $matches[1][0];
            }

            return false;
        }

        if($service === 'GoogleDrive') {
            $regexp = '~https?:\/\/             # Either http or https
                        (?:[\w]+\.)*            # Optional subdomains
                        google\.com             # Match google.com
                        .*?                     # Added support for a third way of adding Google Drive Video
                        \/file\/d\/             # Slashes and strings before Id
                        ([0-9a-zA-Z]+)          # $1: VIDEO_ID is aplha-numeric
                        \/preview~ix';
            preg_match_all($regexp, $url, $matches);

            if(count($matches) > 1 && isset($matches[1]) && isset($matches[1][0])) {
                return $matches[1][0];
            }

            return false;
        }

        if($service === 'dailymotion') {
            $regexp = '~(?:https?:\/\/)*            # Either http or https
                        (?:\/\/)*                   # or protocol-less
                        (?:[\w]+\.)*                # Optional subdomains
                        dailymotion\.com            # Match dailymotion.com
                        \/embed\/video\/            # Slashes and strings before Id
                        ([0-9a-zA-Z]+)              # $1: VIDEO_ID is aplha-numeric
                        \?*                         # Params separator
                        .*~ix';
            preg_match_all($regexp, $url, $matches);

            if(count($matches) > 1 && isset($matches[1]) && isset($matches[1][0])) {
                return $matches[1][0];
            }

            // Dailymotion shortlinks
            $regexp = '~(?:https?:\/\/)*            # Either http or https
                        (?:\/\/)*                   # or protocol-less
                        (?:[\w]+\.)*                # Optional subdomains
                        dai\.ly                     # Match dai.ly
                        \/                          # Slashes and strings before Id
                        ([0-9a-zA-Z]+)              # $1: VIDEO_ID is aplha-numeric
                        \?*                         # Params separator
                        .*~ix';
            preg_match_all($regexp, $url, $matches);

            if(count($matches) > 1 && isset($matches[1]) && isset($matches[1][0])) {
                return $matches[1][0];
            }

            return false;
        }

        return $url;
    }

    /**
     * Function used to get video service from given URL
     */
    public function get_video_service_from_url($url) {
        if(
            stripos($url, 'youtube.com') !== FALSE ||
            stripos($url, 'youtu.be') !== FALSE ||
            stripos($url, 'youtube-nocookie.com') !== FALSE
        ) {
            return 'YouTube';
        }

        if(stripos($url, 'vimeo.com') !== FALSE) {
            return 'Vimeo';
        }

        if(stripos($url, 'google.com') !== FALSE) {
            return 'GoogleDrive';
        }

        if(stripos($url, 'dailymotion.com') !== FALSE) {
            return 'dailymotion';
        }

        if(stripos($url, 'dai.ly') !== FALSE) {
            return 'dailymotion';
        }

        if(stripos($url, 'facebook.com') !== FALSE) {
            return 'FB';
        }

        return 'MediaElement';
    }

    /*
     * Function used to hide comments form via CSS
     */
    public function hide_comments_form_via_css() {
        wp_register_style(
            'gocha-video-plugin-hidden-form',
            plugins_url( 'css/gocha-video-hidden-form.css', dirname(__FILE__)),
            '',
            self::VERSION
        );
    }
}
