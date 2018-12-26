<?php
/**
 * User Profile Main
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
$db_latitude    = get_user_meta( $user_identity, 'latitude', true);
$db_longitude   = get_user_meta( $user_identity, 'longitude', true);
$db_location	= get_user_meta( $user_identity, 'location', true); 
$video_url	  = get_user_meta( $user_identity, 'video_url', true);
$contact_form	  = get_user_meta( $user_identity, 'contact_form', true);
$db_address	    = get_user_meta( $user_identity, 'address', true);
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

$avatar 		= apply_filters(
					'docdirect_get_user_avatar_filter',
					 docdirect_get_user_avatar(array('width'=>270,'height'=>270), $user_identity) ,
					 array('width'=>270,'height'=>270) //size width,height=
				);

$banner 		= apply_filters(
					'docdirect_get_user_avatar_filter2',
					 docdirect_get_user_banner(array('width'=>270,'height'=>270), $user_identity) ,
					 array('width'=>270,'height'=>270) //size width,height=
				);
				
$is_banner	= docdirect_get_user_banner(0, $user_identity,'userprofile_banner');
$is_avatar	= docdirect_get_user_avatar(0, $user_identity,'userprofile_media');
$languages_array	= docdirect_prepare_languages();//Get Language Array

docdirect_init_dir_map();//init Map
docdirect_enque_map_library();//init Map

$section_column	= 'col-md-12 col-sm-12 col-xs-12';
if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){
	$section_column	= 'col-md-12 col-sm-12 col-xs-12';
}

$user_url	= '';
if( isset( $db_directory_type ) && !empty( $db_directory_type ) ) {
	$attached_specialities = get_post_meta( $db_directory_type, 'attached_specialities', true );
	$education_switch  	  = fw_get_db_post_option( $db_directory_type, 'education', true );
	$experience_switch  	  = fw_get_db_post_option( $db_directory_type, 'experience', true );
    // $specialities_list   = docdirect_prepare_taxonomies('directory_type','specialities',0,'array');
	$insurers_list	 = docdirect_prepare_taxonomies('directory_type','insurer',0,'array');
	$current_userdata	   = get_userdata($user_identity);
    $user_url   = $current_userdata->data->user_url;
    $specialities_list   = kt_docdirect_prepare_taxonomies('directory_type','specialities',0,'array');

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
?>
<?php
    $user_premium = get_user_meta($user_identity , 'user_premium' , true);
    if( !in_array('company', $list_terms) &&
        !in_array('medical-centre', $list_terms) &&
        !in_array('hospital-type', $list_terms) &&
        !in_array('scans-testing', $list_terms)
    ) {
        $current_option = get_option( $user_premium, true );
    }else {
        $current_option = get_option( 'company_'.$user_premium, true );
    }
?>
<form class="tg-formeditprofile tg-haslayout do-account-setitngs">
        <div class="tg-editprofile tg-haslayout">
            <div class="<?php echo esc_attr( $section_column );?> tg-findheatlhwidth">
                <div class="row">

                  <?php
                  $cl_company = '';
                  if(kt_is_company($user_identity)){
                    $cl_company = 'company';
                  }?>
                    <div class="tg-editimg <?php echo $cl_company;?>">
                    	<div class="tg-editimg-avatar">
                            <div class="tg-heading-border tg-small">
                                <h3>
                                    <?php pll_e('Profile photo');?>
                                    <?php if( $user_roles[0] != 'professional' ){?>
                                        <span style="font-size: 14px; color: rgb(122, 184, 15);display: block;">*Profile Photo will not be shown anywhere on the site</span>
                                    <?php }?>
                                </h3>
                            </div>
                            <figure class="tg-docimg">
                                <?php $ava_id = get_user_meta($user_identity, 'userprofile_media', true);?>
                                <input class="userprofile_media" type="hidden" name="userprofile_media" value="<?php echo $ava_id;?>">
                                <span class="user-avatar"><img src="<?php echo esc_url( $avatar );?>" alt="<?php pll_e('Avatar');?>"  /></span>
                                <?php if( isset( $is_avatar ) && !empty( $is_avatar ) ) {?>
                                    <a href="javascript:;" class="tg-deleteimg del-avatar"><i class="fa fa-plus"></i></a>
                                <?php }?>
                                <div id="plupload-container">
                                    <a href="javascript:;" id="kt_upload-profile-avatar" class="tg-uploadimg upload-avatar"><?php pll_e('Upload Image');?><i class="fa fa-upload"></i></a>
                                </div>
                            </figure>
                            <div class="tg-uploadtips">
                                <h4><?php pll_e('tips for uploading');?></h4>
                                <ul class="tg-instructions">
                                    <li><?php pll_e('Max Upload Size: ');?><?php docdirect_format_size_units($dir_datasize,'print');?></li>
                                    <li><?php pll_e('Dimensions: 370x377');?></li>
                                    <li><?php pll_e('Extensions: JPG,JPEG,PNG,GIF');?></li>
                                </ul>
                            </div>
                            <div id="errors-log"></div>
                        </div>
                        <?php
                        if( $user_roles[0] == 'professional' ){?>
                        <div class="tg-editimg-banner">
                            <div class="tg-heading-border tg-small">
                                <h3><?php pll_e('Mobile Banner');?></h3>
                            </div>
                            <figure class="tg-docimg">
                                <?php
                                    $banner_id = get_user_meta($user_identity, 'userprofile_banner_mobile', true);
                                    if ( isset( $banner_id ) && !empty( $banner_id ) ) {
                                        $banner_url = docdirect_get_image_source($banner_id,270,270);
                                    }else {
                                        $banner_url = get_template_directory_uri().'/images/user270x270.jpg';
                                    }
                                ?>
                                <input class="userprofile_banner_mobile" type="hidden" name="userprofile_banner_mobile" value="<?php echo $banner_id;?>">
                                <span class="user-banner_mobile"><img src="<?php echo esc_url( $banner_url );?>" alt="<?php pll_e('Avatar');?>"  /></span>
                                <?php if( isset( $banner_id ) && !empty( $banner_id ) ) {?>
                                    <a href="javascript:;" class="tg-deleteimg del-banner_mobile"><i class="fa fa-plus"></i></a>
                                <?php }?>
                                <div id="plupload-container-banner">
                                    <a href="javascript:;" id="kt_upload-profile-banner_mobile" class="tg-uploadimg upload-banner"><?php pll_e('Upload Image');?><i class="fa fa-upload"></i></a>
                                </div>
                            </figure>
                            <div class="tg-uploadtips">
                                <h4><?php pll_e('tips for uploading');?></h4>
                                <ul class="tg-instructions">
                                    <li><?php pll_e('Max Upload Size: ');?><?php docdirect_format_size_units($dir_datasize,'print');?></li>
                                    <li><?php pll_e('Dimensions: 370x200');?></li>
                                    <li><?php pll_e('Extensions: JPG,JPEG,PNG,GIF');?></li>
                                </ul>
                            </div>
                            <div id="errors-log"></div>
                        </div>
                        <?php if(kt_is_company($user_identity)){?>
                        <div class="tg-editimg-banner">
                            <div class="tg-heading-border tg-small">
                                <h3><?php pll_e('Company Logo');?></h3>
                            </div>
                            <figure class="tg-docimg">
                                <?php
                                    $banner_id = get_user_meta($user_identity, 'userprofile_company_logo', true);
                                    if ( isset( $banner_id ) && !empty( $banner_id ) ) {
                                        $banner_url = docdirect_get_image_source($banner_id,270,270);
                                    }else {
                                        $banner_url = get_template_directory_uri().'/images/user270x270.jpg';
                                    }
                                ?>
                                <input class="userprofile_company_logo" type="hidden" name="userprofile_company_logo" value="<?php echo $banner_id;?>">
                                <span class="user-company_logo"><img src="<?php echo esc_url( $banner_url );?>" alt="<?php pll_e('Avatar');?>"  /></span>
                                <?php if( isset( $banner_id ) && !empty( $banner_id ) ) {?>
                                    <a href="javascript:;" class="tg-deleteimg del-company_logo"><i class="fa fa-plus"></i></a>
                                <?php }?>
                                <div id="plupload-container-banner">
                                    <a href="javascript:;" id="kt_upload-profile-company_logo" class="tg-uploadimg upload-banner"><?php pll_e('Upload Image');?><i class="fa fa-upload"></i></a>
                                </div>
                            </figure>
                            <div class="tg-uploadtips">
                                <h4><?php pll_e('tips for uploading');?></h4>
                                <ul class="tg-instructions">
                                    <li><?php pll_e('Max Upload Size: ');?><?php docdirect_format_size_units($dir_datasize,'print');?></li>
                                    <li><?php pll_e('Dimensions: 370x200');?></li>
                                    <li><?php pll_e('Extensions: JPG,JPEG,PNG,GIF');?></li>
                                </ul>
                            </div>
                            <div id="errors-log"></div>
                        </div>
                        <?php }?>
                        <div class="row">
                        	<div class="col-xs-12 desktop_banner">
                                <div class="tg-heading-border tg-small">
                                    <h3><?php pll_e('Desktop Banner');?></h3>
                                </div>
                                <figure class="tg-docimg">
                                    <?php
                                        $banner_id = get_user_meta($user_identity, 'userprofile_banner', true);
                                        if ( isset( $banner_id ) && !empty( $banner_id ) ) {
                                            $banner_url = docdirect_get_image_source($banner_id,0,0);
                                        }else {
                                            $banner_url = get_stylesheet_directory_uri().'/images/doctor-banner-default.jpg';
                                        }
                                    ?>
                                    <input class="userprofile_banner" type="hidden" name="userprofile_banner" value="<?php echo $banner_id;?>">
                                    <span class="user-banner"><img src="<?php echo esc_url( $banner_url );?>" alt="<?php pll_e('Avatar');?>"  /></span>
                                    <?php if( isset( $is_banner ) && !empty( $is_banner ) ) {?>
                                        <a href="javascript:;" class="tg-deleteimg del-banner"><i class="fa fa-plus"></i></a>
                                    <?php }?>
                                    <div id="plupload-container-banner">
                                        <a href="javascript:;" id="kt_upload-profile-banner" class="tg-uploadimg upload-banner"><?php pll_e('Upload Image');?><i class="fa fa-upload"></i></a>
                                    </div>
                                </figure>
                                <div class="tg-uploadtips">
                                    <h4><?php pll_e('tips for uploading');?></h4>
                                    <ul class="tg-instructions">
                                        <li><?php pll_e('Max Upload Size: ');?><?php docdirect_format_size_units($dir_datasize,'print');?></li>
                                        <li><?php pll_e('Dimensions: 1920x450');?></li>
                                        <li><?php pll_e('Extensions: JPG,JPEG,PNG,GIF');?></li>
                                    </ul>
                                </div>
                                <div id="errors-log"></div>
                            </div>
                        </div>
                        <?php }?>
                   	</div>
                </div>
            </div>
        </div>

		<?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
            <div class="tg-editprofile tg-haslayout">
                <div class="col-md-12 col-sm-12 col-xs-12 tg-expectwidth">
                    <div class="row">
                        <div class="tg-otherphotos">
                            <div class="tg-heading-border tg-small">
                                <h3><a href="javascript:;"><?php pll_e('Media Gallery');?></a></h3>
                                <div id="plupload-container"></div>
                            </div>
                            <?php if(!isset($current_option['photo_gallery'])){?>
                                <?php kt_button_upgrade_premium();?>
                            <?php }else {?>
                                <?php 
                                $user_gallery    = get_user_meta( $user_identity, 'user_gallery', true);
                                $number_of_photo = intval($current_option['photo_number']);
                                ?>
                                <div id="tg-photoscroll" class="tg-photoscroll">
                                    <div class="form-group">
                                        <ul class="tg-otherimg kt_doc-user-gallery" id="gallery-sortable-container">
                                            <?php 
                                            $counter    = 0;
                                            if( isset( $user_gallery ) && !empty( $user_gallery ) ) {
                                                foreach( $user_gallery as $key  => $value ){
                                            ?>
                                            <li class="gallery-item gallery-thumb-item">
                                                <figure> 
                                                    <a href="javascript:;"><img width="100" height="100" src="<?php echo esc_attr( $value['url'] );?>" alt="<?php pll_e('Gallery');?>"></a>
                                                    <div class="tg-img-hover"><a href="javascript:;" data-attachment="<?php echo esc_attr( $value['id'] );?>"><i class="fa fa-plus"></i><i class='fa fa-refresh fa-spin'></i></a></div>
                                                    
                                                </figure>
                                                <input type="hidden" value="<?php echo esc_attr( $value['id'] );?>" name="user_gallery[<?php echo esc_attr( $value['id'] );?>][attachment_id]">
                                                <input type="hidden" value="<?php echo esc_attr( $value['url'] );?>" name="user_gallery[<?php echo esc_attr( $value['id'] );?>][url]">
                                            </li>
                                            <?php }}?>
                                            <?php
                                            if (count( $user_gallery) >= $number_of_photo ) {
                                                ?>
                                            <li class="gallery-item gallery-thumb-item kt_target" style="display: none;">
                                                <div class="gallery-button">
                                                    <div id="plupload-container-gallery" data-max="<?php echo $number_of_photo;?>">
                                                        <button type="button" id="vkl_attach-gallery" class="tg-btn tg-btn-lg"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </li>
                                                <?php
                                                echo '<li class="gallery-item gallery-thumb-item more_slots gallery-item gallery-thumb-item">';
                                                echo '<span>No Image Uploads Left</span>';
                                                echo '</li>';
                                            } else {
                                                ?>
                                            <li class="gallery-item gallery-thumb-item kt_target">
                                                <div class="gallery-button">
                                                    <div id="plupload-container-gallery" data-max="<?php echo $number_of_photo;?>">
                                                        <button type="button" id="vkl_attach-gallery" class="tg-btn tg-btn-lg"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </li>
                                                <?php
                                                echo '<li style="display: none;" class="gallery-item gallery-thumb-item more_slots gallery-item gallery-thumb-item">';
                                                echo '<span>No Image Uploads Left</span>';
                                                echo '</li>';
                                            }
                                            
                                            ?>
                                        </ul>
                                        <ul class="tg-otherimg">
                                        </ul>
                                        
                                    </div>
                                </div>
                                <div id="errors-log-gallery"></div>
                            <?php }?>

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <?php if(!isset($current_option['video_gallery'])){?>
                            <?php kt_button_upgrade_premium();?>
                        <?php }else {?>
                            <div class="all_videos">
                                <?php 
                                $video_url    = get_user_meta( $user_identity, 'video_url', true);
                                $max_video = intval($current_option['video_number']);
                                ?>
                                <?php 
                                if( isset( $video_url ) && !empty( $video_url ) ) {
                                    foreach( $video_url as $key  => $value ){
                                ?>
                                    <div class="form-group item_video">
                                        <input class="form-control" name="video_url[]" value="<?php echo esc_url( $value );?>" type="url" placeholder="<?php pll_e('Enter Url');?>">
                                        <a class="remove_video" href="javascript:;"><i class="fa fa-remove"></i></a>
                                    </div>
                                <?php }}?>
                            </div>
                            <?php if( count($video_url) <= $max_video ){?>
                            <div class="col-sm-12">
                                <div class="tg-addfield add-new-video" data-max="<?php echo $max_video;?>">
                                    <button type="button">
                                        <i class="fa fa-plus"></i>
                                        <span><?php pll_e('Add Video Link');?></span>
                                    </button>
                                </div>
                            </div>
                            <?php }?>
                        <?php }?>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php }?>
        <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Basic Information');?></h3>
                </div>
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="basics[nickname]" value="<?php echo get_user_meta($user_identity,'nickname',true); ?>" type="text" placeholder="<?php pll_e('Username');?>">
                        </div>
                    </div>
                    <?php if( (!in_array('company', $list_terms) &&            
        !in_array('medical-centre', $list_terms) &&
        !in_array('hospital-type', $list_terms) &&           
        !in_array('scans-testing', $list_terms)
    )  && $user_roles[0] == 'professional') {?>
                    <div class="col-md-8 col-sm-12 col-xs-12 plus_group">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                        <div class="doc-select">
                                            <select name="title_name">
                                                <option value=""><?php pll_e('Title');?></option>
                                                <?php
                                                $current_title = get_user_meta($user_identity,'title_name',true);
                                                $array_title = array(
                                                                pll__('Dr'),
                                                                pll__('Mr'),
                                                                pll__('Ms'),
                                                                pll__('Mrs'),
                                                                pll__('Miss'),
                                                                pll__('Professor'),
                                                                pll__('Lord'),
                                                                );
                                                foreach ($array_title as $key => $value) {
                                                    $selected = ($current_title == $value) ? 'selected' : '' ;
                                                    echo '<option '.$selected.'>'. $value .'</option>';
                                                }
                                            ?>
                                            </select>
                                        </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="form-group">
                                        <input id="male" value="male" type="radio" name="gende" <?php echo $retVal = (get_user_meta($user_identity,'gende',true) == 'male') ? 'checked' : '' ; ?>>
                                        <label for="male"><?php pll_e('Male');?></label>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-6">
                                <div class="form-group">
                                        <input id="female" value="female" type="radio" name="gende" <?php echo $retVal = (get_user_meta($user_identity,'gende',true) == 'female') ? 'checked' : '' ; ?>>
                                        <label for="female"><?php pll_e('Female');?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                    <?php if( $user_roles[0] != 'professional') {?>
                    <div class="col-md-4 col-sm-12 col-xs-12 plus_group">
                        <div class="row">
                            <div class="col-md-6 col-xs-6">
                                <div class="form-group">
                                        <input id="male" value="male" type="radio" name="gende" <?php echo $retVal = (get_user_meta($user_identity,'gende',true) == 'male') ? 'checked' : '' ; ?>>
                                        <label for="male"><?php pll_e('Male');?></label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-6">
                                <div class="form-group">
                                        <input id="female" value="female" type="radio" name="gende" <?php echo $retVal = (get_user_meta($user_identity,'gende',true) == 'female') ? 'checked' : '' ; ?>>
                                        <label for="female"><?php pll_e('Female');?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <?php }?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <?php
                            if( !in_array('company', $list_terms) &&            
                                !in_array('medical-centre', $list_terms) &&
                                !in_array('hospital-type', $list_terms) &&           
                                !in_array('scans-testing', $list_terms)
                            )  {
                                $placeholder = pll__('First Name');
                            }else {
                                $placeholder = pll__('Company Name');
                            }
                        ?>
                        <div class="form-group">
                            <input class="form-control" name="basics[first_name]" value="<?php echo get_user_meta($user_identity,'first_name',true); ?>" type="text" placeholder="<?php echo $placeholder;?>">
                        </div>
                    </div>
                    <?php if( !in_array('company', $list_terms) &&            
                        !in_array('medical-centre', $list_terms) &&
                        !in_array('hospital-type', $list_terms) &&           
                        !in_array('scans-testing', $list_terms)
                    )  {?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="basics[last_name]" value="<?php echo get_user_meta($user_identity,'last_name',true); ?>" type="text" placeholder="<?php pll_e('Last Name');?>">
                        </div>
                    </div>
                    <?php }?>
                    <?php
                    if( $user_roles[0] == 'professional' ){?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="basics[tagline]" value="<?php echo get_user_meta($user_identity,'tagline',true); ?>" type="text" placeholder="<?php pll_e('Specialty Eg. Gynaecologist');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="basics[phone_number]" value="<?php echo get_user_meta($user_identity,'phone_number',true); ?>" type="text" placeholder="<?php pll_e('Phone');?>">
                        </div>
                    </div>
                    <?php }?>
                    <?php
                    if( $user_roles[0] != 'professional' ){?>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="basics[card_number]" value="<?php echo get_user_meta($user_identity,'card_number',true); ?>" type="text" placeholder="<?php pll_e('HKID Card / Passport #');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">            
                            <select name="patient_insurers" class="locations-select">
                                <option value=""><?php pll_e('Select Insurance');?></option>
                            <?php 
                            $db_insurer = get_user_meta( $user_identity, 'patient_insurers', true);
                            $insurers_list   = docdirect_prepare_taxonomies('directory_type','insurer',0,'array');
                            if( isset( $insurers_list ) && !empty( $insurers_list ) ){
                                foreach( $insurers_list as $key => $insurer ){
                                    $checked    = '';
                                    if( isset( $db_insurer ) && !empty( $db_insurer ) && $db_insurer === $insurer->term_id ){
                                        $selected    = 'selected';
                                    }
                                    
                                ?>
                                <option value="<?php echo esc_attr( $insurer->term_id );?>" <?php echo $selected;?>><?php echo esc_attr( $insurer->name );?></option>
                            <?php }}?>
                            </select>
                        </div>
                    </div>
                    <?php }?>
                    <?php
                    if( $user_roles[0] == 'professional' ){?>
                    <div class="col-md-4 col-sm-6 col-xs-12 price_group">
                        <script type="text/javascript">
                            function isNumberKey(evt)
                                {
                                    var charCode = (evt.which) ? evt.which : evt.keyCode;
                                    if (charCode != 46 && charCode > 31 
                                    && (charCode < 48 || charCode > 57))
                                    return false;
                                    return true;
                                } 
                        </script>
                                <div class="form-group">
                                    <label for=""><?php echo esc_attr( '$' );?></label>
                                    <input class="form-control" onkeypress="return isNumberKey(event)" name="basics[price_min]" value="<?php echo get_user_meta($user_identity,'price_min',true); ?>" type="text" placeholder="<?php pll_e('Appointment Price');?>">
                                </div>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <?php /*?><?php 
                                $user_description   = get_user_meta($user_identity,'description',true);
                                $user_description = !empty($user_description) ? $user_description : '';
                                $settings = array( 
                                    'editor_class' => 'booking_approved',
                                    'textarea_name' => 'basics[description]', 
                                    'teeny' => true, 
                                    'media_buttons' => true, 
                                    'textarea_rows' => 10,
                                    'quicktags' => true,
                                    'editor_height' => 300
                                    
                                );
                                
                                wp_editor( $user_description, 'description', $settings );
                            ?><?php */?>
                            <div id="aboutab"> 
                                <ul  class="nav nav-tabs">
                                    <?php $plugins_url = plugins_url();
                                        $flag_url =  $plugins_url.'/polylang/flags/';
                                    ?>
                                    <li class="active">
                                        <a  href="#English" data-toggle="tab">English <img src="<?php echo $flag_url;?>gb.png" /></a>
                                    </li>
                                    <li>
                                        <a href="#Traditional" data-toggle="tab">中文 (香港) <img src="<?php echo $flag_url;?>hk.png" /></a>
                                    </li>
                                    <li>
                                        <a href="#Simplified" data-toggle="tab">中文 (中国) <img src="<?php echo $flag_url;?>cn.png" /></a>
                                    </li>
                                    <li>
                                        <a href="#French" data-toggle="tab">Français <img src="<?php echo $flag_url;?>fr.png" /></a>
                                    </li>
                                </ul>

                                <div class="tab-content clearfix">
                                    <div class="tab-pane active" id="English">
                                        <?php    
                                        // textarea_name in array can have brackets!
                                            $content = get_user_meta($user_identity,'desc',true);
                                            $settings = array('media_buttons' => false, 'tinymce' => false, 'quicktags' => false, 'textarea_name' => 'basics[desc]', 'editor_height' => '200');
                                            wp_editor($content, $editor_id, $settings);
                                        ?>
                                    </div>
                                    <div class="tab-pane" id="Traditional">
                                        <?php    
                                        // textarea_name in array can have brackets!
                                            $content = get_user_meta($user_identity,'desc_hk',true);
                                            $settings = array('media_buttons' => false, 'tinymce' => false, 'quicktags' => false, 'textarea_name' => 'basics[desc_hk]', 'editor_height' => '200');
                                            wp_editor($content, $editor_id, $settings);
                                        ?>
                                    </div>
                                    <div class="tab-pane" id="Simplified">
                                        <?php    
                                        // textarea_name in array can have brackets!
                                            $content = get_user_meta($user_identity,'desc_cn',true);
                                            $settings = array('media_buttons' => false, 'tinymce' => false, 'quicktags' => false, 'textarea_name' => 'basics[desc_cn]', 'editor_height' => '200');
                                            wp_editor($content, $editor_id, $settings);
                                        ?>
                                    </div>
                                    <div class="tab-pane" id="French">
                                        <?php    
                                        // textarea_name in array can have brackets!
                                            $content = get_user_meta($user_identity,'desc_fr',true);
                                            $settings = array('media_buttons' => false, 'tinymce' => false, 'quicktags' => false, 'textarea_name' => 'basics[desc_fr]', 'editor_height' => '200');
                                            wp_editor($content, $editor_id, $settings);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <?php
                            $dir_profile_page = '';
                            if (function_exists('fw_get_db_settings_option')) {
                                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
                            }
                            $profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
                            
                            if ( defined( 'POLYLANG_VERSION' ) ) {
                                $profile_page = pll_get_post($profile_page);
                            }

                            ?>
                            <a target="_blank" class="btn btn-primary btn-practice" href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'listing'); ?>"><i class="fa fa-map-marker"></i><?php pll_e('Add Practice Locations');?></a>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
       
        <!--Language-->
        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
        <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Language');?></h3>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <select name="language[]" class="chosen-select" multiple>
                                <option disabled="disabled" value=""><?php pll_e('Select Languages');?></option>
                                <?php 
                                if( isset( $languages_array ) && !empty( $languages_array ) ){
                                    
                                    foreach( $languages_array as $key=>$value ){
                                        $selected   = '';
                                        if( isset( $db_languages[$key] ) ){
                                            $selected   = 'selected';
                                        }
                                        ?>
                                    <option <?php echo esc_attr( $selected );?> value="<?php echo esc_attr( $key );?>"><?php echo esc_attr( $value );?></option>
                                <?php }}?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>

        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
        <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Specialties');?></h3>
                </div>
                <div class="row1">
                    <div class="specialities-list">
                        <ul>
                            <?php 
                            if( isset( $specialities_list ) && !empty( $specialities_list ) ){
                                foreach( $specialities_list as $key => $speciality ){

                                    $trans_id =  pll_get_term($speciality->term_id, 'en');
                                    if( in_array( $trans_id, $attached_specialities ) ) {
                                        $term = get_term( $trans_id, 'specialities' );
                                        $name = $term->name;
                                        $slug = $term->slug;

                                        $db_speciality  = get_user_meta( $user_identity, $term->slug, true);
                                        $checked    = '';
                                        if( isset( $db_speciality ) && !empty( $db_speciality ) && $db_speciality === $term->slug ){
                                            $checked    = 'checked';
                                        }

                                        /*$presenter_custom_fields = get_option( "taxonomy_term_$speciality->term_id" );
                                        $s_keyword = $presenter_custom_fields[s_keyword];
                                        if (strpos($s_keyword, 'zalo3') !== false) {
                                            echo 'true';
                                        }*/
                                ?>
                                <li>
                                    <div class="tg-checkbox user-selection">
                                        <div class="tg-packages active-user-type specialities-type">
                                            <input type="checkbox" <?php echo esc_attr( $checked );?> name="specialities[<?php echo esc_attr( $term->term_id );?>]" value="<?php echo esc_attr( $term->slug );?>" id="<?php echo esc_attr( $speciality->slug );?>">
                                            <label for="<?php echo esc_attr( $speciality->slug );?>"><?php echo esc_attr( $speciality->name );?></label>
                                        </div>
                                    </div>
                                    
                                </li>
                            <?php }}}?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Procedures');?></h3>
                    <p>Type your procedure and press enter to save</p>
                </div>
                <div class="procedures-list">
                    <?php $val = get_user_meta( $user_identity, 'user_profile_procedures', true );?>
                    <input name="procedures" type="text" value="<?php echo $val;?>" data-role="tagsinput" />
                </div>
            </div>
        </div>

        <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Insurers');?></h3>
                </div>
                <div class="row1">
                    <div class="specialities-list">
                    	<ul>
                        	<?php 
							if( isset( $insurers_list ) && !empty( $insurers_list ) ){
								foreach( $insurers_list as $key => $insurer ){
									$db_insurer	= get_user_meta( $user_identity, $insurer->slug, true);
									$checked	= '';
									if( isset( $db_insurer ) && !empty( $db_insurer ) && $db_insurer === $insurer->slug ){
										$checked	= 'checked';
									}
									
								?>
                            	<li>
									<div class="tg-checkbox user-selection">
                                        <div class="tg-packages active-user-type insurers-type">
                                            <input type="checkbox" <?php echo esc_attr( $checked );?> name="insurers[<?php echo esc_attr( $insurer->term_id );?>]" value="<?php echo esc_attr( $insurer->slug );?>" id="<?php echo esc_attr( $insurer->slug );?>">
                                            <label for="<?php echo esc_attr( $insurer->slug );?>"><?php echo esc_attr( $insurer->name );?></label>
                                        </div>
                                    </div>
									
                                </li>
                            <?php }}?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--Price/Services List-->
        <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection tg-prices">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Prices/Services List','docdirect');?></h3>
                </div>
                <div class="tg-education-detail tg-haslayout">
                  <table class="table-striped prices_wrap" id="table-striped">
                    <thead class="cf">
                      <tr>
                        <th><?php pll_e('Title','docdirect');?></th>
                        <th><?php pll_e('Price','docdirect');?></th>
                      </tr>
                    </thead>
                    <?php 
                    $prices_list    = get_the_author_meta('prices_list',$user_identity);
                    $counter    = 0;
                    if( !empty( $prices_list ) ) {
                        foreach( $prices_list as $key   => $value ){
                        ?>
                        <tbody class="prices_item">
                          <tr>
                            <td data-title="Title"><?php echo esc_attr( $value['title'] );?>
                              <div class="tg-table-hover prices-action"> 
                                  <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a> 
                                  <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
                              </div>
                            </td>
                            <td data-title="Company"><?php echo esc_attr( $value['price'] );?></td>
                          </tr>
                          <tr>
                           <td class="prices-data edit-me-row" colspan="3">
                             <div class="experience-data-wrap">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control" value="<?php echo esc_attr( $value['title'] );?>" name="prices[<?php echo intval( $counter );?>][title]" type="text" placeholder="<?php pll_e('Title','docdirect');?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control" value="<?php echo esc_attr( $value['price'] );?>" name="prices[<?php echo intval( $counter );?>][price]" type="text" placeholder="<?php pll_e('Price','docdirect');?>">
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <textarea class="form-control" name="prices[<?php echo intval( $counter );?>][description]" placeholder="<?php pll_e('Description','docdirect');?>"><?php echo esc_attr( $value['description'] );?></textarea>
                                    </div>
                                </div>
                               </div>
                           </td>
                          </tr>
                         </tbody>
                       <?php
                            $counter++;
                            }
                        }
                    ?>
                  </table>
                </div>
                <div class="col-sm-12">
                    <div class="tg-addfield add-new-prices">
                        <button type="button">
                            <i class="fa fa-plus"></i>
                            <span><?php pll_e('Add Prices/Services','docdirect');?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>

        <?php
        if( $user_roles[0] == 'professional' ){?>
        <!-- <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection">
                <div class="tg-heading-border tg-small">
                    <h3><?php //pll_e('Insurance Plan');?></h3>
                    <p>Type your insurance and press enter to save</p>
                </div>
                <div class="insurance_plan-list">
                    <?php //$val = get_user_meta( $user_identity, 'user_profile_insurance_plan', true );?>
                    <input name="insurance_plan" type="text" value="<?php //echo $val;?>" data-role="tagsinput" />
                </div>
            </div>
        </div> -->
        
        <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Social Settings');?></h3>
                </div>
                <p><strong><?php pll_e('Note: Leave them empty to hide social icons at detail page.');?></strong></p>
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="socials[facebook]" value="<?php echo get_user_meta($user_identity,'twitter',true); ?>" type="text" placeholder="<?php pll_e('Facebook');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="socials[twitter]" value="<?php echo get_user_meta($user_identity,'twitter',true); ?>" type="text" placeholder="<?php pll_e('Twitter');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="socials[linkedin]" value="<?php echo get_user_meta($user_identity,'linkedin',true); ?>" type="text" placeholder="<?php pll_e('Linkedin');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="socials[pinterest]" value="<?php echo get_user_meta($user_identity,'pinterest',true); ?>" type="text" placeholder="<?php pll_e('Pinterest');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="socials[google_plus]" value="<?php echo get_user_meta($user_identity,'google_plus',true); ?>" type="text" placeholder="<?php pll_e('Google Plus');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="socials[instagram]" value="<?php echo get_user_meta($user_identity,'instagram',true); ?>" type="text" placeholder="<?php pll_e('Instagram');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="socials[tumblr]"  value="<?php echo get_user_meta($user_identity,'tumblr',true); ?>"type="text" placeholder="<?php pll_e('Tumblr');?>">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="form-group">
                            <input class="form-control" name="socials[skype]"  value="<?php echo get_user_meta($user_identity,'skype',true); ?>"type="text" placeholder="<?php pll_e('Skype');?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>
        
        <?php if( isset( $experience_switch ) && $experience_switch === 'enable' ) {?>
        <!--Experience-->
        <div class="tg-bordertop tg-haslayout">
  			<div class="tg-formsection tg-experience">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Experience');?></h3>
                </div>
                <div class="tg-education-detail tg-haslayout">
                  <table class="table-striped experiences_wrap" id="table-striped">
                    <thead class="cf">
                      <tr>
                        <th><?php pll_e('Experience Title');?></th>
                        <th><?php pll_e('Company/Organization');?></th>
                        <th class="numeric"><?php pll_e('Year');?></th>
                      </tr>
                    </thead>
				    <?php 
                    $experience_list	= get_the_author_meta('experience',$user_identity);
                    $counter	= 0;
                    if( isset( $experience_list ) && !empty( $experience_list ) ) {
                        foreach( $experience_list as $key	=> $value ){
                        $flag	= rand(1,9999);
						
						if( !empty( $value['end_date'] ) ) {
							$end_date	= date('M,Y',strtotime( $value['end_date']));
						} else{
							$end_date	= esc_html__('Current');
						}
                        ?>
                      	<tbody class="experiences_item">
                          <tr>
                            <td data-title="Code"><?php echo esc_attr( $value['title'] );?>
                              <div class="tg-table-hover experience-action"> 
                                  <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a> 
                                  <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
                              </div>
                            </td>
                            <td data-title="Company"><?php echo esc_attr( $value['company'] );?></td>
                            <td data-title="Price" class="numeric"><?php echo esc_attr( date('M,Y',strtotime( $value['start_date'] ) ) );?>&nbsp;-&nbsp;<?php echo esc_attr( $end_date );?></td>
                          </tr>
                          <tr>
                           <td class="experience-data edit-me-row" colspan="3">
                             <div class="experience-data-wrap">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control" value="<?php echo esc_attr( $value['title'] );?>" name="experience[<?php echo intval( $counter );?>][title]" type="text" placeholder="<?php pll_e('Title');?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control" value="<?php echo esc_attr( $value['company'] );?>" name="experience[<?php echo intval( $counter );?>][company]" type="text" placeholder="<?php pll_e('Company/Organization');?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control exp_start_date_<?php echo esc_attr( $flag );?>" id="exp_start_date" value="<?php echo esc_attr( $value['start_date'] );?>" name="experience[<?php echo intval( $counter );?>][start_date]" type="text" placeholder="<?php pll_e('Start Date');?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control exp_end_date_<?php echo esc_attr( $flag );?>" id="exp_end_date" value="<?php echo esc_attr( $value['end_date'] );?>" name="experience[<?php echo intval( $counter );?>][end_date]" type="text" placeholder="<?php pll_e('End Date');?>">
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <textarea class="form-control" name="experience[<?php echo intval( $counter );?>][description]" placeholder="<?php pll_e('Experience Description');?>"><?php echo esc_attr( $value['description'] );?></textarea>
                                    </div>
                                </div>
                                <script>
                                   jQuery(document).ready(function(e) {
                                    jQuery('.exp_start_date_<?php echo esc_js( $flag );?>').datetimepicker({
                                       format:'Y-m-d',
                                      onShow:function( ct ){
                                       this.setOptions({
                                        maxDate:jQuery('.exp_end_date_<?php echo esc_js( $flag );?>').val()?jQuery('.exp_end_date_<?php echo esc_js( $flag );?>').val():false
                                       })
                                      },
                                      timepicker:false
                                     });
                                    jQuery('.exp_end_date_<?php echo esc_js( $flag );?>').datetimepicker({
                                       format:'Y-m-d',
                                      onShow:function( ct ){
                                       this.setOptions({
                                        minDate:jQuery('.exp_start_date_<?php echo esc_js( $flag );?>').val()?jQuery('.exp_start_date_<?php echo esc_js( $flag );?>').val():false
                                       })
                                      },
                                      timepicker:false
                                     });
                                   }); 
                                </script>
                               </div>
                           </td>
                      	  </tr>
                         </tbody>
                       <?php
							$counter++;
							}
						}
					?>
                  </table>
                </div>
                <div class="col-sm-12">
                    <div class="tg-addfield add-new-experiences">
                        <button type="button">
                            <i class="fa fa-plus"></i>
                            <span><?php pll_e('Add Experience');?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php if( isset( $education_switch ) && $education_switch === 'enable' ) {?>
        <!--Education-->
        <div class="tg-bordertop tg-haslayout">
            <div class="tg-formsection tg-education">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Education');?></h3>
                </div>
                <div class="tg-education-detail tg-haslayout">
                  <table class="table-striped educations_wrap" id="table-striped">
                    <thead class="cf">
                      <tr>
                        <th><?php pll_e('Degree / Education Title');?></th>
                        <th><?php pll_e('Institute');?></th>
                        <th class="numeric"><?php pll_e('Year');?></th>
                      </tr>
                    </thead>
                    <?php 
                    $education_list = get_the_author_meta('education',$user_identity);
                    $counter    = 0;
                    if( isset( $education_list ) && !empty( $education_list ) ) {
                        foreach( $education_list as $key    => $value ){
                            if( !empty( $value['end_date'] ) ) {
                                $end_date   = date('M,Y',strtotime( $value['end_date']));
                            } else{
                                $end_date   = esc_html__('Current');
                            }
                        
                        $flag   = rand(1,9999);
                        ?>
                        <tbody class="educations_item">
                          <tr>
                            <td data-title="Code"><?php echo esc_attr( $value['title'] );?>
                              <div class="tg-table-hover education-action"> 
                                  <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a> 
                                  <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
                              </div>
                            </td>
                            <td data-title="Company"><?php echo esc_attr( $value['institute'] );?></td>
                            <td data-title="Price" class="numeric"><?php echo esc_attr( date('M,Y',strtotime( $value['start_date'] ) ) );?>&nbsp;-&nbsp;<?php echo esc_attr( $end_date );?></td>
                          </tr>
                          <tr>
                           <td class="education-data edit-me-row" colspan="3">
                             <div class="education-data-wrap">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control" value="<?php echo esc_attr( $value['title'] );?>" name="education[<?php echo intval( $counter );?>][title]" type="text" placeholder="<?php pll_e('Title');?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control" value="<?php echo esc_attr( $value['institute'] );?>" name="education[<?php echo intval( $counter );?>][institute]" type="text" placeholder="<?php pll_e('Institute');?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control edu_start_date_<?php echo esc_attr( $flag );?>" id="edu_start_date" value="<?php echo esc_attr( $value['start_date'] );?>" name="education[<?php echo intval( $counter );?>][start_date]" type="text" placeholder="<?php pll_e('Start Date');?>">
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <input class="form-control edu_end_date_<?php echo esc_attr( $flag );?>" id="edu_end_date" value="<?php echo esc_attr( $value['end_date'] );?>" name="education[<?php echo intval( $counter );?>][end_date]" type="text" placeholder="<?php pll_e('End Date');?>">
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                        <textarea class="form-control" name="education[<?php echo intval( $counter );?>][description]" placeholder="<?php pll_e('Education Description');?>"><?php echo esc_attr( $value['description'] );?></textarea>
                                    </div>
                                </div>
                                <script>
                                   jQuery(document).ready(function(e) {
                                    jQuery('.edu_start_date_<?php echo esc_js( $flag );?>').datetimepicker({
                                       format:'Y-m-d',
                                      onShow:function( ct ){
                                       this.setOptions({
                                        maxDate:jQuery('.edu_end_date_<?php echo esc_js( $flag );?>').val()?jQuery('.edu_end_date_<?php echo esc_js( $flag );?>').val():false
                                       })
                                      },
                                      timepicker:false
                                     });
                                    jQuery('.edu_end_date_<?php echo esc_js( $flag );?>').datetimepicker({
                                       format:'Y-m-d',
                                      onShow:function( ct ){
                                       this.setOptions({
                                        minDate:jQuery('.edu_start_date_<?php echo esc_js( $flag );?>').val()?jQuery('.edu_start_date_<?php echo esc_js( $flag );?>').val():false
                                       })
                                      },
                                      timepicker:false
                                     });
                                   }); 
                                </script>
                               </div>
                           </td>
                          </tr>
                         </tbody>
                       <?php
                            $counter++;
                            }
                        }
                    ?>
                  </table>
                </div>
                <div class="col-sm-12">
                    <div class="tg-addfield add-new-educations">
                        <button type="button">
                            <i class="fa fa-plus"></i>
                            <span><?php pll_e('Add Education');?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>

        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
        <div class="tg-bordertop tg-haslayout">
          <div class="tg-formsection tg-honor-awards">
            <div class="tg-heading-border tg-small">
              <h3><?php pll_e('Honors & Awards');?></h3>
            </div>
            <div class="tg-education-detail tg-haslayout">
              <table class="table-striped awards_wrap">
                <thead class="cf">
                  <tr>
                    <th><?php pll_e('Title');?></th>
                    <th><?php pll_e('Year');?></th>
                  </tr>
                </thead>
                <?php 
                $awards_list    = get_the_author_meta('awards',$user_identity);
                $counter    = 0;
                if( isset( $awards_list ) && !empty( $awards_list ) ) {
                    foreach( $awards_list as $key   => $value ){
                    ?>
                    <tbody class="awards_item">
                      <tr>
                        <td data-title="Code"><?php echo esc_attr( $value['name'] );?>
                          <div class="tg-table-hover award-action"> 
                            <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a>
                            <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
                           </div>
                        </td>
                        <td data-title="Company"><?php echo esc_attr( date('F m, Y',strtotime( $value['date'] ) ) );?></td>
                      </tr>
                      <tr>
                        <td class="award-data edit-me-row"colspan="2">
                            <div class="tg-education-form tg-haslayout">
                                <div class="award-data">
                                        <div class="col-md-8 col-sm-8 col-xs-12">
                                            <div class="form-group">
                                                <input class="form-control" value="<?php echo esc_attr( $value['name'] );?>" name="awards[<?php echo intval( $counter );?>][name]" type="text" placeholder="<?php pll_e('Award Name');?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-xs-12">
                                            <div class="form-group">
                                                <input class="form-control award_datepicker" id="award_datepicker" value="<?php echo esc_attr( $value['date'] );?>" name="awards[<?php echo intval( $counter );?>][date]" type="text" placeholder="<?php pll_e('Award Date');?>">
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                <textarea class="form-control" name="awards[<?php echo intval( $counter );?>][description]" placeholder="<?php pll_e('Award Description');?>"><?php echo esc_attr( $value['description'] );?></textarea>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </td>
                      </tr>
                    </tbody>
                    <?php
                        $counter++;
                        }
                    }
                ?>
              </table>
              </div>
            <div class="col-sm-12">
               <div class="tg-addfield add-new-awards">
                  <button type="button">
                      <i class="fa fa-plus"></i>
                      <span><?php pll_e('Add Awards');?></span>
                  </button>
               </div>
            </div>
          </div>
        </div>

        <?php }}?>
        
        <div class="button-wrapper"><button type="submit" class="tg-btn process-account-settings"><?php pll_e('update');?></button></div>
        <button type="submit" class="mobile_button_save hidden-sm1 hidden-md1 hidden-lg1 tg-btn process-account-settings">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
            <span><?php pll_e('Save');?></span>
        </button>
    
</form>
<!--Video Url-->
<script type="text/template" id="tmpl-load-video">   
        <div class="form-group item_video">
            <input class="form-control" name="video_url[]" value="" type="url" placeholder="<?php pll_e('Enter Url');?>">
            <a class="remove_video" href="javascript:;"><i class="fa fa-remove"></i></a>
        </div>
</script>
<!--Price List-->
<script type="text/template" id="tmpl-load-prices">
    <tbody class="prices_item">
      <tr>
        <td data-title="Code"><?php pll_e('Title','docdirect');?>
          <div class="tg-table-hover prices-action"> 
              <a href="javascript:;" class="delete-me"><i class="tg-delete fa fa-close"></i></a> 
              <a href="javascript:;" class="edit-me"><i class="tg-edit fa fa-pencil"></i></a> 
          </div>
        </td>
        <td data-title="Company"><?php pll_e('Price','docdirect');?></td>
      </tr>
      <tr>
       <td class="prices-data edit-me-row" colspan="3">
         <div class="prices-data-wrap">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <input class="form-control" value="" name="prices[{{data}}][title]" type="text" placeholder="<?php pll_e('Title','docdirect');?>">
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="form-group">
                    <input class="form-control" value="" name="prices[{{data}}][price]" type="text" placeholder="<?php pll_e('Price','docdirect');?>">
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="form-group">
                    <textarea class="form-control" name="prices[{{data}}][description]" placeholder="<?php pll_e('Description','docdirect');?>"></textarea>
                </div>
            </div>
          </div>
        </td>
      </tr>
    </tbody>
</script>
<?php

