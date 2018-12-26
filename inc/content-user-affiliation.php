<?php
                            $type_aff = get_post_meta($post_id, 'type_aff', true);
                            $user_from = get_post_meta($post_id, 'user_from', true);
                            $user_to = get_post_meta($post_id, 'user_to', true);
                            if ($type_aff == 'in_db') {
                                // $user_id = $user_to; 
                                $user = get_userdata($user_id);
                                $review_data    = kt_docdirect_get_everage_rating ( $user->ID );
                                $name = $user->first_name.' '.$user->last_name;
                                $thumbnail = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                                $link = get_author_posts_url($user->ID);
                                $tagline = get_user_meta($user->ID, 'tagline', true);
                                $specialities = get_user_meta($user->ID,'user_profile_specialities',true);
                                if (!empty($specialities)) {
                                    $aloha = array_slice($specialities, 0, 4);
                                    $specialities_val = implode(', ', array_values($aloha));
                                }
                                $rating = docdirect_get_rating_stars($review_data,'return', 'hide');
                            }else {
                                $user_id = $user_from;
                                $width = '150';
                                $height = '150';
                                $thumbnail  = docdirect_prepare_thumbnail($post_id ,$width,$height);
                                $link = 'javascript:;';
                                $name = get_the_title($post_id);
                                $tagline = get_post_meta($post_id, 'tagline', true);
                                $specialities_val = get_post_meta($post_id, 'specialties', true);
                                $rating = '';
                            }
                            // var_dump($user_id);
                            ?>
                            <article id="user-<?php echo intval( $user->ID );?>" data-post_id="<?php echo intval( $post_id );?>" class="col-sm-6 tg-doctor-profile1 user-<?php echo intval( $user->ID );?>">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <a href="<?php echo $link;?>" data-post_id="12<?php echo intval( $post_id );?>" class="list-avatar">                                        
                                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>">
                                            <?php
                                            if (get_post_meta($post_id, 'type_aff', true) == 'in_db') {
                                                echo '<span class="member_tag">'.pll__('Member').'</span>';
                                            }
                                            ?>
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
                            <?php