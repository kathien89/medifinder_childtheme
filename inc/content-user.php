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
        <div class="button_gr">
        <?php

          global $current_user;
          $user_identity  = $current_user->ID;
          $list_request = array_keys(kt_getlist_affiliations($current_user->ID, 'pending', ''));

          
              $title_aff_myplacework = get_user_meta($user_identity, 'aff_myplacework', true);
              $title_aff_myplacework = ($title_aff_myplacework == '') ? pll__('My Place of work') : $title_aff_myplacework ;
              $title_aff_myteam = get_user_meta($user_identity, 'aff_myteam', true);
              $title_aff_myteam = ($title_aff_myteam == '') ? pll__('My Team') : $title_aff_myteam ;
              $title_aff_other = get_user_meta($user_identity, 'aff_other', true);
              $title_aff_other = ($title_aff_other == '') ? pll__('Other Connections') : $title_aff_other ;
                $arr_group = array(
                  'aff_myplacework' => $title_aff_myplacework,
                  'aff_myteam' => $title_aff_myteam,
                  'aff_other' => $title_aff_other,
                );

          if (in_array( $user->ID , $list_request)) {
            ?>
              <a class="aff_btn" href="javascript:;"><i class="fa fa-check-square"></i></a>
            <?php
          }else {
            ?>
            <div class="dropdown">
              <a class="aff_btn dropdown-toggle" data-toggle="dropdown" href="javascript:;"><i class="fa fa-plus-square"></i></a>
              <ul class="dropdown-menu">
                <?php
                  foreach ($arr_group as $key => $value) {
                    ?>
                      <li><a class="request_aff" data-group="<?php echo $key;?>" href="javascript:;"><?php echo $value;?></a></li>
                    <?php
                  }
                ?>
              </ul>
            </div>
              <!-- <a class="aff_btn request_aff" href="javascript:;"><i class="fa fa-plus-square"></i></a> -->
            <?php  
          }
        ?>
        </div>
    </div>             
</article>