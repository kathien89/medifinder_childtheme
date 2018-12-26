<?php
/**
 * Map Search Postion Top
 * return html
 */

global $current_user, $wp_roles,$userdata,$post;
get_header();

$search_page_map 		= fw_get_db_settings_option('search_page_map');
$dir_map_marker_default = fw_get_db_settings_option('dir_map_marker');

docdirect_init_dir_map();//init Map
docdirect_enque_map_library();//init Map

//Search center point
$direction	= docdirect_get_location_lat_long();

if( isset( $search_page_map ) && $search_page_map === 'enable' ){
	?>
	<div class="map-top">
		<div class="row tg-divheight">
			<div class="tg-mapbox">
				<div id="map_canvas" class="tg-location-map tg-haslayout"></div>
				<?php do_action('docdirect_map_controls');?>
				<div id="gmap-noresult"></div>
			</div>
		</div>
	</div>
<?php }?>
<?php if( have_posts() ) {?>
    <div class="container">
        <div class="row">
            <?php 
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            ?>
        </div>
    </div>
<?php }

?>
<div class="container">
    <div id="doc-twocolumns" class="doc-twocolumns"> 
      <?php if( !empty( $found_title ) ) {?>
      	<span class="doc-searchresult"><?php echo force_balance_tags( $found_title );?></span>
      <?php }?>
      <form class="doc-formtheme doc-formsearchwidget search-result-form">
          <div class="row">
            <div class="col-xs-12 pull-left">
              <aside id="doc-sidebar" class="doc-sidebar">
                <?php do_action( 'docdirect_search_filters' );?>
                <?php if (is_active_sidebar('search-page-sidebar')) {?>
                  <div class="tg-doctors-list tg-haslayout">
                    <?php dynamic_sidebar('search-page-sidebar'); ?>
                  </div>
                <?php }?>
              </aside>
            </div>
            <div class="col-xs-12 pull-right">
              <div id="doc-content" class="doc-content">
                <div class="doc-doctorlisting">
                  <div class="doc-pagehead">
                    <div class="doc-sortby"> 
                     <span class="doc-select">
                      <select name="sort_by" class="sort_by" id="sort_by">
                          <option value=""><?php pll_e('Sort By','docdirect');?></option>
                          <option value="recent" <?php echo isset( $_GET['sort_by'] ) && $_GET['sort_by'] == 'recent' ? 'selected' : '';?>><?php pll_e('Most recent','docdirect');?></option>
                          <option value="featured" <?php echo isset( $_GET['sort_by'] ) && $_GET['sort_by'] == 'featured' ? 'selected' : '';?>><?php pll_e('Featured','docdirect');?></option>
                          <option value="title" <?php echo isset( $_GET['sort_by'] ) && $_GET['sort_by'] == 'title' ? 'selected' : '';?>><?php pll_e('Alphabetical','docdirect');?></option>
                          <option value="distance" <?php echo isset( $_GET['sort_by'] ) && $_GET['sort_by'] == 'distance' ? 'selected' : '';?>><?php pll_e('Sort By Distance','docdirect');?></option>
                          <option value="likes" <?php echo isset( $_GET['sort_by'] ) && $_GET['sort_by'] == 'likes' ? 'selected' : '';?>><?php pll_e('Sort By Likes','docdirect');?></option>
                      </select>
                      </span>
                      <span class="doc-select">
                        <select class="order_by" name="order" id="order">
                          <option value="ASC" <?php echo isset( $_GET['order'] ) && $_GET['order'] == 'ASC' ? 'selected' : '';?>><?php pll_e('ASC','docdirect');?></option>
                          <option value="DESC" <?php echo isset( $_GET['order'] ) && $_GET['order'] == 'DESC' ? 'selected' : '';?>><?php pll_e('DESC','docdirect');?></option>
                        </select>
                      </span> 
                      <span class="doc-select">
                           <select name="per_page" class="per_page">
                            <option value=""><?php pll_e('Per Page','docdirect');?></option>
                            <option value="10" <?php echo isset( $_GET['per_page'] ) && $_GET['per_page'] == '10' ? 'selected' : '';?>>10</option>
                            <option value="20" <?php echo isset( $_GET['per_page'] ) && $_GET['per_page'] == '20' ? 'selected' : '';?>>20</option>
                            <option value="50" <?php echo isset( $_GET['per_page'] ) && $_GET['per_page'] == '50' ? 'selected' : '';?>>50</option>
                            <option value="70" <?php echo isset( $_GET['per_page'] ) && $_GET['per_page'] == '70' ? 'selected' : '';?>>70</option>
                            <option value="100" <?php echo isset( $_GET['per_page'] ) && $_GET['per_page'] == '100' ? 'selected' : '';?>>100</option>
                          </select>
                      </span> 
                    </div>
                  </div>
                  <div class="doc-bloggrid">
                    <div class="row">
                    <?php
                    $user_query  = new WP_User_Query($query_args);
                    $directories	=  array();
          					$directories['status']	= 'none';
          					$directories['lat']  = floatval ( $direction['lat'] );
          					$directories['long'] = floatval ( $direction['long'] );

                    if ( ! empty( $user_query->results ) ) {
                        $directories['status']	= 'found';
                        
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
                      $business_email = '';
                      $phone_number = '';
                      $address = '';
                      $fax = '';
                      $mtr_exit = '';

                            if (!empty($current_info)) {
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
                                     docdirect_get_user_avatar(array('width'=>270,'height'=>270), $user->ID),
                                     array('width'=>270,'height'=>270) //size width,height
                                );
                                
                            $privacy		= docdirect_get_privacy_settings($user->ID); //Privacy settin
                            
                            if( !empty( $u_latitude ) && !empty( $u_longitude ) ) {
                                $directories_array['latitude']   = $u_latitude;
                                $directories_array['longitude'] = $u_longitude;
                                $directories_array['fax']     = $fax;
                                $directories_array['mtr_exit']     = $mtr_exit;
                                $directories_array['description']  = $user->description;
                                $directories_array['user_id']   = $user->ID;
                                $directories_array['title']   = $user->display_name;
                                $directories_array['name']     = kt_get_title_name($user->ID).$user->first_name.' '.$user->last_name;
                                $directories_array['email']   = $user->user_email;
                                $directories_array['phone_number'] = $phone_number;
                                $directories_array['address']   = $address;
                                $directories_array['group']   = $slug;
                                $featured_string   = $featured_date;
                                $current_string = strtotime( $current_date );
                                $review_data  = kt_docdirect_get_everage_rating ( $user->ID );
                                // $get_username = docdirect_get_username( $user->ID );
                                $get_username = kt_get_title_name($user->ID).docdirect_get_username( $user->ID );

                  							if( isset( $dir_map_marker['url'] ) && !empty( $dir_map_marker['url'] ) ){
                  								$directories_array['icon']	 = $dir_map_marker['url'];
                  							} else{
                  								if( !empty( $dir_map_marker_default['url'] ) ){
                  									$directories_array['icon']	 = $dir_map_marker_default['url'];
                  								} else{
                  									$directories_array['icon']	 	   = get_template_directory_uri().'/images/map-marker.png';
                  								}
                  							}
                                $empty_class = '';
                                if( empty( get_user_meta( $user->ID, 'tagline', true) ) ) {
                                  $empty_class = 'empty_tagline';
                                }
                                
                                $infoBox  = '<div class="tg-map-marker '.$empty_class.'">';
                                $infoBox  .= '<figure class="tg-docimg"><a class="userlink" href="'.get_author_posts_url($user->ID).'"><img src="'.esc_url( $avatar ).'" alt="'.esc_attr( $directories_array['name'] ).'"></a>';
                                
                                $infoBox  .= '</figure>';
                                
                                $infoBox  .= '<div class="tg-mapmarker-content">';
                                $infoBox  .= '<div class="tg-heading-border tg-small">';
                                $infoBox  .= '<h3><a class="userlink" href="'.get_author_posts_url($user->ID).'">'.$directories_array['name'].'</a></h3>';
                                  if( !empty( get_user_meta( $user->ID, 'tagline', true) ) ) {
                                    $infoBox  .= '<span>'. get_user_meta( $user->ID, 'tagline', true) .'</span>';
                                  }
                                $infoBox  .= '</div>';
                                $infoBox  .= '<ul class="tg-likestars">';
                                    $infoBox  .= '<li>'. docdirect_get_rating_stars($review_data,'return') .'</li>';
                                    $infoBox  .= '<li>'. docdirect_get_wishlist_button($user->ID, false).'</li>';
                                    $infoBox  .= '<li><span>'.intval( docdirect_get_user_views( $user->ID ) ). ' '. pll__('view(s)').'</span></li>';
                                $infoBox  .= '</ul>';
                                $infoBox  .= '</div>';
                                $infoBox  .= '</div>';
                                
                                $directories_array['html']['content'] = $infoBox;
                                $directories['users_list'][]  = $directories_array;
    
                                // $banner = docdirect_get_user_banner(array('width'=>270,'height'=>270), $user->ID);
                                $banner_id = get_user_meta($user->ID, 'userprofile_banner_mobile', true);
                                if ( isset( $banner_id ) && !empty( $banner_id ) ) {
                                  $banner = docdirect_get_image_source($banner_id,270,270);
                                }else {
                                  $banner = get_stylesheet_directory_uri().'/images/doctor-banner-default.jpg';
                                }
    
                            ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 doc-verticalaligntop">
                              <div class="doc-featurelist" class="user-<?php echo intval( $user->ID );?>">
                                    <figure class="doc-featureimg" style="background-image: url(<?php echo $banner;?>)"> 
                                        <?php if( isset( $featured_string ) && $featured_string > $current_string ){?>
                                            <?php //kt_docdirect_get_featured_tag(true, $user->ID, 'v2');?>
                                        <?php }?>
                                        <a href="<?php echo get_author_posts_url($user->ID); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
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
                                            <span class="fee"><?php pll_e('Consultation Price: ')?><span>$<?php echo get_user_meta($user->ID,'price_min',true);?></span></span>
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
                                            <li><i class="fa fa-envelope-o"></i><a href="mailto:<?php echo esc_attr( $business_email);?>?subject:<?php pll_e('Hello','docdirect');?>"><?php echo esc_attr( $business_email);?></a></li>
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
                                                <li class="dynamic-locations"><i class='fa fa-globe'></i><span><?php pll_e('within','docdirect');?>&nbsp;<?php echo esc_attr($distance);?></span></li>
                                            <?php } else{?>
                                                <li class="dynamic-location-<?php echo intval($user->ID);?>"></li>
                                                <?php  
                                                        wp_add_inline_script( 'docdirect_functions', 'if ( window.navigator.geolocation ) {
                                                            window.navigator.geolocation.getCurrentPosition(
                                                                function(pos) {
                                                                    jQuery.cookie("geo_location", pos.coords.latitude+"|"+pos.coords.longitude, { expires : 365 });
                                                                    var with_in = _get_distance(pos.coords.latitude, pos.coords.longitude, '.esc_js($u_latitude).','. esc_js($u_longitude).',"K");
                                                                    jQuery(".dynamic-location-'.intval($user->ID).'").html("<i class=\'fa fa-globe\'></i><span>"+scripts_vars.with_in+_get_round(with_in, 2)+scripts_vars.kilometer+"</i></span>");
                                                                    
                                                                }
                                                            );
                                                        }
                                                    ' );
                                                    }
                                                ?>
                                        <?php }?>
                                        <?php 
                                          if (function_exists('kt_get_user_insurers')) {
                                            kt_get_user_insurers($user->ID);
                                          }
                                        ?>
                                      </ul>
                                      <?php 
                                        if (function_exists('kt_custom')) {
                                          kt_custom($user->ID);
                                        }
                                      ?>
                                    </div>
                                </div>
                            </div>
                        <?php
                          }
                        }
                     } else{?>
                        <?php DoctorDirectory_NotificationsHelper::informations(pll__('No Result Found.','docdirect'));?>
                    <?php }?>
                    <?php if( isset( $search_page_map ) && $search_page_map === 'enable' ){?>
                    <script>
                        jQuery(document).ready(function() {
                             /* Init Markers */
                            vkl_docdirect_init_map_script(<?php echo json_encode( $directories );?>);
                        });
                    </script>
                    <?php }?> 
                    </div>
                  </div>
                </div>
                <?php 
                //Pagination
                if( isset( $total_users ) && $total_users > $limit ) {?>
                    <?php docdirect_prepare_pagination($total_users,$limit);?>
                <?php }?>
              </div>
            </div>
          </div>
      </form>
    </div>
</div>
	
<?php

function kt_add_modal_footer() {
  global $current_user;
  $current_author_profile = get_userdata( $current_user->ID );
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
                <a href="<?php echo get_author_posts_url($current_author_profile->ID); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $current_author_profile->display_name );?>"></a>
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
                <input id="teluserphone" type="text" name="phone" class="form-control" id="teluserphone" placeholder="<?php pll_e( 'Phone Number *' ) ?>">
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
            if (function_exists('kt_read_insurer')) {
                $list_insurer = kt_read_insurer();
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
                            <span><?php pll_e('Close','docdirect'); ?></span>
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
get_footer();

