<?php
// create post type
function kt_create_affll_post_type(){ 
    register_post_type('affiliation', array(
        'labels' => array(
            'name' => __('Affiliation', 'theme'),
            'singular_name' => __('Affiliation', 'theme'),
            'menu_name' => __('Affiliation', 'theme'),
            'add_new' => __('Add Affiliation', 'theme'),
            'add_new_item' => __('Add New Affiliation', 'theme'),
            'edit_item' => __('Edit Affiliation', 'theme'),
            'new_item' => __('New Affiliation', 'theme'),
            'view_item' => __('View Affiliation', 'theme'),
            'search_items' => __('Search Affiliation', 'theme'),
            'not_found' => __('No Affiliation found', 'theme'),
            'not_found_in_trash' => __('No Affiliation found in Trash', 'theme'),
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
		'rewrite'			  => array('slug' => 'affiliation', 'with_front' => true),
		'query_var'           => false,
		'has_archive'         => false,
    )); 
}
add_action( 'init', 'kt_create_affll_post_type' );

function kt_ajax_search_user() {
	global $current_user, $wp_roles,$userdata,$post;

	if (isset($_POST['current_user_id'])) {
		$c_id = $_POST['current_user_id'];
	}else {
		$c_id = $current_user->ID;
	}

	// $apply_affiliation = get_user_meta( $c_id, 'apply_affiliation', true );
	$ex1 = array_keys(kt_getlist_affiliations($c_id, 'approved'));
	$ex2 = array_keys(kt_getlist_affiliations_request($c_id, 'approved'));
	$exclude = array_unique(array_merge($ex1, $ex2));

	$s = sanitize_text_field($_POST['search_string']);
	$directory_type = $_POST['type_category'];
	$exclude[] = $c_id;
	// $exclude = implode( ',', $exclude );

	if($s != '') {
		$meta_query_args = array();

	    // if (!isset($_POST['type'])) {
			$query_args	= array(
						'role'  => 'professional',
		                // 'meta_key' => 'user_featured',
		                // 'exclude' => array_values( $exclude )
						);
		// }
	    if ( !isset($_POST['type']) || isset($_POST['current_user_id']) ) {
	    	$query_args['exclude'] = array_values( $exclude );
	    }
		$search_args	= array(
							'search'         => '*'.esc_attr( $s ).'*',
							'search_columns' => array(
								'ID',
								'display_name',
								'user_login',
								'user_nicename',
								// 'user_email',
								// 'user_url',
							)
						);

		// $query_args	= array_merge( $query_args, $search_args );

		$meta_by_name = array('relation' => 'OR',);
		$meta_by_name[] = array(
								'key' 	   => 'first_name',
								'value' 	 => $s,
								'compare'   => 'LIKE',
							);
		
		$meta_by_name[] = array(
								'key' 	   => 'last_name',
								'value' 	 => $s,
								'compare'   => 'LIKE',
							);
		
		$meta_by_name[] = array(
								'key' 	   => 'nickname',
								'value' 	 => $s,
								'compare'   => 'LIKE',
							);
		
		$meta_by_name[] = array(
						'key' 	   => 'username',
						'value' 	 => $s,
						'compare'   => 'LIKE',
					);

		if( !empty( $meta_by_name ) ) {
			$meta_query_args[]	= array_merge( $meta_by_name,$meta_query_args );
		}

		if( isset( $directory_type ) && !empty( $directory_type ) ){
			$meta_query_args[] = array(
									'key' 	   => 'directory_type',
									'value' 	 => $directory_type,
									'compare'   => '=',
								);
		}	
	    /*$meta_query_args[] = array(
	                  'key'     => 'verify_user',
	                  'value'   => 'on',
	                  'compare' => '='
	                );*/

	    if (isset($_POST['type'])) {
	        $today = time();
	        $meta_query_args[] = array(
	                    'key'     => 'user_featured',
	                    'value'   => $today,
	                    'type' => 'numeric',
	                    'compare' => '>'
	                    );
	    }

		if( !empty( $meta_query_args ) ) {
			$query_relation = array('relation' => 'AND',);
			$meta_query_args	= array_merge( $query_relation,$meta_query_args );
			$query_args['meta_query'] = $meta_query_args;
		}

		$user_query  = new WP_User_Query($query_args);
		$output = '';
		if ( ! empty( $user_query->results ) ) {
			$output .= '<div class="wrap">';

			foreach ( $user_query->results as $user ) {
				ob_start();
					if (isset($_POST['type'])) {
			        	include( locate_template( 'inc/content-user-home.php' ) );
					} else {
			        	include( locate_template( 'inc/content-user.php' ) );
					}
					
			        $output .= ob_get_contents();                  
			    ob_end_clean(); 
			}
			$output .= '</div>';
			$output .= '<a class="close_response_wrap close_specialities_wrap" href="javascript:;">
	            	<i class="fa fa-close"></i>
	            	<span><?php esc_html_e(\'Close\',\'docdirect\'); ?></span>
	          	</a>';

		}else {

			$output .= 'not found';

		}
	}

	$json['data']	 = $output;
	$json['type']	 = 'success';
	$json['message']  = esc_html__('Success','docdirect');
	echo json_encode($json);
	die;
}

add_action('wp_ajax_search_user','kt_ajax_search_user');
add_action( 'wp_ajax_nopriv_search_user', 'kt_ajax_search_user' );

function kt_ajax_request_aff() {

	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;
	$type_group = $_POST['type_group'];
	$user_to = $_POST['user_to'];

	$count = kt_check_affiation($user_identity, $user_to);

	$user_to_data	= get_userdata($user_to);
	$user_to_name	   = $user_to_data->first_name.' '.$user_to_data->last_name;

	$old_data = get_user_meta( $user_to, 'request_affiliation', true );
	$old_data = json_decode($old_data);
	if ( $count > 0 ) {
	    $msg = "exists";

		$json['type']	 = 'error';
		$json['message']  = pll__($msg,'docdirect');
		echo json_encode($json);
		die;

	}else {
		/*$old_data[] = $current_user->ID;
		$new_request = json_encode($old_data);
		update_user_meta( $user_to, 'request_affiliation', $new_request );*/

		$args = array(
			'post_type' => 'affiliation',
            'posts_per_page' => -1,
			'post_title' => $user_to_name,
			'post_status' => 'publish',
			'post_author' => $user_identity,
			'tax_input' => array()
		);

		$post_id = wp_insert_post( $args );

		update_post_meta( $post_id, 'user_from', $user_identity );
		update_post_meta( $post_id, 'aff_status', 'pending' );
		update_post_meta( $post_id, 'user_to', $user_to );
		update_post_meta( $post_id, 'type_aff', 'in_db' );
		update_post_meta( $post_id, 'group_aff', $type_group );

		kt_process_email_request($user_to);
		ob_start();
	        include( locate_template( 'inc/content-user-remove.php' ) );
	        $output = ob_get_contents();                  
	    ob_end_clean(); 

		$json['output']	 = $output;
		$json['type']	 = 'success';
		$json['message']  = pll__('Success','docdirect');
		echo json_encode($json);
		die;
	}

}

add_action('wp_ajax_request_aff','kt_ajax_request_aff');
add_action( 'wp_ajax_nopriv_request_aff', 'kt_ajax_request_aff' );

function kt_ajax_remove_request_aff() {

	global $current_user, $wp_roles,$userdata,$post;
	$c_id = $current_user->ID;
	$post_id = $_POST['post_id'];

	wp_delete_post($post_id);

	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;
}

add_action('wp_ajax_remove_request_aff','kt_ajax_remove_request_aff');
add_action( 'wp_ajax_nopriv_remove_request_aff', 'kt_ajax_remove_request_aff' );

function kt_ajax_remove_approve_aff() {

	global $current_user, $wp_roles,$userdata,$post;
	$c_id = $current_user->ID;
	$post_id = $_POST['post_id'];

	wp_delete_post($post_id);

	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;
}

add_action('wp_ajax_remove_approve_aff','kt_ajax_remove_approve_aff');
add_action( 'wp_ajax_nopriv_remove_approve_aff', 'kt_ajax_remove_approve_aff' );

function kt_ajax_approve_aff() {

	global $current_user, $wp_roles,$userdata,$post;
	$c_id = $current_user->ID;
	$user_to = $_POST['user_to'];

	//remove position
	$old_data = get_user_meta( $c_id, 'request_affiliation', true );
	$old_data = json_decode($old_data);
	$pos = array_search($user_to, $old_data);
	if ($pos !== false) {
		unset($old_data[$pos]);
		$new_request = json_encode(array_values($old_data));
		update_user_meta( $c_id, 'request_affiliation', $new_request );  

		//update in current user
		$old_apply = get_user_meta( $c_id, 'apply_affiliation', true );
		$old_apply = json_decode($old_apply);
		if (in_array($user_to, $old_apply)) {
		    $message = "exists";
		}else {
			$old_apply[] = $user_to;
			$new_apply = json_encode($old_apply);
			update_user_meta( $c_id, 'apply_affiliation', $new_apply );  
		}
		//update in user to
		$to_old_apply = get_user_meta( $user_to, 'apply_affiliation', true );
		$to_old_apply = json_decode($to_old_apply);
		if (in_array($user_to, $to_old_apply)) {
		    $message = "exists";
		}else {
			$to_old_apply[] = $c_id;
			$to_new_apply = json_encode($to_old_apply);
			update_user_meta( $user_to, 'apply_affiliation', $to_new_apply );  
		}

	}
	

	$json = json_encode($new_apply);
	echo $json;
	die;
}

add_action('wp_ajax_approve_aff','kt_ajax_approve_aff');
add_action( 'wp_ajax_nopriv_approve_aff', 'kt_ajax_approve_aff' );

function kt_ajax_decline_aff() {

	global $current_user, $wp_roles,$userdata,$post;
	$c_id = $current_user->ID;
	$user_to = $_POST['user_to'];

	//remove position
	$old_data = get_user_meta( $c_id, 'request_affiliation', true );
	$old_data = json_decode($old_data);
	$pos = array_search($user_to, $old_data);
	if ($pos !== false) {
		unset($old_data[$pos]);
		$new_request = json_encode(array_values($old_data));
		update_user_meta( $c_id, 'request_affiliation', $new_request );  
	}
	

	$json = json_encode($new_request);
	echo $json;
	die;
}

add_action('wp_ajax_decline_aff','kt_ajax_decline_aff');
add_action( 'wp_ajax_nopriv_decline_afff', 'kt_ajax_decline_aff' );



function kt_ajax_submit_aff(){
	global $current_user;
	$user_identity	= $current_user->ID;

  	$post_title = isset( $_POST['post_title'] ) ? esc_sql( $_POST['post_title'] ) : ''; 
  	$tagline = isset( $_POST['tagline'] ) ? esc_sql( $_POST['tagline'] ) : ''; 
  	$email = isset( $_POST['email'] ) ? esc_sql( $_POST['email'] ) : ''; 
  	$specialties = isset( $_POST['specialties'] ) ? esc_sql( $_POST['specialties'] ) : ''; 
  	$group_aff = isset( $_POST['group_aff'] ) ? esc_sql( $_POST['group_aff'] ) : ''; 
  	$post_featured_image = isset( $_POST['post_featured_image'] ) ? esc_sql( $_POST['post_featured_image'] ) : ''; 

	if( $_POST['post_title'] != '' 
		&& $_POST['tagline'] != '' 
		&& $_POST['email'] != '' 
		&& $_POST['specialties'] != ''
		&& $_POST['group_aff'] != ''
		&& $_POST['post_featured_image'] != ''
	) {
		if( $_POST['email'] != '' ) {
			if(!is_email( $email )) {
				$json['type']	 = 'error';
				$json['message']  = pll__('Email not valid','docdirect');
				echo json_encode($json);
				die;
			}
			if ( email_exists( $email ) ) {
				$json['type']	 = 'error';
				$json['message']  = pll__('Email not exists in system','docdirect');
				echo json_encode($json);
				die;
			}
		}
		$args = array(
			'post_type' => 'affiliation',
			'post_title' => $post_title,
			// 'post_content' => $post_desc,
			'post_status' => 'publish',
			'post_author' => $user_identity,
			'tax_input' => array()
		);

		$post_id = wp_insert_post( $args );

		set_post_thumbnail( $post_id, $post_featured_image );

		update_post_meta( $post_id, 'user_from', $user_identity );
		update_post_meta( $post_id, 'type_aff', 'out_db' );
		update_post_meta( $post_id, 'tagline', $tagline );
		update_post_meta( $post_id, 'email', $email );
		update_post_meta( $post_id, 'specialties', $specialties );
		update_post_meta( $post_id, 'group_aff', $group_aff );

		$json['type']	 = 'success';
		$json['message']  = pll__('Success','docdirect');
		echo json_encode($json);
		die;
	}else {
		$json['type']	 = 'error';
		$json['message']  = pll__('Please fill all field','docdirect');
		echo json_encode($json);
		die;
	}
  	

}
add_action('wp_ajax_submit_aff', 'kt_ajax_submit_aff');
add_action('wp_ajax_nopriv_submit_aff', 'kt_ajax_submit_aff');

function kt_ajax_aff_action(){
	global $current_user;
	$user_identity	= $current_user->ID;

  	$post_id = isset( $_POST['post_id'] ) ? esc_sql( $_POST['post_id'] ) : '';
  	$type_action = isset( $_POST['type_action'] ) ? esc_sql( $_POST['type_action'] ) : '';

	if( $type_action == 'approve' ) {

		update_post_meta($post_id, 'aff_status', 'approved');

		$json['type']	 = 'success';
		$json['message']  = pll__('Success','docdirect');
		echo json_encode($json);
		die;
	}else {

		wp_delete_post($post_id);

		$json['type']	 = 'success';
		$json['message']  = pll__('Success','docdirect');
		echo json_encode($json);
		die;
	}
  	

}
add_action('wp_ajax_aff_action', 'kt_ajax_aff_action');
add_action('wp_ajax_nopriv_aff_action', 'kt_ajax_aff_action');

function kt_check_affiation( $user_from, $user_to ) {

	$user_aff = array(
		'posts_per_page'	=> "-1",
		'post_type'		 => 'affiliation',
		'post_status'	   => 'any',
		'author' 			=> $user_from,
		'meta_key'		  => 'user_to',
		'meta_value'		=> $user_to,
		'meta_compare'	  => "=",
		'orderby'		   => 'meta_value',
		'order'			 => 'ASC',
	);

	$affs_query = new WP_Query($user_aff);
	$aff_count = $affs_query->post_count;
	return $aff_count;

}

function kt_getlist_affiliations( $user_id, $status, $group_aff = '' ) {

	$user_aff = array(
		'posts_per_page'	=> "-1",
		'post_type'		 => 'affiliation',
		'post_status'	   => 'any',
		'author' 			=> $user_id,
		'order'			 => 'ASC',
	);

	$meta_query_args[] = array(
								'key'     => 'aff_status',
								'value'   => $status,
								'compare'   => '=',
							);
	$meta_query_args[] = array(
								'key'     => 'type_aff',
								'value'   => 'in_db',
								'compare'   => '=',
							);
	if ($group_aff != '') {
		$meta_query_args[] = array(
								'key'     => 'group_aff',
								'value'   => $group_aff,
								'compare'   => '=',
							);
	}

	if( !empty( $meta_query_args ) ) {
		$query_relation = array('relation' => 'AND',);
		$meta_query_args	= array_merge( $query_relation,$meta_query_args );
		$user_aff['meta_query'] = $meta_query_args;
	}

	$affs_query = new WP_Query($user_aff);
	$aff_count = $affs_query->post_count;
	$posts = $affs_query->posts;
	$list_users = array();
	foreach($posts as $post) {
	    $user_id = get_post_meta($post->ID, 'user_to', true);
	    // $list_users[] = $user_id;
	    $list_users[$user_id] = $post->ID;
	}
	return $list_users;
}

function kt_getlist_affiliations_request( $user_id, $status = 'pending', $group_aff = '' ) {

	$user_aff = array(
		'posts_per_page'	=> "-1",
		'post_type'		 => 'affiliation',
		'post_status'	   => 'any',
		'order'			 => 'ASC',
	);

	$meta_query_args[] = array(
								'key'     => 'user_to',
								'value'   => $user_id,
								'compare'   => '=',
							);
	$meta_query_args[] = array(
								'key'     => 'aff_status',
								'value'   => $status,
								'compare'   => '=',
							);
	if ($group_aff != '') {
		$meta_query_args[] = array(
								'key'     => 'group_aff',
								'value'   => $group_aff,
								'compare'   => '=',
							);
	}
	
	if( !empty( $meta_query_args ) ) {
		$query_relation = array('relation' => 'AND',);
		$meta_query_args	= array_merge( $query_relation,$meta_query_args );
		$user_aff['meta_query'] = $meta_query_args;
	}

	$affs_query = new WP_Query($user_aff);
	$aff_count = $affs_query->post_count;
	$posts = $affs_query->posts;
	$list_users = array();
	foreach($posts as $post) {
		$author_id = $post->post_author;
	    // $user_id = $post->ID;
	    $list_users[$author_id] = $post->ID;
	}
	return $list_users;
}

function get_apply_affiliation_by_user( $user_id, $group_aff ) {

    $list_exists = array();

    $args = array(
            'post_type' => 'affiliation',
            'posts_per_page' => -1,
            // 'author' => $user_id,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'user_from',
                            'value'   => $user_id,
                            'compare' => '='
                        ),
                        array(
                            'key'     => 'group_aff',
                            'value'   => $group_aff,
                            'compare' => '='
                        ),
                        array(
                            'key'     => 'type_aff',
                            'value'   => 'out_db',
                            'compare' => '='
                        )
                    )
        );
        $ListPost = get_posts($args);
        $apply_affiliation3 = array();
        $i = 0;
        foreach($ListPost as $post) {
            // var_dump($post->ID);
            $user_from = get_post_meta($post->ID, 'user_from', true);
            $key = $user_from.'_'.$i;
            $apply_affiliation3[$key] = $post->ID;
            $i++;
        }

    $apply_affiliation1 = kt_getlist_affiliations($user_id, 'approved', $group_aff);
    $apply_affiliation2 = kt_getlist_affiliations_request($user_id, 'approved', $group_aff);
    $apply_affiliation = $apply_affiliation1 + $apply_affiliation2 + $apply_affiliation3;

    return $apply_affiliation;
}


function kt_ajax_load_more_affiliation() {

	global $current_user, $wp_roles,$userdata,$post;
	$c_id = $current_user->ID;
	// $user_id = $_POST['user_id'];
  	$list_user = isset( $_POST['list_user'] ) ? $_POST['list_user'] : '';
  	$list_user = str_replace('\\','',$list_user);
	$apply_affiliation = json_decode($list_user, true);
	$length = $_POST['length'];
	$position = $length-1;

    $ppp = $_POST['ppp'];
    // $apply_affiliation = get_apply_affiliation_by_user($current_author_profile->ID,$group['slug']);
    $count = count($apply_affiliation);
    $apply_affiliation = array_slice($apply_affiliation, $length, $ppp, true);
    $data = '';
    if (!empty($apply_affiliation)) {
        foreach ($apply_affiliation as $user_id => $post_id) {
        	// $data .= $post_id;
		ob_start();
                            $type_aff = get_post_meta($post_id, 'type_aff', true);
                            $user_from = get_post_meta($post_id, 'user_from', true);
                            $user_to = get_post_meta($post_id, 'user_to', true);
                            if ($type_aff == 'in_db') {
                                // $user_id = $user_to; 
                                $user = get_userdata($user_id);
                                $review_data    = kt_docdirect_get_everage_rating ( $user->ID );
                                $name = $user->first_name.' '.$user->last_name;
                                $thumbnail = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                                $link = get_author_posts_url($user->ID);
                                $tagline = get_user_meta($user->ID, 'tagline', true);
                                $specialities = get_user_meta($user->ID,'user_profile_specialities',true);
                                if (!empty($specialities)) {
                                    $aloha = array_slice($specialities, 0, 4);
                                    $specialities_val = implode(', ', array_values($aloha));
                                }
                                $rating = docdirect_get_rating_stars($review_data,'return', 'hide');
                            }else {
                                $user_id = $user_from;
                                $width = '150';
                                $height = '150';
                                $thumb_id = get_post_thumbnail_id($post_id);
                                $thumb_url = wp_get_attachment_image_src($thumb_id, array($width, $height), true);
                                $thumbnail  = $thumb_url[0];
                                $link = 'javascript:;';
                                $name = get_the_title($post_id);
                                $tagline = get_post_meta($post_id, 'tagline', true);
                                $specialities_val = get_post_meta($post_id, 'specialties', true);
                                $rating = '';
                            }
                            // var_dump($user_id);
                            ?>
                            <article id="user-<?php echo intval( $user->ID );?>" data-post_id="<?php echo intval( $post_id );?>" class="col-sm-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <a href="<?php echo $link;?>" data-post_id="12<?php echo intval( $post_id );?>" class="list-avatar">                                        
                                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>">
                                            <?php
                                            if (get_post_meta($post_id, 'type_aff', true) == 'in_db') {
                                                echo '<span class="member_tag">'.pll__('Member').'</span>';
                                            }else {
                                                echo '<span class="invite_tag">'.pll__('Invite').'</span>';
                                            }
                                            ?>
                                        </a>
                                    </div>
                                    <div class="col-sm-8">
                                      
                                      <div class="tg-small">
                                        <h4><a href="<?php echo $link; ?>"><?php echo $name;?></a></h4>
                                      </div>
                                      <?php if( !empty( $tagline ) ){?>
                                          <div class="tg-tagline">
                                            <h5><?php echo $tagline;?></h5>
                                          </div>
                                      <?php }?>
                                      <?php if( !empty( $specialities_val ) ){?>
                                          <div class="tg-specialities">
                                            <p><strong><?php pll_e('Specialities: ');?></strong>
                                                <?php
                                                  echo $specialities_val;
                                                ?>
                                            </p>
                                          </div>
                                      <?php }?>
                                      <?php echo $rating;?>
                                    </div>
                                </div>             
                            </article>
                            <?php
	        $data .= ob_get_contents();                  
	    ob_end_clean(); 
        }
    }
    $end = 'false';
    if ($count <= intval($length+$ppp)) {
    	$end = 'true';
    }

	$json['end']	 = $end;
	$json['data']	 = $data;
	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;
}

add_action('wp_ajax_load_more_affiliation','kt_ajax_load_more_affiliation');
add_action( 'wp_ajax_nopriv_load_more_affiliation', 'kt_ajax_load_more_affiliation' );

function kt_ajax_change_title_group() {

	global $current_user, $wp_roles,$userdata,$post;
	$c_id = $current_user->ID;

  	$title_group = isset( $_POST['title_group'] ) ? $_POST['title_group'] : '';
  	$group_slug = isset( $_POST['group_slug'] ) ? $_POST['group_slug'] : '';
  	if ($title_group != '') {
  		update_user_meta($c_id, $group_slug, $title_group);
		$json['data']	 = $title_group;
  	}

	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;
}

add_action('wp_ajax_change_title_group','kt_ajax_change_title_group');
add_action( 'wp_ajax_nopriv_change_title_group', 'kt_ajax_change_title_group' );

function kt_ajax_edit_group() {

	global $current_user, $wp_roles,$userdata,$post;
	$c_id = $current_user->ID;

  	$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : '';
  	$type_group = isset( $_POST['type_group'] ) ? $_POST['type_group'] : '';
  	if ($post_id != '') {
  		update_post_meta($post_id, 'group_aff', $type_group);
  	}

	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;
}

add_action('wp_ajax_edit_group','kt_ajax_edit_group');
add_action( 'wp_ajax_nopriv_edit_group', 'kt_ajax_edit_group' );


function kt_admin_ajax_request_aff() {

	global $current_user, $wp_roles,$userdata,$post;
	// $user_identity	= $current_user->ID;
	$user_identity = $_POST['current_user_id'];
	$type_group = $_POST['type_group'];
	$user_to = $_POST['user_to'];

	$count = kt_check_affiation($user_identity, $user_to);

	$user_to_data	= get_userdata($user_to);
	$user_to_name	   = $user_to_data->first_name.' '.$user_to_data->last_name;

	$old_data = get_user_meta( $user_to, 'request_affiliation', true );
	$old_data = json_decode($old_data);
	if ( $count > 0 ) {
	    $msg = "exists";

		$json['type']	 = 'error';
		$json['message']  = pll__($msg,'docdirect');
		echo json_encode($json);
		die;

	}else {
		/*$old_data[] = $current_user->ID;
		$new_request = json_encode($old_data);
		update_user_meta( $user_to, 'request_affiliation', $new_request );*/

		$args = array(
			'post_type' => 'affiliation',
            'posts_per_page' => -1,
			'post_title' => $user_to_name,
			'post_status' => 'publish',
			'post_author' => $user_identity,
			'tax_input' => array()
		);

		$post_id = wp_insert_post( $args );

		update_post_meta( $post_id, 'user_from', $user_identity );
		update_post_meta( $post_id, 'aff_status', 'approved' );
		update_post_meta( $post_id, 'user_to', $user_to );
		update_post_meta( $post_id, 'type_aff', 'in_db' );
		update_post_meta( $post_id, 'group_aff', $type_group );

		kt_process_email_request($user_to);
		ob_start();
	        include( locate_template( 'inc/content-user-remove.php' ) );
	        $output = ob_get_contents();                  
	    ob_end_clean(); 

		$json['output']	 = $output;
		$json['type']	 = 'success';
		$json['message']  = pll__('Success','docdirect');
		echo json_encode($json);
		die;
	}

}

add_action('wp_ajax_admin_request_aff','kt_admin_ajax_request_aff');


