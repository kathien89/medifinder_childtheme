<?php
/**
 * User Admin Profile
 * return html
 */
	function docdirect_user_social_mehthods( $userid ) {
		$userfields['user_address']		= esc_html__('User Address','docdirect');
		$userfields['zip']			= esc_html__('Zip Code','docdirect');
		$userfields['tagline']		= esc_html__('Tag Line 1234','docdirect');	
		$userfields['phone_number']	= esc_html__('Phone Number','docdirect');
		$userfields['fax']			= esc_html__('Fax','docdirect');
		$userfields['facebook']		= esc_html__('Facebook','docdirect');	
		$userfields['twitter']		= esc_html__('Twitter','docdirect');
		$userfields['linkedin']		= esc_html__('Linkedin','docdirect');
		$userfields['pinterest']	= esc_html__('Pinterest','docdirect');
		$userfields['google_plus']	= esc_html__('Google Plus','docdirect');
		$userfields['instagram']	= esc_html__('Instagram','docdirect');
		$userfields['tumblr']		= esc_html__('Tumblr','docdirect');
		$userfields['skype']		= esc_html__('Skype','docdirect');
		unset( $userfields['tagline']    );
		return $userfields;
	}
	add_filter('user_contactmethods', 'docdirect_user_social_mehthods', 10, 1);

function kt_after_theme_setup() {

		remove_action('edit_user_profile_update', 'docdirect_personal_options_save', 10, 1 );
    	remove_action('personal_options_update', 'docdirect_personal_options_save', 10, 1 );

		add_action('edit_user_profile_update', 'kt_docdirect_personal_options_save' );
    	add_action('personal_options_update', 'kt_docdirect_personal_options_save' );

}

add_action('after_setup_theme', 'kt_after_theme_setup', 99);

/**
 * @User Public Profile Save
 * @return {}
 */
if (!function_exists('kt_docdirect_personal_options_save')) {

    function kt_docdirect_personal_options_save($user_identity) {
        $current_date	= date('Y-m-d H:i:s');
		$userprofile_media = (isset($_POST['userprofile_media']) && $_POST['userprofile_media'] <> '') ? $_POST['userprofile_media'] : '';
        update_user_meta($user_identity, 'userprofile_media', $userprofile_media);
		
		//Banner
		$userprofile_banner = (isset($_POST['userprofile_banner']) && $_POST['userprofile_banner'] <> '') ? $_POST['userprofile_banner'] : '';
        update_user_meta($user_identity, 'userprofile_banner', $userprofile_banner);

		if( !empty( $_POST['user_premium']) && $_POST['user_premium'] != '' ){
        	update_user_meta($user_identity, 'user_premium', $_POST['user_premium']);
		}
		

		if( !empty( $_POST['featured_days'] )  ){
			//Featured		
			$today = time();
			update_user_meta( $user_identity, 'user_featured', $today );
			$user_featured_date    = get_user_meta( $user_identity, 'user_featured', true);
			$duration	    	  = $_POST['featured_days'];

			if( !empty( $user_featured_date ) && $user_featured_date > strtotime($current_date) ){
				$featured_date	= strtotime("+".$duration." days", $user_featured_date);
				$featured_date	= date('Y-m-d H:i:s',$featured_date);
			} else{
				$current_date	= date('Y-m-d H:i:s');
				$duration	    = $_POST['featured_days'];
				$featured_date		 = strtotime("+".$duration." days", strtotime($current_date));
				$featured_date	     = date('Y-m-d H:i:s',$featured_date);
			}
						
			update_user_meta($user_identity,'user_featured',strtotime( $featured_date )); //Update Expiry
		} else if( !empty( $_POST['featured_exclude'] ) ){
			$user_featured_date    = get_user_meta( $user_identity, 'user_featured', true);
			$duration	    	  = $_POST['featured_exclude'];
			
			if( isset( $user_featured_date ) && !empty( $user_featured_date ) ){
				$featured_date	= strtotime("-".$duration." days", $user_featured_date);
				$featured_date	= date('Y-m-d H:i:s',$featured_date);
			} 
			
			update_user_meta($user_identity,'user_featured',strtotime( $featured_date )); //Update Expiry
		}
		
		//Update Schedules
		update_user_meta( $user_identity, 'schedules', $_POST['schedules'] );
		
		//Update Professional Statements
		update_user_meta( $user_identity, 'professional_statements', $_POST['professional_statements'] );
		
		//Update Genral settings
		if( isset( $_POST['contact_form'] ) && !empty( $_POST['contact_form'] ) ) {
			update_user_meta( $user_identity, 'contact_form', esc_attr( $_POST['contact_form'] ) );
		}else{
			update_user_meta( $user_identity, 'contact_form', 'off' );
		}

		
		update_user_meta( $user_identity, 'video_url', esc_attr( $_POST['video_url'] ) );
		update_user_meta( $user_identity, 'directory_type', esc_attr( $_POST['directory_type'] ) );
		
		//Update General settings
		if( isset( $_POST['basics'] ) && !empty( $_POST['basics'] ) ){
			foreach( $_POST['basics'] as $key=>$value ){
				update_user_meta( $user_identity, $key, esc_attr( $value ) );
			}
		}
		if( !empty( $_POST['basics']['latitude'] ) && !empty( $_POST['basics']['longitude'] ) && !empty( $_POST['basics']['address'] ) ){
			$tit = explode(',', $_POST['basics']['address']);
			$pratice_title	  = str_replace(' ', '_', strtolower(sanitize_text_field( $tit[0] )));
			$basics	  = $_POST['basics'];
			$current_practices[$pratice_title] = array(
									'title' => sanitize_text_field( $tit[0] ),
									'active_location' => true,
									'basics' => $basics, 
									'schedules' => '', 
									'socials' => ''
								);
			update_user_meta($user_identity, 'latitude', $basics['latitude']);
			update_user_meta($user_identity, 'longitude', $basics['longitude']);
			update_user_meta($user_identity, 'user_practices', $current_practices);
		}
		
		if( isset( $_POST['privacy'] ) && !empty( $_POST['privacy'] ) ){
			update_user_meta( $user_identity, 'privacy', $_POST['privacy'] );
		}
		
		//Awawrds
		$awards	= array();
		if( isset( $_POST['awards'] ) && !empty( $_POST['awards'] ) ){
			
			$counter	= 0;
			foreach( $_POST['awards'] as $key=>$value ){
				$awards[$counter]['name']	= 	esc_attr( $value['name'] ); 
				$awards[$counter]['date']	= 	esc_attr( $value['date'] );
				$awards[$counter]['date_formated']	= 	date('d M, Y',strtotime($value['date']));  
				$awards[$counter]['description']	= 	esc_attr( $value['description'] ); 
				$counter++;
			}
			$json['awards']	= $awards;
		}
		update_user_meta( $user_identity, 'awards', $awards );
		
		//Gallery
		$user_gallery	= array();
		if( isset( $_POST['user_gallery'] ) && !empty( $_POST['user_gallery'] ) ){
			$counter	= 0;
			foreach( $_POST['user_gallery'] as $key=>$value ){
				$user_gallery[$value['attachment_id']]['url']	= 	esc_url( $value['url'] ); 
				$user_gallery[$value['attachment_id']]['id']	= 	esc_attr( $value['attachment_id']); 
				$counter++;
			}	
		}
		update_user_meta( $user_identity, 'user_gallery', $user_gallery );
		
		//Education
		$educations	= array();
		if( isset( $_POST['education'] ) && !empty( $_POST['education'] ) ){
			$counter	= 0;
			foreach( $_POST['education'] as $key=>$value ){
				$educations[$counter]['title']		 = esc_attr( $value['title'] ); 
				$educations[$counter]['institute']	 = esc_attr( $value['institute'] ); 
				$educations[$counter]['start_date']	= esc_attr( $value['start_date'] ); 
				$educations[$counter]['end_date']	  = esc_attr( $value['end_date'] ); 
				$educations[$counter]['start_date_formated']	= date('M,Y',strtotime($value['start_date'])); 
				$educations[$counter]['end_date_formated']	  = date('M,Y',strtotime($value['end_date'])); 
				$educations[$counter]['description']			= esc_attr( $value['description'] ); 
				$counter++;
			}
			
			$json['education']	= $educations;
			
		}
		update_user_meta( $user_identity, 'education', $educations );
		
		//Experience
		$experiences	= array();
		if( !empty( $_POST['experience'] ) ){
			$counter	= 0;
			foreach( $_POST['experience'] as $key=>$value ){
				if( !empty( $value['title'] ) && !empty( $value['company'] ) ) {
					$experiences[$counter]['title']	= 	esc_attr( $value['title'] ); 
					$experiences[$counter]['company']	 = 	esc_attr( $value['company'] ); 
					$experiences[$counter]['start_date']	= 	esc_attr( $value['start_date'] ); 
					$experiences[$counter]['end_date']	  = 	esc_attr( $value['end_date'] ); 
					$experiences[$counter]['start_date_formated']  = date('M,Y',strtotime($value['start_date'])); 
					$experiences[$counter]['end_date_formated']	= date('M,Y',strtotime($value['end_date'])); 
					$experiences[$counter]['description']	= 	esc_attr( $value['description'] ); 
					$counter++;
				}
			}
			$json['experience']	= $experiences;
		}
		update_user_meta( $user_identity, 'experience', $experiences );
		
		//Specialities
		$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
		if( isset( $db_directory_type ) && !empty( $db_directory_type ) ) {
			$specialities_list	 = docdirect_prepare_taxonomies('directory_type','specialities',0,'array');
		}
		
		$specialities	= array();
		if( isset( $specialities_list ) && !empty( $specialities_list ) ){
			$counter	= 0;
			foreach( $specialities_list as $key => $speciality ){
				if( isset( $_POST['specialities'] ) && in_array( $speciality->slug, $_POST['specialities'] ) ){
					update_user_meta( $user_identity, $speciality->slug, $speciality->slug );
					$specialities[$speciality->slug]	= $speciality->name;
				}else{
					update_user_meta( $user_identity, $speciality->slug, '' );
				}
				
				$counter++;
			}
		}
		
		update_user_meta( $user_identity, 'user_profile_specialities', $specialities );
		
		
		//Languages
		$languages	= array();
		if( isset( $_POST['language'] ) && !empty( $_POST['language'] ) ){
			$counter	= 0;
			foreach( $_POST['language'] as $key=>$value ){
				$languages[$value]	= 	$value; 
				$counter++;
			}
		}
		
		update_user_meta( $user_identity, 'languages', $languages );
		
		
		//Insurance
		$insurance	= array();
		if( isset( $_POST['insurance'] ) && !empty( $_POST['insurance'] ) ){
			$counter	= 0;
			foreach( $_POST['insurance'] as $key=>$value ){
				$insurance[$value]	= 	$value; 
				$counter++;
			}
			
			$insurance	= array_filter($insurance);
		}
		
		update_user_meta( $user_identity, 'insurance', $insurance );

		//admin control
		
		if( !empty( $_POST['title_name']) && $_POST['title_name'] != '' ){
        	update_user_meta($user_identity, 'title_name', $_POST['title_name']);
		}

		if( !empty( $_POST['gende']) && $_POST['gende'] != '' ){
        	update_user_meta($user_identity, 'gende', $_POST['gende']);
		}
		
		if( isset( $_POST['kt_basics'] ) && !empty( $_POST['kt_basics'] ) ){
			foreach( $_POST['kt_basics'] as $key=>$value ){
				update_user_meta( $user_identity, $key, esc_attr( $value ) );
			}
		}
		
		//insurers
		$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
		if( isset( $db_directory_type ) && !empty( $db_directory_type ) ) {
			$insurers_list	 = docdirect_prepare_taxonomies('directory_type','insurer',0,'array');
		}
		$insurers	= array();
		if( isset( $insurers_list ) && !empty( $insurers_list ) ){
			$counter	= 0;
			foreach( $insurers_list as $key => $insurer ){
				if( isset( $_POST['insurers'] ) && in_array( $insurer->slug, $_POST['insurers'] ) ){
					update_user_meta( $user_identity, $insurer->slug, $insurer->slug );
					$insurers[$insurer->slug]	= $insurer->name;
				}else{
					update_user_meta( $user_identity, $insurer->slug, '' );
				}
				
				$counter++;
			}
		}
		
		update_user_meta( $user_identity, 'user_profile_insurers', $insurers );
		
		//update video url
		$user_video_url	= array();
		if( isset( $_POST['video_url'] ) && !empty( $_POST['video_url'] ) ){
			foreach( $_POST['video_url'] as $key=>$value ){
				$user_video_url[$key]	= $value;
			}
		}
		update_user_meta( $user_identity, 'video_url', $user_video_url );

		//paypal api
		update_user_meta( $user_identity, 'paypal_username', $_POST['paypal_username'] );
		update_user_meta( $user_identity, 'paypal_password', $_POST['paypal_password'] );
		update_user_meta( $user_identity, 'paypal_signature', $_POST['paypal_signature'] );

		//discount
		$discount	= intval($_POST['discount']);
		update_user_meta( $user_identity, 'discount', $discount );
		$discount_expired	= $_POST['discount_expired'];
		update_user_meta( $user_identity, 'discount_expired', $discount_expired );

    }

}


/**
 * @Get User Avatar
 * @return {}
 */
if (!function_exists('docdirect_get_user_avatar')) {
    function docdirect_get_user_avatar($sizes = array(), $user_identity = '') {
        extract(shortcode_atts(array(
			"width" => '300',
			"height" => '300',
			),
		$sizes));
		
		if ($user_identity != '') {
            $thumb_id	= get_user_meta($user_identity, 'userprofile_media', true);
			if( isset( $thumb_id ) && !empty( $thumb_id ) ) {
				$thumb_url = wp_get_attachment_image_src($thumb_id, array($width, $height), true);
				if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
					return $thumb_url[0];
				} else {
					$thumb_url = wp_get_attachment_image_src($thumb_id, "full", true);
					return $thumb_url[0];
				}
			}
			return false;
        }
		return false;
    }
}

/**
 * @Get User Avatar
 * @return {}
 */
if (!function_exists('docdirect_get_user_banner')) {
    function docdirect_get_user_banner($sizes = array(), $user_identity = '') {
        extract(shortcode_atts(array(
			"width"  => '300',
			"height" => '300',
			),
		$sizes));
		
		if ($user_identity != '') {
            $thumb_id	= get_user_meta($user_identity, 'userprofile_banner', true);
			if( isset( $thumb_id ) && !empty( $thumb_id ) ) {
				$thumb_url = wp_get_attachment_image_src($thumb_id, array($width, $height), true);
				if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
					return $thumb_url[0];
				} else {
					$thumb_url = wp_get_attachment_image_src($thumb_id, "full", true);
					return $thumb_url[0];
				}
			}
			return false;
        }
		return false;
    }
}

/**
 * @Get Single image
 * @return {}
 */
if (!function_exists('docdirect_get_single_image')) {
    function docdirect_get_single_image($sizes = array(), $user_identity = '') {
        extract(shortcode_atts(array(
				"width" => '300',
				"height" => '300',
			),
		$sizes));
		
		if ($user_identity != '') {
            $thumb_id	= get_user_meta($user_identity, 'email_media', true);
			if( isset( $thumb_id ) && !empty( $thumb_id ) ) {
				$thumb_url = wp_get_attachment_image_src($thumb_id, array($width, $height), true);
				if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
					return $thumb_url[0];
				} else {
					$thumb_url = wp_get_attachment_image_src($thumb_id, "full", true);
					return $thumb_url[0];
				}
			}
			return false;
        }
		return false;
    }
}

/**
 * @Import Users
 * @return {}
 */
if (!function_exists('docdirect_import_users')) {
	function  docdirect_import_users(){
		?>
        <h3 class="theme-name"><?php esc_html_e('Import Directory Users','docdirect');?></h3>
        <div id="import-users" class="import-users">
            <div class="theme-screenshot">
                <img alt="<?php esc_attr_e('Import Users','docdirect');?>" src="<?php echo get_template_directory_uri();?>/core/images/users.jpg">
            </div>
			<h3 class="theme-name"><?php esc_html_e('Import Users','docdirect');?></h3>
            <div class="user-actions">
                <a href="javascript:;" class="button button-primary doc-import-users"><?php esc_html_e('Import','docdirect');?></a>
            </div>
		</div>
        <?php
	}
}

/**
 * @Add New Users meta
 * @return {}
 */
if (!function_exists('docdirect_save_custom_user_profile_fields')) {
	function docdirect_save_custom_user_profile_fields($user_id){
		# again do this only if you can
		if(!current_user_can('manage_options'))
			return false;
	
		# save my custom field
		update_user_meta($user_id, 'verify_user', 'off');
		update_user_meta( $user_id, 'show_admin_bar_front', false );
	}
	add_action('user_register', 'docdirect_save_custom_user_profile_fields');
}

/**
 * @Get Currencies Symbol
 * @return {}
 */
if (!function_exists('docdirect_get_specialities_ajax')) {

    function docdirect_get_specialities_ajax() {
        $user_identity	= !empty( $_POST['user_id'] ) ? $_POST['user_id'] : '';
		
		$json = array();
		
		if( empty( $_POST['id'] ) ) {
			$json['type']	= 'error';
			$json['message']	= esc_html__('Some error occur, please try again later.','docdirect');
			$json['data']		= $data;
			echo json_encode($json);
			die;
		}
		
		$attached_specialities = get_post_meta( $_POST['id'], 'attached_specialities', true );
		
		
		
		$specialities_list	 = docdirect_prepare_taxonomies('directory_type','specialities',0,'array');
		
		ob_start();
		
		if( isset( $specialities_list ) && !empty( $specialities_list ) ){
			foreach( $specialities_list as $key => $speciality ){
				$db_speciality	= get_user_meta( $user_identity, $speciality->slug, true);
				$checked	= '';
				if( isset( $db_speciality ) && !empty( $db_speciality ) && $db_speciality === $speciality->slug ){
					$checked	= 'checked';
				}

				if( in_array( $speciality->term_id , $attached_specialities ) ) {
				?>
				<li>
					<div class="tg-checkbox user-selection">
						<div class="tg-packages active-user-type specialities-type">
							<input type="checkbox" <?php echo esc_attr( $checked );?> name="specialities[<?php echo esc_attr( $speciality->term_id );?>]" value="<?php echo esc_attr( $speciality->slug );?>" id="<?php echo esc_attr( $speciality->slug );?>">
							<label for="<?php echo esc_attr( $speciality->slug );?>"><?php echo esc_attr( $speciality->name );?></label>
						</div>
					</div>

				</li>
			<?php }
			}
		} else{?>
			<li>
				<div class="tg-checkbox user-selection">
					<div class="tg-packages active-user-type specialities-type">
						<label><?php esc_html_e('No specialities found','docdirect');?></label>
					</div>
				</div>

			</li>	
		<?php
		}
		
		$data	= ob_get_clean();
		
		$json['type']	= 'success';
		$json['message']	= esc_html__('found.','docdirect');
		$json['data']		= $data;
		echo json_encode($json);
		die;
		
    }

    add_action('wp_ajax_docdirect_get_specialities_ajax' , 'docdirect_get_specialities_ajax');
}
