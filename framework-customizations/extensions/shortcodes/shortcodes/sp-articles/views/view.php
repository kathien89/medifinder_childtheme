<?php if (!defined('FW')) die( 'Forbidden' );
/**
 * @var $atts
 */
?>
<div class="sc-blogs">
<div class="row">
	<div class="tg-view tg-blog-list">
		<?php
		$plugins_url = plugins_url();		
		$flag_url =  $plugins_url.'/polylang/flags/';

		global $paged;
		$pg_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
		$pg_paged = get_query_var('paged') ? get_query_var('paged') : 1; //rewrite the global var
		//paged works on single pages, page - works on homepage
		$paged = max( $pg_page, $pg_paged );

		if (isset($atts['get_mehtod']['gadget']) && $atts['get_mehtod']['gadget'] === 'by_posts' && !empty($atts['get_mehtod']['by_posts']['posts'])) {
			$posts_in['post__in'] = !empty( $atts['get_mehtod']['by_posts']['posts'] ) ? $atts['get_mehtod']['by_posts']['posts'] : array();
			$order    = 'DESC';
			$orderby  = 'ID';
			$show_posts  = !empty($atts['get_mehtod']['by_posts']['show_posts']) ? $atts['get_mehtod']['by_posts']['show_posts'] : '-1';
		} else {
			$cat_sepration = array();
			$cat_sepration = $atts['get_mehtod']['by_cats']['categories'];
			$order    	 = !empty($atts['get_mehtod']['by_cats']['order']) ? $atts['get_mehtod']['by_cats']['order'] : 'DESC';
			$orderby  	 = !empty($atts['get_mehtod']['by_cats']['orderby']) ? $atts['get_mehtod']['by_cats']['orderby'] : 'ID';
			$show_posts  = !empty($atts['get_mehtod']['by_cats']['show_posts']) ? $atts['get_mehtod']['by_cats']['show_posts'] : '-1';

			if ( !empty($cat_sepration) ) {
				$slugs = array();
				foreach ($cat_sepration as $key => $value) {
					$term = get_term($value, 'category');
					$slugs[] = $term->slug;
				}

				$filterable = $slugs;
				$tax_query['tax_query'] = array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'category',
						'terms' => $filterable,
						'field' => 'slug',
				));
			}
		}

		//total posts Query 
		$query_args = array(
			'posts_per_page' => -1,
			'post_type' => 'sp_articles',
			'order' => $order,
			'orderby' => $orderby,
			'post_status' => 'publish',
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
			'order' => $order,
			'orderby' => $orderby,
			'post_status' => 'publish',
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
		<article class="tg-post col-sm-6 <?php echo $no_mediaClass;?>">
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
					<?php
					  if (is_sticky()) :
					   echo '<div class="sticky-post-wrap">
								  <div class="sticky-txt">
								   <em class="tg-featuretext">'.esc_html__('Featured','docdirect').'</em>
								   <i class="fa fa-bolt"></i>
								  </div>
							 </div>';
					  endif;
					?>
				</div>
			</div>
		</article>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<?php if(isset($atts['show_pagination']) && $atts['show_pagination'] == 'yes') : ?>
		<?php docdirect_prepare_pagination($count_post,$show_posts);?>
	<?php endif; ?>
</div>
</div>
<script type="text/javascript">
	
$(window).load(function()
  {	
    $('.tg-blog-list').masonry({
	  // columnWidth: 200,
	  itemSelector: '.tg-post'
	});

});
</script>
