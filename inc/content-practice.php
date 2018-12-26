<?php

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

if (isset($_GET['id']) && $_GET['id'] != '') {
	$current_practices = get_user_meta($user_identity, 'user_practices', true);	

	$id	  = $_GET['id'];
	$value = $current_practices[$id];

	if ($value != '') {
		$key_edit	  = $_GET['id'];
		$basics = $value['basics'];
        $socials = $value['socials'];
		$db_schedules = $value['schedules'];
		$db_longitude = $basics['longitude'];
		$db_latitude = $basics['latitude'];
		$db_location = $basics['location'];
		?>
        <div class="tg-heading-border tg-small">    
            <h4><?php echo $value['title'];?></h4>
        </div>
		<?php
	}
}else {
        ?>
        <div class="tg-heading-border tg-small">    
            <h4><?php pll_e('Practice Contact Info');?></h4>
        </div>
        <?php
    }
$dir_profile_page = '';
if (function_exists('fw_get_db_settings_option')) {
    $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
}

$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
?>
    <form class="add_practice" action="" data-redirect="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity);?>">
        <div class="tg-formsection">
            <div class="tg-heading-border tg-small">
                <h4><?php pll_e('Information');?></h4>
            </div>
            <div class="row">
    		    <div class="col-xs-12">
    		        <div class="form-group">
    		            <input class="form-control" name="pratice_title" value="<?php echo $value['title'];?>" type="text" placeholder="<?php pll_e('Practice Name');?>">
    		        </div>
    		    </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="basics[phone_number]" value="<?php echo $basics['phone_number'];?>" type="text" placeholder="<?php pll_e('Phone');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="basics[fax]" value="<?php echo $basics['fax'];?>" type="text" placeholder="<?php pll_e('Fax');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="basics[room_floor]" value="<?php echo $basics['room_floor'];?>" type="text" placeholder="<?php pll_e('Room/Floor/Building');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="basics[address]" value="<?php echo $basics['address'];?>" type="text" placeholder="<?php pll_e('Address');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="basics[user_url]" value="<?php echo $basics['user_url'];?>" type="url" placeholder="<?php pll_e('Your Website');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="basics[business_email]" value="<?php echo $basics['business_email'];?>" type="text" placeholder="<?php pll_e('Add Business Email..');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="basics[mtr_exit]" value="<?php echo $basics['mtr_exit'];?>" type="text" placeholder="<?php pll_e('MTR Exit');?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="tg-formsection">
            <div class="tg-heading-border tg-small">
                <h4><?php pll_e('Locations');?></h4>
            </div>
            <div class="row map-container">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <select name="basics[location]" class="locations-select">
                            <option value=""><?php pll_e('Select Location');?></option>
                            <?php kt_docdirect_get_locations_options($db_location);?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="form-group locate-me-wrap">
                        <input type="text" value="<?php echo $basics['address'];?>" name="basics[address]" class="form-control" id="location-address" />
                        <a href="javascript:;" class="geolocate"><img src="<?php echo get_template_directory_uri();?>/images/geoicon.svg" width="16" height="16" class="geo-locate-me" alt="<?php pll_e('Locate me!');?>"></a>
                    </div>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div id="location-pickr-map" style="height: 400px;width: 100%;"></div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12 elm-display-none">
                    <div class="form-group">
                        <input type="text" readonly="readonly" value="<?php echo esc_attr( $db_longitude );?>" name="basics[longitude]" class="form-control" id="location-longitude" />
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12  elm-display-none">
                    <div class="form-group">
                        <input type="text" readonly="readonly" value="<?php echo esc_attr( $db_latitude );?>" name="basics[latitude]" class="form-control" id="location-latitude" />
                    </div>
                </div>
                <script>
                    jQuery(document).ready(function(e) {
                        //init
                        jQuery.docdirect_init_map(<?php echo esc_attr( $db_latitude );?>,<?php echo esc_attr( $db_longitude );?>);
                    });
                </script>
                <?php
                if ($key_edit != '') {
                    ?>
                    <div class="col-xs-12">
                        <!-- <div class="form-group">
                            <input class="tg-btn pull-right " type="submit" name="submit" value="Update" />
                        </div> -->
                    </div>
                    <input type="hidden" name="action" value="edit_practice">
                    <input type="hidden" name="key" value="<?php echo $key_edit;?>">
                    <?php
                }else {?>
                    <div class="col-xs-12">
                        <!-- <div class="form-group">
                            <input class="tg-btn pull-right " type="submit" name="submit" value="Submit" />
                        </div> -->
                    </div>
                    <input type="hidden" name="action" value="add_practice">
                <?php }?>
            </div>
        </div>

        <div class="tg-formsection">
            <div class="tg-heading-border tg-small">
                <h4><?php pll_e('Schedule');?></h4>
            </div>
            <?php $schedules  = docdirect_get_week_array();?>
            <fieldset class="row">
            
                <div class="schedule">
                    <?php
                    if( isset( $schedules ) && !empty( $schedules ) ) {
                        foreach( $schedules as $key => $value ) {
                        echo '<div class="col-xs-6 col-sm-6 col-md-4">';
                            
                            $start_time = isset( $db_schedules[$key.'_start'] ) ? $db_schedules[$key.'_start'] : '';
                            $end_time   = isset( $db_schedules[$key.'_end'] ) ? $db_schedules[$key.'_end'] : '';
                            
                        ?>
                        <div class="row">
                            <div class="col-md-2 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label><?php echo esc_attr( $key );?></label>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <input type="text" name="schedules[<?php echo esc_attr( $key );?>_start]" value="<?php echo esc_attr( $start_time );?>" class="form-control schedule-pickr" placeholder="<?php pll_e('start time');?>">
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <input type="text" name="schedules[<?php echo esc_attr( $key );?>_end]" value="<?php echo esc_attr( $end_time );?>" class="form-control schedule-pickr" placeholder="<?php pll_e('end time');?>">
                                </div>
                            </div>
                        </div>
                    <?php
                        echo '</div>';
                        }
                    }?>
                </div>
            </fieldset>
        </div>

        <div class="tg-formsection">
            <div class="tg-heading-border tg-small">
                <h4><?php pll_e('Social Settings');?></h4>
            </div>
            <p><strong><?php pll_e('Note: Leave them empty to hide social icons at detail page.');?></strong></p>
            <div class="row">
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="socials[facebook]" value="<?php echo $socials['facebook'];?>" type="text" placeholder="<?php pll_e('Facebook');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="socials[twitter]" value="<?php echo $socials['twitter'];?>" type="text" placeholder="<?php pll_e('Twitter');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="socials[linkedin]" value="<?php echo $socials['linkedin'];?>" type="text" placeholder="<?php pll_e('Linkedin');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="socials[pinterest]" value="<?php echo $socials['pinterest'];?>" type="text" placeholder="<?php pll_e('Pinterest');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="socials[google_plus]" value="<?php echo $socials['google_plus'];?>" type="text" placeholder="<?php pll_e('Google Plus');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="socials[instagram]" value="<?php echo $socials['instagram'];?>" type="text" placeholder="<?php pll_e('Instagram');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="socials[tumblr]"  value="<?php echo $socials['tumblr'];?>"type="text" placeholder="<?php pll_e('Tumblr');?>">
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <div class="form-group">
                        <input class="form-control" name="socials[skype]"  value="<?php echo $socials['skype'];?>"type="text" placeholder="<?php pll_e('Skype');?>">
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="mobile_button_save hidden-sm1 hidden-md1 hidden-lg1 tg-btn">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
            <span><?php pll_e('Save');?></span>
        </button>
    </form>

<script>
    jQuery(document).ready(function(e) {
        //Time Picker
        jQuery('.schedule-pickr').datetimepicker({
          datepicker:false,
          format:'H:i'
        });
    });
</script>