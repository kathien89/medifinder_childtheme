<?php

		/**	
		 * @Profile Menu
		 * @Returns Dashoboard Menu
		 */
        function kt_docdirect_profile_menu( $menu_type="dashboard" ) {
			global $current_user, $wp_roles,$userdata,$post;
            $reference = (isset($_GET['ref']) && $_GET['ref'] <> '') ? $_GET['ref'] : $reference    = '';
			$mode = (isset($_GET['mode']) && $_GET['mode'] <> '') ? $_GET['mode'] : $mode	= '';
			$user_identity	= $current_user->ID;
			$user_role = get_user_meta('roles',$user_identity );
			
			$url_identity	= $user_identity;
			if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
				$url_identity	= $_GET['identity'];
			}

			$dir_profile_page = '';
			if (function_exists('fw_get_db_settings_option')) {
                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
                $directory_type    = get_user_meta( $user_identity, 'directory_type', true);
                $article_switch    = fw_get_db_post_option($directory_type, 'articles', true);
            }

			$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
			$db_user_type	= get_user_meta( $url_identity, 'user_type', true);
			
			if ( defined( 'POLYLANG_VERSION' ) ) {
				$profile_page = pll_get_post($profile_page);
			}

			ob_start();
			
			if ( is_user_logged_in() ){ 
            if( isset( $menu_type ) && $menu_type === 'dashboard' ) {?>
            <div class="tg-widget tg-widget-accordions">
                <h3><?php pll_e('Dashboard');?></h3>
                <ul class="docdirect-menu">
                    <?php if( $url_identity ==  $user_identity ) {?>
                         <li class="<?php echo ( $reference === 'settings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'settings', $user_identity); ?>"><i class="fa fa-gears"></i><?php pll_e('Profile Information');?></a></li>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                         <li class="<?php echo ( $reference === 'practices' ? 'active':'');?> tg-privatemessages tg-hasdropdown">
                            <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'listing'); ?>">
                                <i class="fa fa-map-marker"></i><?php pll_e('Manage Practices');?>
                            </a>
                            <ul class="tg-emailmenu">
                                <li class="<?php echo ( ($reference === 'practices' && $mode === 'listing') ? 'tg-active' : ''); ?>">
                                    <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'listing'); ?>">
                                        <span><?php esc_html_e('Practices listing', 'docdirect'); ?></span>
                                    </a>
                                </li>
                                <li class="<?php echo ( ($reference === 'practices' && $mode === 'add') ? 'tg-active' : ''); ?>">
                                    <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'practices', $user_identity, '', 'add'); ?>">
                                        <span><?php esc_html_e('Add New Practice', 'docdirect'); ?></span>
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
                            <li class="<?php echo ( $reference === 'invite-review' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invite-review', $user_identity); ?>"><i class="fa fa-user-plus"></i><?php pll_e('Invite Patient Review');?></a></li>
                        <?php }?>
                        <?php if( isset( $article_switch ) && $article_switch === 'enable' ){?>
                            <?php if ( function_exists('fw_get_db_settings_option') && fw_ext('articles')) { ?>
                                <li class="tg-privatemessages tg-hasdropdown <?php echo ( $reference === 'articles' ? 'tg-active' : ''); ?>">
                                    <a id="tg-btntoggle" class="tg-btntoggle" href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'articles', $user_identity, '', 'listing'); ?>">
                                        <span><?php esc_html_e('Manage Articles', 'docdirect'); ?></span>
                                        <em class="tg-totalmessages"><?php echo intval(docdirect_get_total_articles_by_user($user_identity)); ?></em>
                                    </a>
                                    <ul class="tg-emailmenu">
                                        <li class="<?php echo ( $mode === 'listing' ? 'tg-active' : ''); ?>">
                                            <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'articles', $user_identity, '', 'listing'); ?>">
                                                <span><?php esc_html_e('Article listing', 'docdirect'); ?></span>
                                            </a>
                                        </li>
                                        <li class="<?php echo ( $mode === 'add' ? 'tg-active' : ''); ?>">
                                            <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'articles', $user_identity, '', 'add'); ?>">
                                                <span><?php esc_html_e('Add New Article', 'docdirect'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        
                        
                        <h4 class="separator"><?php pll_e('Account Settings');?></h4>
                        <li class="grey <?php echo ( $reference === 'wishlist' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'wishlist', $user_identity); ?>"><i class="fa fa-heart"></i><?php pll_e('My Favourites');?></a></li>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="grey <?php echo ( $reference === 'dashboard' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'dashboard', $user_identity); ?>"><i class="fa fa-line-chart"></i><?php pll_e('Reviews & Statistics');?></a></li>
                        <?php }?>
                        <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                            <li class="grey <?php echo ( $reference === 'invoices' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity); ?>"><i class="fa fa-money"></i><?php pll_e('Upgrade Membership');?></a></li>
                        <?php }?>
                        <li class="grey <?php echo ( $reference === 'security' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'security', $user_identity); ?>"><i class="fa fa-lock"></i><?php pll_e('Change Password');?></a></li>
                        <?php if ( is_user_logged_in() ) {?>
                            <li class="grey"><a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><i class="fa fa-sign-in"></i><?php pll_e('Logout');?></a></li>
                        <?php }?>
                    <?php } else{ ?>
                        <li class=""><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'dashboard', $user_identity); ?>"><?php pll_e('Go to your profile');?></a></li>
                    <?php }?>
                </ul>
            </div>
            <?php } else{
					$avatar = apply_filters(
							'docdirect_get_user_avatar_filter',
							 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_identity) //size width,height
						);
				?>
            	
                <ul>
                    <?php /*?><li><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'dashboard', $user_identity); ?>"><img src="<?php echo esc_url( $avatar );?>" alt="<?php pll_e('Avatar');?>"  /><?php pll_e('Profile');?></a></li><?php */?>
                    <li class="<?php echo ( $reference === 'settings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'settings', $user_identity); ?>"><i class="fa fa-gears"></i><?php pll_e('Profile Information');?></a></li>
                    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                    <li class="<?php echo ( $reference === 'privacy-settings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'privacy-settings', $user_identity); ?>"><i class="fa fa-eye"></i><?php pll_e('Privacy Settings');?></a></li>
                    <?php }?>
					<?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="<?php echo ( $reference === 'addblog' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'addblog', $user_identity); ?>"><i class="fa fa-plus"></i><?php pll_e('Add Article');?></a><?php kt_button_premium_menu('articles');?></li>
                    <?php }?>
                        <?php if( isset( $article_switch ) && $article_switch === 'enable' ){?>
                            <?php if ( function_exists('fw_get_db_settings_option') && fw_ext('articles')) { ?>
                                <li class="tg-privatemessages tg-hasdropdown <?php echo ( $reference === 'articles' ? 'tg-active' : ''); ?>">
                                    <a id="tg-btntoggle" class="tg-btntoggle" href="javascript:">
                                        <span><?php esc_html_e('Manage Articles', 'docdirect'); ?></span>
                                        <em class="tg-totalmessages"><?php echo intval(docdirect_get_total_articles_by_user($user_identity)); ?></em>
                                    </a>
                                    <ul class="tg-emailmenu">
                                        <li class="<?php echo ( $mode === 'listing' ? 'tg-active' : ''); ?>">
                                            <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'articles', $user_identity, '', 'listing'); ?>">
                                                <span><?php esc_html_e('Article listing', 'docdirect'); ?></span>
                                            </a>
                                        </li>
                                        <li class="<?php echo ( $mode === 'add' ? 'tg-active' : ''); ?>">
                                            <a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'articles', $user_identity, '', 'add'); ?>">
                                                <span><?php esc_html_e('Add New Article', 'docdirect'); ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="<?php echo ( $reference === 'affiliation' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'affiliation', $user_identity); ?>"><i class="fa fa-user-plus"></i><?php pll_e('Add Affiliation');?></a><?php kt_button_premium_menu('affiliations');?></li>
                    <?php }?>
					<?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="<?php echo ( $reference === 'invite-review' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invite-review', $user_identity); ?>"><i class="fa fa-user-plus"></i><?php pll_e('Invite Patient Review');?></a></li>
                    <?php }?>
                    
                    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="<?php echo ( $reference === 'schedules' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'schedules', $user_identity); ?>"><i class="fa fa-list"></i><?php pll_e('My Office Hours');?></a></li>
                    <?php }?>

                    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="<?php echo ( $reference === 'bookings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'bookings', $user_identity); ?>"><i class="fa fa-book"></i><?php pll_e('Patient Bookings');?>
                                <?php
                                    $var = kt_get_number_booking($current_user->ID);
                                    if ( $var > 0 ) {
                                        echo '<span class="number_booking">'.$var.'</span>';
                                    }
                                ?></a><?php kt_button_premium_menu('patient_bookings');?></li>
                    <?php }else {?>
                        <li class="<?php echo ( $reference === 'mybookings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'mybookings', $user_identity); ?>"><i class="fa fa-book"></i><?php pll_e('My Bookings');?></a></li>
                    <?php }?>
                    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="<?php echo ( $reference === 'booking-schedules' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'booking-schedules', $user_identity); ?>"><i class="fa fa-calendar-check-o"></i><?php pll_e('Booking Schedule');?></a></li>
                    <?php }?>
                    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="<?php echo ( $reference === 'booking-settings' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'booking-settings', $user_identity); ?>"><i class="fa fa-cog"></i><?php pll_e('Email/Payout Options');?></a></li>
                    <?php }?>
                        <li class="green <?php echo ( $reference === 'wishlist' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'wishlist', $user_identity); ?>"><i class="fa fa-heart"></i><?php pll_e('My Favourites');?></a></li>
                    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="green <?php echo ( $reference === 'dashboard' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'dashboard', $user_identity); ?>"><i class="fa fa-line-chart"></i><?php pll_e('Reviews & Statistics');?></a></li>
                    <?php }?>
                    <?php if( apply_filters('docdirect_do_check_user_type',$user_identity ) === true ){?>
                        <li class="green <?php echo ( $reference === 'invoices' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'invoices', $user_identity); ?>"><i class="fa fa-money"></i><?php pll_e('Upgrade Membership');?></a></li>
                    <?php }?>
                    <li class="green <?php echo ( $reference === 'security' ? 'active':'');?>"><a href="<?php DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'security', $user_identity); ?>"><i class="fa fa-lock"></i><?php pll_e('Change Password');?></a></li>
                    
					<?php if ( is_user_logged_in() ) {?>
                        <li class="green"><a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>"><i class="fa fa-sign-in"></i><?php pll_e('Logout');?></a></li>
                    <?php }?>
                </ul>
			<?php
				}
			}
			echo ob_get_clean();
        }


/**
 *@Class headers
 *@return html
 */
if (!class_exists('kt_docdirect_headers')) {

    class kt_docdirect_headers {

        function __construct() {
            add_action('kt_docdirect_init_headers', array(&$this, 'docdirect_init_headers'));
			add_action('kt_docdirect_prepare_headers', array(&$this, 'docdirect_prepare_header'));
        }

        /**
         * @Init Header
         * @return {}
         */
        public function docdirect_init_headers() {
			$post_name	= docdirect_get_post_name();
			if(function_exists('fw_get_db_settings_option')){
				$maintenance = fw_get_db_settings_option('maintenance');
				$preloader = fw_get_db_settings_option('preloader');
                $header_type = fw_get_db_settings_option('header_type');
			} else {
				$maintenance = '';
				$preloader = '';
                $header_type = '';
			}
            
            if( isset( $header_type['gadget'] ) && $header_type['gadget'] === 'header_v2' ){
                $header_classes = 'doc-header doc-haslayout';
            } else{
                $header_classes = 'tg-haslayout tg-inner-header doc-header';
            }
			
            if ( isset($maintenance) && $maintenance == 'disable' ){
                if( isset( $preloader['gadget'] ) && $preloader['gadget'] === 'enable' ){
                    if( isset( $preloader['enable']['preloader']['gadget'] ) && $preloader['enable']['preloader']['gadget'] === 'default' ){
                        ?>
                         <div class="preloader-outer">
                              <div class="pin"></div>
                              <div class="pulse"></div>
                         </div>
                    <?php
                    } elseif( isset( $preloader['enable']['preloader']['gadget'] ) 
                             && $preloader['enable']['preloader']['gadget'] === 'custom'
                             && !empty( $preloader['enable']['preloader']['custom']['loader']['url'] )
                    ){
                        ?>
                            <div class="preloader-outer">
                                <div class="preloader-inner">
                                    <img width="100" src="<?php echo esc_url($preloader['enable']['preloader']['custom']['loader']['url']);?>" alt="<?php esc_html_e('loader','docdirect');?>" />
                                </div>
                            </div>
                        <?php
                    }
                }
            }
            ?>
			<?php get_template_part('template-parts/template','comingsoon'); ?>
            <div id="wrapper" class="tg-haslayout">
                <header id="header" class="<?php echo esc_attr( $header_classes );?>">
                     <?php do_action('kt_docdirect_prepare_headers');?>
                </header>
                <?php do_action('docdirect_prepare_subheaders');?>
             <main id="main" class="tg-page-wrapper tg-haslayout">
            <?php
		}
		
	    /**
         * @Prepare Header Data
         * @return {}
         */
        public function docdirect_prepare_header() {
            global $post, $woocommerce;

            $main_logo      = '';
            $shoping_cart   = '';
            $lang           = '';
            $res_table_title    = '';
            $res_link           = '';
            
            if (function_exists('fw_get_db_settings_option')) {
                $header_type = fw_get_db_settings_option('header_type');
                $main_logo = fw_get_db_settings_option('main_logo');
                $inner_logo = fw_get_db_settings_option('inner_logo');
                $shoping_cart = fw_get_db_settings_option('shoping_cart');
                $lang = fw_get_db_settings_option('lang');
                $registration = fw_get_db_settings_option('registration');
            }
            
            //fw_print($header_type);
            
            ob_start();

            if (isset($main_logo['url']) && !empty($main_logo['url'])) {
                $logo = $main_logo['url'];
            } else {
                $logo = get_template_directory_uri() . '/images/logo.png';
            }
            
            if( isset( $header_type['gadget'] ) && $header_type['gadget'] === 'header_v2' ){
            ?>
            <div class="doc-topbar doc-haslayout">
              <div class="container">
                <div class="row">
                  <div class="col-sm-12"> 
                    <?php if( !empty( $header_type['header_v2']['contact_info'] ) ){?>
                        <span class="doc-contactweb"><?php echo do_shortcode( $header_type['header_v2']['contact_info'] );?></span>
                    <?php }?>
                    <?php 
                    if( !empty( $header_type['header_v2']['social_icons'] ) 
                       ||
                       ( !empty( $header_type['header_v2']['multilingual'] ) && $header_type['header_v2']['multilingual'] === 'enable' )
                    ){?>
                        <div class="doc-languages">
                            <?php 
                                if( !empty( $header_type['header_v2']['multilingual'] ) && $header_type['header_v2']['multilingual'] === 'enable' ){
                                    do_action('wpml_add_language_selector');
                                }
                            ?>
                            <?php if( !empty( $header_type['header_v2']['social_icons'] ) ){?>
                                <ul class="tg-socialicon">
                                    <?php 
                                        $social_icons   = $header_type['header_v2']['social_icons'];
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
                        </div>
                    <?php }?>
                    <?php
                        if ( has_nav_menu( 'top-menu' ) ) {
                             wp_nav_menu( array( 'theme_location' => 'top-menu', 'container_class' => 'top_menu' ) );
                        }
                        
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="container">
              <div class="row">
                <div class="col-xs-12"> 
                  <strong class="doc-logo"><?php $this->docdirect_prepare_logo($logo,'','');?></strong>
                  <div class="doc-navigationarea">
                    <nav id="doc-nav" class="doc-nav">
                      <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#doc-navigation1" aria-expanded="false">
                            <span class="sr-only"><?php esc_html_e('Toggle navigation','docdirect');?></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                      </div>
                      <div class="doc-navigation collapse navbar-collapse" id="doc-navigation">
                         <?php $this->docdirect_prepare_navigation('main-menu', '', '', '0'); ?>
                      </div>
                    </nav>
                    <div class="doc-admin"><?php $this->docdirect_prepare_registration_v2();?></div>            
                  </div>
                </div>
              </div>
            </div>
            <?php    
            } else{
            ?>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="tg-navigationarea">
                            <strong class="logo"><?php $this->docdirect_prepare_logo($logo,'','');?></strong>
                            <nav id="tg-nav" class="tg-nav">
                                <div class="navbar-header">
                                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#tg-navigation" aria-expanded="false">
                                        <span class="sr-only"><?php esc_html_e('Toggle navigation','docdirect');?></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                </div>
                                <div class="collapse navbar-collapse" id="tg-navigation">
                                    <?php $this->docdirect_prepare_navigation('main-menu', '', '', '0'); ?>
                                </div>
                            </nav>
                            <div class="doc-menu">
                                <?php $this->docdirect_prepare_registration();?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            
            echo ob_get_clean();
        }
		
		/**
         * @Main Logo
         * @return {}
         */
        public function docdirect_prepare_registration() {
			global $current_user, $wp_roles,$userdata,$post;
			
			$enable_resgistration = '';
			$enable_login = '';
			
			if (function_exists('fw_get_db_settings_option')) {
				$enable_resgistration = fw_get_db_settings_option('registration');
				$enable_login = fw_get_db_settings_option('enable_login');
			}
			ob_start();
			$dir_obj	= new DocDirect_Scripts();
			
			if( $enable_login === 'enable' || $enable_resgistration === 'enable' ) {
			?>
			<ul class="tg-login-logout">
				<?php if( is_user_logged_in() ) {
						$user_identity	= $current_user->ID;
						$avatar = apply_filters(
							'docdirect_get_user_avatar_filter',
							 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_identity) //size width,height
						);
						
						$first_name   		   = get_user_meta( $user_identity, 'first_name', true);
						$last_name   		    = get_user_meta( $user_identity, 'last_name', true);
						$display_name   		 = get_user_meta( $user_identity, 'display_name', true);
						
						if( !empty( $first_name ) ){
							$username	= $first_name;
						} else if( !empty( $last_name ) ){
							$username	= $last_name;
						} else{
							$username	= $display_name;
						}
					?>
					<li class="session-user-info"><a href="javascript:;"><span class="s-user"><?php echo esc_attr( $username );?></span><img alt="<?php pll_e('Welcome');?>" src="<?php echo esc_url( $avatar );?>"></a><?php kt_docdirect_profile_menu('menu');?></li>
					<?php } else {?>
                    <li class="session-user-info">
                        <a href="javascript:;" data-toggle="modal" data-target=".tg-user-modal"><span class="s-user"><?php pll_e('Login/Register');?></span><img alt="<?php pll_e('Login');?>" src="<?php echo get_template_directory_uri() . '/images/singin_icon.png';?>"></a>
                        <span><a href="javascript:;" data-toggle="modal" data-target=".tg-user-modal"></a></span>
                     </li>
				<?php }?>
			</ul>
			<?php 
			}
			echo ob_get_clean();
		}
        /**
         * @Main Logo
         * @return {}
         */
        public function docdirect_prepare_registration_v2() {
            global $current_user, $wp_roles,$userdata,$post;
            
            $enable_resgistration = '';
            $enable_login = '';
            
            if (function_exists('fw_get_db_settings_option')) {
                $enable_resgistration = fw_get_db_settings_option('registration');
                $enable_login = fw_get_db_settings_option('enable_login');
            }
            ob_start();
            $dir_obj    = new DocDirect_Scripts();
            
            if( $enable_login === 'enable' || $enable_resgistration === 'enable' ) {
                     if( is_user_logged_in() ) {
                        $user_identity  = $current_user->ID;
                        $avatar = apply_filters(
                            'docdirect_get_user_avatar_filter',
                             docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_identity) //size width,height
                        );
                        
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
                    ?>
                    <div class="doc-user">
                        <div class="doc-dropdown">
                            <figure class="doc-adminpic">
                                <a href="javascript:;"><img src="<?php echo esc_url( $avatar );?>" alt="<?php esc_html_e('Welcome','docdirect');?>"></a>
                                <?php
                                    $var = kt_get_number_booking($current_user->ID);
                                    if ( $var > 0 ) {
                                        echo '<span class="number_booking">'.$var.'</span>';
                                    }
                                ?>
                            </figure>
                            <a href="javascript:;" class="doc-usermenu doc-btndropdown">
                                <em><?php echo esc_attr( $username );?></em>
                            </a>
                            <!-- <div class="dropdown-menu doc-dropdownbox doc-usermenu">
                                <?php //kt_docdirect_profile_menu('menu');?>
                            </div> -->
                       </div>
                   </div>
                 <?php } else {?>
                        <a class="doc-btn" href="javascript:;" data-toggle="modal" data-target=".tg-user-modal"><?php esc_html_e('Login/Sign up','docdirect');?></a>
                <?php }?>
            <?php 
            }
            echo ob_get_clean();
        }
        
		
		/**
         * @Woo
         * @return {}
         */
        public function docdirect_shoping_cart($enable_woo='') {
			ob_start();
			global $woocommerce;
			?>
			<?php if (function_exists('is_woocommerce') && $enable_woo === 'enable') { ?>
				<div class="tg-minicart">
					<span class="cart-contents">
						<a id="tg-minicart-button" href="javascript:;">
							<i class="fa fa-cart-plus"></i>
							<span class="tg-badge"><?php echo intval($woocommerce->cart->cart_contents_count); ?></span>
						</a>
					</span>
					<div class="tg-minicart-box">
						<div class="widget_shopping_cart_content"></div>
					</div>
				</div>
			<?php } ?> 
			<?php 
			echo ob_get_clean();
		}
		
		/**
         * @Main Logo
         * @return {}
         */
        public function docdirect_prepare_logo($logo_url='',$image_classes='',$link_classes='') {
			ob_start();
			?>
			<a class="<?php echo esc_attr( $link_classes );?>" href="<?php echo esc_url(home_url('/')); ?>"><img class="<?php echo esc_attr( $image_classes );?>" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo()); ?>"></a>
			<?php 
			echo ob_get_clean();
		}
		
        /**
         * @Main Navigation
         * @return {}
         */
        public static function docdirect_prepare_navigation($location = '', $id = 'menus', $class = '', $depth = '0') {

		   if ( has_nav_menu($location) ) {
                $defaults = array(
                    'theme_location' => "$location",
                    'menu' => '',
                    'container' => '',
                    'container_class' => '',
                    'container_id' => '',
                    'menu_class' => "$class",
                    'menu_id' => "$id",
                    'echo' => false,
                    'fallback_cb' => 'wp_page_menu',
                    'before' => '',
                    'after' => '',
                    'link_before' => '',
                    'link_after' => '',
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth' => "$depth",
                    //'walker' => new Doctor Directory_Menu_Walker
				);
                echo do_shortcode(wp_nav_menu($defaults));
            } else {
                $defaults = array(
                    'theme_location' => "",
                    'menu' => '',
                    'container' => '',
                    'container_class' => '',
                    'container_id' => '',
                    'menu_class' => "$class",
                    'menu_id' => "$id",
                    'echo' => false,
                    'fallback_cb' => 'wp_page_menu',
                    'before' => '',
                    'after' => '',
                    'link_before' => '',
                    'link_after' => '',
                    'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth' => "$depth",
                    'walker' => '');
                echo do_shortcode(wp_nav_menu($defaults));
            }
        }

    }

    new kt_docdirect_headers();
}


