<?php
/**
 * User Profile Main
 * return html
 */

global $current_user, $wp_roles,$userdata,$post;
$dir_obj	= new DocDirect_Scripts();
$user_identity	= $current_user->ID;
$url_identity	= $user_identity;

if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
	$url_identity	= $_GET['identity'];
}

//Get profile hits
$year	= date('y');
$month	= date('m');
$profile_hits = get_user_meta($url_identity , 'profile_hits' , true);
$months_array	= docdirect_get_month_array(); //Get Month  Array
$hits_data	= '';
if( isset( $profile_hits[$year] ) && !empty( $profile_hits[$year] ) ){
	$current_hits	= $profile_hits[$year];
	foreach( $months_array as $key => $value ){
		$hits_data[$key]	= 0;
		if( isset( $current_hits[$key] ) ) {
			$hits_data[$key]	= $current_hits[$key];
		}
	}	
} else{
	foreach( $months_array as $key => $value ){
		$hits_data[$key]	= 0;
	}
}
?>
<?php
docdirect_enque_rating_library();//rating
do_action('kt_check_subscriber');
$user_featured = get_user_meta($url_identity, 'user_featured', true);
$today = current_time( 'timestamp' );

$author = get_userdata( $user_identity );
$user_roles = $author->roles;
if( $user_roles[0] == 'professional' && ( $user_featured == '' && $user_featured < $today ) ){
	$show_popup = get_user_meta($url_identity , 'show_popup' , true);
	if( $show_popup == '' ) {
		function kt_add_modal_footer1() {
		?>
		<div class="modal fade tg-popup-activetrial">
		  <div class="tg-modal-content" role="document">
		  	<div class="confirmbox">
			  	<div class="tg-activetrial">
				    <p><?php pll_e('To activate your <span>6 months free trial</span> we must verify your payment option. No charges will be applied until 180 days have passed. You can adjust your payment plan in the dashboard or suspend your account at any time.');?></p>
				    <a href="javascript:;" class="yes"><?php pll_e('Verify Payment Option');?></a>
				    <a href="javascript:;" class="" data-dismiss="modal"><?php pll_e('No Thanks, Will Activate Later');?></a>
				</div>
			</div>
		  </div>
		</div>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						$('.tg-popup-activetrial').modal('show');
					});
				</script>
		<?php
		}
		add_action('wp_footer', 'kt_add_modal_footer1', 99);
	}?>
	<?php
	$new_user = get_user_meta($url_identity , 'new_user' , true);
	if( $new_user == '' ) {?>
		<div class="tg-graph tg-haslayout new_user">
			<div class="tg-profilehits">
				<div class="tg-heading-border tg-small">
					<?php
						//prepare return url
						$dir_profile_page = '';
						if (function_exists('fw_get_db_settings_option')) {
					        $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
					    }
						$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
						
						$return_url	= DocDirect_Scripts::docdirect_profile_menu_link($profile_page, 'booking-settings', $user_identity,true);

						$permalink = add_query_arg( 
								array(
									'ref'=>  'booking-settings' ,
									'identity'=>   urlencode( $user_identity ) ,
									'verify'=>   'paypal' 
									), esc_url( get_permalink($profile_page) 
								) 
							);
					?>			
				    <p><?php pll_e('To activate your <span>6 months free trial</span> we must verify your payment option. No charges will be applied until 180 days have passed. You can adjust your payment plan in the dashboard or suspend your account at any time.');?></p>
				    <a class="btn btn-success" href="<?php echo $permalink;?>"><?php pll_e('Verify Payment Option');?></a>
				</div>
			</div>
		</div>
	<?php }
}?>
<div class="tg-graph tg-haslayout">
    <?php if( $user_roles[0] == 'professional' ){?>
    <div class="tg-statistics">
		<div class="tg-heading-border tg-small">
			<h3><?php pll_e('Statistics');?></h3>
		</div>
	    <?php		
	   		$review_data    = kt_docdirect_get_everage_rating ( $url_identity );
	   		$list_data = $review_data['list_data'];
	   		/*echo '<pre>';
	   		var_dump($review_data['list_data']);
	   		echo '</pre>';*/
			$wishlist    = get_user_meta($url_identity,'wishlist', true);
			$total_bookings = kt_get_total_booking($url_identity);
	    ?>
	    <ul class="kt_blocks">
	    	<li>
    			<img src="<?php echo get_stylesheet_directory_uri();?>/images/review/overall_score.jpg">
	    		<p><span><?php echo $review_data['total_points'];?></span><br> Overall scrore</p>
	    	</li>
	    	<li>
    			<img src="<?php echo get_stylesheet_directory_uri();?>/images/review/total_likes.jpg">
	    		<p><span><?php echo count($wishlist);?></span><br> Total likes</p>
	    	</li>
	    	<li>
    			<img src="<?php echo get_stylesheet_directory_uri();?>/images/review/total_views.jpg">
	    		<p><span><?php echo intval( docdirect_get_user_views( $url_identity ) );?></span><br> Total views</p>
	    	</li>
	    	<li>
    			<img src="<?php echo get_stylesheet_directory_uri();?>/images/review/total_bookings.png">
	    		<p><span><?php echo $total_bookings;?></span><br> Bookings</p>
	    	</li>
	    </ul>
	    <?php
			global $array_review;
			global $array_emoji;
		?>
		<div class="row">
			<?php 
			foreach ($array_review as $key => $rv) {
				$rv_data = $list_data[$rv['name']];
				/*echo '<pre>';
				var_dump($rv_data);
				echo '</pre>';*/
			?>
		    <div class="kt_score col-sm-6 col-lg-4">
		    	<div>
			    	<figure><img src="<?php echo get_stylesheet_directory_uri();?>/images/review/<?php echo $rv['img'];?>.png"></figure>
			    	<div class="">
				    	<h5><?php echo $rv['title'];?></h5>
				    	<span><?php echo $rv_data['total'];?> Pts</span>
				    	<ul>
				    	<?php foreach ($array_emoji as $point => $name) {?>
					    	<li>
					    		<img src="<?php echo get_stylesheet_directory_uri();?>/images/review/<?php echo $name;?>_active.png">
					    		<small><?php echo isset($rv_data['details'][$point])?$rv_data['details'][$point]: '0';?></small>
					    	</li>
				    	<?php }?>
				    	</ul>
				    </div>
			    </div>
			</div>
			<?php }?>
		</div>
	</div>
	<div class="tg-profilehits">
		<div class="tg-heading-border tg-small">
			<h3><?php pll_e('Profile Hits');?></h3>
		</div>
		<canvas id="canvas" class="canvas"></canvas>
	</div>
	<?php }?>
</div>
<?php if( apply_filters('docdirect_do_check_user_type',$url_identity ) === true ){?>
		<div class="tg-costumerreview">
			<div class="tg-heading-border tg-small">
				<h3><?php pll_e('Customer Reviews');?></h3>
			</div>
			<div id="tg-reviewscrol" class="tg-reviewscrol">
				<ul class="tg-reviews tg-reviewlisting">
					<?php if( apply_filters('kt_docdirect_count_reviews',$url_identity) > 0 ){   
						$arr_v = array(
								'recommendation' => '5'
							);

						//Main Query	
						global $paged;
						$show_posts    = get_option('posts_per_page') ? get_option('posts_per_page') : '-1';  
						// $show_posts	= 1; 

						$args = array('posts_per_page' => '-1', 
									'post_type' => 'docdirectreviews', 
									'paged' => $paged, 
									'order' => 'DESC', 
									'orderby' => 'ID', 
									'post_status' => 'publish', 
									'lang' => '',
									'ignore_sticky_posts' => 1
								);
						 
						$meta_query_args = array();
						$meta_query_args = array('relation' => 'AND',);
						$meta_query_args[] = array(
												'key' 	   => 'user_to',
												'value' 	 => $url_identity,
												'compare'   => '=',
												'type'	  => 'NUMERIC'
											);
						$args['meta_query'] = $meta_query_args;
						
						$query 		= new WP_Query( $args );
						$count_post = $query->post_count;

						//////////
						$meta_query_args = array();
						$meta_query_args = array('relation' => 'AND',);
						$meta_query_args[] = array(
												'key' 	   => 'user_to',
												'value' 	 => $url_identity,
												'compare'   => '=',
												'type'	  => 'NUMERIC'
											);
								
						$args = array(
							'posts_per_page' => $show_posts,
							'post_type' => 'docdirectreviews', 
							'paged' => $paged, 
							'order' => 'DESC', 
							'orderby' => 'ID', 
							'post_status' => 'publish', 
							'lang' => '',
							'ignore_sticky_posts' => 1
						);
						
						$args['meta_query'] = $meta_query_args;
						
						$average_rating	= 0;
						$average_count	= 0;
						$query 		= new WP_Query($args);
						while($query->have_posts()) : $query->the_post();
							global $post;
							$user_rating = fw_get_db_post_option($post->ID, 'user_rating', true);
							$user_from = fw_get_db_post_option($post->ID, 'user_from', true);
                        	$user_to = fw_get_db_post_option($post->ID, 'user_to', true);
							$review_date = fw_get_db_post_option($post->ID, 'review_date', true);
							$user_data 	  = get_user_by( 'id', intval( $user_from ) );
							
							$avatar = apply_filters(
											'docdirect_get_user_avatar_filter',
											 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_from),
											 array('width'=>140,'height'=>89) //size width,height
										);
							
							if( !empty( $user_data ) ) {
							$user_name	= $user_data->first_name.' '.$user_data->last_name;
					
							if( empty( $user_name ) ){
								$user_name	= $user_data->user_login;
							}

                        	$user_rating1 = json_decode($user_rating, true);
                        	$sum = array_sum($user_rating1);
                        	$percentage	= $sum/5*20;

							// $percentage	= $user_rating*20;
							
							$average_rating	= $average_rating + $user_rating;
							$average_count++;

                        	$author = get_userdata( $user_from );
                        	$user_roles = $author->roles;	                        
	                        if( $user_roles[0] == 'professional' && $user_from != $user_to ){
	                            $link = get_author_posts_url($user_from);
	                        
	                            $avatar = apply_filters(
	                                        'docdirect_get_user_avatar_filter',
	                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_from),
	                                         array('width'=>150,'height'=>150) //size width,height
	                                    );
	                            $name = esc_attr( $user_name );
	                        }else {
	                            $link = 'javascript:;';
	                            $avatar = get_stylesheet_directory_uri().'/images/verified-patient.png';
	                            $name = pll__('By a Verified Patient');
	                        }
						?>
                    <li id="rv-<?php echo $post->ID;?>">
                        <div class="tg-review">
                          <figure class="tg-reviewimg"> 
                            <a href="<?php echo $link; ?>"><img src="<?php echo esc_url( $avatar );?>" alt="<?php pll_e('Reviewer');?>"></a>
                          </figure>
                          <div class="tg-reviewcontet">
                            <div class="tg-reviewhead">
                              <div class="tg-reviewheadleft">
                                <h3><a href="<?php echo $link; ?>"><?php echo $name;?></a></h3>
                                <span><?php echo human_time_diff( strtotime( $review_date ), current_time('timestamp') ) . ' ago'; ?></span> </div>
                              <div class="tg-reviewheadright">
                                <p><span><?php printf( pll__( '+ %s Points', 'text_domain' ), $sum );?></span><br><?php pll_e('Review Score');?></p>
                              </div>
                            </div>
                            <div class="tg-description">
                              <p><?php the_content();?></p>
                            </div>
                          </div>
                        </div>
                        <?php                            
                            $args1       = array(
                                'post_type' => 'docdirectreviews', 
                                'order' => 'DESC', 
                                'orderby' => 'ID', 
                                'post_status' => 'publish',
                                'lang' => '', 
                                'post_parent' => $post->ID,
                                'ignore_sticky_posts' => 1
                            );                            
                            $query1      = new WP_Query($args1);
                        ?>
                        <div class="button">
                        	<div class="expand">
	                          	<span><i class="fa fa-star"></i><?php pll_e('Rating Details');?></span>
	                          	<span><i class="fa fa-close"></i><?php pll_e('Close');?></span>
	                         </div>
                            <?php if ($current_user->ID == $_GET['identity']) {
                            	if( $query1->post_count > 0 ) {
                            	?>
                                	<a class="reply_btn view_reply" rel="nofollow" href="javascript:;"><i class="fa fa-reply"></i><?php pll_e('View Reply');?></a>
                                <?php }else{?>
                                	<a class="reply_btn open_reply_form" rel="nofollow" href="javascript:;" aria-label="Reply to"><i class="fa fa-reply"></i><?php pll_e('Reply');?></a>
                               <?php }
                            }?>
                        </div>
                        <div class="reply_hidden">
                        	<?php
                            if( $query1->have_posts() ){
                                echo '<ul>';
                                while($query1->have_posts()) : $query1->the_post();
                                    global $post;

                                    $user_data    = get_user_by( 'id', intval( get_the_author_meta('ID') ) );

                                    $avatar = apply_filters(
                                        'docdirect_get_user_avatar_filter',
                                         docdirect_get_user_avatar(array('width'=>150,'height'=>150), get_the_author_meta('ID')),
                                         array('width'=>150,'height'=>150) //size width,height
                                    );
                                    $user_name  = '';
                                    if( !empty( $user_data ) ) {
                                        $user_name  = $user_data->first_name.' '.$user_data->last_name;
                                    }
                                    
                                    if( empty( $user_name ) && !empty( $user_data ) ){
                                        $user_name  = $user_data->user_login;
                                    }
                                ?>
                                <li id="rv-<?php echo $post->ID;?>">
                                    <div class="tg-review">
                                      <figure class="tg-reviewimg"> 
                                        <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><img src="<?php echo esc_url( $avatar );?>" alt="<?php pll_e('Reviewer');?>"></a>
                                      </figure>
                                      <div class="tg-reviewcontet">
                                        <div class="tg-reviewhead">
                                          <div class="tg-reviewheadleft">
                                            <h3><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo esc_attr( $user_name );?></a>
                                                <?php if ($current_user->ID == $_GET['identity']) {?>
                                                            <a class="tg-btn remove_reply_button" href="javascript:;"><i class="fa fa-times"></i></a>
                                                <?php }?>
                                            </h3>
                                            <span><?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago'; ?></span>
                                          </div>
                                        </div>
                                        <div class="tg-description">
                                         	<p><?php the_content();?></p>
                                        </div>
                                      </div>
                                    </div>
                                </li>
                                <?php
                                endwhile;
                                echo '</ul>';
                            }
                        	?>
                        </div>
                        <div class="row star_detail">
							<?php foreach ($array_review as $key => $rv) {
								$num_point = $user_rating1[$key]
							?>
						    <div class="col-sm-6 col-lg-4">
						    	<img src="<?php echo get_stylesheet_directory_uri();?>/images/review/<?php echo $rv['img'];?>.png">
						    	<div class="content">
							    	<h5><?php echo $rv['title'];?></h5>
							    	<div class="clearfix"></div>
							    	<?php foreach ($array_emoji as $point => $name) {
							    		$active = ($num_point == $point) ? 'active' : '' ;
							    	?>
								    	<span class="icon_emoji <?php echo $name.' '. $active;?>" for="<?php echo $name.'-'.$key;?>"></span>
							    	<?php }?>
							    	<span class="num_point"><?php echo $num_point;?><i class="fa fa-plus"></i></span>
							    </div>
						    </div>
							<?php }?>
                        </div>
                    </li>
                     	<?php
						}
					 		endwhile; wp_reset_postdata();
					} else{?>
                    	 <li class="noreviews-found"> <?php DoctorDirectory_NotificationsHelper::informations(pll__('No Reviews Found.'));;?></li>
                    <?php }?>
				</ul>
			  <div class="col-md-xs-12">
			  	<?php 
					if( isset($count_post) && $count_post > $show_posts ) {
						docdirect_prepare_pagination($count_post,$show_posts);
					}
				?>
			  </div>
			</div>
		</div>
    <?php 
	if( isset( $average_rating ) && $average_rating > 0 ){
		$percentage	= ( $average_rating/ $average_count)*20;
		?>
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="row">
                <div class="tg-heading-border tg-small">
                    <h3><?php pll_e('Overall Rank');?></h3>
                </div>
                <div class="tg-ratingbox">
                    <div class="tg-stars star-rating">
                        <span style="width:<?php echo esc_attr( $percentage );?>%"></span>
                    </div>
                    <strong><?php pll_e('very good');?></strong> </div>
                <a class="tg-btn" href="<?php echo get_author_posts_url($url_identity); ?>"><?php pll_e('Read More');?></a> 
            </div>
        </div>
    <?php }?>
<?php }?>
<?php 
$author = get_userdata( $user_identity );
$user_roles = $author->roles;
if( $user_roles[0] == 'professional' ){?>
<script>
	var lineChartData  = {
		labels: <?php echo json_encode( array_values( $months_array ) );?>,
		datasets: [
			{
				label: "<?php pll_e('Profile Hits');?>",
				fillColor : "rgba(220,220,220,0)",
				strokeColor : "rgba(203,202,201,1)",
				pointColor : "rgba(93,89,85,1)",
				pointStrokeColor : "rgba(238,238,238,1)",
				pointHighlightFill : "rgba(125,187,0,1)",
				pointHighlightStroke : "rgba(220,220,220,1)",
				data : <?php echo json_encode( array_values( $hits_data ) );?>
		},
		]
	};
	window.onload = function(){
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myLine = new Chart(ctx).Line(lineChartData, {
			responsive: true
		});
	}
</script>
<?php 
if( isset( $current_user->ID ) 
    && 
    $current_user->ID == $_GET['identity']
    &&
    is_user_logged_in()
){
?>

    <div id="reply_review" style="display: none;">
        <form class="tg-formleavereview form-review">
            <div class="message_contact  theme-notification"></div>
            <fieldset>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="tg-heading-border tg-small">
                            <h4>Reply Review</h4>
                        </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group">
                        <input type="text" name="user_subject" class="form-control" placeholder="<?php pll_e('Subject');?>">
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <div class="form-group">
                        <textarea class="form-control" name="user_description" placeholder="<?php pll_e('Reply Description *');?>"></textarea>
                      </div>
                    </div>
                    <div class="col-sm-12">
                      <button class="tg-btn pull-left" id="cancel-review-reply-link" type="button"><?php pll_e('Cancel reply');?></button>
                      <button class="tg-btn make-reply" type="submit"><?php pll_e('Submit Reply');?></button>
                    </div>
                </div>
            </fieldset>
        </form>
        
    </div>

<?php }?>
<?php }?>

