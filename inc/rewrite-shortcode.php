<?php

add_action( 'init', 'remove_my_shortcodes', 10 );
function remove_my_shortcodes() {
    remove_shortcode( 'user_authentication' );
	add_shortcode('user_authentication', 'kt_shortCodeCallBack' );
}

function kt_shortCodeCallBack() {
	$enable_resgistration	= '';
	$enable_login		= '';
	$captcha_settings		= '';
	
	if(function_exists('fw_get_db_settings_option')) {
		$enable_resgistration = fw_get_db_settings_option('registration', $default_value = null);
		$enable_login = fw_get_db_settings_option('enable_login', $default_value = null);
		$captcha_settings = fw_get_db_settings_option('captcha_settings', $default_value = null);
	}
docdirect_enque_map_library();//init Map
	wp_enqueue_script('intlTelInput');
	wp_enqueue_style('intlTelInput');
	?>
    <div class="modal fade tg-user-modal" tabindex="-1" role="dialog">
		<div class="tg-modal-content">
			<ul class="tg-modaltabs-nav" role="tablist">
				<li role="presentation" class="active"><a href="#tg-signin-formarea" aria-controls="tg-signin-formarea" role="tab" data-toggle="tab"><?php pll_e('Sign In','docdirect_core');?></a></li>
				<li role="presentation"><a href="#tg-signup-formarea" aria-controls="tg-signup-formarea" role="tab" data-toggle="tab"><?php pll_e('Sign Up','docdirect_core');?></a></li>
			</ul>
			<div class="tab-content tg-haslayout">
				<div role="tabpanel" class="tab-pane tg-haslayout active" id="tg-signin-formarea">
					<?php if( $enable_login == 'enable' ) {
						if( apply_filters('docdirect_is_user_logged_in','check_user') === false ) {
							
							//Demo Ready
							$demo_username	= '';
							$demo_pass		= '';
							if( isset( $_SERVER["SERVER_NAME"] ) 
								&& $_SERVER["SERVER_NAME"] === 'themographics.com' ){
								$demo_username	= 'demo';
								$demo_pass		= 'demo';
							}
							
							if(function_exists('fw_get_db_settings_option')){
								$site_key = fw_get_db_settings_option('site_key');
							} else {
								$site_key = '';
							}

							$forgot_passwrod	= wp_lostpassword_url('/');
							
							?>
                            <form class="tg-form-modal tg-form-signin do-login-form">
                                <fieldset>
                                    <div class="form-group">
                                        <input type="text" name="username" value="<?php echo esc_attr( $demo_username );?>" placeholder="<?php pll_e('Enter Email or Username','docdirect_core');?>" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" value="<?php echo esc_attr( $demo_pass );?>" class="form-control" placeholder="<?php pll_e('Password','docdirect_core');?>">
                                    </div>
                                    <div class="form-group tg-checkbox">
                                        <label>
                                            <input type="checkbox" class="form-control">
                                            <?php pll_e('Remember Me','docdirect_core');?>
                                        </label>
                                        <a class="tg-forgot-password" href="javascript:;">
                                            <i><?php pll_e('Forgot Password','');?></i>
                                            <i class="fa fa-question-circle"></i>
                                        </a>	
                                    </div>
                                    <?php 
									if( isset( $captcha_settings ) 
											&& $captcha_settings === 'enable' 
										) {
									?>
                                        <div class="domain-captcha">
                                            <div id="recaptcha_signin"></div>
                                        </div>
                                    <?php }?>
                                    <button class="tg-btn tg-btn-lg do-login-button"><?php pll_e('LOGIN now','docdirect_core');?></button>
                                </fieldset>
                            </form>
							<div class="social_login">
								<h4><?php pll_e('Patient Social Login','docdirect_core');?></h4>
								<?php //echo do_shortcode('[oa_social_login]');?>
								<?php echo do_shortcode('[apsl-login-lite]');?>
							</div>
                         <?php }
						} else{?>
						<div class="tg-form-modal">
							<p class="alert alert-info theme-notification"><?php pll_e('Sign In is disabled by administrator','docdirect_core');?></p>
						</div>
					<?php }?>
				</div>
				<div role="tabpanel" class="tab-pane tg-haslayout" id="tg-signup-formarea">
					<?php 
					if( $enable_resgistration == 'enable') {
						if( apply_filters('docdirect_is_user_logged_in','check_user') === false ) {?>
						<form class="tg-form-modal tg-form-signup do-registration-form">
						<fieldset>
							<div class="form-group">
								<div class="tg-radiobox user-selection active-user-type">
									<input type="radio" checked="checked" name="user_type" value="professional" id="professional">
									<label for="professional"><?php pll_e('Professional','docdirect_core');?></label>
								</div>
								<div class="tg-radiobox user-selection active-user-type visitor-type">
									<input type="radio" name="user_type" value="visitor" id="visitor">
									<label for="visitor"><?php pll_e('Patient','docdirect_core');?></label>
								</div>
							</div>
							<div class="form-group social_login">
								<?php echo do_shortcode('[apsl-login-lite]');?>
							</div>
							<div class="form-group user-types"> 
								<?php 
								$member_group_terms = get_terms( array(
								    'taxonomy' => 'group_label',
								    // 'orderby' => 'ID',
						    		'i_order_terms' => true,
						    		'parent'   => 0,
								    'hide_empty' => false,
								) );
								?>
								<input class="select_category" type="hidden" name="directory_type" value="" />
						    	<a class="dropdown-button-group" href="javascript:;"><?php pll_e('Select User Type','docdirect_core');?></a>
						    	<div class="dropdown-input-group">
						    		<div class="dropdown-wrap">
									<?php
								foreach ( $member_group_terms as $p_term ) {
									?>
									    <h5><?php echo $p_term->name; ?></h5>
									    <div class="wrap_group <?php echo $p_term->slug;?>">
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
									    <ul class="<?php echo $member_group_term->slug; ?>">
									    <?php
									    if ( $member_group_query->have_posts() ) : while ( $member_group_query->have_posts() ) : $member_group_query->the_post(); 
									    	$trans_id = pll_get_post(get_the_ID(), 'en');

											$category_image = fw_get_db_post_option($trans_id, 'category_image', true);

											if( !empty( $category_image['attachment_id'] ) ){
												$banner_url	= docdirect_get_image_source($category_image['attachment_id'],150,150);
										 		$banner	= '<img src="'.$banner_url.'">';
									  		} else{
										 		$banner	= '<i class="fa '.$dir_icon.'"></i>';
										 	}
									    ?>
									        <li data-slug="<?php echo get_the_slug($trans_id);?>" data-id="<?php echo intval( pll_get_post(get_the_ID(), 'en_GB') );?>"><?php echo $banner; ?><?php echo the_title(); ?></li>
									    <?php endwhile; endif; ?>
									    </ul>
									    <?php
									    // Reset things, for good measure
									    $member_group_query = null;
									    wp_reset_postdata();
									}
									echo '</div>';
								}
									?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<input type="text" name="username" class="form-control" placeholder="<?php pll_e('Username','docdirect_core');?>">
							</div>
							<div class="form-group">
								<input type="email" name="email" class="form-control" placeholder="<?php pll_e('Email','docdirect_core');?>">
							</div>
							<div class="form-group plus_group">
								<div class="doc-select">
									<select name="title_name">
										<option value=""><?php pll_e('Title','docdirect_core');?></option>
										<?php
											$array_title = array(
												pll__('Dr'),
												pll__('Mr'),
												pll__('Ms'),
												pll__('Mrs'),
												pll__('Miss'),
												pll__('Professor'),
												pll__('Lord'),
												);
											foreach ($array_title as $value) {
												?>
												<option value="<?php echo $value;?>"><?php echo $value;?></option>
												<?php
											}
										?>
									</select>
								</div>
								<div>
									<input id="male" type="radio" name="gender" value="male">
									<label for="male"><?php pll_e('Male','docdirect_core');?></label>
								</div>
								<div>
									<input id="female" type="radio" name="gender" value="female">
									<label for="female"><?php pll_e('Female','docdirect_core');?></label>
								</div>
							</div>
							<div class="form-group">
								<input type="text" name="first_name" placeholder="<?php pll_e('First Name','docdirect_core');?>" class="form-control">
							</div>
							<div class="form-group last_name_group">
								<input type="text" name="last_name" placeholder="<?php pll_e('Last Name','docdirect_core');?>" class="form-control">
							</div>
							<div class="form-group">
								<input id="tel_userphone" type="text" name="phone_number" class="form-control" placeholder="Phone Number">
								<script type="text/javascript">		
									jQuery(document).ready(function(e) {							
										docdirect_intl_tel_input23();
									});
								</script>
							</div>
							<div class="form-group">
								<input type="password" name="password" class="form-control" placeholder="<?php pll_e('Password','docdirect_core');?>">
							</div>
							<div class="form-group">
								<input type="password" name="confirm_password" class="form-control" placeholder="<?php pll_e('Confirm Password','docdirect_core');?>">

							</div>
							<div class="form-group tg-checkbox">
								<input name="terms"  type="hidden" value="0"  />
								<label><input name="terms" class="form-control" type="checkbox"><?php pll_e(' I agree with the terms and conditions','docdirect_core');?></label>
								
							</div>
                            <?php 
							if( isset( $captcha_settings ) 
									&& $captcha_settings === 'enable' 
								) {
							?>
								<div class="domain-captcha">
									<div id="recaptcha_signup"></div>
								</div>
							<?php }?>
                            
							<button class="tg-btn tg-btn-lg  kt_do-register-button" type="submit" disabled="disabled"><?php pll_e('Create an Account','docdirect_core');?></button>
						</fieldset>
					</form>
                    <?php }
					} else{?>
						<div class="tg-form-modal">
							<p class="alert alert-info theme-notification"><?php pll_e('Registration is disabled by administrator','docdirect_core');?></p>
						</div>
					<?php }?>
				</div>
			</div>
		</div>
	</div>
    <?php
}
