<?php
/**
 * User Affiliation 
 */

global $current_user, $wp_roles,$userdata,$post;
$dir_obj	= new DocDirect_Scripts();
$user_identity	= $current_user->ID;
$url_identity	= $user_identity;
if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
	$url_identity	= $_GET['identity'];
}
$delete_account_text 		= fw_get_db_settings_option('delete_account_text');
$profile_status = get_user_meta($url_identity , 'profile_status' , true);

    $today = time();
?>
<?php
$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
    $terms = get_the_terms($db_directory_type, 'group_label');
    $current_group_label_slug = $terms[0]->slug;
    $list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
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
    // var_dump($current_option);
?>
<?php if($current_option['affiliations'] != ''){?>
<div class="tg-myaccount tg-haslayout">
	<div class="tg-affiliation tg-haslayout">
		<div class="tg-editprofile tg-affiliation tg-haslayout">
			<div class="tg-otherphotos">
				<div class="tg-heading-border tg-small">				
					<h2><?php pll_e( 'Affiliation' );?></h2>
				</div>
			
				<p>
					<?php pll_e('Add any partners or affiliations you may have to your profile. Patients will be able see connections you have and link to their profile. Search our database or invite a professional to join and build your connections. Once you have added a new affiliation below, a confirmation will be sent the user before becoming visible on your profile. In the pending requests section you can approve or decline a connection.');?>
				</p>
              	
              	<a class="btn btn-success btn-lg btn-invite" href="javascript:;" data-toggle="modal" data-target=".tg-invite-modal"><i class="fa fa-envelope"></i> <?php pll_e('Invite professional to sign up');?></a>
			
			</div>
		</div>
		<div class="tg-editprofile tg-haslayout">
			<div class="tg-otherphotos">
				<div class="tg-heading-border tg-small">				
					<h2><?php pll_e( 'ADD PROFILE CONNECTIONS' );?></h2>
				</div>

				
				<ul class="nav nav-pills">
				  <li class="active"><a data-toggle="tab" href="#affsearch"><?php pll_e( 'Search MediFinder' );?></a></li>
				  <li><a data-toggle="tab" href="#tabadd"><?php pll_e( 'Add Custom' );?></a></li>
				</ul>

				<div class="tab-content">
				  <div id="affsearch" class="tab-pane fade in active">
				    <h3><i class="fa fa-user-plus"></i><?php pll_e( 'Search MediFinder' );?></h3>
				    
					<form id="search_user" class="" method="POST" action="" enctype="multipart/form-data">

						<div class="row">

						    <div class="form-group col-sm-8">
						    	<div class="row">
						    		<div class="col-sm-8">
						        		<input type="text" name="search_string" id="search_string" class="form-control"  placeholder="<?php pll_e( 'Search user' ) ?>">
						    		</div>
						    		<div class="col-sm-4 pull_right">
						        		<input type="button" name="submit" class="btn btn-primary search_user_btn"  value="<?php pll_e( 'Search' ) ?>">
						    		</div>
						    		<div class="response col-sm-12">

						    		</div>
						    	</div>
						    </div>

						</div>

						<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

					</form>
				  </div>
				  <div id="tabadd" class="tab-pane fade">				
					<h3><i class="fa fa-plus-circle"></i><?php pll_e( 'Add Custom Connections' );?></h3>
					<form id="submit_aff" class="" method="POST" action="" enctype="multipart/form-data">

					    <div class="form-group">
					    	<div class="row">
						    	<div class="col-sm-6">
							        <label for="post_title"><?php pll_e( 'Name' ); ?></label>
							        <input type="text" name="post_title" id="post_title" class="form-control" value="">
						        </div>
						    	<div class="col-sm-6">
							        <label for="post_tagline"><?php pll_e( 'Tagline' ); ?></label>
							        <input type="text" name="tagline" id="tagline" class="form-control" value="">
						        </div>
					        </div>
					    </div>
					    <div class="form-group">
					    	<div class="row">
						    	<div class="col-sm-6">
							        <label for="post_email"><?php pll_e( 'Email' ); ?></label>
							        <input type="text" name="email" id="post_email" class="form-control" value="">
						        </div>
					        </div>
					    </div>
					    <div class="form-group">
					        <label for="post_title"><?php pll_e( 'Specialties' ); ?></label>
					        <textarea name="specialties" id="specialties" class="form-control"></textarea>
					    </div>
					    <div class="form-group">
					        <label for="post_title"><?php pll_e( 'Group' ); ?></label>
					        <?php
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
					  		?>
					        <select name="group_aff" class="doc-select">
					        	<option value="">Select</option>
					        	<?php
				                  foreach ($arr_group as $key => $value) {
				                    ?>
					        			<option value="<?php echo $key;?>"><?php echo $value;?></option>
				                    <?php
				                  }
				                ?>
					        </select>
					    </div>
						<div class="form-group">
							<label for="post_featured_image"><?php pll_e( 'Image' ); ?></label>
							<input type="hidden" name="post_featured_image" id="post_featured_image" class="form-control" value="">
							<div class="clearfix"></div>
	                        <figure class="tg-docimg">
		                        <?php
									$url_image = get_template_directory_uri().'/images/user150x150.jpg';
									$img .= '<img src="'.$url_image.'" />';
								?>
	                            <span class="user-avatar featured-image-wrap"><?php echo $img;?></span>
	                            <a href="javascript:;" id="upload-profile-avatar1" class="tg-uploadimg upload-avatar featured-image"><i class="fa fa-upload"></i></a> 
	                            <div id="plupload-container"></div>
	                        </figure>
							<div class="clearfix"></div>
						</div>
						<div class="form-group">

						<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>					
						<input type="submit" class="btn btn-primary post-form" value="<?php pll_e( 'Submit' );?>" name="submit">
						</div>
					</form>
				  </div>
				</div>

				<div class="tg-heading-border tg-small">				
					<h2><?php pll_e( 'Your Sent Invitation Requests' );?></h2>
				</div>

				<div class="list_user waiting_list">
				<div class="row">
					<?php
					$list_request = kt_getlist_affiliations($user_identity, 'pending');

					if (!empty($list_request)) {
						foreach ($list_request as $user_id => $post_id) {
							$user = get_userdata($user_id);
							?>
							<?php

								$review_data	= kt_docdirect_get_everage_rating ( $user->ID );
								$directories_array['name']	 	 = $user->first_name.' '.$user->last_name;
								$avatar = apply_filters(
										'docdirect_get_user_avatar_filter',
										 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
										 array('width'=>150,'height'=>150) //size width,height
									);
									
					        $user_featured = get_user_meta($user->ID, 'user_featured', true);
					        if($user_featured >= $today) {
							?>
							<article id="user-<?php echo intval( $user->ID );?>" data-post_id="<?php echo intval( $post_id );?>" class="col-sm-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
								<div class="row">
							        <div class="col-sm-4">
							            <a href="<?php echo get_author_posts_url($user->ID); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
							            
							        </div>
							        <div class="col-sm-8">
							          
							          <div class="tg-small">
							            <h4><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo esc_attr( $directories_array['name'] );?></a></h4>
							          </div>
							          <?php if( !empty( get_user_meta($user->ID,'tagline',true) ) ){?>
							              <div class="tg-tagline">
							                <h5><?php echo get_user_meta($user->ID,'tagline',true);?></h5>
							              </div>
							          <?php }?>
							          <?php if( !empty( get_user_meta($user->ID,'user_profile_specialities',true) ) ){?>
							              <div class="tg-specialities">
							                <p><strong><?php pll_e('Specialities: ');?></strong>
							                    <?php
							                      $aloha = get_user_meta($user->ID,'user_profile_specialities',true);
							                      $aloha = array_slice($aloha, 0, 4);
							                      echo implode(', ', array_values($aloha));
							                     ?>
							                </p>
							              </div>
							          <?php }?>
							          <?php docdirect_get_rating_stars($review_data,'echo', 'hide');?>

        							  <div class="button_gr">
              						  	<a class="aff_btn remove_request_aff" href="javascript:;"><i class="fa fa-times"></i></a>
              						  </div>
							        </div>
							    </div>             
							</article>
							<?php
							}
						}
					}
					?>
				</div>
				</div>
			</div>
		</div>
		<div class="tg-editprofile tg-haslayout">
			<div class="tg-otherphotos">
			<div class="tg-heading-border tg-small">				
				<h2>Pending Requests</h2>
			</div>
			
			<div class="list_user">

				<?php
					$request_affiliation = kt_getlist_affiliations_request($user_identity);

					if (!empty($request_affiliation)) {
						foreach ($request_affiliation as $user_id => $post_id) {
							if(user_id_exists($user_id)){ 
							$user = get_userdata($user_id);
							?>
							<?php

								$review_data	= kt_docdirect_get_everage_rating ( $user->ID );
								$directories_array['name']	 	 = kt_get_title_name($user->ID).$user->first_name.' '.$user->last_name;
								$avatar = apply_filters(
										'docdirect_get_user_avatar_filter',
										 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
										 array('width'=>150,'height'=>150) //size width,height
									);
									
					        $user_featured = get_user_meta($user->ID, 'user_featured', true);
					        if($user_featured >= $today) {
							?>
							<article id="user-<?php echo intval( $user->ID );?>" data-post_id="<?php echo intval( $post_id );?>" class="col-sm-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
								<div class="row">
							        <div class="col-sm-4">
							            <a href="<?php echo get_author_posts_url($user->ID); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
							            
							        </div>
							        <div class="col-sm-8">
							          
							          <div class="tg-small">
							            <h4><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo esc_attr( $directories_array['name'] );?></a></h4>
							          </div>
							          <?php if( !empty( get_user_meta($user->ID,'tagline',true) ) ){?>
							              <div class="tg-tagline">
							                <h5><?php echo get_user_meta($user->ID,'tagline',true);?></h5>
							              </div>
							          <?php }?>
							          <?php if( !empty( get_user_meta($user->ID,'user_profile_specialities',true) ) ){?>
							              <div class="tg-specialities">
							                <p><strong><?php pll_e('Specialities: ');?></strong>
							                    <?php
							                      $aloha = get_user_meta($user->ID,'user_profile_specialities',true);
							                      $aloha = array_slice($aloha, 0, 4);
							                      echo implode(', ', array_values($aloha));
							                     ?>
							                </p>
							              </div>
							          <?php }?>
							          <?php docdirect_get_rating_stars($review_data,'echo', 'hide');?>
							          <div class="clearfix"></div>
              							<a class="aff_action_btn approve_btn btn btn-default" data-action="approve" href="javascript:;">Approve</a>
              							<a class="aff_action_btn decline_btn btn btn-default" data-action="decline" href="javascript:;">Decline</a>

							        </div>
							    </div>             
							</article>
							<?php
							}
							}
						}
					}else{?>
                     	<p><?php pll_e( 'No request.' );?></p>
                  	<?php }
				?>
				</div>
			</div>
		</div>

		<?php
  		$title_aff_myplacework = get_user_meta($user_identity, 'aff_myplacework', true);
  		$title_aff_myplacework = ($title_aff_myplacework == '') ? pll__('My Place of work') : $title_aff_myplacework ;
  		$title_aff_myteam = get_user_meta($user_identity, 'aff_myteam', true);
  		$title_aff_myteam = ($title_aff_myteam == '') ? pll__('My Team') : $title_aff_myteam ;
  		$title_aff_other = get_user_meta($user_identity, 'aff_other', true);
  		$title_aff_other = ($title_aff_other == '') ? pll__('Other Connections') : $title_aff_other ;
        $arr_group = array(
                array(
                    'slug' => 'aff_myplacework',
                    'name' => $title_aff_myplacework,
                    'icon' => 'fa-hospital-o',
                ),
                array(
                    'slug' => 'aff_myteam',
                    'name' => $title_aff_myteam,
                    'icon' => 'fa-users',
                ),
                array(
                    'slug' => 'aff_other',
                    'name' => $title_aff_other,
                    'icon' => 'fa-link',
                )
            );
        foreach ($arr_group as $group) {
		?>
        	<?php
                $list_exists = array();

                $apply_affiliation1 = kt_getlist_affiliations($user_identity, 'approved', $group['slug']);
                $apply_affiliation2 = kt_getlist_affiliations_request($user_identity, 'approved', $group['slug']);
                $apply_affiliation = $apply_affiliation1 + $apply_affiliation2;
                $count1 = count($apply_affiliation);
                $args = array(
                    'post_type' => 'affiliation',
            		'posts_per_page' => -1,
                    'author' => $user_identity,
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key'     => 'group_aff',
                            'value'   => $group['slug'],
                            'compare' => '='
                        ),
                        array(
                            'key'     => 'type_aff',
                            'value'   => 'out_db',
                            'compare' => '='
                        )
                    )
                );
                $ListPost = get_posts($args);
                $count2 = count($ListPost);
                $count = $count1 + $count2;
                $cl = 'smallheight';
        	?>
        <?php if($count > 0){?>
		<div class="aff_group tg-haslayout">
			<div class="tg-otherphotos">
			<div class="tg-heading-border tg-small">				
				<h2>
                    <i class="fa <?php echo $group['icon'];?>"></i>
                    <span>
                    	<span><?php echo $group['name'];?></span>
                    	<?php //if( $group['slug']== 'aff_myplacework'){?>
                    		<a href="javascript:;"><i class="fa fa-edit"></i></a>
                    	<?php //}?>
                    </span>  
                    <?php //if( $group['slug']== 'aff_myplacework'){?>
                    <form class="form-inline" method="post">
                    	<div class="row">
	 						<div class="col-sm-8">
	                    		<input type="text" name="title_group" value="<?php echo $group['name'];?>">
	                    	</div>
	 						<div class="col-sm-4">
	                    		<input type="hidden" name="group_slug" value="<?php echo $group['slug'];?>">
	                    		<input class="btn btn-primary" type="submit" name="submit" value="Update">
	                    	</div>
                    	</div>
                    </form>
                    <?php //}?>
				</h2>
			</div>
            <?php if ($count > 2) $cl = '';?>
            <div class="list_user <?php echo $cl;?>">
			<div class="row">
					<?php
				  		$title_aff_myplacework = get_user_meta($user_identity, 'aff_myplacework', true);
				  		$title_aff_myplacework = ($title_group == '') ? pll__('My Place of work') : $title_group ;
				  		$title_aff_myteam = get_user_meta($user_identity, 'aff_myteam', true);
				  		$title_aff_myteam = ($title_group == '') ? pll__('My Team') : $title_group ;
				  		$title_aff_other = get_user_meta($user_identity, 'aff_other', true);
				  		$title_aff_other = ($title_group == '') ? pll__('Other Connections') : $title_group ;
			          $arr_group = array(
			            'aff_myplacework' => $title_aff_myplacework,
			            'aff_myteam' => $title_aff_myteam,
			            'aff_other' => $title_aff_other,
			          );
			        ?>
					<?php
					// $apply_affiliation = array_unique(array_merge($apply_affiliation1, $apply_affiliation2));

					// $apply_affiliation = get_user_meta( $user_identity, 'apply_affiliation', true );
					// $apply_affiliation = json_decode($apply_affiliation);
					if (!empty($apply_affiliation)) {
						foreach ($apply_affiliation as $user_id => $post_id) {
							$list_exists[] = $post_id;
							if(user_id_exists($user_id) && $user_id != $user_identity){ 
							$user = get_userdata($user_id);
							?>
							<?php

								$review_data	= kt_docdirect_get_everage_rating ( $user->ID );
								$directories_array['name']	 	 = kt_get_title_name($user->ID).$user->first_name.' '.$user->last_name;
								$avatar = apply_filters(
										'docdirect_get_user_avatar_filter',
										 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
										 array('width'=>150,'height'=>150) //size width,height
									);
									
					        $user_featured = get_user_meta($user->ID, 'user_featured', true);
					        if($user_featured >= $today) {
							?>
							<article id="user-<?php echo intval( $user->ID );?>" data-post_id="<?php echo intval( $post_id );?>" class="col-sm-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
								<div class="row">
							        <div class="col-sm-4">
        							  <div class="button_gr">
              						  	<a class="aff_btn remove_approve_btn" href="javascript:;"><i class="fa fa-times"></i></a>
              						  </div>
							            <a href="<?php echo get_author_posts_url($user->ID); ?>" data-post_id="12<?php echo intval( $post_id );?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>">
                                        <?php
                                            if (get_post_meta($post_id, 'type_aff', true) == 'in_db') {
                                                echo '<span class="member_tag member">'.pll__('Member').'</span>';
                                            }
                                        ?>
                                        </a>
              						  	<a class="aff_btn change_group_btn dropdown-toggle" data-toggle="dropdown" href="javascript:;"><i class="fa fa-cog"></i><?php pll_e('Edit Category');?></a>
              						  	<?php							          
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
							                ?>
								        <ul class="dropdown-menu edit_category">
							                <?php
							                  foreach ($arr_group as $key => $value) {
							                    ?>
							                      <li class="<?php echo $retVal = ($key == $group['slug']) ? 'active' : '';?>"><a class="edit_group" data-group="<?php echo $key;?>" href="javascript:;"><?php echo $value;?></a></li>
							                    <?php
							                  }
							                ?>
							            </ul>  
							            
							        </div>
							        <div class="col-sm-8">
							          
							          <div class="tg-small">
							            <h4><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo esc_attr( $directories_array['name'] );?></a></h4>
							          </div>
							          <?php if( !empty( get_user_meta($user->ID,'tagline',true) ) ){?>
							              <div class="tg-tagline">
							                <h5><?php echo get_user_meta($user->ID,'tagline',true);?></h5>
							              </div>
							          <?php }?>
							          <?php if( !empty( get_user_meta($user->ID,'user_profile_specialities',true) ) ){?>
							              <div class="tg-specialities">
							                <p><strong><?php pll_e('Specialities: ');?></strong>
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
							<?php
							}
							}
						}
					}
					?>
					<?php				
						foreach($ListPost as $post):
							if( !in_array($post->ID, $list_exists) ){
							?>
							<article id="user-<?php echo intval( $post->ID );?>" data-post_id="<?php echo intval( $post->ID );?>" class="col-sm-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
								<div class="row">
							        <div class="col-sm-4">
        							  <div class="button_gr">
              						  	<a class="aff_btn remove_approve_btn" href="javascript:;"><i class="fa fa-times"></i></a>
              						  </div>
							        	<?php
		                                	$email = get_post_meta($post->ID, 'email', true);
		                                	$data_modal = 'data-email="'.$email.'"';
                                			$cl = 'hasmodal';
		                                ?>
							        	<a class="hasmodal" href="javascript:;" <?php echo $data_modal;?>>
							            <?php 
								            $width = '150';
								            $height = '150';
								            $thumbnail	= docdirect_prepare_thumbnail($post->ID ,$width,$height);
							            	// echo get_the_post_thumbnail($post->ID, 'thummbnail');
							            ?>
                        					<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>">
                        					<?php echo '<span class="member_tag invite_tag">'.pll__('Invite').'</span>';?>
							            </a>

              						  	<a class="aff_btn change_group_btn dropdown-toggle" data-toggle="dropdown" href="javascript:;"><i class="fa fa-cog"></i><?php pll_e('Edit Category');?></a>
              						  	<?php								          
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
								        ?>
								        <ul class="dropdown-menu edit_category">
							                <?php
							                  foreach ($arr_group as $key => $value) {
							                    ?>
							                      <li class="<?php echo $retVal = ($key == $group['slug']) ? 'active' : '';?>"><a class="edit_group" data-group="<?php echo $key;?>" href="javascript:;"><?php echo $value;?></a></li>
							                    <?php
							                  }
							                ?>
							            </ul>     
							        </div>
							        <div class="col-sm-8">
							          
							          <div class="tg-small">
							            <h4><?php echo get_the_title($post->ID);?></h4>
							          </div>
							          <?php //echo get_post_meta($post->ID,'group_aff',true);?>
							          <?php if( !empty( get_post_meta($post->ID,'tagline',true) ) ){?>
							              <div class="tg-tagline">
							                <h5><?php echo get_post_meta($post->ID,'tagline',true);?></h5>
							              </div>
							          <?php }?>
							          <?php if( !empty( get_post_meta($post->ID,'specialties',true) ) ){?>
							              <div class="tg-specialities">
							                <p><strong><?php pll_e('Specialities: ');?></strong>
							                    <?php
							                      $aloha = get_post_meta($post->ID,'specialties',true);
							                      echo $aloha;
							                     ?>
							                </p>
							              </div>
							          <?php }?>
							        </div>
							    </div>             
							</article>
						<?php
							}
						endforeach;
					?>
				</div>
			</div>
			</div>
		</div>
		<?php }?>
		<?php }?>
	</div>
</div>
<?php }else {?>
<div class="tg-myaccount tg-haslayout">
	<?php kt_button_upgrade_premium();?>
</div>
<?php }?>

<?php
function kt_add_modal_footer() {
?>
<div class="modal fade tg-invite-modal">
  <div class="tg-modal-content" role="document">
	  	<div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title"><?php pll_e( 'Invite' ) ?></h4>
	    </div>
	  	<div class="tg-invite-form">
		    <form action="#" method="post" class="tg-form-modal invite-form">
		    <fieldset>
			    <div class="row">
			      	<!-- <div class="form-group col-sm-12">
					    <label for="subject"><?php pll_e( 'Subject' ) ?></label>
					    <input type="text" name="subject" class="form-control" id="subject" placeholder="<?php pll_e( 'subject' ) ?>">
					</div> -->
			      	<div class="form-group col-sm-12">
					    <label for="email"><?php pll_e( 'Email' ) ?></label>
					    <input type="email" name="email" class="form-control" id="email" placeholder="<?php pll_e( 'email' ) ?>">
					</div>
			      	<div class="form-group col-sm-12">
					    <label for="email"><?php pll_e( 'Description' ) ?></label>
					    <p><?php pll_e( 'Feel free to add your personal invitiation message to a colleague or clinic.' ) ?></p>
						<textarea name="desc" class="form-control"><?php pll_e( 'MediFinder is a booking & review platform that offers medical professionals an alternative to reach out to new patients. Promote your specialties & fill up empty schedules with our 24 hour online booking system. Patients can view your credentials, pricing, insurance affiliations & any colleagues or clinics you have connections with. We also offer all professionals a 6 months free listing! ' ) ?></textarea>
					</div>
			      	<div class="form-group col-sm-12 response">
		        	</div>
			      	<div class="form-group col-sm-12">
		        		<button type="button" class="tg-btn submit_invite"><?php pll_e('Submit');?></button>
		        	</div>
					<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

				</div>
			</fieldset>
			</form>
		</div>
  </div>
</div>

<?php
}
add_action('wp_footer', 'kt_add_modal_footer');