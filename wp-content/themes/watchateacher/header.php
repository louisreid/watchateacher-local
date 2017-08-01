<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package understrap
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="page" class="hfeed site wrap">

    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <!-- .navbar-toggle is used as the toggle for collapsed navbar content -->
<!--                  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
 -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                
                <a class="navbar-brand page-scroll header-logo" href="/">
                    <img src="/wp-content/themes/watchateacher/img/logo-sm-white.png" alt="">
                </a>
                
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

                             <!-- The WordPress Menu goes here -->
                            <?php 
                                if (is_user_logged_in()){
                                    wp_nav_menu(
                                            array(
                                                'theme_location' => 'primary',
                                                // 'container_class' => 'collapse navbar-collapse navbar-responsive-collapse',
                                                'menu_class' => 'nav navbar-nav',
                                                'fallback_cb' => '',
                                                'menu_id' => 'main-menu',
                                                'walker' => new wp_bootstrap_navwalker()
                                            )
                                    ); 
                                }
                            ?>
                            <ul class="nav navbar-nav navbar-right">
                                <?php 
                                    if (is_user_logged_in()){
                                        $user = wp_get_current_user();
                                        echo '<li><a href="'.bp_loggedin_user_domain().'">'.$user->user_firstname.' '.$user->user_lastname.'</a></li>';
                                    }
                                ?>
                                <li>
                                <?php 
                                    if (is_user_logged_in()) {
                                       echo '<a href="'.wp_logout_url().'">log out</a>';
                                    } else {
                                       // echo '<a href="'.wp_login_url().'">log in</a>';
                                       echo '<a href="'.get_site_url().'/login/">log in</a>';
                                   };
                                 ?>
                                </li>
                            </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>
    
            






