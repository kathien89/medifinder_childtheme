<?php
/**
 * Update User Password
 *
 * @param json
 * @return string
 */
if ( ! function_exists( 'kt_docdirect_do_process_subscription' ) ) {

	remove_action('wp_ajax_docdirect_do_process_subscription', 'docdirect_do_proccess_subscription');
	remove_action('wp_ajax_nopriv_docdirect_do_process_subscription', 'docdirect_do_process_subscription');

	function kt_docdirect_do_process_subscription() {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		$json	=  array();

		$user = wp_get_current_user(); //trace($user);
		
		$do_check = check_ajax_referer( 'docdirect_renew_nounce', 'renew-process', false );
		if( $do_check == false ){
			$json['type']	= 'error';
			$json['message']	= esc_html__('No kiddies please!','docdirect');	
			$json['payment_type']  = 'gateway';
			echo json_encode($json);
			die;
		}
		
		//Account de-activation			
		
		if ( empty( $_POST['packs'] ) ) {
			$json['type']		=  'error';	
			$json['message']		= esc_html__('Please select a plan to subscribe.','docdirect');	
			$json['payment_type']  = 'gateway';
			echo json_encode($json);
			exit;
		} else if ( empty( $_POST['gateway'] ) ) {
			$json['type']		=  'error';	
			$json['message']		= esc_html__('Please select a payment gateway to subscribe.','docdirect');	
			$json['payment_type']  = 'gateway';
			echo json_encode($json);
			exit;
		} else if ( empty( $_POST['packs'] ) ||  empty( $_POST['gateway'] ) ) { 
			$json['type']		=  'error';	
			$json['message']		= esc_html__('Some error occur, please try again later.','docdirect');
			$json['payment_type']  = 'gateway';	
			echo json_encode($json);
			exit;
		}
		
		
		
		$pack_title		  = get_the_title( $_POST['packs'] ); 
		$duration 			= fw_get_db_post_option($_POST['packs'], 'duration', true);
		$price 			   = fw_get_db_post_option($_POST['packs'], 'price', true);
		$pac_subtitle 		= fw_get_db_post_option($_POST['packs'], 'pac_subtitle', true);
		$currency_select 	 = fw_get_db_settings_option('currency_select');
		$currency_sign  	   = fw_get_db_settings_option('currency_sign');
		
			
		if( isset( $_POST['gateway'] ) && $_POST['gateway'] === 'paypal' ){
			
			/*---------------------------------------------
			 * @Paypal Payment Gateway Process
			 * @Return HTML
			 ---------------------------------------------*/
			$sandbox_enable = fw_get_db_settings_option('paypal_enable_sandbox');
			$business_email = fw_get_db_settings_option('paypal_bussiness_email');
			$listner_url    = fw_get_db_settings_option('paypal_listner_url');
		
			$package_name	= $pack_title.' - '.$duration.esc_html__('Days','docdirect');
			
            if (isset($sandbox_enable) && $sandbox_enable == 'on') {
                $paypal_path = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
            } else {
                $paypal_path = 'https://www.paypal.com/cgi-bin/webscr';
            }

            // if ($currency_select == '' || $business_email == '' || $listner_url == '') {
            if ($currency_select == '') {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Some error occur please contact to administrator.', 'docdirect');
                echo json_encode($json);
                die;
            }
			
			//prepare return url
			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
            }
			
			//Add New Order
			$order_no	= docdirect_add_new_order(
				array(
					'packs'		=> sanitize_text_field( $_POST['packs'] ),
					'gateway'	  => sanitize_text_field( $_POST['gateway'] ),
					'price'		=> number_format((float)$price, 2, '.', ''),
					'payment_type' => 'gateway',
					'mc_currency'  => $currency_select,
				)
			);
			
			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
			
			$return_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity,true);

			$cancel_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity,true);
			
			$custom = array();
			$custom['order_no'] = $order_no;
			$custom['packs'] = $_POST['packs'];
			$custom['user_identity'] = $user_identity;

			// Create a new PayPal class instance, and set the sandbox mode to true
			$paypal = new wp_paypal_gateway (true);
			 
			$requestParams = array(
			    'RETURNURL' => get_the_permalink($profile_page), //Enter your webiste URL here
			    'CANCELURL' => $cancel_url//Enter your website URL here
			); 
			$orderParams = array(
			    'LOGOIMG' => "http://demo.tntechs.com.vn/thienvk/medifinder/wp-content/uploads/2016/10/logo-11.png", //You can paste here your logo image URL
			    // "MAXAMT" => "100", //Set max transaction amount
			    "NOSHIPPING" => "1", //I do not want shipping
			    "ALLOWNOTE" => "0", //I do not want to allow notes
			    "BRANDNAME" => $package_name,
			    "GIFTRECEIPTENABLE" => "0",
			    "GIFTMESSAGEENABLE" => "0"
			);
			$item = array(
			    'LOCALECODE' => 'en_UK',
			 
			    'PAYMENTREQUEST_0_AMT' => $price,
			    'PAYMENTREQUEST_0_CURRENCYCODE' => 'HKD',
			    'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			    'PAYMENTREQUEST_0_ITEMAMT' => $price,
    			'PAYMENTREQUEST_0_CUSTOM' => json_encode($custom),
			 
			    'L_PAYMENTREQUEST_0_NAME0' => $pack_title,
			    'L_PAYMENTREQUEST_0_DESC0' => $pack_title,
			    'L_PAYMENTREQUEST_0_QTY0' => 1,
			    'L_PAYMENTREQUEST_0_AMT0' => $price,

			    'L_BILLINGTYPE0' => 'RecurringPayments',
			    'L_BILLINGAGREEMENTDESCRIPTION0' => $package_name,
			        //"PAYMENTREQUEST_0_INVNUM" => $transaction->id - This field is useful if you want to send your internal transaction ID
			);
			 
			// Display the response if successful or the debug info

		    $paypal->setExpressCheckout($requestParams + $orderParams + $item);
		    $response = $paypal->getResponse();

			if(is_array($response) && $response["ACK"]=="Success"){
			// if ($paypal->setExpressCheckout($requestParams + $orderParams + $item)) {
			    // print_r($paypal->getResponse());
			    $json['url']  = $url = $paypal->getRedirectURL();
				$output  = '';
				$output .= '<script>
								window.location = "'.$url.'";
						  </script>';

	            $json['form_data']  = $output;
	            $json['type'] 		= 'success';
				$json['payment_type']  = 'paypal';
			} else {
	            $json['message']  = 'Error';
	            $json['form_data']  = 'Error';
	            $json['type'] 		= 'error';
				$json['payment_type']  = 'paypal';
			}
	
		}else if( isset( $_POST['gateway'] ) && $_POST['gateway'] === 'stripe' ){
				/*---------------------------------------------
				 * @Strip Payment Gateway Process
				 * @Return HTML
				 ---------------------------------------------*/
				 $currency_sign   = '';
				 $stripe_secret    = '';
				 $stripe_publishable = '';
				 $stripe_site     = '';
				 $stripe_decimal  = '';
					
				 if (function_exists('fw_get_db_settings_option')) {
					$currency_sign   = fw_get_db_settings_option('currency_select');
					$stripe_secret    = fw_get_db_settings_option('stripe_secret');
					$stripe_publishable = fw_get_db_settings_option('stripe_publishable');
					$stripe_site     = fw_get_db_settings_option('stripe_site');
					$stripe_decimal  = fw_get_db_settings_option('stripe_decimal');
				 }
				 
				 $total_amount	= $price;
				 
				 if( isset( $stripe_decimal ) && $stripe_decimal == 0 ){
					$package_amount	= $price;
				 } else{
					$package_amount	= $price.'00';	 
				 }
				  
				  
				//prepare return url
				$dir_profile_page = '';
				if (function_exists('fw_get_db_settings_option')) {
					$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
				}
				
				//Add New Order
				$order_no	= docdirect_add_new_order(
					array(
						'packs'		=> sanitize_text_field( $_POST['packs'] ),
						'gateway'	  => sanitize_text_field( $_POST['gateway'] ),
						'price'		=> number_format((float)$price, 2, '.', ''),
						'payment_type' => 'gateway',
						'mc_currency'  => $currency_select,
					)
				);
				
				$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
				
				$return_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity,true);
				
				$userdata	  = get_userdata( $user_identity );
				$user_email	= '';
				if( !empty( $userdata ) ) {
					$user_email	= $userdata->user_email;
				}
				
				$first_name	 = get_user_meta($user_identity,'first_name',true);
				$last_name	 = get_user_meta($user_identity,'last_name',true);
				$user_name	 = get_user_meta($user_identity,'first_name',true).' '.get_user_meta($user_identity,'last_name',true);
				$useraddress   = get_user_meta($user_identity,'address',true);

				
				$package_name	= $pack_title.' - '.$duration.esc_html__(' Days','docdirect');
				   
				echo json_encode( 
					array( 
						   'first_name' 	  => $first_name,
						   'last_name' 	   => $last_name,
						   'username' 	 	=> $user_name,
						   'email' 		   => $user_email,
						   'useraddress'     => $useraddress,
						   'order_no' 	    => $order_no,
						   'user_identity'   => $user_identity,
						   'package_id' 	  => $_POST['packs'],
						   'package_name'    => $package_name,
						   'gateway' 	  => 'stripe',
						   'type' 		 => 'success',
						   'payment_type' => 'stripe',
						   'process'=>true, 
						   'name'=> $stripe_site, 
						   'description'=> $package_name,
						   'amount' => $package_amount,
						   'total_amount' => $total_amount,
						   'key'=> $stripe_publishable,
						   'currency'=> $currency_sign
						  )
					);
				 
				 die;

		}else if( isset( $_POST['gateway'] ) && $_POST['gateway'] === 'authorize' ){			
			/*---------------------------------------------
			 * @Authorize.Net Payment Gateway Process
			 * @Return HTML
			 ---------------------------------------------*/
			
			$current_date   				 = date('Y-m-d H:i:s');
			$output					   = '';
			$authorize_login_id 		   = fw_get_db_settings_option('authorize_login_id');
			$authorize_transaction_key 	= fw_get_db_settings_option('authorize_transaction_key');
			$authorize_listner_url 		= fw_get_db_settings_option('authorize_listner_url');
			$authorize_enable_sandbox 	 = fw_get_db_settings_option('authorize_enable_sandbox');
			
			$timeStamp	= time();
			$sequence	 = rand(1, 1000);
			
			if( phpversion() >= '5.1.2' ) {
				{ $fingerprint = hash_hmac("md5", $authorize_login_id . "^" . $sequence . "^" . $timeStamp . "^" . $price . "^". $currency_select, $authorize_transaction_key); }
			} else {
				$fingerprint = bin2hex(mhash(MHASH_MD5, $authorize_login_id . "^" . $sequence . "^" . $timeStamp . "^" . $price . "^". $currency_select, $authorize_transaction_key));
			}
				
			$package_name	= $pack_title.' - '.$duration.esc_html__('Days','docdirect');
			

            if ($currency_select == '' || $authorize_login_id == '') {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Some error occur please contact to administrator.', 'docdirect');
                echo json_encode($json);
                die;
            }
			
			if (isset($authorize_enable_sandbox) && $authorize_enable_sandbox == 'on') {
                $gateway_path = 'https://test.authorize.net/gateway/transact.dll';
            } else {
                $gateway_path = 'https://secure.authorize.net/gateway/transact.dll';
            }
			
			//Add New Order
			$order_no	= docdirect_add_new_order(
				array(
					'packs'		=> sanitize_text_field( $_POST['packs'] ),
					'gateway'	  => sanitize_text_field( $_POST['gateway'] ),
					'price'		=> $price,
					'payment_type' => 'gateway',
				)
			);
			
			//prepare return url
			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
            }

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
			
			$return_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity,true);
			
			$output .= '<form name="AuthorizeForm" id="authorize-form" action="'.$gateway_path.'" method="post">  
							<input type="hidden" name="x_login" value="'.$authorize_login_id.'">
							<input type="hidden" name="x_type" value="AUTH_CAPTURE"/>
							<input type="hidden" name="x_amount" value="'.$price.'">
							<input type="hidden" name="x_fp_sequence" value="'.$sequence.'" />
							<input type="hidden" name="x_fp_timestamp" value="'.$timeStamp.'" />
							<input type="hidden" name="x_fp_hash" value="'.$fingerprint.'" />
							<input type="hidden" name="x_show_form" value="PAYMENT_FORM" />
							<input type="hidden" name="x_invoice_num" value="'.$package_name.'">
							<input type="hidden" name="x_po_num" value="'.$order_no.'|_|'.$_POST['packs'].'|_|'.$user_identity.'">
							<input type="hidden" name="x_cust_id" value="'.sanitize_text_field($order_no).'"/> 
							<input type="hidden" name="x_first_name" value="'.get_user_meta('first_name' ,$user_identity).'"> 
							<input type="hidden" name="x_last_name" value="'.get_user_meta('last_name' ,$user_identity).'"> 
							<input type="hidden" name="x_address" value="'.get_user_meta( 'address' ,$user_identity).'"> 
							<input type="hidden" name="x_fax" value="'.get_user_meta('fax' ,$user_identity).'"> 
							<input type="hidden" name="x_email" value="'.get_user_meta('email' ,$user_identity).'"> 
							<input type="hidden" name="x_description" value="'.$package_name.'">
							<input type="hidden" name="x_currency_code" value="'.$currency_select.'" />	 
							<input type="hidden" name="x_cancel_url" value="'.esc_url( $return_url ).'" />
							<input type="hidden" name="x_cancel_url_text" value="Cancel Order" />
							<input type="hidden" name="x_relay_response" value="TRUE" />
							<input type="hidden" name="x_relay_url" value="'.sanitize_text_field( $authorize_listner_url ).'"/> 
							<input type="hidden" name="x_test_request" value="false"/>
						</form>';					

            $output .= '<script>
							jQuery("#authorize-form").submit();
					  </script>';

            $json['form_data']  = $output;
            $json['type'] 		= 'success';
			$json['payment_type']  = 'gateway';
	
		} else if( isset( $_POST['gateway'] ) && $_POST['gateway'] === 'bank' ){			
			/*---------------------------------------------
			 * @Bank Transfer Gateway Process
			 * @Return HTML
			 ---------------------------------------------*/
			$bank_name = fw_get_db_settings_option('bank_name');
			$bank_account = fw_get_db_settings_option('bank_account');
			$other_information    = fw_get_db_settings_option('other_information');
			$package_name	= $pack_title.'-'.$duration.' '.esc_html__('Days','docdirect');
			$first_name	  = get_user_meta($user_identity,'first_name',true);
			$last_name	 = get_user_meta($user_identity,'last_name',true);
			$user_name	 = get_user_meta($user_identity,'first_name',true).' '.get_user_meta($user_identity,'last_name',true);
			$useraddress   = get_user_meta($user_identity,'address',true);
			$package_id	= $_POST['packs'];
			
			$payment_date = date('Y-m-d H:i:s');
			$user_featured_date	= get_the_author_meta('user_featured',$user_identity, true);
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
			
			$userdata	  = get_userdata( $user_identity );
			$user_email	= '';
			if( !empty( $userdata ) ) {
				$user_email	= $userdata->user_email;
			}
				
		
			//Add New Order
			$order_no	= docdirect_add_new_order(
				array(
					'packs'		=> sanitize_text_field( $_POST['packs'] ),
					'gateway'	  => sanitize_text_field( $_POST['gateway'] ),
					'price'		=> $price,
					'mc_currency'  => $currency_select,
				)
			);
			
			$html	= '';
			$html	.= '<div class="membership-price-header">'.esc_html__('Order Summary','docdirect').'</div>';
			$html	.= '<div class="system-gateway">';
			
			$html	.= '<ul>';
				$html	.= '<li>';
					$html	.= '<label for="doc-payment-bank">'.esc_html__('General Information','docdirect').'</label>';
					$html	.= '<ul>';
						$html	.= '<li>';
						$html	.= '<span class="pull-left col-md-6 col-xs-12">'.esc_html__('Order No','docdirect').'</span>';
						$html	.= '<span class="pull-right col-md-6 col-xs-12">'.$order_no.'</span>';
						$html	.= '</li>';
						
						$html	.= '<li>';
						$html	.= '<span class="pull-left col-md-6 col-xs-12">'.esc_html__('Package Name','docdirect').'</span>';
						$html	.= '<span class="pull-right col-md-6 col-xs-12">'.get_the_title($_POST['packs']).'</span>';
						$html	.= '</li>';
					
						$html	.= '<li>';
						$html	.= '<span class="pull-left col-md-6 col-xs-12">'.esc_html__('Price','docdirect').'</span>';
						$html	.= '<span class="pull-right col-md-6 col-xs-12">'.$currency_sign.$price.'</span>';
						$html	.= '</li>';
						
						
					$html	.= '</ul>';
				$html	.= '</li>';
				$html	.= '<li>';
					$html	.= '<label for="doc-payment-bank">'.esc_html__('Bank Information','docdirect').'</label>';
					$html	.= '<ul>';
						if( !empty( $other_information ) ) {
							$html	.= '<li>';
							$html	.= '<span class="pull-left col-md-6 col-xs-12">'.esc_html__('Bank Name','docdirect').'</span>';
							$html	.= '<span class="pull-right col-md-6 col-xs-12">'.$bank_name.'</span>';
							$html	.= '</li>';
						}
						
						if( !empty( $other_information ) ) {
							$html	.= '<li>';
							$html	.= '<span class="pull-left col-md-6 col-xs-12">'.esc_html__('Bank Account No','docdirect').'</span>';
							$html	.= '<span class="pull-right col-md-6 col-xs-12">'.$bank_account.'</span>';
							$html	.= '</li>';
						}
						
						if( !empty( $other_information ) ) {
							$html	.= '<li>';
							$html	.= '<span class="pull-left col-md-6 col-xs-12">'.esc_html__('Other Information','docdirect').'</span>';
							$html	.= '<span class="pull-right col-md-6 col-xs-12">'.$other_information.'</span>';
							$html	.= '</li>';
						}
					$html	.= '</ul>';
				$html	.= '</li>';
			$html	.= '</ul>';
			
			
			//Send ean email 
			if( class_exists( 'DocDirectProcessEmail' ) ) {
				$email_helper	= new DocDirectProcessEmail();
				$emailData	   = array();
				$emailData['mail_to']	  	   = $user_email;
				$emailData['name']			  = $user_name;
				$emailData['invoice']	  	   = $order_no;
				$emailData['package_name']	  = $package_name;					
				$emailData['amount']			= $currency_sign.$price;
				$emailData['status']			= esc_html__('Pending','docdirect');
				$emailData['method']			= esc_html__('Bank Transfer','docdirect');
				$emailData['date']			  = date('Y-m-d H:i:s');
				$emailData['expiry']			= $featured_date;
				$emailData['address']		   = $useraddress;
				
				$email_helper->process_invoice_email( $emailData );
			}
			
            $json['form_data']  = $html;
			$json['payment_type']  = 'bank';
            $json['type'] 		= 'success';
	
		} else{
			$json['message'] = esc_html__('Some error occur please contact to administrator.', 'docdirect');
            $json['type'] 		= 'error';
			$json['payment_type']  = 'gateway';
		}
		
		echo json_encode($json);
		exit;
	}
	add_action('wp_ajax_docdirect_do_process_subscription', 'kt_docdirect_do_process_subscription');
	add_action('wp_ajax_nopriv_docdirect_do_process_subscription', 'kt_docdirect_do_process_subscription');
}


if ( ! function_exists( 'kt_cancel_subcription' ) ) {

	function kt_cancel_subcription() {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		$json	=  array();
		$json['type'] 		= 'error';
		$json['message'] 		= 'error';

		$payment_profileid = get_user_meta($user_identity, 'payment_profileid', true);

		$requestParams = array('PROFILEID' => $payment_profileid);
	    $paypal = new wp_paypal_gateway (true);
	    $paypal->GetRecurringPaymentsProfileDetails($requestParams);
	    $response = $paypal->getResponse();
		if(is_array($response) && $response["ACK"]=="Success"){

			if ($response["STATUS"] == 'Active') {
				$requestParams = array(
		        	'PROFILEID' => $payment_profileid,
		        	'ACTION'    => 'Cancel'
		        );
	    		$paypal->ManageRecurringPaymentsProfileStatus($requestParams);
	    		$response = $paypal->getResponse();

	    		delete_user_meta($user_identity, 'payment_profileid');
	    		// delete_user_meta($user_identity, 'user_current_package');
		        
				$json['type'] 		= 'success';
				$json['message'] 	= 'Cancel Package Successful';
			}

		}else {
			$json['message'] 		= 'error';
		}

		echo json_encode($json);
		exit;
	}
	add_action('wp_ajax_cancel_subscription', 'kt_cancel_subcription');
	add_action('wp_ajax_nopriv_cancel_subscription', 'kt_cancel_subcription');
}


/**
 * @Make Review
 * @return 
 */
if ( ! function_exists( 'kt_docdirect_make_review' ) ) {

	remove_action('wp_ajax_docdirect_make_review','docdirect_make_review');
	remove_action( 'wp_ajax_nopriv_docdirect_make_review', 'docdirect_make_review' );

	function kt_docdirect_make_review() {
		global $current_user, $wp_roles,$userdata,$post;

		$user_to	= isset( $_POST['user_to'] ) && !empty( $_POST['user_to'] ) ? $_POST['user_to'] : '';
		$dir_review_status	= 'pending';
		if (function_exists('fw_get_db_settings_option')) {
            $dir_review_status = fw_get_db_settings_option('dir_review_status', $default_value = null);
        }

		/*$number_review_3months = kt_count_reviews_3months($current_user->ID ,$_POST['user_to']);
		if ($number_review_3months > 1) {
			$json['type']		= 'error';
			$json['message']	 = pll__('Max 2 reviews in 90 days .');	
			echo json_encode($json);
			die;
		}*/
		
        if(isset($_POST['guest_name'])) {
			if( $_POST['guest_name'] == '' 
				|| $_POST['guest_email'] == '' 
				|| $_POST['review_code'] == ''
			) {
				$json['type']	= 'error';
				$json['message']	= pll__('Please fill guest fields.');	
				echo json_encode($json);
				die;
			}
			if ( !is_email( $_POST['guest_email']  ) ) {
				$json['type']	= 'error';
				$json['message']	= pll__('Email address is invalid.');	
				echo json_encode($json);
				die;
			}
		}

		$user_rating1	   = $_POST['user_rating'];	
		$detail_rating	   = $_POST['detail_rating'];
		$final_detail = array();
		foreach ($detail_rating as $key => $value) {
			$final_detail[$key] = array( $user_rating1[$key], $value );
		}

		/*$reviews_query = new WP_Query($user_reviews);
		$reviews_count = $reviews_query->post_count;
		if( isset( $reviews_count ) && $reviews_count > 0 ){
			$json['type']		= 'error';
			$json['message']	= pll__('You have already submit a review.', 'docdirect');
			echo json_encode($json);
			die();
		}*/
		
        if(isset($_POST['review_code']) && $_POST['review_code'] != '') {
			/*$val_object = kt_check_review_code_by_user( $_POST['review_code'], $user_to );
			if ($val_object == false) {
				$json['type']	= 'error';
				$json['message']	= pll__('Review Code not exists.');	
				echo json_encode($json);
				die;
			}else {
				$invite_review_id = $val_object->ID;
			}*/
        }
        
		if(count($_POST['user_rating']) < 6 ) {
			$json['type']		= 'error';
			$json['message']	 = pll__('Please fill all rating');	
			echo json_encode($json);
			die;
		}
        
		$db_directory_type	 = get_user_meta( $user_to, 'directory_type', true);
			
		if( $_POST['user_subject'] != '' 
			&& $_POST['user_description'] != '' 
			&& $_POST['user_to'] != ''
			// && $_POST['review_code'] != ''
		) {
		
			$user_subject	  = sanitize_text_field( $_POST['user_subject'] );
			$user_description  = sanitize_text_field( $_POST['user_description'] );
			$user_rating	   = sanitize_text_field( json_encode($_POST['user_rating']) );	
			if( is_user_logged_in() ){
				$user_from	     = sanitize_text_field( $current_user->ID );
			}else {
				$user_from = sanitize_text_field( $_POST['user_to'] );
			}
			$user_to	   	   = sanitize_text_field( $_POST['user_to'] );
			$directory_type	   	   = $db_directory_type;
			
			$review_post = array(
				'post_title'  => $user_subject,
				'post_status' => $dir_review_status,
				'post_content'=> $user_description,
				'post_author' => $user_from,
				'post_type'   => 'docdirectreviews',
				'post_date'   => current_time('Y-m-d H:i:s')
			);
			
			$post_id = wp_insert_post( $review_post );
	
			$review_meta = array(
				'user_rating' 	 => $user_rating,
				'user_from' 	   => $user_from,
				'user_to'   		 => $user_to,
				'detail_rating'   		 => $detail_rating,
				'directory_type'  => $directory_type,
				'review_date'   	 => current_time('Y-m-d H:i:s'),
			);
			if( !is_user_logged_in() ){
				$review_meta['guest_name'] = $_POST['guest_name'];
				$review_meta['guest_email'] = $_POST['guest_email'];
				$review_meta['review_code'] = $_POST['review_code'];
			}

			update_post_meta( $invite_review_id, 'status', 'completed' );

			//Update post meta
			foreach( $review_meta as $key => $value ){
				update_post_meta($post_id,$key,$value);
			}
			
			$new_values = $review_meta;
			
			if (isset($post_id) && !empty($post_id)) {
				fw_set_db_post_option($post_id, null, $new_values);
			}
			
			$json['type']	   = 'success';
			

			if( isset( $dir_review_status ) && $dir_review_status == 'publish' ) {
				$json['message']	= pll__('Your review published successfully.');
				$json['html']	   = 'refresh';
			} else{
				$json['message']	= pll__('Your review submitted successfully, it will be publised after approval.');
				$json['html']	   = '';
			}
			
			if( class_exists( 'DocDirectProcessEmail' ) ) {
				$user_from_data	= get_userdata($user_from);
				$user_to_data	  = get_userdata($user_to);
				$email_helper	  = new DocDirectProcessEmail();
				
				$emailData	= array();
				
				//User to data
				$emailData['email_to']	    = $user_to_data->user_email;
				$emailData['link_to']	= get_author_posts_url($user_to_data->ID);
				if( !empty( $user_to_data->display_name ) ) {
					$emailData['username_to']	   = $user_to_data->display_name;
				} elseif( !empty( $user_to_data->first_name ) || $user_to_data->last_name ) {
					$emailData['username_to']	   = $user_to_data->first_name.' '.$user_to_data->last_name;
				}
				
				$emailData['username_from']	   = $user_from_data->first_name.' '.$user_from_data->last_name;

				$emailData['link_from']	= get_author_posts_url($user_from_data->ID);
				
				//General

                $rating_decode = json_decode($user_rating, true);
                $sum = array_sum($rating_decode);
                $trungbinh	= $sum/5;

				// $emailData['detail_rating']	        = $final_detail;
				$emailData['rating']	        = $trungbinh;
				$emailData['reason']	        = $user_subject;
				$emailData['comment_content']	        = $user_description;
				
				kt_process_rating_email($emailData);
			}
			
			echo json_encode($json);
			die;
			
		} else{
			$json['type']		= 'error';
			$json['message']	 = pll__('Please fill all the fields.');	
			echo json_encode($json);
			die;
		}
		
	}
	add_action('wp_ajax_docdirect_make_review','kt_docdirect_make_review');
	add_action( 'wp_ajax_nopriv_docdirect_make_review', 'kt_docdirect_make_review' );
}

/**
 * @Make Review
 * @return 
 */
if ( ! function_exists( 'kt_docdirect_make_reply' ) ) {

	function kt_docdirect_make_reply() {
		global $current_user, $wp_roles,$userdata,$post;

		$user_to	= isset( $_POST['user_to'] ) && !empty( $_POST['user_to'] ) ? $_POST['user_to'] : '';
			
		if( $_POST['user_subject'] == '' || $_POST['user_description'] == '' ) {

			$json['type']		= 'error';
			$json['message']	 = pll__('Please fill all the fields.');	
			echo json_encode($json);
			die;
		}else {
			$post_parent = $_POST['post_parent'];
			if ( FALSE != get_post_status( $post_parent ) ) {

                $user_from = fw_get_db_post_option($post_parent, 'user_from', true);

				$user_subject	  = sanitize_text_field( $_POST['user_subject'] );
				$user_description  = sanitize_text_field( $_POST['user_description'] );


				$review_post = array(
					'post_title'  => $user_subject,
					'post_status' => 'publish',
					'post_content'=> $user_description,
					'post_author' => $current_user->ID,
					'post_parent' => $post_parent,
					'post_type'   => 'docdirectreviews',
					'post_date'   => current_time('Y-m-d H:i:s')
				);
				$post_id = wp_insert_post( $review_post );

				if (isset($post_id) && !empty($post_id)) {

					$user_from_data	= get_userdata($user_from);
					$email_helper	  = new DocDirectProcessEmail();
					$emailData	= array();
					
					//User to data
					$emailData['email_to']	    = $user_from_data->user_email;
					$emailData['link_to']	= get_author_posts_url($current_user->ID);
					if( !empty( $current_user->display_name ) ) {
						$emailData['username_to']	   = $current_user->display_name;
					} elseif( !empty( $current_user->first_name ) || $current_user->last_name ) {
						$emailData['username_to']	   = $current_user->first_name.' '.$current_user->last_name;
					}
					
					//User from data
					if( !empty( $user_from_data->display_name ) ) {
						$emailData['username_from']	   = $user_from_data->display_name;
					} elseif( !empty( $user_from_data->first_name ) || $user_from_data->last_name ) {
						$emailData['username_from']	   = $user_from_data->first_name.' '.$user_from_data->last_name;
					}

					$emailData['link_from']	= get_author_posts_url($user_from_data->ID);
					
					kt_process_reply_email($emailData);

					$json['type']	   = 'success';
					$json['message']	= esc_html__('Your reply published successfully.');
					$json['html']	   = 'refresh';
					echo json_encode($json);
					die;
				}
			}else {
				$json['type']		= 'error';
				$json['message']	 = pll__('ERROR.');	
				echo json_encode($json);
			}
		}
		
	}
	add_action('wp_ajax_make_reply','kt_docdirect_make_reply');
	add_action( 'wp_ajax_nopriv_make_reply', 'kt_docdirect_make_reply' );
}

function kt_docdirect_remove_reply()  {

	$data_id	= isset( $_POST['data_id'] ) && !empty( $_POST['data_id'] ) ? $_POST['data_id'] : '';

		wp_delete_post($data_id);
		$json['type']		= 'success';
		$json['message']	 = pll__('Remove Success.');	
		echo json_encode($json);
		die;

}

add_action('wp_ajax_remove_reply','kt_docdirect_remove_reply');
add_action( 'wp_ajax_nopriv_remove_reply', 'kt_docdirect_remove_reply' );

function kt_process_reply_email( $params = '' ) {
		global $current_user;
		$email_helper	= new DocDirectProcessEmail();
			
			extract( $params );

			$subject = 'New review reply!';
			$rating_content_default = 'Hey %name%!<br/>

									%current_name% has responded to your review. Please select the link below to see the response:<br/> 
									%link%
									<br/>
									<br/>

									
									Sincerely,<br/>
									MediFinder Team<br/>
									%logo%';				
			
			
			//set defalt contents
			if( empty( $rating_content ) ){
				$rating_content = $rating_content_default;
			}
			
			$rating_from	= '<a href="'.$link_from.'"  alt="'.esc_html__('Rating from').'">'.$username_from.'</a>';
			$link		   = '<a href="'.$link_to.'" alt="'.esc_html__('User link').'">'.$link_to.'</a>';
			$logo		   = kt_process_get_logo();
			
			$rating_content = str_replace("%name%", nl2br($username_from), $rating_content); //Replace name
			$rating_content = str_replace("%current_name%", nl2br($username_to), $rating_content); //Replace comment
			
			$rating_content = str_replace("%link%", nl2br($link), $rating_content); //Replace email
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
			wp_mail($email_to, $subject, $body);
			
			return true;
}
/**
 * @Delete Email Logo
 * @return {}
 */
if ( ! function_exists( 'kt_docdir_update_booking_settings' ) ) {
	function kt_docdir_update_booking_settings() {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		
		update_user_meta( $user_identity, 'confirmation_title', $_POST['confirmation_title'] );
		update_user_meta( $user_identity, 'approved_title', $_POST['approved_title'] );
		update_user_meta( $user_identity, 'cancelled_title', $_POST['cancelled_title'] );
		
		update_user_meta( $user_identity, 'booking_cancelled', $_POST['booking_cancelled'] );
		update_user_meta( $user_identity, 'booking_confirmed', $_POST['booking_confirmed'] );
		update_user_meta( $user_identity, 'booking_approved', $_POST['booking_approved'] );
		update_user_meta( $user_identity, 'schedule_message', $_POST['schedule_message'] );
		update_user_meta( $user_identity, 'currency', $_POST['currency'] );
		update_user_meta( $user_identity, 'currency_symbol', $_POST['currency_symbol'] );
		update_user_meta( $user_identity, 'thank_you', $_POST['thank_you'] );
		
		update_user_meta( $user_identity, 'paypal_enable', $_POST['paypal_enable'] );
		update_user_meta( $user_identity, 'paypal_email_id', $_POST['paypal_email_id'] );
		update_user_meta( $user_identity, 'paypal_username', $_POST['paypal_username'] );
		update_user_meta( $user_identity, 'paypal_password', $_POST['paypal_password'] );
		update_user_meta( $user_identity, 'paypal_signature', $_POST['paypal_signature'] );
		update_user_meta( $user_identity, 'stripe_enable', $_POST['stripe_enable'] );
		update_user_meta( $user_identity, 'stripe_secret', $_POST['stripe_secret'] );
		update_user_meta( $user_identity, 'stripe_publishable', $_POST['stripe_publishable'] );
		update_user_meta( $user_identity, 'stripe_site', $_POST['stripe_site'] );
		update_user_meta( $user_identity, 'stripe_decimal', $_POST['stripe_decimal'] );
		
		$json['type']		=  'success';	
		$json['message']		= esc_html__('Booking settings updated.','docdirect');	

		echo json_encode($json);
		exit;
	}
	add_action('wp_ajax_docdir_update_booking_settings', 'kt_docdir_update_booking_settings');
	add_action('wp_ajax_nopriv_docdir_update_booking_settings', 'kt_docdir_update_booking_settings');
}


/**
 * @Process Booking
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_do_process_booking' ) ) {

	remove_action('wp_ajax_docdirect_do_process_booking','docdirect_do_process_booking');
	remove_action( 'wp_ajax_nopriv_docdirect_do_process_booking', 'docdirect_do_process_booking' );

	function kt_docdirect_do_process_booking(){ 
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');
		
		$bk_category 	  = $_POST['bk_category'];
		$bk_service 	   = $_POST['bk_service'];
		$booking_date 	 = $_POST['booking_date'];
		$timestamp 	 	= strtotime($_POST['booking_date']);
		$slottime		 = $_POST['slottime'];
		$subject 		  = $_POST['subject'];
		$username 		 = $_POST['username'];
		$userphone 		= $_POST['userphone'];
		$useremail 		= $_POST['useremail'];
		$booking_note 	 = $_POST['booking_note'];
		$payment 		  = $_POST['payment'];
		$user_to		  = $_POST['data_id'];
		$patient_insurers	= $_POST['patient_insurers'];
		$usercard	= $_POST['usercard'];
		$status		   = 'pending';
		$user_from		= $current_user->ID;
		
		$bk_status	= 'pending';
		$payment_status	= 'pending';
		
		
		//Add Booking
		$appointment = array(
			'post_title'  => $subject,
			'post_status' => 'publish',
			'post_author' => $current_user->ID,
			'post_type'   => 'docappointments',
			'post_date'   => current_time('Y-m-d h')
		);
		
		//User Detail
		$currency	    	= get_user_meta( $user_to, 'currency', true);
		$stripe_secret	   = get_user_meta( $user_to, 'stripe_secret', true);
		$stripe_publishable  = get_user_meta( $user_to, 'stripe_publishable', true);
		$stripe_site	     = get_user_meta( $user_to, 'stripe_site', true);
		$paypal_enable	   = get_user_meta( $user_to, 'paypal_enable', true);
		$stripe_decimal	  = get_user_meta( $user_to, 'stripe_decimal', true);
			 
		//Price
		$services = get_user_meta($user_to , 'booking_services' , true);
		 
		if( !empty( $services[$bk_service]['price'] ) ){
		  $price	= $services[$bk_service]['price'];	
		}
			 
 
		if( isset( $payment ) && $payment === 'stripe' ){

			$post_id  = wp_insert_post($appointment);
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$appointment_no = substr($blogname,0,2).'-'.docdirect_unique_increment(5);
			
			$appointment_meta = array(
				'bk_code' 	  	  => $appointment_no,
				'bk_category' 	  => $bk_category,
				'bk_service' 	   => $bk_service,
				'bk_booking_date' 	 => $booking_date,
				'bk_slottime' 		 => $slottime,
				'bk_subject' 		  => $subject,
				'bk_username' 		 => $username,
				'bk_userphone' 		=> $userphone,
				'bk_useremail' 		=> $useremail,
				'bk_booking_note' 	 => $booking_note,
				'bk_payment' 		  => $payment,
				'bk_user_to' 		  => $user_to,
				'bk_timestamp' 		=> $timestamp,
				'bk_status' 		   => $bk_status,
				'payment_status' 	  => $payment_status,
				'bk_user_from' 		=> $user_from,
				'bk_paid_amount' 	  => $price,
				'bk_currency' 	     => $currency,
				'patient_insurers' 	     => $patient_insurers,
				'usercard' 	     => $usercard,
				'bk_transaction_status'  => 'pending',
				'bk_payment_date' 	    => date('Y-m-d H:i:s'),
			);
			
			$new_values = $appointment_meta;
			if ( isset( $post_id ) && !empty( $post_id ) ) {
				fw_set_db_post_option($post_id, null, $new_values);
			}
			
			//Update post meta
			foreach( $appointment_meta as $key => $value ){
				update_post_meta($post_id,$key,$value);
			}

			 if( class_exists( 'DocDirectProcessEmail' ) ) {
				//Send Email
				// $email_helper	  = new DocDirectProcessEmail();
				$emailData	= array();
				$emailData['post_id']	= $post_id;
				// $email_helper->process_appointment_confirmation_email($emailData);
				// $email_helper->process_appointment_confirmation_admin_email($emailData);
				kt_process_appointment_confirmation_email($emailData);
				kt_process_appointment_confirmation_admin_email($emailData);
			 }
			 
			 //Process Payment

			 $total_amount	= $price;
			 if( isset( $stripe_decimal ) && $stripe_decimal == 0 ){
				$service_amount	= $price;
			 } else{
				$service_amount	= $price.'00';	 
			 }
			 
			 echo json_encode( 
				array( 
					   'username' 	 	=> $user_name,
					   'email' 		   => $useremail,
					   'order_no' 	    => $post_id,
					   'user_to'   		 => $user_to,
					   'user_from' 	   => $user_from,
					   'subject'    	 => $subject,
					   'process'		 => true, 
					   'name'			=> $stripe_site, 
					   'amount' 		  => $service_amount,
					   'total_amount' 	=> $total_amount,
					   'key'			 => $stripe_publishable,
					   'currency'		=> $currency,
					   'data'			=> '',
					   'type'			=> 'success',
					   'payment_type'	=> 'stripe',
					  )
				);
				
			 die;
			 
		} else if( isset( $payment ) && $payment === 'paypal' ){

			$paypal_username    = get_user_meta( $user_to, 'paypal_username', true);
			$paypal_password    = get_user_meta( $user_to, 'paypal_password', true);
			$paypal_signature	= get_user_meta( $user_to, 'paypal_signature', true);

			if ( $paypal_username == '' || $paypal_password == '' || $paypal_signature == '' ) {
                $json['type'] = 'error';
                $json['message'] = esc_html__('This doctor not yet setup payment.', 'docdirect');
                echo json_encode($json);
                die;
			}

			$post_id  = wp_insert_post($appointment);
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$appointment_no = substr($blogname,0,2).'-'.docdirect_unique_increment(5);
			
			$appointment_meta = array(
				'bk_code' 	  	  => $appointment_no,
				'bk_category' 	  => $bk_category,
				'bk_service' 	   => $bk_service,
				'bk_booking_date' 	 => $booking_date,
				'bk_slottime' 		 => $slottime,
				'bk_subject' 		  => $subject,
				'bk_username' 		 => $username,
				'bk_userphone' 		=> $userphone,
				'bk_useremail' 		=> $useremail,
				'bk_booking_note' 	 => $booking_note,
				'bk_payment' 		  => $payment,
				'bk_user_to' 		  => $user_to,
				'bk_timestamp' 		=> $timestamp,
				'bk_status' 		   => $bk_status,
				'payment_status' 	  => $payment_status,
				'bk_user_from' 		=> $user_from,
				'bk_paid_amount' 	  => $price,
				'bk_currency' 	     => $currency,
				'patient_insurers' 	     => $patient_insurers,
				'usercard' 	     => $usercard,
				'bk_transaction_status'  => 'pending',
				'bk_payment_date' 	    => date('Y-m-d H:i:s'),
			);
			
			$new_values = $appointment_meta;
			if ( isset( $post_id ) && !empty( $post_id ) ) {
				fw_set_db_post_option($post_id, null, $new_values);
			}
			
			//Update post meta
			foreach( $appointment_meta as $key => $value ){
				update_post_meta($post_id,$key,$value);
			}

			if( class_exists( 'DocDirectProcessEmail' ) ) {
				//Send Email
				$email_helper	  = new DocDirectProcessEmail();
				$emailData	= array();
				$emailData['post_id']	= $post_id;
				// $email_helper->process_appointment_confirmation_email($emailData);
				// $email_helper->process_appointment_confirmation_admin_email($emailData);
				kt_process_appointment_confirmation_email($emailData);
				kt_process_appointment_confirmation_admin_email($emailData);
			}

			
			
			/*---------------------------------------------
			 * @Paypal Payment Gateway Process
			 * @Return HTML
			 ---------------------------------------------*/
			$sandbox_enable = fw_get_db_settings_option('user_enable_sandbox');
			$business_email	  = get_user_meta( $user_to, 'paypal_email_id', true);
			$currency	    	= get_user_meta( $user_to, 'currency', true);
			$listner_url	= '';
			
			if( class_exists( 'DocDirectGlobalSettings' ) ) {
				$plugin_url	= DocDirectGlobalSettings::get_plugin_url();
				$listner_url	   = $plugin_url. '/payments/booking.php';
			}			
			$return_url	= get_author_posts_url($user_to);
			
			$custom = array();
			$custom['post_id'] = $post_id;

			// Create a new PayPal class instance, and set the sandbox mode to true
			$paypal = new wp_paypal_gateway (true);
			$paypal->setVarApi($paypal_username, $paypal_password, $paypal_signature);

			$time_format = get_option('time_format');
			$time = explode('-',$slottime);
			$realtime = date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) ).' - '.date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) );
			$desc = 'Booking APPOINTMENT from Medi-finder.com. Price: '.$services[$bk_service]['price'].'$HKD. Time: '.$realtime;

			$requestParams = array(
			    'RETURNURL' => $return_url, //Enter your webiste URL here
			    'CANCELURL' => $return_url//Enter your website URL here
			); 
			$orderParams = array(
			    'LOGOIMG' => "http://demo.tntechs.com.vn/thienvk/medifinder/wp-content/uploads/2016/10/logo-11.png", //You can paste here your logo image URL
			    // "MAXAMT" => "100", //Set max transaction amount
			    "NOSHIPPING" => "1", //I do not want shipping
			    "ALLOWNOTE" => "0", //I do not want to allow notes
			    "BRANDNAME" => $blogname,
			    "GIFTRECEIPTENABLE" => "0",
			    "GIFTMESSAGEENABLE" => "0"
			);
			$item = array(
			    'LOCALECODE' => 'en_UK',
			 
			    'PAYMENTREQUEST_0_AMT' => $services[$bk_service]['price'],
			    'PAYMENTREQUEST_0_CURRENCYCODE' => 'HKD',
			    'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			    'PAYMENTREQUEST_0_ITEMAMT' => $services[$bk_service]['price'],
    			'PAYMENTREQUEST_0_CUSTOM' => json_encode($custom),
			 
			    'L_PAYMENTREQUEST_0_NAME0' => $services[$bk_service]['title'],
			    'L_PAYMENTREQUEST_0_DESC0' => $services[$bk_service]['title'],
			    'L_PAYMENTREQUEST_0_QTY0' => 1,
			    'L_PAYMENTREQUEST_0_AMT0' => $services[$bk_service]['price'],

			    'L_BILLINGTYPE0' => 'MerchantInitiatedBillingSingleAgreement',
			    'L_BILLINGAGREEMENTDESCRIPTION0' => $desc,
			        //"PAYMENTREQUEST_0_INVNUM" => $transaction->id - This field is useful if you want to send your internal transaction ID
			);
			 
			// Display the response if successful or the debug info
			$paypal->setExpressCheckout($requestParams + $orderParams + $item);
			// $response = $paypal->getResponse();
			    // print_r($response);
			$url = $paypal->getRedirectURL();

			$output  = '';
			if ($url == false) {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Some error occur please contact to administrator.', 'docdirect');
                echo json_encode($json);
                die;
			}else {				
				$output .= '<script>
								window.location = "'.$url.'";
						  </script>';
	            $json['form_data']  = $output;
                $json['type'] = 'success';
				$json['payment_type']  = 'paypal';
                echo json_encode($json);
                die;
			}
			
			if(is_array($response) && $response["ACK"]=="Success"){
				$url = $paypal->getRedirectURL();
			    $json['url']  = $url;
				$output .= '<script>
								window.location = "'.$url.'";
						  </script>';

	            $json['form_data']  = $output;
	            $json['type'] 		= 'success';
				$json['payment_type']  = 'paypal';
			} else {
			    /*print_r($paypal->debug_info);*/
                $json['type'] = 'error';
                $json['message'] = esc_html__('Some error occur please contact to administrator.', 'docdirect');
                echo json_encode($json);
                die;
			}

		} else {

			$post_id  = wp_insert_post($appointment);
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$appointment_no = substr($blogname,0,2).'-'.docdirect_unique_increment(5);
			
			$appointment_meta = array(
				'bk_code' 	  	  => $appointment_no,
				'bk_category' 	  => $bk_category,
				'bk_service' 	   => $bk_service,
				'bk_booking_date' 	 => $booking_date,
				'bk_slottime' 		 => $slottime,
				'bk_subject' 		  => $subject,
				'bk_username' 		 => $username,
				'bk_userphone' 		=> $userphone,
				'bk_useremail' 		=> $useremail,
				'bk_booking_note' 	 => $booking_note,
				'bk_payment' 		  => $payment,
				'bk_user_to' 		  => $user_to,
				'bk_timestamp' 		=> $timestamp,
				'bk_status' 		   => $bk_status,
				'payment_status' 	  => $payment_status,
				'bk_user_from' 		=> $user_from,
				'bk_paid_amount' 	  => $price,
				'bk_currency' 	     => $currency,
				'patient_insurers' 	     => $patient_insurers,
				'usercard' 	     => $usercard,
				'bk_transaction_status'  => 'pending',
				'bk_payment_date' 	    => date('Y-m-d H:i:s'),
			);
			
			$new_values = $appointment_meta;
			if ( isset( $post_id ) && !empty( $post_id ) ) {
				fw_set_db_post_option($post_id, null, $new_values);
			}
			
			//Update post meta
			foreach( $appointment_meta as $key => $value ){
				update_post_meta($post_id,$key,$value);
			}

			if( class_exists( 'DocDirectProcessEmail' ) ) {
				//Send Email
				$email_helper	  = new DocDirectProcessEmail();
				$emailData	= array();
				$emailData['post_id']	= $post_id;
				// $email_helper->process_appointment_confirmation_email($emailData);
				// $email_helper->process_appointment_confirmation_admin_email($emailData);
				kt_process_appointment_confirmation_email($emailData);
				kt_process_appointment_confirmation_admin_email($emailData);
			}
			
		    $json['data']	= kt_docdirect_get_booking_step_five($user_to);
			$json['message']		= esc_html__('Your boooking has submitted.','docdirect');	
			$json['type']  =  'success';
			$json['payment_type']  =  'local';
			echo json_encode($json);
			die;
		}
	}
	add_action('wp_ajax_docdirect_do_process_booking','kt_docdirect_do_process_booking');
	add_action( 'wp_ajax_nopriv_docdirect_do_process_booking', 'kt_docdirect_do_process_booking' );
}


/**
 * @Approve/Cancel Booking
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_change_appointment_status' ) ) {

	remove_action('wp_ajax_docdirect_change_appointment_status','docdirect_change_appointment_status');
	remove_action( 'wp_ajax_nopriv_docdirect_change_appointment_status', 'docdirect_change_appointment_status' );

	function kt_docdirect_change_appointment_status(){ 
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;;
		
		if( empty( $_POST['type'] ) 
			||
			empty( $_POST['id'] )
		){
			$json['type']	= 'error';
			$json['message']	= esc_html__('Some error occur, please try again later.','docdirect');
			echo json_encode($json);
			die;
		}
		
		$type	= $_POST['type'];
		$post_id	  = $_POST['id'];
		
		if( $type === 'approve' ){

			$value	= 'approved';

			if (isset($_POST['location']) && $_POST['location'] != '') {
				$location = $_POST['location'];
				update_post_meta($post_id,'bk_location',$location);
			}
			
			update_post_meta($post_id,'bk_status',$value);
			
			//Send Email
			$email_helper	  = new DocDirectProcessEmail();
			$emailData	= array();
			$emailData['post_id']	= $post_id;
			// $email_helper->process_appointment_approved_email($emailData);
			kt_process_appointment_approved_email($emailData);
			// kt_process_appointment_review_email($emailData);
			
			//Send status
			$json['action_type']	= $value;
			$json['type']		   = 'success';
			$json['message']		= esc_html__('Appointment status has updated.','docdirect');
			echo json_encode($json);
			die;
		
		} else if( $type === 'cancel' ){

			$bk_payment       = get_post_meta($post_id, 'bk_payment',true);
			
			if ( $bk_payment == 'paypal' ) {

		        $paypal = new wp_paypal_gateway (true);

		        $paypal_username    = get_user_meta( $current_user->ID, 'paypal_username', true);
		        $paypal_password    = get_user_meta( $current_user->ID, 'paypal_password', true);
		        $paypal_signature   = get_user_meta( $current_user->ID, 'paypal_signature', true);
	        	$paypal->setVarApi($paypal_username, $paypal_password, $paypal_signature);

	            $TRANSACTIONID = get_post_meta($post_id, 'bk_code', true);
	        	$requestParams = array('TRANSACTIONID' => $TRANSACTIONID);
		        $paypal->RefundTransaction($requestParams);
		        $response = $paypal->getResponse();
	        	if(is_array($response) && $response["ACK"]=="Success"){

					$value	= 'cancelled';

					//Send Email
					$email_helper	  = new DocDirectProcessEmail();
					$emailData	= array();
					$emailData['post_id']	= $post_id;
					$email_helper->process_appointment_cancelled_email($emailData);
					
					wp_delete_post( $post_id );
					
					//Return status
					$json['action_type']	= $value;
					$json['type']		   = 'error';
					$json['message']		= esc_html__('Appointment has been cancelled.','docdirect');
					echo json_encode($json);
					die;
				}else {
					//Return status
					$json['action_type']	= 'error';
					$json['type']		   = '';
					$json['message']		= esc_html__('Some error occur, please try again later.','docdirect');
					echo json_encode($json);
					die;
				}

			}else {

				wp_delete_post( $post_id );

				//Return status
				$json['action_type']	= 'cancelled';
				$json['type']		   = 'success';
				$json['message']		= esc_html__('Appointment has been cancelled.','docdirect');
				echo json_encode($json);
				die;

			}
		}

		
	}
	add_action('wp_ajax_docdirect_change_appointment_status','kt_docdirect_change_appointment_status');
	add_action( 'wp_ajax_nopriv_docdirect_change_appointment_status', 'kt_docdirect_change_appointment_status' );
}



/**
 * @Booking Step 1
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_get_booking_step_one' ) ) {
	function kt_docdirect_get_booking_step_one($user_identity='',$return_type='echo'){
		global $current_user, $wp_roles,$userdata,$post;
		$booking_all_services	= array();
		$services_cats = get_user_meta($user_identity , 'services_cats' , true);
		$booking_services = get_user_meta($user_identity , 'booking_services' , true);
		$currency_symbol	       = get_user_meta( $user_identity, 'currency_symbol', true);
		$currency_symbol	= !empty( $currency_symbol ) ? $currency_symbol : '';

		ob_start();
		?>
        <div class="bk-step-1">
          <div class="form-group">
            <div class="tg-select">
              <select name="bk_service" class="bk_service">
                <option value=""><?php esc_html_e('Select Service*','docdirect');?></option>
                <?php 
				if( !empty( $booking_services ) ) {
					
					foreach( $booking_services as $key => $value ){
						$booking_all_services[$value['category']][$key]	= $value;
				 ?>
                   <option value="<?php echo esc_attr( $key );?>"><?php echo esc_attr( $value['title'] );?>&nbsp;--&nbsp;<?php echo esc_attr( $currency_symbol );?><?php echo esc_attr( $value['price'] );?></option>
                 <?php
						}
					}
				?>
              </select>
              <script>
				jQuery(document).ready(function() {
					var Z_Editor = {};
					Z_Editor.services = {};
					Z_Editor.all_services = {};
					window.Z_Editor = Z_Editor;
					Z_Editor.services = jQuery.parseJSON( '<?php echo addslashes(json_encode($booking_all_services));?>' );
					Z_Editor.all_services = jQuery.parseJSON( '<?php echo addslashes(json_encode($booking_services));?>' );
				});
			</script> 
            <script type="text/template" id="tmpl-load-services">
				<option value=""><?php esc_html_e('Select Service*','docdirect');?></option>
				<#
					var _option	= '';
					if( !_.isEmpty(data) ) {
						_.each( data , function(element, index, attr) { #>
							 <option value="{{index}}">{{element.title}}&nbsp;--&nbsp;<?php echo esc_attr( $currency_symbol );?>{{element.price}}</option>
						<#	
						});
					}
				#>
			</script> 
            </div>
          </div>
        </div>
        <?php
		if( isset( $return_type ) && $return_type == 'return' ){
			return ob_get_clean();
		} else {
			echo ob_get_clean();
		}
	}
}

/**
 * @Booking Step 2
 * Schedules
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_get_booking_step_two' ) ) {

	remove_action('wp_ajax_docdirect_get_booking_step_two','docdirect_get_booking_step_two');
	remove_action( 'wp_ajax_nopriv_docdirect_get_booking_step_two', 'docdirect_get_booking_step_two' );

	function kt_docdirect_get_booking_step_two(){
		global $current_user, $wp_roles,$userdata,$post;
		
		$user_id	= $_POST['data_id'];
		
		//echo $_POST['slot_date']; die;
		if( !empty( $_POST['slot_date'] ) ){
			$day		= strtolower(date('D',strtotime( $_POST['slot_date'] )));
			$current_date_string	= date('M d, l',strtotime($_POST['slot_date']));
			$current_date	= $_POST['slot_date'];
			$slot_date	   = $_POST['slot_date'];
		} else{
	    	if (isset($_POST["booking_date"]) && isset($_POST["booking_time"])) {
				$booking_date	= $_POST['booking_date'];
				$booking_time	= $_POST['booking_time'];
				
				$day		= strtolower(date('D',strtotime( $_POST['booking_date'] )));
				$current_date_string	= date('M d, l',strtotime($_POST['booking_date']));
				$current_date	= $_POST['booking_date'];
				$slot_date	   = $_POST['booking_date'];
	    	}else {
				$day		= strtolower(date('D'));
				$current_date_string	= date('M d, l');
				$current_date	= date('Y-m-d');
				$slot_date	   = date('Y-m-d');
	    	}
		}

		$week_days	= docdirect_get_week_array();
		
		$default_slots	= array();
		$default_slots = get_user_meta($user_id , 'default_slots' , false);
		$time_format   = get_option('time_format');
		
		//Custom Slots
		$custom_slot_list	= kt_docdirect_custom_timeslots_filter($default_slots,$user_id);

		//Get booked Appointments
		$year  = date_i18n('Y',strtotime($slot_date));
		$month = date_i18n('m',strtotime($slot_date));
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

		$today = current_time( 'timestamp' );
		//Data
		ob_start();
        if( !empty( $todays_defaults ) ) {
        foreach( $todays_defaults as $key => $value ){
            $time = explode('-',$key);
            //echo $time[0];
            $b_date = $current_date. ' ' .$time[0];
            // echo date('d M Y G:i', strtotime($b_date));
            
            if( ( !empty( $appointments_array[$key]['bk_slottime'] ) && $appointments_array[$key]['bk_slottime'] == $key )
            	|| strtotime($b_date) < $today
            ){
                $slotClass	= 'tg-booked';
                $slot_status	= 'disabled';
            } else{
                $slotClass	= 'tg-available';
                $slot_status	= '';
	            $checked = '';
	            if ($key == $booking_time) {
	            	$checked = 'checked';
	            }
            }
        ?>
        <div class="tg-doctimeslot <?php echo sanitize_html_class( $slotClass );?>">
            <div class="tg-box">
                <div class="tg-radio">
                    <input <?php echo $checked;?> <?php echo esc_attr( $slot_status );?> id="<?php echo esc_attr( $key );?>" value="<?php echo esc_attr( $key );?>" type="radio" name="slottime">
                    <label for="<?php echo esc_attr( $key );?>"><?php echo date($time_format,strtotime('2016-01-01 '.$time[0]) );?>&nbsp;-&nbsp;<?php echo date($time_format,strtotime('2016-01-01 '.$time[1]) );?></label>
                </div>
            </div>
        </div>
        <?php
        } }else {
        	kt_next_avai_button($user_id, $slot_date);
        }
      
		$json['data']	 = ob_get_clean();
		$json['type']	 = 'success';
		$json['message']  = esc_html__('slots returned','docdirect');
		echo json_encode($json);
		die;
	}
	add_action('wp_ajax_docdirect_get_booking_step_two','kt_docdirect_get_booking_step_two');
	add_action( 'wp_ajax_nopriv_docdirect_get_booking_step_two', 'kt_docdirect_get_booking_step_two' );
}


/**
 * @Booking Step 3
 * Customer detail
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_get_booking_step_three' ) ) {

	remove_action('wp_ajax_docdirect_get_booking_step_three','docdirect_get_booking_step_three');
	remove_action( 'wp_ajax_nopriv_docdirect_get_booking_step_three', 'docdirect_get_booking_step_three' );

	function kt_docdirect_get_booking_step_three(){
		global $current_user, $wp_roles,$userdata,$post;
		ob_start();
		$username	= '';
		$userphone	= '';
		$useremail	= '';
		
		if( !empty( $current_user->ID ) ){
			$user_id	= $_POST['data_id'];
			$user_date	= get_userdata(intval($current_user->ID));
			$user_login   = $user_date->user_login;
			$nickname     = $user_date->nickname;
			$first_name   = $user_date->first_name; 
			$last_name    = $user_date->last_name;
			$userphone    = $user_date->phone_number;
			$card_number    = $user_date->card_number;
			$patient_insurers    = $user_date->patient_insurers;

			$insurer   = get_term_by('id', $patient_insurers, 'insurer');
			
			if( !empty( $user_date->user_email ) ){
				$useremail    = $user_date->user_email;
			}
			
			if( !empty( $first_name ) || !empty( $last_name ) ){
				$username	= $first_name.' '.$last_name;
			} else if( !empty( $nickname ) ){
				$username	= $nickname;
			} else{
				$username	= $user_login;
			}

		}
		?>
        <div class="bk-customer-form">
            <div class="form-group">
              <input type="text" class="form-control" name="subject" placeholder="<?php esc_attr_e('Subject*','docdirect');?>">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="username" value="<?php echo esc_attr($username);?>" placeholder="<?php esc_attr_e('Your Name*','docdirect');?>">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" id="teluserphone" value="<?php echo esc_attr($userphone);?>" name="userphone" placeholder="<?php esc_attr_e('Phone*','docdirect');?>">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="useremail" value="<?php echo esc_attr($useremail);?>" placeholder="<?php esc_attr_e('Email*','docdirect');?>">
            </div>
            <div class="form-group">
              <input type="text" class="form-control" id="card_number" value="<?php echo esc_attr($card_number);?>" name="usercard" placeholder="<?php esc_attr_e('ID Card Number','docdirect');?>">
            </div>
            <div class="form-group insurers">
                <?php
                    $current_insurer_text = $insurer->name;
                    if ( $insurer->name == '' ) {
                        $current_insurer_text = pll__('Select Insurance');
                    }
                ?>
                <a class="dropdown-button-group" href="javascript:;"><?php echo $current_insurer_text;?></a>
                <input class="select_category" type="hidden" name="patient_insurers" value="<?php echo $insurer->name;?>" />
                <div class="dropdown-input-group">
                    <div class="dropdown-wrap">
                        <li data-slug=""><?php pll_e('Select Insurance');?></li>                       
                            <?php 
                          	if (function_exists('kt_read_insurer')) {
                              $insurers_list = kt_read_insurer();
                          	}
                            if( isset( $insurers_list ) && !empty( $insurers_list ) ){
                              foreach( $insurers_list as $key => $insurer ){
                            ?>
                            <?php
                                $sample_bg_url = get_stylesheet_directory_uri().'/images/sample-insurer.png';
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
            <div class="form-group tg-textarea">
              <textarea class="form-control" name="booking_note" placeholder="<?php esc_attr_e('Note*','docdirect');?>"></textarea>
            </div>
        </div>
		<?php
		$json['data']	 = ob_get_clean();
		$json['type']	 = 'success';
		$json['message']  = pll__('form returned','docdirect');
		echo json_encode($json);
		die;
	}
	
	add_action('wp_ajax_docdirect_get_booking_step_three','kt_docdirect_get_booking_step_three');
	add_action( 'wp_ajax_nopriv_docdirect_get_booking_step_three', 'kt_docdirect_get_booking_step_three' );
}

/**
 * @Booking Step 4
 * Payment Mode
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_get_booking_step_four' ) ) {

	remove_action('wp_ajax_docdirect_get_booking_step_three','docdirect_get_booking_step_four');
	remove_action( 'wp_ajax_nopriv_docdirect_get_booking_step_three', 'docdirect_get_booking_step_four' );

	function kt_docdirect_get_booking_step_four(){
		global $current_user, $wp_roles,$userdata,$post;
		ob_start();

		if( !empty( $_POST['data_id'] ) ){
			$paypal_enable	= get_user_meta( esc_attr( $_POST['data_id'] ), 'paypal_enable', true);
			// $stripe_enable	= get_user_meta( esc_attr( $_POST['data_id'] ) , 'stripe_enable', true);
		}
		
		$user_disable_stripe	= '';
		$user_disable_paypal	= '';
		
		if(function_exists('fw_get_db_settings_option')) {
			// $user_disable_stripe = fw_get_db_settings_option('user_disable_stripe', $default_value = null);
			$user_disable_paypal = fw_get_db_settings_option('user_disable_paypal', $default_value = null);
		}

		?>
        <div class="bk-payment-methods">
            <div class="form-group tg-pay-radiobox">
              <label>
                <input type="radio" value="local" name="payment" checked>
                <span><?php esc_html_e('I will pay locally.','docdirect');?></span> 
              </label>
            </div>
            <?php 
			if( ( !empty( $paypal_enable ) && $paypal_enable === 'on' )
				 && ( !empty( $user_disable_paypal ) && $user_disable_paypal === 'on' ) 
			){?>
            <div class="form-group tg-pay-radiobox tg-paypal">
              <label>
                <input type="radio" value="paypal" name="payment">
                <span><?php esc_html_e('I will pay now through Paypal.','docdirect');?></span></label>
            </div>
            <?php }?>
            <?php 
			if( !empty( $stripe_enable ) && $stripe_enable === 'on' 
				&&  ( !empty( $user_disable_stripe ) && $user_disable_stripe === 'on' ) 
			){?>
            <div class="form-group tg-pay-radiobox tg-creditcard">
              <label>
                <input type="radio" value="stripe" name="payment">
                <span><?php esc_html_e('I will pay now through Credit Card.','docdirect');?></span></label>
            </div>
            <?php }?>
        </div>
		<?php
		$json['data']	 = ob_get_clean();
		$json['type']	 = 'success';
		$json['message']  = pll__('form returned','docdirect');
		echo json_encode($json);
		die;
	}
	add_action('wp_ajax_docdirect_get_booking_step_four','kt_docdirect_get_booking_step_four');
	add_action( 'wp_ajax_nopriv_docdirect_get_booking_step_four', 'kt_docdirect_get_booking_step_four' );
}

/**
 * @Wp Registration
 * @return 
 */
if ( !function_exists('kt_docdirect_user_registration') ) {

	remove_action('wp_ajax_docdirect_user_registration', 'docdirect_user_registration');
	remove_action('wp_ajax_nopriv_docdirect_user_registration', 'docdirect_user_registration');

	function kt_docdirect_user_registration($atts =''){
		global $wpdb;
			$captcha_settings = '';
			$verify_user	= 'off';
			$verify_switch	= '';
			
			if(function_exists('fw_get_db_settings_option')) {
				$verify_switch = fw_get_db_settings_option('verify_user', $default_value = null);
			}
			
			//Demo Ready
			if( isset( $_SERVER["SERVER_NAME"] ) 
				&& $_SERVER["SERVER_NAME"] === 'themographics.com' ){
				$json['type']	   =  "error";
				$json['message']	=  "Registration is disabled by administrator";
				echo json_encode( $json );
				exit();
			}
			
			/*if( !empty( $verify_user ) && $verify_user === 'verified' ){
				$verify_user	= 'on';
			} else{
				$verify_user	= 'off';
			}*/

			if(function_exists('fw_get_db_settings_option')) {
				$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
			}
				
			//recaptcha check
			if( isset( $captcha_settings ) 
				&& $captcha_settings === 'enable' 
			) {
				if ( isset($_POST['g-recaptcha-response']) && !empty( $_POST['g-recaptcha-response'] ) ) {
					  $docReResult = docdirect_get_recaptcha_response($_POST['g-recaptcha-response']);
					  
					  if ( $docReResult == 1 ) {
						  $workdone = 1;
					  } else if ( $docReResult == 2 ) {
						  echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=> esc_html__('Some error occur, please try again later.','docdirect_core' )));
						   die;
					  }else{
						echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=> esc_html__('Wrong reCaptcha. Please verify first.','docdirect_core' )));
						die;
					  }
				
				} else{
					echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=> esc_html__( 'Please enter reCaptcha!','docdirect_core' )));
					die;
				}
			}
		
			$username = esc_sql($_POST['username']); 
			$terms = $_POST['terms']; 
			$password = esc_sql($_POST['password']);   
			$confirm_password = esc_sql($_POST['confirm_password']);   
			
			$json	= array();
			
			//Demo Ready			
			if( empty( $_POST['user_type'] ) ) {
				$json['type']		=  "error";
				$json['message']	=  "Please select user type.";
				echo json_encode( $json );
				exit();
			}
			
			//User Role
			if( isset( $_POST['user_type'] ) && $_POST['user_type'] === 'professional' ) {
				$db_user_role	= 'professional';
			} else{
				$db_user_role	= 'visitor';
			}

			if( isset( $_POST['user_type'] ) && $_POST['user_type'] === 'professional' ) {
				if( empty( $_POST['directory_type'] ) ) {
					$json['type']		=  "error";
					$json['message']	=  "Please select Directory Type.";
					echo json_encode( $json );
					exit();
				}
			}
			
			if(empty($username)) { 
				$json['type']		=  "error";
				$json['message']	=  "User name should not be empty.";
				echo json_encode( $json );
				exit();
			}
			
			$email = esc_sql($_POST['email']); 
			if(empty($email)) { 
				$json['type']		=  "error";
				$json['message']	=  esc_html__("Email should not be empty.", 'docdirect_core');
				echo json_encode( $json );
				exit();
			}

			/*$title_name = esc_sql($_POST['title_name']); 
			if(empty($title_name)) { 
				$json['type']		=  "error";
				$json['message']	=  esc_html__("Title should not be empty.", 'docdirect_core');
				echo json_encode( $json );
				exit();
			}*/
	
			if( !preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email) ) { 
				
				$json['type']		=  "error";
				$json['message']	=  esc_html__("Please enter a valid email.", 'docdirect_core');
				echo json_encode( $json );
				die;
			}

			$phone_number = esc_sql($_POST['phone_number']); 
			if(empty($phone_number)) { 
				$json['type']		=  "error";
				$json['message']	=  esc_html__("Phone number should not be empty.", 'docdirect_core');
				echo json_encode( $json );
				exit();
			}
			if ( preg_match('/\s/',$phone_number) ) {
				$json['type']		=  "error";
				$json['message']	=  esc_html__("Phone number not allow whitespace.", 'docdirect_core');
				echo json_encode( $json );
				exit();
			}
			$arr_phone = kt_get_all_user_phone();
			if(in_array($phone_number, $arr_phone)) {
				$json['type']		=  "error";
				$json['message']	=  esc_html__("Phone number exists.", 'docdirect_core');
				echo json_encode( $json );
				exit();
			}

			if(empty($password)) { 
				$json['type']		=  "error";
				$json['message']	 =  "Password is required.";
				echo json_encode( $json );
				exit();
			}
			
			if( $password != $confirm_password) { 
				$json['type']		=  "error";
				$json['message']	=  esc_html__("Password is not matched.",'docdirect_core');
				echo json_encode( $json );
				exit();
			}
			
			if( $terms  == '0') { 
				$json['type']		=  "error";
				$json['message']	=  "Please Check Terms and Conditions";
				echo json_encode( $json );
				exit();
			}
			
			$random_password = $password;
			 
			$user_identity = wp_create_user( $username,$random_password, $email );
				if ( is_wp_error($user_identity) ) { 
					$json['type']		=  "error";
					$json['message']	=  esc_html__("User already exists. Please try another one.", 'docdirect_core');
					echo json_encode( $json );
					die;
				} else {
					global $wpdb;
					wp_update_user(array('ID'=>esc_sql($user_identity),'role'=>esc_sql($db_user_role),'user_status' => 1));
					
					$wpdb->update(
					  $wpdb->prefix.'users',
					  array( 'user_status' => 1),
					  array( 'ID' => esc_sql($user_identity) )
					);
					
					update_user_meta( $user_identity, 'show_admin_bar_front', 'false' );
					update_user_meta( $user_identity, 'user_type', esc_sql($_POST['user_type'] ) );
					update_user_meta( $user_identity, 'title_name', esc_sql($_POST['title_name'] ) );
					update_user_meta( $user_identity, 'gende', esc_sql($_POST['gender'] ) );
					update_user_meta( $user_identity, 'first_name', esc_sql($_POST['first_name'] ) );
					update_user_meta( $user_identity, 'last_name', esc_sql($_POST['last_name'] ) );
					update_user_meta( $user_identity, 'phone_number', esc_sql($_POST['phone_number'] ) );
					update_user_meta( $user_identity, 'directory_type', esc_sql($_POST['directory_type'] ) );
					update_user_meta( $user_identity, 'profile_status', 'active' );
					update_user_meta( $user_identity, 'verify_user', $verify_user );
					
					
					//Update Profile Hits
					$year			= date('y');
					$month		   = date('m');
					$profile_hits	= array();
					$months_array	= docdirect_get_month_array(); //Get Month  Array
					
					foreach( $months_array as $key => $value ){
						$profile_hits[$year][$key]	= 0;
					}
					
					update_user_meta( $user_identity, 'profile_hits', $profile_hits );
					
					
					if( class_exists( 'DocDirectProcessEmail' ) ) {
						$email_helper	= new DocDirectProcessEmail();
						
						$emailData	= array();
						$emailData['user_identity']	=  $user_identity;
						$emailData['first_name']	   =  $_POST['first_name'];
						$emailData['last_name']		=  $_POST['last_name'];
						$emailData['password']	=  $random_password;
						$emailData['username']	=  $username;
						$emailData['email']	   =  $email;
						// $email_helper->process_registeration_email($emailData);
						
						if( !empty( $verify_switch ) && $verify_switch === 'verified' ){
							$key_hash = md5(uniqid(openssl_random_pseudo_bytes(32)));
							update_user_meta( $user_identity, 'confirmation_key', $key_hash);

							$protocol = is_ssl() ? 'https' : 'http';

							$verify_link = esc_url(add_query_arg(array(
								'key' => $key_hash.'&verifyemail='.$email
											), home_url('/', $protocol)));
							
							$emailData['verify_link'] 	 = $verify_link;
							// $email_helper->process_email_verification($emailData);
							kt_process_email_verification($emailData);
						}
					} else{
						docdirect_wp_new_user_notification(esc_sql($user_identity), $random_password);
					}
			
					//prepare return url
					$dir_profile_page = '';
					if (function_exists('fw_get_db_settings_option')) {
		                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
		            }
					
					$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
					$return_url = get_the_permalink($profile_page);
					
					// $return_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'settings', $user_identity,true);
					// Set the global user object
					$current_user = get_user_by( 'id', $user_identity );

					// set the WP login cookie
					// $secure_cookie = is_ssl() ? true : false;
					// wp_set_auth_cookie( $user_identity, true, $secure_cookie );
					
					$json['profile_url']		=  $return_url;
					$json['type']		=  "success";
					$json['message']	=  esc_html__("Your have successfully signup, please signin or check your email.", "docdirect");
					echo json_encode( $json );
					die;
				}
		die();
	}
	
	add_action('wp_ajax_docdirect_user_registration', 'kt_docdirect_user_registration');
	add_action('wp_ajax_nopriv_docdirect_user_registration', 'kt_docdirect_user_registration');
}

/**
 * @Wp Login
 * @return 
 */
if ( ! function_exists( 'kt_docdirect_ajax_login' ) ) {

	remove_action('wp_ajax_docdirect_ajax_login', 'docdirect_ajax_login');
	remove_action('wp_ajax_nopriv_docdirect_ajax_login', 'docdirect_ajax_login');

	function kt_docdirect_ajax_login(){
		$captcha_settings		= '';
		$user_array = array();
		$user_array['user_login'] 		= esc_sql($_POST['username']);
		$user_array['user_password'] 	= esc_sql($_POST['password']);
		$user_array['login_redirect'] 	= esc_sql($_POST['login_redirect']);

		$login_redirect = home_url( '/' );
		if (!empty($user_array['login_redirect'])) {
			$login_redirect = $user_array['login_redirect'];
		}

		if(function_exists('fw_get_db_settings_option')) {
			$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
		}

		//recaptcha check
		if( isset( $captcha_settings ) 
			&& $captcha_settings === 'enable' 
		) {
			if ( isset($_POST['g-recaptcha-response']) && !empty( $_POST['g-recaptcha-response'] ) ) {
				  $docReResult = docdirect_get_recaptcha_response($_POST['g-recaptcha-response']);
				  
				  if ( $docReResult == 1 ) {
					  $workdone = 1;
				  } else if ( $docReResult == 2 ) {
					  echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=> esc_html__('Some error occur, please try again later.','docdirect_core' )));
					   die;
				  }else{
					echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=> esc_html__('Wrong reCaptcha. Please verify first.','docdirect_core' )));
					die;
				  }
			
			} else{
				echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=> esc_html__( 'Please enter reCaptcha!','docdirect_core' )));
				die;
			}
		}
		
		if ( isset($_POST['rememberme'])){
			$remember  = esc_sql($_POST['rememberme']);
		} else {
			$remember  = '';
		}
	
		if($remember) {
			$user_array['remember'] = true;
		} else {
			$user_array['remember'] = false;
		}
		
		if($user_array['user_login'] == ''){
			echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=> esc_html__('User name should not be empty.','docdirect_core')));
			exit();
		}elseif($user_array['user_password'] == ''){
			echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=>esc_html__('Password should not be empty.','docdirect_core')));
			exit();
		}else{
			$user = get_userdatabylogin($user_array['user_login']);			
			$verify_user = get_user_meta($user->ID , 'verify_user' , true);
			if ($verify_user == 'off') {
				echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=>esc_html__('Verify via email to activate account.','docdirect_core')));
			}else {	
				$status = wp_signon( $user_array, false );
				if ( is_wp_error($status) ){
					echo json_encode(array('type'=>'error','loggedin'=>false, 'message'=>esc_html__('Wrong username or password.','docdirect_core')));
				} else {
					session_start();
					$_SESSION['login_user_id']	= $status->ID;
					echo json_encode(array('type'=>'success','url'=> $login_redirect,'loggedin'=>true, 'message'=>esc_html__('Successfully Logged in.','docdirect_core')));				
				}
			}
		}
	
		die();
	}
	add_action('wp_ajax_docdirect_ajax_login', 'kt_docdirect_ajax_login');
	add_action('wp_ajax_nopriv_docdirect_ajax_login', 'kt_docdirect_ajax_login');
}

/**
 * @Make Review
 * @return 
 */
if ( ! function_exists( 'kt_docdirect_make_review_appointment' ) ) {

	function kt_docdirect_make_review_appointment() {
		global $current_user, $wp_roles,$userdata,$post;

		$user_to	= isset( $_POST['user_to'] ) && !empty( $_POST['user_to'] ) ? $_POST['user_to'] : '';
		$appointment_id	= isset( $_POST['appointment_id'] ) && !empty( $_POST['appointment_id'] ) ? $_POST['appointment_id'] : '';
		$dir_review_status	= 'pending';
		if (function_exists('fw_get_db_settings_option')) {
            $dir_review_status = fw_get_db_settings_option('dir_review_status', $default_value = null);
        }

		$number_review_3months = kt_count_reviews_3months($current_user->ID ,$_POST['user_to']);
		if ($number_review_3months > 1) {
			$json['type']		= 'error';
			$json['message']	 = pll__('Max 2 reviews in 90 days .');	
			echo json_encode($json);
			die;
		}
        
		$db_directory_type	 = get_user_meta( $user_to, 'directory_type', true);
		
		$user_rating1	   = $_POST['user_rating'];	
		$detail_rating	   = $_POST['detail_rating'];
		$final_detail = array();
		foreach ($detail_rating as $key => $value) {
			$final_detail[$key] = array( $user_rating1[$key], $value );
		}
			
		if( $_POST['user_subject'] != '' 
			&& $_POST['user_description'] != '' 
			&& $_POST['user_to'] != ''
		) {
		
			$user_subject	  = sanitize_text_field( $_POST['user_subject'] );
			$user_description  = sanitize_text_field( $_POST['user_description'] );
			$user_rating	   = sanitize_text_field( json_encode($_POST['user_rating']) );			
			if( is_user_logged_in() ){
				$user_from	     = sanitize_text_field( $current_user->ID );
			}else {
				$user_from = sanitize_text_field( $_POST['user_to'] );
			}
			$user_to	   	   = sanitize_text_field( $_POST['user_to'] );
			$directory_type	   	   = $db_directory_type;
			
			$review_post = array(
				'post_title'  => $user_subject,
				'post_status' => $dir_review_status,
				'post_content'=> $user_description,
				'post_author' => $user_from,
				'post_type'   => 'docdirectreviews',
				'post_date'   => current_time('Y-m-d H:i:s')
			);
			
			$post_id = wp_insert_post( $review_post );
	
			$review_meta = array(
				'user_rating' 	 => $user_rating,
				'detail_rating' 	 => $detail_rating,
				'user_from' 	   => $user_from,
				'user_to'   		 => $user_to,
				'directory_type'  => $directory_type,
				'review_date'   	 => current_time('Y-m-d H:i:s'),
			);
			
			$list_invite[$key_list]['status'] = 'complete';
			$new_invite = json_encode($list_invite);
			update_user_meta( $user_to, 'invite_review', $new_invite );

			//Update post meta
			foreach( $review_meta as $key => $value ){
				update_post_meta($post_id,$key,$value);
			}
			
			$new_values = $review_meta;
			
			if (isset($post_id) && !empty($post_id)) {
				fw_set_db_post_option($post_id, null, $new_values);
			}
			
			$json['type']	   = 'success';
			

			if( isset( $dir_review_status ) && $dir_review_status == 'publish' ) {
				$json['message']	= pll__('Your review published successfully.');
				$json['html']	   = 'refresh';
			} else{
				$json['message']	= pll__('Your review submitted successfully, it will be publised after approval.');
				$json['html']	   = '';
			}
			
			if( class_exists( 'DocDirectProcessEmail' ) ) {
				$user_from_data	= get_userdata($user_from);
				$user_to_data	  = get_userdata($user_to);
				$email_helper	  = new DocDirectProcessEmail();
				
				$emailData	= array();
				
				//User to data
				$emailData['email_to']	    = $user_to_data->user_email;
				$emailData['link_to']	= get_author_posts_url($user_to_data->ID);
				if( !empty( $user_to_data->display_name ) ) {
					$emailData['username_to']	   = $user_to_data->display_name;
				} elseif( !empty( $user_to_data->first_name ) || $user_to_data->last_name ) {
					$emailData['username_to']	   = $user_to_data->first_name.' '.$user_to_data->last_name;
				}
				
				//User from data
				/*if( !empty( $user_from_data->display_name ) ) {
					$emailData['username_from']	   = $user_from_data->display_name;
				} elseif( !empty( $user_from_data->first_name ) || $user_from_data->last_name ) {
					$emailData['username_from']	   = $user_from_data->first_name.' '.$user_from_data->last_name;
				}*/
					$emailData['username_from']	   = $user_from_data->first_name.' '.$user_from_data->last_name;

				$emailData['link_from']	= get_author_posts_url($user_from_data->ID);
				
				//General

                $rating_decode = json_decode($user_rating, true);
                $sum = array_sum($rating_decode);
                $trungbinh	= $sum/5;

				$emailData['detail_rating']	        = $final_detail;
				$emailData['rating']	        = $trungbinh;
				$emailData['reason']	        = $user_subject;
				$emailData['comment_content']	        = $user_description;
				
				kt_process_rating_email($emailData);
			}
			update_post_meta($appointment_id, 'user_reviews', 'done');
			
			echo json_encode($json);
			die;
			
		} else{
			$json['type']		= 'error';
			$json['message']	 = pll__('Please fill all the fields.');	
			echo json_encode($json);
			die;
		}
		
	}
	add_action('wp_ajax_docdirect_make_review_appointment','kt_docdirect_make_review_appointment');
	add_action( 'wp_ajax_nopriv_docdirect_make_review_appointment', 'kt_docdirect_make_review_appointment' );
}


/**
 * @Upload Image Gallery
 * @return {}
 */
if ( ! function_exists( 'kt_docdirect_image_uploader' ) ) {

	remove_action('wp_ajax_docdirect_image_uploader', 'docdirect_image_uploader');
	remove_action('wp_ajax_nopriv_docdirect_image_uploader', 'docdirect_image_uploader');

	function kt_docdirect_image_uploader() {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		
		$user_premium = get_user_meta($user_identity , 'user_premium' , true);
		$current_option = get_option( $user_premium, true );
		$number_of_photo = intval($current_option['photo_number']);

		/*-----------------------------Demo Restriction-----------------------------------*/
		if( isset( $_SERVER["SERVER_NAME"] ) 
			&& $_SERVER["SERVER_NAME"] === 'themographics.com' ){
			$json['type']	   =  "error";
			$json['message']	=  esc_html__("Sorry! you are restricted to perform this action on our demo.",'docdirect' );
			echo json_encode( $json );
			exit();
		}
		/*-----------------------------Demo Restriction END--------------------------------*/
		
		$nonce = $_REQUEST[ 'nonce' ];
		$type = $_REQUEST[ 'type' ];
		
		if ( ! wp_verify_nonce( $nonce, 'docdirect_upload_nounce' ) ) {
			$ajax_response = array(
				'success' => false,
				'reason' => 'Security check failed!',
			);
			echo json_encode( $ajax_response );
			die;
		}
		
		$submitted_file = $_FILES[ 'docdirect_uploader' ];
			$json['type1']	   =  count($_FILES[ 'docdirect_uploader' ]);
			$json['type']	   =  "error";
			$json['message']	= $number_of_photo;
			echo json_encode( $json );
			die;
		$uploaded_image = wp_handle_upload( $submitted_file, array( 'test_form' => false ) ); 

		if ( isset( $uploaded_image[ 'file' ] ) ) {
			$file_name = basename( $submitted_file[ 'name' ] );
			$file_type = wp_check_filetype( $uploaded_image[ 'file' ] );

			// Prepare an array of post data for the attachment.
			$attachment_details = array(
				'guid' => $uploaded_image[ 'url' ],
				'post_mime_type' => $file_type[ 'type' ],
				'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
				'post_content' => '',
				'post_status' => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment_details, $uploaded_image[ 'file' ] ); 
			$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_image[ 'file' ] ); 
			wp_update_attachment_metadata( $attach_id, $attach_data );                                    
			
			//Image Size
			$image_size	= 'thumbnail';
			if( isset( $type ) && $type === 'profile_image' ){
				$image_size	= 'docdirect_user_profile';
			} if( isset( $type ) && $type === 'profile_banner' ){
				$image_size	= 'docdirect_user_banner';
				docdirect_get_profile_image_url( $attach_data,$image_size ); //get image url
				$image_size	= 'docdirect_user_profile';
			} else if( isset( $type ) && $type === 'user_gallery' ){
				$image_size	= 'thumbnail';
			}
			
			
			$thumbnail_url = docdirect_get_profile_image_url( $attach_data,$image_size ); //get image url

			if( isset( $type ) && $type === 'profile_image' ){
				update_user_meta($user_identity, 'userprofile_media', $attach_id);
			} if( isset( $type ) && $type === 'profile_banner' ){
				update_user_meta($user_identity, 'userprofile_banner', $attach_id);
			} else if( isset( $type ) && $type === 'email_image' ){
				update_user_meta($user_identity, 'email_media', $attach_id);
			} else if( isset( $type ) && $type === 'user_gallery' ){
				//
			}
			
			$ajax_response = array(
				'success' => true,
				'url' => $thumbnail_url,
				'attachment_id' => $attach_id
			);

			echo json_encode( $ajax_response );
			die;

		} else {
			$ajax_response = array( 'success' => false, 'reason' => 'Image upload failed!' );
			echo json_encode( $ajax_response );
			die;
		}
	}
	// add_action('wp_ajax_docdirect_image_uploader', 'kt_docdirect_image_uploader');
	// add_action('wp_ajax_nopriv_docdirect_image_uploader', 'kt_docdirect_image_uploader');
}


if ( ! function_exists( 'kt_docdirect_update_schedules' ) ) {

	remove_action('wp_ajax_docdirect_update_schedules','docdirect_update_schedules');
	remove_action( 'wp_ajax_nopriv_docdirect_update_schedules', 'docdirect_update_schedules' );

	function kt_docdirect_update_schedules(){ 
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		$json	= array();
		
		$schedules	= $_POST['schedules'];
		update_user_meta( $user_identity, 'schedules', $schedules );
		
		//Time Formate
		if( !empty( $_POST['time_format'] ) ){
			update_user_meta( $user_identity, 'time_format', esc_attr( $_POST['time_format'] ) );
		}
		
		$json['type']	= 'success';
		$json['message']	= esc_html__('Schedules Updated.','docdirect');
		echo json_encode($json);
		die;
	}
	add_action('wp_ajax_docdirect_update_schedules','kt_docdirect_update_schedules');
	add_action( 'wp_ajax_nopriv_docdirect_update_schedules', 'kt_docdirect_update_schedules' );
}


/**
 * @Contact Doctor
 * @return 
 */
if (!function_exists('kt_docdirect_submit_me')) {

	remove_action('wp_ajax_docdirect_submit_me','docdirect_submit_me');
	remove_action( 'wp_ajax_nopriv_docdirect_submit_me', 'docdirect_submit_me' );

	function kt_docdirect_submit_me(){
		global $current_user;
		
		$json	= array();
		if(!is_user_logged_in()){
				$json['type']	= 'error';
				$json['message']	= esc_html__('Please login first.','docdirect');	
				echo json_encode($json);
				die;
		}
		$do_check = check_ajax_referer( 'docdirect_contact_me', 'user_security', false );
		if( $do_check == false ){
			//Do something
		}
		
		$bloginfo 		   = get_bloginfo();
		$email_subject 	=  "(" . $bloginfo . ") Contact Form Received";
		$success_message 	= esc_html__('Message Sent.','docdirect');
		$failure_message 	= esc_html__('Message Fail.','docdirect');
		
		$recipient 	=  sanitize_text_field( $_POST['email_to'] );
		
		if( empty( $_POST['email_to'] )){
			$recipient = get_option( 'admin_email' ,'Aamirshahzad2009@live.com' );
		}
		
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the form fields and remove whitespace.
            
			if( empty( $_POST['username'] )
				|| empty( $_POST['useremail'] ) 
				|| empty( $_POST['userphone']  ) 
				|| empty( $_POST['usersubject']  ) 
				|| empty( $_POST['user_description']  )
			){
				$json['type']	= 'error';
				$json['message']	= esc_html__('Please fill all fields.','docdirect');	
				echo json_encode($json);
				die;
			}
			
			if( ! is_email($_POST['useremail']) ){
				$json['type']	= 'error';
				$json['message']	= esc_html__('Email address is not valid.','docdirect');	
				echo json_encode($json);
				die;
			}
			
			$name	    = sanitize_text_field( $_POST['username'] );
			$email	  	= sanitize_text_field( $_POST['useremail'] );
			$subject	= sanitize_text_field( $_POST['usersubject'] );
			$phone	    = sanitize_text_field( $_POST['userphone'] );
			$message	= sanitize_text_field( $_POST['user_description'] );
			
            // Set the recipient email address.
            // FIXME: Update this to your desired email address.
            // Set the email subject.
            
			if( class_exists( 'DocDirectProcessEmail' ) ) {
				$email_helper	= new DocDirectProcessEmail();
				$emailData	   = array();
				$emailData['name']	  	       = $name;
				$emailData['email']			   = $email;
				$emailData['email_subject']	   = $email_subject;
				$emailData['subject']	  	    = $subject;
				$emailData['phone']	 		    = $phone;					
				$emailData['message']			= $message;
				$emailData['email_to']			= $recipient;
				
				kt_process_contact_user_email( $emailData );
			}
			
            // Send the email.
            $json['type']    = "success";
			$json['message'] = esc_attr($success_message);
			echo json_encode( $json );
			die();
        } else {
            echo 
			$json['type']    = "error";
			$json['message'] = esc_attr($failure_message);
			echo json_encode( $json );
            die();
        }
		
	}
	
	add_action('wp_ajax_docdirect_submit_me','kt_docdirect_submit_me');
	add_action( 'wp_ajax_nopriv_docdirect_submit_me', 'kt_docdirect_submit_me' );
}

/**
 * @hook process articles
 * @type insert
 */
if (!function_exists('fw_ext_docdirect_process_articles')) {

    function fw_ext_docdirect_process_articles() {
        global $current_user, $wp_roles, $userdata;
		$return_url	 = '';
        $type 	 	= !empty($_POST['type']) ? esc_attr($_POST['type']) : '';
        $current  	= !empty($_POST['current']) ? esc_attr($_POST['current']) : '';
		$provider_category	 = get_user_meta( $current_user->ID, 'directory_type', true);
		remove_all_filters("content_save_pre");
		
		if( function_exists('docdirect_is_demo_site') ) { 
			docdirect_is_demo_site();
		}; //if demo site then prevent
		
        $do_check = check_ajax_referer('docdirect_article_nounce', 'docdirect_article_nounce', false);
        if ($do_check == false) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('No kiddies please!', 'docdirect');
            echo json_encode($json);
            die;
        }

        if (empty($_POST['article_title'])) {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Title field should not be empty.', 'docdirect');
            echo json_encode($json);
            die;
        }

        $title = !empty($_POST['article_title']) ? esc_attr($_POST['article_title']) : esc_html__('unnamed', 'docdirect');
        $article_detail = force_balance_tags($_POST['article_detail']);
        $article_detail_hk = force_balance_tags($_POST['article_detail_hk']);
        $article_detail_cn = force_balance_tags($_POST['article_detail_cn']);
        $article_detail_fr = force_balance_tags($_POST['article_detail_fr']);

        $title_hk = esc_attr($_POST['title_hk']);
        $title_cn = esc_attr($_POST['title_cn']);
        $title_fr = esc_attr($_POST['title_fr']);
		
        $attachment_id = !empty($_POST['attachment_id']) ? intval($_POST['attachment_id']) : '';
        $article_tags  = !empty($_POST['article_tags']) ? $_POST['article_tags'] : array();

        $dir_profile_page = '';
        if (function_exists('fw_get_db_settings_option')) {
            $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
        }

        $profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
            
		$category_id = isset( $_POST['category_id'] ) ? $_POST['category_id'] : '';
		
        //add/edit job
        if (isset($type) && $type === 'add') {

            $article_post = array(
                'post_title' 	=> $title,
                'post_status' 	=> 'publish',
                'post_content'  => $article_detail,
                'post_author' 	=> $current_user->ID,
                'post_type' 	=> 'sp_articles',
                'post_date' 	=> current_time('Y-m-d H:i:s')
            );
            
			$post_id = wp_insert_post($article_post);
            
			wp_set_post_terms($post_id, $article_tags, 'article_tags');
			wp_set_post_terms($post_id, $category_id, 'sp_category');
            if (!empty($attachment_id)) {
                set_post_thumbnail($post_id, $attachment_id);
            }
			update_post_meta($post_id, 'attachment_file', $_POST['attachment_file']);
            
			$return_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'articles', $current_user->ID, 'true', 'listing');
        	$json['return_url']	= htmlspecialchars_decode($return_url);
			
			update_post_meta($post_id, 'provider_category', $provider_category);
			
			update_post_meta($post_id, 'article_detail_hk', $article_detail_hk);
			update_post_meta($post_id, 'article_detail_cn', $article_detail_cn);
			update_post_meta($post_id, 'article_detail_fr', $article_detail_fr);

			update_post_meta($post_id, 'title_hk', $title_hk);
			update_post_meta($post_id, 'title_cn', $title_cn);
			update_post_meta($post_id, 'title_fr', $title_fr);
			
		} elseif (isset($type) && $type === 'update' && !empty($current)) {
            $post_author = get_post_field('post_author', $current);

            if (intval($current_user->ID) === intval($post_author)) {
                $article_post = array(
                    'ID' => $current,
                    'post_title' => $title,
                    'post_content' => $article_detail,
                );
				
                wp_update_post($article_post);
                $post_id = $current;
                wp_set_post_terms($post_id, $article_tags, 'article_tags');
                wp_set_post_terms($post_id, $category_id, 'sp_category');
                update_post_meta($post_id, 'provider_category', $provider_category);
				update_post_meta($post_id, 'attachment_file', $_POST['attachment_file']);

				update_post_meta($post_id, 'article_detail_hk', $article_detail_hk);
				update_post_meta($post_id, 'article_detail_cn', $article_detail_cn);
				update_post_meta($post_id, 'article_detail_fr', $article_detail_fr);

				update_post_meta($post_id, 'title_hk', $title_hk);
				update_post_meta($post_id, 'title_cn', $title_cn);
				update_post_meta($post_id, 'title_fr', $title_fr);
				
				if (!empty($attachment_id)) {
                    set_post_thumbnail($post_id, $attachment_id);
                }

			} else {
                $json['type'] = 'error';
                $json['message'] = esc_html__('Some error occur, please try again later.', 'docdirect');
                echo json_encode($json);
                die;
            }
        } else {
            $json['type'] = 'error';
            $json['message'] = esc_html__('Some error occur, please try again later.', 'docdirect');
            echo json_encode($json);
            die;
        }
		
		
        $json['type'] = 'success';
        $json['message'] = esc_html__('Article updated successfully.', 'docdirect');
        echo json_encode($json);
        die;
    }

    add_action('wp_ajax_fw_ext_docdirect_process_articles', 'fw_ext_docdirect_process_articles');
    add_action('wp_ajax_nopriv_fw_ext_docdirect_process_articles', 'fw_ext_docdirect_process_articles');
}

/**
 * @Add Time Slots
 * @return {}
 */
if ( ! function_exists( 'docdirect_save_custom_slots' ) ) {
	function docdirect_save_custom_slots(){
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		$timeslots_object	 = sanitize_text_field($_POST['custom_timeslots_object']);
		$custom_timeslots_object = stripslashes($timeslots_object);
		update_user_meta( $user_identity, 'custom_slots', addslashes($custom_timeslots_object) );
		
		$json['events']	= kt_get_user_calendar();
		$json['type']	= 'success';
		$json['message']	= esc_html__('Custom dates added successfully.','docdirect');
			
		echo json_encode($json);
		die;
		
	}
	add_action('wp_ajax_docdirect_save_custom_slots','docdirect_save_custom_slots');
	add_action( 'wp_ajax_nopriv_docdirect_save_custom_slots', 'docdirect_save_custom_slots' );
}

/**
 * @Delete banner
 * @return {}
 */
if ( ! function_exists( 'docdir_delete_user_banner' ) ) {
	function docdir_delete_user_banner() {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		$json	=  array();
		
		/*-----------------------------Demo Restriction-----------------------------------*/
		if( isset( $_SERVER["SERVER_NAME"] ) 
			&& $_SERVER["SERVER_NAME"] === 'themographics.com' ){
			$json['type']	   =  "error";
			$json['message']	=  esc_html__("Sorry! you are restricted to perform this action on our demo.",'docdirect' );
			echo json_encode( $json );
			exit();
		}
		/*-----------------------------Demo Restriction END--------------------------------*/
		
		$update_avatar = update_user_meta($user_identity, 'userprofile_banner', '');
		if($update_avatar){
			$json['avatar'] = get_stylesheet_directory_uri().'/images/doctor-banner-default.jpg';
			$json['type']		=  'success';	
			$json['message']		= esc_html__('Banner deleted.','docdirect');	
		} else {
			$json['type']		=  'error';	
			$json['message']		= esc_html__('Some error occur, please try again later.','docdirect');	
		}
		echo json_encode($json);
		exit;
	}
	add_action('wp_ajax_docdir_delete_user_banner', 'docdir_delete_user_banner');
	add_action('wp_ajax_nopriv_docdir_delete_user_banner', 'docdir_delete_user_banner');
}

if ( ! function_exists( 'docdir_delete_user_banner_mobile' ) ) {
	function docdir_delete_user_banner_mobile() {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		$json	=  array();
		
		/*-----------------------------Demo Restriction-----------------------------------*/
		if( isset( $_SERVER["SERVER_NAME"] ) 
			&& $_SERVER["SERVER_NAME"] === 'themographics.com' ){
			$json['type']	   =  "error";
			$json['message']	=  esc_html__("Sorry! you are restricted to perform this action on our demo.",'docdirect' );
			echo json_encode( $json );
			exit();
		}
		/*-----------------------------Demo Restriction END--------------------------------*/
		
		$update_avatar = update_user_meta($user_identity, 'userprofile_banner_mobile', '');
		if($update_avatar){
			$json['avatar'] = get_template_directory_uri().'/images/user270x270.jpg';
			$json['type']		=  'success';	
			$json['message']		= esc_html__('Banner deleted.','docdirect');	
		} else {
			$json['type']		=  'error';	
			$json['message']		= esc_html__('Some error occur, please try again later.','docdirect');	
		}
		echo json_encode($json);
		exit;
	}
	add_action('wp_ajax_docdir_delete_user_banner_mobile', 'docdir_delete_user_banner_mobile');
	add_action('wp_ajax_nopriv_docdir_delete_user_banner_mobile', 'docdir_delete_user_banner_mobile');
}

if ( ! function_exists( 'docdir_delete_user_company_logo' ) ) {
	function docdir_delete_user_company_logo() {
		global $current_user, $wp_roles,$userdata,$post;
		$user_identity	= $current_user->ID;
		$json	=  array();
		
		/*-----------------------------Demo Restriction-----------------------------------*/
		if( isset( $_SERVER["SERVER_NAME"] ) 
			&& $_SERVER["SERVER_NAME"] === 'themographics.com' ){
			$json['type']	   =  "error";
			$json['message']	=  esc_html__("Sorry! you are restricted to perform this action on our demo.",'docdirect' );
			echo json_encode( $json );
			exit();
		}
		/*-----------------------------Demo Restriction END--------------------------------*/
		
		$update_avatar = update_user_meta($user_identity, 'userprofile_company_logo', '');
		if($update_avatar){
			$json['avatar'] = get_template_directory_uri().'/images/user270x270.jpg';
			$json['type']		=  'success';	
			$json['message']		= esc_html__('Company logo deleted.','docdirect');	
		} else {
			$json['type']		=  'error';	
			$json['message']		= esc_html__('Some error occur, please try again later.','docdirect');	
		}
		echo json_encode($json);
		exit;
	}
	add_action('wp_ajax_docdir_delete_user_company_logo', 'docdir_delete_user_company_logo');
	add_action('wp_ajax_nopriv_docdir_delete_user_company_logo', 'docdir_delete_user_company_logo');
}





