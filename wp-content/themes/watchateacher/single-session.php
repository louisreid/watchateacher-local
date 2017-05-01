<?php

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
function tsm_deregister_admin_styles() {
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
          <?php while ( have_posts() ) : the_post(); ?>
          <div class="page-header">
            <?php 
            $rubric = get_field('pre_rubric'); 
           ?>
            <h1><?php echo $rubric->post_title ?>
            
          <small>Session between <?php echo get_the_author(); ?> and <?php $users = get_field('coach'); echo $users['nickname']; ?></small>
          </h1>
          </div>
          <?php 
            $term_list = wp_get_post_terms($post->ID, 'session_id', array("fields" => "all"));
            $term_list = (array) $term_list[0];
            $session_id = $term_list['slug'];
          ?>
          <div class="row">
            <div class="col-xs-12 col-sm-4">
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <b>Rubric Preview:</b>
                </div>
                <div class="panel-body">
                  <p>
                    <?php 
                      if ($rubric->post_excerpt){
                          echo $rubric->post_excerpt;
                      } else { 
                          echo 'This rubric has no excerpt';
                      }

                    ?>
                  </p>
                  <a href="<?php echo get_permalink($rubric->ID); ?>" class="btn btn-default">Full Rubric</a>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-sm-8">
              <?php
              $args = array(
                  'post_type' => 'comment',
                  'posts_per_page' => 10,
                  'tax_query' => array(
                          array (
                              'taxonomy' => 'session_id',
                              'field' => 'slug',
                              'terms' => $session_id 
                              )
                      )
                  );
              $the_query = new WP_Query( $args );

              if ( $the_query->have_posts() ) {
                  while ( $the_query -> have_posts() ) : $the_query -> the_post(); 
                      // get_template_part( 'content', 'page')

                      echo get_the_author().': '.get_field('text');
                      echo "<br>";
                      if (get_field('video_url')){
                          echo 'Convert this to GOCHA later:'.'[gocha_video url="'.get_field('video_url').'" commentdisplaymode="0"]';
                      } else {
                          echo 'No video here.';
                      };
                      echo '<hr>';

                  endwhile;
              } else {
                  echo "No comments found";
              }

            ?>
                <div class="panel panel-default">
                  <div class="panel-heading">Add comment</div>
                  <div class="panel-body">
                    <?php
                    // Bail if not logged in or able to post
                    if ( ! ( is_user_logged_in()|| current_user_can('publish_posts') ) ) {
                        echo '<p>You must be a registered author to post.</p>';
                        return;
                    }

                    $new_post = array(
                        'post_id'            => 'comment', // Create a new post
                        'field_groups'       => array(28), // Create post field group ID(s)
                        'form'               => true,
                        // 'return'             => '%post_url%', // Redirect to new post url
                        'html_before_fields' => '',
                        'html_after_fields'  => '',
                        'submit_value'       => 'Add comment',
                        'updated_message'    => 'Comment added!'
                    );
                    acf_form( $new_post );
                ?>
                      <?php endwhile; // end of the session loop. ?>
                  </div>
                </div>
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
