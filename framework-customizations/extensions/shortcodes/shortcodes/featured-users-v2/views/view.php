<?php
if (!defined('FW'))
    die('Forbidden');
/**
 * @var $atts
 */
$today = time();
$users_type	= $atts['user_type'];
$show_users	= !empty( $atts['show_users'] ) ? $atts['show_users'] : 10;
$order		 = !empty( $atts['order'] ) ? $atts['order'] : 'DESC';
$uniq_flag = fw_unique_increment();
$query_args	= array(
					'role'  => 'professional',
					'order' => $order,
					'number' => $show_users 
				 );

if( isset( $users_type ) && !empty( $users_type ) && $users_type !='all' ) {
	$meta_query_args[] = array(
						'key'     => 'directory_type',
						'value'   => $users_type,
						'compare' => '='
					);
}

  $query_relation = array('relation' => 'OR',);
  $user_practices_args  = array();
  $user_practices_args[] = array(
              'key'     => 'user_practices',
              'compare' => 'EXISTS'
            );
  
  $meta_query_args[]  = array_merge( $query_relation,$user_practices_args );

//Verify user
$meta_query_args[] = array(
						'key'     => 'verify_user',
						'value'   => 'on',
						'compare' => '='
					);
$today = time();
$meta_query_args[] = array(
            'key'     => 'user_featured',
            'value'   => $today,
            'type' => 'numeric',
            'compare' => '>'
            );
/*
$meta_query_args[] = array(
						'key'     => 'user_featured',
						'value'   => $today,
						'type' => 'numeric',
						'compare' => '>'
					);*/

if( !empty( $meta_query_args ) ) {
	$query_relation = array('relation' => 'AND',);
	$meta_query_args	= array_merge( $query_relation,$meta_query_args );
	$query_args['meta_query'] = $meta_query_args;
}
																					
$query_args['meta_key']	   = 'user_featured';
$query_args['orderby']	   = 'meta_value';	
$user_query  = new WP_User_Query($query_args);	

$flag	= rand(1,9999);	
?>
<div class="sc-featured-users-v2">
    <div class="col-lg-offset-2 col-lg-8 col-md-offset-1 col-md-10 col-sm-offset-0 col-sm-12 col-xs-12">
      <div class="doc-section-head">
        <?php if ( !empty($atts['heading']) && !empty($atts['heading']) ) { ?>
        <div class="doc-section-heading">
          <?php if (!empty($atts['heading'])) { ?>
                <h2><?php echo esc_attr($atts['heading']); ?></h2>
          <?php } ?>
          <?php if (!empty($atts['sub_heading'])) { ?>
                <span><?php echo esc_attr($atts['sub_heading']); ?></span>
          <?php } ?>
        </div>
        <?php } ?>
        <?php if (!empty($atts['description'])) { ?>
            <div class="doc-description">
                <p><?php echo esc_attr($atts['description']); ?></p>
            </div>
        <?php } ?>
      </div>
    </div>
    <?php if ( ! empty( $user_query->results ) ) {?>
	<div class="doc-featurelisting">
      <div id="doc-featureslider-<?php echo esc_attr( $flag );?>" class="doc-featureslider owl-carousel">
		<?php
            if ( ! empty( $user_query->results ) ) {
                if( isset( $directory_type ) && !empty( $directory_type ) ) {
                    $title = get_the_title($directory_type);
                    $postdata = get_post($directory_type); 
                    $slug 	 = $postdata->post_name;
                } else{
                    $title = '';
                    $slug = '';
                }

                foreach ( $user_query->results as $user ) {
                    
                    $current_info = kt_get_current_active_info_doctor($user->ID);
                      $u_latitude    = '';
                      $u_longitude    = '';
                      $address = '';
                    if ($current_info) {
                      $basics = $current_info['basics'];
                      $u_latitude    = $basics['latitude'];
                      $u_longitude    = $basics['longitude'];
                      $business_email = $basics['business_email'];
                      $phone_number = $basics['phone_number'];
                      $address = $basics['address'];
                      $fax = $basics['fax'];
                      $mtr_exit = $basics['mtr_exit'];
                    }

                    $directory_type = get_user_meta( $user->ID, 'directory_type', true);
                    $dir_map_marker = fw_get_db_post_option($directory_type, 'dir_map_marker', true);
                    $reviews_switch    = fw_get_db_post_option($directory_type, 'reviews', true);
                    $featured_date  = get_user_meta($user->ID, 'user_featured', true);
                    $current_date   = date('Y-m-d H:i:s');
                    $avatar = apply_filters(
                            'docdirect_get_user_avatar_filter',
                             docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
                             array('width'=>150,'height'=>150) //size width,height
                        );
                    if(kt_is_company($user->ID)){
                      $company_logo_id = get_user_meta($user->ID, 'userprofile_company_logo', true);
                      if ( isset( $company_logo_id ) && !empty( $company_logo_id ) ) {
                        $avatar = docdirect_get_image_source($company_logo_id,full,full);
                      }
                    }
                    $business_email = get_user_meta( $user->ID, 'business_email', true);
                        
                    $privacy		= docdirect_get_privacy_settings($user->ID); //Privacy settin
                    
                    if( !empty( $u_latitude ) && !empty( $u_longitude ) ) {
                        $directories_array['latitude']	 = $u_latitude;
                        $directories_array['longitude']	= $u_longitude;
                        $directories_array['fax']		  = $fax;
                        $directories_array['mtr_exit']     = $mtr_exit;
                        $directories_array['description']  = $user->description;
                        $directories_array['title']		= $user->display_name;
                        $directories_array['name']	 	 = kt_get_title_name($user->ID).$user->first_name.' '.$user->last_name;
                        $directories_array['email']	 	= $business_email;
                        $directories_array['phone_number'] = $phone_number;
                        $directories_array['address']	  = $address;
                        $directories_array['group']		= $slug;
                        $featured_string   = $featured_date;
                        $current_string	= strtotime( $current_date );
                        $review_data	= kt_docdirect_get_everage_rating ( $user->ID );
                        $get_username	= docdirect_get_username( $user->ID );
                        // $banner = docdirect_get_user_banner(array('width'=>150,'height'=>150), $user->ID);
                        $banner_id = get_user_meta($user->ID, 'userprofile_banner_mobile', true);                        
                        if ( isset( $banner_id ) && !empty( $banner_id ) ) {
                          $banner = docdirect_get_image_source($banner_id,270,270);
                        }else {
                          $banner = get_stylesheet_directory_uri().'/images/doctor-banner-default.jpg';
                        }
                    ?>
                  <div class="doc-featurelist item">
                                    <figure class="doc-featureimg" style="background-image: url(<?php echo $banner;?>)"> 
                                        <?php if( isset( $featured_string ) && $featured_string > $current_string ){?>
                                            <?php //kt_docdirect_get_featured_tag(true, $user->ID, 'v2');?>
                                        <?php }?>
                                        <a href="<?php echo get_author_posts_url($user->ID); ?>" class="list-avatar"><img width="150" height="150" src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
                                        <div class="clearfix"></div>
                                        <?php docdirect_get_verified_tag(true,$user->ID,'','v2');?>
                                        <h2><a href="<?php echo get_author_posts_url($user->ID); ?>" class="list-avatar1"><?php echo ( $get_username );?></a></h2>
                                        <?php if( !empty( $user->tagline ) ) {?>
                                            <span><?php echo esc_attr( $user->tagline );?></span>
                                        <?php }?>
                                        <ul class="doc-matadata">
                                          
                                          <li><?php docdirect_get_likes_button($user->ID);?></li>
                                           <li><?php docdirect_get_wishlist_button($user->ID,true,'v2');?></li>
                                          <?php
                                             if( isset( $reviews_switch ) && $reviews_switch === 'enable' ){
                                                docdirect_get_rating_stars_v2($review_data,'echo');
                                             }
                                            ?>
                                        </ul>

                                        <a href="<?php echo get_author_posts_url($user->ID); ?>" class="list-avatar1">
                                          <!-- <figcaption></figcaption> -->
                                        </a>
                                    </figure>
                                    <div class="doc-featurecontent">                                      
                                        <?php kt_get_tag_company($user->ID);?>
                                      <ul class="doc-addressinfo">
                                        <?php if( !empty( $directories_array['address'] ) ) {?>
                                        <li> <i class="fa fa-usd"></i>
                                            <span class="fee"><?php pll_e('Consultation Price: ')?><span>$<?php echo get_user_meta($user->ID,'price_min',true); ?></span></span>
                                        </li>
                                        <li> <i class="fa fa-map-marker"></i>
                                          <address><?php echo esc_attr( $directories_array['address'] );?></address>
                                        </li>
                                        <?php }?>
                                        <?php if( !empty( $directories_array['phone_number'] ) 
                                                  &&
                                                    !empty( $privacy['phone'] )
                                                  && 
                                                    $privacy['phone'] == 'on'
                                            ) {?>
                                            <li><i class="fa fa-phone"></i><span><?php echo esc_attr( $directories_array['phone_number'] );?></span></li>
                                        <?php }?>
                                        <?php if( !empty( $business_email ) 
                                                  &&
                                                    !empty( $privacy['email'] )
                                                  && 
                                                    $privacy['email'] == 'on'
                                            ) {?>
                                            <li><i class="fa fa-envelope-o"></i><a href="mailto:<?php echo esc_attr( $business_email);?>?subject:<?php esc_html_e('Hello','docdirect');?>"><?php echo esc_attr( $business_email);?></a></li>
                                        <?php }?>
                                        <?php if( !empty( $directories_array['fax'] ) ) {?>
                                            <li><i class="fa fa-fax"></i><span><?php echo esc_attr( $directories_array['fax']);?></span></li>
                                        <?php }?>
                                        <?php if( !empty( $directories_array['mtr_exit'] ) ) {?>
                                            <li class="mtr_exit"><img src="<?php echo get_stylesheet_directory_uri();?>/images/mtr_hong_kong_logo.svg" width="16" height="16"><span><?php echo esc_attr( $directories_array['mtr_exit']);?></span></li>
                                        <?php }?>
    
                                        <?php 
                                        if( !empty( $u_latitude ) && !empty( $u_longitude ) ){  
                                            if( !empty( $_GET['geo_location'] ) ) {
                                                $args = array(
                                                    'timeout'     => 15,
                                                    'headers' => array('Accept-Encoding' => ''),
                                                    'sslverify' => false
                                                );
                                                
                                                $address   = !empty($_GET['geo_location']) ? $_GET['geo_location'] : '';
                                                $prepAddr  = str_replace(' ','+',$address);
                                    
                                                $url   = 'http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false';
                                                $response   = wp_remote_get( $url, $args );
                                                $geocode  = wp_remote_retrieve_body($response);
                                                $output   = json_decode($geocode);
                                                
                                                if( isset( $output->results ) && !empty( $output->results ) ) {
                                                    $Latitude = $output->results[0]->geometry->location->lat;
                                                    $Longitude  = $output->results[0]->geometry->location->lng;
                                                    $distance = docdirectGetDistanceBetweenPoints($Latitude,$Longitude,$u_latitude,$u_longitude,'Km');
                                                }
                                            }
                                            ?>
                                            <?php if( !empty( $distance ) ) {?>
                                                <li class="dynamic-locations"><i class='fa fa-globe'></i><span><?php esc_html_e('within','docdirect');?>&nbsp;<?php echo esc_attr($distance);?></span></li>
                                            <?php }?>
                                        <?php }?>
                                        <?php 
                                          if (function_exists('kt_get_user_insurers')) {
                                            kt_get_user_insurers($user->ID);
                                          }
                                        ?>
                                        <?php 
                                            if (function_exists('kt_custom2')) {
                                              kt_custom2($user->ID);
                                            }
                                        ?>
                                      </ul>
                                    </div>
                  </div>
                 <?php }
                 }
            }
         ?>
      </div>
		<script>
            jQuery(document).ready(function(e) {
                jQuery("#doc-featureslider-<?php echo esc_js( $flag );?>").owlCarousel({
					items:3,
					rtl: <?php docdirect_owl_rtl_check();?>,
					nav: false,
					autoPlay: true,
					pagination: false,
					loop: true,
					navText : ['<i class="doc-btnprev icon-arrows-1"></i>','<i class="doc-btnnext icon-arrows"></i>'],
					responsive:{
						0:{items:1},
						481:{items:2},
						991:{items:3},
						1200:{items:3},
						1280:{items:4},
					}
                });
            });
        </script>
    </div>
    <?php }?>
</div>				
<?php
function kt_add_modal_footer() {
?>

<div class="modal fade tg-quickbooking">
  <div class="modal-dialog  modal-lg tg-modal-content1" role="document">
      <div class="tg-quickbooking-form">
        <?php //kt_quickbooking(35);?>
      </div>
  </div>
</div>

<div class="modal fade tg-confirmpopup confirm_booking">
  <div class="tg-modal-content" role="document">
        <div class="confirmbox">
            <h5><?php pll_e( 'Start Booking Process?' ) ?></h5>
            <a class="yes" href="javascript:;"><?php pll_e( 'Start Process' ) ?></a>
            <a target="_blank" class="view_profile" href="javascript:;"><?php pll_e( 'View Profile' ) ?></a>
            <a class="" href="javascript:;" data-dismiss="modal"><?php pll_e( 'Go Back' ) ?></a>
        </div>
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
                <input id="teluserphone" type="text" name="phone" class="form-control" placeholder="<?php pll_e( 'Phone Number *' ) ?>">
                <script type="text/javascript">                  
                  jQuery(document).ready(function(e) {
                    docdirect_intl_tel_input23();
                  });
                </script>
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="email" class="form-control" id="email" placeholder="<?php pll_e( 'Email Address *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="date_of_birth" class="form-control" id="date_of_birth" placeholder="<?php pll_e( 'Date of Birth' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">              
                <div class="form-group insurers">
                    <?php
                        $insurers_list   = docdirect_prepare_taxonomies('directory_type','insurer',0,'array');
                        $current_insurer_text = pll__('Insurers');
                        $current_insurer       = !empty( $_GET['insurer'] ) ? $_GET['insurer'] : '';
                        if ( $current_insurer != '' ) {
                            $insurer    = get_term_by( 'slug', $current_insurer, 'insurer');
                            $current_insurer_text = $insurer->name;
                        }

                    ?>
                    <a class="dropdown-button-group" href="javascript:;"><?php echo $current_insurer_text;?></a>
                    <input class="select_category" type="hidden" name="insurer" value="" />
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


<?php
}
add_action('wp_footer', 'kt_add_modal_footer');