<?php


/**
 * @Account Settings
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_account_settings' ) ) {

	remove_action('wp_ajax_docdirect_account_settings','docdirect_account_settings');
	remove_action( 'wp_ajax_nopriv_docdirect_account_settings', 'docdirect_account_settings' );

	function kt_docdirect_account_settings(){ 
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		
		//Update Socials
		if( isset( $_POST['socials'] ) && !empty( $_POST['socials'] ) ){
			foreach( $_POST['socials'] as $key=>$value ){
				update_user_meta( $user_identity, $key, esc_attr( $value ) );
			}
		}
		
		$first_name	= '';
		$last_name	= '';
		
		//Update avatar banner
		if( isset( $_POST['userprofile_media'] ) && !empty( $_POST['userprofile_media'] ) ){
			update_user_meta( $user_identity, 'userprofile_media', $_POST['userprofile_media'] );
		}
		if( isset( $_POST['userprofile_banner'] ) && !empty( $_POST['userprofile_banner'] ) ){
			update_user_meta( $user_identity, 'userprofile_banner', $_POST['userprofile_banner'] );
		}
		if( isset( $_POST['userprofile_banner_mobile'] ) && !empty( $_POST['userprofile_banner_mobile'] ) ){
			update_user_meta( $user_identity, 'userprofile_banner_mobile', $_POST['userprofile_banner_mobile'] );
		}
		if( isset( $_POST['userprofile_company_logo'] ) && !empty( $_POST['userprofile_company_logo'] ) ){
			update_user_meta( $user_identity, 'userprofile_company_logo', $_POST['userprofile_company_logo'] );
		}
		
		//Update Basics
		if( isset( $_POST['basics'] ) && !empty( $_POST['basics'] ) ){
			foreach( $_POST['basics'] as $key => $value ){
				if( $key === 'first_name' ){
					$first_name	= $value;
				} else if( $key === 'last_name' ){
					$last_name	= $value;
				}
				
				update_user_meta( $user_identity, $key, esc_attr( $value ) );
			}
		}
		
		//Time Formate
		if( !empty( $_POST['time_format'] ) ){
			update_user_meta( $user_identity, 'time_format', esc_attr( $_POST['time_format'] ) );
		}
		
		//update username
		$username	= trim( $first_name.' '.$last_name );
		update_user_meta( $user_identity, 'username', esc_attr( $username ) );
		
		//Update General settings
		
		//update video url
		$user_video_url	= array();
		if( isset( $_POST['video_url'] ) && !empty( $_POST['video_url'] ) ){
			foreach( $_POST['video_url'] as $key=>$value ){
				$user_video_url[$key]	= $value;
			}
		}
		update_user_meta( $user_identity, 'video_url', $user_video_url );

		wp_update_user( array( 'ID' => $user_identity, 'user_url' => esc_attr($_POST['basics']['user_url']) ) );

		update_user_meta( $user_identity, 'business_email', esc_attr( $_POST['basics']['business_email'] ) );
		update_user_meta( $user_identity, 'gende', esc_attr( $_POST['gende'] ) );
		update_user_meta( $user_identity, 'title_name', esc_attr( $_POST['title_name'] ) );
		
		//Awawrds
		$awards	= array();
		if( isset( $_POST['awards'] ) && !empty( $_POST['awards'] ) ){
			
			$counter	= 0;
			foreach( $_POST['awards'] as $key=>$value ){
				$awards[$counter]['name']	= esc_attr( $value['name'] ); 
				$awards[$counter]['date']	= esc_attr( $value['date'] );
				$awards[$counter]['date_formated']	= date('d M, Y',strtotime($value['date']));  
				$awards[$counter]['description']	  = esc_attr( $value['description'] ); 
				$counter++;
			}
			$json['awards']	= $awards;
		}
		update_user_meta( $user_identity, 'awards', $awards );
		
		//Gallery
		$user_gallery	= array();
		if( isset( $_POST['user_gallery'] ) && !empty( $_POST['user_gallery'] ) ){
			$counter	= 0;
			foreach( $_POST['user_gallery'] as $key=>$value ){
				$user_gallery[$value['attachment_id']]['url']	= esc_url( $value['url'] ); 
				$user_gallery[$value['attachment_id']]['id']	= esc_attr( $value['attachment_id']); 
				$counter++;
			}
		}
		update_user_meta( $user_identity, 'user_gallery', $user_gallery );
		
		//Specialities
		$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
		if( isset( $db_directory_type ) && !empty( $db_directory_type ) ) {
			$specialities_list	 = docdirect_prepare_taxonomies('directory_type','specialities',0,'array');
		}
		
		$specialities	= array();
		if( isset( $specialities_list ) && !empty( $specialities_list ) ){
			$counter	= 0;
			foreach( $specialities_list as $key => $speciality ){
				if( isset( $_POST['specialities'] ) && in_array( $speciality->slug, $_POST['specialities'] ) ){
					update_user_meta( $user_identity, $speciality->slug, $speciality->slug );
					$specialities[$speciality->slug]	= $speciality->name;
				}else{
					update_user_meta( $user_identity, $speciality->slug, '' );
				}
				
				$counter++;
			}
		}
		
		update_user_meta( $user_identity, 'user_profile_specialities', $specialities );

		//insurers
		$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
		if( isset( $db_directory_type ) && !empty( $db_directory_type ) ) {
			$insurers_list	 = docdirect_prepare_taxonomies('directory_type','insurer',0,'array');
		}

		$insurers	= array();
		if( isset( $insurers_list ) && !empty( $insurers_list ) ){
			$counter	= 0;
			foreach( $insurers_list as $key => $insurer ){
				if( isset( $_POST['insurers'] ) && in_array( $insurer->slug, $_POST['insurers'] ) ){
					update_user_meta( $user_identity, $insurer->slug, $insurer->slug );
					$insurers[$insurer->slug]	= $insurer->name;
				}else{
					update_user_meta( $user_identity, $insurer->slug, '' );
				}
				
				$counter++;
			}
		}
		
		update_user_meta( $user_identity, 'user_profile_insurers', $insurers );

		//procedures
		$patient_insurers = $_POST['patient_insurers'];
		update_user_meta( $user_identity, 'patient_insurers', $patient_insurers );

		//procedures
		$procedures = $_POST['procedures'];
		update_user_meta( $user_identity, 'user_profile_procedures', $procedures );

		//insurance_plan
		$procedures = $_POST['insurance_plan'];
		update_user_meta( $user_identity, 'user_profile_insurance_plan', $procedures );
		
		//Education
		$educations	= array();
		if( isset( $_POST['education'] ) && !empty( $_POST['education'] ) ){
			$counter	= 0;
			foreach( $_POST['education'] as $key=>$value ){
				if( !empty( $value['title'] ) && !empty( $value['institute'] ) ) {
					$educations[$counter]['title']	= 	esc_attr( $value['title'] ); 
					$educations[$counter]['institute']	 = 	esc_attr( $value['institute'] ); 
					$educations[$counter]['start_date']	= 	esc_attr( $value['start_date'] ); 
					$educations[$counter]['end_date']	  = 	esc_attr( $value['end_date'] ); 
					$educations[$counter]['start_date_formated']  = date('M,Y',strtotime($value['start_date'])); 
					$educations[$counter]['end_date_formated']	= date('M,Y',strtotime($value['end_date'])); 
					$educations[$counter]['description']	= 	esc_attr( $value['description'] ); 
					$counter++;
				}
			}
			$json['education']	= $educations;
		}
		update_user_meta( $user_identity, 'education', $educations );
		
		//Experience
		$experiences	= array();
		if( !empty( $_POST['experience'] ) ){
			$counter	= 0;
			foreach( $_POST['experience'] as $key=>$value ){
				if( !empty( $value['title'] ) && !empty( $value['company'] ) ) {
					$experiences[$counter]['title']	= 	esc_attr( $value['title'] ); 
					$experiences[$counter]['company']	 = 	esc_attr( $value['company'] ); 
					$experiences[$counter]['start_date']	= 	esc_attr( $value['start_date'] ); 
					$experiences[$counter]['end_date']	  = 	esc_attr( $value['end_date'] ); 
					$experiences[$counter]['start_date_formated']  = date('M,Y',strtotime($value['start_date'])); 
					$experiences[$counter]['end_date_formated']	= date('M,Y',strtotime($value['end_date'])); 
					$experiences[$counter]['description']	= 	esc_attr( $value['description'] ); 
					$counter++;
				}
			}
			$json['experience']	= $experiences;
		}
		update_user_meta( $user_identity, 'experience', $experiences );

		//Price/Services
		$prices	= array();
		if( !empty( $_POST['prices'] ) ){
			$counter	= 0;
			foreach( $_POST['prices'] as $key=>$value ){
				if( !empty( $value['title'] ) ) {
					$prices[$counter]['title']	= 	esc_attr( $value['title'] ); 
					$prices[$counter]['price']	 = 	esc_attr( $value['price'] ); 
					$prices[$counter]['description']	= 	esc_attr( $value['description'] ); 
					$counter++;
				}
			}
			$json['prices_list']	= $prices;
		}
		
		update_user_meta( $user_identity, 'prices_list', $prices );
		
		//Languages
		$languages	= array();
		if( isset( $_POST['language'] ) && !empty( $_POST['language'] ) ){
			$counter	= 0;
			foreach( $_POST['language'] as $key=>$value ){
				$languages[$value]	= 	$value; 
				$counter++;
			}
		}
		update_user_meta( $user_identity, 'languages', $languages );
		
		$json['type']	= 'success';
		$json['message']	= pll__('Settings saved.');
		echo json_encode($json);
		die;
	}
	add_action('wp_ajax_docdirect_account_settings','kt_docdirect_account_settings');
	add_action( 'wp_ajax_nopriv_docdirect_account_settings', 'kt_docdirect_account_settings' );
}

/**
 * @Get featuired tag
 * @return 
 */
if (!function_exists('kt_docdirect_get_featured_tag')) {
	function kt_docdirect_get_featured_tag($echo=false,$user_id=null,$view='v1'){
		global $current_user;
		$user_current_package = get_user_meta($user_id, 'user_current_package', true);

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
			$text = pll__('Company');
	    }else {
			$text = pll__('Professional');
	    }
		
		ob_start();
		if( isset( $view ) && $view === 'v2' ){
			?>
			<a class="doc-featuredicon" href="javascript:;"><i class="fa fa-bolt"></i><?php echo $text;?></a>
			<?php
		} else{
			?>
			<span class="tg-featuredtags">
				<a class="tg-featured" href="javascript:;"><?php echo $text;?></a>
			</span>
			<?php
		}
		if( $echo == true ){
			echo ob_get_clean();
		} else{
			return ob_get_clean();
		}
	}
}

/**
 * Update Order
 *
 * @param json
 * @return string
 */
if (!function_exists('kt_docdirect_update_order_data')) {
	function kt_docdirect_update_order_data($data=array()){
		extract($data);
		$today = current_time( 'timestamp' );
		$payment_date = date('Y-m-d H:i:s');
		// $user_featured_date = get_user_meta( $user_identity, 'user_featured', true);
		if ( $user_featured_date == '' || $user_featured_date < $today ) {
			$user_featured_date = $today;
		}
		//update last
		// $number_days = ceil($user_featured_date/86400);
		update_user_meta( $user_identity, 'last_user_featured', $user_featured_date);
		$featured_date	= date('Y-m-d H:i:s');
		
		if( isset( $user_featured_date ) && !empty( $user_featured_date ) ){
			$duration = fw_get_db_post_option($package_id, 'duration', true);
			if( $duration > 0 ){
				$featured_date	= strtotime("+".$duration." days", $user_featured_date);
				$featured_date	= date('Y-m-d H:i:s',$featured_date);
			}
		} else{
			$current_date	= date('Y-m-d H:i:s');
			$duration = fw_get_db_post_option($package_id, 'duration', true);
			if( $duration > 0 ){
				$featured_date		 = strtotime("+".$duration." days", strtotime($current_date));
				$featured_date	     = date('Y-m-d H:i:s',$featured_date);
			}
		}
		
		update_user_meta($user_identity,'user_featured',strtotime( $featured_date )); //Update Expiry
		update_user_meta($user_identity,'user_current_package',$package_id); //Current package
					
		$order_meta = array(
			'transaction_id' 	=> $txn_id,
			'order_status' 	  => 'approved',
			'price' 			 => $payment_gross,
			'payment_date' 	  => $payment_date,
			'payment_date_string'  => strtotime( $payment_date ),
			'expiry_date_string'   => strtotime( $featured_date ),
			'expiry_date' 	   => $featured_date,
			'payment_method' 	=> $payment_method,
			'package' 		   => $package_id,
			'mc_currency' 	   => $mc_currency,
			'payment_user' 	  => $user_identity,
		);
		
		//Update meta for searching purpose
		foreach( $order_meta as $key=>$value){
			update_post_meta($order_id,$key,$value);
		}
		
		$new_values = $order_meta;
		if (isset($order_id) && !empty($order_id)) {
			fw_set_db_post_option($order_id, null, $new_values);
		}
		
		return $featured_date;
	}
}

/**
 * @Stripe Payment
 *
 * @param json
 * @return string
 */
if ( !function_exists('kt_docdirect_complete_stripe_payment') ) {

	remove_action('wp_ajax_docdirect_complete_stripe_payment','docdirect_complete_stripe_payment');
	remove_action( 'wp_ajax_nopriv_docdirect_complete_stripe_payment', 'docdirect_complete_stripe_payment' );
	
	function kt_docdirect_complete_stripe_payment() {		
		$first_name   = $_POST['first_name'];
		$last_name	= $_POST['last_name'];
		$username	 = $_POST['username'];
		$user_identity	 = $_POST['user_identity'];
		$email  		= $_POST['email'];
		$order_no 	 = $_POST['order_no'];
		$package_id   = $_POST['package_id'];
		$package_name = $_POST['package_name'];
		$useraddress  = $_POST['useraddress'];
		$gateway 	  = $_POST['gateway'];
		$type		 = $_POST['type'];
		$token	    = $_POST['token'];
		$payment_type = $_POST['payment_type'];
		$process 	  = $_POST['process'];
		$name		 = $_POST['name'];
		$amount	   = $_POST['amount'];
		$total_amount = $_POST['total_amount'];
		$token	    = $_POST['token'];
		
		$currency_sign	= 'USD';
		
		 if (function_exists('fw_get_db_settings_option')) {
			$currency_sign   = fw_get_db_settings_option('currency_select');
			$stripe_secret    = fw_get_db_settings_option('stripe_secret');
			$stripe_publishable = fw_get_db_settings_option('stripe_publishable');
			$stripe_site     = fw_get_db_settings_option('stripe_site');
			$stripe_decimal  = fw_get_db_settings_option('stripe_decimal');
		 }
		 
		 
		 if( class_exists( 'DocDirectGlobalSettings' ) ) {		 
		 	require_once( DocDirectGlobalSettings::get_plugin_path().'/libraries/stripe/init.php');
		 } else{
			$json['type']     = 'error';
			$json['message']  = pll__('Stripe API not found.');
			echo json_encode($json);
			die;
		 }
		 
		 $stripe = array(
			"secret_key"      => $stripe_secret,
			"publishable_key" => $stripe_publishable
		  );

		  \Stripe\Stripe::setApiKey($stripe['secret_key']);
		  
		  $charge = \Stripe\Charge::create(array(
			'amount'   => $amount,
			'currency' => ''.$currency_sign.'',
			'source'  => $token['id'],
			'description' => $package_name,
		  ));
		
		if ($charge->status == 'succeeded') {
			
			if( !empty( $charge->source->id ) ){
				$transaction_id	= $charge->source->id;
			} else{
				$transaction_id	= docdirect_unique_increment(10);
			}
			
			//Update Order
            $expiry_date	= kt_docdirect_update_order_data(
				array(
					'order_id'		 => $order_no,
					'user_identity'	=> $user_identity,
					'package_id'	   => $package_id,
					'txn_id'		   => $transaction_id,
					'payment_gross'	=> $total_amount,
					'payment_method'   => 'stripe',
					'mc_currency'	  => $currency_sign,
				)
			);
			
			//Add Invoice
			docdirect_new_invoice(
				array(
					'user_identity'	 => $user_identity,
					'package_id'		=> $package_id,
					'txn_id'			=> $transaction_id,
					'payment_gross'	 => $total_amount,
					'item_name'		 => $package_name,
					'payer_email'	   => $email,
					'mc_currency'	   => $currency_sign,
					'address_name'	  => $useraddress,
					'ipn_track_id'	  => '',
					'transaction_status'=> 'approved',
					'payment_method'	=> 'stripe',
					'full_address'	  => $useraddress,
					'first_name'		=> $first_name,
					'last_name'		 => $last_name,
					'purchase_on'	   => date('Y-m-d H:i:s'),
				)
			);
			
			//Send ean email 
			if( class_exists( 'DocDirectProcessEmail' ) ) {
				$email_helper	= new DocDirectProcessEmail();
				$emailData	= array();
				$emailData['name']			  = $_POST['first_name'].' '.$_POST['last_name'];
				$emailData['mail_to']	  	   = $email;
				$emailData['invoice']	  	   = $transaction_id;
				$emailData['package_name']	  = $package_name;					
				$emailData['amount']			= $currency_sign.$total_amount;
				$emailData['status']			= pll__('Approved');
				$emailData['method']			= pll__('Stripe( Credit Card )');
				$emailData['date']			  = date('Y-m-d H:i:s');
				$emailData['expiry']			= $expiry_date;
				$emailData['address']		   = $useraddress;
				

				$email_helper->process_invoice_email($emailData);
			}
						
			
			$json['type']     = 'success';
			$json['message']  = pll__('Thank you! Your package has been updated.');
			echo json_encode($json);
			die;
		}
		
		$json['type']     = 'error';
		$json['message']  = pll__('Some Error occur, please try again later.');
		echo json_encode($json);
		die;
	}
	add_action('wp_ajax_docdirect_complete_stripe_payment', 'kt_docdirect_complete_stripe_payment');
	add_action('wp_ajax_nopriv_docdirect_complete_stripe_payment', 'kt_docdirect_complete_stripe_payment');
}

/**
 * @Authenticate user
 * @return 
 */
if (!function_exists('kt_docdirect_count_reviews')) {
	function kt_docdirect_count_reviews( $user_id ='' ){
		$user_reviews = array(
			'posts_per_page'	=> "-1",
			'post_type'		 => 'docdirectreviews',
			'post_status'	   => 'publish',
                    'lang' => '', 
			'meta_key'		  => 'user_to',
			'meta_value'		=> $user_id,
			'meta_compare'	  => "=",
			'orderby'		   => 'meta_value',
			'order'			 => 'ASC',
		);
		
		$reviews_query = new WP_Query($user_reviews);
		$reviews_count = $reviews_query->post_count;
		return intval( $reviews_count );
	}
	add_filter( 'kt_docdirect_count_reviews', 'kt_docdirect_count_reviews' );
}

if ( ! function_exists( 'kt_docdirect_get_locations_options' ) ) {
	function kt_docdirect_get_locations_options($current='') {
		$taxonomyName = "locations";

		//This gets top layer terms only.  This is done by setting parent to 0.  
		$parent_terms = get_terms( $taxonomyName, array( 'parent' => 0, 'orderby' => 'slug', 'hide_empty' => false ) ); 
		$options	= '';
		if( isset( $parent_terms ) && !empty( $parent_terms ) ) {
			foreach ( $parent_terms as $pterm ) {
				//Get the Child terms
				
				$terms = get_terms( $taxonomyName, array( 'parent' => $pterm->term_id, 'orderby' => 'slug', 'hide_empty' => false ) );
				if( isset( $terms ) && !empty( $terms ) ) {
					$options	.= '<optgroup  label="'.$pterm->name.'">';
					foreach ( $terms as $term ) {
						$selected	= '';
						if( $current === $term->slug ){
							$selected	= 'selected';
						}
						$options	.= '<option '.$selected.' value="'.$term->slug.'">'.$term->name.'</option>';
					}
					$options	.= '</optgroup>';
				} else{
					$selected	= '';
					if( $current === $pterm->slug ){
						$selected	= 'selected';
					}
					$options	.= '<option '.$selected.' value="'.$pterm->slug.'">'.$pterm->name.'</option>';
				}
			}
		}
		
		echo force_balance_tags( $options );
	}
}

/**
 * @Booking Step 2
 * Schedules
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_get_booking_step_two_calender' ) ) {
	function kt_docdirect_get_booking_step_two_calender($user_identity='',$return_type='echo', $fixed_date=null){
		global $current_user, $wp_roles,$userdata,$post;
		$schedule_message	     = get_user_meta( $user_identity, 'schedule_message', true);
		if ($fixed_date != null) {
			$fixed_date = strtotime($fixed_date);
			$current_date	= date('Y-m-d', $fixed_date);
			$slot_date	= date('Y-m-d', $fixed_date);
			$current_date_string	= date('M j, l', $fixed_date);
		}else {			
			$current_date	= date('Y-m-d');
			$slot_date	= date('Y-m-d');
			$current_date_string	= date('M j, l');
		}
			
		?>
        <div class="bk-booking-schedules">
            <div class="tg-appointmenttime">
              <?php if( !empty( $schedule_message ) ){?>
                  <div class="tg-description">
                      <p><?php echo force_balance_tags( $schedule_message );?></p>
                  </div>
              <?php }?>
              <div class="clearfix"></div>
              <div class="tg-dayname booking-pickr"> 
              	<strong><?php echo esc_attr( $current_date_string );?></strong>
                <input type="hidden" name="booking_date" class="booking_date" value="<?php echo esc_attr( $current_date );?>" />
              </div>
              <div class="tg-timeslots step-two-slots">
                  <div class="tg-timeslotswrapper"></div>
	              <div class="row custom_button">
		          		<a href="javascript:;" class="tg-btn this_month"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i><?php pll_e(' Month');?></a>
	          			<a href="javascript:;" class="tg-btn prev_day"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i><?php pll_e('Day');?></a>
	          			<a href="javascript:;" class="tg-btn next_day"><?php pll_e('Day');?><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
		          		<a href="javascript:;" class="tg-btn next_month"><?php pll_e('Month');?><i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>
	              </div>
              </div>

       		</div>

        </div>
        <?php
	}
}

/**
 * @Booking Step 5
 * Thank You Message
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_get_booking_step_five' ) ) {
	function kt_docdirect_get_booking_step_five($user_identity){
		global $current_user, $wp_roles,$userdata,$post;
		ob_start();
		$thank_you	     		= get_user_meta( $user_identity, 'thank_you', true);
		$schedule_message	     = get_user_meta( $user_identity, 'schedule_message', true);
		
		if( empty( $thank_you ) ){
			$thank_you	= pll__('Thank you for your appointment request. Please wait for your booking to be confirmed.  You can view the status of your booking in the ‘My Bookings’ dashboard tab. If you have paid upfront for your appointment, your money will be released once the healthcare professional has approved your appointment. If your appointment has been declined by the healthcare professional, you will be refunded immediately.','docdirect');
		}
		?>
        <div class="bk-thanks-message">
            <div class="tg-message">
              <h2><?php esc_html_e('Thank you!','docdirect');?></h2>
              <div class="tg-description">
                <p><?php echo force_balance_tags( $thank_you );?></p>
              </div>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}
}

/**
 * @User Row action
 * @return 
 */
if ( !function_exists('kt_docdirect_user_user_table_action_links') )  {
	add_filter('user_row_actions', 'kt_docdirect_user_user_table_action_links', 11, 2);
	
	function kt_docdirect_user_user_table_action_links($actions, $user) {
		$is_suspend = get_user_meta($user->ID, 'suspend_search', true);
		
		$actions['docdirect_status_suspend'] = "<a style='color:" . ((isset( $is_suspend ) && $is_suspend === 'on') ? 'green' : 'red') . "' href='" . esc_url(admin_url("users.php?action=docdirect_status_suspend&users=" . $user->ID . "&nonce=" . wp_create_nonce('docdirect_change_status_suspend_' . $user->ID))) . "'>" . ((isset( $is_suspend ) && $is_suspend === 'on') ? pll__('Unsuspend Search', 'docdirect') : pll__('Suspend Search', 'docdirect')) . "</a>";
		return $actions;
	}
}

/**
 * @verify users status
 * @return 
 */
if ( !function_exists('docdirect_status_suspend') )  {
	add_action('admin_action_docdirect_status_suspend', 'docdirect_status_suspend');
	function docdirect_status_suspend() {
		
		if (isset($_REQUEST['users']) && isset($_REQUEST['nonce'])) {
			$nonce = !empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : '';
			$users = !empty( $_REQUEST['users'] ) ? $_REQUEST['users'] : '';
			
			if (wp_verify_nonce($nonce, 'docdirect_change_status_suspend_' . $users)) {
				$is_approved = get_user_meta($users, 'suspend_search', true);
				if ( isset( $is_approved ) && $is_approved === 'off' ) {
					 $new_status = 'on';
					 $message_param = 'suspend';
				} else {
					$new_status = 'off';
					$message_param = 'unsuspend';
				}
				update_user_meta($users, 'suspend_search', $new_status);
				$redirect = admin_url('users.php?updated=' . $message_param);
			} else {
				$redirect = admin_url('users.php?updated=docdirect_false');
			}
		} else {
			$redirect = admin_url('users.php?updated=docdirect_false');
		}
		wp_redirect($redirect);
	}
}

/**
 * Check if user is verified user
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'kt_docdirect_is_user_verified' ) ) {
	function kt_docdirect_is_user_verified($user_id='') {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		if( isset( $user_id ) && !empty( $user_id ) ) {
			$verify_user = get_user_meta($user_id , 'verify_user' , true);
			$user_type   = get_user_meta($user_id , 'user_type' , true);
			
			if( $user_identity == $user_id
				&&
				$user_type === 'professional'  
				&& 
				( $verify_user == 'off' || empty( $verify_user ) )
				
			){
				add_action( 'wp_footer', 'kt_docdirect_is_user_verified_message' );
			}
		}
	}
	add_action( 'docdirect_is_user_verified', 'kt_docdirect_is_user_verified' );
}

/**
 * Check if user is verified user
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'kt_docdirect_is_user_verified_message' ) ) {
	function kt_docdirect_is_user_verified_message() {
		?>
        <div class="sticky-queue bottom-right">
            <div class="sticky border-top-right important" id="s939311313">
                <span class="sticky-close"></span><p class="sticky-note">
                <?php
					wp_kses( _e( '<span>You will be visible on the directory after filling out your public profile & search criteria.</span><span>After we have reviewed your certifications you will then receive the verified physician tag.</span><span>If you have not received a verified physician tag in 3 working days then you may contact the administrator.</span>', 'docdirect' ),array(
																		'a' => array(
																			'href' => array(),
																			'title' => array()
																		),
																		'br' => array(),
																		'em' => array(),
																		'strong' => array(),
																	))?>
																	</p>
            </div>
        </div>
		<?php 
	}
}

/**
 * @Prepare social sharing links
 * @return sizes
 */
if (!function_exists('kt_docdirect_prepare_profile_social_sharing')) {

    function kt_docdirect_prepare_profile_social_sharing($thumbnail='',$user_id,$description='') {
        $facebook  = pll__('Share on Facebook' , 'docdirect');
        $twitter   = pll__('Share on Twitter' , 'docdirect');
        $gmail     = pll__('Share on Google +' , 'docdirect');
        $pinterest   = pll__('Share on Pinterest' , 'docdirect');

        if (function_exists('fw_get_db_post_option')) {
            $social_facebook  = fw_get_db_settings_option('social_facebook');
            $social_twitter   = fw_get_db_settings_option('social_twitter');
            $social_gmail     = fw_get_db_settings_option('social_gmail');
            $social_pinterest   = fw_get_db_settings_option('social_pinterest');
			$twitter_username	= !empty( $social_twitter['enable']['twitter_username'] ) ? $social_twitter['enable']['twitter_username']:'';
        } else {
            $social_facebook  = 'enable';
            $social_twitter   = 'enable';
            $social_gmail     = 'enable';
            $social_pinterest   = 'enable';
			$twitter_username	= '';
        }
		
		$user_url	=  get_author_posts_url($user_id);
		// $username   = docdirect_get_username($user_id);
        $user = get_userdata($user_id);
		$username   = kt_get_title_name($user->ID).esc_attr( $user->first_name.' '.$user->last_name );;
        $output = '';
		
		if ( ( isset($social_facebook['gadget']) && $social_facebook['gadget'] == 'enable' )
			 ||
			 ( isset($social_twitter['gadget']) && $social_twitter['gadget'] == 'enable' )
			 ||
			 ( isset($social_gmail['gadget']) && $social_gmail['gadget'] == 'enable' )
			 ||
			 ( isset($social_pinterest['gadget']) && $social_pinterest['gadget'] == 'enable' )
		) {
			
			$output .= "<div class='profile-share social-share'>";
			$output .= "<h3>".pll__('Share','docdirect')."</h3>";

			$output .= "<ul class='tg-socialiconstwo'>";
			if (isset($social_facebook) && $social_facebook == 'enable') {
				$output .= '<li class="tg-facebook"><a class="tg-social-facebook" href="http://www.facebook.com/sharer.php?u=' . urlencode( esc_url( $user_url) ) . '&picture='.$thumbnail.'&title='.$username.'&description='.$description.'" onclick="window.open(this.href, \'post-share\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"><i class="fa fa-facebook"></i><span>' .$facebook . '</span></a></li>';
			}

			if (isset($social_twitter['gadget']) 
				&& 
				$social_twitter['gadget'] == 'enable'
			) {
				$output .= '<li class="tg-twitter"><a class="tg-social-twitter" href="https://twitter.com/intent/tweet?text=' . htmlspecialchars(urlencode(html_entity_decode($username, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '&url=' . urlencode( esc_url( $user_url ) ) . '&via=' . urlencode( !empty( $twitter_username )? $twitter_username : get_bloginfo( 'name' ) ) . '"  ><i class="fa fa-twitter"></i><span>' . $twitter . '</span></a></li>';
				$tweets	= '!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");';
				wp_add_inline_script( 'docdirect_callbacks', $tweets );
			}

			if (isset($social_gmail) && $social_gmail == 'enable') {
				$output .= '<li class="tg-googleplus"><a class="tg-social-google" href="http://plus.google.com/share?url=' . esc_url( $user_url) . '" onclick="window.open(this.href, \'post-share\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"><i class="fa fa-google-plus"></i><span>'.$gmail.'</span></a></li>';
			}
			if (isset($social_pinterest) && $social_pinterest == 'enable') {
				$output .= '<li class="tg-pinterest"><a class="tg-social-pinterest" href="http://pinterest.com/pin/create/button/?url=' . esc_url( $user_url ) . '&amp;media=' . ( ! empty( $thumbnail ) ? $thumbnail : '' ) . '&description=' . htmlspecialchars(urlencode(html_entity_decode($username, ENT_COMPAT, 'UTF-8')), ENT_COMPAT, 'UTF-8') . '" onclick="window.open(this.href, \'post-share\',\'left=50,top=50,width=600,height=350,toolbar=0\'); return false;"><i class="fa fa-pinterest-p"></i><span>'.$pinterest.'</span></a></li>';
			}

			$output .= '</ul></div>';
			echo balanceTags($output , true);
		}
    }

}

/**
 * @Product Image 
 * @return {}
 */
if (!function_exists('docdirect_get_user_avatar_fallback')) {
	function docdirect_get_user_avatar_fallback($object, $atts=array()){
		extract(shortcode_atts(array(
			"width" => '300',
			"height" => '300',
			),
		$atts));
		
		if( isset( $object ) 
			&& !empty( $object ) 
			&& $object != NULL 
		){
			return $object;
		} else{
			return get_stylesheet_directory_uri().'/images/profile'.$width.'x'.$height.'.jpg';
		}
	}
	
	add_filter( 'docdirect_get_user_avatar_filter', 'docdirect_get_user_avatar_fallback', 10, 3 );
}

if (!function_exists('docdirect_get_user_avatar_fallback2')) {
	function docdirect_get_user_avatar_fallback2($object, $atts=array()){
		extract(shortcode_atts(array(
			"width" => '300',
			"height" => '300',
			),
		$atts));
		
		if( isset( $object ) 
			&& !empty( $object ) 
			&& $object != NULL 
		){
			return $object;
		} else{
			return get_template_directory_uri().'/images/user'.$width.'x'.$height.'.jpg';
		}
	}
	
	add_filter( 'docdirect_get_user_avatar_filter2', 'docdirect_get_user_avatar_fallback2', 10, 3 );
}

/**
 * @get Excerpt
 * @return link
 */
if (!function_exists('kt_crop_article_excerpt')) {

    function kt_crop_article_excerpt($charlength = '255', $more = 'true', $text = 'Read More',$strip_tags='yes', $meta_key) {
        global $post;
        $excerpt = get_post_meta(get_the_ID(), $meta_key, true );
        if (strlen($excerpt) > $charlength) {
            if ($charlength > 0) {
                $excerpt = substr($excerpt, 0, $charlength);
            } else {
                $excerpt = $excerpt;
            }
            if ($more == 'true') {
                $link = '<a href="' . esc_url(get_permalink()) . '" class="tg-more">' . esc_attr($text) . '</a>';
            } else {
                $link = '...';
            }
            
			if( isset( $strip_tags ) && $strip_tags === 'yes' ){
				echo wp_strip_all_tags($excerpt) . $link;
			} else{
				echo force_balance_tags($excerpt . $link);
			}
			
        } else {
			if( isset( $strip_tags ) && $strip_tags === 'yes' ){
				echo wp_strip_all_tags($excerpt);
			} else{
				echo force_balance_tags($excerpt);
			}
        }
    }

}

/**
 * Order_Status
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'kt_docdirect_prepare_order_status' ) ) {
	function kt_docdirect_prepare_order_status($type="array",$index='cancelled'){
		$status	= array(
					'approved'	=> pll__('Approved', 'docdirect'),
					'pending'	=> pll__('Pending', 'docdirect'),
					'cancelled'	=> pll__('Rejected', 'docdirect'),
				);
		
		if( $type === 'array' ){
			return $status;
		}else{
			if( isset( $status[$index] ) ){
				return  '<span class="'.$index.'">'.$status[$index].'</span>';
			} else{
				return '';
			}
			
		}
	}
}

/**
 * @get all specialities
 * @return 
 */
if (!function_exists('docdirect_prepare_specialities')) {
	function docdirect_prepare_specialities(){
		global $post;
		$args = array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'specialities',
			'pad_counts'               => false 
		); 
		
		$specialities = get_categories($args); 
		//$specialities_attached	= docdirect_get_attached_specialities();
		$specialities_attached	= array();

		$speciality_array	= array();
		foreach ( $specialities as $speciality ) {
			if (function_exists('kt_read_specialities')) {
				$list_sp = kt_read_specialities();
			}
			if ( is_array($specialities_attached) && !in_array( $speciality->term_id,$specialities_attached) ) {
		     	$img = '';
				if (!empty($list_sp[$speciality->term_id][1])) {
					$img = '<img width="150" height="150" src="'.$list_sp[$speciality->term_id][1].'">';
				}
				$speciality_array[$speciality->term_id]	= $img.$speciality->name;
			}
		}
		
		return $speciality_array;
	}
}


function kt_ajax_search_user_inactive() {
	global $current_user, $wp_roles,$userdata,$post;

	$exclude = array_unique(array_merge($ex1, $ex2));

	$s = sanitize_text_field($_POST['search_string']);
	$directory_type = $_POST['type_category'];

	if($s != '') {
		$meta_query_args = array();

		$query_args	= array(
					'role'  => 'temporary_profile',
					);
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
	    $meta_query_args[] = array(
	                  'key'     => 'verify_user',
	                  'value'   => 'on',
	                  'compare' => '!='
	                );

		if( !empty( $meta_query_args ) ) {
			$query_relation = array('relation' => 'AND',);
			$meta_query_args	= array_merge( $query_relation,$meta_query_args );
			$query_args['meta_query'] = $meta_query_args;
		}

		$user_query  = new WP_User_Query($query_args);
		$output = '';
		if ( ! empty( $user_query->results ) ) {
			$output .= '<ul class="">';

			foreach ( $user_query->results as $user ) {
				$output .= '<li class="">';
				$output .= '<div class="col-xs-8">';
				$output .= '<h4>'.$user->first_name.' '.$user->last_name.'</h4>';
				$output .= kt_get_tag_company($user->ID,$icon='',$echo=false);
				$output .= '</div><div class="col-xs-4">';
				$output .= '<a class="open_modal_active" data-user_id="'.$user->ID.'" href="javascript:;">'.pll__('Activate').'</a>';
				$output .= '</div></li>';
			}

			$output .= '</ul>';
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

add_action('wp_ajax_search_user_inactive','kt_ajax_search_user_inactive');
add_action( 'wp_ajax_nopriv_search_user_inactive', 'kt_ajax_search_user_inactive' );


add_action('wp_footer', 'kt_modal_inactive');
function kt_modal_inactive() {
	?>
	<?php 
		global $current_user;
		if(!is_user_logged_in()) {
	?>
	<div class="modal fade modal_inactive tg-request-only" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  	  <div class="modal-dialog tg-modal-content" role="document">
  	  	<div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><?php pll_e( 'Activate' ) ?></h4>
	    </div>
		<form action="#" method="post" class="tg-form-modal activate-form">
	      <fieldset class="booking-model-contents">
	      	<div class="form-group">
			    <label for="ac_email"><?php pll_e( 'Email' ) ?></label>
			    <input type="text" name="email" class="form-control" id="ac_email" placeholder="<?php pll_e( 'email' ) ?>">
			    <p><?php pll_e('Please enter your public company email to receive your profile activation code.');?></p>
			</div>
	      	<div class="form-group">
	      		<input class="ac_user_id" type="hidden" name="user_id">
		        <input type="submit" name="submit" class="tg-btn submit_activate" value="<?php pll_e('Submit');?>">
        	</div>
	      </fieldset>
	    </form>
	  </div>
	</div>
	<?php }
}


function kt_ajax_check_activate() {

  	$user_id = isset( $_POST['user_id'] ) ? esc_sql( $_POST['user_id'] ) : '';
  	$email = isset( $_POST['email'] ) ? $_POST['email'] : '';
  	if ($email == '') {
		$json['type']     = 'error';
		$json['message']  = pll__('Email empty.');
		echo json_encode($json);
		die;
  	}else if( !is_email( $email ) ) {
		$json['type']     = 'error';
		$json['message']  = pll__('Email not valid.');
		echo json_encode($json);
		die;
  	}else{
		$user = get_userdata($user_id);
		$user_email = $user->user_email;
		if( $user_email == $email ) {

			$email_helper	= new DocDirectProcessEmail();
			
			$emailData	= array();
			$emailData['user_identity']	=  $user_id;
			$emailData['first_name']	   =  $_POST['first_name'];
			$emailData['last_name']		=  $_POST['last_name'];
			$emailData['password']	=  $random_password;
			$emailData['username']	=  $username;
			$emailData['email']	   =  $email;

			$key_hash = md5(uniqid(openssl_random_pseudo_bytes(32)));
			update_user_meta( $user_id, 'confirmation_key', $key_hash);

			$page_activate = get_permalink( get_page_by_path( 'activate-account' ) );

			$protocol = is_ssl() ? 'https' : 'http';

			$verify_link = esc_url(add_query_arg(array(
				'key' => $key_hash.'&verifyemail='.$email
							), $page_activate));

			$emailData['verify_link'] 	 = $verify_link;
			$email_helper->process_email_verification($emailData);

			$json['data']	 = $verify_link;
			$json['type']	 = 'success';
			$json['message']  = pll__('An email was sent. Please check your inbox','docdirect');
			echo json_encode($json);
			die;
		}else {
			$page_contact = get_permalink( get_page_by_path( 'activate-account-2' ) );
			$json['redirect']     = $page_contact;
			$json['type']     = 'error';
			$json['message']  = pll__('Email not match. Please contact with admin');
			echo json_encode($json);
			die;
		}
	}

}

add_action('wp_ajax_check_activate','kt_ajax_check_activate');
add_action( 'wp_ajax_nopriv_check_activate', 'kt_ajax_check_activate' );

add_shortcode('form_activate', 'kt_form_activate');
function kt_form_activate($atts) {
	$atts = shortcode_atts( array(
		
	), $atts);
	ob_start();
	global $wpdb;
	global $current_user, $wp_roles,$userdata,$post;
	?>
	<div class="">
		<?php
    	if ( !empty($_GET['key']) && !empty($_GET['verifyemail']) && !is_user_logged_in() ) {
            $verify_key 	= esc_attr( $_GET['key'] );
            $user_email 	= esc_attr( $_GET['verifyemail'] );

            $user_identity = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", $user_email));
			if( !empty( $user_identity ) ){
				$confirmation_key = get_user_meta(intval( $user_identity ), 'confirmation_key', true);
				if ( $confirmation_key === $verify_key ) {
					?>
					<div class="col-xs-6">
						<h3><?php pll_e('You must change password to activate account');?></h3>
						<form action="#" method="post" class="changepass-form">
					      	<div class="form-group">
							    <label for="pass1"><?php pll_e( 'New password' ) ?></label>
							    <input type="password" name="pass1" class="form-control" id="pass1">
							</div>
					      	<div class="form-group">
							    <label for="pass2"><?php pll_e( 'Retype password' ) ?></label>
							    <input type="password" name="pass2" class="form-control" id="pass2">
							</div>
					      	<div class="form-group"">
						        <input type="submit" name="submit" class="tg-btn submit_changepass" value="<?php pll_e('Submit');?>">
				        	</div>
					    </form>
					    <div class="clearfix"></div>
					    <?php
					    if (isset($_POST['submit'])) {
  							$pass1 = isset( $_POST['pass1'] ) ? esc_sql($_POST['pass1']) : '';
  							$pass2 = isset( $_POST['pass2'] ) ? esc_sql($_POST['pass2']) : '';
  							if ($pass1 != $pass2) {
  								?>
  								<div class="alert alert-danger"><?php pll_e('Error');?></div>
  								<?php
  							}else {
  								wp_set_password( $pass1, $user_identity );
								update_user_meta($user_identity, 'verify_user', 'on');
								$u = new WP_User( $user_identity );
								// Remove role
								$u->remove_role( 'temporary_profile' );

								// Add role
								$u->add_role( 'professional' );
  								?>
  								<div class="alert alert-success"><?php pll_e('Success');?></div>
  								<?php
					            function kt_add_code_footer_activate() {
									global $current_user;
									$user_identity	= $current_user->ID;

									$dir_profile_page = '';
									if (function_exists('fw_get_db_settings_option')) {
								        $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
								    }
									$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
									
									$login_redirect = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'settings','',true);
									var_dump($login_redirect);
					            	?>
					                <script type="text/javascript">
					                	jQuery(document).ready(function($){
					                		setTimeout(function(){location.href = '<?php echo $login_redirect;?>'} , 2000);
											// jQuery('.tg-user-modal').modal('toggle');
											// jQuery('input.login_redirect').val('');
										});
					                </script>
					            <?php }
					            add_action('wp_footer', 'kt_add_code_footer_activate', 100);
  							}
					    }
					    ?>
					</div>
					<?php
				}else {?>
					error
				<?php }
			}
		}?>
	</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

/**
 * Add role
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'kt_add_user_roles' ) ) {
	function kt_add_user_roles() {
		
		$professional = add_role('temporary_profile', esc_html__('Temporary Profile','docdirect'));
	}
	add_action( 'admin_init', 'kt_add_user_roles' );
}


function docdirect_do_check_user_type($user_identity){
	if( isset( $user_identity ) && !empty( $user_identity ) ) {
		$data	= get_userdata( $user_identity );
		if( isset( $data->roles[0] ) && !empty( $data->roles[0] ) && ( $data->roles[0] === 'temporary_profile' || $data->roles[0] === 'professional' || $data->roles[0] === 'administrator' ) ){
			return true;
		} else {
			return false;
		}
	}
	
	return false;
}
add_filter( 'docdirect_do_check_user_type', 'docdirect_do_check_user_type', 10, 3 );



