<?php

// hook into the init action
add_action( 'init', 'create_insurer_taxonomies', 10 );

// create taxonomy
function create_insurer_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Insurers', 'taxonomy general name', 'docdirect' ),
		'singular_name'     => _x( 'Insurer', 'taxonomy singular name', 'docdirect' ),
		'search_items'      => __( 'Search Insurers', 'docdirect' ),
		'all_items'         => __( 'All Insurers', 'docdirect' ),
		'parent_item'       => __( 'Parent Insurer', 'docdirect' ),
		'parent_item_colon' => __( 'Parent Insurer:', 'docdirect' ),
		'edit_item'         => __( 'Edit Insurer', 'docdirect' ),
		'update_item'       => __( 'Update Insurer', 'docdirect' ),
		'add_new_item'      => __( 'Add New Insurer', 'docdirect' ),
		'new_item_name'     => __( 'New Insurer Name', 'docdirect' ),
		'menu_name'         => __( 'Insurer', 'docdirect' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'meta_box_cb'       => false,
		'show_in_quick_edit'       => false,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'insurer' ),
	);

	register_taxonomy( 'insurer', array( 'directory_type' ), $args );

	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Group Label', 'taxonomy general name', 'docdirect' ),
		'singular_name'     => _x( 'Group Label', 'taxonomy singular name', 'docdirect' ),
		'search_items'      => __( 'Search Group Label', 'docdirect' ),
		'all_items'         => __( 'All Group Label', 'docdirect' ),
		'parent_item'       => __( 'Parent Group Label', 'docdirect' ),
		'parent_item_colon' => __( 'Parent Group Label:', 'docdirect' ),
		'edit_item'         => __( 'Edit Group Label', 'docdirect' ),
		'update_item'       => __( 'Update Group Label', 'docdirect' ),
		'add_new_item'      => __( 'Add New Group Label', 'docdirect' ),
		'new_item_name'     => __( 'New Group Label Name', 'docdirect' ),
		'menu_name'         => __( 'Group Label', 'docdirect' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'group_label' ),
	);

	register_taxonomy( 'group_label', array( 'directory_type' ), $args );

	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name', 'docdirect' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name', 'docdirect' ),
		'search_items'      => __( 'Search Category', 'docdirect' ),
		'all_items'         => __( 'All Category', 'docdirect' ),
		'parent_item'       => __( 'Parent Category', 'docdirect' ),
		'parent_item_colon' => __( 'Parent Category:', 'docdirect' ),
		'edit_item'         => __( 'Edit Category', 'docdirect' ),
		'update_item'       => __( 'Update Category', 'docdirect' ),
		'add_new_item'      => __( 'Add New Category', 'docdirect' ),
		'new_item_name'     => __( 'New Category Name', 'docdirect' ),
		'menu_name'         => __( 'Category', 'docdirect' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'sp_category' ),
	);

	register_taxonomy( 'sp_category', array( 'sp_articles' ), $args );

}

add_action( 'widgets_init', 'theme_slug_widgets_init', 99 );
function theme_slug_widgets_init() {
    register_sidebar( array(
        'name' => __( 'Ads Sidebar', 'docdirect' ),
        'id' => 'sidebar-ads',
        'description' => __( 'Ads.', 'docdirect' ),
        'before_widget' => '<div id="%1$s" class="tg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
    ) );

    /*register_sidebar( array(
        'name' => __( 'Blog Page Sidebar | Ads ', 'docdirect' ),
        'id' => 'sidebar-ads2',
        'description' => __( 'Ads.', 'docdirect' ),
        'before_widget' => '<div id="%1$s" class="tg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
    ) );*/

    register_sidebar( array(
        'name' => __( 'Medical Articles Sidebar', 'docdirect' ),
        'id' => 'sidebar-article',
        'description' => __( 'Ads.', 'docdirect' ),
        'before_widget' => '<div id="%1$s" class="tg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
    ) );
}

//add user meta when register -- premium user
function kt_update_user_meta($user_id) {

    $user_info = get_userdata( $user_id );
    $user_registered = $user_info->user_registered;
    // $value = strtotime($user_registered);
	$db_directory_type	 = get_user_meta( $user_id, 'directory_type', true);
    $terms = get_the_terms($db_directory_type, 'group_label');
    $list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
    $current_group_label_slug = $terms[0]->slug;
    if( in_array('company', $list_terms) ||
            in_array('medical-centre', $list_terms) ||
            in_array('hospital-type', $list_terms) ||
            in_array('scans-testing', $list_terms)
    ) {
		$val = get_option('company_trial_premium_days', true );
    }else {
		$val = get_option('trial_premium_days', true );
    }

	$membership_date	= strtotime("+".$val." days", strtotime($user_registered));
	$membership_date	= date('Y-m-d H:i:s', $membership_date);

	update_user_meta($user_id, 'user_featured', strtotime($membership_date));
	update_user_meta($user_id, 'user_premium', 'free_trial');

	update_user_meta($user_id, 'currency', 'HKD');
	update_user_meta($user_id, 'currency_symbol', '$');
	
	update_user_meta( $user_id, 'show_admin_bar_front', 'false' );

	$default_booking_services = array(
		'consultation' => array(
			'title' => 'Consultation',
			'price' => '100',
		)
	);
	update_user_meta( $user_id, 'booking_services', $default_booking_services );

}
add_action( 'user_register', 'kt_update_user_meta');

add_action('hook_author', 'kt_add_post_author');
function kt_add_post_author($post_id) {
	$author_id = get_post_field( 'post_author', $post_id );
	$author = get_userdata( $author_id );
	$avatar = apply_filters(
				'docdirect_get_user_avatar_filter',
				 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $author_id),
				 array('width'=>365,'height'=>365) //size width,height
			);
	$user_roles = $author->roles;

	$permalink = add_query_arg( 
			array(
				'doctor'=>  $author->user_login ,
				), esc_url( get_permalink(get_page_by_path( 'archive-article' )) 
			) 
		);
	?>
	<div class="author_info">
		<?php
		if( $user_roles[0] == 'administrator' ){?>
	        <img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $author->first_name.' '.$author->last_name );?>">
		<span class="name"><?php echo esc_attr( $author->first_name.' '.$author->last_name );?></span>
	    <?php }else {?>
			<a href="<?php echo get_author_posts_url($author_id); ?>"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $author->first_name.' '.$author->last_name );?>"></a>
			<span class="name"><a href="<?php echo $permalink; ?>"><?php echo kt_get_title_name($author->ID).esc_attr( $author->first_name.' '.$author->last_name );?></a></span>
	    <?php }?>
		<br />
		<span><?php echo get_the_time('d M Y g:i:A', $post_id);?></span>
	</div>
	<?php
}

add_action('widget-ads', 'kt_widget_ads');

function kt_widget_ads() {
	if( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-ads') ) : endif;
	// if( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-ads2') ) : endif;
}

// Enable shortcodes in text widgets
add_filter('widget_text','do_shortcode');

function custom_loginlogo() {
	echo '<style type="text/css">
	h1 a {background-image: url('.get_stylesheet_directory_uri().'/images/logo-login.png) !important;background-size: contain !important;
    width: 317px !important; }
	</style>';
}
add_action('login_head', 'custom_loginlogo');

add_action( 'after_setup_theme', 'kt_remove_parent_theme_search_filters', 5 );

function kt_remove_parent_theme_search_filters() {

	remove_action( 'docdirect_search_filters', 'docdirect_search_filters' );
	add_action( 'docdirect_search_filters', 'kt_docdirect_search_filters' );
	remove_action('docdirect_verify_user_account', 'docdirect_verify_user_account');
	add_filter( 'the_content', 'wpautop' );
	//change selec specialities backend
    remove_action('fw_option_types_init', '_action_theme_include_custom_option_types');
}

function kt_docdirect_theme_setup() {

        register_nav_menus(array(
			'top-menu'  => esc_attr('Header Top Menu', 'docdirect'),
        ));

	remove_action( 'init', 'docdirect_prepare_users_base' );
	remove_filter( 'author_rewrite_rules', 'wpse17106_author_rewrite_rules' );
	remove_action( 'docdirect_is_user_verified', 'docdirect_is_user_verified' );
	add_theme_support( 'title-tag' );

}

add_action('after_setup_theme', 'kt_docdirect_theme_setup');

//admin join
add_filter( 'posts_join', 'segnalazioni_search_join' );
function segnalazioni_search_join ( $join ) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni".
    if ( is_admin() && 'edit.php' === $pagenow && 'docappointments' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {    
        $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    return $join;
}

add_filter( 'posts_where', 'segnalazioni_search_where' );
function segnalazioni_search_where( $where ) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "segnalazioni".
    if ( is_admin() && 'edit.php' === $pagenow && 'docappointments' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)", $where );
    }
    return $where;
}
//group by post ID
add_filter( 'posts_groupby', function ($groupby, $query) {

    global $wpdb;
    if(is_search() && is_admin() && $_GET['post_type'] == 'docappointments')
    {
        $groupby = "{$wpdb->posts}.ID";
    }
    return $groupby;

}, 10, 2 );

add_filter('manage_docappointments_posts_columns', 'kt_appointments_columns_add');


function kt_update_user_affiliation($user_id) {

    $user_info = get_userdata( $user_id );
    $user_registered = $user_info->user_registered;
    $user_email = $user_info->user_email;
    
    $user_roles = $user_info->roles;
    // if( $user_roles[0] == 'professional' ){
	    $args = array(
	            'post_type' => 'affiliation',
	            'posts_per_page' => -1,
	                    'meta_query' => array(
	                        'relation' => 'AND',
	                        array(
	                            'key'     => 'email',
	                            'value'   => $user_email,
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

	    foreach($ListPost as $post) {
            update_post_meta($post->ID, 'user_to', $user_id);
            update_post_meta($post->ID, 'type_aff', 'in_db');
            update_post_meta($post->ID, 'aff_status', 'approved');
	    }
	// }

}
add_action( 'user_register', 'kt_update_user_affiliation');

function kt_custom_author_base(){
	$author_base = "doctor" ; //Your desired author base.
	global $wp_rewrite ;
	$wp_rewrite->author_base = $author_base ;
	$wp_rewrite->flush_rules() ;
}
add_action( 'init', 'kt_custom_author_base', 0 ) ;

/**
 * Enables the Excerpt meta box in Page edit screen.
 */
function wpcodex_add_excerpt_support_for_custom_post_type() {
	add_post_type_support( 'sp_articles', array('comments','excerpt') );
}
add_action( 'init', 'wpcodex_add_excerpt_support_for_custom_post_type' );

function add_author_caps() {
        $role = get_role( 'professional' );

        // author caps
		  $role->add_cap('edit_published_posts');
		  $role->add_cap('delete_posts');
		  $role->add_cap('upload_files');
		  // editor caps
		  $role->add_cap('edit_others_posts');
		  $role->add_cap('edit_published_pages');
          $role->add_cap( 'delete_attachments' ); //of course this wont work 

		  $role->add_cap('edit_post');

		  $role->remove_cap('edit_posts');
}
add_action( 'init', 'add_author_caps', 99);
add_action( 'admin_init', 'add_author_caps', 99);

add_filter('body_class','my_class_names');
function my_class_names($classes) {
    if (! ( is_user_logged_in() ) ) {
        $classes[] = 'logged-out';
    }
    return $classes;
}



