<?php

if ( ! wp_next_scheduled( 'my_task_hook' ) ) {
  wp_schedule_event( time(), 'hourly', 'my_task_hook' );
}

// add_action( 'kt_check_subscriber', 'my_task_function' );
add_action( 'my_task_hook', 'my_task_function' );

// update_user_meta(35, 'last_user_featured', strtotime( 'Sep 21 2017' ));
// update_user_meta(35, 'user_featured', strtotime( 'Sep 22 2017' ));
function my_task_function() {
	$arg = array(
		'role'		=> 'professional',
		'meta_query' => array(
			'relation' => 'AND', // Optional, defaults to "AND"
			array(
				'key' 	  => 'payment_profileid',
				'value'   => '',
				'compare' => '!=',
			)
		)
	);
	$allusers = get_users( $arg );
	// Array of WP_User objects.
    $body = 'this all user effect';
	$arr_user = array();
	$date_user = array();
	$paypal = new wp_paypal_gateway (true);

	$timestamp = current_time( 'timestamp' );
	$today = time();;
	// $next_hour	= strtotime("+1 hours", $today);

	foreach ( $allusers as $user ) {
		$user_id = $user->ID;
		$last_user_featured = get_user_meta($user_id, 'last_user_featured', true);
		$user_featured = get_user_meta($user_id, 'user_featured', true);
		$payment_profileid = get_user_meta($user_id, 'payment_profileid', true);

		// $newday	= strtotime("+".$duration." days", $last_user_featured);

		// if( $newday >= $today && $newday < $next_hour ) {

		$requestParams = array('PROFILEID' => $payment_profileid);
	    $paypal->GetRecurringPaymentsProfileDetails($requestParams);
	    $response = $paypal->getResponse();
		if(is_array($response) && $response["ACK"]=="Success"){
			if ($response["STATUS"] == 'Active') {		

				$BILLINGFREQUENCY = intval($response["BILLINGFREQUENCY"]);
				$LASTPAYMENTDATE = strtotime($response["LASTPAYMENTDATE"]);
				$next_hour_payment = strtotime("+1 hours", $LASTPAYMENTDATE);
				
				if( $LASTPAYMENTDATE <= $today && $today < $next_hour_payment ) {

					$arr_user[] = $user_id;

					$user_current_package = get_user_meta($user_id, 'user_current_package', true);
                    $price = fw_get_db_post_option($user_current_package, 'price', true);
					 if (function_exists('fw_get_db_settings_option')) {
						$currency_sign   = fw_get_db_settings_option('currency_select');
					 }
					//Add New Order
					$order_no	= docdirect_add_new_order(
						array(
							'packs'		=> sanitize_text_field( $user_current_package ),
							'gateway'	  => sanitize_text_field( 'paypal' ),
							'price'		=> number_format((float)$price, 2, '.', ''),
							'payment_type' => 'gateway',
							'mc_currency'  => $currency_sign,
						)
					);

					//Update Order
		            $expiry_date	= kt_docdirect_update_order_data(
						array(
							'order_id'		 => $order_no,
							'user_identity'	=> $user_id,
							'package_id'	   => $user_current_package,
							'txn_id'		   => 'kt0000119',
							'payment_gross'	=> $price,
							'payment_method'   => 'paypal',
							'mc_currency'	  => 'HKD',
						)
					);


				}
			}			
		}

		// }

	}
	$body .= json_encode($arr_user);
	if (!empty($arr_user)) {
  		wp_mail( 'hunter.asian@gmail.com', 'Automatic email', $body);
	}
}


//email end user trail
if ( ! wp_next_scheduled( 'kt_task_hook' ) ) {
  wp_schedule_event( time(), 'hourly', 'kt_task_hook' );
}

add_action( 'kt_task_hook', 'kt_email_end_free_trail' );

function kt_email_end_free_trail() {

    $today = current_time( 'timestamp' );
	$next_hour	= strtotime("+1 hours", $today);
	$arg = array(
		'role'		=> 'professional',
		'meta_query' => array(
			'relation' => 'AND', // Optional, defaults to "AND"
			array(
				'key' 	  => 'user_premium',
				'value'   => 'free_trial',
				'compare' => '=',
			),
			array(
				'key' 	  => 'user_featured',
				'value'   => $today,
				'compare' => '>=',
			),
		)
	);
	$allusers = get_users( $arg );
	foreach ( $allusers as $user ) {
		$user_id = $user->ID;
		$user_featured = get_user_meta($user_id, 'user_featured', true);
		/*if( $user_featured >= $today && $user_featured < $next_hour ) {
			$end_trial = get_user_meta($user_id, 'end_trial', true);
			if( !isset($end_trial) || $end_trial == '' ) {
  				wp_mail( 'hunter.asian@gmail.com', 'End trial email', $user_id);
  				update_user_meta($user_id, 'end_trial', 'done');
			}else {
  				wp_mail( 'hunter.asian@gmail.com', 'khac biet', $user_id);
			}
		}else {
  				wp_mail( 'hunter.asian@gmail.com', 'ngoai le', $user_id);
		}*/
		
		if( $user_featured >= $today && $user_featured < $next_hour ) {
			$end_trial = get_user_meta($user_id, 'end_trial', true);
			if( !isset($end_trial) || $end_trial == '' ) {
  				update_user_meta($user_id, 'end_trial', 'done');
  				// wp_mail( 'hunter.asian@gmail.com', 'End trial email', $user_id);
  				kt_send_email_end_free_trail($user_id);
			}
		}
	}

}

function isa_add_cron_recurrence_interval( $schedules ) {
 
    $schedules['every_fifteen_minutes'] = array(
            'interval'  => 15*60,
            'display'   => __( 'Every 15 Minutes', 'textdomain' )
    );
     
    return $schedules;
}
add_filter( 'cron_schedules', 'isa_add_cron_recurrence_interval' );

if ( ! wp_next_scheduled( 'your_fifteen_minute_action_hook' ) ) {
    wp_schedule_event( time(), 'every_fifteen_minutes', 'your_fifteen_minute_action_hook' );
}

add_action('your_fifteen_minute_action_hook', 'isa_test_cron_job_send_mail');
 
function isa_test_cron_job_send_mail() {
    
    $today = current_time( 'timestamp' );
	$yesterday	= strtotime("-1 days", $today);

	$arg = array(
		'post_type'		=> 'docappointments',
		'post_status' => 'publish', 
		'meta_query' => array(
			'relation' => 'AND', // Optional, defaults to "AND"
			array(
				'key' 	  => 'bk_status',
				'value'   => 'approved',
				'compare' => '=',
			),
			array(
				'key'     => 'complete_status',
				'compare'   => 'NOT EXISTS'
			),
			/*array(
				'key'     => 'bk_timestamp',
				'value'   => $yesterday,
				'compare' => '>=',
				'type'	  => 'NUMERIC'
			)*/
		)
	);

	$date_format = get_option('date_format');
	$time_format = get_option('time_format');
	$all_apm = get_posts( $arg );
	foreach ( $all_apm as $post ) {
		$bk_timestamp     = get_post_meta($post->ID, 'bk_timestamp',true);
		$bk_booking_date  = get_post_meta($post->ID, 'bk_booking_date',true);
		$bk_slottime 	  = get_post_meta($post->ID, 'bk_slottime',true);
		$time = explode('-',$bk_slottime);

		$date_bk = date($date_format,strtotime($bk_booking_date));
		$time_bk = date($time_format,strtotime('2016-01-01 '.$time[0]) );
		$date_time_booking = $date_bk.' '.$time_bk;

		$next_time	= strtotime("+60 minutes", $today);
		$next_time_plus	= strtotime("+75 minutes", $today);

		if( strtotime($date_time_booking) >= $next_time && strtotime($date_time_booking) < $next_time_plus ) {
			update_post_meta($post->ID, 'reminder', 'yes');
			$emailData['post_id']	= $post->ID;
			kt_patient_remind_booking_email($emailData);
		}
		$next_day	= strtotime("+1 day", strtotime($date_time_booking));

		if ( $next_day <= $today ) {			

			update_post_meta($post->ID, 'complete_status', 'completed');
			
			//Send Email
			$emailData	= array();
			$emailData['post_id']	= $post->ID;
			kt_complete_appointment_email($emailData);
		}
	}
 
}
//Schedule
function my_cron_schedules($schedules){
    if(!isset($schedules["5min"])){
        $schedules["5min"] = array(
            'interval' => 5*60,
            'display' => __('Once every 5 minutes'));
    }
    if(!isset($schedules["30min"])){
        $schedules["30min"] = array(
            'interval' => 30*60,
            'display' => __('Once every 30 minutes'));
    }
    return $schedules;
}
// add_filter('cron_schedules','my_cron_schedules');

function kt_send_email_end_free_trail( $user_id ) {
		global $current_user;
		// extract( $params );
			
		$email_helper	= new DocDirectProcessEmail();

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
			
			$email_to		 = 'hunter.asian@gmail.com';			
			 
		    $user_info = get_userdata( $user_id );
			$username 		= $user_info->display_name;
			$user_email 		= $user_info->user_email;

			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
				$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			}

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';

			$permalink = add_query_arg( 
								array(
									'ref'=>   urlencode( 'invoices' ) ,
									'identity'=>   urlencode( $user_id )   ),
									esc_url( get_permalink($profile_page) 
								) 
							);

			$linkpackage	= esc_url( $permalink );
			
			$subject = 'End Free Trail!';
			$end_trial_content_default = 'Hi,<br/>
											%username% has ended trial
											<br/><br/>
											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
									';
			
			$admin_email = '';	
			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('end_trial_subject');
				$admin_email = fw_get_db_settings_option('end_trial_admin_email');
				$email_content = fw_get_db_settings_option('end_trial_content');
			}

			if( !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $admin_email) ) { 
				$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			}
			
			//set defalt contents
			if( empty( $email_content ) ){
				$email_content = $end_trial_content_default;
			}

			/*$subject_default = 'End Trial';
			$email_content = 'Hey %customer_name%!<br/><br/>

						Your Account End Trial<br/><br/>
						
						Sincerely,<br/>
						%logo%';	*/	
			
			
			//set defalt title
			$booking_approved = $email_content;
			
			$logo		   = kt_process_get_logo();
			
			$booking_approved = str_replace("%link_appointment%", nl2br($link_appointment), $booking_approved); //Replace link_appointment

			$booking_approved = str_replace("%username%", nl2br($username), $booking_approved); //Replace Name
			$booking_approved = str_replace("%logo%", nl2br($logo), $booking_approved); //Replace logo
			$booking_approved = str_replace("%linkpackage%", nl2br($linkpackage), $booking_approved); //Replace logo

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			$body			.= $email_helper->prepare_email_headers($customer_name);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_approved.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			$body	.= $email_helper->prepare_email_footers();
			// wp_mail($email_to, $subject, $body);
			wp_mail($admin_email, $subject, $body);
			wp_mail($user_email, $subject, $body);
			
			return true;
}






