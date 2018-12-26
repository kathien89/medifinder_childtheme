<?php
/**
 * Theme functions file
 */

/**
 * Enqueue parent theme styles first
 * Replaces previous method using @import
 * <http://codex.wordpress.org/Child_Themes>
 */

function kia_add_favicon(){ ?>
    <!-- Custom Favicons -->
    <link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri();?>/favicon.ico"/>
    <?php }
add_action('wp_head','kia_add_favicon');

function kt_custom_scripts() {
    // wp_deregister_script( 'jquery' );
    // wp_register_script('jquery', '//code.jquery.com/jquery-2.2.4.min.js', array(), '2.2.4', true); // true will place script in the footer
    wp_enqueue_script( 'jquery' );
}
if(!is_admin()) {
    add_action('wp_enqueue_scripts', 'kt_custom_scripts', 9);
}

function docdirect_child_theme_enqueue_styles() {

	$query_args = array(
		'family' => 'Open+Sans:400,300,700,600|Montserrat:400,700',
		'subset' => 'latin,latin-ext',
	);
	wp_register_style( 'google_fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ), array(), null );
	wp_enqueue_style( 'google_fonts' );
    
    $parent_style = 'docdirect_theme_style';
    
  	wp_enqueue_style( 'docdirect_child_style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'bootstrap.min', $parent_style)
    );

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    // wp_enqueue_script( 'jquery' );

    wp_enqueue_script( 'mcafee', 'https://cdn.ywxi.net/js/1.js', false, false, true);
    wp_enqueue_script( 'idTabs', get_stylesheet_directory_uri() . '/js/jquery.idTabs.min.js', true, '1.0', true );
    wp_enqueue_script( 'custom', get_stylesheet_directory_uri() . '/js/custom.js', true, '1.0', true );
    //allsite
    wp_enqueue_style( 'custom_exclude', get_stylesheet_directory_uri() . '/css/kt_exclude/custom_exclude.css' );
    wp_enqueue_style( 'nouislider', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.css' );
    wp_enqueue_script( 'nouislider', 'https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/11.1.0/nouislider.min.js', true, false, true );

    //formvalidation
    wp_enqueue_style( 'formvalidation', get_stylesheet_directory_uri() . '/css/formValidation.min.css' );
    wp_enqueue_script('formValidation', get_stylesheet_directory_uri() . '/js/formValidation.min.js', false,false, true);
    wp_enqueue_script('formValidation-bootstrap', get_stylesheet_directory_uri() . '/js/form.bootstrap.min.js', false,false, true);

    wp_enqueue_style( 'tagsinput', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css' );
    wp_enqueue_script( 'tagsinput', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js', true );

    //zzzzzzzz

    wp_enqueue_style( 'toggle', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css' );
    wp_enqueue_script( 'toggle', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js', true );

    wp_enqueue_style( 'jscrollpane', 'https://cdnjs.cloudflare.com/ajax/libs/jScrollPane/2.2.0/style/jquery.jscrollpane.min.css' );
    wp_enqueue_script( 'jscrollpane	', 'https://cdnjs.cloudflare.com/ajax/libs/jScrollPane/2.2.0/script/jquery.jscrollpane.min.js', true );

    wp_enqueue_style( 'intlTelInput', get_stylesheet_directory_uri() . '/css/intelinput/intlTelInput.css' );
    wp_enqueue_script( 'intlTelInput', get_stylesheet_directory_uri() . '/js/intlTelInput.min.js', true );

	wp_enqueue_media();
	wp_enqueue_script('image-uploads', get_stylesheet_directory_uri() . '/js/front-uploader.js', false, false, true );
	//colorbox
	wp_enqueue_script('colorbox', get_stylesheet_directory_uri() . '/js/jquery.colorbox-min.js', false, false, false );
    wp_enqueue_style( 'colorbox', get_stylesheet_directory_uri() . '/css/colorbox.css' );

	wp_enqueue_script('oms', 'https://jawj.github.io/OverlappingMarkerSpiderfier/bin/oms.min.js', false, false, false );

	wp_enqueue_script('masonry-js', 'https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js', false, false, true );

    wp_enqueue_script( 'menu', get_stylesheet_directory_uri() . '/js/menu.js', true, '1.0', true );
    wp_enqueue_script( 'lazy', get_stylesheet_directory_uri() . '/js/jquery.lazy.min.js', true, '1.0', true );
    wp_enqueue_script( 'validid', get_stylesheet_directory_uri() . '/js/validid.js', true, '1.0', true );

    wp_enqueue_style( 'fullcalendar', get_stylesheet_directory_uri() . '/css/fullcalendar.min.css' );
    wp_enqueue_script( 'fullcalendar', get_stylesheet_directory_uri() . '/js/fullcalendar.min.js', true, '1.0', true );

	// Register the script
	wp_register_script( 'ratings_handle', '', array(), '', true);

	//Localize the script with new data
	$translation_array = array(
		'recommendation' => array(
				'rating_1' => pll__('Very unlikely'),
				'rating_2' => pll__('Unlikely'),
				'rating_3' => pll__('Neutral'),
				'rating_4' => pll__('Likely'),
				'rating_5' => pll__('Very likely'),
			),
		'bedside_manner' => array(
				'rating_1' => pll__('Bad'),
				'rating_2' => pll__('Unsatisfactory'),
				'rating_3' => pll__('Satisfactory'),
				'rating_4' => pll__('Good'),
				'rating_5' => pll__('Excellent!'),
			),
		'waiting_time' => array(
				'rating_1' => pll__('Over two hours'),
				'rating_2' => pll__('Over an hour'),
				'rating_3' => pll__('Over 30 mins'),
				'rating_4' => pll__('Under 30 mins'),
				'rating_5' => pll__('Seen right away!'),
			),
		'supporting_staff' => array(
				'rating_1' => pll__('Bad'),
				'rating_2' => pll__('Unsatisfactory'),
				'rating_3' => pll__('Satisfactory'),
				'rating_4' => pll__('Good'),
				'rating_5' => pll__('Excellent!'),
			),
		'facilities' => array(
				'rating_1' => pll__('Bad'),
				'rating_2' => pll__('Unsatisfactory'),
				'rating_3' => pll__('Satisfactory'),
				'rating_4' => pll__('Good'),
				'rating_5' => pll__('Excellent!'),
			)
	);

	$confirm_string_array = array(
		'delete_aff' => array(
				'title' => pll__('Delete Affiliation'),
				'message' => pll__('Are you sure you want to delete this Affiliation?'),
			),
		'dontshow_msag' => pll__('Don\'t show this message again'),
		'complete_appointment' => array(
				'title' => pll__('Complete Appointment?'),
				'message' => pll__('Are you sure, you want to complete this appointment?'),
			),
		'confirm_appointment' => array(
				'title' => pll__('Confirm Appointment'),
				'message' => pll__('You will arrive?'),
				'message2' => pll__('You will not arrive?'),
			),
		'delete_favorites' => array(
				'title' => pll__('Delete Favourites','docdirect'),
				'message' => pll__('Are you sure, you want to delete this?','docdirect'),
			),
		'delete_practice' => array(
				'title' => pll__('Delete Practice','docdirect'),
				'message' => pll__('Are you sure, you want to delete this?','docdirect'),
			),
		're_post' => array(
				'title' => pll__('Re-Post Appointment?','docdirect'),
				'message' => pll__('Would you like to re-post cancelled appointment?','docdirect'),
			),
		'delete_slot' => 'Slot deleted succesfully',
		'update_slot' => 'Slot updated succesfully',
		'more_text' => 'Read More',
	);

	wp_localize_script( 'ratings_handle', 'rating_vars', $translation_array );
	wp_localize_script( 'ratings_handle', 'confirm_vars', $confirm_string_array );

	//Enqueued script with localized data.
	wp_enqueue_script( 'ratings_handle' );


}

add_action( 'wp_enqueue_scripts', 'docdirect_child_theme_enqueue_styles', 99 );

/*********admin script*********/
function kt_admin_enqueue() {    

    wp_enqueue_style( 'admin-css', get_stylesheet_directory_uri() . '/css/admin.css' );
    wp_enqueue_style( 'tagsinput', get_stylesheet_directory_uri() . '/css/bootstrap-tagsinput.css' );
    wp_enqueue_script( 'tagsinput', get_stylesheet_directory_uri() . '/js/bootstrap-tagsinput.min.js', true );

}
add_action( 'admin_enqueue_scripts', 'kt_admin_enqueue' );

// Hai debug
if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}
// end debug

//PAYPAL
require_once ( STYLESHEETPATH. '/inc/paypal.php'); //DPayPal
require_once ( STYLESHEETPATH. '/inc/rewrite-ajax.php'); //rewrite ajax
require_once ( STYLESHEETPATH. '/inc/rewrite-shortcode.php'); //rewrite shortcode
require_once ( STYLESHEETPATH. '/inc/rewrite-usermenu.php'); //rewrite widget
require_once ( STYLESHEETPATH. '/inc/admin_functions.php'); //admin fn
require_once ( STYLESHEETPATH. '/inc/custom_function.php'); //DPayPal
require_once ( STYLESHEETPATH. '/inc/rewrite-fn_email.php'); //renew email
require_once ( STYLESHEETPATH. '/inc/shortcode-articles.php'); //	
require_once ( STYLESHEETPATH. '/inc/fn-affiliation.php'); //rewrite affiliation
require_once ( STYLESHEETPATH. '/inc/fn-invite_review.php'); //rewrite invite review

require_once ( STYLESHEETPATH. '/inc/part-theme_hook.php');

require_once ( STYLESHEETPATH. '/inc/part-1.php');
require_once ( STYLESHEETPATH. '/inc/part-2.php');
// 

require_once ( STYLESHEETPATH. '/inc/part-search_grid.php');

require_once ( STYLESHEETPATH. '/inc/part-calendar.php');

require_once ( STYLESHEETPATH. '/inc/part-cronjobs.php');

