<?php
/**
 * Template Name: Archive Article
 */

get_header(); 
global $current_user;

$tg_sidebar	 	 = 'full';
$section_width	 = 'col-xs-12';
if (function_exists('fw_ext_sidebars_get_current_position')) {
	$current_position = fw_ext_sidebars_get_current_position();
	if( $current_position !== 'full' &&  ( $current_position == 'left' || $current_position == 'right' ) ) {
		$tg_sidebar	= $current_position;
		$section_width	= 'col-lg-8 col-md-8 col-sm-8 col-xs-12';
	}
}


$doctor_username = $_GET['doctor'];
$current_author_profile = get_user_by( 'login', $doctor_username );

$avatar = apply_filters(
				'docdirect_get_user_avatar_filter',
				 docdirect_get_user_avatar(array('width'=>300,'height'=>300), $current_author_profile->ID),
				 array('width'=>300,'height'=>300) //size width,height
			);

$banner	= docdirect_get_user_banner(array('width'=>1920,'height'=>450), $current_author_profile->ID);
docdirect_enque_rating_library();//rating
$review_data	= kt_docdirect_get_everage_rating ( $current_author_profile->ID );
$banner_parallax	= '';
if( !empty( $banner ) ){
	$banner_parallax	= 'data-appear-top-offset="600" data-parallax="scroll" data-image-src="'.$banner.'"';
}else {
    $banner_parallax    = 'data-appear-top-offset="600" data-parallax="scroll" data-image-src="'.get_stylesheet_directory_uri().'/images/doctor-banner-default.jpg"';
}
$privacy		= docdirect_get_privacy_settings($current_author_profile->ID); //Privacy settings
        $verify_user    = get_user_meta( $current_author_profile->ID, 'verify_user', true);
        $public_profile    = get_user_meta( $current_author_profile->ID, 'public_profile', true);
$apointmentClass	= 'appointment-disabled';
if( !empty( $privacy['appointments'] )
    && 
    $privacy['appointments'] == 'on'
    && 
    $verify_user == 'on'
	&& 
	$public_profile != 'off'
 ) {
	$apointmentClass	= 'appointment-enabled';
	if( function_exists('docdirect_init_stripe_script') ) {
		//Strip Init
		docdirect_init_stripe_script();
	}
	
	if( isset( $current_user->ID ) 
	 && 
		$current_user->ID != $current_author_profile->ID
	){
		$apointmentClass	= 'appointment-enabled';
	} else{
		$apointmentClass	= 'appointment-disabled';
	}
}
$doctor_name = kt_get_title_name($current_author_profile->ID).esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );

		$plugins_url = plugins_url();		
		$flag_url =  $plugins_url.'/polylang/flags/';
?>
<div id="tg-userbanner" class="kt_class tg-userbanner tg-haslayout parallax-window" <?php echo ($banner_parallax);?>>
	<div class="container">
    	<div class="row">
        <div class="col-sm-12 col-xs-12">
        	<div class="tg-userbanner-content">
                <h1><?php echo $doctor_name;?></h1>
                <?php if( !empty( $current_author_profile->tagline ) ) {?>
                <span><?php echo esc_attr( $current_author_profile->tagline );?></span>
                <?php }?>
                <ul class="tg-likestars">
                    <li><?php docdirect_get_rating_stars($review_data,'echo');?></li>
                    <li><?php docdirect_get_wishlist_button($current_author_profile->ID,true);?></li>
                    <li><span><?php echo intval( docdirect_get_user_views($current_author_profile->ID) );?>&nbsp;<?php pll_e('view(s)');?></span></li> 
                    <li><?php docdirect_get_likes_button($current_author_profile->ID);?></li>
                </ul>
                <?php 
				 if( !empty( $privacy['appointments'] )
					  && 
					  $privacy['appointments'] == 'on'
                        && 
                        $verify_user == 'on'
                        && 
                        $public_profile != 'off'
				 ) {
					 if( isset( $current_user->ID ) 
						 && 
							$current_user->ID != $current_author_profile->ID
						 &&
							is_user_logged_in()
					 ){
					?>
						<button class="tg-btn tg-btn-lg make-appointment-btn" type="button" data-toggle="modal" data-target=".tg-appointmentpopup"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
					<?php 
					}  else if( $current_user->ID != $current_author_profile->ID ){?>
						<button class="tg-btn tg-btn-lg make-appointment-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
				<?php }}?>
                <?php
                     if( isset( $current_user->ID ) 
                         && 
                            $current_user->ID != $current_author_profile->ID
                         &&
                            is_user_logged_in()
                     ){?>
                        <button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-request-only"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }else {?>
                        <button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }?>
            </div>
          </div>
        </div>
    </div>
</div>
<div class="container">
	<div class="row">
      <div class="tg-userdetail <?php echo sanitize_html_class( $apointmentClass );?>">
		<div class="col-lg-3 col-md-4 col-sm-4 col-xs-3">
            <figure class="tg-userimg">
            	<a href="<?php echo get_author_posts_url($current_author_profile->ID); ?>">
            		<img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?>">
            	</a>
            </figure>
		</div>
		<div class="col-lg-9 col-md-8 col-sm-8 col-xs-9">
          <div class="tg-userbanner-content">
                <h1><?php echo kt_get_title_name($current_author_profile->ID).esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?></h1>
                <?php if( !empty( $current_author_profile->tagline ) ) {?>
                <span><?php echo esc_attr( $current_author_profile->tagline );?></span>
                <?php }?>
                <ul class="tg-likestars">
                    <li><?php docdirect_get_rating_stars($review_data,'echo');?></li>
                    <li><?php docdirect_get_wishlist_button($current_author_profile->ID,true);?></li>
                    <li><span><?php echo intval( docdirect_get_user_views($current_author_profile->ID) );?>&nbsp;<?php pll_e('view(s)');?></span></li> 
                    <li><?php docdirect_get_likes_button($current_author_profile->ID);?></li>
                </ul>
                <?php 
				 if( !empty( $privacy['appointments'] )
					  && 
					  $privacy['appointments'] == 'on'
                        && 
                        $verify_user == 'on'
                        && 
                        $public_profile != 'off'
				 ) {
					 if( isset( $current_user->ID ) 
						 && 
							$current_user->ID != $current_author_profile->ID
						 &&
							is_user_logged_in()
					 ){
					?>
						<button class="tg-btn tg-btn-lg make-appointment-btn" type="button" data-toggle="modal" data-target=".tg-appointmentpopup"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
					<?php 
					}  else if( $current_user->ID != $current_author_profile->ID ){?>
						<button class="tg-btn tg-btn-lg make-appointment-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-calendar"></i><?php pll_e('Book Appoinment');?></button>
                <?php }}?>
                <?php
                     if( isset( $current_user->ID ) 
                         && 
                            $current_user->ID != $current_author_profile->ID
                         &&
                            is_user_logged_in()
                     ){?>
                        <button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-request-only"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }else if(!is_user_logged_in()) {?>
                        <button class="tg-btn tg-btn-lg request-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><i class="fa fa-envelope"></i><?php pll_e('Request Appointment');?></button>
                <?php }?>
            </div>
        </div>
      </div>
	</div>
</div>

<div class="container">
	<div class="sc-blogs">
		<div class="row">
			<div class="col-xs-12">
		  		<div class="tg-heading-border tg-small">
			  		<h3><i class="fa fa-folder-open"></i><?php echo $doctor_name;?> | <?php pll_e('Article Archive');?></h3>
			    </div>
			    
	            <div class="tg-sortfilters">
	                <form class="form-sort-articles" method="get" action="">
	                    <div class="tg-sortfilter tg-sortby">
	                        <div class="tg-select">
	                            <select name="sort" class="sort_by">
	                                <option value="ID" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'ID' ? 'selected' : ''; ?>><?php esc_html_e('Latest articles at top', 'docdirect'); ?></option>
	                                <option value="title" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'title' ? 'selected' : ''; ?>><?php esc_html_e('Order by title', 'docdirect'); ?></option>
	                                <option value="name" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'name' ? 'selected' : ''; ?>><?php esc_html_e('Order by article name', 'docdirect'); ?></option>
	                                <option value="date" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'date' ? 'selected' : ''; ?>><?php esc_html_e('Order by date', 'docdirect'); ?></option>
	                                <option value="rand" <?php echo isset($_GET['sort']) && $_GET['sort'] == 'rand' ? 'selected' : ''; ?>><?php esc_html_e('Random order', 'docdirect'); ?></option>
	                            </select>
	                        </div>
	                    </div>
	                    <div class="tg-sortfilter tg-arrange">
	                        <div class="tg-select">
	                            <select name="order" class="order_by">
	                                <option value="DESC" <?php echo isset($_GET['order']) && $_GET['order'] == 'DESC' ? 'selected' : ''; ?>><?php esc_html_e('DESC', 'docdirect'); ?></option>
	                                <option value="ASC" <?php echo isset($_GET['order']) && $_GET['order'] == 'ASC' ? 'selected' : ''; ?>><?php esc_html_e('ASC', 'docdirect'); ?></option>
	                            </select>
	                        </div>
	                    </div>
	                    <input type="hidden" class="" value="<?php echo $doctor_username; ?>" name="doctor">
	                </form>
	            </div>
			</div>
		<div class="tg-view tg-blog-list myclass">
			<?php
		global $paged;
		$pg_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
		$pg_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
		//paged works on single pages, page - works on homepage
		$paged = max( $pg_page, $pg_paged );

		$show_posts = get_option('posts_per_page') ? get_option('posts_per_page') : '2';

		$order = 'DESC';
		if (!empty($_GET['order'])) {
		    $order = esc_attr($_GET['order']);
		}

		$sorting = 'ID';
		if (!empty($_GET['sort'])) {
		    $sorting = esc_attr($_GET['sort']);
		}

		//total posts Query 
		$query_args = array(
			'posts_per_page' => -1,
			'post_type' => 'sp_articles',
    		'orderby' => 'ID',
			'post_status' => 'publish',
    		'author' => $current_author_profile->ID,
			'ignore_sticky_posts' => 1);

		//By Categories
		if (!empty($cat_sepration)) {
			$query_args = array_merge($query_args, $tax_query);
		}
		//By Posts 
		if (!empty($posts_in)) {
			$query_args = array_merge($query_args, $posts_in);
		}
		$query = new WP_Query($query_args);
		$count_post = $query->post_count;  

		//Main Query 
		$query_args = array(
			'posts_per_page' => $show_posts,
			'post_type' => 'sp_articles',
			'paged' => $paged,
    		'orderby' => $sorting,
    		'order' => $order,
			'post_status' => 'publish',
    		'author' => $current_author_profile->ID,
			'ignore_sticky_posts' => 1);

		//By Categories
		if (!empty($cat_sepration)) {
			$query_args = array_merge($query_args, $tax_query);
		}
		//By Posts 
		if (!empty($posts_in)) {
			$query_args = array_merge($query_args, $posts_in);
		}	
		$query = new WP_Query($query_args);
		while($query->have_posts()) : $query->the_post();
			global $post;
			$width  = '470';
			$height = '305';
			$thumbnail	= docdirect_prepare_thumbnail($post->ID ,$width,$height);
			
			if ( empty( $thumbnail ) ){
				$no_mediaClass	= 'media_none';
			}else {
				$no_mediaClass	= '';
			}
			$title_hk = get_post_meta($post->ID, 'title_hk', true);
			$title_cn = get_post_meta($post->ID, 'title_cn', true);
			$title_fr = get_post_meta($post->ID, 'title_fr', true);

			$article_detail_hk = get_post_meta($post->ID, 'article_detail_hk', true);
			$article_detail_cn = get_post_meta($post->ID, 'article_detail_cn', true);
			$article_detail_fr = get_post_meta($post->ID, 'article_detail_fr', true);
		?>
		<article class="tg-post col-sm-4 <?php echo $no_mediaClass;?>">
			<div class="tg-box">
				<figure class="tg-feature-img">
					<?php if( isset( $thumbnail ) && !empty( $thumbnail ) ){?>
						<a href="<?php echo esc_url( get_the_permalink() ); ?>"><img width="470" height="300" src="<?php echo esc_url($thumbnail);?>" alt="<?php echo sanitize_title( get_the_title() ); ?>"></a>
					<?php }?>
					<ul class="tg-metadata">
						<li><i class="fa fa-clock-o"></i><time datetime="<?php echo date_i18n('Y-m-d', strtotime(get_the_date('Y-m-d',$post->ID))); ?>"><?php echo date_i18n('d M, Y', strtotime(get_the_date('Y-m-d',$post->ID))); ?></time> </li>
						<li><i class="fa fa-comment-o"></i><a href="<?php echo esc_url( comments_link());?>">&nbsp;<?php comments_number( esc_html__('0 Comments','docdirect'), esc_html__('1 Comment','docdirect'), esc_html__('% Comments','docdirect') ); ?></a></li>
					</ul>
				</figure>
				<div class="tg-contentbox">
					<div class="tg-displaytable">
						
						<div class="tg-displaytablecell1">
                            <ul  class="nav nav-tabs">
                                <li class="active"><a  href="#English<?php echo $post->ID;?>" data-toggle="tab"><img src="<?php echo $flag_url;?>gb.png" /></a></li>
                            	<?php if ( !empty($title_hk) && !empty($article_detail_hk) ) {?>
                                <li><a href="#Traditional<?php echo $post->ID;?>" data-toggle="tab"><img src="<?php echo $flag_url;?>hk.png" /></a></li>
                                <?php }?>
                            	<?php if ( !empty($title_cn) && !empty($article_detail_cn) ) {?>
                                <li><a href="#Simplified<?php echo $post->ID;?>" data-toggle="tab"><img src="<?php echo $flag_url;?>cn.png" /></a></li>
                                <?php }?>
                            	<?php if ( !empty($title_fr) && !empty($article_detail_fr) ) {?>
                                <li><a href="#French<?php echo $post->ID;?>" data-toggle="tab"><img src="<?php echo $flag_url;?>fr.png" /></a></li>
                                <?php }?>
                            </ul>
                            <div class="tab-content">
                                <div id="English<?php echo $post->ID;?>" class="tab-pane fade in active">
									<div class="tg-heading-border tg-small">
										<h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?> </a></h3>
									</div>
									<div class="tg-description">
										<?php docdirect_prepare_excerpt('140','false',''); ?>
									</div>
                                </div>
                            	<?php if ( !empty($title_hk) && !empty($article_detail_hk) ) {?>
                                <div id="Traditional<?php echo $post->ID;?>" class="tab-pane fade in">     
									<div class="tg-heading-border tg-small">
										<h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo $title_hk; ?> </a></h3>
									</div>
									<div class="tg-description">
										<?php kt_crop_article_excerpt('140','false','', 'yes', 'article_detail_hk'); ?>
									</div>                                   
                                </div>
                                <?php }?>
                            	<?php if ( !empty($title_cn) && !empty($article_detail_cn) ) {?>
                                <div id="Simplified<?php echo $post->ID;?>" class="tab-pane fade in">                          	
									<div class="tg-heading-border tg-small">
										<h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo $title_cn; ?> </a></h3>
									</div>
									<div class="tg-description">
										<?php kt_crop_article_excerpt('140','false','', 'yes', 'article_detail_cn'); ?>
									</div>
                                </div>
                                <?php }?>
                            	<?php if ( !empty($title_fr) && !empty($article_detail_fr) ) {?>
                                <div id="French<?php echo $post->ID;?>" class="tab-pane fade in">
									<div class="tg-heading-border tg-small">
										<h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo $title_fr; ?> </a></h3>
									</div>
									<div class="tg-description">
										<?php kt_crop_article_excerpt('140','false','', 'yes', 'article_detail_fr'); ?>
									</div>
                                </div>
                                <?php }?>
                            </div>
						</div>
							<?php kt_add_post_author($post->ID);?>
						<a href="<?php echo esc_url( get_the_permalink() ); ?>"><span class="tg-show"><em class="icon-eye"><i class="fa fa-eye"></i></em></span></a>
					</div>
				</div>
			</div>
		</article>
		<?php endwhile; wp_reset_postdata(); ?>
		</div>	    
		<?php docdirect_prepare_pagination($count_post,$show_posts);?>
		</div>
	</div>
</div>
<?php
add_action('wp_footer', 'kt_article_footer');
function kt_article_footer() {
?>
1234
<?php 
global $current_user;
$doctor_username = $_GET['doctor'];
$current_author_profile = get_user_by( 'login', $doctor_username );

$privacy		= docdirect_get_privacy_settings($current_author_profile->ID); //Privacy settings
        $verify_user    = get_user_meta( $current_author_profile->ID, 'verify_user', true);
        $public_profile    = get_user_meta( $current_author_profile->ID, 'public_profile', true);
if( isset( $current_user->ID ) 
	&& 
	$current_user->ID != $current_author_profile->ID
	&&
	is_user_logged_in()
){

	if( !empty( $privacy['appointments'] )
	  && 
	  	$privacy['appointments'] == 'on'
 ) {

?>
<div class="modal fade tg-reviewpopup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog tg-modalcontent" role="document">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <p>You have already written a review. In order to post another review you must rebook with this specialist again through the system.  Once a booking is confirmed, you will gain another automatic review token.  You may also request a review code from the specialist directly. </p>
        <a class="btn btn-success" data-dismiss="modal">Ok, will try again later.</a>
  </div>
</div>

<div class="modal fade tg-appointmentpopup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg tg-modalcontent" role="document">
    <form action="#" method="post" class="appointment-form">
      <fieldset class="booking-model-contents">
        <ul class="tg-navdocappointment" role="tablist">
          <li class="active"><a href="javascript:;" class="bk-step-1"><?php pll_e('1. choose service');?></a></li>
          <li><a href="javascript:;" class="bk-step-2"><?php pll_e('2. available schedule');?></a></li>
          <li><a href="javascript:;" class="bk-step-3"><?php pll_e('3. your contact detail');?></a></li>
          <li><a href="javascript:;" class="bk-step-4"><?php pll_e('4. Payment Mode');?></a></li>
          <li><a href="javascript:;" class="bk-step-5"><?php pll_e('5. Finish');?></a></li>
        </ul>
        <div class="tab-content tg-appointmenttabcontent" data-id="<?php echo esc_attr( $current_author_profile->ID );?>">
          <div class="tab-pane active step-one-contents" id="one">
           	<?php kt_docdirect_get_booking_step_one($current_author_profile->ID,'echo');?>
          </div>
          <div class="tab-pane step-two-contents" id="two">
          	<?php                 
                if (isset($_POST["booking_date"])) {
                    kt_docdirect_get_booking_step_two_calender($current_author_profile->ID,'echo',$_POST["booking_date"]);
                }else {
                    kt_docdirect_get_booking_step_two_calender($current_author_profile->ID,'echo');
                }
            ?>
          </div>
          <div class="tab-pane step-three-contents" id="three"></div>
          <div class="tab-pane step-four-contents" id="four"></div>
          <div class="tab-pane step-five-contents" id="five"></div>
          <div class="tg-btnbox booking-step-button">
              <?php
                $cl = '';
                if (isset($_POST["booking_date"])) {
                    $cl = 'has_quickbooking';
                }
              ?>
              <button type="button" class="tg-btn kt_bk-step-next col-sm-push-9 col-md-push-10 <?php echo $cl;?>"><?php pll_e('next');?></button>
              <button type="button" class="tg-btn bk-step-prev col-sm-pull-9 col-md-pull-10"><?php pll_e('Previous');?></button>
            </div>
        </div>
      </fieldset>
    </form>
  </div>
</div>
<?php }}?>
<div class="modal fade tg-request-only" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="tg-modal-content" role="document">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title"><?php pll_e('Request Appointment');?></h4>
        <p><?php pll_e('Healthcare providers can change their appointment availability status to request only when away or busy. please send an appointment request with by filling in the required* fields:');?></p>
    </div>
    <div class="tg-request-form">
        <form action="#" method="post" class="request-form">
            <div class="doctor_info">
                <?php
                    // $user_info = get_userdata( $current_author_profile->ID );
                    $avatar = apply_filters(
                            'docdirect_get_user_avatar_filter',
                             docdirect_get_user_avatar(array('width'=>150,'height'=>150), $current_author_profile->ID),
                             array('width'=>150,'height'=>150) //size width,height
                        );
                ?>
                <a href="<?php echo get_author_posts_url($current_author_profile->ID); ?>" class="list-avatar"><img src="<?php echo esc_attr( $avatar );?>" alt="<?php echo esc_attr( $directories_array['name'] );?>"></a>
                <div>
                    <h4><?php echo esc_attr( $current_author_profile->first_name.' '.$current_author_profile->last_name );?></h4>
                    <?php if( !empty( $current_author_profile->tagline ) ) {?>
                        <span><?php echo esc_attr( $current_author_profile->tagline );?></span>
                    <?php }?>
                </div>
                <input type="hidden" name="doctor_id" value="<?php echo $current_author_profile->ID;?>">
            </div>
          <fieldset>
          <div class="row">
            <div class="form-group1 col-sm-6">
                <input type="text" name="first_name" class="form-control" id="name" placeholder="<?php pll_e( 'First Name *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="last_name" class="form-control" id="name" placeholder="<?php pll_e( 'Last Name *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <div class="doc-select">
                    <select name="gender">
                        <option value="">Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="phone" class="form-control" id="name" placeholder="<?php pll_e( 'Phone Number *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="email" class="form-control" id="name" placeholder="<?php pll_e( 'Email Address *' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="date_of_birth" class="form-control" id="name" placeholder="<?php pll_e( 'Date of Birth' ) ?>">
            </div>
            <div class="form-group1 col-sm-6">              
                <div class="form-group insurers">
                    <?php
                        $patient_insurers = get_user_meta( $current_user->ID, 'patient_insurers', true);

                        $insurer = get_term_by('id', $patient_insurers, 'insurer');

                        $current_insurer_text = $insurer->name;
                        $current_insurer       = !empty( $_GET['insurer'] ) ? $_GET['insurer'] : '';
                        if ( $current_insurer != '' ) {
                            $insurer    = get_term_by( 'slug', $current_insurer, 'insurer');
                            $current_insurer_text = $insurer->name;
                        }
						if (function_exists('kt_read_insurer')) {
		    				$list_insurer = kt_read_insurer();
						}

                    ?>
                    <a class="dropdown-button-group" href="javascript:;"><?php echo $current_insurer_text;?></a>
                    <input class="select_category" type="hidden" name="insurer" value="<?php echo $insurer->name;?>" />
                    <div class="dropdown-input-group">
                        <div class="dropdown-wrap">
                            <li data-slug=""><?php pll_e('Select Insurers');?></li>                       
                            <?php                                     
                            if( isset( $insurers_list ) && !empty( $insurers_list ) ){
                              if (function_exists('kt_read_insurer')) {
                                  $list_insurer = kt_read_insurer();
                              }
                              foreach( $insurers_list as $key => $insurer ){
                            ?>
                            <?php
                            // $taxonomy_image_url = get_option('z_taxonomy_image'.$insurer->term_id);
                                        $sample_bg_url = get_template_directory_uri().'/images/sample-insurer.png';
                                        $bg_url = ($list_insurer[$insurer->term_id][1]!='') ? $list_insurer[$insurer->term_id][1] : $sample_bg_url;
                            ?>
                            <li data-slug="<?php echo $insurer->slug;?>">
                              <img width="150" height="150" src="<?php echo $bg_url; ?>" >
                              <span><?php echo esc_attr( $insurer->name ); ?></span>                
                            </li>

                            <?php }}?>
                        </div>
                        <a class="close_specialities_wrap" href="javascript:;">
                            <i class="fa fa-close"></i>
                            <span><?php esc_html_e('Close','docdirect'); ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group1 col-sm-6">
                <input type="text" name="hkid" class="form-control" id="name" placeholder="<?php pll_e( 'HKID/Passport #' ) ?>">
            </div>
            <div class="form-group1 col-sm-12">
                <label><?php pll_e( 'Reason for visit' ) ?></label>
                <textarea name="message"></textarea>
            </div>
            <div class="form-group1 col-sm-12">
                <a class="btn btn-submit" href="javascript:;"><?php pll_e( 'Submit' ) ?></a>
                <a class="btn btn-cancel" href="javascript:;" data-dismiss="modal"><?php pll_e( 'Cancel' ) ?></a>
            </div>
          </div>
          </fieldset>
        </form>
    </div>
  </div>
</div>
<?php }?>
<?php get_footer(); ?>