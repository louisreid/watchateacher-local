<?php 
include 'ChromePhp.php';
	// ChromePhp::log(print_r($post_id));


/**
 * Back-end creation of new candidate post
 * @uses Advanced Custom Fields Pro
 */

add_filter('acf/pre_save_post' , 'do_pre_save_post' );
function do_pre_save_post( $post_id ) {

	// Bail if not logged in or not able to post
	if ( ! ( is_user_logged_in() || current_user_can('publish_posts') ) ) {
		return;
	}

	// check if this is to be a new post
	// if( $post_id != 'new' ) {
	// 	return $post_id;
	// }


	switch ($post_id) {
		case 'session':
			$unique = mktime();
			$session_id = 'session_'.$unique;

			$coach_tax = $_POST['acf']['field_58fb775b405b6'];
			$coach_tax = get_term($coach_tax,'coach');
			$coach_tax = $coach_tax->slug;

			// GET THE VALUE OF THE COACH TAXONOMY FIELD AND PASS IT INTO THE ACTUAL COACH TAXONOMY KK

			// Create a new post
			$post = array(
				'post_type'     => 'session', // Your post type ( post, page, custom post type )
				'post_status'   => 'publish', // (publish, draft, private, etc.)
				// 'post_title' => $post_id . rand(10000,99999),
				'post_title' => $unique,
				// 'post_content' => 'This is the content'
				// 'post_content' => $coach_username
				'post_content' => $coach_tax
			);

			// insert the post
			$post_id = wp_insert_post( $post );
			wp_set_post_terms( $post_id, $session_id, 'session_id' );
			wp_set_post_terms( $post_id, $coach_tax, 'coach' );

			// Save the fields to the post
			do_action( 'acf/save_post' , $post_id );

			return $post_id;

			break;
		
		case 'comment':
			$slug = basename(get_permalink());
			$term = 'session_'.$slug;
			$taxonomy = 'session_id';

			// Create a new post
			$post = array(
				'post_type'     => 'comment', // Your post type ( post, page, custom post type )
				'post_status'   => 'publish', // (publish, draft, private, etc.)
				'post_title' => $slug.'+'.mktime(),
				'post_content' => 'This is the content'
			);

			// insert the post
			$post_id = wp_insert_post( $post );
			wp_set_post_terms( $post_id, $term, $taxonomy );

			// Save the fields to the post
			do_action( 'acf/save_post' , $post_id );

			return $post_id;

			break;

		default:
			return $post_id;
			break;
	}


}
