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


//Verify user
$meta_query_args[] = array(
						'key'     => 'verify_user',
						'value'   => 'on',
						'compare' => '='
					);
$meta_query_args[] = array(
						'key'     => 'user_featured',
						'value'   => $today,
						'type' => 'numeric',
						'compare' => '>'
					);

if( !empty( $meta_query_args ) ) {
	$query_relation = array('relation' => 'AND',);
	$meta_query_args	= array_merge( $query_relation,$meta_query_args );
	$query_args['meta_query'] = $meta_query_args;
}
																					
$query_args['meta_key']	  = 'user_featured';
$query_args['orderby']	   = 'meta_value';	
$user_query  = new WP_User_Query($query_args);
			
?>
<div class="sc-featured-users">
	<?php if ( !empty($atts['heading']) || !empty($atts['description'])) { ?>
        <div class="col-sm-8 col-sm-offset-2 col-xs-12">
            <div class="tg-section-head tg-haslayout">
                <?php if (!empty($atts['heading'])) { ?>
                    <div class="tg-section-heading tg-haslayout">
                        <h2><?php echo esc_attr($atts['heading']); ?></h2>
                    </div>
                <?php } ?>
                <?php if (!empty($atts['description'])) { ?>
                    <div class="tg-description tg-haslayout">
                        <p><?php echo esc_attr($atts['description']); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>	
    
    <?php
		if ( ! empty( $user_query->results ) ) {
		?>
       <div id="tg-featuredlist-<?php echo esc_attr( $uniq_flag );?>" class="tg-featuredlist-slider tg-featuredlist-slider-v2 tg-haslayout">
		<?php
            foreach ( $user_query->results as $user ) {

				$directory_type = get_user_meta( $user->ID, 'directory_type', true);
				$avatar = apply_filters(
							'docdirect_get_user_avatar_filter',
							 docdirect_get_user_avatar(array('width'=>275,'height'=>191), $user->ID),
							 array('width'=>275,'height'=>191) //size width,height
						);
					
				$first_name   		   = get_user_meta( $user->ID, 'first_name', true);
				$last_name   		    = get_user_meta( $user->ID, 'last_name', true);
				$display_name   		 = get_user_meta( $user->ID, 'display_name', true);
				
				if( !empty( $first_name ) || !empty( $last_name ) ){
					$username	= $first_name.' '.$last_name;
				} else{
					$username	= $display_name;
				}
				
				$featured_string	= $user->user_featured;
				$current_date 	  = date('Y-m-d H:i:s');
				$current_string	= strtotime( $current_date );
				$data	= kt_docdirect_get_everage_rating ( $user->ID );
				
				$charlength = '16';
				
				if (strlen($username) > $charlength) {
					if ($charlength > 0) {
						$username = substr($username, 0, $charlength).'...';
					} else {
						$username = $username;
					}
				}else{
					$username = $username;
				}
								
				?>
                <div class="item">
                    <figure>
                        <a href="<?php echo get_author_posts_url($user->ID); ?>"><img src="<?php echo esc_url( $avatar );?>" alt="<?php esc_html_e('User');?>"></a>
                        <?php docdirect_get_wishlist_button($user->ID,true);?>
						<?php if( isset( $featured_string ) && $featured_string > $current_string ){?>
							<?php kt_docdirect_get_featured_tag(true);?>
                        <?php }?>
                        <?php docdirect_get_verified_tag(true,$user->ID);?>
                    </figure>
                    <div class="tg-contentbox">
                        <h3><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo esc_attr( $username );?></a></h3>

                      	<?php if( !empty( get_user_meta($user->ID,'tagline',true) ) ){?>
                            <p><?php echo get_user_meta($user->ID,'tagline',true);?></p>
                      	<?php }?>

                        <div class="feature-rating">
                            <span class="tg-stars star-rating">
                                <span style="width:<?php echo esc_attr( $data['percentage'] );?>%"></span>
                            </span>
                        </div>

	                    <span class="fee">$<?php echo get_user_meta($user->ID,'price_min',true); ?></span>
                        <?php 
							if( !empty( $user->address ) ) {
								$charlength = '35';
								if (strlen($user->address) > $charlength) {
									if ($charlength > 0) {
										$address = substr($user->address, 0, $charlength).'...';
									} else {
										$address = $user->address;
									}
								}else{
									$address = $user->address;
								}
							?>
                            <address><?php echo esc_attr( $address );?></address>
                        <?php }?>
                        <?php if( !empty( $user->phone_number ) ) {?>
                           <div class="tg-phone"><i class="fa fa-phone"></i> <em><?php echo esc_attr( $user->phone_number );?></em></div>
                        <?php }?>
                    </div>
                </div>
                <?php
			}
			?>
      </div>
      <script>
	  	jQuery(document).ready(function(e) {
            jQuery("#tg-featuredlist-<?php echo esc_js( $uniq_flag );?>").owlCarousel({
				autoPlay: false,
				items: 4,
				itemsDesktop: [1199, 3],
				itemsDesktopSmall: [991, 2],
				itemsTabletSmall: [568, 1],
				slideSpeed: 300,
				paginationSpeed: 400,
				pagination: false,
				navigation: true,
				navigationText: [
					"<i class='tg-prev fa fa-angle-left'></i>",
					"<i class='tg-next fa fa-angle-right'></i>"
				]
			});
        });
	  </script>
	  <?php
      } else{
		  DoctorDirectory_NotificationsHelper::informations(pll__('No users Found.'));
      }
	  ?>
</div>				
