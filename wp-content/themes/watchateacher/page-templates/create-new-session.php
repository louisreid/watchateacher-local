<?php
/**
 * Template Name: Create New Session
 *
 * @package understrap
 */

/**
 * Add required acf_form_head() function to head of page
 * @uses Advanced Custom Fields Pro
 */
add_action( 'get_header', 'tsm_do_acf_form_head', 1 );
function tsm_do_acf_form_head() {
    // Bail if not logged in or not able to post
    if ( ! ( is_user_logged_in() || current_user_can('publish_posts') ) ) {
        return;
    }
    acf_form_head();
}

/**
 * Deregister the admin styles outputted when using acf_form
 */
add_action( 'wp_print_styles', 'tsm_deregister_admin_styles', 999 );
function tsm_deregister_admin_styles( $post_id ) {
    // Bail if not logged in or not able to post
    if ( ! ( is_user_logged_in() || current_user_can('publish_posts') ) ) {
        return;
    }
    wp_deregister_style( 'wp-admin' );
}

get_header(); ?>
  <div class="wrapper" id="page-wrapper">
    <div id="content" class="container">
      <div id="primary" class="col-md-12 content-area">
        <main id="main" class="site-main" role="main">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h1>Create a new coaching session</h1>
            </div>
            <div class="panel-body">
                <?php
                        // Bail if not logged in or able to post
                        if ( ! ( is_user_logged_in()|| current_user_can('publish_posts') ) ) {
                            echo '<p>You must be a registered author to post.</p>';
                            return;
                        }

                        $new_post = array(
                            'post_id'            => 'session', // Create a new post
                            'field_groups'       => array(471), // Create post field group ID(s)
                            'form'               => true,
                            'return'             => '%post_url%', // Redirect to new post url
                            'html_before_fields' => '',
                            'html_after_fields'  => '',
                            'submit_value'       => 'Submit',
                            'updated_message'    => 'Session added!'
                        );
                        acf_form( $new_post );
                    ?>
            </div>
          </div>
        </main>
        <!-- #main -->
      </div>
      <!-- #primary -->
    </div>
    <!-- Container end -->
  </div>
  <!-- Wrapper end -->
  <?php get_footer(); ?>
