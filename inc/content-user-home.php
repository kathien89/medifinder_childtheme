<?php

	$review_data	= kt_docdirect_get_everage_rating ( $user->ID );
	$directories_array['name']	 	 = kt_get_title_name( $user->ID ).$user->first_name.' '.$user->last_name;
	$avatar = apply_filters(
			'docdirect_get_user_avatar_filter',
			 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
			 array('width'=>150,'height'=>150) //size width,height
		);
		
?>
<article id="user-<?php echo intval( $user->ID );?>" class="tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
	<div class="row">
        <div class="col-xs-3">
            <a href="<?php echo get_author_posts_url($user->ID); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
            
        </div>
        <div class="col-xs-9">
          
          <div class="tg-heading-border tg-small">
            <h4><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo esc_attr( $directories_array['name'] );?></a></h3>
          </div>
          <?php if( !empty( get_user_meta($user->ID,'tagline',true) ) ){?>
              <div class="tg-tagline">
                <h5><?php echo get_user_meta($user->ID,'tagline',true);?></h4>
              </div>
          <?php }?>
          <?php if( !empty( get_user_meta($user->ID,'user_profile_specialities',true) ) ){?>
              <div class="tg-specialities">
                <p><strong><?php pll_e('Specialities: ', 'docdirect');?></strong>
                    <?php
                      $aloha = get_user_meta($user->ID,'user_profile_specialities',true);
                      $aloha = array_slice($aloha, 0, 4);
                      echo implode(', ', array_values($aloha));
                     ?>
                </p>
              </div>
          <?php }?>
          <?php docdirect_get_rating_stars($review_data,'echo', 'hide');?>
        </div>
    </div>             
</article>