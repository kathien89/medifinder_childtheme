<?php
/**
 * User Favorites
 * return html
 */

global $current_user,$paged;


?>

<div class="tg-listingarea">
  <div class="tg-listing">
  	<div class="tg-heading-border tg-small">
	  	<h3><i class="fa fa-heart" style="margin-right: 10px;"></i><?php pll_e('My Favorites');?></h3>
	  </div>
    <div class="tg-lists tg-favorites list_user">
     <?php 
	 	
    $today = time();
		$limit = get_option('posts_per_page');
		if (empty($paged)) $paged = 1;
		$offset = ($paged - 1) * $limit;
		$wishlist    = get_user_meta($current_user->ID,'wishlist', true);
		$wishlist    = !empty($wishlist) && is_array( $wishlist ) ? $wishlist : array();
		
		$total_users = (int)count($wishlist); //Total Users
		
						
		$query_args	= array(
								'role'  => 'professional',
								'order' => 'DESC',
								'orderby' => 'ID',
								'include' => $wishlist
							 );
		
		$query_args['number']	= $limit;
		$query_args['offset']	= $offset;
										 
		$user_query  = new WP_User_Query($query_args);
		if ( ! empty( $wishlist ) ) {
			if ( ! empty( $user_query->results ) ) {
			  foreach ( $user_query->results as $user ) {
			  
			  $directories_array['name']	 	 = $user->first_name.' '.$user->last_name;
			  $directory_type	= $user->directory_type;
			  $avatar = apply_filters(
									'docdirect_get_user_avatar_filter',
									 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
									 array('width'=>150,'height'=>150) //size width,height
								);
                                $review_data    = kt_docdirect_get_everage_rating ( $user->ID );
                                $name = kt_get_title_name($user->ID).$user->first_name.' '.$user->last_name;
                                $link = get_author_posts_url($user->ID);
                                $tagline = get_user_meta($user->ID, 'tagline', true);
                                $specialities = get_user_meta($user->ID,'user_profile_specialities',true);
                                if (!empty($specialities)) {
                                    $aloha = array_slice($specialities, 0, 4);
                                    $specialities_val = implode(', ', array_values($aloha));
                                }
                                $rating = docdirect_get_rating_stars($review_data,'return', 'hide');
        $user_featured = get_user_meta($user->ID, 'user_featured', true);
        if($user_featured >= $today) {
			  ?>
			  
				<article id="wishlist-<?php echo intval( $user->ID );?>" data-post_id="<?php echo intval( $post_id );?>" class="col-sm-12 col-md-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
                                <div class="row">
                                    <div class="col-sm-4">
        							  <div class="button_gr">
              						  	<a class="aff_btn kt_remove-wishlist" data-wl_id="<?php echo intval($user->ID); ?>" href="javascript:;"><i class="fa fa-times"></i></a>
              						  </div>
                                        <a href="<?php echo $link;?>" <?php echo $data_modal;?> class="list-avatar <?php echo $cl;?>">                                        
                                            <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>">       
                                            <span class="member_tag member">    
	                                            <?php kt_get_tag_company($user->ID);?>
	                                        </span>
                                        </a>
                                    </div>
                                    <div class="col-sm-8">
                                      
                                      <div class="tg-small">
                                        <h4><a href="<?php echo $link; ?>"><?php echo $name;?></a></h4>
                                      </div>
                                      <?php if( !empty( $tagline ) ){?>
                                          <div class="tg-tagline">
                                            <h5><?php echo $tagline;?></h5>
                                          </div>
                                      <?php }?>
                                      <?php if( !empty( $specialities_val ) ){?>
                                          <div class="tg-specialities">
                                            <p><strong><?php pll_e('Specialities: ');?></strong>
                                                <?php
                                                  echo $specialities_val;
                                                ?>
                                            </p>
                                          </div>
                                      <?php }?>
                                      <?php echo $rating;?>
                                    </div>
                                </div>             
                            </article>
		   <?php }
      }} else{?>
				<div class="tg-list"><p><?php pll_e('Nothing found.'); ?></p></div>
		  <?php }?>
         <?php } else{?>
            <div class="tg-list"><p><?php pll_e('Nothing found.'); ?></p></div>
      <?php }?>
    </div>
    <?php 
	//Pagination
	if( $total_users > $limit ) {?>
	  <div class="tg-btnarea">
			<?php docdirect_prepare_pagination($total_users,$limit);?>
	  </div>
	<?php }?>
  </div>
</div>
