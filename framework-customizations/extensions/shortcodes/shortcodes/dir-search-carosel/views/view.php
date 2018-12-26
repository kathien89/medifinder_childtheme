<?php
if (!defined('FW')) {
    die('Forbidden');
}
/**
 * @var $atts
 */
$uni_flag = fw_unique_increment();
$lang = pll_current_language();

$args = array('posts_per_page' => '-1', 
			   'post_type' => 'directory_type', 
			   'post_status' => 'publish',
			   'suppress_filters' => false
		);


$cust_query = get_posts($args);
docdirect_init_dir_map();//init Map
docdirect_enque_map_library();//init Map
$dir_search_page = fw_get_db_settings_option('dir_search_page');

if( isset( $dir_search_page[0] ) && !empty( $dir_search_page[0] ) ) {
    $search_page     = get_permalink((int)$dir_search_page[0]);
	$search_page 	 = get_permalink(pll_get_post((int)$dir_search_page[0]));    
} else{
	$search_page 	 = '';
}

if (function_exists('fw_get_db_settings_option')) {
	$dir_location = fw_get_db_settings_option('dir_location');
	$dir_keywords = fw_get_db_settings_option('dir_keywords');
	$dir_longitude = fw_get_db_settings_option('dir_longitude');
	$dir_latitude = fw_get_db_settings_option('dir_latitude');
	$dir_longitude	= !empty( $dir_longitude ) ? $dir_longitude : '-0.1262362';
	$dir_latitude	= !empty( $dir_latitude ) ? $dir_latitude : '51.5001524';
} else{
	$dir_location = '';
	$dir_keywords = '';
	$dir_longitude = '-0.1262362';
	$dir_latitude  = '51.5001524';
}

$gallery	= $atts['gallery'];
if( empty( $gallery ) ){
	$gallery	=  array();
	$gallery[0]['url']	= get_template_directory_uri().'/images/banner.jpg';
}
$flag	= rand(1,9999);
?>
<div class="tg-banner-holder">
    <div class="tg-banner-content">
        <div class="container">
            <?php if( !empty( $atts['title'] ) ){?>
            <div class="tg-heading-border">
                <h1><span><?php echo esc_attr( $atts['title'] );?></span><div class="dynamic-title"></div></h1>
            </div>
            <?php }?>
            <div class="tg-searcharea-v2">
                <ul class="nav nav-pills">
                  <li class="active"><a data-toggle="tab" href="#speacialtiesz"><?php pll_e('Specialty Search');?></a></li>
                  <li><a data-toggle="tab" href="#doctorsz"><?php pll_e('Name Search');?></a></li>
                </ul>

                <div class="tab-content">
                  <div id="speacialtiesz" class="tab-pane fade in active">                    
                    <form class="tg-searchform directory-map" action="<?php echo esc_url( $search_page);?>" method="get" id="directory-map">
                        <fieldset>
                           <?php if( isset( $dir_keywords ) && $dir_keywords === 'enable' ){?>
                              <div class="form-group">
                                <input type="text" name="by_name" placeholder="<?php pll_e('Type Name...');?>" class="form-control">
                              </div>
                            <?php }?>
                            <?php 
                                /*if (function_exists('kt_group_label')) {
                                    kt_group_label();
                                }*/
                                if (function_exists('kt_direct_search')) {
                                    kt_direct_search();
                                }
                            ?>
                            <?php if( isset( $dir_location ) && $dir_location === 'enable' ){?>
                            <div class="form-group">
                                <div class="locate-me-wrap">
                                   <?php kt_docdirect_locateme_snipt();?>
                                    <script>
                                        jQuery(document).ready(function(e) {
                                            //init
                                            jQuery.docdirect_init_map(<?php echo esc_js( $dir_latitude );?>,<?php echo esc_js( $dir_longitude );?>);
                                        });
                                    </script> 

                                </div>
                            </div>
                            <?php }?>
                            <div class="form-group">
                                <input type="submit" id="search_banner" class="tg-btn" value="<?php pll_e('search');?>" />
                            </div>
                        </fieldset>
                    </form>
                  </div>
                  <div id="doctorsz" class="tab-pane fade">                  
                    <form class="tg-searchform directory-map" action="<?php echo esc_url( $search_page);?>" method="get" id="directory-map2">
                        <fieldset>
                            <?php 
                                if (function_exists('kt_search_insurers')) {
                                    kt_search_insurers();
                                }
                            ?>
                            <div class="form-group">
                                <input type="submit" id="search_banner2" class="tg-btn" value="<?php pll_e('search');?>" />
                            </div>
                        </fieldset>
                    </form>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <div id="searchbanner-<?php echo esc_attr( $flag );?>" class="tg-homeslidertwo owl-carousel">
        <?php 
		if( !empty( $gallery ) ){
            foreach( $gallery as $key => $value ){?>
            <div class="item">
                <figure>
                    <img width="1500" height="1000" src="<?php echo esc_url( $value['url'] );?>" alt="<?php pll_e( 'Search','docdirect' );?>">
                </figure>
            </div>
        <?php }}?>
    </div>
    <script>
		jQuery(document).ready(function(e) {
            jQuery("#searchbanner-<?php echo esc_js( $flag );?>").owlCarousel({
				items:1,
				nav:true,
				rtl: <?php docdirect_owl_rtl_check();?>,
				loop: true,
				dots: false,
				autoplay: false,
				navText : ["<i class='tg-prev fa fa-angle-left'></i>", "<i class='tg-next fa fa-angle-right'></i>"],
			});
        });
	</script>
</div>
