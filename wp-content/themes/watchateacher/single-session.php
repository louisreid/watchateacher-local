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
            $custom_rubric_title = get_field('custom_rubric_title');
            $custom_rubric = get_field('custom_rubric');
            if ($rubric) {?>
            <h1><?php echo $rubric->post_title ?><br>
            <?php } elseif ($custom_rubric_title) {?>
            <h1><?php echo $custom_rubric_title ?><br>
            <?php } ?>
            
          <small>Session between <?php echo get_the_author(); ?> and <?php
            $users = get_field('coach'); 
            echo $users->name; 
          ?></small>
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
                  <b><?php echo do_shortcode('[fa class="fa-bullseye"]'); ?> Rubric</b>
                </div>
                <div class="panel-body">
                  <p>
                    <?php 
                      if ($rubric->post_content){
                          echo $rubric->post_content;
                      } elseif ($custom_rubric) {
                          echo $custom_rubric;
                      } else { 
                          echo 'This rubric has no excerpt';
                      }

                    ?>
                  </p>
                  <!-- <a href="<?php  
                  // echo get_permalink($rubric->ID); 
                  ?>" class="btn btn-default">Full Rubric</a>-->
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-sm-8">
              <?php
              $args = array(
                  'post_type' => 'comment',
                  'order' => 'ASC',
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
                     ?> <div class="bs-callout bs-callout-primary"> <?php
                      if (get_field('new_video_url')){
                          ?>
                            <h4>Video posted by: <?php $author = get_the_author_meta('display_name'); echo $author ?></h4>
                          <?php
                          echo do_shortcode('[gocha_video url="'.get_field('new_video_url').'"]');
                      } elseif (get_field('exemplary_video')) {
                          $selected_post = get_field('exemplary_video');
                          $selected_post_custom_fields = get_post_custom($selected_post->ID);
                          $selected_post_video_link = $selected_post_custom_fields[video_link][0];
                          echo do_shortcode('[gocha_video url="'.$selected_post_video_link.'"]');
                      } else {
                          ?>
                          <?php
                          echo get_the_author().': '.get_field('message');
                      };
                  ?>
                    </div>
                  <?php

                  endwhile;
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
                        'field_groups'       => array(465), // Create post field group ID(s)
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
  <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">How to add video</h4>
      </div>
      <div class="modal-body">
        <p>At present Watch A Teacher doesn't have the capability to upload video directly to the site.</p>
        <p>Please follow the steps below to add video privately using YouTube.</p>
        <ol>
          <li>Download the YouTube app (<a href="https://itunes.apple.com/gb/app/youtube-watch-upload-and-share-videos/id544007664?mt=8">iOS</a>, <a href="https://play.google.com/store/apps/details?id=com.google.android.youtube&hl=en_GB">Android</a>) on your smartphone</li>
          <li>Record and upload the video using either the camera or the YouTube app on your phone, set the privacy to <?php echo do_shortcode('[fa class="fa-link"]') ?> Unlisted.</li>
          <li>Paste the link to the uploaded video into the form on this page.</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>
  <?php get_footer(); ?>
