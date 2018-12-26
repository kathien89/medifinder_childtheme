

                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 doc-verticalaligntop temp_profile">
                              <div class="doc-featurelist" class="user-<?php echo intval( $user->ID );?>">
                              		<span class="name"><?php echo $get_username;?></span>
                                    <?php if( !empty( $directory_type ) ) {?>
                                        <span class="type"><?php echo get_the_title($directory_type);?></span>
                                    <?php }?>
                                    <?php if( !empty( $user->tagline ) ) {?>
                                        <span class="tagline"><?php echo esc_attr( $user->tagline );?></span>
                                    <?php }?>
                                    <div class="doc-featurecontent">
                                        <?php //kt_get_tag_company($user->ID);?>
                                      <ul class="doc-addressinfo">
                                        <?php if( !empty( $directories_array['address'] ) ) {?>
                                        <li> <i class="fa fa-usd"></i>
                                            <span class="fee"><?php pll_e('Consultation Price: ')?><span>$<?php echo get_user_meta($user->ID,'price_min',true);?></span></span>
                                        </li>
                                        <li> <i class="fa fa-map-marker"></i>
                                          <address><?php echo esc_attr( $directories_array['address'] );?></address>
                                        </li>
                                        <?php }?>
                                        <?php if( !empty( $directories_array['phone_number'] ) 
                                                  &&
                                                    !empty( $privacy['phone'] )
                                                  &&
                                                    $privacy['phone'] == 'on'
                                            ) {?>
                                            <li><i class="fa fa-phone"></i><span><?php echo esc_attr( $directories_array['phone_number'] );?></span></li>
                                        <?php }?>
                                        <?php if( !empty( $business_email ) 
                                                  &&
                                                    !empty( $privacy['email'] )
                                                  &&
                                                    $privacy['email'] == 'on'
                                            ) {?>
                                            <li><i class="fa fa-envelope-o"></i><a href="mailto:<?php echo esc_attr( $business_email);?>?subject:<?php pll_e('Hello','docdirect');?>"><?php echo esc_attr( $business_email);?></a></li>
                                        <?php }?>
                                        <?php if( !empty( $directories_array['fax'] ) ) {?>
                                            <li><i class="fa fa-fax"></i><span><?php echo esc_attr( $directories_array['fax']);?></span></li>
                                        <?php }?>
                                        <?php if( !empty( $directories_array['mtr_exit'] ) ) {?>
                                            <li class="mtr_exit"><img src="<?php echo get_stylesheet_directory_uri();?>/images/mtr_hong_kong_logo.svg" width="16" height="16"><span><?php echo esc_attr( $directories_array['mtr_exit']);?></span></li>
                                        <?php }?>
                                        <?php
                                        if( !empty( $u_latitude ) && !empty( $u_longitude ) ){
                                            if( !empty( $_GET['geo_location'] ) ) {
                                                $args = array(
                                                    'timeout'     => 15,
                                                    'headers' => array('Accept-Encoding' => ''),
                                                    'sslverify' => false
                                                );
                                                $address   = !empty($_GET['geo_location']) ? $_GET['geo_location'] : '';
                                                $prepAddr  = str_replace(' ','+',$address);
                                                $url   = 'http://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false';
                                                $response   = wp_remote_get( $url, $args );
                                                $geocode  = wp_remote_retrieve_body($response);
                                                $output   = json_decode($geocode);
                                                if( isset( $output->results ) && !empty( $output->results ) ) {
                                                    $Latitude = $output->results[0]->geometry->location->lat;
                                                    $Longitude  = $output->results[0]->geometry->location->lng;
                                                    $distance = docdirectGetDistanceBetweenPoints($Latitude,$Longitude,$u_latitude,$u_longitude,'Km');
                                                }
                                            }
                                            ?>
                                            <?php if( !empty( $distance ) ) {?>
                                                <li class="dynamic-locations"><i class='fa fa-globe'></i><span><?php pll_e('within','docdirect');?>&nbsp;<?php echo esc_attr($distance);?></span></li>
                                            <?php } else{?>
                                                <li class="dynamic-location-<?php echo intval($user->ID);?>"></li>
                                                <?php
                                                        wp_add_inline_script( 'docdirect_functions', 'if ( window.navigator.geolocation ) {
                                                            window.navigator.geolocation.getCurrentPosition(
                                                                function(pos) {
                                                                    jQuery.cookie("geo_location", pos.coords.latitude+"|"+pos.coords.longitude, { expires : 365 });
                                                                    var with_in = _get_distance(pos.coords.latitude, pos.coords.longitude, '.esc_js($u_latitude).','. esc_js($u_longitude).',"K");
                                                                    jQuery(".dynamic-location-'.intval($user->ID).'").html("<i class=\'fa fa-globe\'></i><span>"+scripts_vars.with_in+_get_round(with_in, 2)+scripts_vars.kilometer+"</i></span>");
                                                                }
                                                            );
                                                        }
                                                    ' );
                                                    }
                                                ?>
                                        <?php }?>
                                        <div class="wrap_tag"><span class="company_tag"><?php pll_e('Listing');?></span></div>
                                      </ul>
                                    </div>
                                    <?php if($thumb_id != false){
                                    $avatar_full = docdirect_get_image_source($thumb_id,full,full);
                                    ?>
                                    <div class="doc-comlogo" style="background-image: url(<?php echo $avatar_full;?>)">
                                    </div>
                                  <?php }?>
                                </div>
                            </div>