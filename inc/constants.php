<?php
/**
 *  Contants
 */
 
if (!function_exists('kt_docdirect_prepare_constants')) {
    function kt_docdirect_prepare_constants() {
		
		$user_status = 'false';	
		if ( is_user_logged_in() ){ 
			$user_status = 'true';	
		}
		
		$calendar_locale    = 'en';
		$calendar_format	= 'Y-m-d';
		$site_rtl = 'false';
		$center_point	= 'disable';
		
		if (function_exists('fw_get_db_settings_option')) {
			$site_key = fw_get_db_settings_option('site_key');
			$dir_cluster_marker = fw_get_db_settings_option('dir_cluster_marker');
			$dir_map_marker = fw_get_db_settings_option('dir_map_marker');
			$dir_cluster_color = fw_get_db_settings_option('dir_cluster_color');
			$dir_map_type = fw_get_db_settings_option('dir_map_type');
			$dir_zoom = fw_get_db_settings_option('dir_zoom');
			$dir_longitude = fw_get_db_settings_option('dir_longitude');
			$dir_latitude = fw_get_db_settings_option('dir_latitude');
			$dir_datasize = fw_get_db_settings_option('dir_datasize');
			$dir_map_scroll = fw_get_db_settings_option('dir_map_scroll');
			$map_styles = fw_get_db_settings_option('map_styles');
			$country_restrict = fw_get_db_settings_option('country_restrict');
			$dir_close_marker = get_template_directory_uri().'/images/close.gif';
			$captcha_settings = fw_get_db_settings_option('captcha_settings');
			$center_point = fw_get_db_settings_option('center_point');
			
			$calendar_format    = fw_get_db_settings_option('calendar_format');
			$calendar_locale    = fw_get_db_settings_option('calendar_locale');
			
			$calendar_format	= !empty( $calendar_format ) ?  $calendar_format : 'Y-m-d';
			
			$center_point	= !empty( $center_point ) ?  $center_point : 'disable';
			if( is_rtl() ){
				$site_rtl = 'true';
			} else{
				$site_rtl = 'false';
			}
			
			if( !empty( $country_restrict['gadget'] ) && $country_restrict['gadget'] === 'enable' && !empty( $country_restrict['enable']['country_code'] ) ){
				$country_restrict	= $country_restrict['enable']['country_code'];
			} else{
				$country_restrict	= '';
			}
			
			if( !empty( $dir_cluster_marker ) ){
				$dir_cluster_marker = $dir_cluster_marker['url'];
			} else{
				$dir_cluster_marker = get_template_directory_uri().'/images/cluster.png';
			}
			
			if( empty( $dir_map_marker ) ){
				$dir_map_marker = get_template_directory_uri().'/images/masrker.png';
			}
			
			if( empty( $dir_cluster_color ) ){
				$dir_cluster_color = '#7dbb00';
			}
			
			if( empty( $dir_map_type ) ){
				$dir_map_type = 'ROADMAP';
			}
			
			if( empty( $dir_zoom ) ){
				$dir_zoom = '12';
			}
			
			if( empty( $dir_longitude ) ){
				$dir_longitude = '-0.1262362';
			}
			
			if( empty( $dir_latitude ) ){
				$dir_latitude = '51.5001524';
			}
			
			if( empty( $dir_datasize ) ){
				$dir_datasize = '5242880';
			} 
			
			if( empty( $dir_map_scroll ) ){
				$dir_map_scroll = 'false';
			}      
			
		} else{
			$site_key = '';
			$dir_cluster_marker = get_template_directory_uri().'/images/cluster.png';
			$dir_map_marker = get_template_directory_uri().'/images/marker.png';
			$dir_cluster_color = '#7dbb00';
			$dir_map_type = 'ROADMAP';
			$dir_zoom = '12';
			$dir_longitude = '-0.1262362';
			$dir_latitude = '51.5001524';
			$dir_datasize = '5242880';
			$dir_map_scroll = 'false';
			$map_styles = 'none';
			$country_restrict = '';
			$dir_close_marker = get_template_directory_uri().'/images/close.gif';
			$captcha_settings = '';
		}
		
		$data_size_in_kb	= $dir_datasize / 1024;
		wp_localize_script('docdirect_user_profile', 'scripts_vars', array(
			'ajaxurl' => admin_url('admin-ajax.php'),
			'theme_path_uri' => get_template_directory_uri(),
			'theme_path' => get_template_directory(),
			'award_name' => pll__('Award Name','docdirect'),
			'award_date' => pll__('Award Date','docdirect'),
			'award_description' => pll__('Award Description','docdirect'),
			'file_upload_title' => pll__('Avatar Upload','docdirect'),
			'delete_message' => pll__('Are you sure you want to delete your account?','docdirect'),
			'deactivate' => pll__('Are you sure you want to deactivate your account?','docdirect'),
			'delete_title' => pll__('Delete account?','docdirect'),
			'deactivate_title' => pll__('Deactivate account?','docdirect'),
			'file_upload_title' => pll__('Avatar Upload','docdirect'),
			'docdirect_upload_nounce' => wp_create_nonce ( 'docdirect_upload_nounce' ),
			
			'dir_close_marker' => $dir_close_marker,
			'dir_cluster_marker' => $dir_cluster_marker,
			'dir_map_marker' => $dir_map_marker,
			'dir_cluster_color' => $dir_cluster_color,
			'dir_map_type' => $dir_map_type,
			'dir_zoom' => $dir_zoom,
			'dir_longitude' => $dir_longitude,
			'dir_latitude' => $dir_latitude,
			'dir_datasize' => $dir_datasize,
			'data_size_in_kb' => $data_size_in_kb.'kb',
			'dir_map_scroll' => $dir_map_scroll,
			'map_styles' => $map_styles,
			'site_key' => $site_key,
			
			'rating_1' => pll__('Not Satisfied','docdirect'),
			'rating_2' => pll__('Satisfied','docdirect'),
			'rating_3' => pll__('Good','docdirect'),
			'rating_4' => pll__('Very Good','docdirect'),
			'rating_5' => pll__('Excellent','docdirect'),
			
			'delete_award' => pll__('Delete Award','docdirect'),
			'delete_award_message' => pll__('Are you sure, you want to delete this award?','docdirect'),
			'delete_education' => pll__('Delete Degree','docdirect'),
			'delete_education_message' => pll__('Are you sure, you want to delete this Degree?','docdirect'),
			
			'delete_experience' => pll__('Delete Experience','docdirect'),
			'delete_experience_message' => pll__('Are you sure, you want to delete this experience?','docdirect'),
			
			'delete_prices' => pll__('Delete Price/Service 111111111111111','docdirect'),
			'delete_prices_message' => pll__('2222222222222Are you sure, you want to delete this price/service item?','docdirect'),
			
			'delete_category' => pll__('Delete Category','docdirect'),
			'delete_category_message' => pll__('Are you sure, you want to delete this category?','docdirect'),
			
			'delete_service' => pll__('Delete Service','docdirect'),
			'delete_service_message' => pll__('Are you sure, you want to delete this service?','docdirect'),
			
			'delete_slot' => pll__('Delete Slot','docdirect'),
			'delete_slot_message' => pll__('Are you sure, you want to delete this slot?','docdirect'),
			
			'delete_slot_date' => pll__('Delete Slot Date','docdirect'),
			'delete_slot_date_message' => pll__('Are you sure, you want to delete this slot date?','docdirect'),
			
			'approve_appointment' => pll__('Approve Appointment?','docdirect'),
			'approve_appointment_message' => pll__('Are you sure, you want to approve this appointment?','docdirect'),
			'cancel_appointment' => pll__('Cancel Appointment?','docdirect'),
			'cancel_appointment_message' => pll__('Are you sure, you want to cancel this appointment?','docdirect'),
			
			'delete_teams' => pll__('Delete Member','docdirect'),
			'delete_teams_message' => pll__('Are you sure, you want to delete this team member?','docdirect'),
			
			'booking_time' => pll__('Please select booking time.','docdirect'),
			'gmap_norecod' => pll__('No Record Found.','docdirect'),
			'fav_message' => pll__('Please login first.','docdirect'),
			'fav_nothing' => pll__('Nothing found.','docdirect'),
			'empty_category' => pll__('Please add category name.','docdirect'),
			'complete_fields' => pll__('Please fill all the fields.','docdirect'),
			'system_error' => pll__('Some error occur, please try again later.','docdirect'),
			'valid_email' => pll__('Please add valid email address.','docdirect'),
			'user_status' => $user_status,
			'custom_slots_dates' => pll__('Atleast one date! start or end date is required.','docdirect'),
			'finish' => pll__('Finish','docdirect'),
			'with_in' => pll__('within ','docdirect'),
			'kilometer' => pll__(' km','docdirect'),
			'select_language' => pll__(' Select Language','docdirect'),
			'add_now' => pll__('Add Now','docdirect'),
			'view_profile' => pll__('View Full Profile','docdirect'),
			'no_team' => pll__('No team members, Add your teams now.','docdirect'),
			'country_restrict' => $country_restrict,
			'captcha_settings' => $captcha_settings,
			'site_rtl' => $site_rtl,
			'center_point' => $center_point,
			'calendar_format' => $calendar_format,
			'calendar_locale' => $calendar_locale,
			'yes' => pll__('Yes','docdirect'),
			'no' => pll__('No','docdirect'),
			'account_verification' => pll__('Your account has verified.','docdirect'),

		));
	}
	add_action('wp_enqueue_scripts', 'kt_docdirect_prepare_constants');
}