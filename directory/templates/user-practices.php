<?php
/**
 * User Location
 * return html
 */

global $current_user, $wp_roles,$userdata,$post;

$user_identity  = $current_user->ID;
$current_date   = date('Y-m-d H:i:s');
$db_schedules   = array();
$tagline   		= get_user_meta( $user_identity, 'tagline', true);
$featured_date  = get_user_meta( $user_identity, 'user_featured', true);
$db_schedules   = get_user_meta( $user_identity, 'schedules', false);
$db_languages   = get_user_meta( $user_identity, 'languages', true);
// $db_latitude    = get_user_meta( $user_identity, 'latitude', true);
// $db_longitude   = get_user_meta( $user_identity, 'longitude', true);
// $db_location	= get_user_meta( $user_identity, 'location', true); 
$video_url	  = get_user_meta( $user_identity, 'video_url', true);
$contact_form	  = get_user_meta( $user_identity, 'contact_form', true);
// $db_address	    = get_user_meta( $user_identity, 'address', true);
$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);


$featured_string   = $featured_date;
$current_string	= strtotime( $current_date );
			
if (function_exists('fw_get_db_settings_option')) {
	$dir_longitude = fw_get_db_settings_option('dir_longitude');
	$dir_latitude = fw_get_db_settings_option('dir_latitude');
	$dir_datasize = fw_get_db_settings_option('dir_datasize');
	$dir_longitude	= !empty( $dir_longitude ) ? $dir_longitude : '105.834160';
	$dir_latitude	= !empty( $dir_latitude ) ? $dir_latitude : '21.027764';
} else{
	$dir_longitude = '105.834160';
	$dir_latitude = '21.027764';
	$dir_datasize = '5242880'; // 5 MB
}

$db_longitude	= !empty( $db_longitude ) ? $db_longitude : $dir_longitude;
$db_latitude	= !empty( $db_latitude ) ? $db_latitude : $dir_latitude;

$dblatitude	 = isset( $db_latitude ) && !empty( $db_latitude ) ? $db_latitude : $dir_latitude;
$dblongitude	= isset( $db_longitude ) && !empty( $db_longitude ) ? $db_longitude : $dir_longitude;

docdirect_init_dir_map();//init Map
docdirect_enque_map_library();//init Map

$section_column	= 'col-md-12 col-sm-12 col-xs-12';
if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){
	$section_column	= 'col-md-12 col-sm-12 col-xs-12';
}

$user_url	= '';
if( isset( $db_directory_type ) && !empty( $db_directory_type ) ) {

    $terms = get_the_terms($db_directory_type, 'group_label');
    $current_group_label_slug = $terms[0]->slug;
    $list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
}

$author = get_userdata( $user_identity );
$user_roles = $author->roles;

if( empty( $attached_specialities )){
	$attached_specialities	= array();
} 

$dir_profile_page = '';
if (function_exists('fw_get_db_settings_option')) {
    $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
}

$get_username = docdirect_get_username($user_identity);
$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
?>
<?php
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
<div class="manage_practice">
<?php 
    if( isset( $_GET['mode'] ) && ($_GET['mode'] == 'add' || $_GET['mode'] == 'edit') ){
        get_template_part('inc/content','practice');
    }else {

    ?>
    <!--Locations-->
    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>

    <div class="tg-joblisting tg-dashboardmanagejobs practices_listing">
        <div class="tg-dashboardhead">
            <div class="tg-dashboardtitle">
                <h2><i class="fa fa-map-marker"></i><?php esc_html_e('Practices Info & Locations', 'docdirect'); ?></h2>
            </div>
            <div class="tg-btnaddservices">
                <?php
                    $current_practices = get_user_meta($user_identity, 'user_practices', true);
                    $disabled = '';
                    $href = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, true, 'add');
                    if (empty($current_practices)) {
                        $disabled = 'disabled';
                        $href = 'javascript:;';
                    }
                ?>
                <a class="<?php echo $disabled;?>" href="<?php echo $href;?>">+ <?php esc_html_e('Add New Practice', 'docdirect'); ?></a>
            </div>
        </div>
            <?php if (empty($current_practices)) {?>
                <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'add');?>" class="btn btn-primary add_main_location"><i class="fa fa-map-marker"></i><?php esc_html_e('Add Main Location', 'docdirect'); ?></a>
            <?php }?>
        <table class="tg-tablejoblidting job-listing-wrap fw-ext-article-listing">
            <tbody>
                <?php
                $list = array();
                foreach ($current_practices as $key => $value) {
                    $list[$key] = $value['title'];
                }
                if (!empty($list)) {
                    $i=0;
                    foreach ($list as $key => $value) {
                        if($current_practices[$key]['active_location']){ 
                            $class = "active";
                            $txt =  pll__('Active Location');
                            $checked = "checked=\"checked\"";
                        }else{ 
                            $class = "";
                            $txt = pll__('Listing Only');
                            $checked = "";
                        }
                    ?>
                    <tr class="<?php echo $class;?>">
                        <td>
                            <div class="tg-contentbox"> 
                                <div class="tg-title">
                                    <h3><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'edit', $key); ?>"><?php echo $value;?></a></h3>
                                </div>
                            </div>
                            <div class="action_location">
                                <span><?php echo $txt;?></span>
                                <div class="onoffswitch">
                                    <input type="checkbox" name="change_al" class="onoffswitch-checkbox" id="<?php echo $key;?>" value="true" <?php echo $checked; ?>>
                                    <label class="onoffswitch-label" for="<?php echo $key;?>">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
                            <figure class="tg-companylogo">
                                <a class="tg-btnedite delete_practice" href="javascript:;" data-key="<?php echo $key;?>"><i class="fa fa-trash"></i></a>
                                <a class="tg-btnedite" href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'edit', $key); ?>"><i class="fa fa-pencil"></i></a>
                            </figure>
                        </td>
                    </tr>
                    <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

        <?php }?>
    <?php }?>
</div>