<?php
/**
 * User Invoices
 * return html
 */

global $current_user, $wp_roles,$userdata,$post,$paged;
$dir_obj	= new DocDirect_Scripts();
$user_identity	= $current_user->ID;
$url_identity	= $user_identity;

if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
	$url_identity	= $_GET['identity'];
}

if (function_exists('fw_get_db_settings_option')) {
	$currency_select = fw_get_db_settings_option('currency_select');
} else{
	$currency_select = 'USD';
}


if (empty($paged)) $paged = 1;
$limit = get_option('posts_per_page');


$meta_query_args[] = array(
							'key'     => 'bk_user_to',
							'value'   => $current_user->ID,
							'compare'   => '=',
							'type'	  => 'NUMERIC'
						);

if( !empty( $_GET['by_date'] ) ){
	$meta_query_args[] = array(
							'key'     => 'bk_timestamp',
							'value'   => strtotime($_GET['by_date']),
							'compare'   => '=',
							'type'	  => 'NUMERIC'
						);
}
										

$show_posts    = get_option('posts_per_page') ? get_option('posts_per_page') : '-1';           
$args 		= array( 'posts_per_page' => -1, 
					 'post_type' => 'docappointments', 
					 'post_status' => 'publish', 
					 'ignore_sticky_posts' => 1,
					);
						
if( !empty( $meta_query_args ) ) {
	$query_relation = array('relation' => 'AND',);
	$meta_query_args	= array_merge( $query_relation,$meta_query_args );
	$args['meta_query'] = $meta_query_args;
}

$query 		= new WP_Query( $args );
$count_post = $query->post_count;   
$args 		= array( 'posts_per_page' => $show_posts, 
					 'post_type' => 'docappointments', 
					 'post_status' => 'publish', 
					 'ignore_sticky_posts' => 1,
					 'order'	=> 'DESC',
					 'orderby'	=> 'ID',
					 'paged' => $paged, 
           'lang' => 'en'
					);


if( !empty( $meta_query_args ) ) {
	$query_relation = array('relation' => 'AND',);
	$meta_query_args	= array_merge( $query_relation,$meta_query_args );
	$args['meta_query'] = $meta_query_args;
}

$dir_profile_page = '';
if (function_exists('fw_get_db_settings_option')) {
	$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
}

$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';	

$thank_you          = get_user_meta( $user_identity, 'thank_you', true);
$schedule_message      = get_user_meta( $user_identity, 'schedule_message', true);

?>

<?php
$db_directory_type   = get_user_meta( $user_identity, 'directory_type', true);
    $terms = get_the_terms($db_directory_type, 'group_label');
    $current_group_label_slug = $terms[0]->slug;
    $user_premium = get_user_meta($user_identity , 'user_premium' , true);
    if($current_group_label_slug != 'medical-centre') {
        $current_option = get_option( $user_premium, true );
    }else {
        $current_option = get_option( 'company_'.$user_premium, true );
    }
?>
<?php if($current_option['patient_bookings'] != ''){?>
<div class="doc-booking-listings dr-bookings">
  <div class="tg-dashboard tg-docappointmentlisting tg-haslayout">

      <form action="#" name="email-settings" class="email-settings">
        <div class="tg-formsection">
            <div class="email-settings-tabs">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Booking Message','docdirect');?></h3>
                </div>
                <div class="appointment-cancelled  booking-email-wrap">
                    <div class="tg-small doc-tab-link">
                        <h3><?php pll_e('Schedule Message','docdirect');?></h3>
                    </div>
                    <div class="tab-data">
                        <div class="email-params">
                            <p><?php pll_e('This message will be shown to the patient at the online date & time booking stage. You can provide patients with your additonal booking requirements or contact information if necessary.','docdirect');?></p>
                        </div>
                        <div class="email-contents">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <?php 
                                            $default_message = pll__('Please select the date and time which suits your schedule. We will contact you if any changes are necessary. A direct refund will be issued (only online booking) if we have to decline the selected time slot. If you wish to book a specific time slot not presented here, please do not hesitate to contact us at +852 XXXX XXXX , or email us at insertyouremail@info.com.', 'docdirect');
                                            $schedule_message = !empty($schedule_message) ? $schedule_message : $default_message;
                                            $settings = array( 
                                                'editor_class' => 'schedule_message', 
                                                'teeny' => true, 
                                                'media_buttons' => false, 
                                                'textarea_rows' => 10,
                                                'quicktags' => false,
                                                'editor_height' => 300
                                                
                                            );
                                            
                                            wp_editor( $schedule_message, 'schedule_message', $settings );
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="appointment-cancelled  booking-email-wrap">
                    <div class="tg-small doc-tab-link">
                        <h3><?php pll_e('Thank You Message','docdirect');?></h3>
                    </div>
                    <div class="tab-data">
                        <div class="email-params">
                            <p><?php pll_e('This message will be shown to the patient upon completing the online booking process.','docdirect');?></p>
                        </div>
                        <div class="email-contents">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <?php 
                                            $default_thank_you = pll__('Thank you for booking with us, If you have any other questions please contact us at +852 XXXX XXXX , or email us at insertyouremail@info.com.', 'docdirect');
                                            $thank_you = !empty($thank_you) ? $thank_you : $default_thank_you;
                                            $settings = array( 
                                                'editor_class' => 'thank_you', 
                                                'teeny' => true, 
                                                'media_buttons' => false, 
                                                'textarea_rows' => 10,
                                                'quicktags' => false,
                                                'editor_height' => 300
                                                
                                            );
                                            
                                            wp_editor( $thank_you, 'thank_you', $settings );
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="update-email-settings"><?php pll_e('Update','docdirect');?></button>
        </div>
      </form>

    <div class="tg-heading-border tg-small">
      <h4><?php pll_e('Appointments','docdirect');?></h4>
    </div>
    <form class="tg-formappointmentsearch" action="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'bookings', $user_identity); ?>" method="get">
      <fieldset>
        <h4><?php pll_e('Search Here','docdirect');?>:</h4>
        <div class="form-group">
          <input type="hidden" class="" value="bookings" name="ref">
          <input type="hidden" class="" value="<?php echo intval( $user_identity ); ?>" name="identity">
          <input type="text" class="form-control booking-search-date" value="<?php echo isset( $_GET['by_date'] ) && !empty( $_GET['by_date'] ) ? $_GET['by_date'] : '';?>" name="by_date" placeholder="<?php pll_e('Search by date','docdirect');?>">
          <button type="submit"><i class="fa fa-search"></i></button>
        </div>
      </fieldset>
    </form>
    <div class="tg-appointmenttable">
      <table class="table">
        <thead class="thead-inverse">
          <tr>
            <th><?php pll_e('id.','docdirect');?></th>
            <th><?php pll_e('Subject','docdirect');?></th>
            <th><?php pll_e('Phone','docdirect');?></th>
            <th><?php pll_e('More Detail','docdirect');?></th>
            <th><?php pll_e('Patient Status','docdirect');?></th>
            <th></th>
            <th><?php pll_e('Action Complete','docdirect');?></th>
          </tr>
        </thead>
        <tbody>
        <?php 
			$query 		= new WP_Query($args);
			$services_cats = get_user_meta($user_identity , 'services_cats' , true);
			$booking_services = get_user_meta($user_identity , 'booking_services' , true);
			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
      $currency_sign       = fw_get_db_settings_option('currency_sign');
			
			$counter	= 0;
			if( $query->have_posts() ):
			 while($query->have_posts()) : $query->the_post();
			 	global $post;
				
				$counter++;
			    $bk_code          = get_post_meta($post->ID, 'bk_code',true);
				$bk_category      = get_post_meta($post->ID, 'bk_category',true);
				$bk_service       = get_post_meta($post->ID, 'bk_service',true);
				$bk_booking_date  = get_post_meta($post->ID, 'bk_booking_date',true);
				$bk_slottime 	  = get_post_meta($post->ID, 'bk_slottime',true);
				$bk_subject       = get_post_meta($post->ID, 'bk_subject',true);
				$bk_username      = get_post_meta($post->ID, 'bk_username',true);
				$bk_userphone 	  = get_post_meta($post->ID, 'bk_userphone',true);
				$bk_useremail     = get_post_meta($post->ID, 'bk_useremail',true);
				$bk_booking_note  = get_post_meta($post->ID, 'bk_booking_note',true);
				$bk_payment       = get_post_meta($post->ID, 'bk_payment',true);
				$bk_user_to       = get_post_meta($post->ID, 'bk_user_to',true);
				$bk_timestamp     = get_post_meta($post->ID, 'bk_timestamp',true);
        $bk_status        = get_post_meta($post->ID, 'bk_status',true);
        $usercard        = get_post_meta($post->ID, 'usercard',true);

				$bk_user_from     = get_post_meta($post->ID, 'bk_user_from',true);
				$bk_currency 	  = get_post_meta($post->ID, 'bk_currency', true);
				$bk_paid_amount   = get_post_meta($post->ID, 'bk_paid_amount', true);
        $bk_transaction_status = get_post_meta($post->ID, 'bk_transaction_status', true);
        $gender = get_user_meta($bk_user_from,'gende',true);

        $today = current_time( 'timestamp' );
        $bk_slottime    = get_post_meta($post->ID, 'bk_slottime',true);
        $time = explode('-',$bk_slottime);

        $date_bk = date($date_format,strtotime($bk_booking_date));
        $time_bk = date($time_format,strtotime('2016-01-01 '.$time[0]) );
        $date_time_booking = $date_bk.' '.$time_bk;

				$patient_insurers = get_post_meta($post->ID, 'patient_insurers', true);
				
        $complete_status  = get_post_meta($post->ID, 'complete_status',true);
        $confirm_status  = get_post_meta($post->ID, 'confirm_status',true);

        $payment_amount  = $bk_currency.' '.$currency_sign.$bk_paid_amount;
				
				$time = explode('-',$bk_slottime);
				
				$trClass	= 'booking-odd';
				if( $counter % 2 == 0 ){
					$trClass	= 'booking-even';
				}  
		  ?>
          <tr class="<?php echo esc_attr( $trClass );?> booking-<?php echo intval( $post->ID );?>">
            <td data-name="id"><?php echo esc_attr( $bk_code );?></td>
            <td data-name="subject"><?php echo esc_attr( $bk_subject );?></td>
            <td data-name="phone"><?php echo esc_attr( $bk_userphone );?></td>
            <td data-name="notes"><a class="get-detail" href="javascript:;"><img src="<?php echo get_stylesheet_directory_uri();?>/images/info-icon.svg"></a></td>
            <td>
              <?php if( isset( $confirm_status ) && $confirm_status == 'no' ) {?>
                  <span class="cancelled"><?php pll_e('Cancelled');?></span>
              <?php }else if ( isset( $bk_status ) && $bk_status == 'approved' && $complete_status != 'completed' ) {?>
                  <span class="confirmed"><?php pll_e('Confirmed');?></span>
              <?php }else if ( isset( $complete_status ) && $complete_status == 'completed' ) {?>
                  <span class="finished"><?php pll_e('Finished');?></span>
              <?php }else {?>
                  <span class="none"><?php pll_e('None');?></span>
              <?php }?>
            </td>
            <td>
              <?php if( isset( $confirm_status ) && $confirm_status == 'no' ) {?>
                  <span class="cancelled"><?php pll_e('Cancelled');?></span>
              <?php }else if( isset( $bk_status ) && $bk_status == 'approved' ){?>
                	<a class="tg-btncheck appointment-actioned" href="javascript:;"><i class="fa fa-check"></i><?php pll_e('Approved','docdirect');?></a> 
                <?php }else if( isset( $bk_status ) && $bk_status == 'cancelled' ){?>
                	<a class="tg-btncheck appointment-actioned cancelled" href="javascript:;"><i class=" fa fa-times"></i><?php pll_e('Cancelled','docdirect');?></a> 
                <?php }else {?>
                     <a class="tg-btncheck open_approve_modal" data-type="approve" data-id="<?php echo intval( $post->ID );?>"><?php pll_e('Approve','docdirect');?></a>
                     <!-- <a class="tg-btncheck kt_get-process" data-type="approve" data-id="<?php //echo intval( $post->ID );?>" href="javascript:;"><?php //pll_e('Approve','docdirect');?></a>  -->
                     <a class="tg-btnclose kt_cancel_booking" data-type="cancel" data-id="<?php echo intval( $post->ID );?>" href="javascript:;"><?php pll_e('Cancel','docdirect');?></a>
                <?php }?>
               
            </td>
            <td>
              <?php if( isset( $complete_status ) && $complete_status == 'completed' ){?>
                  <a class="tg-btncheck appointment-actioned" href="javascript:;"><?php pll_e('Completed','docdirect');?></a> 
              <?php }else {?>
                <?php if( isset( $bk_status ) && $bk_status == 'approved' && $confirm_status != 'no' && $today >= strtotime($date_time_booking) ){?>
                  <a class="tg-btncheck kt_action-complete" data-id="<?php echo intval( $post->ID );?>" href="javascript:;"><?php pll_e('Complete','docdirect');?></a> 
                <?php }?>
              <?php }?>
              <?php if( isset( $confirm_status ) && $confirm_status == 'no' ) {?>
                  <a class="tg-btnclose kt_get-process" data-type="cancel" data-id="<?php echo intval( $post->ID );?>" href="javascript:;"><?php pll_e('Re-Post','docdirect');?></a>
              <?php }?>
            </td>
          </tr>
          <tr class="tg-appointmentdetail bk-elm-hide">
            <td colspan="7">
                <div class="appointment-data-wrap">
                    <ul class="tg-leftcol">
                      <li> 
                            <strong><?php pll_e('tracking id','docdirect');?>:</strong> 
                            <span><?php echo esc_attr( $bk_code );?></span> 
                      </li>
                      <li>
                            <strong><?php pll_e('Insurance','docdirect');?>:</strong>
                            <?php if( !empty( $patient_insurers ) ){?>
                                <span><?php echo $patient_insurers;?></span>
                            <?php }?>
                      </li>
                      <li> <strong><?php pll_e('Patient Name','docdirect');?>:</strong> <span><?php echo esc_attr( $bk_username );?></span> </li>
                      <li> 
                            <strong><?php pll_e('Service','docdirect');?>:</strong>
                            <?php if( !empty( $booking_services[$bk_service] ) ){?>
                                <span><?php echo esc_attr( $booking_services[$bk_service]['title'] );?></span>
                            <?php }?>
                      </li>
                      <li> <strong><?php pll_e('Phone','docdirect');?>:</strong> <span><?php echo esc_attr( $bk_userphone );?></span> </li>
                      <li> 
                            <strong><?php pll_e('Meeting Time','docdirect');?>:</strong> 
                            <span><?php echo date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );?>&nbsp;-&nbsp;<?php echo date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) );?></span> 
                      </li>
                      <li> <strong><?php pll_e('Patient Email','docdirect');?>:</strong> <span><?php echo esc_attr( $bk_useremail );?></span> </li>
                      <li> <strong><?php pll_e('HKID','docdirect');?>:</strong> <span><?php echo esc_attr( $usercard );?></span> </li>
                      <li> <strong><?php pll_e('Gender','docdirect');?>:</strong> <span><?php echo esc_attr( $gender );?></span> </li>
                      <li> 
                            <strong><?php pll_e('Appointment date','docdirect');?>:</strong> 
                            <?php if( !empty( $bk_booking_date ) ){?>
                                <span><?php echo date($date_format,strtotime($bk_booking_date));?></span> 
                            <?php }?>
                      </li>
                      <li> 
                            <strong><?php pll_e('Status','docdirect');?>:</strong>
                            <span><?php echo esc_attr( $bk_status );?></span> 
                      </li>
                      <li> 
                            <strong><?php pll_e('Payment Type','docdirect');?>:</strong>
                            <span><?php echo esc_attr( docdirect_prepare_payment_type( 'value',$bk_payment ) );?></span> 
                      </li>
                      <?php if( !empty( $payment_amount ) ){?>
                          <li> 
                                <strong><?php pll_e('Appointment Fee','docdirect');?>:</strong>
                                <span><?php echo esc_attr( $payment_amount );?></span>
                          </li>
                      <?php }?>
                      <?php if( !empty( $bk_transaction_status ) ){?>
                          <li> 
                             <strong><?php pll_e('Payment Status','docdirect');?>:</strong>
                             <span><?php echo esc_attr( docdirect_prepare_order_status( 'value',$bk_transaction_status ) );?></span>
                          </li>
                      <?php }?>
                      <?php
                        $hkid_passport = get_user_meta($bk_user_from,'card_number',true);
                      ?>
                      <?php if( !empty( $hkid_passport ) ){?>
                          <li> 
                             <strong><?php pll_e('HKID Card / Passport #','docdirect');?>:</strong>
                             <span><?php echo esc_attr( $hkid_passport );?></span>
                          </li>
                      <?php }?>
                    </ul>
                    <div class="tg-rightcol"> <strong><?php pll_e('notes:','docdirect');?></strong>
                      <?php if( !empty( $bk_booking_note ) ){?>
                          <div class="tg-description">
                            <p><?php echo esc_attr( $bk_booking_note );?></p>
                          </div>
                      <?php }?>
                    </div>
                  </div>
              </td>
          </tr>
          <?php 
		  endwhile; wp_reset_postdata(); 
		  else:
		  ?>
		  <tr>
			<td colspan="6">
				<?php DoctorDirectory_NotificationsHelper::informations(esc_html__('No appointments found.','docdirect'));?>
			</td>
		  </tr>
		<?php endif;?>
        </tbody>
      </table>
      <div class="col-md-xs-12">
		<?php 
            if( $count_post > $limit ) {
                docdirect_prepare_pagination($count_post,$limit);
            }
        ?>
      </div>
    </div>
  </div>
</div>
<?php }else {?>
<div class="tg-myaccount tg-haslayout">
  <?php pll_e('This feature is limited, please contact to admin for active feature');?>
</div>
<?php }?>

<script type="text/template" id="tmpl-status-approved">
	<a class="tg-btncheck appointment-actioned fa fa-check" href="javascript:;"><?php pll_e('Approved','docdirect');?></a> 
</script>
<?php

function kt_add_modal_footer() {
  global $current_user, $wp_roles,$userdata,$post;
  $user_identity  = $current_user->ID;
?>

<div class="modal fade tg-approve-booking" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="tg-modal-content" role="document">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title"><?php pll_e('Select Preferred Appointment Location');?></h4>
    </div>
    <div class="tg-approve-form">
        <form id="doctor_approve_booking" action="#" method="post" class="tg-form-modal invite-form">
          <fieldset>         
            <!-- <div class="tg-box">
                <div class="tg-radio">
                    <input id="default" value="" type="radio" name="location">
                    <label for="default"><?php //echo get_user_meta($user_identity,'address',true);?></label>
                </div>
            </div> -->
            <center class="warning_mes"><p>
              <i class="fa fa-exclamation-triangle"></i><strong><?php pll_e('Warning');?></strong><br>
              <?php pll_e('Please phone and confirm with patients prior to changing your default booking location.');?>
            </p></center>
            <?php
            $i=0;
            $current_practices = get_user_meta($user_identity, 'user_practices', true);
            foreach ($array_keys = array_keys($current_practices) as $key => $value) {
              $i++;
              if( $i == 1 ) {
                  $activeTab  = 'checked=checked';
                  $icon = ' | <i class="fa fa-star"></i>';
                  $cl='default';
              }else {
                  $activeTab  = '';
                  $icon = '';
                  $cl='';
              }
              ?>              
              <div class="tg-box">
                  <div class="tg-radio">
                      <input class="<?php echo $cl;?>" <?php echo $activeTab;?> id="<?php echo esc_attr( $value );?>" value="<?php echo esc_attr( $value );?>" type="radio" name="location">
                      <label for="<?php echo esc_attr( $value );?>"><?php echo $current_practices[$value]['title'].$icon?></label>
                  </div>
              </div>
              <?php
            }
            ?>
            <div class="row">
              <div class="col-xs-6">
                <a class="tg-btn" class="close" data-dismiss="modal"><?php pll_e('Go Back','docdirect');?></a> 
              </div>
              <div class="col-xs-6">
                <a class="tg-btn kt_get-process" data-type="approve" data-id="" href="javascript:;"><?php pll_e('Approve','docdirect');?></a> 
              </div>
            </div>
            <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
          </fieldset>
        </form>
    </div>
  </div>
</div>

<div class="modal fade tg-cancel-booking" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="tg-modal-content" role="document">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title"><?php pll_e('Cancel Appointment');?></h4>
    </div>
    <div class="tg-cancel-form">

        <form id="doctor_cancel_booking" action="#" method="post" class="tg-form-modal invite-form">
        <fieldset>
          <div class="form-group">
            <label for="desc"><?php pll_e( 'Description' ) ?></label>
            <textarea name="desc" class="form-control"><?php pll_e( '' ) ?></textarea>
          </div>
          <div class="form-group">
            <input type="hidden" name="appointment_id">
            <button type="button" class="tg-btn"><?php pll_e('Submit');?></button>
          </div>
          <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
        </fieldset>
        </form>
    </div>
  </div>
</div>
<?php
}
add_action('wp_footer', 'kt_add_modal_footer');
