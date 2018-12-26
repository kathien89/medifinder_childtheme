<?php
/**
 * User Invoices
 * return html
 */

global $current_user, $wp_roles,$userdata,$post;
$dir_obj	= new DocDirect_Scripts();
$user_identity	= $current_user->ID;
$url_identity	= $user_identity;

if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
	$url_identity	= $_GET['identity'];
}

$user_disable_stripe    = '';
$user_disable_paypal    = '';


$confirmation_title_default = pll__('Your Appointment Confirmation','docdirect');
$approved_title_default  = pll__('Your Appointment Approved','docdirect');
$cancelled_title_default    = pll__('Your Appointment Cancelled','docdirect');

if(function_exists('fw_get_db_settings_option')) {
    $user_disable_stripe = fw_get_db_settings_option('user_disable_stripe', $default_value = null);
    $user_disable_paypal = fw_get_db_settings_option('user_disable_paypal', $default_value = null);
    $booking_confirmed_default = fw_get_db_settings_option('confirm_booking', $default_value = null);
    $booking_approved_default = fw_get_db_settings_option('approve_booking', $default_value = null);
    $booking_cancelled_default = fw_get_db_settings_option('cancel_booking', $default_value = null);

    $confirmation_title_default = fw_get_db_settings_option('confirm_subject', $default_value = null);
    $approved_title_default = fw_get_db_settings_option('approve_subject', $default_value = null);
    $cancelled_title_default = fw_get_db_settings_option('cancel_subject', $default_value = null);
}

$email_logo 		= apply_filters(
					'docdirect_single_image_filter',
					 docdirect_get_single_image(array('width'=>150,'height'=>150), $user_identity) ,
					 array('width'=>150,'height'=>150) //size width,height=
				);


//Default template for booking confirmation			
if( empty( $booking_confirmed_default ) ){	
    $booking_confirmed_default	= 'Hey %customer_name%!<br/>

						This is confirmation that you have booked "%service%"<br/>
						We will let you know regarding your booking soon.<br/><br/>
						
						Thank you for choosing our company.<br/><br/>
						
						Sincerely,<br/>
						MediFinder Team<br/>
						%logo%';
}
//Default template for booking cancellation
if( empty( $booking_cancelled_default ) ){
    $booking_cancelled_default	= 'Hey %customer_name%!<br/>

						 This is confirmation that your booking regarding "%service%" has been cancelled/declined.<br/>
						
						 You may contact the specialist directly for inquiries about the cancellation, or re-book an appointment at another time or date.<br/><br/>
						
						 Sincerely,<br/>
						 The MediFinder Team<br/>
						 %logo%';
}
//Default template for booking Approved
if( empty( $booking_approved_default ) ){
$booking_approved_default	= 'Hey %customer_name%!<br/>

						This is confirmation that your booking regarding "%service%" has been approved by  "%doctor_name%".<br/>
						
						We are waiting you at "%address%" on %appointment_date% at %appointment_time%.<br/><br/>

                        ----------------------------------------------------

                        Price of Service: $%price%
                        Appointment Time/Date: %appointment_time% / %appointment_date%
                        Phone Number: %phone_number%
                        Email: %doctor_email%
                        Website: %website%
                        Floor: %floor%
                        Location: %location%
                        Gmaps link: %gmap_link%

                        ----------------------------------------------------

                        Once you have finished your appointment, you can post a review and share your experience with others.
                        <a href="%link_review%">Review</a><br/><br/>
						
						Sincerely,<br/>
						MediFinder Team<br/>
						%logo%';
}
delete_user_meta( $user_identity, 'confirmation_title');
delete_user_meta( $user_identity, 'approved_title');
delete_user_meta( $user_identity, 'cancelled_title');

delete_user_meta( $user_identity, 'booking_cancelled');
delete_user_meta( $user_identity, 'booking_confirmed');
delete_user_meta( $user_identity, 'booking_approved');

/*$confirmation_title	    = get_user_meta( $user_identity, 'confirmation_title', true);
$approved_title	    	= get_user_meta( $user_identity, 'approved_title', true);
$cancelled_title	       = get_user_meta( $user_identity, 'cancelled_title', true);

$booking_cancelled	    = get_user_meta( $user_identity, 'booking_cancelled', true);
$booking_confirmed	    = get_user_meta( $user_identity, 'booking_confirmed', true);
$booking_approved	     = get_user_meta( $user_identity, 'booking_approved', true);*/

$thank_you	     		= get_user_meta( $user_identity, 'thank_you', true);
$schedule_message	     = get_user_meta( $user_identity, 'schedule_message', true);
$email_media 			  = get_user_meta($user_identity , 'email_media' , true);

$currency	    		 = get_user_meta( $user_identity, 'currency', true);
$currency_symbol	      = get_user_meta( $user_identity, 'currency_symbol', true);

$confirmation_title	= !empty( $confirmation_title ) ? $confirmation_title : $confirmation_title_default;
$approved_title		= !empty( $approved_title ) ? $approved_title : $approved_title_default;
$cancelled_title	   = !empty( $cancelled_title ) ? $cancelled_title : $cancelled_title_default;

$booking_cancelled	= !empty( $booking_cancelled ) ? $booking_cancelled : $booking_cancelled_default;
$booking_confirmed	= !empty( $booking_confirmed ) ? $booking_confirmed : $booking_confirmed_default;
$booking_approved	 = !empty( $booking_approved ) ? $booking_approved : $booking_approved_default;

$currencies	= docdirect_prepare_currency_symbols();
$currencies_array	= array();
foreach($currencies as $key => $value ){
	$currencies_array[$key] = $value['name'].'-'.$value['code'];
}


//Payments
$paypal_enable	= get_user_meta( $user_identity, 'paypal_enable', true);
$paypal_email_id    = get_user_meta( $user_identity, 'paypal_email_id', true);
$paypal_username    = get_user_meta( $user_identity, 'paypal_username', true);
$paypal_password    = get_user_meta( $user_identity, 'paypal_password', true);
$paypal_signature	= get_user_meta( $user_identity, 'paypal_signature', true);
$stripe_enable	= get_user_meta( $user_identity, 'stripe_enable', true);
$stripe_secret	= get_user_meta( $user_identity, 'stripe_secret', true);
$stripe_publishable	= get_user_meta( $user_identity, 'stripe_publishable', true);
$stripe_site	= get_user_meta( $user_identity, 'stripe_site', true);
$paypal_enable	= get_user_meta( $user_identity, 'paypal_enable', true);
$stripe_decimal	= get_user_meta( $user_identity, 'stripe_decimal', true);

?>
<div class="doc-booking-emails dr-bookings">
    <div class="tg-haslayout">
        <form action="#" name="email-settings" class="email-settings">
       <div class="tg-formsection">
        <div class="email-settings-tabs">
            <div class="tg-heading-border tg-small">
                <h3><?php pll_e('Payment Settings','docdirect');?></h3>
            </div>
            <div class="booking-email-wrap booking-currency-wrap">
                <div class="tg-small doc-tab-link">
                    <h3><?php pll_e('PayPal Settings','docdirect');?></h3>
                </div>
                <div class="tab-data">
                    <div class="email-params">
                        <p><?php pll_e('This will be used in booking payment methods.','docdirect');?></p>
                    </div>
                    <div class="email-contents tg-form-privacy">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">  
                                    <div class="tg-paypal-gateway"> 
                                      <div class="tg-iosstylcheckbox">
                                        <input type="hidden" name="paypal_enable">
                                        <input type="checkbox" <?php echo isset( $paypal_enable ) && $paypal_enable === 'on' ? 'checked':'';?>  name="paypal_enable" id="tg-paypal_enable">
                                        <label for="tg-paypal_enable" class="checkbox-label" data-private="<?php esc_attr_e('Disable','docdirect');?>" data-public="<?php esc_attr_e('Enable','docdirect');?>"></label>
                                      </div>
                                      <span class="tg-privacy-name"><?php pll_e('PayPal','docdirect');?></span>
                                      <p><?php esc_attr_e('Please enable PayPal gateway for booking payments.','docdirect');?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <span class="tg-privacy-name how_to"><?php pll_e('Paypal API <a target="blank" href="https://www.youtube.com/watch?v=weUVQPKZq98">(How create paypal API? )</a>','docdirect');?></span>
                                </div>
                                <div class="form-group">
                                    <span class="tg-privacy-name"><?php pll_e('Paypal API Username','docdirect');?></span>
                                    <input type="text" placeholder="<?php pll_e('Paypal Username','docdirect');?>" class="paypal_username" name="paypal_username" value="<?php echo esc_attr($paypal_username);?>" />
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <span class="tg-privacy-name"><?php pll_e('Paypal API Password','docdirect');?></span>
                                    <input type="password" placeholder="<?php pll_e('Paypal Password','docdirect');?>" class="paypal_password" name="paypal_password" value="<?php echo esc_attr($paypal_password);?>" />
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <span class="tg-privacy-name how_to"><?php pll_e('Paypal API Signature <a target="blank" href="https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature">(How get paypal signature info? )</a>','docdirect');?></span>
                                    <input type="password" placeholder="<?php pll_e('Paypal Signature','docdirect');?>" class="paypal_signature" name="paypal_signature" value="<?php echo esc_attr($paypal_signature);?>" />
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12 text-right">
                            <a class="btn btn-primary verify_paypal_button" href="javascript:;"><?php pll_e('Verify','docdirect');?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       </div>
       <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="row">
            <div class="button-wrapper"><button type="submit" class="tg-btn update-email-settings"><?php pll_e('Update','docdirect');?></button></div>
        </div>
       </div>
    </form>
</div>
</div>
<?php

    if ($paypal_username == '' || $paypal_password == '' || $paypal_signature == '') {
        function kt_add_code_footer() {
            if(is_user_logged_in()){?>
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        $('.tg-verify-paypal').modal('toggle');
                    });
                </script>
            <?php }
        }
        // add_action('wp_footer', 'kt_add_code_footer', 99);
    }
$user_featured = get_user_meta($user_identity, 'user_featured', true);
// if ($user_featured == '') {    
    function kt_add_modal_footer() {
    ?>
    <div class="modal fade tg-verify-paypal">
      <div class="tg-modal-content" role="document">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php pll_e( 'Payment Verify' ) ?></h4>
            </div>
            <div class="tg-form-privacy">
                <form action="#" method="post" class="tg-form-modal pp-form">
                <fieldset>
                    <div class="row">
                        
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <span class="tg-privacy-name how_to"><?php pll_e('Paypal API <a target="blank" href="https://www.youtube.com/watch?v=weUVQPKZq98">(How create paypal API? )</a>','docdirect');?></span>
                                </div>
                                <div class="form-group">
                                    <span class="tg-privacy-name"><?php pll_e('Paypal API Username','docdirect');?></span>
                                    <input type="text" placeholder="<?php pll_e('Paypal Username','docdirect');?>" class="paypal_username" name="paypal_username" value="<?php echo esc_attr($paypal_username);?>" />
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <span class="tg-privacy-name"><?php pll_e('Paypal API Password','docdirect');?></span>
                                    <input type="password" placeholder="<?php pll_e('Paypal Password
','docdirect');?>" class="paypal_password" name="paypal_password" value="<?php echo esc_attr($paypal_password);?>" />
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <span class="tg-privacy-name how_to"><?php pll_e('Paypal API Signature <a target="blank" href="https://www.paypal.com/businessprofile/mytools/apiaccess/firstparty/signature">(How get paypal signature info? )</a>','docdirect');?></span>
                                    <input type="password" placeholder="<?php pll_e('Paypal Signature
','docdirect');?>" class="paypal_signature" name="paypal_signature" value="<?php echo esc_attr($paypal_signature);?>" />
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12 text-right">
                            <a class="btn btn-primary verify_paypal_button" href="javascript:;"><?php pll_e('Verify','docdirect');?></a>
                            </div>

                    </div>
                </fieldset>
                </form>
            </div>
      </div>
    </div>
    <?php
    }
    add_action('wp_footer', 'kt_add_modal_footer');
// }


