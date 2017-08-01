<?php 


	// ALL THE CF7 FORM

	// DYNAMIC SELECT
	
	// If this file is called directly, abort.
	if (!defined('WPINC')) { die; }
	
	/*
			This is an example of adding a shortcode for dynamic select using functions and hooks
	*/
	
	function cf7_dynamic_select_do_example1($choices, $args=array()) {
		// this function returns and array of label => value pairs to be used in the select field

		$args = array (
		    'role' => 'Coach',
		    'order' => 'ASC',
		    'orderby' => 'display_name'
		    );
		// Create the WP_User_Query object
		$wp_user_query = new WP_User_Query($args);

		// Get the results
		$authors = $wp_user_query->get_results();

		$choices = array(
		  '-- Make a Selection --' => '',
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
		        $choices[$author_info->first_name.' '.$author_info->last_name] = $author_info->user_nicename;
		    }
		    echo '</ul>';
		} else {
		    echo 'No authors found';
		}

		return $choices;
	} // end function cf7_dynamic_select_do_example1
	add_filter('wpcf7_dynamic_select_example1', 'cf7_dynamic_select_do_example1', 10, 2);


	add_filter('cf7_2_post_filter-badger-badger_type','filter_badger',10,2);
	function filter_badger($value, $post_id){
	  //$value is the post field value to return, by default it is empty
	  //$post_id is the ID of the post to which the form values are being mapped to
	  $value = $value."Miriam".mktime();
	  return $value;
	};



	// 
	// EXAMPLE of using filters to change values
	// 
	// 
	add_filter('cf7_2_post_filter-chicken-title','filter_first',10,2);
	function filter_first($value, $post_id){
	  //$value is the post field value to return, by default it is empty
	  //$post_id is the ID of the post to which the form values are being mapped to
	  $value = "Miriam".mktime();
	  return $value;
	};
	add_filter('cf7_2_post_filter-chicken-slug','filter_slug',10,2);
	function filter_slug($value, $post_id){
	  //$value is the post field value to return, by default it is empty
	  //$post_id is the ID of the post to which the form values are being mapped to
	  $value = "Miriamslug";
	  return $value;
	};




	// add_filter('cf7_2_post_filter-quick-contact-age','filter_date_to_age',10,3);
	// function filter_date_to_age($value, $post_id, $form_data){
	//   //$value is the post field value to return, by default it is empty
	//   //$post_id is the ID of the post to which the form values are being mapped to
	//   // $form_data is the submitted form data as an array of field-name=>value pairs
	//   if(isset($form_data['hidden-title'])){
	//     //calculate the age
	//     $value = $form_data['hidden-title'].mktime();
	//   };
	//   return $value;
	// };



	// add_action( ' cf7_2_post_save-session ', 'f_save_post',10,3);

	// function f_save_post($cf7_key, $submitted_data, $submitted_files){}

	// add_filter( ' cf7_2_post_load-session ', 'f_load_post',10,5);
	// function f_load_post( $field_value_pairs, $cf7_key, $form_fields, $form_field_options, $cf7_post_id){
	//   //$form_field_options options set in the form field tags
	//   //$cf7_post_id the cf7 form id in case you need to load the form object
	//   foreach($form_fields as $field=>$type){
	//     $field_value_pairs[$field] = 'coach';//load your value
	//   }
	//   //if this is a saved draft form, you can set your mapped post id
	//   //it will be set as hidden field so you can map the (re)submission to the same post
	//   $field_value_pairs['map_post_id'] = $post_id;
	//   return $field_value_pairs;
	// }




	// 
	// REAL CODE for
	// 
	// 


	// Dynamic Select for Coach selection
	// 
	function cf7_dynamic_select_coach($choices, $args=array()) {
		// this function returns and array of label => value pairs to be used in the select field

		$args = array (
		    'role' => 'Coach',
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
		        $choices[$author_info->first_name.' '.$author_info->last_name] = $author_info->user_nicename;
		    }
		    echo '</ul>';
		} else {
		    echo 'No authors found';
		}

		return $choices;
	} // end function cf7_dynamic_select_coach
	add_filter('wpcf7_dynamic_select_coach', 'cf7_dynamic_select_coach', 10, 2);


	// Dynamic Select for Premade Rubric selection
	// 
	function cf7_dynamic_select_pre_rubric($choices, $args=array()) {
		// this function returns and array of label => value pairs to be used in the select field


		$choices = array();	
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'author'	   => '',
			'author_name'	   => '',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts_array = get_posts( $args ); 
    foreach($posts_array as $single_post){
      // echo '<pre>';
      // echo print_r($single_post->post_title);
      // echo '</pre>';
      $choices[$single_post->post_title] = $single_post->ID;
    }
		// get the id's of the posts with titles as labels


		return $choices;
	}
	add_filter('wpcf7_dynamic_select_pre_rubric','cf7_dynamic_select_pre_rubric',10,2);


