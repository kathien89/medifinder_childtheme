<?php

function kt_patient_confirm_email( $params = '' ) {

		global $current_user;
		extract( $params );
			
		$email_helper	= new DocDirectProcessEmail();

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
		
			$user_from	= get_post_meta($post_id, 'bk_user_from', true);
			$user_to	  = get_post_meta($post_id, 'bk_user_to', true);

		    $user_info = get_userdata( $user_to );
			$username 		= $user_info->display_name;
			$user_email 		= $user_info->user_email;
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service	= $booking_services[$bk_service]['title'];
			$patient_name = $user_from_data->display_name;
			 
			$provider_name 		= docdirect_get_username($user_to);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );

			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
				$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			}

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';	

			$permalink = add_query_arg( 
								array(
									'ref'=>   urlencode( 'bookings' ) ,
									'identity'=>   urlencode( $user_to ),
									'cancel_id'=>   urlencode( $post_id )    ),
									esc_url( get_permalink($profile_page) 
								) 
							);

			$link_appointment	= esc_url( $permalink );

			$current_userdata	   = get_userdata($user_to);
			
			$subject_default = 'Confirm Appointment';
			$booking_approved_default = 'Hey %doctor_name%,<br/>

						%patient_name% will not arrive<br/>
						please cancel appointment<br/><br/>
                        <a href="%link_appointment%">Dashboard</a><br/><br/>
						
						Sincerely,<br/>
						%logo%';
				
			if (function_exists('fw_get_db_post_option')) {
				// $subject = fw_get_db_settings_option('complete_appoinment_subject');
				// $booking_approved = fw_get_db_settings_option('complete_appoinment_content');
				$subject = fw_get_db_settings_option('patient_confirm_subject');
				$booking_approved = fw_get_db_settings_option('patient_confirm_content');
			}
			
			//set defalt contents
			if( empty( $subject ) ){
				$subject = $subject_default;
			}
			
			//set defalt title
			if( empty( $booking_approved ) ){
				$booking_approved = $booking_approved_default;
			}
			
			$provider	= '<a href="'.get_author_posts_url($user_to).'"  alt="'.esc_html__('provider','docdirect').'">'.$provider_name.'</a>';
			$logo		   = kt_process_get_logo();

			$booking_approved = str_replace("%link_review%", nl2br($link_review), $booking_approved); //Replace link_appointment

			$booking_approved = str_replace("%doctor_name%", nl2br($provider_name), $booking_approved); //Replace Name
			$booking_approved = str_replace("%patient_name%", nl2br($patient_name), $booking_approved); //Replace patient
			$booking_approved = str_replace("%link_appointment%", nl2br($link_appointment), $booking_approved); //Replace link_appointment
			$booking_approved = str_replace("%logo%", nl2br($logo), $booking_approved); //Replace logo

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			$body			.= $email_helper->prepare_email_headers($username);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_approved.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			$body	.= $email_helper->prepare_email_footers();
			wp_mail($user_email, $subject, $body);
			
			return true;
}


function kt_doctor_cancel_booking_email( $params = '' ) {

		global $current_user;
		extract( $params );
			
		$email_helper	= new DocDirectProcessEmail();

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
		
			$user_from	= get_post_meta($post_id, 'bk_user_from', true);
			$user_to	  = get_post_meta($post_id, 'bk_user_to', true);
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			$bk_code		= get_post_meta($post_id, 'bk_code', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service	= $booking_services[$bk_service]['title'];
			 
			$doctor_name 		= docdirect_get_username($user_to);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );

			$link_review	= get_permalink($post_id);

			$current_userdata	   = get_userdata($user_to);

			$bk_currency 	  = get_post_meta($post_id, 'bk_currency', true);
			$bk_paid_amount   = get_post_meta($post_id, 'bk_paid_amount', true);
			$payment_amount  = $bk_currency.$bk_paid_amount;

			$doctor_email	    = $user_to_data->user_email;
			$bk_location = get_post_meta($post_id,'bk_location', true);
			if ($bk_location != '') {
				$current_practices = get_user_meta($user_to, 'user_practices', true);
				$basics = $current_practices[$bk_location]['basics'];
				$phone_number = $basics['phone_number'];
				$room_floor = $basics['room_floor'];
				$user_url = $basics['user_url'];
				$db_address = $basics['address'];
			}else {
				$user_url	= $current_userdata->data->user_url;
				$phone_number	    = get_user_meta( $user_to, 'phone_number', true);
				$room_floor	    = get_user_meta( $user_to, 'room_floor', true);
				$db_address	    = get_user_meta( $user_to, 'address', true);
			}

		    $user_from_info = get_userdata( $user_from );
			$username 		= $user_from_info->display_name;

		    $user_info = get_userdata( $user_to );
			$doctor_name 		= $user_info->display_name;
			$user_email 		= $user_info->user_email;
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service	= $booking_services[$bk_service]['title'];
			$price		= get_post_meta($post_id, 'bk_paid_amount', true);
			 
			$doctor_name 		= docdirect_get_username($user_to);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );

			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
				$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			}

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';	

			$permalink = add_query_arg( 
								array(
									'ref'=>   urlencode( 'mybookings' ) ,
									'identity'=>   urlencode( $user_from ),
									'appointment_id'=>   urlencode( $post_id )    ),
									esc_url( get_permalink($profile_page) 
								) 
							);

			$link_review	= esc_url( $permalink );

			$current_userdata	   = get_userdata($user_to);

			$gmap_link	    = 'http://maps.google.com/?q='.urlencode($db_address);
			
			$subject_default = 'Canceled Appointment';
			$booking_approved_default = 'Hey %patient_name%,<br/>

						%doctor% just canceled appointment booking<br/>
						%desc%<br/><br/>
						
						Sincerely,<br/>
						%logo%';
				

			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('cancel_subject');
				$booking_approved = fw_get_db_settings_option('cancel_booking');
			}
			
			//set defalt contents
			if( empty( $subject ) ){
				$subject = $subject_default;
			}
			
			//set defalt title
			if( empty( $booking_approved ) ){
				$booking_approved = $booking_approved_default;
			}			
			
			$provider	= '<a href="'.get_author_posts_url($user_to).'"  alt="'.esc_html__('provider','docdirect').'">'.$provider_name.'</a>';
			$logo		   = kt_process_get_logo();

			$booking_approved = str_replace("%link_review%", nl2br($link_review), $booking_approved); //Replace link_appointment

			$booking_approved = str_replace("%provider%", nl2br($provider_name), $booking_approved); //Replace Name
			$booking_approved = str_replace("%customer_name%", nl2br($customer_name), $booking_approved); //
			$booking_approved = str_replace("%service%", nl2br($service), $booking_approved); //Replace Name
			$booking_approved = str_replace("%price%", nl2br($price), $booking_approved); //Replace Name

			$booking_approved = str_replace("%doctor%", nl2br($provider_name), $booking_approved); //Replace Name
			$booking_approved = str_replace("%patient_name%", nl2br($patient_name), $booking_approved); //Replace patient
			$booking_approved = str_replace("%desc%", nl2br($desc), $booking_approved); //Replace link_appointment
			$booking_approved = str_replace("%logo%", nl2br($logo), $booking_approved); //Replace logo

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			// $body			.= $email_helper->prepare_email_headers($customer_name);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_approved.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			// $body	.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $subject, $booking_approved);
			
			return true;
}

function kt_patient_remind_booking_email( $params = '' ) {

		global $current_user;
		extract( $params );
			
		$email_helper	= new DocDirectProcessEmail();
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
		
			$user_from	= get_post_meta($post_id, 'bk_user_from', true);
			$user_to	  = get_post_meta($post_id, 'bk_user_to', true);

		    $user_info = get_userdata( $user_to );
			$username 		= $user_info->display_name;
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$patient_name = $user_from_data->display_name;
			$user_email 		= $user_from_data->user_email;
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service	= $booking_services[$bk_service]['title'];
			$patient_name = $user_from_data->display_name;
			 
			$doctor_name 		= docdirect_get_username($user_to);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );

			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
				$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			}

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';	

			$permalink = add_query_arg( 
								array(
									'ref'=>   urlencode( 'mybookings' ) ,
									'identity'=>   urlencode( $user_from )   ),
									esc_url( get_permalink($profile_page) 
								) 
							);

			$link_appointment	= esc_url( $permalink );

			$current_userdata	   = get_userdata($user_to);
			
			$subject_default = 'Canceled Appointment';
			$booking_approved_default = 'Hi %patient_name%,<br/>

										This is a reminder for your upcoming appointment with %doctor_name%, at %appointment_time% on %appointment_date%.
										Please login to cancel your appointment from your dashboar<br/>
				                        <a href="%link_appointment%">Dashboard</a><br/><br/>
										
										Sincerely,<br/>
										%logo%';

			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('patient_remind_subject');
				$booking_approved = fw_get_db_settings_option('patient_remind_content');
			}
			
			//set defalt contents
			if( empty( $subject ) ){
				$subject = $subject_default;
			}
			
			//set defalt title
			if( empty( $booking_approved ) ){
				$booking_approved = $booking_approved_default;
			}
			$provider	= '<a href="'.get_author_posts_url($user_to).'"  alt="'.esc_html__('provider','docdirect').'">'.$provider_name.'</a>';
			$logo		   = kt_process_get_logo();

			$booking_approved = str_replace("%link_appointment%", nl2br($link_appointment), $booking_approved); //Replace link_appointment

			$booking_approved = str_replace("%appointment_date%", nl2br($appointment_date), $booking_approved); //Replace patient
			$booking_approved = str_replace("%appointment_time%", nl2br($appointment_time), $booking_approved); //Replace patient
			$booking_approved = str_replace("%doctor_name%", nl2br($doctor_name), $booking_approved); //Replace doctor_name
			$booking_approved = str_replace("%patient_name%", nl2br($patient_name), $booking_approved); //Replace patient
			$booking_approved = str_replace("%logo%", nl2br($logo), $booking_approved); //Replace logo

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			// $body			.= $email_helper->prepare_email_headers($customer_name);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_approved.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			// $body	.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $subject, $booking_approved);
			
			return true;
}

function kt_process_appointment_confirmation_email( $params = '' ) {
			global $current_user;
			extract( $params );

			$email_helper	= new DocDirectProcessEmail();
			
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
		
			$user_from	= get_post_meta($post_id, 'bk_user_from', true);
			$user_to	  = get_post_meta($post_id, 'bk_user_to', true);
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_subject	= get_post_meta($post_id, 'bk_subject', true);
			$bk_booking_note	= get_post_meta($post_id, 'bk_booking_note', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			$bk_code		= get_post_meta($post_id, 'bk_code', true);
			
			$insurance		= get_post_meta($post_id, 'patient_insurers', true);
			$idcard		= get_post_meta($post_id, 'usercard', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$patient_email = $user_from_data->user_email;
			$phone_number = $user_from_data->phone_number;

			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service			 = $booking_services[$bk_service]['title'];
			// $price			 = $booking_services[$bk_service]['price'];
			$price		= get_post_meta($post_id, 'bk_paid_amount', true);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );
			 
			$user_from_name 		= docdirect_get_username($user_from);
			$doctor_name 		= docdirect_get_username($user_to);
			
			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
				$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			}

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';

			$permalink = add_query_arg( 
								array(
									'ref'=>   urlencode( 'bookings' ) ,
									'identity'=>   urlencode( $user_to )   ),
									esc_url( get_permalink($profile_page) 
								) 
							);

			$link_appointment	= esc_url( $permalink );
			
			$subject_default = esc_html__('Your Appointment Confirmation!','docdirect');
			$booking_confirmed_default = 'Hey %customer_name%!<br/>

						This is confirmation that you have booked "%service%"<br/> with %provider%
						We will let your know regarding your booking soon.<br/><br/>
						
						Thank you for choosing our company.<br/><br/>
						
						Sincerely,<br/>
						%logo%';
			
			
			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('confirm_subject');
				$appointment_content = fw_get_db_settings_option('confirm_booking');
			}
			
			//set defalt contents
			if( empty( $subject ) ){
				$subject = $subject_default;
			}
			 
			//set defalt contents
			if( empty( $appointment_content ) ){
				$booking_confirmed = $booking_confirmed_default;
			} else{
				$booking_confirmed = $appointment_content;
			}
			
			$logo		   = kt_process_get_logo();
			
			$booking_confirmed = str_replace("%customer_name%", nl2br($customer_name), $booking_confirmed); //Replace Name
			
			$booking_confirmed = str_replace("%service%", nl2br($service), $booking_confirmed); //Replace service

			$booking_confirmed = str_replace("%id_order%", nl2br($bk_code), $booking_confirmed); //Replace
			 
			$booking_confirmed = str_replace("%appointment_date%", nl2br($appointment_date), $booking_confirmed); //Replace patient
			$booking_confirmed = str_replace("%appointment_time%", nl2br($appointment_time), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%price%", nl2br($price), $booking_confirmed); //Replace patient

			$booking_confirmed = str_replace("%phone_number%", nl2br($phone_number), $booking_confirmed); //Replace patient
			$booking_confirmed = str_replace("%provider%", nl2br($doctor_name), $booking_confirmed); //Replace doctor_name
			$booking_confirmed = str_replace("%user_from%", nl2br($user_from_name), $booking_confirmed); //Replace provider name
			$booking_confirmed = str_replace("%logo%", nl2br($logo), $booking_confirmed); //Replace logo
			$booking_confirmed = str_replace("%link%", nl2br($link_appointment), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%patient_email%", nl2br($patient_email), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%subject%", nl2br($bk_subject), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%idcard%", nl2br($idcard), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%insurance%", nl2br($insurance), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%comment%", nl2br($bk_booking_note), $booking_confirmed); //Replace 
			$booking_approved = str_replace("%id_oder%", nl2br($bk_code), $booking_approved);
			 
			
			
			$blogname 		= wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			
			
			$attachments	  = '';
			$body			 = '';
			$body			.= $email_helper->prepare_email_headers('');
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_confirmed.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			$body 			.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $subject, $booking_confirmed);
			return true;
}

function kt_process_appointment_confirmation_admin_email( $params = '' ) {
			global $current_user;
			extract( $params );

			$email_helper	= new DocDirectProcessEmail();
			
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
		
			$user_from	= get_post_meta($post_id, 'bk_user_from', true);
			$user_to	  = get_post_meta($post_id, 'bk_user_to', true);
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_subject	= get_post_meta($post_id, 'bk_subject', true);
			$bk_booking_note	= get_post_meta($post_id, 'bk_booking_note', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			$bk_code		= get_post_meta($post_id, 'bk_code', true);
			
			$insurance		= get_post_meta($post_id, 'patient_insurers', true);
			$idcard		= get_post_meta($post_id, 'usercard', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$patient_email = $user_from_data->user_email;
			$phone_number = $user_from_data->phone_number;

			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service			 = $booking_services[$bk_service]['title'];
			// $price			 = $booking_services[$bk_service]['price'];
			$price		= get_post_meta($post_id, 'bk_paid_amount', true);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );
			 
			$user_from_name 		= docdirect_get_username($user_from);
			$doctor_name 		= docdirect_get_username($user_to);
			
			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
				$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			}

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';

			$permalink = add_query_arg( 
								array(
									'ref'=>   urlencode( 'bookings' ) ,
									'identity'=>   urlencode( $user_to )   ),
									esc_url( get_permalink($profile_page) 
								) 
							);

			$link_appointment	= esc_url( $permalink );
			
			$subject_default = esc_html__('A new Appointment has received!','docdirect');
			$booking_confirmed_default = 'Hello %doctor_name%<br>

					This is confirmation that you have received a new appointment from %user_from%.
					Please login and approve the appointment:<br>

					<a style="color: #fff; padding: 0 40px; margin: 0 0 15px; font-size: 17px; font-weight: 600; line-height: 60px; border-radius: 8px; background: #5dc560; vertical-align: top; display: inline-block;" href="%link%">Approve Appointment</a><br>

					----------------------------------------------------

					<strong>Appointment Information:</strong><br>

					Patient Name: %user_from%<br>
					Appointment Time/Date: %appointment_time% / %appointment_date%<br>
					Phone Number: %phone_number%<br>
					Email: %patient_email%<br>

					----------------------------------------------------

					Sincerely,
					%logo%';
			
			
			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('appointment_subject');
				$appointment_content = fw_get_db_settings_option('appointment_content');
			}
			
			//set defalt contents
			if( empty( $subject ) ){
				$subject = $subject_default;
			}
			 
			//set defalt contents
			if( empty( $appointment_content ) ){
				$booking_confirmed = $booking_confirmed_default;
			} else{
				$booking_confirmed = $appointment_content;
			}
			
			$logo		   = kt_process_get_logo();
			 
			$booking_confirmed = str_replace("%appointment_date%", nl2br($appointment_date), $booking_confirmed); //Replace patient
			$booking_confirmed = str_replace("%appointment_time%", nl2br($appointment_time), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%price%", nl2br($price), $booking_confirmed); //Replace patient

			$booking_confirmed = str_replace("%phone_number%", nl2br($phone_number), $booking_confirmed); //Replace patient
			$booking_confirmed = str_replace("%doctor_name%", nl2br($doctor_name), $booking_confirmed); //Replace doctor_name
			$booking_confirmed = str_replace("%user_from%", nl2br($user_from_name), $booking_confirmed); //Replace provider name
			$booking_confirmed = str_replace("%logo%", nl2br($logo), $booking_confirmed); //Replace logo
			$booking_confirmed = str_replace("%link%", nl2br($link_appointment), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%patient_email%", nl2br($patient_email), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%subject%", nl2br($bk_subject), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%idcard%", nl2br($idcard), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%insurance%", nl2br($insurance), $booking_confirmed); //Replace 
			$booking_confirmed = str_replace("%comment%", nl2br($bk_booking_note), $booking_confirmed); //Replace 
			$booking_approved = str_replace("%id_oder%", nl2br($bk_code), $booking_approved);
			 
			
			
			$blogname 		= wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_to	= !empty( $user_to_data->user_email ) ? $user_to_data->user_email : $admin_email; 
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			
			
			$attachments	  = '';
			$body			 = '';
			$body			.= $email_helper->prepare_email_headers('');
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_confirmed.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			$body 			.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $subject, $booking_confirmed);
			return true;
}



function kt_process_contact_user_email( $params = '' ) {
			global $current_user;
			extract( $params );

			$user = get_user_by( 'email', $email_to );
			$doctor_id = $user->ID;
			$doctor_name 		= docdirect_get_username($doctor_id);

			$email_helper	= new DocDirectProcessEmail();
			
			$email_subject = !empty( $email_subject ) ? $email_subject : "(" . $bloginfo . ")".esc_html__('Contact Form Received','docdirect');
			
			$contact_default = 'Hello,<br/>

						A person has contact you, description of message is given below.<br/><br/>
						Subject : %subject%<br/>
						Name : %name%<br/>
						Email : %email%<br/>
						Phone Number : %phone%<br/>
						Message : %message%<br/><br/><br/>
						
						Sincerely,<br/>';
			
			
			if (function_exists('fw_get_db_post_option')) {
				$contact_subject = fw_get_db_settings_option('contact_subject');
				$contact_content = fw_get_db_settings_option('contact_content');
			}
			
			//set contents
			if( empty( $contact_content ) ){
				$contact_content = $contact_default;
			}
			
			//email title
			if( !empty( $contact_subject ) ){
				$email_subject = $contact_subject;
			} else{
				$email_subject = $email_subject;
			}
			
			$logo		   = kt_process_get_logo();
			 
			$contact_content = str_replace("%subject%", nl2br($subject), $contact_content); //Replace Subject
			$contact_content = str_replace("%name%", nl2br($name), $contact_content); //Replace Name
			$contact_content = str_replace("%email%", nl2br($email), $contact_content); //Replace email
			$contact_content = str_replace("%phone%", nl2br($phone), $contact_content); //Replace phone
			$contact_content = str_replace("%message%", nl2br($message), $contact_content); //Replace message
			$contact_content = str_replace("%doctor_name%", nl2br($doctor_name), $contact_content); //Replace message
			$contact_content = str_replace("%logo%", nl2br($logo), $contact_content); //Replace message
		
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			$body			.= $email_helper->prepare_email_headers($name);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$contact_content.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			$body	.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $email_subject, $contact_content);
			
			return true;
		 }



function kt_process_appointment_approved_email( $params = '' ) {

		global $current_user;
		extract( $params );
			
		$email_helper	= new DocDirectProcessEmail();

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
		
			$user_from	= get_post_meta($post_id, 'bk_user_from', true);
			$user_to	  = get_post_meta($post_id, 'bk_user_to', true);
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			$bk_code		= get_post_meta($post_id, 'bk_code', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service	= $booking_services[$bk_service]['title'];
			 
			$doctor_name 		= docdirect_get_username($user_to);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );

			$link_review	= get_permalink($post_id);

			$current_userdata	   = get_userdata($user_to);

			$bk_currency 	  = get_post_meta($post_id, 'bk_currency', true);
			$bk_paid_amount   = get_post_meta($post_id, 'bk_paid_amount', true);
			$payment_amount  = $bk_currency.$bk_paid_amount;

			$doctor_email	    = $user_to_data->user_email;
			$bk_location = get_post_meta($post_id,'bk_location', true);
			if ($bk_location != '') {
				$current_practices = get_user_meta($user_to, 'user_practices', true);
				$basics = $current_practices[$bk_location]['basics'];
				$phone_number = $basics['phone_number'];
				$room_floor = $basics['room_floor'];
				$user_url = $basics['user_url'];
				$db_address = $basics['address'];
			}else {
				$user_url	= $current_userdata->data->user_url;
				$phone_number	    = get_user_meta( $user_to, 'phone_number', true);
				$room_floor	    = get_user_meta( $user_to, 'room_floor', true);
				$db_address	    = get_user_meta( $user_to, 'address', true);
			}

			$gmap_link	    = 'http://maps.google.com/?q='.urlencode($db_address);
			
			$subject_default = 'Your Appointment Approved';
			$booking_approved_default = 'Hey %customer_name%!<br/>

						This is confirmation that your booking regarding "%service%" with %provider% has been approved.<br/>
						
						We are waiting you at "%address%" on %appointment_date% at %appointment_time%.<br/><br/>

                        ----------------------------------------------------<br/><br/>

                        Price of Service: $%price%<br/>
                        Appointment Time/Date: %appointment_time% / %appointment_date%<br/>
                        Phone Number: %phone_number%<br/>
                        Email: %doctor_email%<br/>
                        Website: %website%<br/>
                        Floor: %floor%<br/>
                        Location: %location%<br/>
                        Gmaps link: %gmap_link%<br/><br/>

                        ----------------------------------------------------<br/><br/>

                        Once you have finished your appointment, you can post a review and share your experience with others.
                        <a href="%link_review%">Review</a><br/><br/>
						
						Sincerely,<br/>
						%logo%';
				

			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('approve_subject');
				$booking_approved = fw_get_db_settings_option('approve_booking');
			}
			
			//set defalt contents
			if( empty( $subject ) ){
				$subject = $subject_default;
			}
			
			//set defalt title
			if( empty( $booking_approved ) ){
				$booking_approved = $booking_approved_default;
			}
			
			$provider	= '<a href="'.get_author_posts_url($user_to).'"  alt="'.pll__('provider','docdirect').'">'.$provider_name.'</a>';
			$logo		   = kt_process_get_logo();
			
			$booking_approved = str_replace("%location%", nl2br($db_address), $booking_approved);
			$booking_approved = str_replace("%doctor_email%", nl2br($doctor_email), $booking_approved);
			$booking_approved = str_replace("%floor%", nl2br($room_floor), $booking_approved);
			$booking_approved = str_replace("%website%", nl2br($user_url), $booking_approved);
			$booking_approved = str_replace("%phone_number%", nl2br($phone_number), $booking_approved);
			$booking_approved = str_replace("%price%", nl2br($payment_amount), $booking_approved);
			$booking_approved = str_replace("%gmap_link%", nl2br($gmap_link), $booking_approved);

			$booking_approved = str_replace("%link_review%", nl2br($link_review), $booking_approved); //Replace link_appointment

			$booking_approved = str_replace("%customer_name%", nl2br($customer_name), $booking_approved); //Replace Name
			$booking_approved = str_replace("%doctor_name%", nl2br($doctor_name), $booking_approved); //Replace Name
			$booking_approved = str_replace("%service%", nl2br($service), $booking_approved); //Replace service
			$booking_approved = str_replace("%provider%", nl2br($provider), $booking_approved); //Replace provider
			$booking_approved = str_replace("%address%", nl2br($db_address), $booking_approved); //Replace address
			$booking_approved = str_replace("%appointment_date%", nl2br($appointment_date), $booking_approved); //Replace date
			$booking_approved = str_replace("%appointment_time%", nl2br($appointment_time), $booking_approved); //Replace time
			$booking_approved = str_replace("%logo%", nl2br($logo), $booking_approved); //Replace logo
			$booking_approved = str_replace("%id_order%", nl2br($bk_code), $booking_approved); //Replace logo

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			// $body			.= $email_helper->prepare_email_headers($customer_name);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_approved.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			// $body	.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $subject, $booking_approved);
			
			return true;
}

function kt_complete_appointment_email( $params = '' ) {

		
		global $current_user;
		extract( $params );
			
		$email_helper	= new DocDirectProcessEmail();

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
		
			$user_from	= get_post_meta($post_id, 'bk_user_from', true);
			$user_to	  = get_post_meta($post_id, 'bk_user_to', true);
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			$bk_code		= get_post_meta($post_id, 'bk_code', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service	= $booking_services[$bk_service]['title'];
			 
			$doctor_name 		= docdirect_get_username($user_to);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );

			$link_review	= get_permalink($post_id);

			$current_userdata	   = get_userdata($user_to);

			$bk_currency 	  = get_post_meta($post_id, 'bk_currency', true);
			$bk_paid_amount   = get_post_meta($post_id, 'bk_paid_amount', true);
			$payment_amount  = $bk_currency.$bk_paid_amount;

			$doctor_email	    = $user_to_data->user_email;
			$bk_location = get_post_meta($post_id,'bk_location', true);
			if ($bk_location != '') {
				$current_practices = get_user_meta($user_to, 'user_practices', true);
				$basics = $current_practices[$bk_location]['basics'];
				$phone_number = $basics['phone_number'];
				$room_floor = $basics['room_floor'];
				$user_url = $basics['user_url'];
				$db_address = $basics['address'];
			}else {
				$user_url	= $current_userdata->data->user_url;
				$phone_number	    = get_user_meta( $user_to, 'phone_number', true);
				$room_floor	    = get_user_meta( $user_to, 'room_floor', true);
				$db_address	    = get_user_meta( $user_to, 'address', true);
			}

		    $user_from_info = get_userdata( $user_from );
			$username 		= $user_from_info->display_name;

		    $user_info = get_userdata( $user_to );
			$doctor_name 		= $user_info->display_name;
			$user_email 		= $user_info->user_email;
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service	= $booking_services[$bk_service]['title'];
			 
			$doctor_name 		= docdirect_get_username($user_to);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );

			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
				$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			}

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';	

			$permalink = add_query_arg( 
								array(
									'ref'=>   urlencode( 'mybookings' ) ,
									'identity'=>   urlencode( $user_from ),
									'appointment_id'=>   urlencode( $post_id )    ),
									esc_url( get_permalink($profile_page) 
								) 
							);

			$link_review	= esc_url( $permalink );

			$current_userdata	   = get_userdata($user_to);

			$gmap_link	    = 'http://maps.google.com/?q='.urlencode($db_address);
			
			$subject_default = 'Your Appointment Approved';
			$booking_approved_default = 'Hey %customer_name%!<br/>

						This is confirmation that your booking regarding "%service%" with %provider% has been approved.<br/>
						
						We are waiting you at "%address%" on %appointment_date% at %appointment_time%.<br/><br/>

                        ----------------------------------------------------<br/><br/>

                        Price of Service: $%price%<br/>
                        Appointment Time/Date: %appointment_time% / %appointment_date%<br/>
                        Phone Number: %phone_number%<br/>
                        Email: %doctor_email%<br/>
                        Website: %website%<br/>
                        Floor: %floor%<br/>
                        Location: %location%<br/>
                        Gmaps link: %gmap_link%<br/><br/>

                        ----------------------------------------------------<br/><br/>

                        Once you have finished your appointment, you can post a review and share your experience with others.
                        <a href="%link_review%">Review</a><br/><br/>
						
						Sincerely,<br/>
						%logo%';
				

			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('complete_appoinment_subject');
				$booking_approved = fw_get_db_settings_option('complete_appoinment_content');
			}
			
			//set defalt contents
			if( empty( $subject ) ){
				$subject = $subject_default;
			}
			
			//set defalt title
			if( empty( $booking_approved ) ){
				$booking_approved = $booking_approved_default;
			}
			
			$provider	= '<a href="'.get_author_posts_url($user_to).'"  alt="'.esc_html__('provider','docdirect').'">'.$provider_name.'</a>';
			$logo		   = kt_process_get_logo();

			$booking_approved = str_replace("%link_review%", nl2br($link_review), $booking_approved); //Replace link_appointment

			$booking_approved = str_replace("%username%", nl2br($username), $booking_approved); //Replace Name
			$booking_approved = str_replace("%doctor_name%", nl2br($doctor_name), $booking_approved); //Replace Name
			$booking_approved = str_replace("%logo%", nl2br($logo), $booking_approved); //

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			// $body			.= $email_helper->prepare_email_headers($customer_name);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_approved.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			// $body	.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $subject, $booking_approved);
			
			return true;
}


function kt_process_rating_email( $params = '' ) {
		global $current_user;
		$email_helper	= new DocDirectProcessEmail();
			
			extract( $params );

			$detail_rating_string = '';
			foreach ($detail_rating as $key => $value) {
				$detail_rating_string .= ucwords(str_replace('_', ' ', $key)) .': '.$value[0].' '.pll__('Stars').' - '. $value[1].'<br>';
			}

			$rating = $rating.' '.pll__('Stars');

			$subject = 'New rating received!';
			$rating_content_default = 'Hey %name%!<br/>

									A new rating has been received, Detail for rating is given below:
									<br/>
									Rating: %rating%<br/>
									Rating From: %rating_from%<br/>
									Reason: %reason%<br/>
									Comment: <br/>
									%comment_content%
									---------------------------------------<br/>
									You can view this at %link%
									
									Sincerely,<br/>
									MediFinder Team
									%logo%';
				
			
			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('rating_subject');
				$rating_content = fw_get_db_settings_option('rating_content');
			}
			
			//set defalt contents
			if( empty( $rating_content ) ){
				$rating_content = $rating_content_default;
			}
			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
				$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			}

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';	

			$permalink = add_query_arg( 
								array(
									'ref'=>   urlencode( 'dashboard' ) ,
									'identity'=>   urlencode( $current_user->ID ),
									esc_url( get_permalink($profile_page) ),
								) 
							);
			
			$rating_from	= '<a href="'.$link_from.'"  alt="'.pll__('Rating from').'">'.$username_from.'</a>';
			// $link		   = '<a href="'.$permalink.'" alt="'.pll__('User link').'">'.$link_to.'</a>';
			$logo		   = kt_process_get_logo();
			
			$rating_content = str_replace("%rating%", nl2br($rating), $rating_content); //Replace rating
			$rating_content = str_replace("%detail_rating%", nl2br($detail_rating_string), $rating_content); //Replace detail_rating
			$rating_content = str_replace("%reason%", nl2br($reason), $rating_content); //Replace reason
			$rating_content = str_replace("%name%", nl2br($username_to), $rating_content); //Replace name
			$rating_content = str_replace("%comment_content%", nl2br($comment_content), $rating_content); //Replace comment
			
			$rating_content = str_replace("%rating_from%", nl2br($rating_from), $rating_content); //Replace email
			$rating_content = str_replace("%link%", nl2br($permalink), $rating_content); //Replace email
			$rating_content = str_replace("%logo%", nl2br($logo), $rating_content); //Replace logo
			
			
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			
			$attachments	  = '';
			$body			 = '';
			$body			.= $email_helper->prepare_email_headers($name);

			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$rating_content.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			$body 			.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $subject, $rating_content);
			
	return true;
}

function kt_process_email_verification($params = '') {
			global $current_user;
			extract($params);
			$email_helper	= new DocDirectProcessEmail();

			$name = docdirect_get_username($user_identity);

			$subject_default = esc_html__('Account Verification', 'docdirect');
			$email_content_default = 'Hi %name%!<br/>

										<p><strong>Verify Your Account</strong></p>
										<p>You account has created with given below email address:</p>
										<p>Email Address: %account_email%</p>
										<p>If this was a mistake, just ignore this email and nothing will happen.</p>
										<p>To verifiy your account, click below link:</p>
										<p><a style="color: #fff; padding: 0 50px; margin: 0 0 15px; font-size: 20px; font-weight: 600; line-height: 60px; border-radius: 8px; background: #5dc560; vertical-align: top; display: inline-block; font-family: "Work Sans", Arial, Helvetica, sans-serif; text-decoration: none;" href="%link%">Verify</a></p><br />
										Sincerely,<br/>
										%logo%
								';

			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('ave_subject');
				$ave_content = fw_get_db_settings_option('ave_content');
			}

			//set defalt contents
			if (empty($ave_content)) {
				$ave_content = $email_content_default;
			}

			//set defalt subject
			if (empty($subject)) {
				$subject = $subject_default;
			}

			$logo		   = kt_process_get_logo();

			$ave_content = str_replace("%name%", $name, $ave_content); //Replace Name
			$ave_content = str_replace("%account_email%", $email, $ave_content); //Replace email
			$ave_content = str_replace("%link%", $verify_link, $ave_content); //Replace Link
			$ave_content = str_replace("%logo%", nl2br($logo), $ave_content); //Replace logo

			$body = '';
			$body .= $email_helper->prepare_email_headers();

			$body .= '<div style="width: 100%; float: left; padding: 0 0 60px; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;">';
			$body .= '<div style="width: 100%; float: left;">';
			$body .= '<p>' . $ave_content . '</p>';
			$body .= '</div>';
			$body .= '</div>';
			$body 			.= $email_helper->prepare_email_footers();
			wp_mail($email, $subject, $ave_content);
		}



