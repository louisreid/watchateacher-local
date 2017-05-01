<?php
/**
 *
 * Template Name: Sessions
 * 
 * @package understrap
 */

get_header(); ?>
  <div class="wrapper" id="page-wrapper">
    <div id="content" class="container">
      <div class="row">
        <div id="primary" class="col-md-12 content-area">
          <main id="main" class="site-main" role="main">
            <div class="row">
              <div class="col-sm-6 col-xs-12">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3>My coaching sessions</h3></div>
                  <div class="panel-body">
  
                    <?php
                      wp_reset_postdata();
                      $logged_in_user = wp_get_current_user();
                      $logged_in_user = $logged_in_user->nickname;
                      $args = array(
                          'post_type' => 'session',
                          'tax_query' => array(
                            array(
                              'taxonomy' => 'coach',
                              'field' => 'slug',
                              'terms' => $logged_in_user
                              )
                            )
                          // 'meta_key' => 'coach',
                          // 'meta_query' => array(
                          //     array(
                          //         'key' => 'coach_post',
                          //         // 'value' => $logged_in_user,
                          //         // 'value' => serialize('coach_test'),
                          //         // 'value' => '"'."coach_test".'"',
                          //         // 'compare' => 'IN',
                          //         // 'compare' => 'LIKE'
                          //     ),
                          // ),
                      );
                      // $uservar = wp_get_current_user();
                      // echo '<pre>';
                      // echo print_r($uservar);
                      // echo '</pre>';
                      // $args = array (
                      //   'post_type' => 'post',
                      //   'meta_query' => array (
                      //     array (
                      //         'key' => 'user',
                      //         // 'value' => $uservar,
                      //         'compare' => 'LIKE'
                      //       )
                      //     )
                      //   );
                      $the_query = new WP_Query( $args );

                      if ( $the_query -> have_posts() ) {?>
                      <div class="list-group">
                        <?php while ( $the_query -> have_posts() ) : $the_query -> the_post(); ?>
                        <a href="<?php echo get_permalink(); ?>" class="list-group-item">
                              <h4>
                              <?php //echo get_the_title(); ?>
                              <?php 
                              if (get_field("pre_rubric")){
                                $rubric = get_field("pre_rubric");
                                echo $rubric->post_title;
                              } elseif (get_field("custom_rubric")) {
                                $rubric = get_field("custom_rubric_title");
                                echo $rubric;
                              } else { 
                                echo "No rubric found";
                              }; ?>
                            </h4>
                              <p>Coach:
                                <?php $coach = wp_get_post_terms($post->ID, 'coach', array('fields' => 'all')); echo $coach[0]->name; ?> </p>
                          </a>
                        <?php endwhile;?>
                      </div>
                      <?php
                        } else {
                            echo "No sessions found";
                        }

                        wp_reset_postdata();

                        ?>
                        <hr>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-xs-12">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h3>My training sessions</h3></div>
                  <div class="panel-body">
                    <?php
                        $current_user = wp_get_current_user();
                        $args = array(
                            'post_type' => 'session',
                            'posts_per_page' => 10,
                            'author_name' => $current_user->user_login 
                        );
                        $the_query = new WP_Query( $args );

                        if ( $the_query -> have_posts() ) {?>
                      <div class="list-group">
                        <?php while ( $the_query -> have_posts() ) : $the_query -> the_post(); ?>
                        <a href="<?php echo get_permalink(); ?>" class="list-group-item">
                              <h4>
                              <?php //echo get_the_title(); ?>
                              <?php 
                              if (get_field("pre_rubric")){
                                $rubric = get_field("pre_rubric");
                                echo $rubric->post_title;
                              } elseif (get_field("custom_rubric")) {
                                $rubric = get_field("custom_rubric_title");
                                echo $rubric;
                              } else { 
                                echo "No rubric found";
                              }; ?>
                            </h4>
                              <p>Coach:
                                <?php $coach = wp_get_post_terms($post->ID, 'coach', array('fields' => 'all')); echo $coach[0]->name; ?> </p>
                          </a>
                        <?php endwhile;?>
                      </div>
                      <?php
                            } else {
                              echo "No sessions found";
                          }

                      wp_reset_postdata();

                        ?>
                        <hr>
                  </div>
                </div>
              </div>
            </div>
          </main>
          <!-- #main -->
        </div>
        <!-- #primary -->
      </div>
      <!-- .row -->
    </div>
    <!-- Container end -->
  </div>
  <!-- Wrapper end -->
  <?php get_footer(); ?>
