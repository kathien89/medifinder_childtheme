<?php
/**
 * The template for displaying all single posts.
 *
 * @package Doctor Directory
 */
docdirect_post_views(get_the_ID()); // Update Post Views
get_header();

$docdirect_sidebar = 'full';
$section_width = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
if (function_exists('fw_ext_sidebars_get_current_position')) {
    $current_position = fw_ext_sidebars_get_current_position();
    if ($current_position != 'full' && ( $current_position == 'left' || $current_position == 'right' )) {
        $docdirect_sidebar = $current_position;
        $section_width = 'col-lg-9 col-md-9 col-sm-8 col-xs-12';
    }
}

if (isset($docdirect_sidebar) && $docdirect_sidebar == 'right') {
    $aside_class = 'pull-right';
    $content_class = 'pull-left';
} else {
    $aside_class = 'pull-left';
    $content_class = 'pull-right';
}

global $current_user, $wp_roles,$userdata,$post,$paged;
$user_identity	= $current_user->ID;
docdirect_enque_rating_library();//rating

$dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
// $redirect = get_the_permalink($profile_page);
$redirect = DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'mybookings', $user_identity,true);
?>

<div class="container">
	<div class="row">
		<div id="tg-towcolumns" class="tg-haslayout">
			<div class="<?php echo esc_attr($section_width); ?> <?php echo sanitize_html_class($content_class); ?>">
				<?php
					while (have_posts()) : the_post();
						global $post;
						
						?>
						<div id="tg-content" class="tg-content tg-post-detail tg-overflowhidden <?php echo sanitize_html_class( $blogClass );?>">
							<article class="tg-post tg-haslayout">
								<div class="tg-post-data tg-haslayout">
									<div class="tg-heading-border tg-small">
										<h2><?php the_title(); ?></h2>
									</div>
									<div class="tg-description">
                                        <?php 
										$bk_status = get_post_meta($post->ID, 'bk_status',true);
										$bk_user_to = get_post_meta($post->ID, 'bk_user_to',true);
										$bk_user_from = get_post_meta($post->ID, 'bk_user_from',true);
										$user_reviews = get_post_meta($post->ID, 'user_reviews',true);
                                        if ( $bk_status != 'approved' ) {
                                        	echo "this appoitment not complete to review";
                                        }else if ( $user_reviews == 'done' ) {
                                        	echo "you did reviewed this";
                                        }else {
                                        	if ( $bk_user_from == $user_identity ) {
	                                        	?>

                <div id="leavereview" class="tg-leaveyourreview">
                  <div class="tg-userheading">
                    <h2><?php pll_e('Leave Your Review');?></h2>
                  </div>
                  <div class="message_contact  theme-notification"></div>
                  <form class="tg-formleavereview form-review">
                    <fieldset>
                      <div class="row">
                        <div class="col-sm-4">
                            <label>Recommendation</label>
                            <div class="tg-stars"><div id="jRate"></div><span class="your-rate"><strong><?php pll_e('Very likely');?></strong></span></div>
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate strong').html(rating_vars.recommendation.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="col-sm-4">
                            <label>Waiting Time</label>
                            <div class="tg-stars"><div class="jRate" id="jRate3"></div><span class="your-rate3"><strong><?php pll_e('30 mins to an hour');?></strong></span></div>
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate3").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating3').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating3').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate3 strong').html(rating_vars.waiting_time.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="col-sm-4">
                            <label>Facilities</label>
                            <div class="tg-stars"><div class="jRate" id="jRate5"></div><span class="your-rate5"><strong><?php pll_e('Satisfactory');?></strong></span></div>
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate5").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating5').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating5').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate5 strong').html(rating_vars.facilities.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-4">
                            <label>Bedside Manner</label>
                            <div class="tg-stars"><div class="jRate" id="jRate2"></div><span class="your-rate2"><strong><?php pll_e('Satisfactory');?></strong></span></div>
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate2").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating2').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating2').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate2 strong').html(rating_vars.bedside_manner.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="col-sm-4">
                            <label>Supporting Staff</label>
                            <div class="tg-stars"><div class="jRate" id="jRate4"></div><span class="your-rate4"><strong><?php pll_e('Satisfactory');?></strong></span></div>
                            <script type="text/javascript">
                                jQuery(function () {
                                    var that = this;
                                    var toolitup = jQuery("#jRate4").jRate({
                                        rating: 3,
                                        min: 0,
                                        max: 5,
                                        precision: 1,
                                        startColor: "#7dbb00",
                                        endColor: "#7dbb00",
                                        backgroundColor: "#DFDFE0",
                                        onChange: function(rating) {
                                            jQuery('.user_rating4').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_5);
                                            }
                                        },
                                        onSet: function(rating) {
                                            jQuery('.user_rating4').val(rating);
                                            if( rating == 1 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_1);
                                            } else if( rating == 2 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_2);
                                            } else if( rating == 3 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_3);
                                            } else if( rating == 4 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_4);
                                            } else if( rating == 5 ){
                                                jQuery('.your-rate4 strong').html(rating_vars.supporting_staff.rating_5);
                                            }
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-sm-6">
                          <div class="form-group">
                            <input type="text" name="user_subject" class="form-control" placeholder="<?php esc_attr_e('Subject');?>">
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-group">
                            <textarea class="form-control" name="user_description" placeholder="<?php esc_attr_e('Review Description *');?>"></textarea>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <button class="tg-btn kt_make-review_appointment" type="submit"><?php pll_e('Submit Review');?></button>
                          <input type="hidden" name="redirect" class="redirect" value="<?php echo $redirect;?>" />
                          <input type="hidden" name="user_rating[recommendation]" class="user_rating" value="3" />
                          <input type="hidden" name="user_rating[bedside_manner]" class="user_rating2" value="3" />
                          <input type="hidden" name="user_rating[waiting_time]" class="user_rating3" value="3" />
                          <input type="hidden" name="user_rating[supporting_staff]" class="user_rating4" value="3" />
                          <input type="hidden" name="user_rating[facilities]" class="user_rating5" value="3" />
                          <input type="hidden" name="user_to" class="user_to" value="<?php echo esc_attr( $bk_user_to );?>" />
                          <input type="hidden" name="appointment_id" class="user_to" value="<?php echo esc_attr( $post->ID );?>" />
                        </div>
                      </div>
                    </fieldset>
                  </form>
                </div>
	                                        	<?php
	                                        }
                                        }
                                        ?>
									</div>
								</div>
							</article>
						</div>
				<?php
					endwhile;
					wp_reset_postdata();
				?>
			</div>
			
			<?php if (function_exists('fw_ext_sidebars_get_current_position')) { ?>
			<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 sidebar-section <?php echo sanitize_html_class($aside_class); ?>">
				<aside id="tg-sidebar" class="tg-sidebar tg-haslayout"><?php echo fw_ext_sidebars_show('blue'); ?></aside>
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>
