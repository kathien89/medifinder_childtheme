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
							'key'     => 'bk_user_from',
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
?>
<?php
  if (isset($_GET["appointment_id"])) {
      $post_id = $_GET["appointment_id"];
      $user_to    = get_post_meta($post_id, 'bk_user_to', true);
      $user_reviews = get_post_meta($post_id, 'user_reviews',true);
      if ( $user_reviews != 'done' ){
        function kt_add_code_footer() {
            ?>
                <script type="text/javascript">
                    jQuery(document).ready(function($){
                        $('.tg-formleavereview input[name=user_to]').val(<?php echo $user_to;?>);
                        $('.tg-formleavereview input[name=appointment_id]').val(<?php echo $post_id;?>);
                        $('.tg-review-booking').modal('toggle');
                    });
                </script>
            <?php
        }
        add_action('wp_footer', 'kt_add_code_footer', 99);
      }
  }
?>
<div class="doc-booking-listings dr-bookings">
  <div class="tg-dashboard tg-docappointmentlisting tg-haslayout">
    <div class="tg-heading-border tg-small">
      <h3><?php pll_e('Appointments','docdirect');?></h3>
    </div>
    <form class="tg-formappointmentsearch" action="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'mybookings', $user_identity); ?>" method="get">
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
            <th><?php pll_e('Provider','docdirect');?></th>
            <th><?php pll_e('Subject','docdirect');?></th>
            <th><?php pll_e('More Detail','docdirect');?></th>
            <th><?php pll_e('Booking Status','docdirect');?></th>
            <th><?php pll_e('Cancel Booking','docdirect');?></th>
            <th><?php pll_e('Review Status','docdirect');?></th>
          </tr>
        </thead>
        <tbody>
        <?php 
			$query 		= new WP_Query($args);
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
				$bk_user_from     = get_post_meta($post->ID, 'bk_user_from',true);
				$bk_currency 	  = get_post_meta($post->ID, 'bk_currency', true);
				$bk_paid_amount   = get_post_meta($post->ID, 'bk_paid_amount', true);
				$bk_transaction_status = get_post_meta($post->ID, 'bk_transaction_status', true);

        $complete_status  = get_post_meta($post->ID, 'complete_status',true);
        $confirm_status  = get_post_meta($post->ID, 'confirm_status',true);
        $reminder  = get_post_meta($post->ID, 'reminder',true);

        $provider_id = get_userdata( $bk_user_to );
        $provider_name = kt_get_title_name($provider_id->ID).$provider_id->first_name.' '.$provider_id->last_name;
        $phone_number = get_user_meta($provider_id->ID,'phone_number',true);
        $business_email = get_user_meta($provider_id->ID,'business_email',true);
        $address = get_user_meta($provider_id->ID,'address',true);

        $today = current_time( 'timestamp' );
        $bk_slottime    = get_post_meta($post->ID, 'bk_slottime',true);
        $time = explode('-',$bk_slottime);

        $date_bk = date($date_format,strtotime($bk_booking_date));
        $time_bk = date($time_format,strtotime('2016-01-01 '.$time[0]) );
        $date_time_booking = $date_bk.' '.$time_bk;

      $services_cats = get_user_meta($provider_id->ID , 'services_cats' , true);
      $booking_services = get_user_meta($provider_id->ID , 'booking_services' , true);
				
				$payment_amount  = $bk_currency.' '.$currency_sign.$bk_paid_amount;
				
				$time = explode('-',$bk_slottime);
        
				$trClass	= 'booking-odd';
				if( $counter % 2 == 0 ){
					$trClass	= 'booking-even';
				}  
		  ?>
          <tr class="<?php echo esc_attr( $trClass );?> booking-<?php echo intval( $post->ID );?>">
            <td data-name="id"><?php echo esc_attr( $provider_name );?></td>
            <td data-name="subject"><?php echo esc_attr( $bk_subject );?></td>
            <td data-name="notes"><a class="get-detail" href="javascript:;"><img src="<?php echo get_stylesheet_directory_uri();?>/images/info-icon.svg" alt=""></a></td>
            <td>
              <?php if( isset( $complete_status ) && $complete_status == 'completed' ){?>
                  <span><?php pll_e('Completed','docdirect');?></span>
              <?php }else {?>
                  <?php echo kt_docdirect_prepare_order_status( 'value',$bk_status );?>
              <?php }?>
            </td>
            <td>
                <?php if ($confirm_status == 'no') {?>
                  <a class="tg-btnclose appointment-actioned" href="javascript:;"><i class="fa fa-check"></i><?php pll_e('Cancelled','docdirect');?></a> 
                <?php }else if( $bk_status == 'cancelled' ) {?>
                  <span><?php pll_e('Cancelled');?></span>
                <?php }else if( isset( $bk_status ) && $bk_status == 'approved' && $reminder == 'yes' && $complete_status != 'completed' || $today >= strtotime($date_time_booking)  && $complete_status != 'completed'  ){?>
                  <a class="tg-btnclose kt_action-confirm" data-type="no" data-id="<?php echo intval( $post->ID );?>" href="javascript:;"><?php pll_e('Cancel','docdirect');?></a> 
                <?php }?>
            </td>
            <td>
              <?php
                $user_reviews = get_post_meta($post->ID, 'user_reviews',true);
                if ( $user_reviews == 'done' ){?>
                  <span><?php pll_e('Reviewed');?></span>
                <?php }else {
                  if( $bk_status == 'approved' && isset( $complete_status ) && $complete_status == 'completed' ){?>
                  <a class="btn btn-primary kt_popup_review" data-id="<?php echo intval( $post->ID );?>" data-user_to="<?php echo intval( $bk_user_to );?>" href="javascript:;"><?php pll_e('Review');?></a>
              <?php }else if($bk_status == 'cancelled' || $confirm_status == 'no') {?>
                  <span><?php pll_e('Cancelled');?></span>
              <?php }else {?>
                  <span><?php pll_e('Pending');?></span>
                <?php }
              }?>
            </td>
          </tr>
          <tr class="tg-appointmentdetail bk-elm-hide">
            <td colspan="6">
                <div class="appointment-data-wrap">
                    <ul class="tg-leftcol">
                      <?php
                        $bk_location = get_post_meta($post->ID,'bk_location', true);
                        if ($bk_location != '') {
                          $current_practices = get_user_meta($provider_id->ID , 'user_practices', true);
                          $practice_name = $current_practices[$bk_location]['title'];
                          $basics = $current_practices[$bk_location]['basics'];
                          $socials = $current_practices[$bk_location]['socials'];

                          $room_floor = $basics['room_floor'];
                          $address = $basics['address'];
                          $phone_number = $basics['phone_number'];
                          $business_email = $basics['business_email'];
                          $user_url = $basics['user_url'];
                          $fax = $basics['fax'];
                          $skype = $socials['skype'];
                        }else {
                          $room_floor     = $provider_id->room_floor;
                          $address        = $provider_id->address;
                          $phone_number   = $provider_id->phone_number;
                          $business_email   = $provider_id->business_email;
                          $user_url     = $provider_id->user_url;
                          $fax     = $provider_id->fax;
                          $skype     = $provider_id->skype;
                        }
                      ?>
                      <li> 
                            <strong><?php pll_e('tracking id','docdirect');?>:</strong> 
                            <span><?php echo esc_attr( $bk_code );?></span> 
                      </li>
                      <li>
                            <strong><?php pll_e('Healthcare','docdirect');?>:</strong>
                            <?php if( !empty( $provider_id->tagline ) ){?>
                                <span><?php echo esc_attr( $provider_id->tagline );?></span>
                            <?php }?>
                      </li>
                      <li> <strong><?php pll_e('Doctor Name','docdirect');?>:</strong> <span><?php echo esc_attr( $provider_name );?></span> </li>
                      <li> 
                            <strong><?php pll_e('Service','docdirect');?>:</strong>
                            <?php if( !empty( $booking_services[$bk_service] ) ){?>
                                <span><?php echo esc_attr( $booking_services[$bk_service]['title'] );?></span>
                            <?php }?>
                      </li>
                      <li> <strong><?php pll_e('Phone','docdirect');?>:</strong> <span><?php echo esc_attr( $phone_number );?></span> </li>
                      <li> <strong><?php pll_e('Email','docdirect');?>:</strong> <span><?php echo esc_attr( $business_email );?></span> </li>
                      <li> 
                            <strong><?php pll_e('Appointment date','docdirect');?>:</strong> 
                            <?php if( !empty( $bk_booking_date ) ){?>
                                <span><?php echo date($date_format,strtotime($bk_booking_date));?></span> 
                            <?php }?>
                      </li>
                      <li> 
                            <strong><?php pll_e('Meeting Time','docdirect');?>:</strong> 
                            <span><?php echo date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );?>&nbsp;-&nbsp;<?php echo date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) );?></span> 
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
                    </ul>
                    <div class="tg-rightcol"> <strong><?php pll_e('notes:','docdirect');?></strong>
                      <?php if( !empty( $bk_booking_note ) ){?>
                          <div class="tg-description">
                            <p><?php echo esc_attr( $bk_booking_note );?></p>
                          </div>
                      <?php }?>
                      <h3><?php pll_e('Contact Details');?></h3>
                      <?php if( $bk_status == 'approved') {?>
                      <ul class="tg-doccontactinfo">
                        <?php if( !empty( $practice_name ) ) {?>
                            <li> <i class="fa fa-home"></i> <address><?php echo esc_attr( $practice_name );?></address> </li>
                        <?php }?>
                        <?php if( !empty( $room_floor ) ) {?>
                            <li> <i class="fa fa-home"></i> <address><?php echo esc_attr( $room_floor );?></address> </li>
                        <?php }?>
                        <?php if( !empty( $address ) ) {?>
                          <li> <i class="fa fa-map-marker"></i> <address><?php echo esc_attr( $address );?></address> </li>
                        <?php }?>
                        <?php if( !empty( $business_email ) 
                      &&
                      !empty( $privacy['email'] )
                      && 
                      $privacy['email'] == 'on'
                ) {?>
                            <li><i class="fa fa-envelope-o"></i><a href="mailto:<?php echo esc_attr( $business_email );?>?subject:<?php pll_e('Hello');?>"><?php echo esc_attr( $business_email );?></a></li>
                        <?php }?>
                        <?php if( !empty( $phone_number ) ) {?>
                          <li> <i class="fa fa-phone"></i> <span><?php echo esc_attr( $phone_number );?></span> </li>
                        <?php }?>
                        <?php if( !empty( $fax ) ) {?>
                          <li><i class="fa fa-fax"></i> <span><?php echo esc_attr( $fax );?></span> </li>
                        <?php }?>
                        <?php if( !empty( $skype ) ) {?> 
                          <li><i class="fa fa-skype"></i><span><?php echo esc_attr( $skype );?></span></li>
                        <?php }?>
                        <?php if( !empty( $user_url ) ) {?>
                            <li><i class="fa fa-link"></i><a href="<?php echo esc_attr( $provider_id->user_url);?>" target="_blank"><?php echo esc_attr( $provider_id->user_url);?></a></li>
                        <?php }?>
                      </ul>
                        <a target="_blank" class="tg-btn tg-btn-lg" href="http://maps.google.com/maps?saddr=&amp;daddr=<?php echo esc_attr( $address );?>" target="_blank"><?php pll_e('open map');?></a>
                      <?php }else {
                        pll_e('Appointment contact details will show once your provider approves the booking.');
                      }?>
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
<script type="text/template" id="tmpl-status-approved">
	<a class="tg-btncheck appointment-actioned fa fa-check" href="javascript:;"><?php pll_e('Approved','docdirect');?></a> 
</script>

<?php

function kt_add_modal_footer() {
docdirect_enque_rating_library();//rating
?>

<div class="modal fade tg-review-booking" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog  modal-lg tg-modal-content1" role="document">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title"><?php pll_e('Review Appointment');?></h4>
    </div>
    <div class="tg-review-form">

                  <div class="message_contact  theme-notification"></div>
                  <form class="tg-formleavereview form-review">
                    <fieldset>
                      <div class="row">
                        <div class="col-sm-4">
                            <label>Recommendation</label>
                            <div class="tg-stars"><div id="jRate"></div><span class="your-rate"><strong><?php pll_e('Very likely');?></strong></span></div>
                            <input class="detail_rating" type="hidden" name="detail_rating[recommendation]" value="<?php pll_e('Very likely');?>">
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate").jRate({
                                        rating: 5,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_1);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_2);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_3);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_4);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_5);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_1);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_2);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_3);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_4);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_5);
                                                jQuery('.detail_rating').val(rating_vars.recommendation.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="col-sm-4">
                            <label>Waiting Time</label>
                            <div class="tg-stars"><div class="jRate" id="jRate3"></div><span class="your-rate3"><strong><?php pll_e('30 mins to an hour');?></strong></span></div>
                            <input class="detail_rating3" type="hidden" name="detail_rating[waiting_time]" value="<?php pll_e('30 mins to an hour');?>">
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate3").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating3').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_1);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_2);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_3);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_4);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_5);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating3').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_1);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_2);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_3);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_4);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_5);
                                                jQuery('.detail_rating3').val(rating_vars.waiting_time.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="col-sm-4">
                            <label>Facilities</label>
                            <div class="tg-stars"><div class="jRate" id="jRate5"></div><span class="your-rate5"><strong><?php pll_e('Satisfactory');?></strong></span></div>
                            <input class="detail_rating5" type="hidden" name="detail_rating[facilities]" value="<?php pll_e('30 mins to an hour');?>">
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate5").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating5').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_1);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_2);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_3);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_4);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_5);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating5').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_1);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_2);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_3);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_4);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_5);
                                                jQuery('.detail_rating5').val(rating_vars.facilities.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <label>Bedside Manner</label>
                            <div class="tg-stars"><div class="jRate" id="jRate2"></div><span class="your-rate2"><strong><?php pll_e('Satisfactory');?></strong></span></div>
                            <input class="detail_rating2" type="hidden" name="detail_rating[bedside_manner]" value="<?php pll_e('Satisfactory');?>">
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate2").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating2').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_1);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_2);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_3);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_4);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_5);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating2').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_1);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_2);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_3);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_4);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_5);
                                                jQuery('.detail_rating2').val(rating_vars.bedside_manner.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="col-sm-4">
                            <label>Supporting Staff</label>
                            <div class="tg-stars"><div class="jRate" id="jRate4"></div><span class="your-rate4"><strong><?php pll_e('Satisfactory');?></strong></span></div>
                            <input class="detail_rating4" type="hidden" name="detail_rating[supporting_staff]" value="<?php pll_e('Satisfactory');?>">
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate4").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating4').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_1);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_2);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_3);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_4);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_5);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating4').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_1);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_2);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_3);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_4);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_5);
                                                jQuery('.detail_rating4').val(rating_vars.supporting_staff.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <input type="text" name="user_subject" class="form-control" placeholder="<?php esc_attr_e('Subject');?>">
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-group">
                            <textarea class="form-control" name="user_description" placeholder="<?php esc_attr_e('Review Description *');?>"></textarea>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <button class="tg-btn kt_make-review_appointment" type="submit"><?php pll_e('Submit Review');?></button>
                          <input type="hidden" name="redirect" class="redirect" value="<?php echo $redirect;?>" />
                          <input type="hidden" name="user_rating[recommendation]" class="user_rating" value="3" />
                          <input type="hidden" name="user_rating[bedside_manner]" class="user_rating2" value="3" />
                          <input type="hidden" name="user_rating[waiting_time]" class="user_rating3" value="3" />
                          <input type="hidden" name="user_rating[supporting_staff]" class="user_rating4" value="3" />
                          <input type="hidden" name="user_rating[facilities]" class="user_rating5" value="3" />
                          <?php
                          if (isset($_GET["appointment_id"])) {
                            $post_id = $_GET["appointment_id"];
                            $user_to    = get_post_meta($post_id, 'bk_user_to', true);
                          }
                          ?>
                          <input type="hidden" name="user_to" class="user_to" value="<?php echo esc_attr( $user_to );?>" />
                          <input type="hidden" name="appointment_id" class="user_to" value="<?php echo esc_attr( $post_id );?>" />
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
