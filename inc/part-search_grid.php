<?php

function kt_get_current_active_info_doctor($user_id) {

	$current_practices = get_user_meta($user_id, 'user_practices', true);
	$val = array();
	if (!empty($current_practices)) {	
		$array_keys = array_keys($current_practices);
		foreach ($current_practices as $key => $value) {
			if ( $current_practices[$key]['active_location'] == true ) {
				$val = $current_practices[$key];
				break;
			}
		}
	}
	return $val;
}

function kt_get_title_name($user_id="") {
	
	global $current_user, $wp_roles,$userdata,$post;
	if (empty($user_id)) {
		$user_id = $current_user->ID;
	}

	$title_name   		   = get_user_meta( $user_id, 'title_name', true);
	if ($title_name != '') {
		$title_name = $title_name. '. ';
	}
	return $title_name;
}

/**
 * @Authenticate user
 * @return 
 */
if (!function_exists('kt_docdirect_get_everage_rating')) {
	function kt_docdirect_get_everage_rating( $user_id='', $type=false ){
		
		$meta_query_args = array('relation' => 'AND',);
		$meta_query_args[] = array(
								'key' 	   => 'user_to',
								'value' 	 => $user_id,
								'compare'   => '=',
								'type'	  => 'NUMERIC'
							);
								
		$args 		= array('posts_per_page'   => -1, 
							'post_type'		 => 'docdirectreviews',
							'post_status'	   => 'publish',
                    		'lang' => '', 
							'orderby' 		   => 'meta_value_num',
							'meta_key' 	 => 'user_rating',
							'order' 		=> 'ASC',
						);
		
		$args['meta_query'] = $meta_query_args;
				
		$average_rating	= 0;
		$average_count	 = 0;
		$query 		= new WP_Query($args);
		
		$rate_1	= array('rating' => 0, 'total'=>0);
		$rate_2	= array('rating' => 0, 'total'=>0);
		$rate_3	= array('rating' => 0, 'total'=>0);
		$rate_4	= array('rating' => 0, 'total'=>0);
		$rate_5	= array('rating' => 0, 'total'=>0);
		
		//fw_print($query);
		$sum = 0;
		while($query->have_posts()) : $query->the_post();
			global $post;
			$user_rating = fw_get_db_post_option($post->ID, 'user_rating', true);
			$user_from = fw_get_db_post_option($post->ID, 'user_from', true);
			$user_name = fw_get_db_post_option($post->ID, 'user_name', true);
			$review_date = fw_get_db_post_option($post->ID, 'review_date', true);
			$user_data 	  = get_user_by( 'id', intval( $user_from ) );
			
            
            $user_rating = json_decode($user_rating, true);
            if ($type == false) {
                $sum += array_sum($user_rating)/5;
            	$user_rating = floor(array_sum($user_rating)/5);
            } else {
            	$user_rating = $user_rating[$type];
            }
            

			if( $user_rating == 1 ){
				$rate_1['rating']   = $rate_1['rating']+$user_rating;   
				$rate_1['total']	= $rate_1['total']+ 1;   
			} else if( $user_rating == 2 ){
				$rate_2['rating']   = $rate_2['rating']+$user_rating;   
				$rate_2['total']	= $rate_2['total']+ 1;   
			} else if( $user_rating == 3 ){
				$rate_3['rating']   = $rate_3['rating']+$user_rating;   
				$rate_3['total']	= $rate_3['total']+ 1;   
			} else if( $user_rating == 4 ){
				$rate_4['rating']   = $rate_4['rating']+$user_rating;   
				$rate_4['total']	= $rate_4['total']+ 1;   
			} else if( $user_rating == 5 ){
				$rate_5['rating']   = $rate_5['rating']+$user_rating;   
				$rate_5['total']	= $rate_5['total'] + 1;   
			}

			$average_rating	= $average_rating + $user_rating;
			$average_count++;
		
		endwhile; wp_reset_postdata();

		$data['reviews']	= 0;
		$data['percentage']	= 0;
		if( isset( $average_rating ) && $average_rating > 0 ){
			$data['average_rating']	= $average_rating/$average_count;
			$data['total_average_rating']	= $sum/$average_count;
			$data['reviews']	= $average_count;
			$data['percentage'] = ( $average_rating/ $average_count)*20;
			$data['by_ratings']	= array($rate_1,$rate_2,$rate_3,$rate_4,$rate_5);
		}
		
		return $data;
	}
	
}

function kt_get_user_insurers($user_id) {
	?>
                    <?php 
					$db_insurer	= get_user_meta( $user_id, 'user_profile_insurers', true);
                    if( !empty( $db_insurer ) ) { 
                    	if (function_exists('kt_read_insurer')) {
		    				$list_insurer = kt_read_insurer();
						}
                    	echo '<li>';
                    	$db_insurer = array_slice($db_insurer, 0, 12);
						foreach( $db_insurer as $key => $value ){
                            $term_data = get_term_by('name', $value, 'insurer');
							// $taxonomy_image_url = get_option('z_taxonomy_image'.$term_data->term_id);
                        	$bg_url = ($list_insurer[$term_data->term_id][1]!='') ? $list_insurer[$term_data->term_id][1] : $sample_bg_url;
							$img = '<img width="150" height="150" src="'.$bg_url.'">';
                     ?>
                        <?php echo $img;?>
                      <?php }
                    	echo '</li>';
                    }?>
<?php
}

function kt_is_company($user_id) {
	$db_directory_type   = get_user_meta( $user_id, 'directory_type', true);
	$terms = get_the_terms($db_directory_type, 'group_label');
	$list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
	// $current_group_label_slug = $terms[0]->slug;
	$user_premium = get_user_meta($user_id , 'user_premium' , true);
	if ($icon == false && $echo==true) { $output = '<div class="wrap_tag">';}
	if( in_array('company', $list_terms) ||
            in_array('medical-centre', $list_terms) ||
            in_array('hospital-type', $list_terms) ||
            in_array('scans-testing', $list_terms)
    ) {return true;}
	else {
		return false;
	}
}

function kt_get_tag_company($user_id,$icon='',$echo=true) {
	$db_directory_type   = get_user_meta( $user_id, 'directory_type', true);
	$terms = get_the_terms($db_directory_type, 'group_label');
	$list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
	// $current_group_label_slug = $terms[0]->slug;
	$user_premium = get_user_meta($user_id , 'user_premium' , true);
	$icon_flag = ($icon == true) ? '<i  class="fa fa-flag"></i>' : '' ;
	if ($icon == false && $echo==true) { $output = '<div class="wrap_tag">';}
	if( in_array('company', $list_terms) ||
            in_array('medical-centre', $list_terms) ||
            in_array('hospital-type', $list_terms) ||
            in_array('scans-testing', $list_terms)
    ) {
		$tit = get_the_title($db_directory_type);
		if ($icon == true) {
			$output .= '<li class="tg-varified"><a class="company_tag" href="javascript:;">'.$icon_flag. $tit . '</a></li>';
		}else {
			$output .=  '<span class="company_tag">'.$icon_flag. $tit . '</span>';
		}
	}else {
		if ($icon == true) {
			$output .=  '<li class="tg-varified"><a class="company_tag" href="javascript:;">'.$icon_flag. pll__('Professional'). '</a></li>';
		}else {
			$output .=  '<span class="company_tag">'.$icon_flag. pll__('Professional'). '</span>';
		}
	}
	if ($icon == false && $echo==true) { $output .=  '</div>';}
	if( $echo == true ){
		echo $output;
	} else{
		return $output;
	}
}


function kt_custom($user_id) {
	global $wp_query,$current_user;

	$db_directory_type	 = get_user_meta( $user_id, 'directory_type', true);
    $terms = get_the_terms($db_directory_type, 'group_label');
    $list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
    // $current_group_label_slug = $terms[0]->slug;
    $user_premium = get_user_meta($user_id , 'user_premium' , true);
    if( in_array('company', $list_terms) ||
            in_array('medical-centre', $list_terms) ||
            in_array('hospital-type', $list_terms) ||
            in_array('scans-testing', $list_terms)
    ) {
        $current_option = get_option( 'company_'.$user_premium, true );
    }else {
        $current_option = get_option( $user_premium, true );
    } 	
		$privacy		= docdirect_get_privacy_settings($user_id); //Privacy settings
		if( !empty( $privacy['appointments'] )
						  && 
						  $privacy['appointments'] == 'on'
						  && $current_option['patient_bookings'] != ''
					 ) {
			echo '<div class="quickbooking">';

			$today = current_time( 'timestamp' );
			// $tomorrow_time	= strtotime("+1 days", $today);

			/*$day		= strtolower(date('D'));
			$current_date_string	= date('M d, l');
			$current_date	= date('Y-m-d');
			$slot_date	   = date('Y-m-d');*/

			$current_day	=  strtotime(date('Y-m-d', $today));
			$tomorrow_time	= strtotime("+1 days", $current_day);

			$two_day = array($current_day,$tomorrow_time);

		    $output = '';
			foreach ($two_day as $key => $value) {
				$output .= '<div class="item">';

				$name_of_day = pll__('Today');
				if ($key == 1) {
					$name_of_day = date("l", $value);
				}
				ob_start();?>
		        <div class="tg-doctimeslot title">
		            <div class="tg-box">
		                <div class="tg-radio">
		                    <label for=""><?php echo $name_of_day;?></label>
		                </div>
		            </div>
		        </div>
				<?php $output .= ob_get_clean();
				$output .= '</div>';
			}
			$output .= '<div class="wrap_item">';
		    	$dem = 0;
			foreach ($two_day as $now_day) {
				$output .= '<div class="item">';
				$current_date	= date('Y-m-d', $now_day);
				$slot_date	   = date('Y-m-d', $now_day);

				$default_slots	= array();
				$default_slots = get_user_meta($user_id , 'default_slots' , false);
				$time_format   = get_option('time_format');

				//Get booked Appointments
				$year  	  = date_i18n('Y',strtotime($slot_date));
				$month 	  = date_i18n('m',strtotime($slot_date));
				$day_no   = date_i18n('d',strtotime($slot_date));

				$start_timestamp = strtotime($year.'-'.$month.'-'.$day_no.' 00:00:00');
				$end_timestamp = strtotime($year.'-'.$month.'-'.$day_no.' 23:59:59');

					$args 		= array('posts_per_page' => -1, 
										 'post_type' => 'docappointments', 
										 'post_status' => 'publish', 
										 'ignore_sticky_posts' => 1,
										 'meta_query' => array(
												array(
													'key'     => 'bk_timestamp',
													'value'   => array( $start_timestamp, $end_timestamp ),
													'compare' => 'BETWEEN'
												),
												array(
													'key'     => 'bk_user_to',
													'value'   => $user_id,
													'compare' => '='
												),
												array(
													'key'     => 'bk_status',
													'value' => array('approved', 'pending'),
													'compare' => 'IN'
												)
											)
										);
					$query 		= new WP_Query($args);
					$count_post = $query->post_count;
					$appointments_array	= array();
					while($query->have_posts()) : $query->the_post();
						global $post;
						
						$bk_category      = get_post_meta($post->ID, 'bk_category',true);
						$bk_service       = get_post_meta($post->ID, 'bk_service',true);
						$bk_booking_date  = get_post_meta($post->ID, 'bk_booking_date',true);
						$bk_slottime 	  = get_post_meta($post->ID, 'bk_slottime',true);
						$bk_subject       = get_post_meta($post->ID, 'bk_subject',true);
						$bk_username      = get_post_meta($post->ID, 'bk_username',true);
						$bk_userphone 	 = get_post_meta($post->ID, 'bk_userphone',true);
						$bk_useremail     = get_post_meta($post->ID, 'bk_useremail',true);
						$bk_booking_note  = get_post_meta($post->ID, 'bk_booking_note',true);
						$bk_payment       = get_post_meta($post->ID, 'bk_payment',true);
						$bk_user_to       = get_post_meta($post->ID, 'bk_user_to',true);
						$bk_timestamp     = get_post_meta($post->ID, 'bk_timestamp',true);
						$bk_status        = get_post_meta($post->ID, 'bk_status',true);
						$bk_user_from     = get_post_meta($post->ID, 'bk_user_from',true);
						
						$appointments_array[$bk_slottime]['bk_category'] = $bk_category;
						$appointments_array[$bk_slottime]['bk_service'] = $bk_service;
						$appointments_array[$bk_slottime]['bk_booking_date'] = $bk_booking_date;
						$appointments_array[$bk_slottime]['bk_slottime'] = $bk_slottime;
						$appointments_array[$bk_slottime]['bk_subject'] = $bk_subject;
						$appointments_array[$bk_slottime]['bk_username'] = $bk_username;
						$appointments_array[$bk_slottime]['bk_userphone'] = $bk_userphone;
						$appointments_array[$bk_slottime]['bk_useremail'] = $bk_useremail;
						$appointments_array[$bk_slottime]['bk_booking_note'] = $bk_booking_note;
						$appointments_array[$bk_slottime]['bk_user_to'] = $bk_user_to;
						$appointments_array[$bk_slottime]['bk_timestamp'] = $bk_timestamp;
						$appointments_array[$bk_slottime]['bk_status'] = $bk_status;
						$appointments_array[$bk_slottime]['bk_user_from'] = $bk_user_from;
						
					endwhile; wp_reset_postdata(); 

				//Custom Slots
				$custom_slot_list	= kt_docdirect_custom_timeslots_filter($default_slots,$user_id);

				$formatted_date = date_i18n('Ymd',strtotime($slot_date));
				$day_name 	   = strtolower(date('D',strtotime($slot_date)));
				
				if (  isset($custom_slot_list[$formatted_date]) 
					&& 
					  !empty($custom_slot_list[$formatted_date])
				){
					$todays_defaults = is_array($custom_slot_list[$formatted_date]) ? $custom_slot_list[$formatted_date] : json_decode($custom_slot_list[$formatted_date],true);
					
					$todays_defaults_details = is_array($custom_slot_list[$formatted_date.'-details']) ? $custom_slot_list[$formatted_date.'-details'] : json_decode($custom_slot_list[$formatted_date.'-details'],true);
				
				} else if ( isset($custom_slot_list[$formatted_date]) 
							&& 
							empty($custom_slot_list[$formatted_date])
				){
					$todays_defaults = false;
					$todays_defaults_details = false;
				} else if (  isset($custom_slot_list[$day_name]) 
							 && 
							 !empty($custom_slot_list[$day_name])
				){
					$todays_defaults = $custom_slot_list[$day_name];
					$todays_defaults_details = $custom_slot_list[$day_name.'-details'];
				} else {
					$todays_defaults = false;
					$todays_defaults_details = false;
				}

		        if( !empty( $todays_defaults ) ) {
		        foreach( $todays_defaults as $key => $value ){
		            $time = explode('-',$key);

		            $b_date = $current_date. ' ' .$time[0];
		            if( strtotime($b_date) < $today ) {
				        // unset($todays_defaults[$key]);
				    }

		        }
		    	}
		    	// $todays_defaults = array_slice($todays_defaults, 0, 4);
		        if( !empty( $todays_defaults ) ) {
		        	if (count($todays_defaults) > 4) {
			    		// $output .= '<div class="force-scroll">';
			    		$output .= '<div>';
		        	}else {
			    		$output .= '<div>';
		        	}
			        foreach( $todays_defaults as $key => $value ){
			            $time = explode('-',$key);
			            $b_date = $current_date. ' ' .$time[0];
			            // echo date('d m Y g:i:A', strtotime($b_date)).'<br>';
			            // echo date('d m Y g:i:A', $today);
			            
			            if( ( !empty( $appointments_array[$key]['bk_slottime'] ) && $appointments_array[$key]['bk_slottime'] == $key )
			            	|| strtotime($b_date) < $today
			            ){
			                $slotClass	= 'tg-booked';
			                $slot_status	= 'disabled';
			            } else{
			                $slotClass	= 'tg-available';
			                $slot_status	= '';
			            }
					ob_start();?>
			        <div class="tg-doctimeslot <?php echo sanitize_html_class( $slotClass );?>">
			            <div class="tg-box">
			                <div class="tg-radio">
			                    <!-- <input <?php echo esc_attr( $slot_status );?> id="<?php echo esc_attr( $key );?>" value="<?php echo esc_attr( $key );?>" type="radio" name="slottime"> -->
			                    <label for="<?php echo esc_attr( $key );?>"><?php echo date($time_format,strtotime('2016-01-01 '.$time[0]) );?>&nbsp;-&nbsp;<?php echo date($time_format,strtotime('2016-01-01 '.$time[1]) );?></label>
			                </div>
			            </div>
			        </div>
					<?php $output .= ob_get_clean();
			        }
				$output .= '</div>';
			    }else {
			    	$dem++;
			    	$output .= '<div class="tg-doctimeslot"></div>';
			    }
				$output .= '</div>';
			}
			    // echo $dem;
			$output .= '</div>';

			if ($dem<2) {
				echo $output;
				echo '<a class="view_full quick_button" data-user_id="'.$user_id.'" href="javascript:;">'.pll__('View full Calendar').'</a>';
			}else {
				$avai = kt_get_next_available($user_id);
				if (!empty($avai)) {
					echo '<a class="tg-btn tg-btn-lg view_full quick_button next_avai" data-date="'.date('Y-m-d', $avai).'" data-user_id="'.$user_id.'" href="javascript:;">'.pll__('Next Available').' '.date('M d', $avai).' <i class="fa fa-forward"></i></a>';
				}else {
					if (is_user_logged_in()) {
						echo '<button class="tg-btn tg-btn-lg make-request-btn" type="button" data-user_id="'.$user_id.'"><i class="fa fa-envelope"></i>'.pll__('Request Appointment').'</button>';
					}else {
						echo '<button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-envelope"></i>'. pll__("Request Appointment").'</button>';
					}
					/*echo '<button class="tg-btn tg-btn-lg make-request-btn" type="button" data-user_id="'.$user_id.'"><i class="fa fa-envelope"></i>'.pll__('Request Appointment').'</button>';*/
				}
			}
			echo '</div>';
		}else {
			if (is_user_logged_in()) {
				echo '<button class="tg-btn tg-btn-lg make-request-btn" type="button" data-user_id="'.$user_id.'"><i class="fa fa-envelope"></i>'.pll__('Request Appointment').'</button>';
			}else {
				echo '<button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-envelope"></i>'. pll__("Request Appointment").'</button>';
			}
			// echo '<button class="tg-btn tg-btn-lg make-request-btn" type="button" data-user_id="'.$user_id.'"><i class="fa fa-envelope"></i>'.pll__('Request Appointment').'</button>';
		}
}

function kt_custom2($user_id) {

	$db_directory_type	 = get_user_meta( $user_id, 'directory_type', true);
    $terms = get_the_terms($db_directory_type, 'group_label');
    $list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
    $current_group_label_slug = $terms[0]->slug;
    $user_premium = get_user_meta($user_id , 'user_premium' , true);
    if( in_array('company', $list_terms) ||
            in_array('medical-centre', $list_terms) ||
            in_array('hospital-type', $list_terms) ||
            in_array('scans-testing', $list_terms)
    ) {
        $current_option = get_option( 'company_'.$user_premium, true );
    }else {
        $current_option = get_option( $user_premium, true );
    } 	
	echo '<div class="quickbooking">';
	$avai = kt_get_next_available($user_id);
	$privacy		= docdirect_get_privacy_settings($user_id); //Privacy settings
	if (!empty($avai) && !empty( $privacy['appointments'] ) && $privacy['appointments'] == 'on' && $current_option['patient_bookings'] != '') {
		if (is_user_logged_in()) {
			echo '<a class="tg-btn tg-btn-lg view_full quick_button next_avai" data-date="'.date('Y-m-d', $avai).'" data-user_id="'.$user_id.'" href="javascript:;">'.pll__('Next Available ').' '.date('M d', $avai).' <i class="fa fa-forward"></i></a>';
		}else {
			echo '<button class="tg-btn tg-btn-lg quick_button next_avai" type="button" data-toggle="modal" data-target=".tg-user-modal">'. pll__("Next Available ").' '.date("M d", $avai).' <i class="fa fa-forward"></i></button>';
		}
	}else {
		if (is_user_logged_in()) {
			echo '<button class="tg-btn tg-btn-lg make-request-btn" type="button" data-user_id="'.$user_id.'"><i class="fa fa-envelope"></i>'.pll__('Request Appointment').'</button>';
		}else {
			echo '<button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-envelope"></i>'. pll__("Request Appointment").'</button>';
		}
			// echo '<button class="tg-btn tg-btn-lg make-request-btn" type="button" data-user_id="'.$user_id.'"><i class="fa fa-envelope"></i>'.pll__('Request Appointment').'</button>';
	}
	echo '</div>';
}

function kt_next_avai_button($user_id='', $slot_date = '') {
	$avai = kt_get_next_available($user_id);
	if (strtotime($slot_date) > $avai) {
		$avai = kt_get_next_available($user_id, $slot_date);
		if ($avai != '') {
			echo '<a class="tg-btn tg-btn-lg quick_button next_avai" data-date="'.date('Y-m-d', $avai).'" data-user_id="'.$user_id.'" href="javascript:;">'.pll__('Next Available').' '.date('M d', $avai).' <i class="fa fa-forward"></i></a>';
		}else {
			echo '<button class="tg-btn tg-btn-lg make-request-btn" type="button" data-user_id="'.$user_id.'"><i class="fa fa-envelope"></i>'.pll__('Request Appointment').'</button>';
		}
	}else {
		echo '<a class="tg-btn tg-btn-lg quick_button next_avai" data-date="'.date('Y-m-d', $avai).'" data-user_id="'.$user_id.'" href="javascript:;">'.pll__('Next Available ').' '.date('M d', $avai).' <i class="fa fa-forward"></i></a>';
	}
}

function kt_quickbooking($user_id, $fixed_date = null) {
	global $wp_query,$current_user;
	$user = get_userdata($user_id);
                                $avatar = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
	?>
		<button type="button" data-dismiss="modal"><i class="fa fa-close"></i></button>
          <div class="tg-online-booking tg-listview-v3 user-section-style1">
            <div class="tg-doctor-profile online-booking" data-id="<?php echo esc_attr( $user_id );?>" data-slot_date="<?php echo esc_attr( $fixed_date );?>">
            	<div class="top_doctor">
		          	<div class="tg-userbanner-content1">
                        <a href="<?php echo get_author_posts_url($user->ID); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
                        <div>
			                <h4><?php echo esc_attr( $user->first_name.' '.$user->last_name );?></h4>
			                <?php if( !empty( $user->tagline ) ) {?>
			                <span><?php echo esc_attr( $user->tagline );?></span>
			                <?php }?>
		                </div>
		            </div>
		            <a target="_blank" href="<?php echo get_author_posts_url($user->ID); ?>" class="pull-right"><i class="fa fa-user-md"></i><span></s><?php pll_e('View Profile');?></span></a>
            	</div>
		        <div class="tg-appointmenttabcontent" data-id="<?php echo esc_attr( $user->ID );?>">
		        </div>
                <?php kt_docdirect_get_booking_step_two_calender($user_id,'echo', $fixed_date);?>
            </div>
          </div>
          <form method="get" action="<?php echo get_author_posts_url($user->ID); ?>">
          	<input type="hidden" name="booking_date" value="">
          	<input type="hidden" name="booking_time" value="">
          </form>
	<?php
}



function kt_ajax_load_quickbooking(){
	global $current_user;

  	$user_id = isset( $_POST['user_id'] ) ? esc_sql( $_POST['user_id'] ) : '';  
  	$fixed_date = isset( $_POST['fixed_date'] ) ? $_POST['fixed_date'] : '';  	
	ob_start();

	kt_quickbooking($user_id, $fixed_date);
  	
	$json['userlink']	 = get_author_posts_url($user_id);;
	$json['data']	 = ob_get_clean();
	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;

}
add_action('wp_ajax_quickbooking', 'kt_ajax_load_quickbooking');
add_action('wp_ajax_nopriv_quickbooking', 'kt_ajax_load_quickbooking');

function kt_get_next_available($user_id, $from_date = "") {
	if ($from_date == "") {
		$today = current_time( 'timestamp' );
		$current_day	=  strtotime(date('Y-m-d', $today));
	}else{
		$current_day	=  strtotime($from_date);
	}

	$args = array('posts_per_page' => -1, 
				 'post_type' => 'docappointments', 
				 'post_status' => 'publish', 
				 'ignore_sticky_posts' => 1,
				 'meta_query' => array(
						array(
							'key'     => 'bk_timestamp',
							'value'   => $current_day,
							'compare' => '>='
						),
						array(
							'key'     => 'bk_user_to',
							'value'   => $user_id,
							'compare' => '='
						),
						array(
							'key'     => 'bk_status',
							'value' => array('approved', 'pending'),
							'compare' => 'IN'
						)
					)
				);
	$query 		= new WP_Query($args);
	$posts = $query->posts;
	$posts_array	= array();
	foreach($posts as $post) {
		$bk_timestamp = get_post_meta($post->ID, 'bk_timestamp', true);
		$bk_slottime = get_post_meta($post->ID, 'bk_slottime',true);
	    $posts_array[$post->ID] = array($bk_timestamp, $bk_slottime);
	}

	$i = 0;
	while ($i <= 364) {
	    $i++;
		$next_day	= strtotime("+".$i." days", $current_day);

		$current_date	= date('Y-m-d', $next_day);

			$slot_date	   = date('Y-m-d', $next_day);

			$default_slots	= array();
			$default_slots = get_user_meta($user_id , 'default_slots' , false);
			$time_format   = get_option('time_format');

			//Get booked Appointments
			$year  	  = date_i18n('Y',strtotime($slot_date));
			$month 	  = date_i18n('m',strtotime($slot_date));
			$day_no   = date_i18n('d',strtotime($slot_date));

			$start_timestamp = strtotime($year.'-'.$month.'-'.$day_no.' 00:00:00');
			$end_timestamp = strtotime($year.'-'.$month.'-'.$day_no.' 23:59:59');

			$appointments_array	= array();
			foreach ( $posts_array as $post_id => $bk_meta ) {
				// $bk_timestamp = get_post_meta($post_id, 'bk_timestamp', true);
				if ($bk_meta[0] >= $start_timestamp && $bk_meta[0] <= $end_timestamp) {					
					
					// $bk_slottime 	  = get_post_meta($post_id, 'bk_slottime',true);
					
					$appointments_array[$bk_meta[1]]['bk_slottime'] = $bk_meta[1];
				}
			}

			//Custom Slots
			$custom_slot_list	= kt_docdirect_custom_timeslots_filter($default_slots,$user_id);

			$formatted_date = date_i18n('Ymd',strtotime($slot_date));
			$day_name 	   = strtolower(date('D',strtotime($slot_date)));
			
			if (  isset($custom_slot_list[$formatted_date]) 
				&& 
				  !empty($custom_slot_list[$formatted_date])
			){
				$todays_defaults = is_array($custom_slot_list[$formatted_date]) ? $custom_slot_list[$formatted_date] : json_decode($custom_slot_list[$formatted_date],true);
				
				$todays_defaults_details = is_array($custom_slot_list[$formatted_date.'-details']) ? $custom_slot_list[$formatted_date.'-details'] : json_decode($custom_slot_list[$formatted_date.'-details'],true);
			
			} else if ( isset($custom_slot_list[$formatted_date]) 
						&& 
						empty($custom_slot_list[$formatted_date])
			){
				$todays_defaults = false;
				$todays_defaults_details = false;
			} else if (  isset($custom_slot_list[$day_name]) 
						 && 
						 !empty($custom_slot_list[$day_name])
			){
				$todays_defaults = $custom_slot_list[$day_name];
				$todays_defaults_details = $custom_slot_list[$day_name.'-details'];
			} else {
				$todays_defaults = false;
				$todays_defaults_details = false;
			}

	        if( !empty( $todays_defaults ) ) {
	        	$length = count($todays_defaults);
	        	// var_dump($length);
	        	foreach( $todays_defaults as $key => $value ){
		            $time = explode('-',$key);
		            $b_date = $current_date. ' ' .$time[0];
		            
		            if( ( !empty( $appointments_array[$key]['bk_slottime'] ) && $appointments_array[$key]['bk_slottime'] == $key )
		            	|| strtotime($b_date) < $today
		            ){
		                $length--;
		            }
			    }
	        	// var_dump($length);
	        	if ($length > 0) {	        	
					$avai = $next_day;
					break;
	        	}
			}

	}
	return $avai;
	// echo date('Y-M-d', $avai);

}

function kt_button_upgrade_premium() {

	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;

	$dir_profile_page = '';
	if (function_exists('fw_get_db_settings_option')) {
        $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
    }
	$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
	
	$invoices_url = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity,true);
	?>
	<a class="doc-btn btn-primary" href="<?php echo $invoices_url;?>"><?php pll_e('Upgrade To Premium');?></a>
	<?php
}


/**
 * @Custom Time Slots
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_custom_timeslots_filter' ) ) {
	function kt_docdirect_custom_timeslots_filter( $default_slots=false,$user_id = false){
		
		$default_slots	= $default_slots[0];
		//print_r($default_slots);
		$custom_timeslots_array = array();
		$custom_slots = get_user_meta($user_id , 'custom_slots' , true);
		
		$custom_slots = json_decode($custom_slots,true);
	
		if (!empty($custom_slots)):
	
			$custom_timeslots_array = docdirect_prepare_seprate_array($custom_slots);
			
			//print_r($custom_timeslots_array);
			
			foreach($custom_timeslots_array as $key => $value):
	
				if ($value['cus_start_date']):
	
					$formatted_date = date_i18n('Ymd',strtotime($value['cus_start_date']));
					$formatted_end_date = date_i18n('Ymd',strtotime($value['cus_end_date']));
	
					if (!$value['cus_end_date']){
						// Single Date
						if ( isset( $value['disable_appointment'] )
							 &&
							 $value['disable_appointment'] === 'disable'
						){
							// Time slots disabled
							$default_slots[$formatted_date] = array();
							$default_slots[$formatted_date.'-details'] = array();
						} else {
							// Add time slots to this date
							$default_slots[$formatted_date] = $value['custom_time_slots'];
							$default_slots[$formatted_date.'-details'] = !empty($value['custom_time_slot_details']) ? $value['custom_time_slot_details'] : array();
						}
					} else {
						// Multiple Dates
						$tempDate = $formatted_date;
						do {
							if ( isset( $value['disable_appointment'] )
								 &&
								 $value['disable_appointment'] === 'disable'
							){
								// Time slots disabled
								$default_slots[$tempDate] = array();
								$default_slots[$tempDate.'-details'] = array();
							} else {
								// Add time slots to this date
				                if (array_key_exists($tempDate, $default_slots)) {
				                  $khoa_1 = array_shift(array_keys($value['custom_time_slots']));
				                  $khoa_2 = array_shift(array_keys($value['custom_time_slot_details']));
				                  $default_slots[$tempDate][$khoa_1] = 0;
				                  $default_slots[$tempDate.'-details'][$khoa_2] = array();
				                }else { 
									$default_slots[$tempDate] = $value['custom_time_slots'];
									$default_slots[$tempDate.'-details'] = !empty($value['custom_time_slot_details']) ? $value['custom_time_slot_details'] : array();
								}
							}
							$tempDate = date_i18n('Ymd',strtotime($tempDate . ' +1 day'));
						} while ($tempDate <= $formatted_end_date);
					}

				endif;
	
			endforeach;
	
		endif;
	
		return $default_slots;
	}
}

//rewrite search filter
add_action( 'after_setup_theme', 'kt_rewrite_theme_search_filters', 5 );

function kt_rewrite_theme_search_filters() {

	remove_action( 'docdirect_search_filters', 'docdirect_search_filters' );
	add_action( 'docdirect_search_filters', 'kt_docdirect_search_filters' );

}

/**
 * @Sort by distance
 * @return array
 */
if (!function_exists('kt_docdirect_search_filters')) {

	function kt_docdirect_search_filters() {
	$zip_code	= isset( $_GET['zip'] ) ? $_GET['zip'] : '';
	$by_name	 = isset( $_GET['by_name'] ) ? $_GET['by_name'] : '';
	$args = array('posts_per_page' => '-1', 
				   'post_type' => 'directory_type', 
				   'post_status' => 'publish',
				   'suppress_filters' => false
			);
	
	$cust_query = get_posts($args);
	
	
	$dir_search_page 		= fw_get_db_settings_option('dir_search_page');
	$dir_search_pagination  = fw_get_db_settings_option('dir_search_pagination');
	$dir_longitude 			= fw_get_db_settings_option('dir_longitude');
	$dir_latitude 			= fw_get_db_settings_option('dir_latitude');
	$google_key 			= fw_get_db_settings_option('google_key');
	
	$dir_keywords 			= fw_get_db_settings_option('dir_keywords');
	$zip_code_search 		= fw_get_db_settings_option('zip_code_search');
	$dir_location 			= fw_get_db_settings_option('dir_location');
	$dir_radius 			= fw_get_db_settings_option('dir_radius');
	$language_search 		= fw_get_db_settings_option('language_search');
	$dir_search_cities 		= fw_get_db_settings_option('dir_search_cities');
	
	
	$dir_longitude			= !empty( $dir_longitude ) ? $dir_longitude : '-0.1262362';
	$dir_latitude		 	= !empty( $dir_latitude ) ? $dir_latitude : '51.5001524';
	
	$insurer  	   = !empty( $_GET['insurer'] ) ? $_GET['insurer'] : '';
	$insurance  	   = !empty( $_GET['insurance'] ) ? $_GET['insurance'] : '';
	$photos  	   	   = !empty( $_GET['photos'] ) ? $_GET['photos'] : '';
	$appointments      = !empty( $_GET['appointments'] ) ? $_GET['appointments'] : '';
	$city      		   = !empty( $_GET['city'] ) ? $_GET['city'] : '';
	
	
	if( isset( $dir_search_page[0] ) && !empty( $dir_search_page[0] ) ) {
		$search_page 	 = get_permalink((int)$dir_search_page[0]);
	} else{
		$search_page 	 = '';
	}
	
	$languages_array	= docdirect_prepare_languages();//Get Language Array

	?>
	<div class="search-filters-wrap">
       <div class="doc-widget doc-widgetsearch">
          <!-- <div class="doc-widgetheading">
            <h2><?php //esc_html_e('Narrow your search','docdirect');?></h2>
          </div> -->
				<ul class="nav nav-pills">
				  <li class="active"><a data-toggle="tab" href="#byspeacialty"><?php pll_e( 'Specialty Search' );?></a></li>
				  <li><a data-toggle="tab" href="#bydoctor"><?php pll_e( 'Name Search' );?></a></li>
				</ul>
          <div class="doc-widgetcontent">
              <fieldset>

				<div class="tab-content">
				  <div id="byspeacialty" class="tab-pane fade in active">
				  	<div class="row">
				  		<div class="col-md-10">
			                <?php 
                                if (function_exists('kt_direct_search')) {
                                    kt_direct_search();
                                }
                            ?>
			                        
			                <?php if( isset( $dir_search_insurance ) && $dir_search_insurance === 'enable' ){?>
			                <div class="form-group">
			                  <div class="doc-select">
			                    <select name="insurance" class="chosen-select">
			                        <option value=""><?php pll_e('Select insurance','docdirect');?></option>
			                        <?php docdirect_get_term_options($insurance,'insurance');?>
			                    </select>
			                  </div>
			                </div>
			                <?php }?>
			                <?php if( isset( $dir_location ) && $dir_location === 'enable' ){?>
			                  <div class="form-group">
			                    <div class="tg-inputicon tg-geolocationicon tg-angledown">
			                        <?php if (function_exists('kt_docdirect_locateme_snipt')) {
			                        	 kt_docdirect_locateme_snipt();
			                        }?>
			                        <script>
			                            jQuery(document).ready(function(e) {
			                                //init
			                                jQuery.docdirect_init_map(<?php echo esc_js( $dir_latitude );?>,<?php echo esc_js( $dir_longitude );?>);
			                            });
			                        </script> 
			                     </div>
			                  </div>
			                <?php }?>
			                <?php if( !empty( $zip_code_search ) && $zip_code_search === 'enable' ){?>
			                  <div class="form-group">
			                    <input type="text" class="form-control" value="<?php echo esc_attr( $zip_code );?>" name="zip" placeholder="<?php esc_html_e('Search users by zip code','docdirect');?>">
			                  </div>
			                <?php }?>
			                <div class="form-group toggle_filter">
			                  	<a class="open" href="javascript:;">
			                  		<span class="close_filters"><?php pll_e('Less Filters','docdirect');?></span>
			                  		<span class="more_filters"><?php pll_e('Add Filters','docdirect');?></span>
			                  		<i class="fa fa-plus"></i>
			                  	</a>
			                </div>
			                <?php if( !empty( $dir_search_cities ) && $dir_search_cities === 'enable' ){?>
			                <div class="form-group">
			                    <div class="doc-select">
			                      <select name="city" class="chosen-select">
			                        <option value=""><?php pll_e('Select city','docdirect');?></option>
			                        <?php docdirect_get_term_options($city,'locations');?>
			                      </select>
			                   </div>
			                </div>
			                <?php }?>
			                <div class="form-group">
			                	<?php  
			                		$min_price = (isset($_GET['min_price'])) ? $_GET['min_price'] : '0';
			                		$max_price = (isset($_GET['max_price'])) ? $_GET['max_price'] : '0';
			                	?>
			                	<div class="slider_wrap">		                    
									<div class="parent_slider"><div id="slider"></div></div>
									<div id="slider-number">
										<span id="span_min_price" class="slider-number-start">$<?php echo intval($min_price);?></span><span  id="span_max_price" class="slider-number-end">$<?php echo $ret = (isset($_GET['max_price'])) ? $_GET['max_price'] : '5000' ; ;?></span>
									</div>
								</div>
								<input id="min_price" type="hidden" name="min_price" value="<?php echo intval($min_price);?>">
								<input id="max_price" type="hidden" name="max_price" value="<?php echo $ret = (isset($_GET['max_price'])) ? $_GET['max_price'] : '5000';?>">
								<script>
									jQuery(document).ready(function(e) {
										var min_price = $('#min_price').val();
										var max_price = $('#max_price').val();
										var slider = document.getElementById('slider');

										noUiSlider.create(slider, {
											start: [min_price, max_price],
											connect: true,
											step: 1,
											range: {
												'min': 0,
												'max': 5000
											}
										});
										var inputFormat = document.getElementById('min_price');
										var inputFormat2 = document.getElementById('max_price');

										var divFormat = document.getElementById('span_min_price');
										var divFormat2 = document.getElementById('span_max_price');

										slider.noUiSlider.on('update', function( values, handle ) {

											/*var res = slider.noUiSlider.get();
											alert(res[0]);
											alert(res[1]);*/

											/*alert(values[0]);
											/*alert(values[0]);
											alert(values[1]);*/
											inputFormat.value = parseInt(values[0]);
											inputFormat2.value = parseInt(values[1]);

											divFormat.innerHTML = '$'+parseInt(values[0]);
											divFormat2.innerHTML = '$'+parseInt(values[1]);

										});

										/*jQuery( "#slider" ).slider({
										   range: true,
										   // min:0,
										   max:5000,
										   values:[min_price,max_price],
										   animate:"slow",
										   orientation: "horizontal",
										   slide: function( event, ui ) {
											  $('#slider-number .slider-number-start').text('$'+ui.values[0]);
											  $('#min_price').val(ui.values[0]);
											  $('#slider-number .slider-number-end').text('$'+ui.values[1]);
											  $('#max_price').val(ui.values[1]);
										   }	
										});*/
										// $( "#slider" ).draggable();//fix drag on mobile
									});
								</script>
			                </div>
			                <?php if( isset( $language_search ) && $language_search === 'enable' ){?>
			                <?php  if( isset( $languages_array ) && !empty( $languages_array ) ){?>
			                <div class="form-group">
			                  <div class="doc-select">     
			                     <select name="languages" class="chosen-select" data-placeholder="<?php pll_e('Select languages','docdirect');?>">
			                        <option value=""><?php pll_e('Select languages','docdirect');?></option>
			                     <?php 
			                        foreach( $languages_array as $key=>$value ){
			                            $selected	= '';
			                            if( !empty( $_GET['languages'] ) && $key == $_GET['languages'] ){
			                                $selected	= 'selected';
			                            }
			                            ?>
			                            <option <?php echo esc_attr( $selected );?> value="<?php echo esc_attr( $key );?>"><?php echo esc_attr( $value );?></option>
			                     <?php }?>
			                    </select>
			                   </div>
			                </div>
			                <?php }?>
			                <?php }?>
			                <div class="form-group">
			                  <div class="doc-select">     
			                     <select name="gender" class="chosen-select" data-placeholder="<?php pll_e('Any Gender','docdirect');?>">
			                        <option value=""><?php pll_e('Any Gender','docdirect');?></option>
			                        <option value="male" <?php echo ($_GET['gender'] == 'male' ) ? 'selected' : '' ;?>><?php pll_e('Male','docdirect');?></option>
			                        <option value="female" <?php echo ($_GET['gender'] == 'female' ) ? 'selected' : '' ;?>><?php pll_e('Female','docdirect');?></option>
			                    </select>
			                   </div>
			                </div>
				  		</div>
				  		<div class="col-md-2">
			                <div class="doc-btnarea">
			                  <!-- <button class="doc-btn" type="submit"><?php pll_e('Reset Filter','docdirect');?></button> -->
			                  <button class="doc-btn apply_filter" type="submit"><?php pll_e('Search','docdirect');?></button>
			                  <input type="hidden" name="view" value="<?php echo !empty($_GET['view'] ) ? $_GET['view'] : '';?>" />
			                </div>
				  		</div>				  		
				  	</div>
				  </div>
				  <div id="bydoctor" class="tab-pane fade">
				  	<div class="row">
				  	  <div class="col-sm-9">
	                            <?php 
	                                if (function_exists('kt_search_insurers')) {
	                                    kt_search_insurers();
	                                }
	                            ?>
				  	  </div>
				  	  <div class="col-sm-3">
		                  <div class="doc-btnarea">
		                  	<button class="doc-btn apply_filter" type="submit"><?php pll_e('Search','docdirect');?></button>
		                  	<input type="hidden" name="view" value="<?php echo !empty($_GET['view'] ) ? $_GET['view'] : '';?>" />
		                  </div>
				  	  </div>
	                </div>
				  </div>
				</div>
                
              </fieldset>
          </div>
        </div>
	</div>
    <?php
	}
}


function kt_direct_search() {

	$member_group_terms = get_terms( array(
						    'taxonomy' => 'group_label',
						    // 'orderby' => 'menu_order',
						    'i_order_terms' => true,
						    'parent'   => 0,
						    'hide_empty' => false,
						) );
	?>
    <div class="form-group direct_search">
    	<div class="bootstrap-tagsinput">
    		<?php
				if( isset( $_GET['speciality'] ) ){
					foreach ($_GET['speciality'] as $value) {
						$tax = get_term_by('slug',  $value, 'specialities');
                        // $term = get_term( $tax->term_id, 'specialities' );
                        $trans_id =  pll_get_term($tax->term_id);
						$trasn_term = get_term_by('id',  $trans_id, 'specialities');
                        $name = $trasn_term->name;
                        $slug = $trasn_term->slug;
						echo '<span data-id="'.$tax->term_id.'" class="tag label label-info">'.$name.'<span data-role="remove"></span></span>';
					}
				}
    		?>
			<input class="direct_key_search" type="text" name="key_search" value="" placeholder="<?php pll_e('Enter Specialty / Condition')?>" autocomplete="off" />
			<span class="button_dropdown"><i class="fa fa-bars"></i></span>
    		<?php
				if( isset( $_GET['speciality'] ) ){
					foreach ($_GET['speciality'] as $value) {
						$tax = get_term_by('slug',  $value, 'specialities');
						echo '<input id="speciality-'.$tax->term_id.'" type="hidden" name="speciality[]" value="'.$tax->slug.'" />';
					}
				}
    		?>
    	</div>
        
    	<div class="dropdown-input-group">
    		<div class="dropdown-wrap">
    			<!-- <i class="fa fa-spinner fa-spin"></i> -->
    		<?php
    		if(isset($member_group_terms)){
				// $specialities_list   = kt_docdirect_prepare_taxonomies('directory_type','specialities',0,'array');
				if (function_exists('kt_read_specialities')) {
    				$list_sp = kt_read_specialities();
				}
			foreach ( $member_group_terms as $p_term ) {
				?>
				    <h5><?php echo $p_term->name; ?></h5>
				    <div class="wrap_group">
				<?php
				$child_member_group_terms = get_terms( array(
						    'taxonomy' => 'group_label',
						    // 'orderby' => 'menu_order',
						    'i_order_terms' => true,
						    'child_of'   => $p_term->term_id,
						    'hide_empty' => false,
						) );
				foreach ( $child_member_group_terms as $member_group_term ) {
				    $member_group_query = new WP_Query( array(
				        'post_type' => 'directory_type',
				        'posts_per_page' => -1,
				        'tax_query' => array(
				            array(
				                'taxonomy' => 'group_label',
				                'field' => 'slug',
				                'terms' => array( $member_group_term->slug ),
				                'operator' => 'IN'
				            )
				        )
				    ) );
				    ?>
				    <h6><?php echo $member_group_term->name; ?></h6>
				    <ul>
				    <?php
				    $img_default = get_stylesheet_directory_uri().'/images/plus.svg';
				    if ( $member_group_query->have_posts() ) : while ( $member_group_query->have_posts() ) : $member_group_query->the_post(); 
				    	$trans_id = pll_get_post(get_the_ID(), 'en');

						$category_image = fw_get_db_post_option($trans_id, 'category_image', true);

						if( !empty( $category_image['attachment_id'] ) ){
							$banner_url	= docdirect_get_image_source($category_image['attachment_id'],150,150);
					 		$banner	= '<img src="'.$banner_url.'">';
				  		} else{
				  			$dir_icon = fw_get_db_post_option($trans_id, 'dir_icon', true);
					 		$banner	= '<i class="fa '.$dir_icon.'"></i>';
					 	}
				    ?>
				        <li data-slug="<?php echo get_the_slug($trans_id);?>" data-id="<?php echo get_the_ID();?>">
				        	<span data-toggle="collapse" href="#collapse-<?php echo get_the_ID();?>">
	        				<?php echo '<span class="banner">'.$banner.'</span>'; ?>
				        	<?php echo the_title(); ?>
				        	<?php
	            				$attached_specialities = get_post_meta( $trans_id, 'attached_specialities', true );
	   							if (!empty($attached_specialities)) {
	   								echo '<i class="fa fa-plus"></i>';
	   								echo '</span>';
	   								echo '<ul id="collapse-'.get_the_ID().'" class="collapse">';
	   								foreach (array_keys($attached_specialities) as $speciality) {
	   									if ($speciality != '') {
											$sp	= get_term( $speciality, 'specialities');
	                                    	$term_goc =  pll_get_term($sp->term_id);
		                                    $term = get_term( $term_goc, 'specialities' );
		                                    $name = $term->name;
		                                    $slug = $term->slug;

									     	/*$img = '<img width="150" height="150" src="'.$img_default.'">';
											if (function_exists('z_taxonomy_image_url')) {
												$img_url = z_taxonomy_image_url($sp->term_id, 'thumbnail');
								        		if( !empty( $img_url ) ) {
								            		$img = '<img width="150" height="150" src="'.$img_url.'">';
								        		}
											}*/
											/*
    										$taxonomy_image_url = get_option('z_taxonomy_image'.$sp->term_id);
    										$img = '<img width="150" height="150" src="'.$taxonomy_image_url.'">';*/
    										// var_dump($list_sp[$sp->term_id][1]);
    										$img = '<img width="150" height="150" src="'.$img_default.'">';
											if (!empty($list_sp[$sp->term_id][1])) {
	    										$img = '<img width="150" height="150" src="'.$list_sp[$sp->term_id][1].'">';
	    									}
	                                    ?>
								        <li class="select_speciality" id="speciality-<?php echo esc_attr( $sp->term_id);?>" data-slug="<?php echo esc_attr( $sp->slug);?>" data-id="speciality-<?php echo esc_attr( $sp->term_id);?>">
	        								<?php echo '<span class="banner">'.$img.'</span>'; ?>
								        	<?php echo esc_attr( $name );?>
								        </li>
	                                    <?php
	   									}
	   								}
	   								echo '</ul>';
	   							}else {
	   								echo '</span>';
	   							}
				        	?>
				        </li>
				    <?php endwhile; endif; ?>
				    </ul>
				    <?php
				    // Reset things, for good measure
				    $member_group_query = null;
				    wp_reset_postdata();
				}
				echo '</div>';
			}
			}
			?>
			</div>
            <a class="close_specialities_wrap" href="javascript:;">
            	<i class="fa fa-close"></i>
            	<span><?php esc_html_e('Close','docdirect'); ?></span>
          	</a>
    	</div>
    	<div class="clone_sps" style="display: none;"></div>
	</div>	
	<div class="form-group insurers">
		<?php
			$current_insurer_text = pll__('Insurers');
			$current_insurer  	   = !empty( $_GET['insurer'] ) ? $_GET['insurer'] : '';
			if ( $current_insurer != '' ) {
				$insurer	= get_term_by( 'slug', $current_insurer, 'insurer');
				$current_insurer_text = $insurer->name;
			}

		?>
    	<a class="dropdown-button-group" href="javascript:;"><?php echo $current_insurer_text;?></a>
		<input class="select_category" type="hidden" name="insurer" value="" />
    	<div class="dropdown-input-group">
    		<div class="dropdown-wrap">
		        <li data-slug=""><?php pll_e('Search All');?></li>
          	<?php                                     
					if (function_exists('kt_read_insurer')) {
	    				$insurers_list = kt_read_insurer();
					}
					if( isset( $insurers_list ) && !empty( $insurers_list ) ){
						foreach( $insurers_list as $key => $insurer ){
						?>
						<?php
						// $taxonomy_image_url = get_option('z_taxonomy_image'.$insurer->term_id);
                        $sample_bg_url = get_template_directory_uri().'/images/sample-insurer.png';
                        $bg_url = ($insurer[1]!='') ? $insurer[1] : $sample_bg_url;
						?>
				        <li data-slug="<?php echo $insurer->slug;?>">
				        	<img width="150" height="150" src="<?php echo $bg_url; ?>" >
				        	<span><?php echo esc_attr( $insurer[0] ); ?></span>			        	
				        </li>

                <?php }}?>
			</div>
            <a class="close_specialities_wrap" href="javascript:;">
            	<i class="fa fa-close"></i>
            	<span><?php esc_html_e('Close','docdirect'); ?></span>
          	</a>
    	</div>
	</div>
	<?php

}
//KT
function kt_docdirect_prepare_taxonomies( $post_type='post',$taxonomy='category',$hide_empty=1 ,$dataType='',$number='') {

    $args = array(
        'type'                     => $post_type,
        'child_of'                 => 0,
        'parent'                   => '',
        'orderby'                  => 'name',
        'order'                    => 'ASC',
        'hide_empty'               => $hide_empty,
        'hierarchical'             => 1,
        'exclude'                  => '',
        'include'                  => '',
        'number'                   => $number,
        'taxonomy'                 => $taxonomy,
        'pad_counts'               => false ,
        'lang'						=> 'en'
    
    ); 
    
    $categories = get_categories( $args );
    
    if( $dataType == 'array' ){
        return $categories;
    }
        
    $custom_cats     = array(); 
    
    if( isset( $categories ) && !empty( $categories ) ) {
        foreach( $categories as $key => $value ) {
            $custom_cats[$value->term_id]   = $value->name;
        }
    }
    return $custom_cats;
}

function kt_search_insurers() {
	?>	
  	<div class="form-group by_name_search">
    	<input type="text" name="by_name" placeholder="<?php pll_e('Type Name...');?>" class="form-control" autocomplete="off" />
    	<div class="dropdown-input-group">
    		<div class="dropdown-wrap response">
    		</div>
    	</div>
  	</div>
	<?php
}

/**
 * @Locate Me Snipt
 * @return 
 */
if (!function_exists('kt_docdirect_locateme_snipt')) {
	function kt_docdirect_locateme_snipt(){
		if (function_exists('fw_get_db_settings_option')) {
			$dir_geo = fw_get_db_settings_option('dir_geo');
			$dir_radius = fw_get_db_settings_option('dir_radius');
			$dir_default_radius = fw_get_db_settings_option('dir_default_radius');
			$dir_max_radius = fw_get_db_settings_option('dir_max_radius');
		} else{
			$dir_geo = '';
			$dir_radius = '';
			$dir_default_radius = 50;
			$dir_max_radius = 300;
		}
		
		$dir_default_radius 	=  !empty($dir_default_radius) ?  $dir_default_radius : 50;
		$dir_max_radius 	=  !empty($dir_max_radius) ?  $dir_max_radius : 300;
		
		$location	= '';
		if( isset( $_GET['geo_location'] ) && !empty( $_GET['geo_location'] ) ){
			$location	= $_GET['geo_location'];
		}
		
		$distance	= $dir_default_radius;
		if( isset( $_GET['geo_distance'] ) && !empty( $_GET['geo_distance'] ) ){
			$distance	= $_GET['geo_distance'];
		}
		
		if (function_exists('fw_get_db_settings_option')) {
			$dir_distance_type = fw_get_db_settings_option('dir_distance_type');
		} else{
			$dir_distance_type = 'mi';
		}
		
		$distance_title = pll__('( Miles )','docdirect');
		if( $dir_distance_type === 'km' ) {
			$distance_title = pll__('( KM )','docdirect');
		}
	?>
    	<div class="locate-me-wrap">
            <div id="location-pickr-map" class="elm-display-none"></div>
            <input type="text"  autocomplete="on" id="location-address" value="<?php echo esc_attr( $location );?>" name="geo_location" placeholder="<?php esc_html_e('Geo location','docdirect');?>" class="form-control">
            <?php if( isset( $dir_geo ) && $dir_geo === 'enable' ){?>
            <a href="javascript:;" class="geolocate"><img src="<?php echo get_template_directory_uri();?>/images/geoicon.svg" width="16" height="16" class="geo-locate-me" alt="<?php esc_html_e('Locate me!','docdirect');?>"></a>
            <?php }?>
            <?php if( isset( $dir_radius ) && $dir_radius === 'enable' ){?>
            <a href="javascript:;" class="geodistance"><i class="fa fa-angle-down" aria-hidden="true"></i></a>
            <div class="geodistance_range elm-display-none">
                <div class="distance-ml"><?php esc_html_e('Distance in','docdirect');?>&nbsp;<?php echo esc_attr( $distance_title );?><span><?php echo esc_js( $distance );?></span></div>
                <input type="hidden" name="geo_distance" value="<?php echo esc_js( $distance );?>" class="geo_distance" />
                <div class="geo_distance" id="geo_distance"></div>
            </div>
            <?php }?>
        </div>
        <?php if( isset( $dir_radius ) && $dir_radius === 'enable' ){?>
		<script>
			jQuery(document).ready(function(e) {
				jQuery( "#geo_distance" ).slider({
				   range: "min",
				   min:1,
				   max:<?php echo esc_js($dir_max_radius);?>,
				   value:<?php echo esc_js( $distance );?>,
				   animate:"slow",
				   orientation: "horizontal",
				   slide: function( event, ui ) {
					  jQuery(this).siblings( ".distance-ml" ).find('span').html( ui.value );
					  jQuery( ".geo_distance" ).val( ui.value );
				   }	
				});
			});
		</script>
        <?php }?>
    <?php
	}
}
if (!function_exists('kt_docdirect_locateme_snipt2')) {
	function kt_docdirect_locateme_snipt2(){
		if (function_exists('fw_get_db_settings_option')) {
			$dir_geo = fw_get_db_settings_option('dir_geo');
			$dir_radius = fw_get_db_settings_option('dir_radius');
			$dir_default_radius = fw_get_db_settings_option('dir_default_radius');
			$dir_max_radius = fw_get_db_settings_option('dir_max_radius');
		} else{
			$dir_geo = '';
			$dir_radius = '';
			$dir_default_radius = 50;
			$dir_max_radius = 300;
		}
		
		$dir_default_radius 	=  !empty($dir_default_radius) ?  $dir_default_radius : 50;
		$dir_max_radius 	=  !empty($dir_max_radius) ?  $dir_max_radius : 300;
		
		$location	= '';
		if( isset( $_GET['geo_location'] ) && !empty( $_GET['geo_location'] ) ){
			$location	= $_GET['geo_location'];
		}
		
		$distance	= $dir_default_radius;
		if( isset( $_GET['geo_distance'] ) && !empty( $_GET['geo_distance'] ) ){
			$distance	= $_GET['geo_distance'];
		}
		
		if (function_exists('fw_get_db_settings_option')) {
			$dir_distance_type = fw_get_db_settings_option('dir_distance_type');
		} else{
			$dir_distance_type = 'mi';
		}
		
		$distance_title = pll__('( Miles )','docdirect');
		if( $dir_distance_type === 'km' ) {
			$distance_title = pll__('( KM )','docdirect');
		}
	?>
    	<div class="locate-me-wrap">
            <div id="kt_location-pickr-map" class="elm-display-none"></div>
            <input type="text"  autocomplete="on" id="kt_location-address" value="<?php echo esc_attr( $location );?>" name="geo_location" placeholder="<?php esc_html_e('Geo location','docdirect');?>" class="form-control">
            <?php if( isset( $dir_geo ) && $dir_geo === 'enable' ){?>
            <a href="javascript:;" class="geolocate"><img src="<?php echo get_template_directory_uri();?>/images/geoicon.svg" width="16" height="16" class="geo-locate-me" alt="<?php esc_html_e('Locate me!','docdirect');?>"></a>
            <?php }?>
            <?php if( isset( $dir_radius ) && $dir_radius === 'enable' ){?>
            <a href="javascript:;" class="geodistance"><i class="fa fa-angle-down" aria-hidden="true"></i></a>
            <div class="geodistance_range elm-display-none">
                <div class="distance-ml"><?php esc_html_e('Distance in','docdirect');?>&nbsp;<?php echo esc_attr( $distance_title );?><span><?php echo esc_js( $distance );?></span></div>
                <input type="hidden" name="geo_distance" value="<?php echo esc_js( $distance );?>" class="geo_distance" />
                <div class="geo_distance" id="geo_distance2"></div>
            </div>
            <?php }?>
        </div>
        <?php if( isset( $dir_radius ) && $dir_radius === 'enable' ){?>
		<script>
			jQuery(document).ready(function(e) {
				jQuery( "#geo_distance2" ).slider({
				   range: "min",
				   min:1,
				   max:<?php echo esc_js($dir_max_radius);?>,
				   value:<?php echo esc_js( $distance );?>,
				   animate:"slow",
				   orientation: "horizontal",
				   slide: function( event, ui ) {
					  jQuery(this).siblings( ".distance-ml" ).find('span').html( ui.value );
					  jQuery( ".geo_distance" ).val( ui.value );
				   }	
				});
			});
		</script>
        <?php }?>
    <?php
	}
}

function get_the_slug( $id=null ){
  if( empty($id) ):
    global $post;
    if( empty($post) )
      return ''; // No global $post var available.
    $id = $post->ID;
  endif;

  $slug = basename( get_permalink($id) );
  return $slug;
}

//write json file after create, edit, delete term
add_action('create_term	', 'kt_list_specialities', 10, 3);
add_action('created_term	', 'kt_list_specialities', 10, 3);
add_action('edit_term', 'kt_list_specialities', 10, 3);
add_action('delete_term', 'kt_list_specialities', 10, 3);
add_action('deleted_term', 'kt_list_specialities', 10, 3);

function kt_list_specialities() {

    $args = array(
        'child_of'                 => 0,
        'orderby'                  => 'name',
        'order'                    => 'ASC',
        'hide_empty'               => false,
        'hierarchical'             => 1,
        'taxonomy'                 => 'specialities',
        'pad_counts'               => false ,
        'lang'						=> 'en'
    
    ); 
    
    $categories = get_categories( $args );
        
    $custom_cats     = array(); 
    
    if( isset( $categories ) && !empty( $categories ) ) {
        foreach( $categories as $key => $value ) {
        	$taxonomy_image_url = get_option('z_taxonomy_image'.$value->term_id);
            $custom_cats[$value->term_id]   = array($value->name, $taxonomy_image_url);
        }
		$file = STYLESHEETPATH.'/list_sp.json';
		// Open the file to get existing content
		// $current = file_get_contents($file);
		// Append a new person to the file
		$current = json_encode($custom_cats);
		// Write the contents back to the file
		file_put_contents($file, $current);
    }

    $args2 = array(
        'child_of'                 => 0,
        'orderby'                  => 'name',
        'order'                    => 'ASC',
        'hide_empty'               => false,
        'hierarchical'             => 1,
        'taxonomy'                 => 'insurer',
        'pad_counts'               => false 
    
    ); 
    
    $categories = get_categories( $args2 );
        
    $custom_cats     = array(); 
    
    if( isset( $categories ) && !empty( $categories ) ) {
        foreach( $categories as $key => $value ) {
        	$taxonomy_image_url = get_option('z_taxonomy_image'.$value->term_id);
            $custom_cats[$value->term_id]   = array($value->name, $taxonomy_image_url);
        }
		$file = STYLESHEETPATH.'/list_insurer.json';
		// Open the file to get existing content
		// $current = file_get_contents($file);
		// Append a new person to the file
		$current = json_encode($custom_cats);
		// Write the contents back to the file
		file_put_contents($file, $current);
    }
    // return $custom_cats;
}

function kt_read_specialities() {
	$string = file_get_contents(STYLESHEETPATH.'/list_sp.json');
	$json_a = json_decode($string, true);
	return $json_a;
}

function kt_read_insurer() {
	$string = file_get_contents(STYLESHEETPATH.'/list_insurer.json');
	$json_a = json_decode($string, true);
	return $json_a;
}


