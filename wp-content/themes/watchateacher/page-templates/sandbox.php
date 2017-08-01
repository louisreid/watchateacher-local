<?php
/**
 * Template Name: Sandbox
 *
 * @package understrap
 */

 
  //response generation function
  $response = "";
 
  //function to generate response
  function my_contact_form_generate_response($type, $message){
 
    global $response;
 
    if($type == "success") $response = "<div class='success'>{$message}</div>";
    else $response = "<div class='error'>{$message}</div>";
 
  }
get_header(); ?>
  <div class="wrapper" id="page-wrapper">
    <div class="container">
      <div class="row">
        <h1>The sandbox - try to create a post</h1>
        <?php
        	// Form mapped to creating a new post type (Contact form 1's)
	        // echo do_shortcode('[cf7-form cf7key="contact-form-1"]') 
        ?>

        <!-- echo sandbox -->
        <?php 
            $args = array (
        'role' => 'Subscriber',
        'order' => 'ASC',
        'orderby' => 'display_name'
        );
    // Create the WP_User_Query object
    $wp_user_query = new WP_User_Query($args);

    // Get the results
    $authors = $wp_user_query->get_results();

    $choices = array(
      'Choose a coach...' => '',
      );
    // Check for results
    if (!empty($authors)) {
        echo '<ul>';
        // loop through each author
        foreach ($authors as $author)
        {
            // get all the user's data
            $author_info = get_userdata($author->ID);
            // fill up the choices array with key value pairs 
            echo '<li>';
            echo $author_info->user_nicename;
            echo '</li>';
            // $choices[$author_info->first_name.' '.$author_info->last_name] = $author_info->user_nicename;
        }
        echo '</ul>';
    } else {
        echo 'No authors found';
    }

         ?>




        <?php
	        // echo do_shortcode('[contact-form-7 id="498" title="Sessions2"]') 
        ?>
    </div>
  </div>
  <script></script>
  <?php get_footer(); ?>
