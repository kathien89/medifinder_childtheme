<?php
function am_enqueue_admin_styles(){

    wp_register_style( 'am_admin_bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css' );
    wp_register_script( 'admin_js', get_stylesheet_directory_uri() . '/js/admin_js.js' );
    wp_register_script( 'am_admin_bootstrap', get_template_directory_uri() . '/js/vendor/bootstrap.min.js' );

    /** @var \WP_Screen $screen */
    $screen = get_current_screen();

    // var_dump( $screen );
    if ( 'user-edit' == $screen->base || 'users' == $screen->base || $_GET['page'] == 'docdirect_plus' )
	    wp_enqueue_style( 'am_admin_bootstrap');
	    wp_enqueue_script( 'admin_js');
	    wp_enqueue_script( 'am_admin_bootstrap');

}

add_action( 'admin_enqueue_scripts', 'am_enqueue_admin_styles' );

function myplugin_settings() {
    require_once('fn-affiliation.php');
}
add_action( 'admin_init', 'myplugin_settings' );


//table
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

function my_add_menu_items(){
  	$hook = add_submenu_page( 'edit.php?post_type=directory_type', 'Manage Membership', 'Manage Membership', 'manage_options', 'dir_membership', 'my_render_list_page' );
  	add_action( "load-$hook", 'add_options' );
}
function add_options() {
	global $myListTable;
	$option = 'per_page';
	$args = array(
	     'label' => 'Users',
	     'default' => 10,
	     'option' => 'users_per_page'
	     );
	add_screen_option( $option, $args );
	$myListTable = new users_List();
}
add_action( 'admin_menu', 'my_add_menu_items' );
function my_render_list_page(){
	global $myListTable;
?>
	<div class="wrap">
		<h2>Membership</h2>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							<?php
							$myListTable->prepare_items();
							$myListTable->display(); ?>
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>
	<?php
}

class users_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __( 'user', 'sp' ), //singular name of the listed records
			'plural'   => __( 'users', 'sp' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );

	}

	/**
	 * Retrieve user’s data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_users( $per_page = 5, $page_number = 1 ) {

	  global $wpdb;

	  $sql = "SELECT * FROM {$wpdb->prefix}users";

	  if ( ! empty( $_REQUEST['orderby'] ) ) {
	    $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
	    $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
	  }

	  $sql .= " LIMIT $per_page";

	  $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


	  $result = $wpdb->get_results( $sql, 'ARRAY_A' );

	  return $result;
	}

	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
	  global $wpdb;

	  $wpdb->delete(
	    "{$wpdb->prefix}customers",
	    [ 'ID' => $id ],
	    [ '%d' ]
	  );
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
	  global $wpdb;

	  $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}users";

	  return $wpdb->get_var( $sql );
	}

	/** Text displayed when no user data is available */
	public function no_items() {
	  _e( 'No users avaliable.', 'sp' );
	}
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

	  // create a nonce
	  $delete_nonce = wp_create_nonce( 'sp_delete_user' );

	  $title = '<strong>' . $item['name'] . '</strong>';

	  $actions = [
	    'delete' => sprintf( '<a href="?page=%s&action=%s&user=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
	  ];

	  return $title . $this->row_actions( $actions );
	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
	  switch ( $column_name ) {
	    case 'username':
	      return $item[ $column_name ];
	    default:
	      return $item[ $column_name ]; //Show the whole array for troubleshooting purposes
	  }
	}
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
	  return sprintf(
	    '<input type="checkbox" name="bulk-edit[]" value="%s" />', $item['ID']
	  );
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
	  $columns = [
	    'cb'      => '<input type="checkbox" />',
	    'username'    => __( 'Username', 'sp' ),
	    'email'    => __( 'Email', 'sp' ),
	    'package' => __( 'Package', 'sp' ),
	    'payment_method' => __( 'Payment Method', 'sp' ),
	    'user_type' => __( 'User Type', 'sp' ),
	    'price' => __( 'Price', 'sp' ),
	    'payment_date' => __( 'Payment Date', 'sp' ),
	    'expiry_date'    => __( 'Expiry Date', 'sp' )
	  ];

	  return $columns;
	}

	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
	  $sortable_columns = array(
	    'username' => array( 'username', true ),
	    'expiry_date' => array( 'expiry_date', false )
	  );

	  return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
	  $actions = [
	    // 'bulk-delete' => 'Edit'
	  ];

	  return $actions;
	}	

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

	  // $this->_column_headers = $this->get_column_info();

	  /** Process bulk action */
	  /*$this->process_bulk_action();

	  $per_page     = $this->get_items_per_page( 'users_per_page', 5 );
	  $current_page = $this->get_pagenum();
	  $total_items  = self::record_count();

	  $this->set_pagination_args( [
	    'total_items' => $total_items, //WE have to calculate the total number of items
	    'per_page'    => $per_page //WE have to determine how many items to show on a page
	  ] );


	  $this->items = self::get_users( $per_page, $current_page );*/
				
		//Order
		$order	= 'DESC';
		if( isset( $_GET['order'] ) && !empty( $_GET['order'] ) ){
			$order	= $_GET['order'];
		}
		
		$sorting_order	= 'ID';
		if( $sort_by === 'recent' ){
			$sorting_order	= 'ID';
		} else if( $sort_by === 'recent' ){
			$sorting_order	= 'display_name';
		}
		$meta_query_args = array();
	  	$query_args	= array(
						'role'  => 'professional',
						'order' => $order,
						'orderby' => $sorting_order,
						//'number'    => $per_page,
						//'offset' => $offset,
					 );

	  	$query_args['meta_key']	  = 'user_featured';
		$query_args['orderby']	   = 'meta_value';	

		$meta_query_args[] = array(
							'key'     => 'user_featured',
							'compare' => 'EXISTS'
						);
        $today = current_time( 'timestamp' );
        $meta_query_args[] = array(
		                    'key'     => 'user_featured',
		                    'value'   => $today,
		                    'type' => 'numeric',
		                    'compare' => '>'
		                  );
				
		if( !empty( $meta_query_args ) ) {
			$query_relation = array('relation' => 'AND',);
			$meta_query_args	= array_merge( $query_relation,$meta_query_args );
			$query_args['meta_query'] = $meta_query_args;
		}
	    $example_data = array();
	    $data = array();
	    // $blogusers  = get_users( $meta_query_args );
		$user_query  = new WP_User_Query($query_args);

		if ( ! empty( $user_query->results  )  ){
		    foreach ( $user_query->results  as $user  ) {

		    $data[] = array(
	                'username'      =>  $user->user_login,
	                'email'         =>  $user->user_email,
	                'package'       => get_current_package($user->ID),
	                'payment_method'       => get_payment_method($user->ID),
	                'user_type'     => get_user_type($user->ID),
	                'price'     	=> get_current_package_price($user->ID),
	                'payment_date'     	=> get_payment_date($user->ID),
	                'expiry_date'   => get_expiry_date($user->ID)
	                );

		    }
		}
	    $example_data = $data;
	    
		$columns = $this->get_columns();
	    $hidden = array();
	    $sortable = $this->get_sortable_columns();
	    $this->_column_headers = array($columns, $hidden, $sortable);
	    $this->process_bulk_action();
	    $data = $example_data;

	  	$per_page     = $this->get_items_per_page( 'users_per_page', 5 );

	    function usort_reorder($a,$b){
	        $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'username'; //If no sort, default to username
	        $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
	        $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
	        return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
	    }
	    usort($data, 'usort_reorder');

	    $current_page = $this->get_pagenum();
	    $total_items = count($data);
	    $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

	    $this->items = $data;


	    $this->set_pagination_args( array(
	        'total_items' => $total_items,                  //WE have to calculate the total number of items
	        'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
	        'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
	    ) );


	}

	public function process_bulk_action() {

	  //Detect when a bulk action is being triggered...
	  if ( 'delete' === $this->current_action() ) {

	    // In our file that handles the request, verify the nonce.
	    $nonce = esc_attr( $_REQUEST['_wpnonce'] );

	    if ( ! wp_verify_nonce( $nonce, 'sp_delete_user' ) ) {
	      die( 'Go get a life script kiddies' );
	    }
	    else {
	      self::delete_user( absint( $_GET['user'] ) );

	      wp_redirect( esc_url( add_query_arg() ) );
	      exit;
	    }

	  }

	  // If the delete bulk action is triggered
	  if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
	       || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
	  ) {

	    $delete_ids = esc_sql( $_POST['bulk-delete'] );

	    // loop over the array of record IDs and delete them
	    foreach ( $delete_ids as $id ) {
	      self::delete_user( $id );

	    }

	    wp_redirect( esc_url( add_query_arg() ) );
	    exit;
	  }
	}


}

function get_expiry_date($user_id) {
	$user_featured = get_user_meta($user_id, 'user_featured', true);
	return date('Y-m-d H:i:A', $user_featured);
}

function get_current_package($user_id) {
	$user_current_membership = get_user_meta($user_id, 'user_current_membership', true);
	return get_the_title($user_current_membership);
}

function get_current_package_price($user_id) {
	$currency_sign = fw_get_db_settings_option('currency_sign');
	$user_current_membership = get_user_meta($user_id, 'user_current_membership', true);
	$price = fw_get_db_post_option($user_current_membership, 'price', true);
	if ($price == '1') {
		return false;
	}else{
		return esc_attr( $currency_sign ).fw_get_db_post_option($user_current_membership, 'price', true);
	}
}

function get_payment_date($user_id) {

	$args = array( 
			'posts_per_page' => 1, 
			'post_type' => 'docdirectinvoices',
			'order'=> 'ASC', 
			'orderby' => 'date',
			'meta_query' => array(
				array('relation' => 'AND',),
				array(
					'key' => 'user_identity',
					'value' => $user_id,
				),
				array(
					'key' => 'package_type',
					'value' => 'membership',
				)
			)
		);

	$myposts = get_posts( $args );
	$purchase_on = fw_get_db_post_option($myposts[0]->ID, 'purchase_on', true);
	if ($purchase_on == '1') {
		return false;
	}else{
		return date('Y-m-d H:i:A', strtotime($purchase_on));
	}
}
function get_payment_method($user_id) {

	$args = array( 
			'posts_per_page' => 1, 
			'post_type' => 'docdirectinvoices',
			'order'=> 'ASC', 
			'orderby' => 'date',
			'meta_query' => array(
				array(
					'key' => 'user_identity',
					'value' => $user_id,
				)
			)
		);

	$myposts = get_posts( $args );
	// return print_r( $myposts[0]->ID, true ) ;
	// return ucwords(fw_get_db_post_option($myposts[0]->ID, 'payment_method', true));
	return esc_attr( docdirect_prepare_payment_type( 'value', fw_get_db_post_option($myposts[0]->ID, 'payment_method', true) ) );
}

function get_user_type($user_id) {
	$directory_type = get_user_meta($user_id, 'directory_type', true);
	return get_the_title($directory_type);
}




/**
add meta box package
*
*/

add_action( 'add_meta_boxes', 'kt_metabox_package_type' );

function kt_metabox_package_type($post){
    add_meta_box('kt_metabox_package_type', 'Package Type', 'kt_metabox_package_type_output', 'directory_packages', 'side' , 'default');
}

add_action('save_post', 'kt_metabox_package_save_metabox');

function kt_metabox_package_save_metabox(){ 
    global $post;
    if(isset($_POST["package_type_select_box"])){
         //UPDATE: 
        $meta_element_class = $_POST['package_type_select_box'];
        $medi_package = $_POST['medi_package_select_box'];
        //END OF UPDATE

        update_post_meta($post->ID, 'package_type', $meta_element_class);
        update_post_meta($post->ID, 'medi_package', $medi_package);
        //print_r($_POST);
    }
}

function kt_metabox_package_type_output($post){
    $meta_element_class = get_post_meta($post->ID, 'package_type', true); //true ensures you get just one value instead of an array
    $medi_package = get_post_meta($post->ID, 'medi_package', true); //true ensures you get just one value instead of an array
    ?>   
    <label>Type :</label>

    <select name="package_type_select_box" id="package_type_select_box">
    	<?php
    		$variable = array(
    				'standard' => 'Standard',
    				'premium' => 'Premium'
    			);
    		foreach ($variable as $key => $value) {
    			?>
      				<option value="<?php echo esc_attr($key); ?>" <?php selected( $meta_element_class, $key ); ?>><?php echo esc_html($value); ?></option>
    			<?php
    		}
    	?>
    </select>
    <br>
    <label>Company/Profestional :</label>

    <select name="medi_package_select_box" id="medi_package_select_box">
    	<?php
    		$variable = array(
    				'profestional' => 'Profestional',
    				'company' => 'Company',
    				'private_hospital' => 'Private Hospital',
    			);
    		foreach ($variable as $key => $value) {
    			?>
      				<option value="<?php echo esc_attr($key); ?>" <?php selected( $medi_package, $key ); ?>><?php echo esc_html($value); ?></option>
    			<?php
    		}
    	?>
    </select>
    <?php
}

// unregister all parent theme Widgets
function kt_unregister_default_wp_widgets() {
    unregister_widget('TG_FEATURED_USERS');
}
add_action('widgets_init', 'kt_unregister_default_wp_widgets', 11);

require_once ( STYLESHEETPATH. '/inc/rewrite-widget.php'); //rewrite widget


//Manage Your Media Only
add_filter( 'ajax_query_attachments_args', 'kt_show_current_user_attachments' );

function kt_show_current_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id ) {
        $query['author'] = $user_id;
    }
    return $query;
}

function add_theme_caps() {
    // gets the author role
    $role = get_role( 'professional' );

    // This only works, because it accesses the class instance.
    // would allow the author to edit others' posts for current theme only
    $role->remove_cap( 'edit_published_posts' ); 
    $role->add_cap( 'upload_files' ); 
    $role->add_cap( 'publish_posts' );
    $role->remove_cap( 'delete_published_posts' ); 
    $role->add_cap( 'edit_posts' ); 
    $role->remove_cap( 'delete_posts' ); 
}
add_action( 'admin_init', 'add_theme_caps');

function kt_get_status_membership($user_id) {

    $user_info = get_userdata( $user_id );
    $user_roles = $user_info->roles;
    if( $user_roles[0] == 'professional' ){
		$user_premium = get_user_meta($user_id, 'user_premium', true);
		$today = current_time( 'timestamp' );
		$user_featured = get_user_meta($user_id, 'user_featured', true);
		if ($user_premium == 'free_trial' && $user_featured < $today) {
			$txt = 'Trial Ended';
			$cls = 'btn-warning';
		}else if ($user_premium == 'free_trial' && $user_featured >= $today) {
			$txt = 'Free Trial';
			$cls = 'btn-danger';
		}else if ($user_premium != '') {
			if ($user_featured > $today) {
				$txt = ucwords(str_replace('_', ' ', $user_premium));
				$cls = ($user_premium != 'premium') ? 'btn-primary' : 'btn-info' ;
			}else {
				$txt = 'Unpaid';
				$cls = 'btn-danger';
			}
		}else {
			if ($user_featured > $today) {
				$txt = 'Standard';
				$cls = 'btn-primary';
			}else {
				$txt = 'Unsigned';
				$cls = 'btn-default';
			}
		}
		$left = $user_featured - $today;
		$remaining_days = ceil(max($left, 0)/86400);
		return '<a class="btn '.$cls.'" href="javascript:;">'.$txt.'</a><br><span>'.$remaining_days.' days left</span>';
	}

}

//Add column to user table
function new_modify_user_table( $column ) {
	unset($column['posts']);
    $column['type_membership'] = 'Membership';
    $column['type_doc'] = 'Type';
    $column['posts'] = 'Posts';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'type_membership' :
            return kt_get_status_membership($user_id);
            break;
        case 'type_doc' :
			$db_directory_type	 = get_user_meta( $user_id, 'directory_type', true);
		    $terms = get_the_terms($db_directory_type, 'group_label');
		    $list_terms = array();
		    foreach ($terms as $key => $value) {
		        $list_terms[] = $value->slug;
		    }
		    $current_group_label_slug = $terms[0]->slug;
		    $user_premium = get_user_meta($current_author_profile->ID , 'user_premium' , true);
		    if( in_array('company', $list_terms) ||
		            in_array('medical-centre', $list_terms) ||
		            in_array('hospital-type', $list_terms) ||
		            in_array('scans-testing', $list_terms)
		    ) {
		    	$vl = 'Company';
		    }else {
		    	$vl = 'Individual';
		    }
            return $vl;
            break;
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 15, 3 );

function kt_add_section_filter() {
    if ( isset( $_GET[ 'membership' ]) ) {
        $current_membership = $_GET[ 'membership' ][0];
    }
    $arr_membership = array(
    					'standard'=> 'Standard',
    					'premium'=> 'Premium',
    					'free_trial'=> 'Free Trial',
    					'unpaid'=> 'Unpaid',
    					'trial_ended'=> 'Trial Ended',
				    );

    echo ' <select name="membership[]" style="float:none;">
    <option value="">Membership Filter</option>';

    foreach ($arr_membership as $key => $value) {
        $selected = $key == $current_membership ? ' selected="selected"' : '';
        echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
    }
    echo '</select>';
    echo '<input id="post-query-submit" class="button" type="submit" value="Filter" name="">';
}
add_action( 'restrict_manage_users', 'kt_add_section_filter', 1 );

function kt_filter_users_by_membership_section( $query ) {
    global $pagenow;

    if ( is_admin() && 
         'users.php' == $pagenow && 
         isset( $_GET[ 'membership' ] )&& 
         $_GET[ 'membership' ][0] != ''
        ) {
        $section = $_GET[ 'membership' ][0];
        // var_dump($section);
		$today = current_time( 'timestamp' );
    	if ($_GET[ 'membership' ][0] == 'standard') {
    		
			$query_relation = array('relation' => 'AND',);
			$featured_args	= array();
			$featured_args[] = array(
									'key'     => 'user_featured',								
									'value' 	 => $today,
									'compare' => '>'	
								);
			
			$featured_args[] = array(
									'key'     => 'user_premium',
									'compare' => 'NOT EXISTS'
								);
			// $meta_query	= array_merge( $query_relation,$featured_args );
	        $meta_query = array(
	        	'relation' => 'AND',
	            array(
	                'key' => 'user_featured',
	                'value' => $today,
					'compare' => '>'	
	            ),
	            array(
	                'key' => 'user_premium',
					'compare' => 'NOT EXISTS'
	            )
	        );
    	}else if($_GET[ 'membership' ][0] == 'unpaid') {
	        $meta_query = array(
	        	'relation' => 'AND',
	            array(
	                'key' => 'user_featured',
	                'value' => $today,
					'compare' => '<'	
	            ),
	            array(
	                'key' => 'user_premium',
					'value' => 'free_trial',
					'compare' => '!='	
	            )
	        );
    	}else if($_GET[ 'membership' ][0] == 'trial_ended') {
	        $meta_query = array(
	        	'relation' => 'AND',
	            array(
	                'key' => 'user_featured',
	                'value' => $today,
					'compare' => '<'	
	            ),
	            array(
	                'key' => 'user_premium',
					'value' => 'free_trial',
					'compare' => '='	
	            )
	        );
    	}else {
	        $meta_query = array(
	            array(
	                'key' => 'user_premium',
	                'value' => $_GET[ 'membership' ][0]
	            )
	        );
        	$query->set( 'meta_key', 'user_premium' );
    	}
        $query->set( 'meta_query', $meta_query );
        $query->set( 'role', 'professional' );
    }
}
add_filter( 'pre_get_users', 'kt_filter_users_by_membership_section' );
/*
add_action( 'in_admin_footer', function() {
?>
<script type="text/javascript">
    var el = jQuery("[name='membership']");
    el.change(function() {
        el.val(jQuery(this).val());
    });
</script>
<?php
} );
*/
//debug query
// add_action('pre_user_query', 'my_user_query');
function my_user_query($userquery){
	echo '<pre>';
    print_r($userquery);
    die();
}


function my_add_menu_items1(){
	// add_menu_page('directory_plus', 'doc plus', 'doc plus', 'manage_options', 'dir_trial_membership', 'my_render_list_page1' );
	add_menu_page('Docdirect Plus', 'Docdirect Plus', 'manage_options', 'docdirect_plus', 'kt_render_list_feature', '', 11);
	add_submenu_page( 'docdirect_plus', 'Trial premium', 'Trial premium', 'manage_options', 'dir_trial_membership', 'my_render_list_page1');
  	// $hook = add_submenu_page( 'edit.php?post_type=directory_type', 'Trial premium', 'Trial premium', 'manage_options', 'dir_trial_membership', 'my_render_list_page1' );
  	// add_action( "load-$hook", 'add_options' );
}

add_action( 'admin_menu', 'my_add_menu_items1' );
function my_render_list_page1(){
	global $myListTable;
?>
	<div class="wrap">
		<h2>Set day trial premium</h2>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<?php
							if (isset($_POST['submit'])) {
								// var_dump($_POST);
								// echo intval($_POST['number_day']);
								update_option( 'trial_premium_days', intval($_POST['number_day']) );
								update_option( 'company_trial_premium_days', intval($_POST['company_number_day']) );
							}
							$val = get_option('trial_premium_days', true );
							$company_val = get_option('company_trial_premium_days', true );
						?>
						<form method="post">
                			<div class="form-group">  
								<label>Profestional</label>
								<input type="text" name="number_day" placeholder="180" value="<?php echo $val;?>"><span>days</span>
							</div>
                			<div class="form-group">  
								<label>Company</label>
								<input type="text" name="company_number_day" placeholder="180" value="<?php echo $company_val;?>"><span>days</span>
							</div>
							<input class="button-primary button-large" type="submit" name="submit">
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>
	<?php
}

add_action('admin_menu', 'wpse149688');
function wpse149688(){
    add_menu_page( 'Wholesale Pricing', 'Wholesale', 'manage_options', 'woo-wholesale', 'woo_wholesale_page_call');
    add_submenu_page( 'woo-wholesale', 'Registrations', 'Registrations', 'manage_options', 'woo-wholesale-registrations', 'wwpr_page_call' ); 
    add_submenu_page('edit.php?post_type=directory_type', __('Test Settings','menu-test'), __('Test Settings','menu-test'), 'manage_options', 'testsettings', 'mt_settings_page');
}

function kt_add_menu_feature(){
  	$hook = add_submenu_page( 'edit.php?post_type=directory_type', 'Feature premium', 'Feature premium', 'manage_options', 'dir_feature', 'kt_render_list_feature' );
  	add_action( "load-$hook", 'add_options' );
}

add_action( 'admin_menu', 'kt_add_menu_feature' );
function kt_render_list_feature(){
	// global $myListTable;
?>
<div class="poststuff" style="margin-right: 15px;">
<div class="row">
	<?php
		$com_arr = array('company_premium', 'company_standard', 'company_free_trial');
		$ft_arr = array('premium', 'standard', 'free_trial');
		if (isset($_POST['submit'])) {
			foreach ($ft_arr as $key => $value) {
				/*echo '<pre>';
				var_dump($_POST[$value]);
				echo '</pre>';*/
				update_option( $value, $_POST[$value] );
			}	
			foreach ($com_arr as $key => $value) {
				/*echo '<pre>';
				var_dump($_POST[$value]);
				echo '</pre>';*/
				update_option( $value, $_POST[$value] );
			}		
		}
	?>
	<form method="post">
		<h2>Company</h2>
	<?php
	foreach ($com_arr as $key => $value) {?>
		<div class="col-md-4">
			<h3>Set Features <?php echo ucwords(str_replace('_', ' ', $value));?></h3>
			<?php
				$myprefix = $value;
				$current_option = get_option( $value, true );
				// var_dump($current_option);
				$options = array (

					array( 	
						"name" => "Photo Gallery",
						"id" => "photo_gallery",
						"type" => "checkbox",
						"std" => ""
					),
					array( 	
						"name" => "Number of Photos",
						"id" => "photo_number",
						"type" => "text",
						"std" => "3"
					),
					array( 	
						"name" => "Video Gallery",
						"id" => "video_gallery",
						"type" => "checkbox",
						"std" => ""
					),
					array( 	
						"name" => "Number of Videos",
						"id" => "video_number",
						"type" => "text",
						"std" => "3"
					),
					array( 	
						"name" => "Articles",
						"id" => "articles",
						"type" => "checkbox",
						"std" => ""
					),
					array( 	
						"name" => "Affiliations",
						"id" => "affiliations",
						"type" => "checkbox",
						"std" => ""
					),
					array( 	
						"name" => "Patient Bookings",
						"id" => "patient_bookings",
						"type" => "checkbox",
						"std" => ""
					),

				);
			?>
			<div class="row">
				
			<?php foreach ($options as $value) {
				echo '<div class="form-group">';
				switch ( $value['type'] ) {

					case 'text':
					?>

					<div class="col-xs-6">
						<label for="<?php echo $myprefix.$value['id']; ?>"><?php echo $value['name']; ?></label>
					</div>	
					<div class="col-xs-6">
					 	<input name="<?php echo $myprefix.'['.$value['id'].']'; ?>" id="<?php echo $myprefix.$value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( $current_option[$value['id']] != "") { echo  $current_option[$value['id']]; } else { echo $value['std']; } ?>" placeholder="<?php echo $value['std']; ?>" />
					</div>
					<div class="clearfix"></div>
					<?php
					break;

					case "checkbox":
					?>

					<div class="col-xs-6">
						<label for="<?php echo $myprefix.$value['id']; ?>"><?php echo $value['name']; ?></label>
					</div>	
					<div class="col-xs-6">
						<?php //var_dump($current_option[$value['id']]);
						if($current_option[$value['id']]){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
						<div class="onoffswitch">
						    <input type="checkbox" name="<?php echo $myprefix.'['.$value['id'].']'; ?>" class="onoffswitch-checkbox" id="<?php echo $myprefix.$value['id']; ?>" value="true" <?php echo $checked; ?>>
						    <label class="onoffswitch-label" for="<?php echo $myprefix.$value['id']; ?>">
						        <span class="onoffswitch-inner"></span>
						        <span class="onoffswitch-switch"></span>
						    </label>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php break; 



				}
				echo '</div>';
			}
			?>
			</div>

		</div>
	<?php }?>
		<h2>Individual</h2>
	<?php
	foreach ($ft_arr as $key => $value) {?>
		<div class="col-md-4">
			<h3>Set Features <?php echo ucwords(str_replace('_', ' ', $value));?></h3>
			<?php
				$myprefix = $value;
				$current_option = get_option( $value, true );
				// var_dump($current_option);
				$options = array (

					array( 	
						"name" => "Photo Gallery",
						"id" => "photo_gallery",
						"type" => "checkbox",
						"std" => ""
					),
					array( 	
						"name" => "Number of Photos",
						"id" => "photo_number",
						"type" => "text",
						"std" => "3"
					),
					array( 	
						"name" => "Video Gallery",
						"id" => "video_gallery",
						"type" => "checkbox",
						"std" => ""
					),
					array( 	
						"name" => "Number of Videos",
						"id" => "video_number",
						"type" => "text",
						"std" => "3"
					),
					array( 	
						"name" => "Articles",
						"id" => "articles",
						"type" => "checkbox",
						"std" => ""
					),
					array( 	
						"name" => "Affiliations",
						"id" => "affiliations",
						"type" => "checkbox",
						"std" => ""
					),
					array( 	
						"name" => "Patient Bookings",
						"id" => "patient_bookings",
						"type" => "checkbox",
						"std" => ""
					),

				);
			?>
			<div class="row">
				
			<?php foreach ($options as $value) {
				echo '<div class="form-group">';
				switch ( $value['type'] ) {

					case 'text':
					?>

					<div class="col-xs-6">
						<label for="<?php echo $myprefix.$value['id']; ?>"><?php echo $value['name']; ?></label>
					</div>	
					<div class="col-xs-6">
					 	<input name="<?php echo $myprefix.'['.$value['id'].']'; ?>" id="<?php echo $myprefix.$value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( $current_option[$value['id']] != "") { echo  $current_option[$value['id']]; } else { echo $value['std']; } ?>" placeholder="<?php echo $value['std']; ?>" />
					</div>
					<div class="clearfix"></div>
					<?php
					break;

					case "checkbox":
					?>

					<div class="col-xs-6">
						<label for="<?php echo $myprefix.$value['id']; ?>"><?php echo $value['name']; ?></label>
					</div>	
					<div class="col-xs-6">
						<?php //var_dump($current_option[$value['id']]);
						if($current_option[$value['id']]){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
						<div class="onoffswitch">
						    <input type="checkbox" name="<?php echo $myprefix.'['.$value['id'].']'; ?>" class="onoffswitch-checkbox" id="<?php echo $myprefix.$value['id']; ?>" value="true" <?php echo $checked; ?>>
						    <label class="onoffswitch-label" for="<?php echo $myprefix.$value['id']; ?>">
						        <span class="onoffswitch-inner"></span>
						        <span class="onoffswitch-switch"></span>
						    </label>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php break; 



				}
				echo '</div>';
			}
			?>
			</div>

		</div>
	<?php }?>
		<div class="col-md-12">
			<input class="button-primary button-large" type="submit" name="submit" value="Save changes">
		</div>
	</form>
</div>
</div>
<?php
}














