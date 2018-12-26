<?php
global $profileuser;
$user_identity= $profileuser->ID;
?>
        <div class="admin_aff tg-bordertop tg-haslayout">
            <div class="tg-formsection1">
                <div class="parent-heading">
                    <h2><?php esc_html_e('Affiliations','docdirect');?></h2>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        
            <div class="tg-otherphotos">      

                <h3><?php pll_e( 'ADD PROFILE CONNECTIONS' );?></h3>
                
                <ul class="nav nav-pills">
                  <li class="active"><a data-toggle="tab" href="#affsearch"><?php pll_e( 'Search MediFinder' );?></a></li>
                  <li><a data-toggle="tab" href="#tabadd"><?php pll_e( 'Add Custom' );?></a></li>
                </ul>

                <div class="tab-content">
                  <div id="affsearch" class="tab-pane fade in active">
                    <h4><i class="fa fa-user-plus"></i><?php pll_e( 'Search MediFinder' );?></h4>
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
                  <div id="tabadd" class="tab-pane fade">               
                    <h4><i class="fa fa-plus-circle"></i><?php pll_e( 'Add Custom Connections' );?></h4>

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
                                    <input type="text" name="aff_email" id="post_email" class="form-control" value="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="post_title"><?php pll_e( 'Specialties' ); ?></label>
                            <textarea name="specialties" id="specialties" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="post_title"><?php pll_e( 'Group' ); ?></label>
                            <select name="group_aff" class="doc-select">
                                <option value="">Select</option>
                                <option value="aff_myplacework"><?php pll_e( 'My Place of work' ); ?></option>
                                <option value="aff_myteam"><?php pll_e( 'My Team' ); ?></option>
                                <option value="aff_other"><?php pll_e( 'Other Connections' ); ?></option>
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
                            <a class="btn btn-primary post-form" href="javascript:;"><?php pll_e( 'Submit' );?></a>
                        </div>
                  </div>
                </div>

            </div>
            <?php
        $title_group = get_user_meta($user_identity, 'title_group', true);
        $title_group = ($title_group == '') ? pll__('My Place of work') : $title_group ;
        $arr_group = array(
                array(
                    'slug' => 'aff_myplacework',
                    'name' => $title_group,
                    'icon' => 'fa-hospital-o',
                ),
                array(
                    'slug' => 'aff_myteam',
                    'name' => pll__('My Team'),
                    'icon' => 'fa-users',
                ),
                array(
                    'slug' => 'aff_other',
                    'name' => pll__('Other Connections'),
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
                <h3>
                    <i class="fa <?php echo $group['icon'];?>"></i>
                    <span>
                        <span><?php echo $group['name'];?></span>
                    </span> 
                </h3>
            <?php if ($count > 2) $cl = '';?>
            <div class="list_user <?php echo $cl;?>">
            <div class="row">
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

                                $review_data    = kt_docdirect_get_everage_rating ( $user->ID );
                                $directories_array['name']       = kt_get_title_name($user->ID).$user->first_name.' '.$user->last_name;
                                $avatar = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user->ID),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                                    
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
                                          $title_group = get_user_meta($user_identity, 'title_group', true);
                                          $title_group = ($title_group == '') ? pll__('My Place of work') : $title_group ;
                                          $arr_group = array(
                                            'aff_myplacework' => $title_group,
                                            'aff_myteam' => pll__('My Team'),
                                            'aff_other' => pll__('Other Connections'),
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
                                            $thumbnail  = docdirect_prepare_thumbnail($post->ID ,$width,$height);
                                            // echo get_the_post_thumbnail($post->ID, 'thummbnail');
                                        ?>
                                            <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo sanitize_title(get_the_title()); ?>">
                                            <?php echo '<span class="member_tag invite_tag">'.pll__('Invite').'</span>';?>
                                        </a>

                                        <a class="aff_btn change_group_btn dropdown-toggle" data-toggle="dropdown" href="javascript:;"><i class="fa fa-cog"></i><?php pll_e('Edit Category');?></a>
                                        <?php
                                          $title_group = get_user_meta($user_identity, 'title_group', true);
                                          $title_group = ($title_group == '') ? pll__('My Place of work') : $title_group ;
                                          $arr_group = array(
                                            'aff_myplacework' => $title_group,
                                            'aff_myteam' => pll__('My Team'),
                                            'aff_other' => pll__('Other Connections'),
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
            </div>
        </div>