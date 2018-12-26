<?php
//user calendar

function kt_load_event() {

	global $current_user;
	$user_id		= $current_user->ID;

	$json = array(
		array(
			"start"	=> "2018-03-05", 
	     	"title"	=> "All Day Event",
        	"allDay" => false // will make the time show
		),
		array(
			"start"	=> "2018-03-05", 
	     	"title"	=> "1234"
		),
		array(
	      	"start"	=> "2018-03-07", 
	      	"end"	=> "2018-03-10", 
	      	"title"	=> "Long Event"
		),
		array(
	      	"start"	=> "2018-03-10", 
	      	"title"	=> "Birthday Event"
		),
		array(
	      	"start"	=> "2018-02-08", 
	      	"title"	=> "vkl Event"
		),
		array(
	      	"title" => "My repeating event",
	        "start" => '10:00', // a start time (10am in this example)
	        "end" => '12:00', // an end time (6pm in this example)
	        
	        "dow" =>  [ 2 ], // Repeat weekly,
		),
	);
	$custom_slots = get_user_meta($user_id , 'custom_slots' , true);

	if( !empty( $custom_slots ) ){
		$custom_slot_list	= json_decode( $custom_slots,true );
	} else{
		$custom_slot_list = array();
	}

	$custom_slot_list	= docdirect_prepare_seprate_array($custom_slot_list);
	    $my_arr = array();
	    $time_format = "H:i";
	    foreach ($custom_slot_list as $key => $value) {
	      $startDate = strtotime($value['cus_start_date']);
	      $endDate = strtotime($value['cus_end_date']);
	      $cus_start_date = date('Y-m-d',$startDate);

	      $number_days = floor(($endDate-$startDate)/86400);
	      if ($number_days > 0) {        
		    foreach ($value['custom_time_slots'] as $key => $value) {
		      for ($i=0; $i <= $number_days; $i++) { 
		        $date = strtotime("+".$i." days", strtotime($cus_start_date));
		        $cur_date = date('Y-m-d',$date);
		        $time = explode('-',$key);
		        $title = date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ).' - '.date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) );
		        $my_arr[] = array(
		            'title' => $title,
		            'start' => $cur_date.' '.date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ),
		            'end' => $cur_date.' '.date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) )
		        );
		      }
		  	}
	      }
	    }

	$user_id = $_GET['user_id'];
	$user_events = get_user_meta($user_id, 'user_events', true);
	$user_events = array_values($user_events);
	echo json_encode($my_arr);
	die;
	/*$json['type']	= 'success';
	$json['message']	= pll__('Settings saved.');
	echo json_encode($json);
	die;*/


}

add_action('wp_ajax_load_event','kt_load_event');
add_action( 'wp_ajax_nopriv_load_event', 'kt_load_event' );

function kt_add_time_slot() {

	global $current_user;
	$user_identity		= $current_user->ID;

    $repeat_month = (isset( $_POST['repeat_month'])) ? $_POST['repeat_month'] : '' ;

	// delete_user_meta($user_identity , 'custom_slots');
    $start_date = (isset( $_POST['start_date'])) ? $_POST['start_date'] : '' ;
    $date_formate = date( 'd-m-Y',strtotime( $start_date ) );
    $start_time = (isset( $_POST['start_time'])) ? $_POST['start_time'] : '' ;
    $start_time = date_i18n("Hi", strtotime($start_time)) ;
    $end_time = (isset( $_POST['end_time'])) ? $_POST['end_time'] : '' ;
    $end_time = date_i18n("Hi", strtotime($end_time)) ;
    $slot_time = str_replace(':', '', $start_time).'-'.str_replace(':', '', $end_time);
    $slot_list = array($slot_time => 0);

	if ( $_POST['start_date']  == '' || $_POST['start_time']  == '' || $_POST['end_time']  == '' ){

		$json['type']	= 'error';
		$json['message']	= pll__('Empty.','docdirect');
		echo json_encode($json);
		die;
	}

	if ( strtotime($end_time) <=  strtotime($start_time) ){

		$json['type']	= 'error';
		$json['message']	= pll__('Slot Time Error.','docdirect');
		echo json_encode($json);
		die;
	}

	if( isset( $_POST['repeat'] ) && !empty( $_POST['repeat'] ) ){

        $year = date('Y', strtotime($start_date));
		$array_val = array(
			'year' => $year,
			// 'month' => $month
		);
        $month = '';
    	$arr_week = array('sun','mon','tue','wed','thu','fri','sat');
		if ( isset( $_POST['repeat_month'] ) && !empty( $_POST['repeat_month'] ) ){
			// $month = array_values($repeat_month);
			$array_val['month'] = array_values($repeat_month);
		} else if( isset( $_POST['repeat_month'] ) && empty( $_POST['repeat_month'] ) ) {
			$array_val['month'] = $arr_week;
		}


		$default_slots	= array();
		$default_slots = get_user_meta($user_identity , 'default_slots' , true);
	    $dayofweek = strtolower(date('D', strtotime($start_date)));

		if (empty($default_slots)): $default_slots = array(); endif;

		foreach (array_keys($default_slots[$dayofweek]) as $key => $value) {
			$z_time = explode('-',$value);

			/*if ($z_time[1] <= $from_time) {
				$arr_1[] = $z_time[1];
			}
			if ($z_time[0] >= $to_time) {
				$arr_2[] = $z_time[0];
			}*/
			$time = explode('-',$value);		
      		if (( $start_time > $time[0] && $start_time < $time[1] ) ||
      			( $end_time > $time[0] && $end_time < $time[1] )
      		) {
      			$flag = 'trung';
				$json['time0']	= $time[0];
				$json['time1']	= $time[1];
				$json['value']	= $value;
      			break;
      		}
		}

	  	if ($flag != '') {
			$json['type']	= 'error';
			$json['message']	= pll__('Slot not available.','docdirect');
		} else {

	        $dow = strtolower(date('w', strtotime($start_date)));
				
				$default_slots[$dayofweek][$start_time.'-'.$end_time] = $array_val;
				
				$default_slots[$dayofweek.'-details'][$start_time.'-'.$end_time]['slot_title'] = $slot_title;
				
			update_user_meta( $user_identity, 'default_slots', $default_slots );

			$event_data = array(
				'id' => strtotime($start_time),
				'title' => date('H:i', strtotime($start_time)).'-'.date('H:i', strtotime($end_time)),
				'start' => date('H:i', strtotime($start_time)),
				'end' => date('H:i', strtotime($end_time)),
				'dow' => array($dow),
	            'color' => '#7dbb00',
	            'className' => 'repeat',
	            'dowend' => $array_val
			);

			$json['event_data']	= $event_data;
			$json['default_slots']	= $default_slots;
			$json['day']	= $dayofweek;
		}

	}else {

		$custom_slots = get_user_meta($user_identity , 'custom_slots' , true);
		if( !empty( $custom_slots ) ){
			$custom_slot_list	= json_decode( $custom_slots,true );
		} else{
			$custom_slot_list = array();
		}
		$custom_slot_list	= docdirect_prepare_seprate_array($custom_slot_list);

		$flag = '';
		    foreach ($custom_slot_list as $key => $value) {
		      $vl = $value['custom_time_slots'];
		      $startDate = strtotime($value['cus_start_date']);
		      $endDate = strtotime($value['cus_end_date']);
		      $cus_start_date = date('Y-m-d',$startDate);
		      if(!is_array($vl)) {
		          $vl = json_decode($vl, true);
		      }
		      if (strtotime($start_date) == $startDate) {
    			foreach ($vl as $key => $value) {
					$time = explode('-',$key);		
		      		if (( $start_time > $time[0] && $start_time < $time[1] ) ||
		      			( $end_time > $time[0] && $end_time < $time[1] )
		      		) {
		      			$flag = 'trung';
						$json['time0']	= $time[0];
		      			break;
		      		}
		      	}
		      }
		      
		  	}		

	  	if ($flag != '') {
			$json['type']	= 'error';
			$json['message']	= pll__('Slot not available.','docdirect');
		} else {

			$custom_time_slots	 		 = '';
			$custom_time_slot_details	 = '';
			
			$current_times = json_decode(stripslashes($custom_time_slots),true);
			$current_times_details = !empty($custom_time_slot_details) ? json_decode(stripslashes($custom_time_slot_details),true) : array();

				if (!empty($current_times[$start_time.'-'.$end_time])){
					$currentCount = $current_times[$start_time.'-'.$end_time]; 
				} else { 
					$currentCount = 0;
				}
				
				$current_times[$start_time.'-'.$end_time] = $count + $currentCount;
				$current_times_details[$start_time.'-'.$end_time]['slot_title'] = $slot_title;

			$event_data = array(
				'id' => strtotime($date_formate .' '.$start_time),
				'title' => date('H:i', strtotime($start_time)).'-'.date('H:i', strtotime($end_time)),
				'start' => date('Y-m-d', strtotime($date_formate)).' '.date('H:i', strtotime($start_time)),
				'end' => date('Y-m-d', strtotime($date_formate)).' '.date('H:i', strtotime($end_time)),
			);

			$json['event_data']	= $event_data;
			$json['date_formate']	= $date_formate;
			$json['repeat']	 = 'custom';
			$json['timeslot']	 = $current_times;
			$json['timeslot_details']  =  $current_times_details;
		}
	}

	$new_custom_slots = get_user_meta($user_identity , 'custom_slots' , true);

	// $json['message']	= pll__('Settings saved.');
	echo json_encode($json);
	die;
}

add_action('wp_ajax_add_time_slot','kt_add_time_slot');
add_action( 'wp_ajax_nopriv_add_time_slot', 'kt_add_time_slot' );

function kt_update_time_slot() {

	global $current_user;
	$user_identity		= $current_user->ID;

    $repeat = (isset( $_POST['repeat'])) ? $_POST['repeat'] : '' ;
    $repeat_month = (isset( $_POST['repeat_month'])) ? $_POST['repeat_month'] : '' ;
    $old_slot = (isset( $_POST['old_slot'])) ? $_POST['old_slot'] : '' ;
		$x_time = explode('-',$old_slot);
		$from_time = $x_time[0];
		$to_time = $x_time[1];
    $old_event = (isset( $_POST['id_event'])) ? $_POST['id_event'] : '' ;

    $start_date = (isset( $_POST['start_date'])) ? $_POST['start_date'] : '' ;
    $date_formate = date( 'd-m-Y',strtotime( $start_date ) );
    $start_time = (isset( $_POST['start_time'])) ? $_POST['start_time'] : '' ;
    $start_time = date_i18n("Hi", strtotime($start_time)) ;
    $end_time = (isset( $_POST['end_time'])) ? $_POST['end_time'] : '' ;
    $end_time = date_i18n("Hi", strtotime($end_time)) ;
    $slot_time = str_replace(':', '', $start_time).'-'.str_replace(':', '', $end_time);

    $dayofweek = strtolower(date('D', strtotime($start_date)));
    $dow = strtolower(date('w', strtotime($start_date)));

			$json['start_time']	= $start_time;
			$json['end_time']	= $end_time;

	if ( strtotime($end_time) >  strtotime($start_time) ){
		if ( $repeat == 'repeat' ) {
			$default_slots = get_user_meta($user_identity , 'default_slots' , true);
			
	        $year = date('Y', strtotime($start_date));
	        $month = '';
			
	    	$arr_week = array('sun','mon','tue','wed','thu','fri','sat');
			if ( isset( $_POST['repeat_month'] ) && !empty( $_POST['repeat_month'] ) ){
				// $month = array_values($repeat_month);
				$array_val['month'] = array_values($repeat_month);
			} else if( isset( $_POST['repeat_month'] ) && empty( $_POST['repeat_month'] ) ) {
				$array_val['month'] = $arr_week;
			}
			$array_val = array(
				'year' => $year,
				'month' => $month
			);

			if ( isset( $default_slots[$dayofweek][$old_slot] ) ){
				unset($default_slots[$dayofweek][$old_slot]);
				unset($default_slots[$dayofweek.'-details'][$old_slot]);

				foreach (array_keys($default_slots[$dayofweek]) as $key => $value) {
					$z_time = explode('-',$value);
					if ($z_time[1] <= $from_time) {
						$arr_1[] = $z_time[1];
					}
					if ($z_time[0] >= $to_time) {
						$arr_2[] = $z_time[0];
					}
					$json['value']	= $value;
				}
				$new_from = (max($arr_1) != false) ? max($arr_1) : '0000' ;
				$new_to = (min($arr_2) != false) ? min($arr_2) : '2400' ;

				if ( $start_time < $new_from || $end_time > $new_to ) {
					$json['type']	= 'error';
					$json['message']	= pll__('Slot not available.','docdirect');
				} else {
					$default_slots[$dayofweek][$start_time.'-'.$end_time] = $array_val;			
					$default_slots[$dayofweek.'-details'][$start_time.'-'.$end_time]['slot_title'] = $slot_title;
					
					update_user_meta( $user_identity, 'default_slots', $default_slots );

					$event_data = array(
						'id' => strtotime($start_time),
						'title' => date('H:i', strtotime($start_time)).'-'.date('H:i', strtotime($end_time)),
						'start' => date('H:i', strtotime($start_time)),
						'end' => date('H:i', strtotime($end_time)),
						'dow' => array($dow),
			            'color' => '#7dbb00',
			            'className' => 'repeat',
            			'dowend' => $array_val
					);

					$json['default_slots'][$dayofweek]	= $default_slots[$dayofweek];
					$json['old_event']	= $old_event;
					$json['event_data']	= $event_data;
					$json['message']	= pll__('Slot updated succesfully.','docdirect');
				}
				
			} else{
				$json['type']	= 'error';
				$json['message']	= pll__('Some error occur, please try again later.','docdirect');
			}
		}else {
			$custom_slots = get_user_meta($user_identity , 'custom_slots' , true);
			if( !empty( $custom_slots ) ){
				$custom_slot_list	= json_decode( $custom_slots,true );
			} else{
				$custom_slot_list = array();
			}
			$custom_slot_list	= docdirect_prepare_seprate_array($custom_slot_list);
			$flag = '';
		    foreach ($custom_slot_list as $key => $value) {
		      $vl = $value['custom_time_slots'];
		      $startDate = strtotime($value['cus_start_date']);
		      $endDate = strtotime($value['cus_end_date']);
		      $cus_start_date = date('Y-m-d',$startDate);
		      if(!is_array($vl)) {
		          $vl = json_decode($vl, true);
		      }
		      if (strtotime($start_date) == $startDate) {
    			foreach ($vl as $key => $value) {
    				if ( $old_slot != $key ) {
	              		$time = explode('-',$key);		
			      		if (( $start_time > $time[0] && $start_time < $time[1] ) ||
			      			( $end_time > $time[0] && $end_time < $time[1] )
			      		) {
			      			$flag = 'trung';
							$json['time0']	= $time[0];
			      			break;
			      		}
    				}
		      	}
		      }
		      
		  	}
		  	if ($flag == '') {

				$current_times[$start_time.'-'.$end_time] = $count + $currentCount;
				$current_times_details[$start_time.'-'.$end_time]['slot_title'] = $slot_title;
			
              	$id = strtotime($start_date.' '.$start_time);
				$event_data = array(
					'id' => $id,
					'title' => date('H:i', strtotime($start_time)).'-'.date('H:i', strtotime($end_time)),
					'start' => $start_date.' '.date('H:i', strtotime($start_time)),
					'end' => $start_date.' '.date('H:i', strtotime($end_time))
				);
				$json['event_data']	= $event_data;
				$json['old_event']	= $old_event;
				$json['repeat']	= 'custom';
				$json['message']	= pll__('Slot updated succesfully.','docdirect');
				$json['timeslot']	 = $current_times;
				$json['timeslot_details']  =  $current_times_details;	  		
		  	} else {		  		
					$json['type']	= 'error';
					$json['message']	= pll__('Slot not available.','docdirect');
		  	}
		  	

				$json['flag']	= $flag;
		}
	}else {		
			$json['type']	= 'error';
			$json['message']	= pll__('Some error occur, please try again later.','docdirect');
	}

	echo json_encode($json);
	die;
}

add_action('wp_ajax_update_time_slot','kt_update_time_slot');
add_action( 'wp_ajax_nopriv_update_time_slot', 'kt_update_time_slot' );


function kt_remove_time_slot() {

	global $current_user;
	$user_identity		= $current_user->ID;

	$default_slots = get_user_meta($user_identity , 'default_slots' , true);

	$date 		= sanitize_text_field( $_POST['date'] );
	$time 	   = sanitize_text_field( $_POST['time'] );

    $dayofweek = strtolower(date('D', strtotime($date)));		

	if( isset( $_POST['repeat'] ) && $_POST['repeat'] == 'repeat' ){

		$default_slots = get_user_meta($user_identity , 'default_slots' , true);

		if ( isset( $default_slots[$dayofweek][$time] ) ){
			unset($default_slots[$dayofweek][$time]);
			unset($default_slots[$dayofweek.'-details'][$time]);
			
			update_user_meta( $user_identity, 'default_slots', $default_slots );
		
			$json['default_slots']	= $default_slots;
			$json['type']	= 'success';
			$json['message']	= pll__('Slot deleted succesfully.','docdirect');
			
		} else{
			$json['type']	= 'error';
			$json['message']	= pll__('Some error occur, please try again later.','docdirect');
		}


	}else {

	}

	echo json_encode($json);
	die;
}

add_action('wp_ajax_remove_time_slot','kt_remove_time_slot');
add_action( 'wp_ajax_nopriv_remove_time_slot', 'kt_remove_time_slot' );
function kt_check_time_slot() {

	global $current_user;
	$user_identity		= $current_user->ID;

		$date 		= sanitize_text_field( $_POST['date'] );
		$time 	   = sanitize_text_field( $_POST['time'] );
		$x_time = explode('-',$time);
		$from_time = $x_time[0];
		$to_time = $x_time[1];

        $dayofweek = strtolower(date('D', strtotime($date)));		

		$default_slots = get_user_meta($user_identity , 'default_slots' , true);

		$arr_1 = array();$arr_2 = array();
		if ( isset( $default_slots[$dayofweek][$time] ) ){
			unset($default_slots[$dayofweek][$time]);
			unset($default_slots[$dayofweek.'-details'][$time]);
			foreach (array_keys($default_slots[$dayofweek]) as $key => $value) {
				$z_time = explode('-',$value);
				if ($z_time[1] <= $from_time) {
					$arr_1[] = $z_time[1];
				}
				if ($z_time[0] >= $to_time) {
					$arr_2[] = $z_time[0];
				}
			}
			// $new_from = max($arr_1);
			// $new_to = min($arr_2);
			$new_from = (max($arr_1) != false) ? max($arr_1) : '0000' ;
			$new_to = (min($arr_2) != false) ? min($arr_2) : '2400' ;

			$time = strtotime($new_from);
			$timeStop = strtotime($new_to);

			$output = '';
			$output2 = '';
			while($time < $timeStop) {
			    $selected = '';
			    // echo date('H:i', $time);
			    if(strtotime($from_time) == $time ) {
			    	$selected = 'selected';
			    }
			    $output .= '<option '.$selected.' value="'.date('Hi', $time).'">'.date('H:i', $time).'</option>';
			    $time = strtotime('+30 minutes', $time);
			    if(strtotime($to_time) == $time ) {
			    	$selected = 'selected';
			    }
			    $output2 .= '<option '.$selected.' value="'.date('Hi', $time).'">'.date('H:i', $time).'</option>';
			}
		
			$json['output']	= $output;
			$json['output2']	= $output2;
			$json['type']	= 'success';
			$json['message']	= pll__('','docdirect');
			
		} else{
			$json['type']	= 'error';
			$json['message']	= pll__('Some error occur, please try again later.','docdirect');
		}

	$json['id_event']	= sanitize_text_field( 'id_event' );
	echo json_encode($json);
	die;
}

add_action('wp_ajax_check_time_slot','kt_check_time_slot');
add_action( 'wp_ajax_nopriv_check_time_slot', 'kt_check_time_slot' );

function kt_get_user_calendar() {

	global $current_user;
	$user_identity		= $current_user->ID;

	$today = current_time('timestamp');
    $today_date = date('Y-m-d',$today);

	$custom_slots = get_user_meta($user_identity , 'custom_slots' , true);
    $default_slots = get_user_meta($user_identity , 'default_slots' , true);
	if( !empty( $custom_slots ) ){
		$custom_slot_list	= json_decode( $custom_slots,true );
	} else{
		$custom_slot_list = array();
	}

	$custom_slot_list	= docdirect_prepare_seprate_array($custom_slot_list);

	$my_arr = array();

    $time_format = "H:i";
    foreach ($custom_slot_list as $key => $value) {
      $vl = $value['custom_time_slots'];
      $startDate = strtotime($value['cus_start_date']);
      $endDate = strtotime($value['cus_end_date']);
      $cus_start_date = date('Y-m-d',$startDate);

      $number_days = floor(($endDate-$startDate)/86400);
      if ($number_days > 0) {
        foreach ($vl as $key => $value) {
            for ($i=0; $i <= $number_days; $i++) {
            	if ( strtotime($cus_start_date) >= strtotime($today_date) ) {
	              $date = strtotime("+".$i." days", strtotime($cus_start_date));
	              $cur_date = date('Y-m-d',$date);
	              $dayofweek = date('w', strtotime($cur_date));
	              $time = explode('-',$key);
	              $title = date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ).' - '.date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) );
	              $my_arr[] = array(
	                  'title' => $title,
	                  'start' => $cur_date.' '.date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ),
	                  'end' => $cur_date.' '.date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) )
	              );
            	}
            }
          }
      }else {
        if(!is_array($vl)) {
          $vl = json_decode($vl, true);
        }
        foreach ($vl as $key => $value) {
            if ( strtotime($cus_start_date) >= strtotime($today_date) ) {
              $time = explode('-',$key);         
              $id = strtotime($cus_start_date.' '.$time[0]);
              $dayofweek = date('w', strtotime($cus_start_date));
              $title = date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ).' - '.date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) );
              $my_arr[] = array(
                  'id' => $id,
                  'title' => $title,
                  'start' => $cus_start_date.' '.date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ),
                  'end' => $cus_start_date.' '.date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) ),

                  // 'dow' =>  array($dayofweek), // Repeat weekly,
              );
          	}
        }
      }
    }

    $arr_week = array('sun','mon','tue','wed','thu','fri','sat');
    foreach ($arr_week as $key => $value) {
      $dow = $key;
      foreach ($default_slots[$value] as $key => $value) {
            $time = explode('-',$key);
            $id = strtotime($time[0]);
			$title = date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ).' - '.date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) );
            $data = array(
              'id' => $id,
              'title' => $title,
              'start' => date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ),
              'end' => date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) ),
              // 'dowend' => $value,
              'dow' =>  array($dow), // Repeat weekly,
              'color' => '#7dbb00',
              'className' => 'repeat'
            );
            if ( strtotime($value) > 0 ) {
            	$data['dowend'] = date('Y-m',strtotime($value));
            }else if( count($value) > 1 ) {
            	$data['dowend'] = $value;
            }
            $my_arr[] = $data;
      }
    }

    return $my_arr;

}