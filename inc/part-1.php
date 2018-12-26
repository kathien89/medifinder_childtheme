<?php
//invoices column
// add_filter('manage_docdirectinvoices_posts_columns', 'kt_invoices_columns_add', 10);

function kt_invoices_columns_add($columns) {
	unset($columns['date']);
	// unset($columns['package']);
	unset($columns['price']);
	unset($columns['payment_option']);
	unset($columns['title']);
	$columns['user'] 		= pll__('User','docdirect_core');
	$columns['package'] 			= pll__('Package','docdirect_core');
	$columns['payment_option'] 		= pll__('Payment Method','docdirect_core');
	$columns['price'] 		= pll__('Price','docdirect_core');
 
		return $columns;
}


function kt_membership(){   
	output();
}

function output() {
	?>
	<div class="wrap">
		<h2>Membership</h2>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>
	<?php
}



/**
 * @Generate Menu Link
 * @Returns 
 */
function kt_docdirect_get_avatar(){
	global $current_user, $wp_roles,$userdata,$post;
	$reference = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : $reference	= '';
	$user_identity	= $current_user->ID;
	$current_date = date('Y-m-d H:i:s');
	
	$user_identity	= $user_identity;
	if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
		$user_identity	= $_GET['identity'];
	}
	
	$avatar = apply_filters(
					'docdirect_get_user_avatar_filter',
					 docdirect_get_user_avatar(array('width'=>270,'height'=>270), $user_identity) //size width,height
				);
    if(kt_is_company($user_identity)){
      $company_logo_id = get_user_meta($user_identity, 'userprofile_company_logo', true);
      if ( isset( $company_logo_id ) && !empty( $company_logo_id ) ) {
        $avatar = docdirect_get_image_source($company_logo_id,full,full);
      }
    }
	
	$featured_date	= get_user_meta($user_identity, 'user_featured', true);
	
	$featured_string   = $featured_date;
	$current_string	= strtotime( $current_date );
	$tagline   		   = get_user_meta( $user_identity, 'tagline', true);
	$first_name   		   = get_user_meta( $user_identity, 'first_name', true);
	$last_name   		   = get_user_meta( $user_identity, 'last_name', true);
	$display_name   		   = get_user_meta( $user_identity, 'display_name', true);
	
	if( !empty( $first_name ) || !empty( $last_name ) ){
		$username	= kt_get_title_name().$first_name.' '.$last_name;
	} else{
		$username	= kt_get_title_name().$display_name;
	}
	?>
	<div class="tg-widget tg-widget-doctor">
		<figure class="tg-docprofile-img">
            <?php 
				$user_roles = $current_user->roles;
				if( $user_roles[0] == 'professional' ){
					kt_docdirect_get_featured_tag(true,$current_user->ID,'v2');
	            	docdirect_get_verified_tag(true,$current_user->ID,'','v2');
	            }
            ?>
			<figcaption>
				<h4><?php echo esc_attr( $username );?></h4>
				<?php if ( isset( $tagline ) && !empty( $tagline ) ) :  ?>
					<span><?php echo esc_attr($tagline); ?></span>
				<?php endif; ?>
			</figcaption>
            <?php docdirect_get_wishlist_button($current_user->ID,true);?>
			<?php //if( isset( $featured_string ) && $featured_string > $current_string ){?>
                <?php //kt_docdirect_get_featured_tag(true);?>
            <?php //}?>
            <?php //docdirect_get_verified_tag(true,$current_user->ID);?>
            <a><img width="370" height="370" src="<?php echo esc_url( $avatar );?>" alt="<?php esc_html_e('Avatar','docdirect');?>"  /></a>
		</figure>
	</div>
<?php
}

function kt_ajax_change_profile_public(){
	global $current_user;
	$user_identity	= $current_user->ID;

  	$data = isset( $_POST['data'] ) ? esc_sql( $_POST['data'] ) : '';  
  	update_user_meta($user_identity, 'public_profile', $data);

	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;

}
add_action('wp_ajax_change_profile_public', 'kt_ajax_change_profile_public');
add_action('wp_ajax_nopriv_change_profile_public', 'kt_ajax_change_profile_public');

function kt_ajax_change_profile_booking(){
	global $current_user;
	$user_identity	= $current_user->ID;

	$privacy		= docdirect_get_privacy_settings($user_identity);
	$privacy['appointments'] = $_POST['data'];
	update_user_meta( $user_identity, 'privacy', docdirect_sanitize_array( $privacy ) );
	
	//update privacy for search
	/*if( !empty( $_POST['privacy'] ) ) {
		foreach( $_POST['privacy'] as $key => $value ) {
			update_user_meta( $user_identity, $key, esc_attr( $value ) );
		}
	}*/

	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;

}
add_action('wp_ajax_change_profile_booking', 'kt_ajax_change_profile_booking');
add_action('wp_ajax_nopriv_change_profile_booking', 'kt_ajax_change_profile_booking');

function kt_custom_profile_link(){
	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;
	
	$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
    $terms = get_the_terms($db_directory_type, 'group_label');
    $list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
    $current_group_label_slug = $terms[0]->slug;
    $user_premium = get_user_meta($user_identity , 'user_premium' , true);
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
	<div class="tg-widget tg-widget-doctor">
		<?php
			$val = get_user_meta($user_identity, 'public_profile', true);
			$checked = 'checked';
			if ( $val == 'off' ) {
				$checked = '';
			}
		?>
	    <input id="change_profile_public" <?php echo $checked;?> type="checkbox" data-width="100">
		<script>
		  jQuery(function($) {
		    $('#change_profile_public').bootstrapToggle({
		      on: '<?php pll_e('<i class="fa fa-eye"></i>Profile Public');?>',
		      off: '<?php pll_e('<i class="fa fa-lock"></i>Profile Private');?>'
		    });

			var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	
		    $('#change_profile_public').change(function() {
		      if(this.checked) {
		      	data = 'on';
        	  }else {
		      	data = 'off';
        	  }
			  $('body').append(loder_html);
	          jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'action' : 'change_profile_public',
	            	'data' : data
	            },
	            dataType:"json",
	            success: function(response) {

					jQuery('body').find('.docdirect-site-wrap').remove();
	                            
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});

	            }
	          });
		    })
		  })
		</script>
	</div>
	<?php if($current_option['patient_bookings'] != ''){?>
	<div class="tg-widget tg-widget-doctor change_profile_booking">
		<?php
			$privacy		= docdirect_get_privacy_settings($user_identity);
			// $val = get_user_meta($user_identity, 'public_profile', true);
			$checked = '';
			if ( isset( $privacy['appointments'] ) && $privacy['appointments'] === 'on' ) {
				$checked = 'checked';
			}
		?>
	    <input id="change_profile_booking" <?php echo $checked;?> type="checkbox" data-width="100">
		<script>
		  jQuery(function($) {
		    $('#change_profile_booking').bootstrapToggle({
		      off: '<?php pll_e('<i class="fa fa-envelope"></i>Request Only Setting');?>',
		      on: '<?php pll_e('<i class="fa fa-calendar"></i>Booking Enable');?>'
		    });

			var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	
		    $('#change_profile_booking').change(function() {
		      if(this.checked) {
		      	data = 'on';
        	  }else {
		      	data = 'off';
        	  }
			  $('body').append(loder_html);
	          jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'action' : 'change_profile_booking',
	            	'data' : data
	            },
	            dataType:"json",
	            success: function(response) {

					jQuery('body').find('.docdirect-site-wrap').remove();
	                            
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});

	            }
	          });
		    })
		  })
		</script>
	</div>
	<?php }?>
	<div class="tg-widget tg-widget-doctor">
		<?php 
			$page = get_page_by_path('professionalclinic-guide');
			$link = $page->ID;
            // $trans_id =  pll_get_post($page->ID);
		?>
		<a class="link_guide" target="_blank" href="<?php echo get_permalink($link);?>"><i class="fa fa-info-circle" aria-hidden="true"></i><?php pll_e('Profile Setup Guide');?></a>
	</div>
<?php
}

function kt_get_number_booking($user_id) {

	$meta_query_args[] = array(
								'key'     => 'bk_user_to',
								'value'   => $user_id,
								'compare'   => '=',
								'type'	  => 'NUMERIC'
							);
								
	$meta_query_args[] = array(
								'key'     => 'bk_status',
								'value'   => 'pending',
								'compare'   => '='
							);
											
	        
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
	$args 		= array( 'posts_per_page' => -1, 
						 'post_type' => 'docappointments', 
						 'post_status' => 'publish', 
						 'ignore_sticky_posts' => 1,
						 'order'	=> 'DESC',
						 'orderby'	=> 'ID',
						 'lang' => 'en'
						);


	if( !empty( $meta_query_args ) ) {
		$query_relation = array('relation' => 'AND',);
		$meta_query_args	= array_merge( $query_relation,$meta_query_args );
		$args['meta_query'] = $meta_query_args;
	}
	$query 		= new WP_Query($args);

	return $query->post_count;
}

function kt_button_premium_menu( $pagename='' ) {

	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;

    // $user_premium = get_user_meta($user_identity , 'user_premium' , true);
    // $current_option = get_option( $user_premium, true );
    
	$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
    $terms = get_the_terms($db_directory_type, 'group_label');
    $list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
    $current_group_label_slug = $terms[0]->slug;
    $user_premium = get_user_meta($user_identity , 'user_premium' , true);
    if( in_array('company', $list_terms) ||
            in_array('medical-centre', $list_terms) ||
            in_array('hospital-type', $list_terms) ||
            in_array('scans-testing', $list_terms)
    ) {
        $current_option = get_option( 'company_'.$user_premium, true );
    }else {
        $current_option = get_option( $user_premium, true );
    }

    if( !isset($current_option[$pagename]) ){
		$dir_profile_page = '';
		if (function_exists('fw_get_db_settings_option')) {
	        $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
	    }
		$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
		
		$invoices_url = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity,true);
		
		echo '<a class="doc-btn btn-primary" href="'.$invoices_url.'">'.pll__("Premium").'</a>';
		
	}
}

function kt_group_label() {

	$member_group_terms = get_terms( array(
						    'taxonomy' => 'group_label',
						    // 'orderby' => 'menu_order',
						    'i_order_terms' => true,
						    'parent'   => 0,
						    'hide_empty' => false,
						) );
	?>
    <div class="form-group">
		<input class="select_category" type="hidden" name="directory_type" value="" />
    	<a class="dropdown-button-group" href="javascript:;"><?php pll_e('Category')?></a>
    	<div class="dropdown-input-group">
    		<div class="dropdown-wrap">
			<?php
			foreach ( $member_group_terms as $member_group_term ) {
			    $member_group_query = new WP_Query( array(
			        'post_type' => 'directory_type',
			        'posts_per_page' => -1,
			        'tax_query' => array(
			            array(
			                'taxonomy' => 'group_label',
			                'field' => 'slug',
			                'terms' => array( $member_group_term->slug ),
			                'operator' => 'IN'
			            )
			        )
			    ) );
			    ?>
			    <h6><?php echo $member_group_term->name; ?></h6>
			    <ul>
			    <?php
			    if ( $member_group_query->have_posts() ) : while ( $member_group_query->have_posts() ) : $member_group_query->the_post(); 
			    	$trans_id = pll_get_post(get_the_ID());

					$category_image = fw_get_db_post_option(get_the_ID(), 'category_image', true);

					if( !empty( $category_image['attachment_id'] ) ){
						$banner_url	= docdirect_get_image_source($category_image['attachment_id'],150,150);
				 		$banner	= '<img width="150" height="150" src="'.$banner_url.'">';
			  		} else{
				 		$banner	= '<i class="fa '.$dir_icon.'"></i>';
				 	}
			    ?>
			        <li data-slug="<?php echo get_the_slug($trans_id);?>" data-id="<?php echo get_the_ID();?>">
			        	<?php $dir_icon = fw_get_db_post_option(get_the_ID(), 'dir_icon', true);?>
			        	<?php echo $banner; ?>
			        	<?php echo the_title(); ?>
			        	
			        </li>
			    <?php endwhile; endif; ?>
			    </ul>
			    <?php
			    // Reset things, for good measure
			    $member_group_query = null;
			    wp_reset_postdata();
			}
			?>
			</div>
            <a class="close_specialities_wrap" href="javascript:;">
            	<i class="fa fa-close"></i>
            	<span><?php esc_html_e('Close','docdirect'); ?></span>
          	</a>
		</div>
	</div>
	<div class="form-group specialities">
		<input class="select_category" type="hidden" name="insurer" value="" />
    	<a class="dropdown-button-group" href="javascript:;"><?php pll_e('Specialities')?></a>
    	<div class="dropdown-input-group">
    		<div class="dropdown-wrap">
			</div>
            <a class="close_specialities_wrap" href="javascript:;">
            	<i class="fa fa-close"></i>
            	<span><?php esc_html_e('Close','docdirect'); ?></span>
          	</a>
    	</div>
	</div>
	<div class="form-group insurers">
		<?php
			$current_insurer_text = pll__('Insurers');
			$current_insurer  	   = !empty( $_GET['insurer'] ) ? $_GET['insurer'] : '';
			if ( $current_insurer != '' ) {
				$insurer	= get_term_by( 'slug', $current_insurer, 'insurer');
				$current_insurer_text = $insurer->name;
			}

		?>
    	<a class="dropdown-button-group" href="javascript:;"><?php echo $current_insurer_text;?></a>
		<input class="select_category" type="hidden" name="insurer" value="" />
    	<div class="dropdown-input-group">
    		<div class="dropdown-wrap">
		        <li data-slug=""><?php pll_e('Search All');?></li>
              	<?php                                     
				$insurers_list	 = docdirect_prepare_taxonomies('directory_type','insurer',0,'array');
					if( isset( $insurers_list ) && !empty( $insurers_list ) ){
						foreach( $insurers_list as $key => $insurer ){
						?>
						<?php
                        $sample_bg_url = get_template_directory_uri().'/images/sample-insurer.png';
                        $bg_url = ($image[0]!='') ? $image[0] : $sample_bg_url;
						?>
				        <li data-slug="<?php echo $insurer->slug;?>">
				        	<img width="150" height="150" src="<?php echo $bg_url; ?>" >
				        	<?php echo esc_attr( $insurer->name ); ?>
				        	
				        </li>

                <?php }}?>
			</div>
            <a class="close_specialities_wrap" href="javascript:;">
            	<i class="fa fa-close"></i>
            	<span><?php esc_html_e('Close','docdirect'); ?></span>
          	</a>
    	</div>
	</div>
	<?php

}


add_action('kt_docdirect_init_headers', 'kt_direct_sidebar_mobile', 9, 2);
function kt_direct_sidebar_mobile() {
	?>
	<div id="c-menu--slide-left" class="c-menu c-menu--slide-left menu-right">
	<div class="wrap_slideleft">
        <div class="doc-user container">
            <div class="row">
				<?php
           		global $current_user, $wp_roles,$userdata,$post;
				if( is_user_logged_in() ) {
                    $user_identity  = $current_user->ID;
                    $avatar = apply_filters(
                        'docdirect_get_user_avatar_filter',
                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_identity) //size width,height
                    );
                        if(kt_is_company($user_identity)){
                          $company_logo_id = get_user_meta($user_identity, 'userprofile_company_logo', true);
                          if ( isset( $company_logo_id ) && !empty( $company_logo_id ) ) {
                            $avatar = docdirect_get_image_source($company_logo_id,full,full);
                          }
                        }
                    
                    $first_name            = get_user_meta( $user_identity, 'first_name', true);
                    $last_name              = get_user_meta( $user_identity, 'last_name', true);
                    $display_name            = get_user_meta( $user_identity, 'display_name', true);
                    
                    if( !empty( $first_name ) ){
                        $username   = $first_name;
                    } else if( !empty( $last_name ) ){
                        $username   = $last_name;
                    } else{
                        $username   = $display_name;
                    }
                   	$username = kt_get_title_name().$first_name.' '.$last_name;

					$today = current_time( 'timestamp' );
					$user_featured = get_user_meta($user_identity, 'user_featured', true);
					$author = get_userdata( $user_identity );
					$user_roles = $author->roles;

					$dir_profile_page = '';
					if (function_exists('fw_get_db_settings_option')) {
					    $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
					}
					$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';

					if ( defined( 'POLYLANG_VERSION' ) ) {
						$profile_page = pll_get_post($profile_page);
					}


					if( $user_roles[0] == 'professional' ){

						$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
					    $terms = get_the_terms($db_directory_type, 'group_label');
					    $list_terms = array();
					    foreach ($terms as $key => $value) {
					        $list_terms[] = $value->slug;
					    }
					    $current_group_label_slug = $terms[0]->slug;
					    $user_premium = get_user_meta($current_author_profile->ID , 'user_premium' , true);
					    if( in_array('company', $list_terms) ||
					            in_array('medical-centre', $list_terms) ||
					            in_array('hospital-type', $list_terms) ||
					            in_array('scans-testing', $list_terms)
					    ) {
							$bien = get_the_title($db_directory_type);
					    }else {
							$bien = pll__('Professional');
					    }
					    
						$view_link = get_author_posts_url($current_user->ID);
						$view_text = pll__('View Public Page');

						$ava_link = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'bookings', $user_identity, true);

					} else {

						$bien = pll__('Patient');
						$view_link = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'wishlist', $user_identity, true);
						$view_text = pll__('My Favourites');
						$ava_link = 'javascript:;';
					}
                ?>
                    <div class="col-xs-4">
                        <a class="doc-avatar" href="<?php echo $ava_link;?>"><img width="150" height="150" src="<?php echo esc_url( $avatar );?>" alt="<?php esc_html_e('Welcome','docdirect');?>">
                        <?php
                            $var = kt_get_number_booking($current_user->ID);
                            if ( $var > 0 ) {
                                echo '<span class="number_booking">'.$var.'</span>';
                            }
                        ?>
                        </a>
                    </div>
                    <div class="col-xs-8">
                    	<h3><?php echo esc_attr( $username );?></h3>
                    	<span><?php echo $bien;?></span>
                    	<div class="clearfix"></div>
	            		<a class="btn btn-default" target="_blank" href="<?php echo $view_link;?>"><?php echo $view_text;?></a>
	            	</div>
            <?php } else {?>                	
                <div class="col-xs-12">
                	<?php
	            	$main_logo      = '';
	                $main_logo = fw_get_db_settings_option('main_logo');
		            if (isset($main_logo['url']) && !empty($main_logo['url'])) {
		                $logo = $main_logo['url'];
		            } else {
		                $logo = get_template_directory_uri() . '/images/logo.png';
		            }
           			?>
                  <strong class="doc-logo">                  	
					<a class="" href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo()); ?>"></a>
                  </strong>
                </div>
            <?php }?>
           </div>
       	</div>

		<ul class="nav nav-pills">
		  <li class="active"><a data-toggle="tab" href="#mainmenu"><i class="fa fa-home"></i><?php pll_e( 'Main Menu' );?></a></li>
		  <li class="tabsearch"><a data-toggle="tab" href="#tabsearch"><i class="fa fa-search"></i></a></li>
		  <?php if( is_user_logged_in() ) {?>
		  <li><a data-toggle="tab" href="#dashboard"><i class="fa fa-cog"></i><?php pll_e( 'Dashboard' );?></a></li>
		  <?php }else {?>
		  	 <li><a href="javascript:;" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-sign-out"></i><?php pll_e('Login/Register');?></a></li>
		  <?php }?>
		</ul>

		<div class="tab-content">
		  <div id="mainmenu" class="tab-pane fade in active">
           	<div class="doc-languages">
           		<?php 
           		/*$current_lang = pll_current_language('slug');
           		$trans = pll_the_languages(array('show_flags' => 1,
		           								'show_names' => 1, 
		           								'dropdown' => 0, 
		           								'post_id' => get_the_ID(), 
		           								// 'hide_current' => 1 ,
		           								'raw' => 1
           								));
           		$output = '';
           		foreach ($trans as $key => $value) {
           			if ($value['slug'] == $current_lang) {
           				$current_output = $value['flag'].' <span>'.$value['name'].'</span>';
           			} else {
		            	$output .= '<li><a href="'.$value['url'].'">'.$value['flag'].' <span>'.$value['name'].'</span></a></li>';
           			}
           			
		        }
		 
		        echo '<a href="javascript:;" class="collapsed" data-toggle="collapse" data-target="#doc-lang" aria-expanded="false">'.$current_output.'</a>';
		        echo '<ul id="doc-lang" class="collapse collapse">';
		        echo $output;
		        echo '</ul>';*/
           		?>
           	</div>
		  	<h4><?php pll_e( 'Main Menu' );?></h4>
		  	<?php
                if ( has_nav_menu( 'main-menu' ) ) {
                     wp_nav_menu( array( 'theme_location' => 'main-menu', 'menu_class' => 'nav navbar-nav', 'container_class' => 'main-menu' ) );
                }                
            ?>
		  	<h4><?php pll_e( 'Infomation' );?></h4>
		  	<?php
                if ( has_nav_menu( 'main-menu' ) ) {
                     wp_nav_menu( array( 'menu' => 'infomation', 'menu_class' => 'nav navbar-nav', 'container_class' => 'info-menu' ) );
                }                
            ?>
		  </div>
		  <div id="tabsearch" class="tab-pane fade">
		  	<?php
			$dir_search_page 		= fw_get_db_settings_option('dir_search_page');
			$dir_search_pagination  = fw_get_db_settings_option('dir_search_pagination');
			$dir_longitude 			= fw_get_db_settings_option('dir_longitude');
			$dir_latitude 			= fw_get_db_settings_option('dir_latitude');
			$google_key 			= fw_get_db_settings_option('google_key');
			
			$dir_keywords 			= fw_get_db_settings_option('dir_keywords');
			$zip_code_search 		= fw_get_db_settings_option('zip_code_search');
			$dir_location 			= fw_get_db_settings_option('dir_location');
			$dir_radius 			= fw_get_db_settings_option('dir_radius');
			$language_search 		= fw_get_db_settings_option('language_search');
			$dir_search_cities 		= fw_get_db_settings_option('dir_search_cities');
			
			
			$dir_longitude			= !empty( $dir_longitude ) ? $dir_longitude : '-0.1262362';
			$dir_latitude		 	= !empty( $dir_latitude ) ? $dir_latitude : '51.5001524';
			
			$insurer  	   = !empty( $_GET['insurer'] ) ? $_GET['insurer'] : '';
			$insurance  	   = !empty( $_GET['insurance'] ) ? $_GET['insurance'] : '';
			$photos  	   	   = !empty( $_GET['photos'] ) ? $_GET['photos'] : '';
			$appointments      = !empty( $_GET['appointments'] ) ? $_GET['appointments'] : '';
			$city      		   = !empty( $_GET['city'] ) ? $_GET['city'] : '';

			$dir_search_page = fw_get_db_settings_option('dir_search_page');
			if( isset( $dir_search_page[0] ) && !empty( $dir_search_page[0] ) ) {
				$search_page 	 = get_permalink((int)$dir_search_page[0]);
				$search_page 	 = pll_get_post((int)$dir_search_page[0]);
				$search_page 	 = get_permalink($search_page);
			} else{
				$search_page 	 = '';
			}
			
			$languages_array	= docdirect_prepare_languages();//Get Language Array
		  	?>
		  	    
            <ul class="nav nav-pills">
              <li class="active"><a data-toggle="tab" href="#speacialties"><?php pll_e('Specialty Search');?></a></li>
              <li><a data-toggle="tab" href="#doctors"><?php pll_e('Name Search');?></a></li>
            </ul>

            <div class="tab-content">
                <div id="speacialties" class="tab-pane fade in active">  
                    <form class="tg-searchform directory-map" action="<?php echo esc_url( $search_page);?>" method="get" id="directory-map3">
                        <fieldset>
                           <?php if( isset( $dir_keywords ) && $dir_keywords === 'enable' ){?>
                              <div class="form-group">
                                <input type="text" name="by_name" placeholder="<?php pll_e('Type Name...');?>" class="form-control">
                              </div>
                            <?php }?>
                            <?php 
                                if (function_exists('kt_direct_search')) {
                                    kt_direct_search();
                                }
                            ?>
                            
			                <?php if( isset( $dir_location ) && $dir_location === 'enable' ){?>
			                  <div class="form-group">
			                    <div class="tg-inputicon tg-geolocationicon tg-angledown">
			                        <?php kt_docdirect_locateme_snipt2();?>
			                     </div>
			                  </div>
			                <?php }?>
			                <?php if( !empty( $zip_code_search ) && $zip_code_search === 'enable' ){?>
			                  <div class="form-group">
			                    <input type="text" class="form-control" value="<?php echo esc_attr( $zip_code );?>" name="zip" placeholder="<?php esc_html_e('Search users by zip code','docdirect');?>">
			                  </div>
			                <?php }?>
			                <div class="form-group toggle_filter">
			                  	<a class="open" href="javascript:;">
			                  		<i class="fa fa-plus"></i>
			                  		<span class="close_filters"><?php pll_e('Less Filters','docdirect');?></span>
			                  		<span class="more_filters"><?php pll_e('More Filters','docdirect');?></span>
			                  	</a>
			                </div>
			                <?php if( !empty( $dir_search_cities ) && $dir_search_cities === 'enable' ){?>
			                <div class="form-group">
			                    <div class="doc-select">
			                      <select name="city" class="chosen-select">
			                        <option value=""><?php pll_e('Select city','docdirect');?></option>
			                        <?php docdirect_get_term_options($city,'locations');?>
			                      </select>
			                   </div>
			                </div>
			                <?php }?>
			                <div class="form-group">
			                	<?php  
			                		$min_price = (isset($_GET['min_price'])) ? $_GET['min_price'] : '0';
			                		$max_price = (isset($_GET['max_price'])) ? $_GET['max_price'] : '0';
			                	?>
			                	<div class="slider_wrap">		                    
									<div class="parent_slider"><div id="slider1"></div></div>
									<div id="slider-number">
										<span id="span_min_price1" class="slider-number-start">$<?php echo intval($min_price);?></span><span  id="span_max_price1" class="slider-number-end">$<?php echo $ret = (isset($_GET['max_price'])) ? $_GET['max_price'] : '5000' ; ;?></span>
									</div>
								</div>
								<input id="min_price1" type="hidden" name="min_price" value="<?php echo intval($min_price);?>">
								<input id="max_price1" type="hidden" name="max_price" value="<?php echo $ret = (isset($_GET['max_price'])) ? $_GET['max_price'] : '5000';?>">
								<script>
									jQuery(document).ready(function(e) {
										var min_price = $('#min_price1').val();
										var max_price = $('#max_price1').val();
										var slider = document.getElementById('slider1');

										noUiSlider.create(slider, {
											start: [min_price, max_price],
											connect: true,
											step: 1,
											range: {
												'min': 0,
												'max': 5000
											}
										});
										var inputFormat = document.getElementById('min_price1');
										var inputFormat2 = document.getElementById('max_price1');

										var divFormat = document.getElementById('span_min_price1');
										var divFormat2 = document.getElementById('span_max_price1');

										slider.noUiSlider.on('update', function( values, handle ) {
											inputFormat.value = parseInt(values[0]);
											inputFormat2.value = parseInt(values[1]);

											divFormat.innerHTML = '$'+parseInt(values[0]);
											divFormat2.innerHTML = '$'+parseInt(values[1]);

										});

									});
								</script>
			                </div>
			                <?php if( isset( $language_search ) && $language_search === 'enable' ){?>
			                <?php  if( isset( $languages_array ) && !empty( $languages_array ) ){?>
			                <div class="form-group">
			                  <div class="doc-select">     
			                     <select name="languages" class="chosen-select" data-placeholder="<?php pll_e('Select languages','docdirect');?>">
			                        <option value=""><?php pll_e('Select languages','docdirect');?></option>
			                     <?php 
			                        foreach( $languages_array as $key=>$value ){
			                            $selected	= '';
			                            if( !empty( $_GET['languages'] ) && $key == $_GET['languages'] ){
			                                $selected	= 'selected';
			                            }
			                            ?>
			                            <option <?php echo esc_attr( $selected );?> value="<?php echo esc_attr( $key );?>"><?php echo esc_attr( $value );?></option>
			                     <?php }?>
			                    </select>
			                   </div>
			                </div>
			                <?php }?>
			                <?php }?>
			                <div class="form-group">
			                  <div class="doc-select">     
			                     <select name="gender" class="chosen-select" data-placeholder="<?php pll_e('Any Gender','docdirect');?>">
			                        <option value=""><?php pll_e('Any Gender','docdirect');?></option>
			                        <option value="male" <?php echo ($_GET['gender'] == 'male' ) ? 'selected' : '' ;?>><?php pll_e('Male','docdirect');?></option>
			                        <option value="female" <?php echo ($_GET['gender'] == 'female' ) ? 'selected' : '' ;?>><?php pll_e('Female','docdirect');?></option>
			                    </select>
			                   </div>
			                </div>
                        </fieldset>
                        <button class="doc-btn" type="submit"><i class="fa fa-search"></i><?php pll_e('Find a Professional');?></button>
                    </form>
                </div>
                  <div id="doctors" class="tab-pane fade">                  
                    <form class="tg-searchform directory-map" action="<?php echo esc_url( $search_page);?>" method="get" id="directory-map4">
                        <fieldset>
                            <?php 
                                if (function_exists('kt_search_insurers')) {
                                    kt_search_insurers();
                                }
                            ?>
                        </fieldset>
                        <button class="doc-btn" type="submit"><i class="fa fa-search"></i><?php pll_e('Find a Professional');?></button>
                    </form>
                  </div>
            </div>
		  </div>
		  <div id="dashboard" class="tab-pane fade">
		  	<?php if( is_user_logged_in() ) {
				$user_identity	= $current_user->ID;
				$user_role = get_user_meta('roles',$user_identity );
				$reference = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : $reference	= '';
				$dir_profile_page = '';
				if (function_exists('fw_get_db_settings_option')) {
	                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
	            }
				$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
				
				if ( defined( 'POLYLANG_VERSION' ) ) {
					$profile_page = pll_get_post($profile_page);
				}
                $directory_type    = get_user_meta( $user_identity, 'directory_type', true);
                $article_switch    = fw_get_db_post_option($directory_type, 'articles', true);
			?>
		  		<h4><?php pll_e( 'Dashboard' );?></h4>
		  		<ul class="nav navbar-nav">
                         <li class="<?php echo ( $reference === 'settings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'settings', $user_identity); ?>"><i class="fa fa-gears"></i><?php pll_e('Profile Information');?></a></li>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                         <li class="tg-privatemessages tg-hasdropdown menu-item-has-children <?php echo ( $reference === 'practices' ? 'active':'');?>">
                         	<a href="javascript:;">
                                <i class="fa fa-map-marker"></i><span><?php pll_e('Manage Practices');?></span>
                         	</a>
                            <ul class="sub-menu">
                                <li class="<?php echo ( $reference === 'practices' && $mode === 'listing' ? 'tg-active' : ''); ?>">
                                    <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'listing'); ?>">
                                		<i class="fa fa-list-ul"></i>
                                        <span><?php esc_html_e('Practice Manager', 'docdirect'); ?></span>
                                    </a>
                                </li>
                                <li class="<?php echo ( $reference === 'practices' && $mode === 'add' ? 'tg-active' : ''); ?>">
                                    <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'add'); ?>">
                                		<i class="fa fa-plus-circle"></i><span><?php esc_html_e('Add New Practice', 'docdirect'); ?></span>
                                    </a>
                                </li>
                            </ul>
                         </li>
                        <?php }?>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                            <li class="<?php echo ( $reference === 'affiliation' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'affiliation', $user_identity); ?>"><i class="fa fa-user-plus"></i><?php pll_e('Add Affiliation');?></a><?php kt_button_premium_menu('affiliations');?></li>
                        <?php }?>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                            <li class="<?php echo ( $reference === 'privacy-settings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'privacy-settings', $user_identity); ?>"><i class="fa fa-eye"></i><?php pll_e('Privacy Settings');?></a></li>
                        <?php }?>
                        
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <h4 class="separator"><?php pll_e('Appointments');?></h4>
                        <?php
                        $db_directory_type   = get_user_meta( $user_identity, 'directory_type', true);
                        $terms = get_the_terms($db_directory_type, 'group_label');
                        $current_group_label_slug = $terms[0]->slug;
                        $user_premium = get_user_meta($user_identity , 'user_premium' , true);
                        // if($current_group_label_slug != 'medical-centre') {
                            $current_option = get_option( $user_premium, true );
                            if (isset($current_option['patient_bookings'])) {
                                ?>
                            <li class="<?php echo ( $reference === 'bookings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'bookings', $user_identity); ?>"><i class="fa fa-book"></i><?php pll_e('Patient Bookings');?><?php
                                    $var = kt_get_number_booking($user_identity);
                                    if ( $var > 0 ) {
                                        echo '<span class="number_booking">'.$var.'</span>';
                                    }
                                ?></a><?php kt_button_premium_menu('patient_bookings');?></li>
                                <?php
                            }
                        // }
                        ?>
                        <?php }else {?>
                            <li class="<?php echo ( $reference === 'mybookings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'mybookings', $user_identity); ?>"><i class="fa fa-book"></i><?php pll_e('My Bookings');?></a></li>
                        <?php }?>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                            <li class="<?php echo ( $reference === 'booking-schedules' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'booking-schedules', $user_identity); ?>"></i><i class="fa fa-calendar-check-o"></i><?php pll_e('Booking Schedule');?></a></li>
                        <?php }?>
                         <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                            <li class="<?php echo ( $reference === 'booking-settings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'booking-settings', $user_identity); ?>"><i class="fa fa-cog"></i><?php pll_e('Email/Payout Options');?></a></li>
                        <?php }?>

						<?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <h4 class="separator"><?php pll_e('Engagement');?></h4>
                            <li class="<?php echo ( $reference === 'invite-review' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invite-review', $user_identity); ?>"><i class="fa fa-paper-plane"></i><?php pll_e('Invite Patient Review');?></a></li>
                        <?php }?>
                        <?php if( isset( $article_switch ) && $article_switch === 'enable' ){?>
                            <?php if ( function_exists('fw_get_db_settings_option') && fw_ext('articles')) { ?>
                                <li class="tg-privatemessages tg-hasdropdown menu-item-has-children <?php echo ( $reference === 'articles' ? 'tg-active' : ''); ?>">
                                    <a id="tg-btntoggle" class="tg-btntoggle" href="javascript:;">
                                    	<i class="fa fa-file-text"></i><span><?php esc_html_e('Manage Articles', 'docdirect'); ?></span>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="<?php echo ( $mode === 'listing' ? 'tg-active' : ''); ?>">
                                            <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'articles', $user_identity, '', 'listing'); ?>">
                                				<i class="fa fa-list-ul"></i>
                                                <span><?php esc_html_e('Article Manager', 'docdirect'); ?></span>
                                        		<em class="tg-totalmessages"><?php echo intval(docdirect_get_total_articles_by_user($user_identity)); ?></em>
                                            </a>
                                        </li>
                                        <li class="<?php echo ( $mode === 'add' ? 'tg-active' : ''); ?>">
                                            <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'articles', $user_identity, '', 'add'); ?>">
                                				<i class="fa fa-plus-circle"></i>
                                                <span><?php esc_html_e('Add New Article', 'docdirect'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        

		  		</ul>
		  		<h4><?php pll_e( 'Account Settings' );?></h4>
		  		<ul class="nav navbar-nav">
                        <li class="grey <?php echo ( $reference === 'wishlist' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'wishlist', $user_identity); ?>"><i class="fa fa-heart"></i><?php pll_e('My Favourites');?></a></li>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="grey <?php echo ( $reference === 'dashboard' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'dashboard', $user_identity); ?>"><i class="fa fa-line-chart"></i><?php pll_e('Reviews & Statistics');?></a></li>
                        <?php }?>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                            <li class="grey <?php echo ( $reference === 'invoices' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity); ?>"><i class="fa fa-money"></i><?php pll_e('Upgrade Membership');?></a></li>
                        <?php }?>
                        <li class="grey <?php echo ( $reference === 'security' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'security', $user_identity); ?>"><i class="fa fa-lock"></i><?php pll_e('Change Password');?></a></li>
		  		</ul>
			<?php }?>
		  </div>
		</div>

	  	<div class="bottom">
	  		<?php
	            if (function_exists('fw_get_db_settings_option')) {
					$header_type = fw_get_db_settings_option('header_type');
	            }
	  		?>
					<?php if( !empty( $header_type['header_v2']['social_icons'] ) ){?>
						<ul class="tg-socialicon">
							<?php 
								$social_icons	= $header_type['header_v2']['social_icons'];
								if(isset($social_icons) && !empty($social_icons)){
									foreach($social_icons as $social){
										?>
										<li>
											<?php
											$url = '';
											if(isset($social['social_url']) && !empty($social['social_url'])){
												$url = 'href="'.esc_url( $social['social_url'] ).'"';
											}else{
												$url = 'href="#"';
											} 
											?>
											<a target="_blank" <?php echo ($url); ?>>
												<?php if(isset($social['social_icons_list']) && !empty($social['social_icons_list'])) { ?>
												<i class="<?php echo esc_attr($social['social_icons_list']); ?>"></i>
												<?php } ?>
											</a>
										</li>
										<?php
									}
								}
							?>  
						 </ul>
					<?php }?>
	  		<?php if( is_user_logged_in() ) {
	  		?>
	  			<a class="pull-right" href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><i class="fa fa-power-off"></i><?php pll_e('Sign out');?></a>
	  		<?php
	  		}
	  		?>
	  	</div>
  	</div>
		<div id="c-mask" class="c-mask">
	  		<button class="c-menu__close"><i class="fa fa-remove"></i></button>
	  	</div><!-- /c-mask -->
	</div>


	<?php
}


/*******************************/
// A callback function to add a custom field to our "specialities" taxonomy
function specialities_taxonomy_custom_fields($tag) {
   // Check for existing taxonomy meta for the term you're editing
    $t_id = $tag->term_id; // Get the ID of the term you're editing
    $term_meta = get_option( "taxonomy_term_$t_id" ); // Do the check
	?>

	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="s_keyword"><?php _e('Search Keyword'); ?></label>
		</th>
		<td>
			<input type="text" name="term_meta[s_keyword]" id="term_meta[s_keyword]" size="25" style="width:60%;" value="<?php echo $term_meta['s_keyword'] ? $term_meta['s_keyword'] : ''; ?>" data-role="tagsinput"><br />
			<span class="description"><?php _e('Search Keyword description'); ?></span>
		</td>
	</tr>

	<?php
}

// A callback function to save our extra taxonomy field(s)
function save_taxonomy_custom_fields( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_term_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
            foreach ( $cat_keys as $key ){
            if ( isset( $_POST['term_meta'][$key] ) ){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( "taxonomy_term_$t_id", $term_meta );
    }
}

// Add the fields to the "specialities" taxonomy, using our callback function
add_action( 'specialities_edit_form_fields', 'specialities_taxonomy_custom_fields', 99, 2 );

// Save the changes made on the "specialities" taxonomy, using our callback function
add_action( 'edited_specialities', 'save_taxonomy_custom_fields', 10, 2 );
 
function kt_search_array($array, $field, $value) {
   foreach($array as $key => $arr)
   {
      if ( $arr[$field] === $value )
         return $key;
   }
   return null;
}

function kt_process_email_invite_review($email, $subject, $desc) {

	global $current_user;

		$email_helper	= new DocDirectProcessEmail();
		

		$current_user_name = $current_user->first_name.' '.$current_user->last_name;
		$current_user_email = $current_user->user_email;
		$link_to = get_author_posts_url($current_user->ID).'#leavereview';
		
		$subject = $subject;
		$email_content_default = 'Hey %email%!<br/><br/>

								Please add review code to field for make review to doctor (%current_user_name%) follow this %link%<br/>

								Review Code: %description%<br/><br/>
								
								Sincerely,<br/>
								MediFinder Team<br/>
								%logo%';
			
		
		//set defalt contents
		if( empty( $email_content ) ){
			$email_content = $email_content_default;
		}
		
		$logo		   = kt_process_get_logo();
		$link		   = '<a href="'.$link_to.'" alt="'.pll__('User link').'">'.$link_to.'</a>';

		$email_content = str_replace("%email%", nl2br($email), $email_content); //Replace Name
		$email_content = str_replace("%current_user_email%", nl2br($current_user_email), $email_content); //Replace current_user_email
		$email_content = str_replace("%current_user_name%", nl2br($current_user_name), $email_content);
		$email_content = str_replace("%description%", nl2br($desc), $email_content); //Replace description
		$email_content = str_replace("%logo%", nl2br($logo), $email_content); //Replace Logo
		$email_content = str_replace("%link%", nl2br($link), $email_content); //Replace Logo
		
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
		
		$email_headers = "From: no-reply <info@no-reply.com>\r\n";
		$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
		$email_headers .= "MIME-Version: 1.0\r\n";
		$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		$attachments	  = '';
		$body			 = '';
		$body			.= $email_helper->prepare_email_headers($email);
		
		
		$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$email_content.'</p>';
		$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
            
            </td>
          </tr></table>';

		$body 			.= $email_helper->prepare_email_footers();
		wp_mail($email, $subject, $email_content);
		
		return true;

}

function kt_get_specialities_keyword() {
	$data_s_array2 = array();
   	$specialities_list   = docdirect_prepare_taxonomies('directory_type','specialities',0,'array');
    if( isset( $specialities_list ) && !empty( $specialities_list ) ){
        foreach( $specialities_list as $key => $speciality ){
            $sp_custom_fields = get_option( "taxonomy_term_$speciality->term_id" );
            $s_keyword = $sp_custom_fields[s_keyword];
            if (!empty($s_keyword)) {
            	$arr_keyword = explode(',',$s_keyword);
				// $data_s_array2	= $data_s_array2+$arr_keyword;
				$data_s_array2	= array_merge_recursive($data_s_array2, $arr_keyword);
            }
        }
    }
    return array_unique($data_s_array2);
}

function kt_ajax_direct_search() {
	global $current_user;
	$user_identity	= $current_user->ID;

	$member_group_terms = get_terms( array(
						    'taxonomy' => 'group_label',
						    // 'orderby' => 'menu_order',
						    'i_order_terms' => true,
						    'parent'   => 0,
						    'hide_empty' => false,
						) );

  	$search_text = isset( $_POST['s'] ) ? esc_sql( $_POST['s'] ) : ''; 
  	$exclude = isset( $_POST['exclude'] ) ? esc_sql( $_POST['exclude'] ) : '';
	ob_start();
  	$args = array(
	    'taxonomy'      => array( 'specialities' ), // taxonomy name
	    'exclude' 		=> $exclude,
	    'hide_empty'    => false,
	    'name__like'    => $search_text
	); 

	$terms = get_terms( $args );

	$all_keyword = kt_get_specialities_keyword();
	if (!empty($all_keyword)) {
		echo '<ul>';
		foreach ($all_keyword as $key => $value) {
	        if (stripos($value, $search_text) !== false) {
				$id = strtolower(str_replace(' ', '_', $value));
	        	?>
		        <li class="select_s_keyword" id="s_keyword-<?php echo esc_attr( $id );?>" data-id="<?php echo esc_attr( $id );?>" data-value="<?php echo esc_attr( $value );?>">
		        	<?php echo esc_attr( $value );?>
		        </li>
	            <?php
	        }
		}
		echo '</ul>';
	}

	$data_s_array1 = array();

	$count = count($terms);
	if($count > 0){
	    foreach ($terms as $term) {
            $data_s_array1[] = $term->term_id;
	    }
	}

	$data_s_array2 = array();
   	$specialities_list   = docdirect_prepare_taxonomies('directory_type','specialities',0,'array');
    if( isset( $specialities_list ) && !empty( $specialities_list ) ){
        foreach( $specialities_list as $key => $speciality ){
            $sp_custom_fields = get_option( "taxonomy_term_$speciality->term_id" );
            $s_keyword = $sp_custom_fields[s_keyword];
            if (stripos($s_keyword, $search_text) !== false) {
                $data_s_array2[] = $speciality->term_id;
            }
        }
    }
	
	$data_s_array = array_unique(array_merge($data_s_array1, $data_s_array2));
	// $data_s_array = $data_s_array1;
	if (function_exists('kt_read_specialities')) {
		$list_sp = kt_read_specialities();
	}
	$img_default = get_stylesheet_directory_uri().'/images/plus.svg';
	foreach ( $member_group_terms as $p_term ) {
		echo '<h5>'.$p_term->name.'</h5><div class="wrap_group open">';
		$child_member_group_terms = get_terms( array(
				    'taxonomy' => 'group_label',
				    // 'orderby' => 'menu_order',
				    'i_order_terms' => true,
				    'child_of'   => $p_term->term_id,
				    'hide_empty' => false,
				) );
		foreach ( $child_member_group_terms as $member_group_term ) {
		    $member_group_query = new WP_Query( array(
		        'post_type' => 'directory_type',
		        'posts_per_page' => -1,
		        'tax_query' => array(
		            array(
		                'taxonomy' => 'group_label',
		                'field' => 'slug',
		                'terms' => array( $member_group_term->slug ),
		                'operator' => 'IN'
		            )
		        )
		    ) );
		    ?>
		    <?php
		    $i=0;
		    $d_parent = '';
		    if ( $member_group_query->have_posts() ) : while ( $member_group_query->have_posts() ) : $member_group_query->the_post(); 
		    	$trans_id = pll_get_post(get_the_ID(), 'en');

				$category_image = fw_get_db_post_option($trans_id, 'category_image', true);

				if( !empty( $category_image['attachment_id'] ) ){
					$banner_url	= docdirect_get_image_source($category_image['attachment_id'],150,150);
			 		$banner	= '<img src="'.$banner_url.'">';
		  		} else{
					$dir_icon = fw_get_db_post_option($trans_id, 'dir_icon', true);
			 		$banner	= '<i class="fa '.$dir_icon.'"></i>';
			 	}
		    ?>
	        	<?php
	    			$attached_specialities = get_post_meta( get_the_ID(), 'attached_specialities', true );
					if (!empty($specialities_list)) {
						$j = 0;
						$d_child = '';
						foreach ($specialities_list as $key => $speciality) {
							if(in_array($speciality->term_id, $data_s_array)) {
	                            $trans_id =  pll_get_term($speciality->term_id, 'en');
	                            if( in_array( $speciality->term_id, $attached_specialities ) ) {
		                            $term = get_term( $trans_id, 'specialities' );
		                            $name = $term->name;
		                            $slug = $term->slug;
									$img = '<img width="150" height="150" src="'.$img_default.'">';
									if (!empty($list_sp[$speciality->term_id][1])) {
										$img = '<img width="150" height="150" src="'.$list_sp[$speciality->term_id][1].'">';
									}
	                            	$j++;
	                            	ob_start();
	                            ?>
						        <li class="select_speciality" id="speciality-<?php echo esc_attr( $speciality->term_id);?>" data-slug="<?php echo esc_attr( $slug);?>" data-id="speciality-<?php echo esc_attr( $speciality->term_id);?>">
		        					<?php echo '<span class="banner">'.$img.'</span>'; ?>
						        	<?php echo esc_attr( $speciality->name );?>
						        </li>
	                            <?php
									$d_child .= ob_get_clean();
	                        	}
	                		}
						}
					}
	        	?>
		        <?php
		        	if($j > 0) {
	                    ob_start();
		        	?>
		        	<li data-slug="<?php echo get_the_slug($trans_id);?>" data-id="<?php echo get_the_ID();?>">
				        <span data-toggle="collapse" href="#collapse-<?php echo get_the_ID();?>">
		        		<?php //$dir_icon = fw_get_db_post_option(get_the_ID(), 'dir_icon', true);?>
		        		<?php echo '<span class="banner">'.$banner.'</span>'; ?>
		        		<?php echo the_title(); 
							echo '<i class="fa fa-minus"></i>';?>
						</span>
		        		<?php
							echo '<ul id="collapse-'.get_the_ID().'" class="collapse in">';
							echo $d_child;
							echo '</ul>';
						?>
		        	</li>
		        	<?php $i++; 
						$d_parent .= ob_get_clean();
					}
		        ?>
		    <?php endwhile; endif; ?>
		    <?php
		        if($i > 0) {?>
				    <h6><?php echo $member_group_term->name; ?></h6>
				    <ul>
				    	<?php echo $d_parent;?>
		   			 </ul>
		    	<?php
				}
		    // Reset things, for good measure
		    $member_group_query = null;
		    wp_reset_postdata();
		}
		echo '</div>';
	}	

	
	$json['data1']	 = $data_s_array;
	$json['data']	 = ob_get_clean();
	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;
}
add_action('wp_ajax_direct_search', 'kt_ajax_direct_search');
add_action('wp_ajax_nopriv_direct_search', 'kt_ajax_direct_search');

function kt_ajax_direct_search_2() {

	global $current_user;
	$user_identity	= $current_user->ID;

	$member_group_terms = get_terms( array(
						    'taxonomy' => 'group_label',
						    // 'orderby' => 'menu_order',
						    'i_order_terms' => true,
						    'parent'   => 0,
						    'hide_empty' => false,
						) );

  	$search_text = isset( $_POST['s'] ) ? esc_sql( $_POST['s'] ) : ''; 
  	$exclude = isset( $_POST['exclude'] ) ? esc_sql( $_POST['exclude'] ) : '';
	ob_start();
  	$args = array(
	    'taxonomy'      => array( 'specialities' ), // taxonomy name
	    'exclude' 		=> $exclude,
	    'hide_empty'    => false,
	    'name__like'    => $search_text
	); 

	$terms = get_terms( $args );

	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
	    echo '<div class="wrap_group"><ul>';
	    foreach ( $terms as $term ) {
            $trans_id =  pll_get_term($term->term_id, 'en');
                $term = get_term( $trans_id, 'specialities' );
                $name = $term->name;
                $slug = $term->slug;

		     	$img = '';
            ?>
	        <li class="select_speciality" id="speciality-<?php echo esc_attr( $term->term_id);?>" data-slug="<?php echo esc_attr( $slug);?>" data-id="speciality-<?php echo esc_attr( $term->term_id);?>">
				<?php echo '<span class="banner">'.$img.'</span>'; ?>
	        	<?php echo esc_attr( $term->name );?>
	        </li>
	    <?php
	    }
	    echo '</ul></div>';
	}

	$json['data']	 = ob_get_clean();
	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;

}

function kt_ajax_direct_search_plus() {

	$member_group_terms = get_terms( array(
						    'taxonomy' => 'group_label',
						    // 'orderby' => 'menu_order',
						    'i_order_terms' => true,
						    'parent'   => 0,
						    'hide_empty' => false,
						) );
	
	$specialities_list   = kt_docdirect_prepare_taxonomies('directory_type','specialities',0,'array');
	foreach ( $member_group_terms as $p_term ) {
				?>
				    <h5><?php echo $p_term->name; ?></h5>
				    <div class="wrap_group">
				<?php
				$child_member_group_terms = get_terms( array(
						    'taxonomy' => 'group_label',
						    // 'orderby' => 'menu_order',
						    'i_order_terms' => true,
						    'child_of'   => $p_term->term_id,
						    'hide_empty' => false,
						) );
				foreach ( $child_member_group_terms as $member_group_term ) {
				    $member_group_query = new WP_Query( array(
				        'post_type' => 'directory_type',
				        'posts_per_page' => -1,
				        'tax_query' => array(
				            array(
				                'taxonomy' => 'group_label',
				                'field' => 'slug',
				                'terms' => array( $member_group_term->slug ),
				                'operator' => 'IN'
				            )
				        )
				    ) );
				    ?>
				    <h6><?php echo $member_group_term->name; ?></h6>
				    <ul>
				    <?php
				    if ( $member_group_query->have_posts() ) : while ( $member_group_query->have_posts() ) : $member_group_query->the_post(); 
				    	$trans_id = pll_get_post(get_the_ID(), 'en');

						$category_image = fw_get_db_post_option($trans_id, 'category_image', true);

						if( !empty( $category_image['attachment_id'] ) ){
							$banner_url	= docdirect_get_image_source($category_image['attachment_id'],150,150);
					 		$banner	= '<img width="150" height="150" src="'.$banner_url.'">';
				  		} else{
				  			$dir_icon = fw_get_db_post_option($trans_id, 'dir_icon', true);
					 		$banner	= '<i class="fa '.$dir_icon.'"></i>';
					 	}
				    ?>
				        <li data-slug="<?php echo get_the_slug($trans_id);?>" data-id="<?php echo get_the_ID();?>">
				        	<span data-toggle="collapse" href="#collapse-<?php echo get_the_ID();?>">
				        	<?php //$dir_icon = fw_get_db_post_option(get_the_ID(), 'dir_icon', true);?>
	        				<?php echo '<span class="banner">'.$banner.'</span>'; ?>
				        	<?php echo the_title(); ?>
				        	<?php
	            				$attached_specialities = get_post_meta( $trans_id, 'attached_specialities', true );
	   							if (!empty($specialities_list)) {
	   								echo '<i class="fa fa-plus"></i>';
	   								echo '</span>';
	   								echo '<ul id="collapse-'.get_the_ID().'" class="collapse">';
	   								foreach ($specialities_list as $key => $speciality) {
	                                    // $trans_id1 =  pll_get_term($speciality->term_id, 'en');
	                                    if( in_array( $speciality->term_id, $attached_specialities ) ) {
	                                    	$term_goc =  pll_get_term($speciality->term_id);
		                                    $term = get_term( $term_goc, 'specialities' );
		                                    $name = $term->name;
		                                    $slug = $term->slug;

									     	$img = '';
	                                    ?>
								        <li class="select_speciality" id="speciality-<?php echo esc_attr( $speciality->term_id);?>" data-slug="<?php echo esc_attr( $speciality->slug);?>" data-id="speciality-<?php echo esc_attr( $speciality->term_id);?>">
	        								<?php echo '<span class="banner">'.$img.'</span>'; ?>
								        	<?php echo esc_attr( $name );?>
								        </li>
	                                    <?php
	                                	}
	   								}
	   								echo '</ul>';
	   							}else {
	   								echo '</span>';
	   							}
				        	?>
				        </li>
				    <?php endwhile; endif; ?>
				    </ul>
				    <?php
				    // Reset things, for good measure
				    $member_group_query = null;
				    wp_reset_postdata();
				}
				echo '</div>';
			}

	$json['data']	 = ob_get_clean();
	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	// echo json_encode($json);
	echo $json['data'];
	die;

}
add_action('wp_ajax_direct_search_plus', 'kt_ajax_direct_search_plus');
add_action('wp_ajax_nopriv_direct_search_plus', 'kt_ajax_direct_search_plus');


function kt_add_practice() {

	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;

	$current_practices = get_user_meta($user_identity, 'user_practices', true);
	$active_location = false;
	if (empty($current_practices)) {
		$active_location = true;
	}

	if ($_POST['pratice_title'] == '') {
		$json['type']		   = 'error';
		$json['message']		= pll__('Name is require.','docdirect');
		echo json_encode($json);
		die;
	}

	if ($_POST['basics']['address'] == '') {
		$json['type']		   = 'error';
		$json['message']		= pll__('Address is require.','docdirect');
		echo json_encode($json);
		die;
	}

	$pratice_title	  = str_replace(' ', '_', strtolower(sanitize_text_field( $_POST['pratice_title'] )));
	$array_keys = array_keys($current_practices);
	if (in_array($pratice_title, $array_keys)) {
		$json['type']		   = 'error';
		$json['message']		= pll__('Address existing.','docdirect');
		echo json_encode($json);
		die;
	}

	$basics	  = $_POST['basics'];
	$schedules	  = $_POST['schedules'];
	$socials	  = $_POST['socials'];

	$current_practices[$pratice_title] = array(
									'title' => sanitize_text_field( $_POST['pratice_title'] ),
									'active_location' => $active_location,
									'basics' => $basics, 
									'schedules' => $schedules, 
									'socials' => $socials
								);

	if ($active_location == true) {
		update_user_meta($user_identity, 'latitude', $basics['latitude']);
		update_user_meta($user_identity, 'longitude', $basics['longitude']);
	}
	update_user_meta($user_identity, 'user_practices', $current_practices);

		$json['current_practices']	= $current_practices;
		$json['type']	= 'success';
		$json['message']	= pll__('Success.');
		echo json_encode($json);
		die;

}

add_action('wp_ajax_add_practice','kt_add_practice');


function kt_edit_practice() {

	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;

	$key = $_POST['key'];

	$current_practices = get_user_meta($user_identity, 'user_practices', true);
	/*if (empty($current_practices)) {
		$current_practices = array();
	}*/

	if ($_POST['pratice_title'] == '') {		
		$json['type']		   = 'error';
		$json['message']		= pll__('Name is require.','docdirect');
		echo json_encode($json);
		die;
	}

	if ($_POST['basics']['address'] == '') {
		$json['type']		   = 'error';
		$json['message']		= pll__('Address is require.','docdirect');
		echo json_encode($json);
		die;
	}

	$pratice_title	  = str_replace(' ', '_', strtolower(sanitize_text_field( $_POST['pratice_title'] )));
	$array_keys = array_keys($current_practices);
	if (in_array($key, $array_keys)) {

		$basics	  = $_POST['basics'];
		$schedules	  = $_POST['schedules'];
		$socials	  = $_POST['socials'];
		$current_active = $current_practices[$key]['active_location'];

		$current_practices[$key] = array(
										'title' => sanitize_text_field( $_POST['pratice_title'] ),
										'active_location' => $current_active,
										'basics' => $basics, 
										'schedules' => $schedules, 
										'socials' => $socials
									);
		update_user_meta($user_identity, 'user_practices', $current_practices);

		$json['current_practices']	= $current_practices;
		$json['type']	= 'success';
		$json['message']	= pll__('Success.');
		echo json_encode($json);
		die;
	}

}
add_action('wp_ajax_edit_practice','kt_edit_practice');

add_action('wp_ajax_delete_practice','kt_delete_practice');

function kt_delete_practice() {

	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;
	$current_practices = get_user_meta($user_identity, 'user_practices', true);	
	$key	  = $_POST['key'];
	$array_keys = array_keys($current_practices);
	if (in_array($key, $array_keys)) {

		unset($current_practices[$key]);
		update_user_meta($user_identity, 'user_practices', $current_practices);

		$json['current_practices']	= $current_practices;
		$json['type']	= 'success';
		$json['message']	= pll__('Success.');
		echo json_encode($json);
		die;
	}else {
		$json['type']	= 'error';
		$json['message']	= pll__('Error.');
		echo json_encode($json);
		die;
	}


}

function kt_load_edit_practice() {

	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;
	$current_practices = get_user_meta($user_identity, 'user_practices', true);	
	$key	  = $_POST['key'];
	$value = $current_practices[$key];

	kt_practice_form($key, $value);

	$json['data1']	 = $value;
	$json['data']	 = ob_get_clean();
	$json['type']	= 'success';
	$json['message']	= pll__('Success.');
	echo json_encode($json);
	die;

}

add_action('wp_ajax_load_edit_practice','kt_load_edit_practice');

function kt_change_active_practice() {

	global $current_user, $wp_roles,$userdata,$post;
	$user_identity	= $current_user->ID;
	$current_practices = get_user_meta($user_identity, 'user_practices', true);	
	$key_change	  = $_POST['key'];
	
	$array_keys = array_keys($current_practices);
	if (in_array($key_change, $array_keys)) {
		foreach ($current_practices as $key => $value) {
			if ( $key == $key_change ) {
				$current_practices[$key]['active_location'] = true;
				$basics = $current_practices[$key]['basics'];
				$json['data1']	 = $basics;
				$vkl = array($key => $current_practices[$key]);
				unset($current_practices[$key]);
			}else {
				$current_practices[$key]['active_location'] = false;
			}
		}
		// array_unshift($current_practices, $new);
		$current_practices = $vkl + $current_practices;
		update_user_meta($user_identity, 'user_practices', $current_practices);

		update_user_meta($user_identity, 'latitude', $basics['latitude']);
		update_user_meta($user_identity, 'longitude', $basics['longitude']);

		$json['data']	 = $current_practices;
		$json['type']	= 'success';
		$json['message']	= pll__('Success.');
		echo json_encode($json);
		die;
	}else {
		$json['type']	= 'error';
		$json['message']	= pll__('Error.');
		echo json_encode($json);
		die;
	}

}

add_action('wp_ajax_change_active_practice','kt_change_active_practice');

function kt_practice_form($key ="", $value = "") {

	if (function_exists('fw_get_db_settings_option')) {
		$dir_longitude = fw_get_db_settings_option('dir_longitude');
		$dir_latitude = fw_get_db_settings_option('dir_latitude');
		$dir_datasize = fw_get_db_settings_option('dir_datasize');
		$dir_longitude	= !empty( $dir_longitude ) ? $dir_longitude : '105.834160';
		$dir_latitude	= !empty( $dir_latitude ) ? $dir_latitude : '21.027764';
	} else{
		$dir_longitude = '105.834160';
		$dir_latitude = '21.027764';
	}

	$db_longitude	= !empty( $db_longitude ) ? $db_longitude : $dir_longitude;
	$db_latitude	= !empty( $db_latitude ) ? $db_latitude : $dir_latitude;

	docdirect_init_dir_map();//init Map
	docdirect_enque_map_library();//init Map

	if ($value != '') {
		$basics = $value['basics'];
		$socials = $value['socials'];
		$db_longitude = $basics['longitude'];
		$db_latitude = $basics['latitude'];
		$db_location = $basics['location'];
		?>
        <h4><?php pll_e('Practice Information');?></h4>
		<?php
	}
	?>
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
                    <input class="form-control" name="basics[address]" value="<?php echo $basics['address'];?>" type="text" placeholder="<?php pll_e('Address');?>">
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    <input class="form-control" name="basics[room_floor]" value="<?php echo $basics['room_floor'];?>" type="text" placeholder="<?php pll_e('Room/Floor/Building');?>">
                </div>
            </div>
            <div class="col-md-4 col-sm-12 col-xs-12">
                <div class="form-group">
                    <input class="form-control" name="basics[user_url]" value="<?php echo $basics['user_url'];?>" type="url" placeholder="<?php pll_e('Your Website');?>">
                </div>
            </div>
            <div class="col-md-4 col-sm-6 col-xs-12">
                <div class="form-group">
                    <input class="form-control" name="basics[business_email]" value="<?php echo $basics['business_email'];?>" type="url" placeholder="<?php pll_e('Add Business Email..');?>">
                </div>
            </div>
        </div>

    <div class="tg-heading-border tg-small">
        <h3><?php pll_e('Social Settings');?></h3>
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

    <div class="tg-heading-border tg-small">
        <h3><?php pll_e('Locations');?></h3>
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
        <div class="col-xs-12">
            <div class="form-group">
                <input class="tg-btn pull-right " type="submit" name="submit" value="Update" />
            </div>
        </div>
        <input type="hidden" name="action" value="edit_practice">
        <?php
		if ($key != '') {
			?>		
            <input type="hidden" name="key" value="<?php echo $key;?>">
			<?php
		}?>
		<?php

}

function get_doctor_locations($user_id){

	$current_author_profile = get_userdata( $user_id );
	$facebook	  = isset( $current_author_profile->facebook ) ? $current_author_profile->facebook : '';
	$twitter	   = isset( $current_author_profile->twitter ) ? $current_author_profile->twitter : '';
	$linkedin	  = isset( $current_author_profile->linkedin ) ? $current_author_profile->linkedin : '';
	$pinterest	 = isset( $current_author_profile->pinterest ) ? $current_author_profile->pinterest : '';
	$google_plus   = isset( $current_author_profile->google_plus ) ? $current_author_profile->google_plus : '';
	$instagram	 = isset( $current_author_profile->instagram ) ? $current_author_profile->instagram : '';
	$tumblr	    = isset( $current_author_profile->tumblr ) ? $current_author_profile->tumblr : '';
	$skype	  	 = isset( $current_author_profile->skype ) ? $current_author_profile->skype : '';
	?>
    <div class="panel-group list_locations" id="accordion" role="tablist" aria-multiselectable="true">
        <?php 
    	$current_practices = get_user_meta($current_author_profile->ID , 'user_practices', true);
		$privacy		= docdirect_get_privacy_settings($current_author_profile->ID); //Privacy settings

    	$i=0;
    	if (!empty($current_practices)) {
    		foreach ($current_practices as $key => $value) {
	    		$i++;
	            if( $i == 1 ) {
	                $activeTab  = 'active';
	                $collapse   = 'in';
	                $icon = '<i class="fa fa-star"></i>';
	            }else {
	                $activeTab  = '';
	                $collapse   = '';
	                $icon = '';
	            }
    			$basics = $current_practices[$key]['basics'];
                $socials = $current_practices[$key]['socials'];
                $schedules = $current_practices[$key]['schedules'];

              	$room_floor = $basics['room_floor'];
              	$address = $basics['address'];
              	$phone_number = $basics['phone_number'];
              	$business_email = $basics['business_email'];
              	$user_url = $basics['user_url'];
              	$fax = $basics['fax'];
              	$mtr_exit = $basics['mtr_exit'];

              	$facebook = $socials['facebook'];
              	$twitter = $socials['twitter'];
              	$linkedin = $socials['linkedin'];
              	$pinterest = $socials['pinterest'];
              	$google_plus = $socials['google_plus'];
              	$instagram = $socials['instagram'];
              	$tumblr = $socials['tumblr'];
              	$skype = $socials['skype'];
    		?>
        <div class="panel panel-default <?php echo $activeTab;?>">
    		<div class="tg-panel-heading" role="tab" id="heading1">
                <h3 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $key;?>" aria-expanded="true" aria-controls="collapse<?php echo $key;?>">
                        <?php echo $value['title'];?>
                    </a>
                    <?php echo $icon;?>
                </h3>
            </div>
            <div id="collapse<?php echo $key;?>" class="panel-collapse collapse <?php echo esc_attr( $collapse );?>" role="tabpanel" aria-labelledby="heading<?php echo $key;?>">
                <div class="panel-body">
                    <ul class="tg-doccontactinfo">
	                <?php if( !empty( $room_floor ) ) {?>
	                    <li> <i class="fa fa-home"></i> <address><?php echo esc_attr( $room_floor );?></address> </li>
	                <?php }?>
	                <?php if( !empty( $address ) ) {?>
	                	<li> <i class="fa fa-map-marker"></i> <address><?php echo esc_attr( $address );?></address> </li>
	                <?php }?>
	                <?php if( !empty( $business_email ) 
							  && 
							  $privacy['email'] == 'on'
					) {?>
	                    <li><i class="fa fa-envelope-o"></i><a href="mailto:<?php echo esc_attr( $business_email );?>?subject:<?php pll_e('Hello');?>"><?php echo esc_attr( $business_email );?></a></li>
	                <?php }?>
	                <?php if( !empty( $phone_number ) 
							  && 
							  $privacy['phone'] == 'on'
					) {?>
	                	<li> <i class="fa fa-phone"></i> <span><?php echo esc_attr( $phone_number );?></span> </li>
	                <?php }?>
	                <?php if( !empty( $fax ) ) {?>
	                	<li><i class="fa fa-fax"></i> <span><?php echo esc_attr( $fax );?></span> </li>
	                <?php }?>
	                <?php if( !empty( $mtr_exit ) ) {?>
	                	<li><img src="<?php echo get_stylesheet_directory_uri();?>/images/mtr_hong_kong_logo.svg" width="16" height="16"><span><?php echo esc_attr( $mtr_exit );?></span> </li>
	                <?php }?>
	                <?php if( !empty( $skype ) ) {?> 
	                	<li><i class="fa fa-skype"></i><span><?php echo esc_attr( $skype );?></span></li>
	                <?php }?>
	                <?php if( !empty( $user_url ) ) {?>
	                    <li><i class="fa fa-link"></i><a href="<?php echo esc_attr( $user_url);?>" target="_blank"><?php echo esc_attr( $user_url);?></a></li>
	                <?php }?>
	              </ul>
	              <?php 
					if(  !empty( $facebook ) 
						 || !empty( $facebook ) 
						 || !empty( $twitter ) 
						 || !empty( $linkedin ) 
						 || !empty( $pinterest ) 
						 || !empty( $google_plus ) 
						 || !empty( $instagram ) 
						 || !empty( $tumblr ) 
						 || !empty( $skype ) 
					){?>
					<ul class="tg-socialicon-v2">
						<?php if(  !empty( $facebook ) ) {?>
							<li class="tg-facebook"><a href="<?php echo esc_url($facebook);?>"><i class="fa fa-facebook-f"></i></a></li>
						<?php }?>
						<?php if(  !empty( $twitter ) ) {?>
						<li class="tg-twitter"><a href="<?php echo esc_url($twitter);?>"><i class="fa fa-twitter"></i></a></li>
						<?php }?>
						<?php if(  !empty( $linkedin ) ) {?>
						<li class="tg-linkedin"><a href="<?php echo esc_url($linkedin);?>"><i class="fa fa-linkedin"></i></a></li>
						<?php }?>
						<?php if(  !empty( $pinterest ) ) {?>
						<li class="tg-pinterest"><a href="<?php echo esc_url($pinterest);?>"><i class="fa fa-pinterest-p"></i></a></li>
						<?php }?>
						<?php if(  !empty( $google_plus ) ) {?>
						<li class="tg-googleplus"><a href="<?php echo esc_url($google_plus);?>"><i class="fa fa-google-plus"></i></a></li>
						<?php }?>
						<?php if(  !empty( $instagram ) ) {?>
						<li class="tg-instagram"><a href="<?php echo esc_url($instagram);?>"><i class="fa fa-instagram"></i></a></li>
						<?php }?>
						<?php if(  !empty( $tumblr ) ) {?>
						<li class="tg-tumblr"><a href="<?php echo esc_url($tumblr);?>"><i class="fa fa-tumblr"></i></a></li>
						<?php }?>
						<?php if(  !empty( $skype ) ) {?>
						<li class="tg-skype"><a href="<?php echo esc_url($skype);?>"><i class="fa fa-skype"></i></a></li>
						<?php }?>
					</ul>
					<?php }?>
	                <a class="tg-btn tg-btn-lg" href="http://maps.google.com/maps?saddr=&amp;daddr=<?php echo esc_attr( $address );?>" target="_blank"><?php pll_e('open map');?></a>	                
		            <div class="tg-userschedule">
		                <h3><?php pll_e('Schedule');?></h3>
	                    <?php 
	                        $week_array	= docdirect_get_week_array();
	                        $db_schedules	= $schedules;
	                        //Time format
	                        if( isset( $schedule_time_format ) && $schedule_time_format === '24hour' ){
	                            $time_format	= 'G:i A';
	                        } else{
	                            $time_format	= 'g:i A';
	                        }
	                        $date_prefix    = date('D');
	                        ?>
                        <ul>
                            <?php
                            if( isset( $week_array ) && !empty( $week_array ) ) {
                            foreach( $week_array as $key => $value ){
                                $start_time_formate  = '';
                                $end_time_formate      = '';
                                $start_time  = $db_schedules[$key.'_start'];
                                $end_time   = $db_schedules[$key.'_end'];
        
                                if( !empty( $start_time ) ){
                                    $start_time_formate = date( $time_format, strtotime( $start_time ) );
                                }
                                
                                
                                if( isset( $end_time ) && !empty( $end_time ) ){
                                    $end_time_formate   = date( $time_format, strtotime( $end_time ) );
                                }
                                
                                //Active day
                                $active = '';
                                if( strtolower( $date_prefix ) == $key ){
                                    $active = 'current';
                                }
                                
                                if( !empty( $start_time_formate ) && $end_time_formate ) {
                                    $data_key   = $start_time_formate.' - '.$end_time_formate;
                                } else if( !empty( $start_time_formate ) ){
                                    $data_key   = $start_time_formate;
                                } else if( !empty( $end_time_formate ) ){
                                    $data_key   = $end_time_formate;
                                } else{
                                    $data_key   = pll__('Closed');
                                }
                            ?>
                            <li class="<?php echo sanitize_html_class( $active );?>"><a href="javascript:;" data-type="<?php echo esc_attr( $data_key );?>"><span><?php echo esc_attr( $value );?></span><em><?php echo esc_attr( $data_key );?></em></a></li>
                            
                        <?php }}?>
                            
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    			<?php
    		}
    	}
        ?>
    </div>
    <?php
}

function kt_ajax_submit_invite(){
  //session_start();
  global $current_user;

  $current_user_name = $current_user->first_name.' '.$current_user->last_name;

  $email = isset( $_POST['email'] ) ? esc_sql( $_POST['email'] ) : '';
  $desc = isset( $_POST['desc'] ) ? esc_sql( $_POST['desc'] ) : '';
  $message = '';
  if( $email != '' && $desc != '' ){

  	if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		if( email_exists( $email )) {
      		$message = '<div class="alert alert-danger">'.__( 'Email exists in my system', 'tntheme' ).'</div>';
	    }else {
	    	$subject = $current_user_name.' has invited you to join their network';
		  	kt_process_email_invite($email, $subject, $desc);
		    $message = '<div class="alert alert-success">'.__( 'Invite Success', 'tntheme' ).'</div>';
		    $success = true;
	    }
	} else {
      $message = '<div class="alert alert-danger">'.__( 'Email is not valid', 'tntheme' ).'</div>';
	}

  }else {

    $message = '<div class="alert alert-danger">'.__( 'Please fill all field', 'couponxl' ).'</div>';

  }

  if($success == true){    
    echo json_encode(array(
        'message' => $message,
        'success' => $success,
      ));
  }else{
    echo json_encode(array(
      'message' => $message,
    ));
  }
  die();
}
add_action('wp_ajax_submit_invite', 'kt_ajax_submit_invite');
add_action('wp_ajax_nopriv_submit_invite', 'kt_ajax_submit_invite');

function kt_process_email_request($user_to) {
	global $current_user;

		$email_helper	= new DocDirectProcessEmail();
		
		$userto = get_userdata($user_to);
		$userto_name = $userto->first_name.' '.$userto->last_name;
		$email = $userto->user_email;


		$current_user_name = $current_user->first_name.' '.$current_user->last_name;
		
		$subject = 'A colleague has invited you to join MediFinder!';
		$email_content_default = 'Hey %name%!<br/>

								You have been invited by %current_user_name%<br/>
								
								Sincerely,<br/>
								MediFinder Team<br/>
								%logo%';
			
		
		//set defalt contents
		if( empty( $email_content ) ){
			$email_content = $email_content_default;
		}
		
		$logo		   = kt_process_get_logo();

		$email_content = str_replace("%name%", nl2br($userto_name), $email_content); //Replace Name
		$email_content = str_replace("%current_user_name%", nl2br($current_user_name), $email_content); //Replace current_user_name
		$email_content = str_replace("%logo%", nl2br($logo), $email_content); //Replace Logo
		
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
		
		$email_headers = "From: no-reply <info@no-reply.com>\r\n";
		$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
		$email_headers .= "MIME-Version: 1.0\r\n";
		$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		$attachments	  = '';
		$body			 = '';
		$body			.= $email_helper->prepare_email_headers($name);
		
		
		$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$email_content.'</p>';
		$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
            
            </td>
          </tr></table>';

		$body 			.= $email_helper->prepare_email_footers();
		wp_mail($email, $subject, $email_content);
		
		return true;
}

function kt_process_email_invite($email, $subject, $desc) {

	global $current_user;

		$email_helper	= new DocDirectProcessEmail();
		
		$userto = get_userdata($user_to);
		$userto_name = $userto->first_name.' '.$userto->last_name;


		$current_user_name = $current_user->display_name;
		$current_user_email = $current_user->user_email;

		$link = get_home_url();
		
		$subject_default = $subject;
		$email_content_default = 'Hey %email%!<br/>

								You have been invited by %current_user_email%<br/>

								%description%<br/>
								
								Sincerely,<br/>
								MediFinder Team<br/>
								%logo%';
		
		if (function_exists('fw_get_db_post_option')) {
			$subject = fw_get_db_settings_option('invitation_subject');
			$email_content = fw_get_db_settings_option('invitation_content');
		}	
		
		//set defalt contents
		if( empty( $subject ) ){
			$subject = $subject_default;
		}
		//set defalt contents
		if( empty( $email_content ) ){
			$email_content = $email_content_default;
		}
		
		$logo		   = kt_process_get_logo();

		$email_content = str_replace("%email%", nl2br($email), $email_content); //Replace Name
		$email_content = str_replace("%username%", nl2br($current_user_name), $email_content); //Replace Name
		$email_content = str_replace("%link%", nl2br($link), $email_content); //Replace Name
		$email_content = str_replace("%current_user_email%", nl2br($current_user_email), $email_content); //Replace current_user_email
		$email_content = str_replace("%message%", nl2br($desc), $email_content); //Replace description
		$email_content = str_replace("%logo%", nl2br($logo), $email_content); //Replace Logo
		
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
		
		$email_headers = "From: no-reply <info@no-reply.com>\r\n";
		$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
		$email_headers .= "MIME-Version: 1.0\r\n";
		$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		$attachments	  = '';
		$body			 = '';
		// $body			.= $email_helper->prepare_email_headers($email);
		
		
		$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$email_content.'</p>';
		$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
            
            </td>
          </tr></table>';

		// $body 			.= $email_helper->prepare_email_footers();
		wp_mail($email, $subject, $email_content);
		
		return true;

}

function kt_process_appointment_review_email( $params = '' ) {
		global $current_user;
		extract( $params );
			
		$email_helper	= new DocDirectProcessEmail();

			$date_format = get_option('date_format');
			$time_format = get_option('time_format');
		
			$user_from	= get_post_meta($post_id, 'bk_user_from', true);
			$user_to	  = get_post_meta($post_id, 'bk_user_to', true);
			
			$customer_name	= get_post_meta($post_id, 'bk_username', true);
			$email_to		 = get_post_meta($post_id, 'bk_useremail', true);
			$bk_booking_date	= get_post_meta($post_id, 'bk_booking_date', true);
			$bk_slottime		= get_post_meta($post_id, 'bk_slottime', true);
			$bk_service		 = get_post_meta($post_id, 'bk_service', true);
			$bk_category		= get_post_meta($post_id, 'bk_category', true);
			
			$user_from_data	= get_userdata($user_from);
			$user_to_data	  = get_userdata($user_to);
			$booking_services	= get_user_meta($user_to , 'booking_services' , true);
			$address			 = get_user_meta($user_to , 'address' , true);
			$service	= $booking_services[$bk_service]['title'];
			 
			$provider_name 		= docdirect_get_username($user_to);
			
			$time = explode('-',$bk_slottime);
			$appointment_date	= date_i18n($date_format,strtotime($bk_booking_date) );
			$appointment_time	= date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );

			$link_appointment	= get_permalink($post_id);
			
			$subject_default = 'Your Appointment Success';
			$booking_approved_default = 'Hey %customer_name%!<br/>

						Review here %link_appointment%
						
						Sincerely,<br/>
						%logo%';				
			
			
			//set defalt contents
			$subject = $subject_default;
			
			//set defalt title
			$booking_approved = $booking_approved_default;
			
			$provider	= '<a href="'.get_author_posts_url($user_to).'"  alt="'.pll__('provider','docdirect').'">'.$provider_name.'</a>';
			$logo		   = kt_process_get_logo();
			
			$booking_approved = str_replace("%link_appointment%", nl2br($link_appointment), $booking_approved); //Replace link_appointment

			$booking_approved = str_replace("%customer_name%", nl2br($customer_name), $booking_approved); //Replace Name
			$booking_approved = str_replace("%service%", nl2br($service), $booking_approved); //Replace service
			$booking_approved = str_replace("%provider%", nl2br($provider), $booking_approved); //Replace provider
			$booking_approved = str_replace("%address%", nl2br($address), $booking_approved); //Replace address
			$booking_approved = str_replace("%appointment_date%", nl2br($appointment_date), $booking_approved); //Replace date
			$booking_approved = str_replace("%appointment_time%", nl2br($appointment_time), $booking_approved); //Replace time
			$booking_approved = str_replace("%logo%", nl2br($logo), $booking_approved); //Replace logo

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$admin_email	= get_option( 'admin_email' ,'info@themographics.com' );
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			$body			.= $email_helper->prepare_email_headers($customer_name);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_approved.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			$body	.= $email_helper->prepare_email_footers();
			wp_mail($email_to, $subject, $body);
			
			return true;
}	

function kt_process_get_logo( $params = '' ) {
	//Get Logo
	if (function_exists('fw_get_db_settings_option')) {
		$main_logo = fw_get_db_settings_option('main_logo');
    }
	
	if (isset($main_logo['url']) && !empty($main_logo['url'])) {
		// $logo = $main_logo['url'];
		$logo = wp_get_attachment_url( $main_logo['attachment_id'] );;
	} else {
		$logo = get_template_directory_uri() . '/images/logo.png';
	}
	
	return '<img width="140" src="'.esc_url( $logo ).'" alt="'.pll__('email-header').'" />';
}
add_action('wp_ajax_ajax_filepdf','kt_ajax_filepdf');
add_action( 'wp_ajax_nopriv_ajax_filepdf', 'kt_ajax_filepdf' );

function kt_ajax_filepdf() {

	global $current_user, $wp_roles,$userdata,$post;
	// $val	= isset( $_POST['val'] ) && !empty( $_POST['val'] ) ? $_POST['val'] : '';

	$result = array();
	$files = array();
	foreach ($_FILES['file'] as $key => $value) {
		foreach ($value as $k => $v) {
			$files[$k][$key] = $v;
		}
	}
	// var_dump($_POST['val']);
	if ( isset( $_POST['val'] ) && !empty( $_POST['val'] ) ) {
		$val = explode(',', $_POST['val']);
	}else {
		$val = array();
	}
	// var_dump($val);
	$list_id = array();
	$output = '';
	foreach ($files as $file) {
		//start upload
		$file_id = kv_handle_attachment($file, $pid); 
		if ($file_id != false) {
			$list_id[] = $file_id;
			$val[] = $file_id;
			$output .= '<div class="col-sm-4">';
			$output .= '<a href="'.wp_get_attachment_url( $file_id ).'" title="'.get_the_title($file_id).'"><img src="'.get_stylesheet_directory_uri() . '/images/pdf_icon.png'.'" alt="'.get_the_title($file_id).'" /></a>';
            $output .= '<span class="remove_pdf" title="remove this file"><i class="fa fa-close"></i></span>';
            $output .= ' <span>'.str_replace('-', ' ', get_the_title($file_id)).'.pdf</span>';
			$output .= '</div>';
		}
	}
		$newval = implode(',', $val);
		$result['val'] = $newval;
	
	$result['list_id'] = implode(',', $list_id);
	$result['output'] = $output;
	$result['message'] = 'success';
	$json = json_encode($result);
	echo $json;
	die;
}

/*******Handle upload file**********/
function kv_handle_attachment($file_handler,$post_id,$set_thu=false) {
	// check to make sure its a successful upload
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$attach_id = media_handle_sideload( $file_handler, $post_id );

	return $attach_id;
}

add_action('wp_ajax_remove_pdf','kt_ajax_remove_pdf');
add_action( 'wp_ajax_nopriv_remove_pdf', 'kt_ajax_remove_pdf' );

function kt_ajax_remove_pdf() {

	global $current_user, $wp_roles,$userdata,$post;
	$result['type'] = 'error';
	$href	= isset( $_POST['href'] ) && !empty( $_POST['href'] ) ? $_POST['href'] : '';
	$val	= isset( $_POST['val'] ) && !empty( $_POST['val'] ) ? $_POST['val'] : '';

	$id = kt_get_image_id($href);
	if ($id != false) {
		$result['type'] = 'success';
		//delete media
		// wp_delete_attachment( $id );

		$val = explode(',', $val);
		$pos = array_search($id, $val);
		if ($pos !== false) {
			unset($val[$pos]);
			$newval = implode(',', $val);
		}
		$result['val'] = $newval;
	}

	$result['output'] = $id;
	$json = json_encode($result);
	echo $json;
	die;
}

// retrieves the attachment ID from the file URL
function kt_get_image_id($image_url) {
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
    return $attachment[0]; 
}

//change selec specialities backend
function kt_action_theme_include_custom_option_types() {
    if (is_admin()) {
        require_once get_stylesheet_directory() . '/framework-customizations/includes/option-types/new-icon/class-fw-option-type-new-icon.php';
		require_once get_stylesheet_directory() . '/framework-customizations/includes/option-types/custom-multi-select/class-fw-option-type-custom-multi-select.php';
        // and all other option types
    }
}
add_action('fw_option_types_init', 'kt_action_theme_include_custom_option_types');

function kt_ajax_kt_user() {
	global $current_user, $wp_roles,$userdata,$post;
	$list_id	= isset( $_POST['list_id'] ) && !empty( $_POST['list_id'] ) ? $_POST['list_id'] : '';
	$list_id = explode(',', $list_id);
	$output = '';
	if (!empty($list_id)) {
		foreach ($list_id as $uid) {
			$user = get_userdata($uid);
            $get_username	= kt_get_title_name($user->ID).docdirect_get_username( $user->ID );
            $avatar = apply_filters(
                    'docdirect_get_user_avatar_filter',
                     docdirect_get_user_avatar(array('width'=>150,'height'=>150), $uid),
                     array('width'=>150,'height'=>150) //size width,height
                );
			$output .= '<li>';
			$output .= '<figure class="doc-featureimg"><a class="userlink" href="'.get_author_posts_url($uid).'"><img src="'.esc_url( $avatar ).'" alt="'.$user->first_name.' '.$user->last_name.'"></a></figure>';
			$output .= '<div class="doc-featurecontent">';
			$output .= '<h4><a href="'.get_author_posts_url($uid).'" class="list-avatar">'. $get_username .'</a></h4>';
			$output .= '<div class="doc-featurehead"><span>'. esc_attr( $user->tagline ). '</span></div>';
			$output .= '</div>';
			$output .= '</li>';
		}
	}

	echo $output;
	die;
}

add_action('wp_ajax_kt_user','kt_ajax_kt_user');
add_action( 'wp_ajax_nopriv_kt_user', 'kt_ajax_kt_user' );

function kt_count_appointments($userfrom, $userto) {

	$meta_query_args[] = array(
								'key'     => 'bk_user_from',
								'value'   => $userfrom,
								'compare'   => '=',
								'type'	  => 'NUMERIC'
							);
	$meta_query_args[] = array(
								'key'     => 'bk_user_to',
								'value'   => $userto,
								'compare'   => '=',
								'type'	  => 'NUMERIC'
							);
	$meta_query_args[] = array(
								'key'     => 'bk_status',
								'value'   => 'approved',
								'compare'   => '='
							);
	$args 		= array('posts_per_page' => -1, 
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

	$count = 0;
	return $count_post;

}



/**
 * @Prepare Columns
 * @return {post}
 */
function kt_appointments_columns_add($columns) {
	// unset($columns['date']);
	unset($columns['profile']);
	unset($columns['apt_from']);
	unset($columns['apt_to']);
	unset($columns['apt_contact']);
	$columns['id_order'] 			= pll__('Order','docdirect_core');
	$columns['apt_from'] 			= pll__('Appointment From','docdirect_core');
	$columns['apt_to'] 				= pll__('Appointment To','docdirect_core');
	$columns['apt_contact'] 		= pll__('Contact Number','docdirect_core');
	$columns['apt_bk_status'] 		= pll__('Booking Status','docdirect_core');
	$columns['apt_bk_transaction_status'] 		= pll__('Payment Status','docdirect_core');
 
		return $columns;
}

add_action('manage_docappointments_posts_custom_column', 'kt_appointments_columns',10, 2);	
/**
 * @Get Columns
 * @return {}
 */
function kt_appointments_columns($name) {
	global $post;
	
	$bk_status 		= '';
	$bk_transaction_status = '';
			
	if (function_exists('fw_get_db_settings_option')) {
		$bk_status = get_post_meta($post->ID, 'bk_status', true);
		$bk_transaction_status = get_post_meta($post->ID, 'bk_transaction_status', true);
		$bk_code = get_post_meta($post->ID, 'bk_code', true);
	}
	
	switch ($name) {
		case 'id_order':
			echo esc_attr( $bk_code );
		break;
		case 'apt_bk_status':
			echo esc_attr( docdirect_prepare_order_status( 'value',$bk_status ) );
		break;
		case 'apt_bk_transaction_status':
			echo esc_attr( docdirect_prepare_order_status( 'value',$bk_transaction_status ) );
		break;
		
	}
}

add_action( 'add_meta_boxes', 'kt_tg_appointments_add_meta_box', 10,1);
/**
 * @Init Meta Boxes
 * @return {post}
 */
function kt_tg_appointments_add_meta_box($post_type){
	if ($post_type == 'docappointments') {
		add_meta_box(
			'tg_appointments_info',
			pll__( 'Appointment Info', 'docdirect_core' ),
			'kt_docdirect_meta_box_appointmentinfo',
			'docappointments',
			'side',
			'high'
		);
		
	}
	
	if ( $post_type == 'docappointments' ) {
		add_meta_box(
			'tg_appointments_detail',
			pll__( 'Appointment Detail', 'docdirect_core' ),
			'kt_docdirect_appointment_detail',
			'docappointments',
			'normal',
			'high'
		);
	}
}
/**
 * @Init Appointment detail
 * @return {post}
 */
function kt_docdirect_appointment_detail(){
	global $post;
	
	if ( function_exists('fw_get_db_settings_option') 
		 &&
		 !empty( $post->ID ) 
		 && isset( $_GET['post'] ) 
		 && !empty( $_GET['post'] )
	) {
		
		$bk_payment_date = get_post_meta($post->ID, 'bk_payment_date', true);
		$bk_transaction_status = get_post_meta($post->ID, 'bk_transaction_status', true);
		$bk_paid_amount = get_post_meta($post->ID, 'bk_paid_amount', true);
		$bk_user_from = get_post_meta($post->ID, 'bk_user_from', true);
		$payment_status = get_post_meta($post->ID, 'payment_status', true);
		$bk_status = get_post_meta($post->ID, 'bk_status', true);
		$bk_timestamp = get_post_meta($post->ID, 'bk_timestamp', true);
		$bk_user_to = get_post_meta($post->ID, 'bk_user_to', true);
		$bk_payment = get_post_meta($post->ID, 'bk_payment', true);
		$bk_booking_note = get_post_meta($post->ID, 'bk_booking_note', true);
		$bk_useremail = get_post_meta($post->ID, 'bk_useremail', true);
		$bk_userphone = get_post_meta($post->ID, 'bk_userphone', true);
		$bk_username = get_post_meta($post->ID, 'bk_username', true);
		$bk_subject = get_post_meta($post->ID, 'bk_subject', true);
		$bk_slottime = get_post_meta($post->ID, 'bk_slottime', true);
		$bk_booking_date = get_post_meta($post->ID, 'bk_booking_date', true);
		$bk_currency = get_post_meta($post->ID, 'bk_currency', true);
		$bk_service = get_post_meta($post->ID, 'bk_service', true);
		$bk_category = get_post_meta($post->ID, 'bk_category', true);
		$bk_code = get_post_meta($post->ID, 'bk_code', true);
		
		
		$services_cats = get_user_meta($bk_user_to , 'services_cats' , true);
		$booking_services = get_user_meta($bk_user_to , 'booking_services' , true);
		

		$purchase_on	 = date('d M, y',strtotime( $bk_payment_date ));
		$bk_user_from	= get_user_by( 'id', intval( $bk_user_from ) );
		$bk_user_to	  = get_user_by( 'id', intval( $bk_user_to ) );
		$payment_amount  = $bk_currency.$bk_paid_amount;
		
		$bk_booking_date	 = date('d M, y',strtotime( $bk_booking_date ));
		
		
		
		$date_format = get_option('date_format');
		$time_format = get_option('time_format');
		$time = explode('-',$bk_slottime);
		
	} else{
		$bk_payment_date = pll__('NILL','docdirect_core');
		$bk_transaction_status = pll__('NILL','docdirect_core');
		$bk_paid_amount = pll__('NILL','docdirect_core');
		$bk_user_from = pll__('NILL','docdirect_core');
		$payment_status = pll__('NILL','docdirect_core');
		$bk_timestamp = pll__('NILL','docdirect_core');
		$bk_user_to = pll__('NILL','docdirect_core');
		$bk_booking_note = pll__('NILL','docdirect_core');
		$bk_useremail = pll__('NILL','docdirect_core');
		$bk_userphone = pll__('NILL','docdirect_core');
		$bk_username = pll__('NILL','docdirect_core');
		$bk_subject = pll__('NILL','docdirect_core');
		$bk_slottime = pll__('NILL','docdirect_core');
		$bk_booking_date = pll__('NILL','docdirect_core');
		$bk_currency	= pll__('NILL','docdirect_core');
		$bk_service	= '';
		$bk_category	= '';
		$bk_code	= '';
		$payment_amount  = pll__('NILL','docdirect_core');
		
	}
	?>
	<ul class="invoice-info">
		<li>
			<strong><?php esc_html_e('Tracking id','docdirect_core');?></strong>
			<span><?php echo esc_attr( $bk_code );?></span>
		</li>
		<li>
			<strong><?php esc_html_e('Appointment Date','docdirect_core');?></strong>
			<span><?php echo esc_attr( $bk_booking_date );?></span>
		</li>
	 	<?php if( !empty( $services_cats[$bk_category] ) ){?>
            <li>
                <strong><?php esc_html_e('Appointment Category','docdirect_core');?></strong>
                <span><?php echo esc_attr( $services_cats[$bk_category] );?></span>
            </li>
        <?php }?>
        <?php if( !empty( $booking_services[$bk_service] ) ){?>
            <li>
                <strong><?php esc_html_e('Appointment Service','docdirect_core');?></strong>
                <span><?php echo esc_attr( $booking_services[$bk_service]['title'] );?></span>
            </li>
        <?php }?>
        <li>
            <strong><?php esc_html_e('Appointment Fee','docdirect_core');?></strong>
            <span><?php echo esc_attr( $payment_amount );?></span>
        </li>
        <li>
			<strong><?php esc_html_e('Contact Number','docdirect_core');?></strong>
			<span><?php echo esc_attr( $bk_userphone );?></span>
		</li>
		<?php if( !empty( $bk_user_from->data ) ){?>
			<li>
				<strong><?php esc_html_e('User From','docdirect_core');?></strong>
				<span><a href="<?php echo get_edit_user_link($bk_user_from->data->ID);?>" target="_blank" title="<?php pll__('Click for user details','docdirect_core');?>"><?php echo esc_attr( $bk_user_from->data->display_name );?></a></span>
			</li>
		<?php }?>
        <?php if( !empty( $bk_user_to->data ) ){?>
			<li>
				<strong><?php esc_html_e('User To','docdirect_core');?></strong>
				<span><a href="<?php echo get_edit_user_link($bk_user_to->data->ID);?>" target="_blank" title="<?php pll__('Click for user details','docdirect_core');?>"><?php echo esc_attr( $bk_user_to->data->display_name );?></a></span>
			</li>
		<?php }?>
        <?php if( !empty( $bk_status ) ){?>
		<li>
			<strong><?php esc_html_e('Booking Status','docdirect_core');?></strong>
			<span><?php echo esc_attr( ucwords( $bk_status ) );?></span>
		</li>
        <?php }?>
        <?php if( !empty( $bk_transaction_status ) ){?>
		<li>
			<strong><?php esc_html_e('Payment Status','docdirect_core');?></strong>
			<span><?php echo esc_attr( docdirect_prepare_order_status( 'value',$bk_transaction_status ) );?></span>
		</li>
        <?php }?>
        <?php if( !empty( $bk_payment ) ){?>
		<li>
			<strong><?php esc_html_e('Payment Method','docdirect_core');?></strong>
			<span><?php echo esc_attr( docdirect_prepare_payment_type( 'value',$bk_payment ) );?></span>
		</li>
        <?php }?>
		<?php if( !empty( $time[0] ) && !empty( $time[1] ) ){?>
        <li>
			<strong><?php esc_html_e('Metting Time','docdirect_core');?></strong>
			<span><?php echo date_i18n($time_format,strtotime('2016-01-01 '.$time[0]) );?>&nbsp;-&nbsp;<?php echo date_i18n($time_format,strtotime('2016-01-01 '.$time[1]) );?></span>
		</li>
        <?php }?>
        <li>
			<strong><?php esc_html_e('Note','docdirect_core');?></strong>
			<span><?php echo esc_attr( $bk_booking_note );?></span>
		</li>
	</ul>
	<?php
}

/**
 * @Init Appointment info
 * @return {post}
 */
function kt_docdirect_meta_box_appointmentinfo(){
	global $post;
	
	if (function_exists('fw_get_db_settings_option')) {
		$bk_code = get_post_meta($post->ID, 'bk_code', true);
		$bk_code	= !empty( $bk_code ) ? $bk_code : pll__('NILL','docdirect_core');
	} else{
		$bk_code = pll__('NILL','docdirect_core');
	}
	
	?>
	<ul class="invoice-info side-panel-info">
		<li>
			<strong><?php esc_html_e('Booking Code','docdirect_core');?></strong>
			<span><?php echo esc_attr( $bk_code );?></span>
		</li>
	</ul>
	<?php
}

function kt_ajax_load_speacialties(){
	global $current_user;
	$user_identity	= $current_user->ID;

  	$first_category = isset( $_POST['id'] ) ? esc_sql( $_POST['id'] ) : '';  	
		ob_start();
  	?>
      <?php 
        if( isset( $first_category ) ){
            $attached_specialities = get_post_meta( $first_category, 'attached_specialities', true );
            if( isset( $attached_specialities ) && !empty( $attached_specialities ) ){
                foreach( $attached_specialities as $key => $speciality ){
                    if( !empty( $speciality ) ) {
                        $term_data	= get_term_by( 'id', $speciality, 'specialities');
                        if( !empty( $term_data ) ) {

                        	$img = '';

                        	?>
                             <div class="doc-checkbox">
                                <input type="checkbox" name="speciality[]" value="<?php echo esc_attr( $term_data->slug );?>" id="speciality-<?php echo esc_attr( $term_data->slug);?>">
                                <label for="speciality-<?php echo esc_attr( $term_data->slug);?>"><?php echo $img.esc_attr( $term_data->name );?></label>
                             </div>
						<?php
                        }
                    }
                }
            }
        }
        ?>
  	<?php
		$json['data']	 = ob_get_clean();
	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;

}
add_action('wp_ajax_load_speacialties', 'kt_ajax_load_speacialties');
add_action('wp_ajax_nopriv_load_speacialties', 'kt_ajax_load_speacialties');



function kt_ajax_verify_paypal(){
	global $current_user;
	$user_identity	= $current_user->ID;

	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

  	$paypal_username = isset( $_POST['paypal_username'] ) ? esc_sql( $_POST['paypal_username'] ) : ''; 
  	$paypal_password = isset( $_POST['paypal_password'] ) ? esc_sql( $_POST['paypal_password'] ) : ''; 
  	$paypal_signature = isset( $_POST['paypal_signature'] ) ? esc_sql( $_POST['paypal_signature'] ) : ''; 

  	if (!empty($paypal_username) && !empty($paypal_password) && !empty($paypal_signature) ) {
  		
		// Create a new PayPal class instance, and set the sandbox mode to true
		$paypal = new wp_paypal_gateway (true);
		$paypal->setVarApi($paypal_username, $paypal_password, $paypal_signature);

		$return_url	= get_author_posts_url($current_user->ID);

		$requestParams = array(
		    'RETURNURL' => $return_url, //Enter your webiste URL here
		    'CANCELURL' => $return_url//Enter your website URL here
		); 
		$orderParams = array(
		    'LOGOIMG' => "", //You can paste here your logo image URL
		    "NOSHIPPING" => "1", //I do not want shipping
		    "ALLOWNOTE" => "0", //I do not want to allow notes
		    "BRANDNAME" => $blogname,
		    "GIFTRECEIPTENABLE" => "0",
		    "GIFTMESSAGEENABLE" => "0"
		);
		$item = array(
		    'LOCALECODE' => 'en_UK',
		 
		    'PAYMENTREQUEST_0_AMT' => '1',
		    'PAYMENTREQUEST_0_CURRENCYCODE' => 'HKD',
		    'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
		    'PAYMENTREQUEST_0_ITEMAMT' => '1',
		 
		    'L_PAYMENTREQUEST_0_NAME0' => 'name',
		    'L_PAYMENTREQUEST_0_DESC0' => 'desc',
		    'L_PAYMENTREQUEST_0_QTY0' => '1',
		    'L_PAYMENTREQUEST_0_AMT0' => '1',

		    'L_BILLINGTYPE0' => 'MerchantInitiatedBillingSingleAgreement',
		    'L_BILLINGAGREEMENTDESCRIPTION0' => 'none',
		        //"PAYMENTREQUEST_0_INVNUM" => $transaction->id - This field is useful if you want to send your internal transaction ID
		);
		 
		// Display the response if successful or the debug info
		$paypal->setExpressCheckout($requestParams + $orderParams + $item);
		$response = $paypal->getResponse();

		$today = current_time('timestamp');

	    if(is_array($response) && $response["ACK"]=="Success"){

			update_user_meta( $user_identity, 'paypal_username', $_POST['paypal_username'] );
			update_user_meta( $user_identity, 'paypal_password', $_POST['paypal_password'] );
			update_user_meta( $user_identity, 'paypal_signature', $_POST['paypal_signature'] );

			$new_user = get_user_meta($url_identity , 'new_user' , true);
			if( $new_user == '' || $new_user == false ) {
				update_user_meta( $user_identity, 'new_user', 'no' );
				$val = '179';
				$membership_date	= strtotime("+".$val." days", $today);

				update_user_meta($user_identity, 'user_featured', $membership_date);

				$json['type']	 = 'success';
				$json['message']  = pll__('Trial Actived','docdirect');
				echo json_encode($json);
				die;
			}else {
				$json['type']	 = 'error';
				$json['message']  = pll__('Only Trial One time','docdirect');
				echo json_encode($json);
				die;
			}
	    }else {
	    	// $msg = $response['L_SHORTMESSAGE0'];
	    	$msg = 'Wrong info';

			$json['type']	 = 'error';
			$json['message']  = $msg;
			echo json_encode($json);
			die;
	    }
  	}else {

		$json['type']	 = 'error';
		$json['message']  = pll__('Please fill all field');
		echo json_encode($json);
		die;
  	}
  	

}
add_action('wp_ajax_verify_paypal', 'kt_ajax_verify_paypal');
add_action('wp_ajax_nopriv_verify_paypal', 'kt_ajax_verify_paypal');

function kt_ajax_confirm_trial(){
	global $current_user;
	$user_identity	= $current_user->ID;

	update_user_meta($user_identity, 'show_popup', 'no');

	//prepare return url
	$dir_profile_page = '';
	if (function_exists('fw_get_db_settings_option')) {
        $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
    }
	$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
	
	$return_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'booking-settings', $user_identity,true);
	
	$permalink = add_query_arg( 
			array(
				'ref'=>  'booking-settings' ,
				'identity'=>   urlencode( $user_identity ) ,
				'verify'=>   'paypal' 
				), esc_url( get_permalink($profile_page) 
			) 
		);
  	
	$json['url']	 = $permalink;
	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;
  	

}
add_action('wp_ajax_confirm_trial', 'kt_ajax_confirm_trial');
add_action('wp_ajax_nopriv_confirm_trial', 'kt_ajax_confirm_trial');

//Making jQuery Google API
function modify_jquery_version() {
    if (!is_admin()) {
        wp_deregister_script('jquery');
        wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js', false, '2.0.s');
        wp_enqueue_script('jquery');
    }
}
// add_action('init', 'modify_jquery_version');


add_filter('document_title_parts', 'dq_override_post_title', 10, 1);
function dq_override_post_title($title){
	global $wp_query,$current_user;
   // change title for singular blog post
    if( is_author() ){ 
		$current_author_profile = $wp_query->get_queried_object();
        // change title parts here
        $title['title'] = kt_get_title_name($current_author_profile->ID).esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );
    	// $title['page'] = '22'; // optional
   	 	// $title['tagline'] = '343242Home Of Genesis Themes'; // optional
        // $title['site'] = 'sgdsadgDevelopersQ'; //optional
    }

    return $title; 
}

// add_action('wp_footer', 'kt_add_button_mobile_quick_search');
function kt_add_button_mobile_quick_search() {

	$dir_search_page = fw_get_db_settings_option('dir_search_page');
	if( !is_front_page() && !is_page($dir_search_page[0]) ){
	?>
	<div class="quick_search"><a class="" href="javascript:;"><i class="fa fa-search"></i><?php pll_e('Find a specialist');?></a></div>
	<?php
	}
}


function kt_ajax_open_modal_request(){
	// global $current_user;
	// $user_identity	= $current_user->ID;

	$doctor_id = $_POST['doctor_id'];
    
	        $user_info = get_userdata( $doctor_id );
	        $avatar = apply_filters(
	                'docdirect_get_user_avatar_filter',
	                 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $doctor_id),
	                 array('width'=>150,'height'=>150) //size width,height
	            );
	ob_start();?>
	    <a href="<?php echo get_author_posts_url($doctor_id); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
	    <div>
	        <h4><?php echo esc_attr( $user_info->first_name.' '.$user_info->last_name );?></h4>
	        <?php if( !empty( $user_info->tagline ) ) {?>
	            <span><?php echo esc_attr( $user_info->tagline );?></span>
	        <?php }?>
	    </div>
	    <input type="hidden" name="doctor_id" value="<?php echo $doctor_id;?>">
  	
	<?php $output .= ob_get_clean();

	$json['output']	 = $output;
	$json['type']	 = 'success';
	$json['message']  = pll__('Success','docdirect');
	echo json_encode($json);
	die;
  	

}
add_action('wp_ajax_open_modal_request', 'kt_ajax_open_modal_request');
add_action('wp_ajax_nopriv_open_modal_request', 'kt_ajax_open_modal_request');


function kt_ajax_request_appoinment(){
	global $current_user;
	$user_identity	= $current_user->ID;

	if (	$_POST['first_name'] == '' ||
		$_POST['last_name'] == '' ||
		$_POST['email'] == '' ||
		$_POST['phone'] == ''
	) {
		$json['type']	 = 'error';
		$json['message']  = pll__('Please fill require field','docdirect');
		echo json_encode($json);
		die;
	}else if( !is_email($_POST['email']) ) {	
		$json['type']		= 'error';
		$json['message']	= pll__('Email address is invalid.', 'docdirect');
		echo json_encode($json);
		die();
	}else {

		$params = array();
		$params['user_id'] = $_POST['doctor_id'];
		$params['first_name'] = $_POST['first_name'];
		$params['last_name'] = $_POST['last_name'];
		$params['email'] = $_POST['email'];
		$params['phone'] = $_POST['phone'];
		$params['gender'] = $_POST['gender'];
		$params['date_of_birth'] = $_POST['date_of_birth'];
		$params['insurer'] = $_POST['insurer'];
		$params['hkid'] = $_POST['hkid'];
		$params['message'] = $_POST['message'];
		kt_send_email_request_appoiment($params);

		$json['type']	 = 'success';
		$json['message']  = pll__('Success','docdirect');
		echo json_encode($json);
		die;

	}
  	
  	

}
add_action('wp_ajax_request_appoinment', 'kt_ajax_request_appoinment');
add_action('wp_ajax_nopriv_request_appoinment', 'kt_ajax_request_appoinment');


function kt_send_email_request_appoiment( $params ) {
		global $current_user;
		extract( $params );
			
		$email_helper	= new DocDirectProcessEmail();
			
			$email_to		 = 'hunter.asian@gmail.com';			
			 
		    $user_info = get_userdata( $user_id );
			$username 		= $user_info->display_name;
			$user_email 		= $user_info->user_email;
			
			$subject_default = 'Request Appointment!';
			$booking_approved_default = 'Hi %username%,<br/>
											you has request appointment<br/><br/>

											First Name: %first_name%<br/>
											Last Name: %last_name%<br/>
											Gender: %gender%<br/>
											Phone Number: %phone%<br/>
											Email Address: %email%<br/>
											Date of Birth: %date_of_birth%<br/>
											Insurer: %insurer%<br/>
											HKID/Passport #: %hkid%<br/>
											Message: %message%<br/>

											<br/><br/>
											Sincerely,<br/>
											DocDirect Team<br/>
											%logo%
									';
			
			
			if (function_exists('fw_get_db_post_option')) {
				$subject = fw_get_db_settings_option('request_appoinment_subject');
				$booking_approved = fw_get_db_settings_option('request_appoinment_content');
			}
			
			//set defalt contents
			if( empty( $subject ) ){
				$subject = $subject_default;
			}
			
			//set defalt title
			if( empty( $booking_approved ) ){
				$booking_approved = $booking_approved_default;
			}
			
			$logo		   = kt_process_get_logo();
			
			$booking_approved = str_replace("%first_name%", nl2br($first_name), $booking_approved); //Replace
			$booking_approved = str_replace("%last_name%", nl2br($last_name), $booking_approved); //Replace
			$booking_approved = str_replace("%gender%", nl2br($gender), $booking_approved); //Replace
			$booking_approved = str_replace("%phone%", nl2br($phone), $booking_approved); //Replace
			$booking_approved = str_replace("%email%", nl2br($email), $booking_approved); //Replace
			$booking_approved = str_replace("%date_of_birth%", nl2br($date_of_birth), $booking_approved); //Replace
			$booking_approved = str_replace("%insurer%", nl2br($insurer), $booking_approved); //Replace
			$booking_approved = str_replace("%hkid%", nl2br($hkid), $booking_approved); //Replace
			$booking_approved = str_replace("%message%", nl2br($message), $booking_approved); //Replace

			$booking_approved = str_replace("%username%", nl2br($username), $booking_approved); //Replace Name
			$booking_approved = str_replace("%logo%", nl2br($logo), $booking_approved); //Replace logo

			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			
			$email_headers = "From: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "Reply-To: no-reply <info@no-reply.com>\r\n";
			$email_headers .= "MIME-Version: 1.0\r\n";
			$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			$attachments	  = '';
			$body			 = '';
			$body			.= $email_helper->prepare_email_headers($customer_name);
			
			
			$body			.= ' <p style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6em; font-weight: normal; margin: 0 0 10px; padding: 0;">'.$booking_approved.'</p>';
			$body			.= '<table class="btn-primary" cellpadding="0" cellspacing="0" border="0" style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; width: auto !important; Margin: 0 0 10px; padding: 0;"><tr style="font-family: \'Helvetica Neue\', \'Helvetica\', Helvetica, Arial, sans-serif; font-size: 100%; line-height: 1.6em; margin: 0; padding: 0;"><td style="font-family: \'Helvetica Neue\', Helvetica, Arial, \'Lucida Grande\', sans-serif; font-size: 14px; line-height: 1.6em; border-radius: 25px; text-align: center; vertical-align: top; background: #348eda; margin: 0; padding: 0;" align="center" bgcolor="#348eda" valign="top">
                
                </td>
              </tr></table>';

			$body	.= $email_helper->prepare_email_footers();
			// wp_mail($email_to, $subject, $body);
			// wp_mail($admin_email, $subject, $body);
			wp_mail($user_email, $subject, $booking_approved);
			
			return true;
		 }

function kt_ajax_complete_appointment_status(){
	global $current_user;
	$user_identity	= $current_user->ID;

		$post_id	  = sanitize_text_field( $_POST['id'] );

		update_post_meta($post_id, 'complete_status', 'completed');
		
		//Send Email
		$email_helper	  = new DocDirectProcessEmail();
		$emailData	= array();
		$emailData['post_id']	= $post_id;
		// $email_helper->process_appointment_approved_email($emailData);
		kt_complete_appointment_email($emailData);


		$json['type']	 = 'success';
		$json['message']  = pll__('Success','docdirect');
		echo json_encode($json);
		die;
  	
  	

}
add_action('wp_ajax_complete_appointment_status', 'kt_ajax_complete_appointment_status');

function kt_ajax_change_confirm_status(){
	global $current_user;
	$user_identity	= $current_user->ID;

		// update_post_meta($post_id, 'complete_status', 'completed');
		$type		  = sanitize_text_field( $_POST['type']);
		$post_id	  = sanitize_text_field( $_POST['id'] );
		
		if( empty( $type ) 
			||
			empty( $post_id )
		){
			$json['type']	= 'error';
			$json['message']	= pll__('Some error occur, please try again later.','docdirect');
			echo json_encode($json);
			die;
		}

		if( $type === 'yes' ){
			$value	= 'yes';
			
			update_post_meta($post_id,'confirm_status',$value);			
			
			//Send status
			$json['action_type']	= $value;
			$json['type']		   = 'success';
			$json['message']		= pll__('Appointment status has updated.','docdirect');
			echo json_encode($json);
			die;
		
		} else if( $type === 'no' ){
			$value	= 'no';

			//Send Email
			$emailData	= array();
			$emailData['post_id']	= $post_id;
			kt_patient_confirm_email($emailData);
			
			update_post_meta($post_id,'confirm_status',$value);
			//wp_delete_post( $post_id );
			
			//Return status
			$json['action_type']	= $value;
			$json['type']		   = 'success';
			$json['message']		= pll__('An email sent to doctor.','docdirect');
			echo json_encode($json);
			die;
		}
		
		//Send Email
		/*$emailData	= array();
		$emailData['post_id']	= $post_id;
		kt_patient_confirm_email($emailData);


		$json['type']	 = 'success';
		$json['message']  = pll__('Success','docdirect');
		echo json_encode($json);
		die;*/
  	
  	

}
add_action('wp_ajax_change_confirm_status', 'kt_ajax_change_confirm_status');

function kt_ajax_doctor_cancel_booking(){
	global $current_user;
	$user_identity	= $current_user->ID;

	$post_id	  = sanitize_text_field( $_POST['appointment_id'] );
	$desc	  = sanitize_text_field( $_POST['desc'] );

	if ( empty( $_POST['appointment_id'] ) ) {
		$json['type']		=  'error';	
		$json['message']		= pll__('Some error occur, please try again later.','docdirect');
		echo json_encode($json);
		die;
	} 

	if ( empty( $_POST['desc'] ) ) {
		$json['type']		=  'error';	
		$json['message']		= pll__('Please type some description.','docdirect');
		echo json_encode($json);
		die;
	} 
			$bk_payment       = get_post_meta($post_id, 'bk_payment',true);
			if ( $bk_payment == 'paypal' ) {

		        $paypal = new wp_paypal_gateway (true);

		        $paypal_username    = get_user_meta( $current_user->ID, 'paypal_username', true);
		        $paypal_password    = get_user_meta( $current_user->ID, 'paypal_password', true);
		        $paypal_signature   = get_user_meta( $current_user->ID, 'paypal_signature', true);
	        	$paypal->setVarApi($paypal_username, $paypal_password, $paypal_signature);

	            $TRANSACTIONID = get_post_meta($post_id, 'bk_code', true);
	        	$requestParams = array('TRANSACTIONID' => $TRANSACTIONID);
		        $paypal->RefundTransaction($requestParams);
		        $response = $paypal->getResponse();
	        	if(is_array($response) && $response["ACK"]=="Success"){

					$value	= 'cancelled';

					//Send Email
					$email_helper	  = new DocDirectProcessEmail();
					$emailData	= array();
					$emailData['post_id']	= $post_id;
					$email_helper->process_appointment_cancelled_email($emailData);

					$emailData['desc']	= $desc;
					kt_doctor_cancel_booking_email($emailData);
					
					update_post_meta($post_id,'bk_status',$value);
					// wp_delete_post( $post_id );
					
					//Return status
					$json['action_type']	= $value;
					$json['type']		   = 'error';
					$json['message']		= pll__('Appointment has been cancelled.','docdirect');
					echo json_encode($json);
					die;
				}else {
					//Return status
					$json['action_type']	= 'error';
					$json['type']		   = '';
					$json['message']		= pll__('Some error occur, please try again later.','docdirect');
					echo json_encode($json);
					die;
				}

			}else {
				
				$value	= 'cancelled';

				$emailData	= array();
				$emailData['post_id']	= $post_id;
				$emailData['desc']	= $desc;
				kt_doctor_cancel_booking_email($emailData);

				update_post_meta($post_id,'bk_status',$value);
				// wp_delete_post( $post_id );

				//Return status
				$json['action_type']	= 'cancelled';
				$json['type']		   = 'success';
				$json['message']		= pll__('Appointment has been cancelled.','docdirect');
				echo json_encode($json);
				die;

			}

			/*$emailData	= array();
			$emailData['post_id']	= $post_id;
			$emailData['desc']	= $desc;
			kt_doctor_cancel_booking_email($emailData);

			wp_delete_post( $post_id );

			$json['type']		   = 'success';
			$json['message']		= pll__('An email has sent to patient.','docdirect');
			echo json_encode($json);
			die;*/


}

add_action('wp_ajax_doctor_cancel_booking', 'kt_ajax_doctor_cancel_booking');

add_action('docdirect_verify_user_account', 'kt_action');
function kt_action() {
	global $wpdb;
        
	if(!is_user_logged_in() && is_front_page()) {
        if ( !empty($_GET['key']) && !empty($_GET['verifyemail']) ) {
            $verify_key 	= esc_attr( $_GET['key'] );
            $user_email 	= esc_attr( $_GET['verifyemail'] );

            $user_identity = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s", $user_email));
			if( !empty( $user_identity ) ){
				$confirmation_key = get_user_meta(intval( $user_identity ), 'confirmation_key', true);
				if ( $confirmation_key === $verify_key ) {
					//prepare return url
					$dir_profile_page = '';
					if (function_exists('fw_get_db_settings_option')) {
		                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
		            }
					
					$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
					$return_url = get_the_permalink($profile_page);
					$return_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'settings', $user_identity,true);

					// $current_user = get_user_by( 'id', $user_identity );
					// wp_set_current_user( $user_identity, $current_user->user_login );
					// set the WP login cookie
					// $secure_cookie = is_ssl() ? true : false;
					// wp_set_auth_cookie( $user_identity, true, $secure_cookie );
					update_user_meta($user_identity, 'verify_user', 'on');
					?>
					<script type="text/javascript">
						jQuery(document).on('ready', function () { 
							jQuery.sticky(scripts_vars.account_verification, {classList: 'success', speed: 200, autoclose: 20000, position: 'top-right', }); 

								$('.tg-user-modal').modal('show');
								// setTimeout(function(){window.location = '<?php echo $return_url;?>';} , 5000);
					});
					</script>
					<?php
				}
			}
					// $script = "jQuery(document).on('ready', function () { jQuery.sticky(scripts_vars.account_verification, {classList: 'success', speed: 200, autoclose: 20000, position: 'top-right', }); });";
            		// wp_add_inline_script('docdirect_functions', $script, 'after');
        }
		
	}

}
add_action('wp_footer', 'kt_add_success_reg_modal');
function kt_add_success_reg_modal() {

	if(function_exists('fw_get_db_settings_option')) {
		$enable_registration = fw_get_db_settings_option('registration', $default_value = null);
		$enable_login = fw_get_db_settings_option('enable_login', $default_value = null);
	}
	if( ( isset( $enable_login ) && $enable_login === 'enable' ) 
                ||  ( isset( $enable_registration ) && $enable_registration === 'enable' ) 
          ) {
        if(!is_user_logged_in()) {
        	?>

	<div class="modal fade tg-user-reg_success" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
		<div class="tg-modal-content">
		    <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">×</button>
		        <h4 class="modal-title"><?php pll_e('Thank you for registering!');?></h4>
		    </div>
      		<div class="modal-body">
				<?php
					pll_e('Please check your email to verify your account.<br> After verification, your dashboard will then be activated.');
				?>
			</div>
		</div>
	</div>
        	<?php
        }
    }
}

//change url logo login
function ryno_change_wplogin_url() {
        return get_bloginfo('url');
}
add_filter('login_headerurl', 'ryno_change_wplogin_url');


add_action('docdirect_prepare_subheaders', 'kt_add_submenu');

function kt_add_submenu() {
	if(is_author()){
		global $wp_query,$current_user;
		$current_author_profile = $wp_query->get_queried_object();
		$user_gallery	  = $current_author_profile->user_gallery;
		$privacy		= docdirect_get_privacy_settings($current_author_profile->ID); //Privacy settings
        $verify_user    = get_user_meta( $current_author_profile->ID, 'verify_user', true);
        $public_profile    = get_user_meta( $current_author_profile->ID, 'public_profile', true);

	    $db_directory_type   = get_user_meta( $current_author_profile->ID, 'directory_type', true);
	    $terms = get_the_terms($db_directory_type, 'group_label');
	    $list_terms = array();
	    foreach ($terms as $key => $value) {
	        $list_terms[] = $value->slug;
	    }
	    $current_group_label_slug = $terms[0]->slug;
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
		<div class="tg-section-btn">
            <button class="btn hidden-xs" data-target="tg-section-map"><?php pll_e('Location');?></button>
            <button class="btn" data-target="tg-aboutuser"><?php pll_e('About');?></button>
            <?php if( isset($current_option['affiliations']) && ( in_array('company', $list_terms) ||
            in_array('medical-centre', $list_terms) ||
            in_array('hospital-type', $list_terms) ||
            in_array('scans-testing', $list_terms)
    ) ){?>
                <button class="btn" data-target="tg-affiliation"><?php pll_e('Affiliation');?></button>
            <?php }?>
            <?php if( !empty( $current_author_profile->prices_list ) ){?>
                <button class="btn hidden-xs" data-target="tg-section-price_list"><?php pll_e('Prices List');?></button> 
            <?php }?>
            <?php 
                if(!empty( $privacy['appointments'] )
                && 
                $privacy['appointments'] == 'on'
                && 
                $verify_user == 'on'
                && 
                $public_profile != 'off'
                && 
                isset($current_option['patient_bookings'])
                ) {
                // if( $current_user->ID != $current_author_profile->ID ) { ?>
                <button class="btn" data-target="tg-online-booking"><?php pll_e('Online booking');?></button>
            <?php }//}?>
            <?php if( isset( $user_gallery ) && !empty( $user_gallery ) && isset($current_option['photo_gallery']) ){?>
                <button class="btn" data-target="tg-userphotogallery"><?php pll_e('Photo Gallery');?></button>
            <?php }?>
            <?php
                $args = array(
                    'post_type' => 'post',
                    'orderby' => 'ID',
                    // 'posts_per_page' => -1,
                    'author' => $current_author_profile->ID,
                );
                $ListPost = get_posts($args);
                if (!empty($ListPost) && isset($current_option['articles'])) {?>
                    <button class="btn" data-target="user-artiles"><?php pll_e('Articles');?></button>
            <?php }?>
            <button class="btn" data-target="tg_specialities"><?php pll_e('Specialties');?></button>
            <?php if( !empty( $current_author_profile->user_profile_insurers )){?>
            	<button class="btn" data-target="tg-insurance"><?php pll_e('Insurance');?></button>
            <?php }?>
            <?php if( isset($current_option['affiliations']) && ( !in_array('company', $list_terms) &&            
		        !in_array('medical-centre', $list_terms) &&
		        !in_array('hospital-type', $list_terms) &&           
		        !in_array('scans-testing', $list_terms)
		    )  ){?>
                <button class="btn" data-target="tg-affiliation"><?php pll_e('Affiliation');?></button>
            <?php }?>
            <button class="btn" data-target="tg-userexperience"><?php pll_e('Experience');?></button>
            <button class="btn" data-target="tg-userreviews"><?php pll_e('Reviews');?></button>
          </div>
	<?php
	}
}
add_action('docdirect_prepare_subheaders', 'kt_add_submenu2', 99);

function kt_add_submenu2() {
	if ( is_page('medical-articles') || is_page('blog-list') || is_page('new-feed') ) {
		?>
		<div class="title_page">
			<div class="container">			
	            <div class="tg-heading-border tg-small">
	                <h3><i class="fa fa-file-text"></i><?php echo get_the_title();?></h3>
	            </div>
			</div>
		</div>
		<?php
	}
}

/**
 * Modify the "must_log_in" string of the comment form.
 *
 * @see http://wordpress.stackexchange.com/a/170492/26350
 */
add_filter( 'comment_form_defaults', function( $fields ) {

    $fields['must_log_in'] = sprintf( 
        pll__( '<p class="must-log-in">
                 <a class="tg-btn" href="javascript:;"  data-toggle="modal" data-target=".tg-user-modal">Login to post a comment.</a></p>' 
        )   
    );
    return $fields;
});

// define the comment_reply_link callback 
function filter_comment_reply_link( $args_before_link_args_after, $args, $comment, $post ) { 
    // make filter magic happen here... 
    if (!is_user_logged_in()) {
    	return '<a class="tg-btn" href="javascript:;" data-toggle="modal" data-target=".tg-user-modal">'.pll__('Login to reply').'</a>'; 
    }else {
    	return $args_before_link_args_after;
    }
}; 
         
// add the filter 
add_filter( 'comment_reply_link', 'filter_comment_reply_link', 10, 4 ); 

function kt_get_all_user_phone() {
	$arg = array(
		'meta_query' => array(
			'relation' => 'AND', // Optional, defaults to "AND"
			array(
				'key' 	  => 'phone_number',
				'value'   => '',
				'compare' => '!=',
			)
		)
	);
	$allusers = get_users( $arg );
	// Array of WP_User objects.
	$arr_user = array();

	foreach ( $allusers as $user ) {
		$user_id = $user->ID;
		$phone_number = get_user_meta($user_id, 'phone_number', true);
		
		$arr_user[] = str_replace(' ', '', $phone_number);
	}
	return $arr_user;
}


function kt_count_reviews_3months($userfrom, $userto) {

	$meta_query_args[] = array(
								'key'     => 'user_from',
								'value'   => $userfrom,
								'compare'   => '='
							);
	$meta_query_args[] = array(
								'key'     => 'user_to',
								'value'   => $userto,
								'compare'   => '='
							);
	$args 		= array('posts_per_page' => -1, 
						'post_type' => 'docdirectreviews', 
						'post_status' => 'publish', 
						'ignore_sticky_posts' => 1,
						'date_query' => array(
					        array(
					            'after'  => '90 days ago'
					        ),
					    ),
					);
	if( !empty( $meta_query_args ) ) {
		$query_relation = array('relation' => 'AND',);
		$meta_query_args	= array_merge( $query_relation,$meta_query_args );
		$args['meta_query'] = $meta_query_args;
	}

	$query 		= new WP_Query( $args );
	$count_post = $query->post_count; 

	$count = 0;
	return $count_post;

}

function user_id_exists($user){

    global $wpdb;

    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user));

    if($count == 1){ return true; }else{ return false; }

}


