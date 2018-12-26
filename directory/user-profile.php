<?php
/**
 *  Template Name: Dashboard
 * 
 */

global $current_user, $wp_roles,$userdata,$post;
$dir_obj	= new DocDirect_Scripts();
$user_identity	= $current_user->ID;
$url_identity	= $user_identity;
if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
	$url_identity	= $_GET['identity'];
}

if( isset( $_GET['identity'] ) && $_GET['identity'] != $user_identity ) {
	do_action('docdirect_update_profile_hits',$url_identity); //Update Profile Hits
}

do_action('docdirect_is_user_active',$url_identity);
do_action('docdirect_is_user_verified',$url_identity);

get_header();

docdirect_init_dir_map();//init Map
docdirect_enque_map_library();//init Map

$rtl_class	= '';
if( is_rtl() ){
	$rtl_class = 'pull-right ';
}
 
/*			$emailData	= array();
			$emailData['post_id']	= 6245;
 kt_doctor_cancel_booking_email($emailData);
 kt_patient_remind_booking_email($emailData);
 kt_process_appointment_confirmation_email($emailData);
 kt_process_appointment_confirmation_admin_email($emailData);
 kt_process_appointment_approved_email($emailData);*/


  if (isset($_GET["ref"]) && $_GET["ref"]!='') {
      function kt_add_code_footer1() {
        if(!is_user_logged_in()) {
          ?>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                  // $('.tg-user-modal').modal('show');
                  setTimeout(function(){
                  	// $('.tg-user-modal').modal('show');
                  	$('.doc-admin .doc-btn').trigger('click');
                  },1000);
                });
            </script>
          <?php
      	}
      }
      add_action('wp_footer', 'kt_add_code_footer1');
  }

?>
<div class="container">
	<div class="row">
		<?php if (is_active_sidebar('user-dashboard-top')) {?>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <div class="tg-haslayout ads-dashboard-top">
                <?php dynamic_sidebar('user-dashboard-top'); ?>
              </div>
          </div>
        <?php }?>
		<?php if( apply_filters( 'docdirect_do_check_user_existance', $url_identity ) ){?>
        <div class="user-info col-sm-12">
        	<?php
				if (isset($_GET["token"]) && $_GET["token"] != '') {
					?>
					<div id="tg-dashboard-invoice" class="tg-dashboard-invoice tg-haslayout">			
						<?php
			    			$token = $_GET["token"];//Returned by paypal, you can save this in SESSION too
						    $requestParams = array('TOKEN' => $token);
						    $paypal = new wp_paypal_gateway (true);
						    $paypal->getExpressCheckout($requestParams);
						    $response = $paypal->getResponse();
					    	$AMT = $response["AMT"];
					    	$payer_email = $response["EMAIL"];
					    	$payer_firstname = $response["FIRSTNAME"];
					    	$payer_lastname = $response["LASTNAME"];
				            /*echo '<pre>';
	            			print_r($response);
				            echo '</pre>';*/
				            $CUSTOM = json_decode($response['CUSTOM'], true);
				            $order_id = $CUSTOM['order_no'];
				            $current_package = $CUSTOM['packs'];
				            $user_identity = $CUSTOM['user_identity'];

							$pack_title		  = get_the_title( $current_package ); 
							$duration 			= fw_get_db_post_option($current_package, 'duration', true);
							$price 			   = fw_get_db_post_option($current_package, 'price', true);
							$pac_subtitle 		= fw_get_db_post_option($current_package, 'pac_subtitle', true);
							$package_name	= $pack_title.' - '.$duration.esc_html__('Days','docdirect');

							$package_type = get_post_meta($current_package, 'package_type', true);

							if (isset($_GET["PayerID"]) && $_GET["PayerID"] != '') {
								$PayerID = $_GET["PayerID"];
						        $requestParams=array(
						            "TOKEN" => $token,
						            "PAYERID" => $PayerID,
						            "PAYMENTREQUEST_0_AMT" => $price,//Payment amount. This value should be sum of of item values, if there are more items in order
						            "PAYMENTREQUEST_0_CURRENCYCODE" => "HKD",//Payment currency
						            "PAYMENTREQUEST_0_ITEMAMT" => $price//Item amount
						        );
							    $paypal->doExpressCheckout($requestParams);
						    	$response = $paypal->getResponse();
					    		$TRANSACTIONID = $response["PAYMENTINFO_0_TRANSACTIONID"];

						    	if(is_array($response) && $response["ACK"]=="Success"){
            						echo '<br>Successful';
						            /*echo '<pre>';
			            			print_r($paypal->getResponse());
						            echo '</pre>';*/

									update_user_meta($user_identity, 'user_premium', $package_type);

									$userdata	  = get_userdata($user_identity);
									$user_email	= $userdata->user_email;
                                	$user_name = $user->first_name.' '.$user->last_name;
									
									//Update Order
						            $expiry_date	= kt_docdirect_update_order_data(
										array(
											'order_id'		 => $order_id,
											'user_identity'	=> $user_identity,
											'package_id'	   => $current_package,
											'txn_id'		   => $TRANSACTIONID,
											'payment_gross'	=> $AMT,
											'payment_method'   => 'paypal',
											'mc_currency'	  => 'HKD',
										)
									);
									
									//Add Invoice
									docdirect_new_invoice(
										array(
											'user_identity'	 	=> $user_identity,
											'package_id'		=> $current_package,
											'txn_id'			=> 'PP_'.$TRANSACTIONID,
											'payment_gross'	 	=> $AMT,
											'item_name'		 	=> $pack_title,
											'payer_email'	  	=> $payer_email,
											'mc_currency'	   	=> 'HKD',
											'address_name'	  	=> '',
											'ipn_track_id'	  	=> '',
											'transaction_status'=> 'approved',
											'payment_method'	=> 'paypal',
											'full_address'	  	=> '',
											'first_name'		=> $payer_firstname,
											'last_name'		 	=> $payer_lastname,
											'purchase_on'	   	=> date('Y-m-d H:i:s'),
										)
									);
									
									
									//Send ean email 
									if( class_exists( 'DocDirectProcessEmail' ) ) {
										$email_helper	= new DocDirectProcessEmail();
										$emailData	= array();
										$emailData['mail_to']	  	   = $user_email;
										$emailData['name']			  = $user_name;
										$emailData['invoice']	  	   = $TRANSACTIONID;
										$emailData['package_name']	  = $pack_title;					
										$emailData['amount']			= $AMT;
										$emailData['status']			= esc_html__('Approved','docdirect_core');
										$emailData['method']			= esc_html__('Paypal','docdirect_core');
										$emailData['date']			  = date('Y-m-d H:i:s');
										$emailData['expiry']			= $expiry_date;
										$emailData['address']		   = '';
										
										$email_helper->process_invoice_email($emailData);
									}


						        }else {
						            /*echo '<pre>';
			            			print_r($paypal->getResponse());
						            echo '</pre>';*/
						        }

						        $current_profileid = get_user_meta($user_identity, 'payment_profileid', true);
						        if ($current_profileid != '') {
						        	
						        	$requestParams = array(
								        'PROFILEID' => $current_profileid,
								        'ACTION'    => 'Cancel'
								        );
								    
								    $response = $paypal->ManageRecurringPaymentsProfileStatus($requestParams);
						            /*echo '<pre>';
			            			print_r($response);
						            echo '</pre>';*/

						        }
						        
						        $requestParams=array(
						            "TOKEN" => $token,
						            "PAYERID" => $PayerID,
						            "PROFILESTARTDATE"=>date("Y-m-d\TH:i:s\Z",strtotime("now")),
						            "DESC" => $package_name,
						            "BILLINGPERIOD"=> "Day",
						            "BILLINGFREQUENCY"=> $duration,
						            // "TOTALBILLINGCYCLES"=>"3",
						            "AMT"=> $price,
						            "CURRENCYCODE"=>"HKD",
						            "PROFILEREFERENCE" => $user_identity,//This value is for example ID of a user 
						            'MAXFAILEDPAYMENTS' => 3

						        );
							    $paypal->CreateRecurringPaymentsProfile($requestParams);
						    	$response = $paypal->getResponse();
						    	if(is_array($response) && $response["PROFILEID"] != ""){
									update_user_meta($user_identity, 'payment_profileid', $response["PROFILEID"]); //Update PROFILEID
            						echo '<br>auto payment Successful';
						            /*echo '<pre>';
			            			print_r($response);
						            echo '</pre>';*/
						        }else {
						            /*echo '<pre>';
			            			print_r($paypal->getResponse());
						            echo '</pre>';*/
						        }

						        
						        
						?>
						<?php
							}
						?>
					</div>
					<?php
				}
			?>
        	<?php
				$author = get_userdata( $url_identity );
				$user_roles = $author->roles;
				if( $user_roles[0] == 'professional' ){

					$user_current_package = get_user_meta($url_identity, 'user_current_package', true);
					$user_featured = get_user_meta($url_identity, 'user_featured', true);

					$today = current_time( 'timestamp' );
					$msg3 = '';
					if ($user_current_package != '') {
						$dir_profile_page = '';
						if (function_exists('fw_get_db_settings_option')) {
			                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			            }
						$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
						$link = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity, true);

						if ($user_featured != '' && $user_featured > $today) {
							$left = $user_featured - $today;
							$remaining_days = ceil($left/86400);
							$msg = $remaining_days.' '.pll__( 'days left before renewal');
						}else {

							$msg = ' <a href="'.$link.'"><i class="fa fa-refresh"></i>'.pll__( 'Review Membership' ).'</a>';
							$msg2 = pll__( 'Purchage membership to remain listed.' );
							/*$left = $today - $user_featured;
							$msg = pll__( 'Membership expired.').' <a href="'.$link.'">'.pll__( 'click to RENEW' ).'</a>';
							$msg2 = pll__( 'Membership has ended. to remain listed you must renew your membership' );
							$msg3 = '<i class="fa fa-repeat" aria-hidden="true"></i> <a href="'.$link.'">'.pll__( 'Renew your Membership' ).'</a>';*/
						}
					}else {
						$udata = get_userdata( $url_identity );
	        			/*$registered = $udata->user_registered;
	        			$left = $today - strtotime($registered);
						$remaining_days = 180 - ceil($left/86400);*/
						$dir_profile_page = '';
						if (function_exists('fw_get_db_settings_option')) {
			                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
			            }
						$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
						$link = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity, true);

						$permalink = add_query_arg( 
								array(
									'ref'=>  'booking-settings' ,
									'identity'=>   urlencode( $user_identity ) ,
									'verify'=>   'paypal' 
									), esc_url( get_permalink($profile_page) 
								) 
							);
						
						$left = $user_featured - $today;
						$remaining_days = ceil($left/86400);
						if ($user_featured != '' && $user_featured > $today) {
							$msg = $remaining_days.' '.pll__( 'days left before renewal');
							$msg2 = pll__( 'Free trial is active. Enjoy your 6 months free membership!' );
						}else {
							$msg = ' <a href="'.$link.'"><i class="fa fa-refresh"></i>'.pll__( 'Review Membership' ).'</a>';
							$msg2 = pll__( 'Purchage membership to remain listed.' );
							// $msg3 = '<i class="fa fa-repeat" aria-hidden="true"></i> <a href="'.$link.'">'.pll__( 'Renew your Membership' ).'</a>';
						}
					}

				}
        	?>
        <?php if ( is_user_logged_in() ) {?>
        	<div class="row">
        		<?php
        			if ( $msg != '' ) {
        		?>
        		<?php if ($user_featured == '') {?>
	        	<div class="col-sm-4 col-md-3 green">
	        	<?php }else{?>
	        	<div class="col-xs-6 col-sm-4 col-md-3 grey">
	        	<?php }?>
	        		<span><?php echo $msg;?></span>
	        	</div>
	        	<?php }?>
	        	
	        		<?php
	        			if ( $msg2 != '' ) {
	        				echo '<div class="col-xs-6 col-sm-8 col-md-6"><span>'.$msg2.'</span></div>';
	        			}
	        		?>
	        	
	        	
	        		<?php
	        			if ( $msg3 != '' ) {
	        				echo '<div class="col-sm-12 col-sm-3 green"><span>'.$msg3.'</span></div>';
	        			}
	        		?>
	        	
	        </div>
	    <?php }?>
        </div>
		<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 <?php echo sanitize_html_class( $rtl_class );?>">
			<aside id="tg-sidebar" class="dashboard-sidebar">
				<?php kt_docdirect_get_avatar();?>
				<?php 
					if ( is_user_logged_in() && ($user_featured != '' && $user_featured > $today) && $current_user->ID == $_GET['identity']) {
						$author = get_userdata( $url_identity );
						$user_roles = $author->roles;
						if( $user_roles[0] == 'professional' ){
							kt_custom_profile_link();
						}
					}
				?>
				<?php kt_docdirect_profile_menu();?>
                <?php if (is_active_sidebar('user-dashboard-sidebar')) {?>
                  <div class="tg-doctors-list tg-haslayout">
                    <?php dynamic_sidebar('user-dashboard-sidebar'); ?>
                  </div>
                <?php }?>
			</aside>
            
		</div>
        <div class="col-lg-9 col-md-9 col-sm-8 col-xs-12 pull-right">
			<?php
				if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'schedules' 
					&& $url_identity == $user_identity 
					&& apply_filters('docdirect_do_check_user_type',$url_identity ) === true
				){
					get_template_part('directory/templates/user','schedules');
				} else if( isset( $_GET['ref'] )
					 && $_GET['ref'] === 'invoices' 
					 && $url_identity == $user_identity 
					 && apply_filters('docdirect_do_check_user_type',$url_identity ) === true 
				 ){
					get_template_part('directory/templates/user','payments');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'settings' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/templates/user','account-settings');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'practices' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/templates/user','practices');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'addblog' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/templates/user','addblog');
				}else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'affiliation' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/templates/user','affiliation');
				}else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'invite-review' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/templates/user','invite-review');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'bookings' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/bookings/templates/user','bookings');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'mybookings' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/bookings/templates/patient','bookings');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'booking-schedules' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/bookings/templates/booking','schedules');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'booking-settings' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/bookings/templates/booking','settings');
				} else if (( isset($_GET['ref']) && $_GET['ref'] === 'articles' ) 
						   && ( isset($_GET['mode']) && $_GET['mode'] === 'listing' ) 
						   && $url_identity == $user_identity
				) {
					if ( post_type_exists( 'sp_articles' ) ) {
						do_action('render_article_listing_view');
					} else{
						DoctorDirectory_NotificationsHelper::informations(esc_html__('Sorry, This feature is compatible with latest version of DocDirect Core Plugin( Since release 3.5 ). Please contact to your site administrator for this issue.','docdirect'));	
					}
				} else if (( isset($_GET['ref']) && $_GET['ref'] === 'articles' ) 
						   && ( isset($_GET['mode']) && $_GET['mode'] === 'add' ) 
						   && $url_identity == $user_identity
				) {	
					if ( post_type_exists( 'sp_articles' ) ) {
						do_action('render_article_add_view');
					} else{
						DoctorDirectory_NotificationsHelper::informations(esc_html__('Sorry, This feature is compatible with latest version of DocDirect Core Plugin( Since release 3.5 ). Please contact to your site administrator for this issue.','docdirect'));	
					}
				} else if (( isset($_GET['ref']) && $_GET['ref'] === 'articles' ) 
						   && ( isset($_GET['mode']) && $_GET['mode'] === 'edit' ) 
						   && $url_identity == $user_identity
				) {
					if ( post_type_exists( 'sp_articles' ) ) {
						do_action('render_article_edit_view');
					} else{
						DoctorDirectory_NotificationsHelper::informations(esc_html__('Sorry, This feature is compatible with latest version of DocDirect Core Plugin( Since release 3.5 ). Please contact to your site administrator for this issue.','docdirect'));	
					}
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'wishlist' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/templates/user','favourites');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'security' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/templates/user','security-settings');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'privacy-settings' 
					&& $url_identity == $user_identity 
				){
					get_template_part('directory/templates/user','privacy-settings');
				} else if( isset( $_GET['ref'] ) 
					&& $_GET['ref'] === 'favourites' 
					&& $url_identity == $user_identity 
					&& apply_filters('docdirect_do_check_user_type',$url_identity ) === true 
				){
					get_template_part('directory/templates/user','account-settings');
				}else{
					get_template_part('directory/templates/dashboard','company'); //Show when current user
					get_template_part('directory/templates/user','profile');
				}
			?>
		</div>
		<?php } else{?>
			<div class="col-xs-12">
				<?php DoctorDirectory_NotificationsHelper::warning(esc_html__('Looks like your lost.','docdirect'));?>
			</div>
		<?php }?>
        
	</div>
</div>

<!----------------------------------------------------
 * Undercore HTML Tempaltes
 ------------------------------------------------- -->
<script type="text/template" id="tmpl-load-gallery">
	<figure>
		<a href="javascript:;"><img height="156" width="156" src="{{data.url}}"></a>
		<div class="tg-img-hover" data-attachment_id="{{data.attachment_id}}">
			<a href="javascript:;" data-attachment="{{data.attachment_id}}"><i class="fa fa-plus"></i><i class="fa fa-refresh fa-spin"></i></a>
		</div>
		<input type="hidden" value="{{data.attachment_id}}" name="user_gallery[{{data.attachment_id}}][attachment_id]">
		<input type="hidden" value="{{data.url}}" name="user_gallery[{{data.attachment_id}}][url]">
	</figure>
</script>
<!--Awards-->
<script type="text/template" id="tmpl-load-awards">
	<tbody class="awards_item new-added">
	  <tr>
		<td data-title="Code"><?php pll_e('Award Title','docdirect');?>
		  <div class="tg-table-hover award-action"> 
			<a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a>
			<a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
		   </div>
		</td>
		<td data-title="Company"><?php pll_e('January 01, 2020','docdirect');?></td>
	  </tr>
	  <tr>
		<td class="award-data edit-me-row"colspan="2">
			<div class="tg-education-form tg-haslayout">
				<div class="award-data">
					<div class="col-md-8 col-sm-8 col-xs-12">
						<div class="form-group">
							<input class="form-control" value="" name="awards[{{data}}][name]" type="text" placeholder="<?php esc_attr_e('Award Name','docdirect');?>">
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">
						<div class="form-group">
							<input class="form-control award_datepicker" id="award_datepicker" value="" name="awards[{{data}}][date]" type="text" placeholder="<?php esc_attr_e('Award Date','docdirect');?>">
						</div>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<textarea class="form-control" name="awards[{{data}}][description]" placeholder="<?php esc_attr_e('Award Description','docdirect');?>"></textarea>
						</div>
					</div>
				</div>
			</div>
		</td>
	  </tr>
	</tbody>
</script>
<script type="text/template" id="tmpl-append-awards">
	<# if( _.isArray(data) && ! _.isEmpty(data) ) { #>
	<table class="table-striped awards_wrap">
		<thead class="cf">
		  <tr>
			<th><?php pll_e('Title','docdirect');?></th>
			<th><?php pll_e('Year','docdirect');?></th>
		  </tr>
		</thead>
		<# _.each( data , function( element, index, attr ) { #>
			
			<tbody class="awards_item new-added">
			  <tr>
				<td data-title="Code">{{element.name}}
				  <div class="tg-table-hover award-action"> 
					<a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a>
					<a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
				   </div>
				</td>
				<td data-title="Company">{{element.date_formated}}</td>
			  </tr>
			  <tr>
				<td class="award-data edit-me-row"colspan="2">
					<div class="tg-education-form tg-haslayout">
						<div class="award-data">
							<div class="col-md-8 col-sm-8 col-xs-12">
								<div class="form-group">
									<input class="form-control" value="{{element.name}}" name="awards[{{index}}][name]" type="text" placeholder="<?php esc_attr_e('Award Name','docdirect');?>">
								</div>
							</div>
							<div class="col-md-4 col-sm-4 col-xs-12">
								<div class="form-group">
									<input class="form-control award_datepicker" id="award_datepicker" value="{{element.date}}" name="awards[{{index}}][date]" type="text" placeholder="<?php esc_attr_e('Award Date','docdirect');?>">
								</div>
							</div>
							<div class="col-md-12 col-sm-12 col-xs-12">
								<div class="form-group">
									<textarea class="form-control" name="awards[{{index}}][description]" placeholder="<?php esc_attr_e('Award Description','docdirect');?>">{{element.description}}</textarea>
								</div>
							</div>
						</div>
					</div>
				</td>
			  </tr>
			</tbody>
			
		<# } ); #>
	<# } #>
</script>
<!--Education-->
<script type="text/template" id="tmpl-load-educations">
	<tbody class="educations_item">
	  <tr>
		<td data-title="Code"><?php esc_attr_e('Title here','docdirect');?>
		  <div class="tg-table-hover education-action"> 
			  <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a> 
			  <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
		  </div>
		</td>
		<td data-title="Company"><?php esc_attr_e('Institute here','docdirect');?></td>
		<td data-title="Price" class="numeric"><?php esc_attr_e('Jan,2020 - Jan,2021','docdirect');?></td>
	  </tr>
	  <tr>
	   <td class="education-data edit-me-row" colspan="3">
		 <div class="education-data-wrap">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<input class="form-control" value="" name="education[{{data}}][title]" type="text" placeholder="<?php esc_attr_e('Title','docdirect');?>">
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<input class="form-control" value="" name="education[{{data}}][institute]" type="text" placeholder="<?php esc_attr_e('Institute','docdirect');?>">
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<input class="form-control edu_start_date_{{data}}" id="edu_start_date" value="" name="education[{{data}}][start_date]" type="text" placeholder="<?php esc_attr_e('Start Date','docdirect');?>">
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<input class="form-control edu_end_date_{{data}}" id="edu_end_date" value="" name="education[{{data}}][end_date]" type="text" placeholder="<?php esc_attr_e('End Date','docdirect');?>">
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<textarea class="form-control" name="education[{{data}}][description]" placeholder="<?php esc_attr_e('Education Description','docdirect');?>"></textarea>
				</div>
			</div>
		  </div>
	    </td>
	  </tr>
	</tbody>
</script>
<script type="text/template" id="tmpl-append-educations">
	<# if( _.isArray(data) && ! _.isEmpty(data) ) { #>
		<table class="table-striped educations_wrap" id="table-striped">
		<thead class="cf">
		  <tr>
			<th><?php pll_e('Degree / Education Title','docdirect');?></th>
			<th><?php pll_e('Institute','docdirect');?></th>
			<th class="numeric"><?php pll_e('Year','docdirect');?></th>
		  </tr>
		</thead>
		<# _.each( data , function( element, index, attr ) { #>
		<tbody class="educations_item">
		  <tr>
			<td data-title="Code">{{element.title}}
			  <div class="tg-table-hover education-action"> 
				  <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a> 
				  <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
			  </div>
			</td>
			<td data-title="Company">{{element.institute}}</td>
			<td data-title="Price" class="numeric">{{element.start_date_formated}} - {{element.end_date_formated}}</td>
		  </tr>
		  <tr>
		   <td class="education-data edit-me-row" colspan="3">
			 <div class="education-data-wrap">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input class="form-control" value="{{element.title}}" name="education[{{index}}][title]" type="text" placeholder="<?php esc_attr_e('Title','docdirect');?>">
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input class="form-control" value="{{element.institute}}" name="education[{{index}}][institute]" type="text" placeholder="<?php esc_attr_e('Institute','docdirect');?>">
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input class="form-control edu_start_date_{{index}}" id="edu_start_date" value="{{element.start_date}}" name="education[{{index}}][start_date]" type="text" placeholder="<?php esc_attr_e('Start Date','docdirect');?>">
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input class="form-control edu_end_date_{{index}}" id="edu_end_date" value="{{element.end_date}}" name="education[{{index}}][end_date]" type="text" placeholder="<?php esc_attr_e('End Date','docdirect');?>">
					</div>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<textarea class="form-control" name="education[{{index}}][description]" placeholder="<?php esc_attr_e('Education Description','docdirect');?>">{{element.description}}</textarea>
					</div>
				</div>
			  </div>
			</td>
		  </tr>
		</tbody>
		<# } ); #>
	<# } #>
</script>
<!--Experience-->
<script type="text/template" id="tmpl-load-experiences">
	<tbody class="experiences_item">
	  <tr>
		<td data-title="Code"><?php esc_attr_e('Title here','docdirect');?>
		  <div class="tg-table-hover experience-action"> 
			  <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a> 
			  <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
		  </div>
		</td>
		<td data-title="Company"><?php esc_attr_e('Company/Organization Name','docdirect');?></td>
		<td data-title="Price" class="numeric"><?php esc_attr_e('Jan,2020 - Jan,2021','docdirect');?></td>
	  </tr>
	  <tr>
	   <td class="experience-data edit-me-row" colspan="3">
		 <div class="experience-data-wrap">
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<input class="form-control" value="" name="experience[{{data}}][title]" type="text" placeholder="<?php esc_attr_e('Title','docdirect');?>">
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<input class="form-control" value="" name="experience[{{data}}][company]" type="text" placeholder="<?php esc_attr_e('Company/Organization','docdirect');?>">
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<input class="form-control exp_start_date_{{data}}" id="exp_start_date" value="" name="experience[{{data}}][start_date]" type="text" placeholder="<?php esc_attr_e('Start Date','docdirect');?>">
				</div>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					<input class="form-control exp_end_date_{{data}}" id="exp_end_date" value="" name="experience[{{data}}][end_date]" type="text" placeholder="<?php esc_attr_e('End Date','docdirect');?>">
				</div>
			</div>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="form-group">
					<textarea class="form-control" name="experience[{{data}}][description]" placeholder="<?php esc_attr_e('Experience Description','docdirect');?>"></textarea>
				</div>
			</div>
		  </div>
	    </td>
	  </tr>
	</tbody>
</script>
<script type="text/template" id="tmpl-append-experiences">
	<# if( _.isArray(data) && ! _.isEmpty(data) ) { #>
		<table class="table-striped experience_wrap" id="table-striped">
		<thead class="cf">
		  <tr>
			<th><?php pll_e('Experience Title','docdirect');?></th>
			<th><?php pll_e('Company/Organization','docdirect');?></th>
			<th class="numeric"><?php pll_e('Year','docdirect');?></th>
		  </tr>
		</thead>
		<# _.each( data , function( element, index, attr ) { #>
		<tbody class="experiences_item">
		  <tr>
			<td data-title="Code">{{element.title}}
			  <div class="tg-table-hover experience-action"> 
				  <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a> 
				  <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
			  </div>
			</td>
			<td data-title="Company">{{element.company}}</td>
			<td data-title="Price" class="numeric">{{element.start_date_formated}} - <# if(! _.isEmpty(element.end_date) ) { #>{{element.end_date_formated}} <# } else { #><?php pll_e('Current','docdirect');?><# } #></td>
		  </tr>
		  <tr>
		   <td class="experience-data edit-me-row" colspan="3">
			 <div class="experience-data-wrap">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input class="form-control" value="{{element.title}}" name="experience[{{index}}][title]" type="text" placeholder="<?php esc_attr_e('Title','docdirect');?>">
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input class="form-control" value="{{element.company}}" name="experience[{{index}}][company]" type="text" placeholder="<?php esc_attr_e('Company/Organization','docdirect');?>">
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input class="form-control edu_start_date_{{index}}" id="exp_start_date" value="{{element.start_date}}" name="experience[{{index}}][start_date]" type="text" placeholder="<?php esc_attr_e('Start Date','docdirect');?>">
					</div>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input class="form-control exp_end_date_{{index}}" id="exp_end_date" value="{{element.end_date}}" name="experience[{{index}}][end_date]" type="text" placeholder="<?php esc_attr_e('End Date','docdirect');?>">
					</div>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<textarea class="form-control" name="experience[{{index}}][description]" placeholder="<?php esc_attr_e('Experience Description','docdirect');?>">{{element.description}}</textarea>
					</div>
				</div>
			  </div>
			</td>
		  </tr>
		</tbody>
		<# } ); #>
	<# } #>
</script>
<?php
get_footer();
