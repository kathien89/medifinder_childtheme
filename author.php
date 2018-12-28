
<?php
/**
 * The template for displaying user detail
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Doctor Directory
 */
global $wp_query,$current_user;
$current_author_profile = $wp_query->get_queried_object();
do_action('docdirect_update_profile_hits',$current_author_profile->ID); //Update Profile Hits
docdirect_set_user_views($current_author_profile->ID); //Update profile views
get_header();//Include Headers

$avatar = apply_filters(
				'docdirect_get_user_avatar_filter',
				 docdirect_get_user_avatar(array('width'=>365,'height'=>365), $current_author_profile->ID),
				 array('width'=>365,'height'=>365) //size width,height
			);


$banner	= docdirect_get_user_banner(array('width'=>1920,'height'=>450), $current_author_profile->ID);

$current_date 	  = date('Y-m-d H:i:s');
$current_string	= strtotime( $current_date );
$featured_string	= $current_author_profile->user_featured;
$user_gallery	  = $current_author_profile->user_gallery;
$directory_type	= $current_author_profile->directory_type;
$contact_form	  = $current_author_profile->contact_form;
$uni_flag 		  = rand(1,9999);
$enable_login      = '';
$user_profile_specialities = '';
$education_switch  = '';

$facebook	  = isset( $current_author_profile->facebook ) ? $current_author_profile->facebook : '';
$twitter	   = isset( $current_author_profile->twitter ) ? $current_author_profile->twitter : '';
$linkedin	  = isset( $current_author_profile->linkedin ) ? $current_author_profile->linkedin : '';
$pinterest	 = isset( $current_author_profile->pinterest ) ? $current_author_profile->pinterest : '';
$google_plus   = isset( $current_author_profile->google_plus ) ? $current_author_profile->google_plus : '';
$instagram	 = isset( $current_author_profile->instagram ) ? $current_author_profile->instagram : '';
$tumblr	    = isset( $current_author_profile->tumblr ) ? $current_author_profile->tumblr : '';
$skype	  	 = isset( $current_author_profile->skype ) ? $current_author_profile->skype : '';

$schedule_time_format  = isset( $current_author_profile->time_format ) ? $current_author_profile->time_format : '12hour';


if(function_exists('fw_get_db_settings_option')) {
	$enable_login = fw_get_db_settings_option('enable_login', $default_value = null);
	$dir_map_marker    = fw_get_db_post_option($directory_type, 'dir_map_marker', true);
	$education_switch    = fw_get_db_post_option($directory_type, 'education', true);
	$experience_switch    = fw_get_db_post_option($directory_type, 'experience', true);
	$reviews_switch    = fw_get_db_post_option($directory_type, 'reviews', true);
	$user_profile_specialities    = fw_get_db_post_option($directory_type, 'user_profile_specialities', true);
}

if( isset( $dir_map_marker['url'] ) && !empty( $dir_map_marker['url'] ) ){
	$dir_map_marker  = $dir_map_marker['url'];
} else{
	$dir_map_marker  = get_template_directory_uri().'/images/map-marker.png';
}

$privacy		= docdirect_get_privacy_settings($current_author_profile->ID); //Privacy settings

docdirect_enque_map_library();//init Map
docdirect_enque_rating_library();//rating
wp_enqueue_script('intlTelInput');
wp_enqueue_style('intlTelInput');

        $verify_user    = get_user_meta( $current_author_profile->ID, 'verify_user', true);
        $public_profile    = get_user_meta( $current_author_profile->ID, 'public_profile', true);

$apointmentClass	= 'appointment-disabled';
if( !empty( $privacy['appointments'] )
    && 
    $privacy['appointments'] == 'on'
    && 
    $verify_user == 'on'
	&& 
	$public_profile != 'off'
 ) {
	$apointmentClass	= 'appointment-enabled';
	if( function_exists('docdirect_init_stripe_script') ) {
		//Strip Init
		docdirect_init_stripe_script();
	}
	
	if( isset( $current_user->ID ) 
	 && 
		$current_user->ID != $current_author_profile->ID
	){
		$apointmentClass	= 'appointment-enabled';
	} else{
		$apointmentClass	= 'appointment-disabled';
	}
}

$review_data	= kt_docdirect_get_everage_rating ( $current_author_profile->ID );

docdirect_init_dir_map();//init Map
docdirect_enque_map_library();//init Map
$banner_parallax	= '';
if( !empty( $banner ) ){
	$banner_parallax	= 'data-appear-top-offset="600" data-parallax="scroll" data-image-src="'.$banner.'"';
}else {
    $banner_parallax    = 'data-appear-top-offset="600" data-parallax="scroll" data-image-src="'.get_stylesheet_directory_uri().'/images/doctor-banner-default.jpg"';
}
// update_user_meta( 21, 'schedules', '');

    if (isset($_GET["booking_date"])) {
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $today = current_time( 'timestamp' );
        $booking_date    = $_GET["booking_date"];
        $booking_time    = $_GET["booking_time"];
        $time = explode('-',$booking_time);
        $date_bk = date($date_format,strtotime($booking_date));
        $time_bk = date($time_format,strtotime('2016-01-01 '.$time[0]) );
        $date_time_booking = $date_bk.' '.$time_bk;
        if ($today <= strtotime($date_time_booking) ) {
            function kt_add_code_footer() {
                echo '<input class="quickbooking booking_date" type="hidden" name="booking_date" value="'.$_GET["booking_date"].'">';
                echo '<input class="quickbooking booking_time" type="hidden" name="booking_time" value="'.$_GET["booking_time"].'">';

                if(is_user_logged_in()){?>
                    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri();?>/js/custom_1.js"></script>
                    <script type="text/javascript">
                        jQuery(document).ready(function($){
                            // jQuery('.tg-appointmentpopup').modal('toggle');
                        });
                    </script>
                <?php }else {
                ?>
                    <script type="text/javascript" src="<?php echo get_stylesheet_directory_uri();?>/js/custom_2.js"></script>
                    <script type="text/javascript">
                        jQuery(document).ready(function($){
                            // jQuery('.tg-user-modal').modal('show');
                        });
                    </script>
                <?php
                }
            }
            add_action('wp_footer', 'kt_add_code_footer', 100);
        }
    }
?>
<?php
    if (isset($_GET["token"]) && $_GET["token"] != '') {
        
        $token = $_GET["token"];//Returned by paypal, you can save this in SESSION too
        $requestParams = array('TOKEN' => $token);
        $paypal = new wp_paypal_gateway (true);

        $paypal_username    = get_user_meta( $current_author_profile->ID, 'paypal_username', true);
        $paypal_password    = get_user_meta( $current_author_profile->ID, 'paypal_password', true);
        $paypal_signature   = get_user_meta( $current_author_profile->ID, 'paypal_signature', true);
        $paypal->setVarApi($paypal_username, $paypal_password, $paypal_signature);

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
        $post_id = $CUSTOM['post_id'];
        if(is_array($response) && $response["ACK"]=="Success"){

            $price = get_post_meta($post_id, 'bk_paid_amount', true);
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
                update_post_meta($post_id, 'bk_code', $TRANSACTIONID);
                update_post_meta($post_id, 'bk_transaction_status', 'approved');
                echo 'Success';
            }else {
                echo $response['L_SHORTMESSAGE0'];
                update_post_meta($post_id, 'bk_transaction_status', 'cancelled');
            }
        }else {
            echo $response['L_SHORTMESSAGE0'];
        }
    }?>
<?php
    $db_directory_type   = get_user_meta( $current_author_profile->ID, 'directory_type', true);
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
        $current_option = get_option( 'company_'.$user_premium, true );
    }else {
        $current_option = get_option( $user_premium, true );
    }
?>
<div id="tg-userbanner" class="tg-userbanner tg-haslayout parallax-window" <?php echo ($banner_parallax);?>>
	<div class="container">
    	<div class="row">
        <div class="col-sm-12 col-xs-12">
        	<div class="tg-userbanner-content">
                <h1><?php echo kt_get_title_name($current_author_profile->ID).esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?></h1>
                <?php if( !empty( $current_author_profile->tagline ) ) {?>
                <span><?php echo esc_attr( $current_author_profile->tagline );?></span>
                <?php }?>
                <ul class="tg-likestars">
                    <li><?php docdirect_get_rating_stars($review_data,'echo');?></li>
                    <li><?php docdirect_get_wishlist_button($current_author_profile->ID,true);?></li>
                    <li><span><?php echo intval( docdirect_get_user_views($current_author_profile->ID) );?>&nbsp;<?php pll_e('view(s)');?></span></li> 
                    <li><?php docdirect_get_likes_button($current_author_profile->ID);?></li>
                </ul>
                <?php 
				 if( !empty( $privacy['appointments'] )
              && 
              $privacy['appointments'] == 'on'
                && 
                $verify_user == 'on'
                && 
                $public_profile != 'off'
                && 
                isset($current_option['patient_bookings'])
         )  {
					 if( isset( $current_user->ID ) 
						 && 
							$current_user->ID != $current_author_profile->ID
						 &&
							is_user_logged_in()
					 ){
					?>
						<button class="tg-btn tg-btn-lg make-appointment-btn" type="button" data-toggle="modal" data-target=".tg-appointmentpopup"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
                    <?php }else if($current_user->ID == $current_author_profile->ID) {?>
                        <button class="tg-btn tg-btn-lg make-appointment-btn yourself" type="button"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
					<?php 
					}  else if( $current_user->ID != $current_author_profile->ID ){?>
						<button class="tg-btn tg-btn-lg make-appointment-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
				<?php }}?>
                <?php
                     if( isset( $current_user->ID ) 
                         && 
                            $current_user->ID != $current_author_profile->ID
                         &&
                            is_user_logged_in()
                     ){?>
                        <button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-request-only"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }else if($current_user->ID == $current_author_profile->ID) {?>
                        <button class="tg-btn tg-btn-lg request-btn yourself" type="button"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }else {?>
                        <button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }?>
            </div>
          </div>
        </div>
    </div>
</div>
<div class="container">
  <div class="row">
    <div class="tg-userdetail <?php echo sanitize_html_class( $apointmentClass );?>">
      <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
        <aside id="tg-sidebar" class="tg-sidebar">
          <div class="tg-widget tg-widgetuserdetail">
            <figure class="tg-userimg"> 
            	<img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?>">
              <figcaption>
                <ul class="tg-featureverified">
                  <?php docdirect_get_verified_tag(true,$current_author_profile->ID,'simple');?>                           
                  <?php kt_get_tag_company($current_author_profile->ID,true);?>
                </ul>
              </figcaption>
            </figure>            
            <div class="tg-userratingtinfo">
                <ul class="tg-doccontactinfo">
                    <li>
                        <strong>Fee (Consultation): </strong><br><span>$<?php echo get_user_meta($current_author_profile->ID,'price_min',true); ?></span>
                    </li>
                    
                    <li>
                        <strong>Recommendation</strong><div class="clearfix"></div>
                        <?php $review_data_recommendation = kt_docdirect_get_everage_rating ( $current_author_profile->ID, 'recommendation' );?>
                        <?php docdirect_get_rating_stars($review_data_recommendation,'echo',false);?>
                        <?php $round_var = ceil($review_data_recommendation['average_rating']);?>
                        <span id="recommendation_span" class="name_rating"></span>
                        <script type="text/javascript">
                            jQuery(function () {
                                jQuery('#recommendation_span').html(rating_vars.recommendation.rating_<?php echo $round_var?>);
                            });
                        </script>
                    </li>
                    <li>
                        <strong>Bedside Manner</strong><div class="clearfix"></div>
                        <?php $review_data_bedside_manner = kt_docdirect_get_everage_rating ( $current_author_profile->ID, 'bedside_manner' );?>
                        <?php docdirect_get_rating_stars($review_data_bedside_manner,'echo',false);?>
                        <?php $round_var = ceil($review_data_bedside_manner['average_rating']);?>
                        <span id="bedside_manner_span" class="name_rating"></span>
                        <script type="text/javascript">
                            jQuery(function () {
                                jQuery('#bedside_manner_span').html(rating_vars.bedside_manner.rating_<?php echo $round_var?>);
                            });
                        </script>
                    </li>
                    <li>
                        <strong>Waiting Time</strong><div class="clearfix"></div>
                        <?php $review_data_waiting_time = kt_docdirect_get_everage_rating ( $current_author_profile->ID, 'waiting_time' );?>
                        <?php docdirect_get_rating_stars($review_data_waiting_time,'echo',false);?>
                        <span id="waiting_time_span" class="name_rating"></span>
                        <script type="text/javascript">
                            jQuery(function () {
                                jQuery('#waiting_time_span').html(rating_vars.waiting_time.rating_<?php echo $round_var?>);
                            });
                        </script>
                    </li>
                    <li>
                        <strong>Supporting Staff</strong><div class="clearfix"></div>
                        <?php $review_data_supporting_staff = kt_docdirect_get_everage_rating ( $current_author_profile->ID, 'supporting_staff' );?>
                        <?php docdirect_get_rating_stars($review_data_supporting_staff,'echo',false);?>
                        <span id="supporting_staff_span" class="name_rating"></span>
                        <script type="text/javascript">
                            jQuery(function () {
                                jQuery('#supporting_staff_span').html(rating_vars.supporting_staff.rating_<?php echo $round_var?>);
                            });
                        </script>
                    </li>
                    <li>
                        <strong>Facilities</strong><div class="clearfix"></div>
                        <?php $review_data_facilities   = kt_docdirect_get_everage_rating ( $current_author_profile->ID, 'facilities' );?>
                        <?php docdirect_get_rating_stars($review_data_facilities,'echo',false);?>
                        <span id="facilities_span" class="name_rating"></span>
                        <script type="text/javascript">
                            jQuery(function () {
                                jQuery('#facilities_span').html(rating_vars.facilities.rating_<?php echo $round_var?>);
                            });
                        </script>
                    </li>
                </ul>
            </div>
            <div class="tg-usercontactinfo">
                <?php get_doctor_locations($current_author_profile->ID);?>
              
              <?php /*?><div class="tg-userpubliclink">
                <h3><?php pll_e('Public Profile URL');?></h3>
                <a target="_blank" href="<?php echo get_author_posts_url($current_author_profile->ID); ?>"><i class="fa fa-link"></i>
                <em>
					<?php 
                        $public_url = str_replace( set_url_scheme( home_url(), 'http' ), set_url_scheme( home_url(), 'relative' ), get_author_posts_url($current_author_profile->ID));
						echo esc_attr($public_url);
                     ?>
                 </em>
                 </a> 
              </div><?php */?>
              <?php kt_docdirect_prepare_profile_social_sharing($avatar,$current_author_profile->ID,$current_author_profile->desc_hk);?>
              <?php 
					if( !empty( $privacy['contact_form'] )
					  && 
						$privacy['contact_form'] == 'on'
					) {
			   ?>
              <div class="tg-usercontatnow">
                <h3><?php pll_e('contact now');?></h3>
                <div class="tg-widgetcontent doc-contact">
                    <form class="contact_form tg-usercontactform">
                        <fieldset>
                            <div class="row">
                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <input type="text" name="username" placeholder="<?php pll_e('Name');?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <input type="email" name="useremail" placeholder="<?php pll_e('Email');?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <input type="text" name="userphone" placeholder="<?php pll_e('Number');?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <input type="text" name="usersubject" placeholder="<?php pll_e('Subject');?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <textarea name="user_description" placeholder="<?php pll_e('Message');?>" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <input type="hidden" name="email_to" value="<?php echo esc_attr( $current_author_profile->user_email );?>" class="form-control">
                                    <?php wp_nonce_field('docdirect_contact_me', 'user_security'); ?>
                                    <?php if(!is_user_logged_in()){?>
                                        <a class="tg-btn" data-toggle="modal" data-target=".tg-user-modal" href="javascript:;">
                                            <?php pll_e('Send');?>
                                        </a>
                                    <?php }else{?>
                                        <button class="tg-btn contact_me" type="submit"><?php pll_e('Send');?></button>
                                    <?php }?>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
              </div>
              <?php }?>
            </div>
          </div>
          <?php 
        if( isset( $current_user->ID ) 
			&& 
				$current_user->ID != $current_author_profile->ID
			&&
				is_user_logged_in()
        
        ){
        ?>
        <div class="claim-box tg-widget tg-claimreport">
            <div class="tg-widgetcontent doc-claim">
                <h3><?php pll_e('Claim/Report This User');?></h3>
                <form class="tg-haslayout claim_form tg-claimform">
                    <fieldset>
                        <div class="form-group">
                            <input type="text" name="subject" placeholder="<?php pll_e('Subject*');?>" class="form-control">
                        </div>
                        <div class="form-group">
                            <textarea name="report" placeholder="<?php pll_e('Report Detail');?>" class="form-control"></textarea>
                        </div>
                        <button class="tg-btn report_now" type="submit"><?php pll_e('report now');?></button>
                        <?php wp_nonce_field('docdirect_claim', 'security'); ?>
                        <input type="hidden" name="user_to" class="user_to" value="<?php echo esc_attr( $current_author_profile->ID );?>" />
                    </fieldset>
                </form>
            </div>
        </div>
        <?php } else if( $current_user->ID != $current_author_profile->ID ){?>
            <div class="claim-box">
                <a class="tg-btn tg-btn-lg"data-toggle="modal" data-target=".tg-user-modal" href="javascript:;">
                    <i class="fa fa-exclamation-triangle"></i>
                    <?php pll_e('Claim This User');?>
                </a>
            </div>
        <?php }?>
                <div class="clearfix"></div>
          <?php if (is_active_sidebar('user-page-sidebar')) {?>
              <div class="tg-doctors-list tg-haslayout">
                <?php dynamic_sidebar('user-page-sidebar'); ?>
              </div>
           <?php }?>
        </aside>
      </div>
      <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
        <div class="tg-haslayout">
          <div class="tg-userbanner-content">
                <h1><?php echo kt_get_title_name($current_author_profile->ID).esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?></h1>
                <?php if( !empty( $current_author_profile->tagline ) ) {?>
                <span><?php echo esc_attr( $current_author_profile->tagline );?></span>
                <?php }?>
                <ul class="tg-likestars">
                    <li><?php docdirect_get_rating_stars($review_data,'echo');?></li>
                    <li><?php docdirect_get_wishlist_button($current_author_profile->ID,true);?></li>
                    <li><span><?php echo intval( docdirect_get_user_views($current_author_profile->ID) );?>&nbsp;<?php pll_e('view(s)');?></span></li> 
                    <li><?php docdirect_get_likes_button($current_author_profile->ID);?></li>
                </ul>
                <?php 
				 if( !empty( $privacy['appointments'] )
              && 
              $privacy['appointments'] == 'on'
                && 
                $verify_user == 'on'
                && 
                $public_profile != 'off'
                && 
                isset($current_option['patient_bookings'])
         )  {
					 if( isset( $current_user->ID )  && $current_user->ID != $current_author_profile->ID
						 &&
							is_user_logged_in()
					 ){
					?>
						<button class="tg-btn tg-btn-lg make-appointment-btn" type="button" data-toggle="modal" data-target=".tg-appointmentpopup"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
                    <?php }else if($current_user->ID == $current_author_profile->ID) {?>
                        <button class="tg-btn tg-btn-lg make-appointment-btn yourself" type="button"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
					<?php 
					}  else if( $current_user->ID != $current_author_profile->ID ){?>
						<button class="tg-btn tg-btn-lg make-appointment-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
                <?php }}?>
                <?php
                     if( isset( $current_user->ID ) && $current_user->ID != $current_author_profile->ID
                         &&
                            is_user_logged_in()
                     ){?>
                        <button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-request-only"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }else if($current_user->ID == $current_author_profile->ID) {?>
                        <button class="tg-btn tg-btn-lg request-btn yourself" type="button"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }else if(!is_user_logged_in()) {?>
                        <button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }?>
            </div>

          <div class="tg-section-map">
            <?php
                $current_info = kt_get_current_active_info_doctor($current_author_profile->ID);
                if ($current_info) {
                    $basics = $current_info['basics'];
                  $latitude    = $basics['latitude'];
                  $longitude    = $basics['longitude'];
                }
            ?>
          <?php if( !empty( $latitude ) && !empty( $longitude ) ) {?>
          <div id="map_canvas" class="tg-location-map tg-haslayout"></div>
          <?php do_action('docdirect_map_controls');?>
          <?php
		  	$directories	= array();
			$directories_array	= array();
		   	$directories['status']	= 'found';
			$directories_array['latitude']	= $latitude;
			$directories_array['longitude']	= $longitude;
			$directories_array['title']	= $current_author_profile->display_name;
			$directories_array['name']	 = kt_get_title_name($current_author_profile->ID).$current_author_profile->first_name.' '.$current_author_profile->last_name;
			$directories_array['email']	 = $current_author_profile->user_email;
			$directories_array['phone_number']	 = $current_author_profile->phone_number;
			$directories_array['address']	 = $current_author_profile->address;
			$directories_array['group']	= '';
			$directories_array['icon']	 	   = $dir_map_marker;
			$avatar = apply_filters(
										'docdirect_get_user_avatar_filter',
										 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $current_author_profile->ID),
										 array('width'=>150,'height'=>150) //size width,height
									);
			
			$infoBox	= '<div class="tg-mapmarker">';
			$infoBox	.= '<figure><img width="60" heigt="60" src="'.esc_url( $avatar ).'" alt="'.esc_attr__('User').'"></figure>';
			$infoBox	.= '<div class="tg-mapmarkercontent">';
			$infoBox	.= '<h3><a href="'.get_author_posts_url($current_author_profile->ID).'">'.$directories_array['name'].'</a></h3>';
			if( !empty( $current_author_profile->tagline ) ) {
				$infoBox	.= '<span>'.$current_author_profile->tagline.'</span>';
			}
			$infoBox	.= '<ul class="tg-likestars">';
			
			if( !empty( $directories_array['address'] ) ) {
				$infoBox	.= '<li>'.docdirect_get_rating_stars($review_data,'return','hide').'</li>';
			}
			$infoBox	.= '<li>'.docdirect_get_wishlist_button($current_author_profile->ID,false).'</li>';
			$infoBox	.= '<li>'.docdirect_get_user_views($current_author_profile->ID).'&nbsp;'.esc_html__('view(s)').'</li>';
			
			$infoBox	.= '</ul>';
			$infoBox	.= '</div>';
																
			$directories_array['html']['content']	= $infoBox;
			$directories['users_list'][]	= $directories_array;
		   ?>
           <script>
			jQuery(document).ready(function() {
				kt_docdirect_init_detail_map_script(<?php echo json_encode( $directories );?>);
			});
		  </script>
          </div> 
          <?php }?>

          <?php if (is_active_sidebar('user-page-top')) {?>
              <div class="tg-doctors-list tg-haslayout user-ad-top">
                <?php dynamic_sidebar('user-page-top'); ?>
              </div>
          <?php }?>
          <div class="tg-aboutuser">
            <div class="tg-userheading">
              <h2><?php pll_e('About');?>&nbsp;<?php echo esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?></h2>
              
            </div>
			<?php if( !empty( $current_author_profile->desc ) ) {?>
              <div class="tg-description">               
                    <div id="aboutab"> 
                        <ul  class="nav nav-tabs">
                            <?php $plugins_url = plugins_url();
                                $flag_url =  $plugins_url.'/polylang/flags/';
                            ?>
                            <li class="active">
                                <a  href="#English" data-toggle="tab">English <img src="<?php echo $flag_url;?>gb.png" /></a>
                            </li>
                            <?php 
                                if (!empty($current_author_profile->desc_hk)) {
                                    ?>
                                    <li>
                                        <a href="#Traditional" data-toggle="tab">中文 (香港) <img src="<?php echo $flag_url;?>hk.png" /></a>
                                    </li>
                                    <?php
                                }
                            ?>
                            <?php 
                                if (!empty($current_author_profile->desc_cn)) {
                                    ?>
                                    <li>
                                        <a href="#Simplified" data-toggle="tab">中文 (中国) <img src="<?php echo $flag_url;?>cn.png" /></a>
                                    </li>
                                    <?php
                                }
                            ?>
                            <?php 
                                if (!empty($current_author_profile->desc_fr)) {
                                    ?>
                                    <li>
                                        <a href="#French" data-toggle="tab">Français <img src="<?php echo $flag_url;?>fr.png" /></a>
                                    </li>
                                    <?php
                                }
                            ?>
                        </ul>

                        <div class="tab-content clearfix">
                            <div class="tab-pane active" id="English">
                                <div class="desc">
                                    <p></p>
                                    <?php 
                                        echo wpautop( $current_author_profile->desc );
                                    ?>
                                </div>
                            </div>
                            <?php 
                                if (!empty($current_author_profile->desc_hk)) {
                                    ?>
                                    <div class="tab-pane" id="Traditional">
                                        <div class="desc">
                                            <p></p>                          
                                            <?php echo wpautop( $current_author_profile->desc_hk );?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>
                            <?php 
                                if (!empty($current_author_profile->desc_cn)) {
                                    ?>                                    
                                    <div class="tab-pane" id="Simplified">
                                        <div class="desc">
                                            <p></p>
                                            <?php echo wpautop( $current_author_profile->desc_cn );?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>
                            <?php 
                                if (!empty($current_author_profile->desc_fr)) {
                                    ?>
                                    <div class="tab-pane" id="French">
                                        <div class="desc">
                                            <p></p> 
                                            <?php echo wpautop( $current_author_profile->desc_fr );?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
              </div>
            <?php }?>
                <?php //echo do_shortcode('[ssba]') ?>
                <?php echo do_shortcode('[addtoany]') ?>
          </div>

          </div>
    <?php if( in_array('company', $list_terms) ||
            in_array('medical-centre', $list_terms) ||
            in_array('hospital-type', $list_terms) ||
            in_array('scans-testing', $list_terms)
    ) {  ?>  
          <?php if( isset($current_option['affiliations']) ){?>
          <div class="tg-affiliation tg-listview-v3 user-section-style">
            <div class="tg-userheading">
              <h2><i class="fa fa-group"></i><?php pll_e('Affiliation');?></h2>
            </div>
            
        <?php
          $title_aff_myplacework = get_user_meta($current_author_profile->ID, 'aff_myplacework', true);
          $title_aff_myplacework = ($title_aff_myplacework == '') ? pll__('My Place of work') : $title_aff_myplacework ;
          $title_aff_myteam = get_user_meta($current_author_profile->ID, 'aff_myteam', true);
          $title_aff_myteam = ($title_aff_myteam == '') ? pll__('My Team') : $title_aff_myteam ;
          $title_aff_other = get_user_meta($current_author_profile->ID, 'aff_other', true);
          $title_aff_other = ($title_aff_other == '') ? pll__('Other Connections') : $title_aff_other ;
            
            $arr_group = array(
                array(
                    'slug' => 'aff_myplacework',
                    'name' => $title_aff_myplacework,
                    'icon' => 'fa-hospital-o',
                ),
                array(
                    'slug' => 'aff_myteam',
                    'name' => $title_aff_myteam,
                    'icon' => 'fa-users',
                ),
                array(
                    'slug' => 'aff_other',
                    'name' => $title_aff_other,
                    'icon' => 'fa-link',
                )
            );
        foreach ($arr_group as $group) {
            $ppp = 6;
            $apply_affiliation1 = get_apply_affiliation_by_user($current_author_profile->ID,$group['slug']);
            $count = count($apply_affiliation1);
            $apply_affiliation = array_slice($apply_affiliation1, 0, $ppp, true);
        ?>
        <?php if($count > 0){?>
        <div class="aff_group">
            <input type="hidden" name="array" value=<?php echo json_encode($apply_affiliation1,JSON_FORCE_OBJECT);?>>
            <input type="hidden" name="posts_per_page" value="<?php echo $ppp;?>">
            <div class="tg-otherphotos1">
            <div class="tg-heading-border tg-small">      
                <h4>
                    <i class="fa <?php echo $group['icon'];?>"></i>
                    <span><?php echo $group['name'];?>  </span>                  
                </h4>
            </div>
            <div class="list_user">
                <div class="row">
                    <?php
                    if (!empty($apply_affiliation)) {
                        foreach ($apply_affiliation as $user_id => $post_id) {
                            $type_aff = get_post_meta($post_id, 'type_aff', true);
                            $user_from = get_post_meta($post_id, 'user_from', true);
                            $user_to = get_post_meta($post_id, 'user_to', true);
                            if ($type_aff == 'in_db') {
                                // $user_id = $user_to; 
                                $user = get_userdata($user_id);
                                $review_data    = kt_docdirect_get_everage_rating ( $user->ID );
                                $name = kt_get_title_name($user_id).$user->first_name.' '.$user->last_name;
                                $thumbnail = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                                $link = get_author_posts_url($user->ID);
                                $tagline = get_user_meta($user->ID, 'tagline', true);
                                $specialities = get_user_meta($user->ID,'user_profile_specialities',true);
                                if (!empty($specialities)) {
                                    $aloha = array_slice($specialities, 0, 4);
                                    $specialities_val = implode(', ', array_values($aloha));
                                }
                                $rating = docdirect_get_rating_stars($review_data,'return', 'hide');
                                $data_modal = '';
                                $cl = '';
                            }else {
                                $user_id = $user_from;
                                $width = '150';
                                $height = '150';
                                $thumb_id = get_post_thumbnail_id($post_id);
                                $thumb_url = wp_get_attachment_image_src($thumb_id, array($width, $height), true);
                                $thumbnail  = $thumb_url[0];
                                $link = 'javascript:;';
                                $name = get_the_title($post_id);
                                $tagline = get_post_meta($post_id, 'tagline', true);
                                $specialities_val = get_post_meta($post_id, 'specialties', true);
                                $rating = '';
                                if ($current_author_profile->ID == $current_user->ID) {
                                    $email = get_post_meta($post_id, 'email', true);
                                    $data_modal = 'data-email="'.$email.'"';
                                    $cl = 'hasmodal';
                                }
                            }
                            if((user_id_exists($user_id) && $user_id != $user_identity && $type_aff == 'in_db') || 
                                ($type_aff == 'out_db') ){
                            // var_dump($user_id);
                            ?>
                            <article id="user-<?php echo intval( $user->ID );?>" data-post_id="<?php echo intval( $post_id );?>" class="col-sm-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <a href="<?php echo $link;?>" <?php echo $data_modal;?> class="list-avatar <?php echo $cl;?>">                                        
                                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>">
                                            <?php
                                            if (get_post_meta($post_id, 'type_aff', true) == 'in_db') {
                                                echo '<span class="member_tag member">'.pll__('Member').'</span>';
                                            }else {
                                                if ($current_author_profile->ID == $current_user->ID) {
                                                    echo '<span class="member_tag invite_tag">'.pll__('Invite').'</span>';
                                                }else {
                                                    echo '<span class="member_tag listing_only">'.pll__('Listing Only').'</span>';
                                                }
                                            }
                                            ?>
                                        </a>
                                    </div>
                                    <div class="col-sm-8">
                                      
                                      <div class="tg-small">
                                        <h4><a href="<?php echo $link; ?>"><?php echo $name;?></a></h4>
                                      </div>
                                      <?php if( !empty( $tagline ) ){?>
                                          <div class="tg-tagline">
                                            <h5><?php echo $tagline;?></h5>
                                          </div>
                                      <?php }?>
                                      <?php if( !empty( $specialities_val ) ){?>
                                          <div class="tg-specialities">
                                            <p><strong><?php pll_e('Specialities: ');?></strong>
                                                <?php
                                                  echo $specialities_val;
                                                ?>
                                            </p>
                                          </div>
                                      <?php }?>
                                      <?php echo $rating;?>
                                    </div>
                                </div>             
                            </article>
                            <?php
                            }
                        }
                    }
                    ?>
                </div>
                <?php if($count > $ppp){?>
                    <div class="col-xs-12 text-center">
                        <a class="load_more btn btn-primary" data-group="<?php echo $group['slug'];?>" href="javascript:;"><?php pll_e('Load more');?></a>
                    </div>
                <?php }?>
            </div>
            </div>
        </div>
        <?php }?>
        <?php }?>
          </div>
         <?php }?>  
    <?php }?>    

        <?php 
         if( !empty( $privacy['appointments'] )
              && 
              $privacy['appointments'] == 'on'
                && 
                $verify_user == 'on'
                && 
                $public_profile != 'off'
                && 
                isset($current_option['patient_bookings'])
         ) {
            // if( $current_user->ID != $current_author_profile->ID ) {
        ?>
          <div class="tg-online-booking tg-listview-v3 user-section-style">
            <div class="tg-userheading">
              <h2><i class="fa fa-book"></i><?php pll_e('Online Booking');?></h2>
            </div>
            <div class="tg-doctor-profile online-booking" data-id="<?php echo esc_attr( $current_author_profile->ID );?>">
                <?php kt_docdirect_get_booking_step_one($current_author_profile->ID,'echo');?>
                <?php kt_docdirect_get_booking_step_two_calender($current_author_profile->ID,'echo',$_GET["booking_date"]);?>
                <?php
                 if( isset( $current_user->ID ) 
                     /*&& 
                        $current_user->ID != $current_author_profile->ID*/
                     &&
                        is_user_logged_in()
                 ){?>
                <button type="button" class="tg-btn submit_bookingonline"><?php pll_e('Submit');?></button>
                <?php }else {?>
                    <button class="tg-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><?php pll_e('Submit');?></button>
                <?php }?>
            </div>
          </div>
          <?php }//}?>
          
          <!--Prices List-->
          <?php 
          if( !empty( $current_author_profile->prices_list ) ){?>
            <div class="prices-list-wrap tg-section-price_list">
                <div class="tg-companyfeaturebox tg-services">
                  <div class="tg-userheading">
                    <h2><i class="fa fa-money" aria-hidden="true"></i><?php esc_html_e('Prices/Services List','docdirect');?></h2>
                  </div>
                  <div id="tg-accordion" class="tg-accordion">
                    <?php 
                    foreach( $current_author_profile->prices_list as $key => $value ){
                        if( !empty( $value['title'] ) ){
                        ?>
                        <div class="tg-service tg-panel">
                          <div class="tg-accordionheading">
                            <h4><span><?php echo esc_attr( $value['title'] );?></span><span><?php echo esc_attr( $value['price'] );?></span></h4>
                          </div>
                          <div class="tg-panelcontent" style="display: none;">
                            <div class="tg-description">
                              <p><?php echo esc_attr( $value['description'] );?></p>
                            </div>
                          </div>
                        </div>
                    <?php }}?>
                  </div>
                </div>
            </div>  
          <?php }?>

          <?php if( isset( $user_gallery ) && !empty( $user_gallery ) && isset($current_option['photo_gallery']) ){?>
          <div class="tg-userphotogallery user-section-style">
            <div class="tg-userheading">
              <h2><i class="fa fa-image"></i><?php pll_e('Photo Gallery');?></h2>
            </div>
            <ul>
              <?php 
              foreach( $user_gallery as $key => $value ){
                  $thumbnail    = docdirect_get_image_source($value['id'],150,150);
                  $orignal    = docdirect_get_image_source($value['id'],0,0);
                  if( !empty( $thumbnail ) ){
                ?>
                <li>
                    <figure>
                       <a href="<?php echo esc_url( $orignal );?>"><img src="<?php echo esc_url( $thumbnail );?>" alt="<?php echo esc_attr( get_the_title( $value['id'] ) );?>">
                        <figcaption><span class="icon-add"></span></figcaption>
                       </a>
                    </figure>
                </li>
              <?php }}?>
              <?php
                if (count($user_gallery) > 5) {
                    echo '<div class="more_button"><a href="javascript:;"><i class="fa fa-plus"></i>
                                    <span class="close_filters">'.pll__("Less",'docdirect').'</span>
                                    <span class="more_filters">'.pll__("More",'docdirect').'</span></a></div>';
                }
              ?>
            </ul>
          </div>
          <?php }?>
          <?php if( isset( $current_author_profile->video_url ) && !empty( $current_author_profile->video_url ) && isset($current_option['video_gallery']) && $current_author_profile->video_url[0] != '' ) {?>
          <div class="tg-presentationvideo user-section-style">
            <div class="tg-userheading">
              <h2><i class="fa fa-youtube-play"></i><?php pll_e('Presentation Video');?></h2>
            </div>
            <?php
                $max_video = intval($current_option['video_number']);

                if( !empty( $current_author_profile->video_url ) ) {
                    $mmm = array_slice($current_author_profile->video_url, 0, $max_video);
                    echo '<div class="row">';
                    foreach( $mmm as $key => $value ){
                        $height = 200;
                        $width  = 368;
                        $post_video = $value;
                        $url = parse_url( $post_video );
                        if ($url['host'] == $_SERVER["SERVER_NAME"]) {
                            echo '<div class="video col-sm-6 col-md-4">';
                            echo do_shortcode('[video width="' . $width . '" height="' . $height . '" src="' . $post_video . '"][/video]');
                            echo '</div>';
                        } else {

                            if ($url['host'] == 'vimeo.com' || $url['host'] == 'player.vimeo.com') {
                                echo '<div class="video col-sm-6 col-md-4">';
                                $content_exp = explode("/", $post_video);
                                $content_vimo = array_pop($content_exp);
                                echo '<iframe allowfullscreen width="' . $width . '" height="' . $height . '" src="https://player.vimeo.com/video/' . $content_vimo . '" 
        ></iframe>';
                                echo '</div>';
                            } elseif ($url['host'] == 'soundcloud.com') {
                                $video = wp_oembed_get($post_video, array('height' => $height));
                                $search = array('webkitallowfullscreen', 'mozallowfullscreen', 'frameborder="no"', 'scrolling="no"');
                                echo '<div class="audio col-sm-6 col-md-4">';
                                $video = str_replace($search, '', $video);
                                echo str_replace('&', '&amp;', $video);
                                echo '</div>';
                            } elseif ($url['host'] == 'youtu.be') {
                                $video = wp_oembed_get($post_video, array('height' => $height));
                                $search = array('webkitallowfullscreen', 'mozallowfullscreen', 'frameborder="no"', 'scrolling="no"');
                                echo '<div class="video col-sm-6 col-md-4">';
                                $video = str_replace($search, '', $video);
                                echo str_replace('&', '&amp;', $video);
                                echo '</div>';
                            } else {
                                echo '<div class="video col-sm-6 col-md-4">';
                                $content = str_replace(array('watch?v=', 'http://www.dailymotion.com/'), array('embed/', '//www.dailymotion.com/embed/'), $post_video);
                                echo '<iframe width="' . $width . '" height="' . $height . '" src="' . $content . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                                echo '</div>';
                            }
                        }
                }
                if (count($mmm) > 3) {
                    echo '<div class="more_button"><a href="javascript:;"><i class="fa fa-plus"></i>
                                    <span class="close_filters">'.pll__("Less",'docdirect').'</span>
                                    <span class="more_filters">'.pll__("More",'docdirect').'</span></a></div>';
                }
                echo '</div>';
            }
            ?>
          </div>
          <?php }?>
          
          <?php if( isset($current_option['articles']) ){
            $args = array(
                'post_type' => 'sp_articles',
                'orderby' => 'ID',
                // 'posts_per_page' => -1,
                'author' => $current_author_profile->ID,
            );
            $ListPost = get_posts($args);
            if (!empty($ListPost)) {?>
          <div class="tg-honourawards tg-listview-v3 user-section-style user-artiles">
            <div class="tg-userheading">
              <h2><i class="fa fa-file-text"></i><?php echo esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?> <?php pll_e('Recent Articles');?></h2>
              <?php
                $permalink = add_query_arg( 
                        array(
                            'doctor'=>  $current_author_profile->user_login ,
                            ), esc_url( get_permalink(get_page_by_path( 'archive-article' )) 
                        ) 
                    );?>
              <a class="articles_link" href="<?php echo $permalink; ?>"><?php echo esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?> | <?php pll_e('Articles Archive');?></a>
            </div>
            <div class="tg-doctor-profile">
                    <?php
                            // echo '<div class="row">';
                            echo '<div class="doc-blogpostslider doc-blogpost  owl-carousel">';
                            $query = new WP_Query($args);
                            while ($query->have_posts()) : $query->the_post();
                                $width = '370';
                                $height = '200';
                                $thumbnail  = docdirect_prepare_thumbnail($post->ID ,$width,$height);
                                $user_ID = get_the_author_meta('ID');
                                
                                $userprofile_media = get_the_author_meta('userprofile_media', $user_ID);
                                
                                if( !empty( $user_ID ) ){
                                    $userprofile_media = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_ID),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                                }
                                
                                if (isset($thumbnail) && $thumbnail) {
                                    $thumbnail = $thumbnail;
                                } else {
                                    $thumbnail = get_stylesheet_directory_uri() . '/images/default-blog-image.jpg';
                                }
                                ?>
                                <article class="item tg-post doc-post">
                                    <div class="tg-box">
                                        <div class="tg-contentbox">
                                            <div class="tg-displaytable1">
                                                <div class="tg-displaytablecell1">
                                                    <div class="tg-heading-border tg-small">
                                                        <?php 
                                                            $length = mb_strlen( get_the_title(), 'utf-8' );
                                                            if ( $length > 79 ) {
                                                                $newTitle = substr( get_the_title(), 0, 80 ).' ...';
                                                            } else {
                                                                $newTitle = get_the_title();
                                                            }
                                                            
                                                        ?>
                                                        <h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo $newTitle; ?> </a></h3>
                                                    </div>
                                                    <div class="tg-description">
                                                        <?php docdirect_prepare_excerpt(120 ,'false',''); ?>
                                                    </div>
                                                    <?php kt_add_post_author($post->ID);?>
                                                </div>
                                                <a href="<?php echo esc_url( get_the_permalink() ); ?>"><span class="tg-show"><em class="icon-add"></em></span></a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                            echo '</div>';
                            // echo '</div>';  
                    ?>
            </div>
            <script>
                jQuery(document).ready(function(e) {
                    jQuery(".user-artiles .doc-blogpostslider").owlCarousel({
                        autoPlay: true,
                        slideSpeed : 300,
                        pagination: false,
                        paginationSpeed : 400,
                        items:3,
                        navigation : false,
                        navigationText : ['<i class="doc-btnprev icon-arrows-1"></i>','<i class="doc-btnnext icon-arrows"></i>'],
                        responsive:{
                            0:{items:1},
                            600:{items:2},
                            768:{items:1},
                            991:{items:2},
                            1200:{items:3}
                        }
                    });
                });
            </script>
           </div>
            <?php }?>
          <?php }?>

          <div class="tg-honourawards tg-listview-v3 user-section-style">
            <div class="tg-userheading">
              <h2><i class="fa fa-commenting"></i><?php pll_e('Languages');?></h2>
            </div>
            <div class="tg-doctor-profile">
                  <ul class="tg-tags">
					<?php 
                    if( !empty( $current_author_profile->languages ) ) {
                        $languages	= docdirect_prepare_languages();
                        $user_languages	 = array();
                        foreach( $current_author_profile->languages as $key => $value ){
                        ?>
                    <li><a href="javascript:;" class="tg-btn"><?php echo esc_attr( $languages[$key] );?></a></li>
                    <?php }} else{?>
                     <li><a href="javascript:;" class="tg-btn"><?php pll_e( 'No Languages selected yet.' );?></a></li>
                    <?php }?>
                  </ul>
              </div>
          </div>
          
          <div class="tg_specialities tg-listview-v3 user-section-style">
            <div class="tg-userheading">
              <h2><i class="fa fa-user-md"></i><?php pll_e('Specialties');?></h2>
            </div>
            <div class="tg-doctor-profile">
                  <ul class="tg-tags">
                    <?php 
                        if( !empty( $current_author_profile->user_profile_specialities ) ) { 
                        foreach( $current_author_profile->user_profile_specialities as $key => $value ){
                            $current_term = get_term_by('slug', $key, 'specialities');                            
                            $trans_id =  pll_get_term($current_term->term_id, '');
                            $term = get_term( $trans_id, 'specialities' );
                            $name = $term->name;
                     ?>
                        <li><a href="javascript:;" class="tg-btn"><?php echo esc_attr( $name );?></a></li>
                      <?php }
                      } else{?>
                         <li><a href="javascript:;"class="tg-btn"><?php pll_e( 'No Speciality added yet.' );?></a></li>
                      <?php }?>
                  </ul>
              </div>
          </div>

          <div class="tg-honourawards tg-listview-v3 user-section-style">
            <div class="tg-userheading">
              <h2><i class="procedures"></i><?php pll_e('Procedures');?></h2>
            </div>
            <div class="tg-doctor-profile">
                  <ul class="tg-tags">
                    <?php 
                        if( !empty( $current_author_profile->user_profile_procedures ) ) { 
                        $pr_val = explode( ',', $current_author_profile->user_profile_procedures );
                        foreach( $pr_val as $key => $value ){
                     ?>
                        <li><a href="javascript:;" class="tg-btn"><?php echo esc_attr( $value );?></a></li>
                      <?php }
                      } else{?>
                         <li><a href="javascript:;"class="tg-btn"><?php pll_e( 'No Procedures added yet.' );?></a></li>
                      <?php }?>
                  </ul>
              </div>
          </div>

          <?php
if( !empty( $current_author_profile->user_profile_insurers ) ) {
    if( apply_filters('docdirect_is_setting_enabled',$current_author_profile->ID,'insurance' ) === true ){?>
    <div class="tg-insurance tg-innetworkinsurrance tg-tagsstyle tg-listview-v3 user-section-style">
        <div class="tg-userheading">
            <h2><i class="fa fa-shield"></i><?php pll_e('In-Network Insurance','docdirect');?></h2>
        </div>
        <div class="see-more-info">
            <p><a href="javascript:;"><?php pll_e('See which insurance(s) covers your care.','docdirect');?>
            <span><i class="fa fa-plus"></i></span></a></p>
        </div>
        <ul class="elm-display-none insurance-wrap">
            <?php
            foreach( $current_author_profile->user_profile_insurers as $key => $value ){
                $insurance      = get_term_by( 'name', $value, 'insurer');
                if( !empty( $insurance ) ) {
                    if (function_exists('z_taxonomy_image_url')) {
                        $img_url = z_taxonomy_image_url($insurance->term_id);
                        if( !empty( $img_url ) ) {
                            $img_id = z_get_attachment_id_by_url($img_url);
                            $insurance_logo = docdirect_get_image_source($img_id,150,150);
                            $img = '<img src="'.$insurance_logo.'">';
                        }
                    }
                    // $insurance_logo = get_term_meta( $insurance->term_id, 'insurance_logo', true );
                    if( !empty( $insurance->name ) ){
                ?>
                <li>
                    <span><?php echo esc_attr( $insurance->name );?></span>
                    <?php if( !empty( $insurance_logo ) ) {?>
                        <span class="insurance_logo"><img src="<?php echo esc_url( $insurance_logo );?>"></span>
                    <?php }?>
                </li>
            <?php }}}?>
        </ul>
    </div>
  <?php }
}

?>

    <?php if( !in_array('company', $list_terms) &&            
        !in_array('medical-centre', $list_terms) &&
        !in_array('hospital-type', $list_terms) &&           
        !in_array('scans-testing', $list_terms)
    ) {  ?>  
          <?php if( isset($current_option['affiliations']) ){?>
          <div class="tg-affiliation tg-listview-v3 user-section-style">
            <div class="tg-userheading">
              <h2><i class="fa fa-group"></i><?php pll_e('Affiliation');?></h2>
            </div>
            
        <?php
          $title_aff_myplacework = get_user_meta($current_author_profile->ID, 'aff_myplacework', true);
          $title_aff_myplacework = ($title_aff_myplacework == '') ? pll__('My Place of work') : $title_aff_myplacework ;
          $title_aff_myteam = get_user_meta($current_author_profile->ID, 'aff_myteam', true);
          $title_aff_myteam = ($title_aff_myteam == '') ? pll__('My Team') : $title_aff_myteam ;
          $title_aff_other = get_user_meta($current_author_profile->ID, 'aff_other', true);
          $title_aff_other = ($title_aff_other == '') ? pll__('Other Connections') : $title_aff_other ;
            
            $arr_group = array(
                array(
                    'slug' => 'aff_myplacework',
                    'name' => $title_aff_myplacework,
                    'icon' => 'fa-hospital-o',
                ),
                array(
                    'slug' => 'aff_myteam',
                    'name' => $title_aff_myteam,
                    'icon' => 'fa-users',
                ),
                array(
                    'slug' => 'aff_other',
                    'name' => $title_aff_other,
                    'icon' => 'fa-link',
                )
            );
        foreach ($arr_group as $group) {
            $ppp = 6;
            $apply_affiliation1 = get_apply_affiliation_by_user($current_author_profile->ID,$group['slug']);
            $count = count($apply_affiliation1);
            $apply_affiliation = array_slice($apply_affiliation1, 0, $ppp, true);
        ?>
        <?php if($count > 0){?>
        <div class="aff_group">
            <input type="hidden" name="array" value=<?php echo json_encode($apply_affiliation1,JSON_FORCE_OBJECT);?>>
            <input type="hidden" name="posts_per_page" value="<?php echo $ppp;?>">
            <div class="tg-otherphotos1">
            <div class="tg-heading-border tg-small">      
                <h4>
                    <i class="fa <?php echo $group['icon'];?>"></i>
                    <span><?php echo $group['name'];?>  </span>                  
                </h4>
            </div>
            <div class="list_user">
                <div class="row">
                    <?php
                    if (!empty($apply_affiliation)) {
                        foreach ($apply_affiliation as $user_id => $post_id) {
                            $type_aff = get_post_meta($post_id, 'type_aff', true);
                            $user_from = get_post_meta($post_id, 'user_from', true);
                            $user_to = get_post_meta($post_id, 'user_to', true);
                            if ($type_aff == 'in_db') {
                                // $user_id = $user_to; 
                                $user = get_userdata($user_id);
                                $review_data    = kt_docdirect_get_everage_rating ( $user->ID );
                                $name = kt_get_title_name($user_id).$user->first_name.' '.$user->last_name;
                                $thumbnail = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                                $link = get_author_posts_url($user->ID);
                                $tagline = get_user_meta($user->ID, 'tagline', true);
                                $specialities = get_user_meta($user->ID,'user_profile_specialities',true);
                                if (!empty($specialities)) {
                                    $aloha = array_slice($specialities, 0, 4);
                                    $specialities_val = implode(', ', array_values($aloha));
                                }
                                $rating = docdirect_get_rating_stars($review_data,'return', 'hide');
                                $data_modal = '';
                                $cl = '';
                            }else {
                                $user_id = $user_from;
                                $width = '150';
                                $height = '150';
                                $thumb_id = get_post_thumbnail_id($post_id);
                                $thumb_url = wp_get_attachment_image_src($thumb_id, array($width, $height), true);
                                $thumbnail  = $thumb_url[0];
                                $link = 'javascript:;';
                                $name = get_the_title($post_id);
                                $tagline = get_post_meta($post_id, 'tagline', true);
                                $specialities_val = get_post_meta($post_id, 'specialties', true);
                                $rating = '';
                                if ($current_author_profile->ID == $current_user->ID) {
                                    $email = get_post_meta($post_id, 'email', true);
                                    $data_modal = 'data-email="'.$email.'"';
                                    $cl = 'hasmodal';
                                }
                            }
                            if((user_id_exists($user_id) && $user_id != $user_identity && $type_aff == 'in_db') || 
                                ($type_aff == 'out_db') ){
                            // var_dump($user_id);
                            ?>
                            <article id="user-<?php echo intval( $user->ID );?>" data-post_id="<?php echo intval( $post_id );?>" class="col-sm-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <?php
                                        if($type_aff == 'in_db' && $user_id != $current_user->ID ) {
                                            ?>
                                            <div class="button_gr">
                                                <?php docdirect_get_wishlist_button($user_id,true,'v2');?>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <a href="<?php echo $link;?>" <?php echo $data_modal;?> class="list-avatar <?php echo $cl;?>">                                        
                                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>">
                                            <?php
                                            if (get_post_meta($post_id, 'type_aff', true) == 'in_db') {
                                                echo '<span class="member_tag member">'.pll__('Member').'</span>';
                                            }else {
                                                if ($current_author_profile->ID == $current_user->ID) {
                                                    echo '<span class="member_tag invite_tag">'.pll__('Invite').'</span>';
                                                }else {
                                                    echo '<span class="member_tag listing_only">'.pll__('Listing Only').'</span>';
                                                }
                                            }
                                            ?>
                                        </a>
                                    </div>
                                    <div class="col-sm-8">
                                      
                                      <div class="tg-small">
                                        <h4><a href="<?php echo $link; ?>"><?php echo $name;?></a></h4>
                                      </div>
                                      <?php if( !empty( $tagline ) ){?>
                                          <div class="tg-tagline">
                                            <h5><?php echo $tagline;?></h5>
                                          </div>
                                      <?php }?>
                                      <?php if( !empty( $specialities_val ) ){?>
                                          <div class="tg-specialities">
                                            <p><strong><?php pll_e('Specialities: ');?></strong>
                                                <?php
                                                  echo $specialities_val;
                                                ?>
                                            </p>
                                          </div>
                                      <?php }?>
                                      <?php echo $rating;?>
                                    </div>
                                </div>             
                            </article>
                            <?php
                            }
                        }
                    }
                    ?>
                </div>
                <?php if($count > $ppp){?>
                    <div class="col-xs-12 text-center">
                        <a class="load_more btn btn-primary" data-group="<?php echo $group['slug'];?>" href="javascript:;"><?php pll_e('Load more');?></a>
                    </div>
                <?php }?>
            </div>
            </div>
        </div>
        <?php }?>
        <?php }?>
          </div>
         <?php }?>
    <?php }?>

          <?php if( isset( $experience_switch ) && $experience_switch === 'enable' ){?>
          <div class="tg-userexperience">
            <div class="tg-userheading">
              <h2><i class="fa fa-briefcase"></i><?php pll_e('Experience');?></h2>
            </div>
            <ul>
            <?php 
			if( !empty( $current_author_profile->experience ) ) {
				foreach( $current_author_profile->experience as $key => $value ){
					$start_year	= '';
					$end_year	= '';
					$period	= '';
					if( !empty( $value['start_date'] ) || !empty( $value['end_date'] ) ){
						if( !empty( $value['start_date'] ) ){
							$start_year	= date('M, Y',strtotime( $value['start_date']));
						}
						
						if( !empty( $value['end_date'] ) ){
							$end_year	= date('M, Y',strtotime( $value['end_date']));
						} else{
							$end_year	= esc_html__('Current');
						} 

						
						if( !empty( $start_year ) || !empty( $end_year ) ){
							$period	= '('.$start_year.'&nbsp;-&nbsp;'.$end_year.')';
						}
					}
				?>
                <li>
                    <div class="tg-dotstyletitle">
                      <h3><?php echo esc_attr( $value['title'] );?>&nbsp;&nbsp;<?php echo esc_attr( $period );?></h3>
                      <span><?php echo esc_attr( $value['company'] );?></span>
                    </div>
                    <div class="tg-description">
                      <p><?php echo esc_attr( $value['description'] );?></p>
                    </div>
               </li>
			   <?php }
			  } else{?>
			  	<li><p><?php pll_e('No experience added yet.');?></p></li>
			  <?php }?>
            </ul>
          </div>
          <?php }?>
          <?php if( isset( $education_switch ) && $education_switch === 'enable' ){?>
          <div class="tg-userexperience tg-userqualification">
            <div class="tg-userheading">
              <h2><i class="fa fa-graduation-cap"></i><?php pll_e('Education');?></h2>
            </div>
            <ul>
			<?php 
            if( !empty( $current_author_profile->education ) ) {
                foreach( $current_author_profile->education as $key => $value ){
                    $start_year	= '';
                    $end_year	= '';
                    $period	= '';
                    if( !empty( $value['start_date'] ) || !empty( $value['end_date'] ) ){
                        if( !empty( $value['start_date'] ) ){
                            $start_year	= date('M, Y',strtotime( $value['start_date']));
                        }
                        
                        if( !empty( $value['end_date'] ) ){
                            $end_year	= date('M, Y',strtotime( $value['end_date']));
                        } else{
                            $end_year	= esc_html__('Current');
                        } 
                        
                        if( !empty( $start_year ) || !empty( $end_year ) ){
                            $period	= '('.$start_year.'&nbsp;-&nbsp;'.$end_year.')';
                        }
                    }
                ?>
                <li>
                    <div class="tg-dotstyletitle">
                      <h3><?php echo esc_attr( $value['title'] );?><strong>&nbsp;&nbsp;</strong><?php echo esc_attr( $period );?></h3>
                      <span><?php echo esc_attr( $value['institute'] );?></span> 
                    </div>
                    <div class="tg-description">
                      <p><?php echo esc_attr( $value['description'] );?></p>
                    </div>
               </li>
               <?php }
              } else{?>
              <li><p><?php pll_e('No education added yet.');?></p></li>
              <?php }?>
        	</ul>
          </div>
          <?php }?>
          
          <div class="tg-userexperience tg-honourawards">
            <div class="tg-userheading">
              <h2><i class="fa fa-trophy"></i><?php pll_e('Honors & Awards');?></h2>
            </div>
            <ul>
				<?php 
                if( !empty( $current_author_profile->awards ) ) {
                    foreach( $current_author_profile->awards as $key => $value ){
                        $period	= '';
                        if( !empty( $value['date'] ) ){
                            if( !empty( $value['date'] ) ){
                                $period	= '('.date('F m, Y',strtotime( $value['date'])).')';
                            }
                        }
                    ?>
                    <li>
                        <div class="tg-dotstyletitle">
                          <h3><?php echo esc_attr( $value['name'] );?>&nbsp;&nbsp;<?php echo esc_attr( $period );?></h3>
                        </div> 
                        <div class="tg-description">
                          <p><?php echo esc_attr( $value['description'] );?></p>
                        </div>
                    </li>
                   <?php }
                  } else{?>
                  	<li><p><?php pll_e('No awards added yet.');?></p></li>
                  <?php }?>
            </ul>
          </div>
          <?php if( isset( $reviews_switch ) && $reviews_switch === 'enable' ){?>
              <div class="tg-userreviews">
                <div class="tg-userheading">
                <?php
                    $review_data    = kt_docdirect_get_everage_rating ( $current_author_profile->ID );
                ?>
                  <h2><?php echo intval( apply_filters('kt_docdirect_count_reviews',$current_author_profile->ID) );?>&nbsp;&nbsp;<?php pll_e('Review(s)');?></h2> 
                </div>
                <?php if( !empty( $review_data['by_ratings'] ) ) {?>
                <div class="tg-ratingbox">
                  <div class="tg-averagerating">
                    <h3><?php pll_e('Average Rating');?></h3>
                    <em><?php echo number_format((float)$review_data['total_average_rating'], 1, '.', '');?></em>
                    <span class="tg-stars"><?php docdirect_get_rating_stars($review_data,'echo','hide');?></span>
                  </div>
                  <div id="tg-userskill" class="tg-userskill">
                    <?php 
                        foreach( $review_data['by_ratings'] as $key => $value ){
                            $final_rate = 0;
                            if( !empty( $value['rating'] ) && !empty( $value['rating'] ) ) {
                                $get_sum	  = $value['rating'];
                                $get_total	= $value['total'];
                                $final_rate	= $get_sum/$get_total*100;
                            } else{
                                $final_rate	= 0;
                            }
                            
                        ?>
                        <div class="tg-skill"> 
                          <span class="tg-skillname"><?php echo intval( $key+1 );?> <?php pll_e('Stars');?></span> 
                          <span class="tg-skillpercentage"><?php echo intval($final_rate/5);?>%</span>
                          <div class="tg-skillbox">
                            <div class="tg-skillholder" data-percent="<?php echo intval($final_rate/5);?>%">
                              <div class="tg-skillbar"></div>
                            </div>
                          </div>
                        </div>
                    <?php }?>
                  </div>
                </div>
                <?php }?>
                <ul class="tg-reviewlisting">
                <?php if( apply_filters('kt_docdirect_count_reviews',$current_author_profile->ID) > 0 ){
                global $paged;
                if (empty($paged)) $paged = 1;
                $show_posts    = get_option('posts_per_page') ? get_option('posts_per_page') : '-1';        
                
                $meta_query_args = array('relation' => 'AND',);
                $meta_query_args[] = array(
                                        'key' 	   => 'user_to',
                                        'value' 	 => $current_author_profile->ID,
                                        'compare'   => '=',
                                        'type'	  => 'NUMERIC'
                                    );
                
                $args = array('posts_per_page' => "-1", 
                    'post_type' => 'docdirectreviews', 
                    'order' => 'DESC', 
                    'orderby' => 'ID', 
                    'post_status' => 'publish', 
                    'lang' => '', 
                    'ignore_sticky_posts' => 1
                );
                
                $args['meta_query'] = $meta_query_args;
                
                $query 		= new WP_Query( $args );
                $count_post = $query->post_count;        
                
                //Main Query	
                $args 		= array('posts_per_page' => $show_posts, 
                    'post_type' => 'docdirectreviews', 
                    'paged' => $paged, 
                    'order' => 'DESC', 
                    'orderby' => 'ID', 
                    'post_status' => 'publish',
                    'lang' => '', 
                    'ignore_sticky_posts' => 1
                );
                
                $args['meta_query'] = $meta_query_args;
                
                $query 		= new WP_Query($args);
                if( $query->have_posts() ){
                    while($query->have_posts()) : $query->the_post();
                        global $post;
                        $user_rating = fw_get_db_post_option($post->ID, 'user_rating', true);
                        $user_from = fw_get_db_post_option($post->ID, 'user_from', true);
                        $user_to = fw_get_db_post_option($post->ID, 'user_to', true);
                        $review_date = fw_get_db_post_option($post->ID, 'review_date', true);
                        $user_data 	  = get_user_by( 'id', intval( $user_from ) );
                        
                        $user_name	= '';
                        if( !empty( $user_data ) ) {
                            $user_name	= $user_data->first_name.' '.$user_data->last_name;
                        }
                        
                        if( empty( $user_name ) && !empty( $user_data ) ){
                            $user_name	= $user_data->user_login;
                        }
                        $user_rating = json_decode($user_rating, true);
                        $sum = array_sum($user_rating);
                        $percentage	= $sum/5*20;

                        $author = get_userdata( $user_from );
                        $user_roles = $author->roles;
                        if( $user_roles[0] == 'professional' && $user_from != $user_to ){
                            $link = get_author_posts_url($user_from);
                        
                            $avatar = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_from),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                            $name = esc_attr( $user_name );
                        }else {
                            $link = 'javascript:;';
                            $avatar = get_stylesheet_directory_uri().'/images/verified-patient.png';
                            $name = pll__('By a Verified Patient');
                        }
                        
                    ?>
                    <li id="rv-<?php echo $post->ID;?>">
                        <div class="tg-review">
                          <figure class="tg-reviewimg"> 
                            <a href="<?php echo $link; ?>"><img src="<?php echo esc_url( $avatar );?>" alt="<?php pll_e('Reviewer');?>"></a>
                          </figure>
                          <div class="tg-reviewcontet">
                            <div class="tg-reviewhead">
                              <div class="tg-reviewheadleft">
                                <h3><a href="<?php echo $link; ?>"><?php echo $name;?></a></h3>
                                <span><?php echo human_time_diff( strtotime( $review_date ), current_time('timestamp') ) . ' ago'; ?></span> </div>
                              <div class="tg-reviewheadright tg-stars star-rating">
                                <span style="width:<?php echo esc_attr( $percentage );?>%"></span>
                              </div>
                            </div>
                            <div class="tg-description">
                              <p><?php the_content();?></p>
                            </div>
                            <?php
                                $args1       = array(
                                    'post_type' => 'docdirectreviews', 
                                    'order' => 'DESC', 
                                    'orderby' => 'ID', 
                                    'post_status' => 'publish',
                                    'lang' => '', 
                                    'post_parent' => $post->ID,
                                    'ignore_sticky_posts' => 1
                                );
                                
                                $query1      = new WP_Query($args1);
                                if ( $current_user->ID == $current_author_profile->ID && $query1->post_count < 1 ) {
                                    ?>
                                        <a class="tg-btn btn_reply" rel="nofollow" href="javascript:;" aria-label="Reply to"><?php pll_e('Reply');?></a>
                                    <?php
                                }
                            ?>
                          </div>
                        </div>
                        <?php                            
                            if( $query1->have_posts() ){
                                echo '<ul>';
                                while($query1->have_posts()) : $query1->the_post();
                                    global $post;

                                    $user_data    = get_user_by( 'id', intval( get_the_author_meta('ID') ) );

                                    $avatar = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), get_the_author_meta('ID')),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                                    $user_name  = '';
                                    if( !empty( $user_data ) ) {
                                        $user_name  = $user_data->first_name.' '.$user_data->last_name;
                                    }
                                    
                                    if( empty( $user_name ) && !empty( $user_data ) ){
                                        $user_name  = $user_data->user_login;
                                    }
                                ?>
                                <li id="rv-<?php echo $post->ID;?>">
                                    <div class="tg-review">
                                      <figure class="tg-reviewimg"> 
                                        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><img src="<?php echo esc_url( $avatar );?>" alt="<?php pll_e('Reviewer');?>"></a>
                                      </figure>
                                      <div class="tg-reviewcontet">
                                        <div class="tg-reviewhead">
                                          <div class="tg-reviewheadleft">
                                            <h3><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo esc_attr( $user_name );?></a>
                                                                         
                                                <?php
                                                    if ($current_user->ID == $current_author_profile->ID) {
                                                        ?>
                                                            <a class="tg-btn remove_reply_button" href="javascript:;"><i class="fa fa-times"></i></a>
                                                        <?php
                                                    }
                                                ?>
                                            </h3>
                                            <span><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></span> </div>
                                        </div>
                                        <div class="tg-description">
                                          <p><?php the_content();?></p>
                                        </div>
                                      </div>
                                    </div>
                                </li>
                                <?php
                                endwhile;
                                echo '</ul>';
                            }
                        ?>
                    </li>
                    <?php 
                        endwhile; wp_reset_postdata();
                    }else{?>
                        <li class="noreviews-found"> <?php DoctorDirectory_NotificationsHelper::informations(pll__('No Reviews Found.'));;?></li>
                    <?php }
                } else{?>
                    <li class="noreviews-found"> <?php DoctorDirectory_NotificationsHelper::informations(pll__('No Reviews Found.'));;?></li>
                <?php }?>
                  
                </ul>
                <?php 
                if( isset( $current_user->ID ) 
                    && 
                    $current_user->ID != $current_author_profile->ID 
                ){?>
                <div id="leavereview" class="tg-leaveyourreview">
                  <div class="tg-userheading">
                    <h2><?php pll_e('Leave Your Review');?></h2>
                  </div>
                  <button class="tg-btn" type="button" data-toggle="modal" data-target=".tg-modal-review"><?php pll_e('Click to review');?></button>
                </div>
                <?php }?>
              </div>
           <?php }?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php 
if( isset( $current_user->ID ) 
    && 
    $current_user->ID == $current_author_profile->ID
    &&
    is_user_logged_in()
){
?>

    <div id="reply_review" style="display: none;">
        <form class="tg-formleavereview form-review">
            <div class="message_contact  theme-notification"></div>
            <fieldset>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="tg-heading-border tg-small">
                            <h4>Reply Review</h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <input type="text" name="user_subject" class="form-control" placeholder="<?php pll_e('Subject');?>">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <textarea class="form-control" name="user_description" placeholder="<?php pll_e('Reply Description *');?>"></textarea>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <button class="tg-btn pull-left" id="cancel-review-reply-link" type="button"><?php pll_e('Cancel reply');?></button>
                      <button class="tg-btn make-reply" type="submit"><?php pll_e('Submit Reply');?></button>
                    </div>
                </div>
            </fieldset>
        </form>
        
    </div>

<?php }?>
<?php 
add_action('wp_footer', 'kt_add_modal_footer_author', 99);
function kt_add_modal_footer_author(){
global $wp_query,$current_user;
$current_author_profile = $wp_query->get_queried_object();
$privacy        = docdirect_get_privacy_settings($current_author_profile->ID); //Privacy settings
if( isset( $current_user->ID ) 
	&& 
	$current_user->ID != $current_author_profile->ID
	&&
	is_user_logged_in()
){

	if( !empty( $privacy['appointments'] )
	  && 
	  	$privacy['appointments'] == 'on'
 ) {

    $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
    $profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
    $redirect = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'mybookings', $current_user->ID,true);
?>

<div class="modal fade tg-reviewpopup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog tg-modalcontent" role="document">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <p><?php pll_e('You have already written a review. In order to post another review you must rebook with this specialist again through the system.  Once a booking is confirmed, you will gain another automatic review token.  You may also request a review code from the specialist directly. ');?></p>
        <a class="btn btn-success" data-dismiss="modal"><?php pll_e('Ok, will try again later.');?></a>
  </div>
</div>

<div class="modal fade tg-appointmentpopup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg tg-modalcontent" role="document">
    <form action="#" method="post" class="appointment-form">
      <fieldset class="booking-model-contents">
        <ul class="tg-navdocappointment" role="tablist">
          <li class="active"><a href="javascript:;" class="bk-step-1"><?php pll_e('1. choose service');?></a></li>
          <li><a href="javascript:;" class="bk-step-2"><?php pll_e('2. available schedule');?></a></li>
          <li><a href="javascript:;" class="bk-step-3"><?php pll_e('3. your contact detail');?></a></li>
          <li><a href="javascript:;" class="bk-step-4"><?php pll_e('4. Payment Mode');?></a></li>
          <li><a href="javascript:;" class="bk-step-5"><?php pll_e('5. Finish');?></a></li>
        </ul>
        <div class="tab-content tg-appointmenttabcontent" data-id="<?php echo esc_attr( $current_author_profile->ID );?>">
          <div class="tab-pane active step-one-contents" id="one">
           	<?php kt_docdirect_get_booking_step_one($current_author_profile->ID,'echo');?>
          </div>
          <div class="tab-pane step-two-contents" id="two">
          	<?php 
                $date_format = get_option('date_format');
                $time_format = get_option('time_format');
                $today = current_time( 'timestamp' );
                $booking_date    = $_GET["booking_date"];
                $booking_time    = $_GET["booking_time"];
                $time = explode('-',$booking_time);
                $date_bk = date($date_format,strtotime($booking_date));
                $time_bk = date($time_format,strtotime('2016-01-01 '.$time[0]) );
                $date_time_booking = $date_bk.' '.$time_bk;
                
                if ( isset($_POST["booking_date"]) && $today <= strtotime($date_time_booking )) {
                    kt_docdirect_get_booking_step_two_calender($current_author_profile->ID,'echo',$_GET["booking_date"]);
                }else {
                    kt_docdirect_get_booking_step_two_calender($current_author_profile->ID,'echo',$_GET["booking_date"]);
                }
            ?>
          </div>
          <div class="tab-pane step-three-contents" id="three"></div>
          <div class="tab-pane step-four-contents" id="four"></div>
          <div class="tab-pane step-five-contents" id="five"></div>
          <div class="tg-btnbox booking-step-button">
              <?php
                $cl = '';
                if (isset($_POST["booking_date"])) {
                    $cl = 'has_quickbooking';
                }
              ?>
              <button type="button" class="tg-btn kt_bk-step-next col-sm-push-9 col-md-push-10 <?php echo $cl;?>"><?php pll_e('next');?></button>
              <button type="button" class="tg-btn bk-step-prev col-sm-pull-9 col-md-pull-10"><?php pll_e('Previous');?></button>
              <a class="hidden tg-btn col-sm-push-9 col-md-push-10" href="<?php echo $redirect;?>"><?php pll_e('Finish');?></a>
            </div>
        </div>
      </fieldset>
    </form>
  </div>
</div>
<?php }}?>
<div class="modal fade tg-modal-review" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog tg-modal-content" role="document">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <?php require_once('inc/form_review.php');?>
  </div>
</div>
<div class="modal fade tg-request-only" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="tg-modal-content" role="document">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title"><?php pll_e('Request Appointment');?></h4>
        <p><?php pll_e('Healthcare providers can change their appointment availability status to request only when away or busy. please send an appointment request with by filling in the required* fields:');?></p>
    </div>
    <div class="tg-request-form">
        <form action="#" method="post" class="request-form">
            <div class="doctor_info">
                <?php
                    // $user_info = get_userdata( $current_author_profile->ID );
                    $avatar = apply_filters(
                            'docdirect_get_user_avatar_filter',
                             docdirect_get_user_avatar(array('width'=>150,'height'=>150), $current_author_profile->ID),
                             array('width'=>150,'height'=>150) //size width,height
                        );
                ?>
                <a href="<?php echo get_author_posts_url($current_author_profile->ID); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
                <div>
                    <h4><?php echo esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?></h4>
                    <?php if( !empty( $current_author_profile->tagline ) ) {?>
                        <span><?php echo esc_attr( $current_author_profile->tagline );?></span>
                    <?php }?>
                </div>
                <input type="hidden" name="doctor_id" value="<?php echo $current_author_profile->ID;?>">
            </div>
          <fieldset>
          <div class="row">
            <div class="form-group1 col-sm-6">
                <input type="text" name="first_name" class="form-control" id="first_name" placeholder="<?php pll_e( 'First Name *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="last_name" class="form-control" id="last_name" placeholder="<?php pll_e( 'Last Name *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <div class="doc-select">
                    <select name="gender">
                        <option value="">Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="phone" class="form-control" id="phone" placeholder="<?php pll_e( 'Phone Number *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="email" class="form-control" id="email_address" placeholder="<?php pll_e( 'Email Address *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="date_of_birth" class="form-control" id="date_of_birth" placeholder="<?php pll_e( 'Date of Birth' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">              
                <div class="form-group insurers">
                    <?php
                        global $current_user;
                        $patient_insurers = get_user_meta( $current_user->ID, 'patient_insurers', true);

                        $insurer = get_term_by('id', $patient_insurers, 'insurer');

                        $current_insurer_text = $insurer->name;
                        $current_insurer       = !empty( $_GET['insurer'] ) ? $_GET['insurer'] : '';
                        if ( $current_insurer != '' ) {
                            $insurer    = get_term_by( 'slug', $current_insurer, 'insurer');
                            $current_insurer_text = $insurer->name;
                        }
                        if (function_exists('kt_read_insurer')) {
                            $insurers_list = kt_read_insurer();
                        }

                    ?>
                    <a class="dropdown-button-group" href="javascript:;"><?php echo $current_insurer_text;?></a>
                    <input class="select_category" type="hidden" name="insurer" value="<?php echo $insurer->name;?>" />
                    <div class="dropdown-input-group">
                        <div class="dropdown-wrap">
                            <li data-slug=""><?php pll_e('Select Insurers');?></li>                   
                            <?php                                     
                            if( isset( $insurers_list ) && !empty( $insurers_list ) ){
                              if (function_exists('kt_read_insurer')) {
                                  $list_insurer = kt_read_insurer();
                              }
                              foreach( $insurers_list as $key => $insurer ){
                            ?>
                            <?php
                            // $taxonomy_image_url = get_option('z_taxonomy_image'.$insurer->term_id);
                                        $sample_bg_url = get_template_directory_uri().'/images/sample-insurer.png';
                                        $bg_url = ($list_insurer[$insurer->term_id][1]!='') ? $list_insurer[$insurer->term_id][1] : $sample_bg_url;
                            ?>
                            <li data-slug="<?php echo $insurer->slug;?>">
                              <img width="150" height="150" src="<?php echo $bg_url; ?>" >
                              <span><?php echo esc_attr( $insurer->name ); ?></span>                
                            </li>

                            <?php }}?>
                        </div>
                        <a class="close_specialities_wrap" href="javascript:;">
                            <i class="fa fa-close"></i>
                            <span><?php esc_html_e('Close','docdirect'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="hkid" class="form-control" id="hkid" placeholder="<?php pll_e( 'HKID/Passport #' ) ?>">
            </div>
            <div class="form-group1 col-sm-12">
                <label><?php pll_e( 'Reason for visit' ) ?></label>
                <textarea name="message"></textarea>
            </div>
            <div class="form-group1 col-sm-12">
                <a class="btn btn-submit" href="javascript:;"><?php pll_e( 'Submit' ) ?></a>
                <a class="btn btn-cancel" href="javascript:;" data-dismiss="modal"><?php pll_e( 'Cancel' ) ?></a>
            </div>
          </div>
          </fieldset>
        </form>
    </div>
  </div>
</div>


<div class="modal fade tg-invite-modal">
  <div class="tg-modal-content" role="document">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?php pll_e( 'Invite' ) ?></h4>
        </div>
        <div class="tg-invite-form">
            <form action="#" method="post" class="tg-form-modal invite-form">
            <fieldset>
                <div class="row">
                    <!-- <div class="form-group col-sm-12">
                        <label for="subject"><?php pll_e( 'Subject' ) ?></label>
                        <input type="text" name="subject" class="form-control" id="subject" placeholder="<?php pll_e( 'subject' ) ?>">
                    </div> -->
                    <div class="form-group col-sm-12">
                        <label for="email"><?php pll_e( 'Email' ) ?></label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="<?php pll_e( 'email' ) ?>">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="email"><?php pll_e( 'Description' ) ?></label>
                        <p><?php pll_e( 'Feel free to add your personal invitiation message to a colleague or clinic.' ) ?></p>
                        <textarea name="desc" class="form-control"><?php pll_e( 'MediFinder is a booking & review platform that offers medical professionals an alternative to reach out to new patients. Promote your specialties & fill up empty schedules with our 24 hour online booking system. Patients can view your credentials, pricing, insurance affiliations & any colleagues or clinics you have connections with. We also offer all professionals a 6 months free listing! ' ) ?></textarea>
                    </div>
                    <div class="form-group col-sm-12 response">
                    </div>
                    <div class="form-group col-sm-12">
                        <button type="button" class="tg-btn submit_invite"><?php pll_e('Submit');?></button>
                    </div>
                    <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

                </div>
            </fieldset>
            </form>
        </div>
  </div>
</div>
<?php }?>
<?php get_footer();?>