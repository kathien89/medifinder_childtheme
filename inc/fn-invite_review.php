<?php
// create post type
function kt_create_ir_post_type(){ 
    register_post_type('invite_review', array(
        'labels' => array(
            'name' => __('Invite', 'theme'),
            'singular_name' => __('Invite', 'theme'),
            'menu_name' => __('Invite', 'theme'),
            'add_new' => __('Add Invite', 'theme'),
            'add_new_item' => __('Add New Invite', 'theme'),
            'edit_item' => __('Edit Invite', 'theme'),
            'new_item' => __('New Invite', 'theme'),
            'view_item' => __('View Invite', 'theme'),
            'search_items' => __('Search Invite', 'theme'),
            'not_found' => __('No Invite found', 'theme'),
            'not_found_in_trash' => __('No Invite found in Trash', 'theme'),
        ),
		'capabilities'       => array( 'create_posts' => false ), //Hide add New Button
		// 'labels'			  => $labels,
		'description'         => esc_html__( '', 'docdirect_core' ),
		'public'              => true,
		'supports'            => array( 'title', 'thumbnail', 'custom-fields', 'author'),
		'show_ui'             => true,
		'capability_type'     => 'post',
		'show_in_nav_menus' => false, 
		'map_meta_cap'        => true,
		'publicly_queryable'  => true,
		'show_in_menu' => 'edit.php?post_type=directory_type',
		'exclude_from_search' => false,
		'hierarchical'        => false,
		'menu_position' 	  => 10,
		'rewrite'			  => array('slug' => 'invite_review', 'with_front' => true),
		'query_var'           => false,
		'has_archive'         => false,
    )); 
}
add_action( 'init', 'kt_create_ir_post_type' );



function kt_ajax_submit_invite_review(){
	global $current_user;

  	$patient_name = isset( $_POST['patient_name'] ) ? esc_sql( $_POST['patient_name'] ) : '';
  	$hkid = isset( $_POST['hkid'] ) ? esc_sql( $_POST['hkid'] ) : '';
  	$phone_number = isset( $_POST['phone_number'] ) ? esc_sql( $_POST['phone_number'] ) : '';
  	$headling_date = isset( $_POST['headling_date'] ) ? esc_sql( $_POST['headling_date'] ) : '';
  	$review_code = isset( $_POST['review_code'] ) ? esc_sql( $_POST['review_code'] ) : '';
  	$email_patient = isset( $_POST['email_patient'] ) ? esc_sql( $_POST['email_patient'] ) : '';
  	
  	if( $patient_name == '' || $hkid == '' || $phone_number == '' || $headling_date == '' || $review_code == '' || $email_patient == '' ){  	
		$json['type']		= 'error';
		$json['message']	= pll__('Please fill all fields.', 'docdirect');
		echo json_encode($json);
		die();
  	}
  	if ( !is_email( $email_patient ) ) {	
		$json['type']		= 'error';
		$json['message']	= pll__('Email address is invalid.', 'docdirect');
		echo json_encode($json);
		die();
  	}

	$user_identity	= $current_user->ID;

  	$val = kt_check_hkid($hkid, $user_identity);
  	if ($val != false) {
  		$phone_val = get_post_meta($val->ID, 'phone_number', true);
  		if ($phone_val != $phone_number) {
	  		$json['type']		= 'error';
			$json['message']	= pll__('Phone Number not match HKID.', 'docdirect');
			echo json_encode($json);
			die();
  		}
  	}

	$invite_post = array(
		'post_title'  => $patient_name,
		'post_status' => 'publish',
		'post_author' => $user_identity,
		'post_type'   => 'invite_review',
		'post_date'   => current_time('Y-m-d H:i:s')
	);
	
	$post_id = wp_insert_post( $invite_post );

	$invite_meta = array(
		'hkid' => $hkid,
		'phone_number' => $phone_number,
		'headling_date' => $headling_date,
		'review_code'=> $review_code,
		'email_patient'=> $email_patient,
		'status'=> 'pending',
	);

	//Update post meta
	foreach( $invite_meta as $key => $value ){
		update_post_meta($post_id,$key,$value);
	}

	$subject = 'Invite review';
	$desc = $review_code;
	kt_process_email_invite_review($email_patient, $subject, $desc);

	/*$list_invite = get_user_meta( $user_identity, 'invite_review', true );
	$list_invite = json_decode( $list_invite, true );
	// Get last id
	$last_item    = end($list_invite);
	$last_item_id = $last_item['id'];
	$new_invite = array(
		'id'  				=> ++$last_item_id,
		'patient_name' 		=> $patient_name, 
		'headling_date' 	=> $headling_date, 
		'review_code' 		=> $review_code, 
		'email_patient' 	=> $email_patient, 
		'status' 			=> 'pending', 
		);
	$list_invite[] = $new_invite;
	
	$subject = 'Invite review';
	$desc = $review_code;
	kt_process_email_invite_review($email_patient, $subject, $desc);

	$new_list = json_encode($list_invite);
	update_user_meta( $user_identity, 'invite_review', $new_list );*/
	
	$json['type']	 = 'success';
	$json['message']  = pll__('Create invite success','docdirect');
	echo json_encode($json);
	die;
  

}
add_action('wp_ajax_submit_invite_review', 'kt_ajax_submit_invite_review');
add_action('wp_ajax_nopriv_submit_invite_review', 'kt_ajax_submit_invite_review');

function kt_ajax_remove_invite(){
	global $current_user;
	$user_identity	= $current_user->ID;

  	$post_id = isset( $_POST['post_id'] ) ? esc_sql( $_POST['post_id'] ) : '';

	$post_author = get_post_field( 'post_author', $post_id );
	if ($post_author != $user_identity) {
		$json['type']		= 'error';
		$json['message']	= pll__('This invite not exists.', 'docdirect');
		echo json_encode($json);
		die();
	}else {
		wp_delete_post($post_id);
		$json['type']	 = 'success';
		$json['message']  = pll__('Remove Invite success','docdirect');
		echo json_encode($json);
		die;
	}

}
add_action('wp_ajax_remove_invite', 'kt_ajax_remove_invite');
add_action('wp_ajax_nopriv_remove_invite', 'kt_ajax_remove_invite');

function kt_check_hkid($hkid, $user_id) {

	$meta_query_args = array('relation' => 'AND',);
	$meta_query_args[] = array(
								'key'     => 'hkid',
								'value'   => $hkid,
								'compare'   => '='
							);
	/*$meta_query_args[] = array(
								'key'     => 'status',
								'value'   => 'pending',
								'compare'   => '='
							);*/
	$args 		= array('posts_per_page' => -1, 
						'author' => $user_id,
						'post_type' => 'invite_review', 
						'post_status' => 'publish', 
						'ignore_sticky_posts' => 1,
						);
	$args['meta_query'] = $meta_query_args;

	$query 		= new WP_Query( $args );
	$count_post = $query->post_count; 

	$count = 0;
	if ($count_post > 0) {
		return $query->posts[0];
	}else {
		return false;
	}

}
function kt_check_review_code_by_user($review_code, $user_id) {

	$meta_query_args = array('relation' => 'AND',);
	$meta_query_args[] = array(
								'key'     => 'review_code',
								'value'   => $review_code,
								'compare'   => '='
							);
	$meta_query_args[] = array(
								'key'     => 'status',
								'value'   => 'pending',
								'compare'   => '='
							);
	$args 		= array('posts_per_page' => -1, 
						'author' => $user_id,
						'post_type' => 'invite_review', 
						'post_status' => 'publish', 
						'ignore_sticky_posts' => 1,
						);
	$args['meta_query'] = $meta_query_args;

	$query 		= new WP_Query( $args );
	$count_post = $query->post_count; 

	$count = 0;
	if ($count_post > 0) {
		return $query->posts[0];
	}else {
		return false;
	}

}



