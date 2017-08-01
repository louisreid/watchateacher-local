<?php
/**
 * Template Name: Homepage
 *
 * @package understrap
 */
get_header(); ?>

    <header>
        <div class="container">
            <div class="row">
                <div class="col-sm-7">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>Watch A Teacher provides you with evidenced feedback on your teaching from coaches that teach your students.</h1>
                            <?php 
                                if (is_user_logged_in()) { 
                            ?>
                                    <a href="/create-new-session" class="btn btn-outline btn-xl">Create New Session</a>
                            <?php 
                                } else {
                            ?>
                                    <a href="<?php echo wp_login_url( $redirect ); ?>" class="btn btn-outline btn-xl">Login Now</a>
                            <?php 
                                };
                             ?>
                                    <a href="/demo" class="btn btn-outline btn-xl">See A Demo</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="device-container">
                        <div class="device-mockup iphone6_plus portrait white">
                            <div class="device">
                                <div class="screen">
                                    <!-- Demo image for screen mockup, you can put an image here, some HTML, an animation, video, or anything else! -->
                                    <!-- <img src="img/demo-screen-1.jpg" class="img-responsive" alt=""> -->
                                    <img src="wp-content/themes/watchateacher/img/screengrab-ipad.png" class="img-responsive img-responsive__device" alt="">
                                </div>
                                <div class="button">
                                    <!-- You can hook the "home button" to some JavaScript events or just remove it -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>


  <?php get_footer(); ?>

