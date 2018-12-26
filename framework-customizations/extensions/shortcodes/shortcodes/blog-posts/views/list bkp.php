<?php if (!defined('FW')) die( 'Forbidden' );
/**
 * @var $atts
 */
?>
<div class="sc-blogs">
<div class="row">
<div class="tg-view tg-blog-list">
		<?php
		global $paged;
		if (empty($paged)) $paged = 1;
		
		$blog_view	= $atts['blog_view'];
		// Count Total Pssts

		$tax_query['cat']	= '';
		$cat_sepration = $atts['posts_category'];
		
		if( isset( $cat_sepration ) && !empty(  $cat_sepration) ) {
			$tax_query['cat']	= implode(',',$cat_sepration);
		}
		
		$show_posts    = $atts['show_posts'] ? $atts['show_posts'] : '-1';        
		$args = array('posts_per_page' => "-1", 'post_type' => 'post', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
		
		if( isset( $cat_sepration ) && !empty( $cat_sepration )) {
			$args	= array_merge($args,$tax_query);
		}
		
		$query 		= new WP_Query( $args );
		$count_post = $query->post_count;        
		
		//Main Query	
		$args 		= array('posts_per_page' => $show_posts, 'post_type' => 'post', 'paged' => $paged, 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
		
		if( isset( $cat_sepration ) && !empty( $cat_sepration )) {
			$args	= array_merge($args,$tax_query);
		}
		
		$query 		= new WP_Query($args);
		while($query->have_posts()) : $query->the_post();
		global $post;
		$width  = '470';
		$height = '205';
		$thumbnail	= docdirect_prepare_thumbnail($post->ID ,$width,$height);
		if ( empty( $thumbnail ) ){
			$no_mediaClass	= 'media_none';
		}else {
			$no_mediaClass	= '';
		}
		?>
		<article class="tg-post col-sm-6 <?php echo $no_mediaClass;?>">
			<div class="tg-box">
				<figure class="tg-feature-img">
					<?php if( isset( $thumbnail ) && !empty( $thumbnail ) ){?>
						<a href="<?php echo esc_url( get_the_permalink() ); ?>"><img width="470" height="200" src="<?php echo esc_url($thumbnail);?>" alt="<?php echo sanitize_title( get_the_title() ); ?>"></a>
					<?php }?>
					<ul class="tg-metadata">
						<li><i class="fa fa-clock-o"></i><time datetime="<?php echo date_i18n('Y-m-d', strtotime(get_the_date('Y-m-d',$post->ID))); ?>"><?php echo date_i18n('d M, Y', strtotime(get_the_date('Y-m-d',$post->ID))); ?></time> </li>
						<li><i class="fa fa-comment-o"></i><a href="<?php echo esc_url( comments_link());?>"><?php comments_number( ' 0 Comments', ' 1 Comment', ' % Comments' ); ?></a></li>
					</ul>
				</figure>
				<div class="tg-contentbox">
					<div class="tg-displaytable">
						<div class="tg-displaytablecell">
							<div class="tg-heading-border tg-small">
								<?php 
									$length = mb_strlen( get_the_title(), 'utf-8' );
									if ( $length > 79 ) {
										$newTitle = substr( get_the_title(), 0, 80 ).' ...';
									} else {
										$newTitle = get_the_title();
									}
									
								?>
								<h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo $newTitle; ?> </a></h3>
							</div>
							<?php if( isset( $atts['show_description'] ) && $atts['show_description'] === 'show' ){?>
								<div class="tg-description">
									<?php docdirect_prepare_excerpt($atts['excerpt_length'],'false',''); ?>
								</div>
							<?php }?>
							<?php kt_add_post_author($post->ID);?>
						</div>
						<a href="<?php echo esc_url( get_the_permalink() ); ?>"><span class="tg-show"><em class="icon-add"></em></span></a>
					</div>
					<?php
					  if (is_sticky()) :
					   echo '<div class="sticky-post-wrap">
								  <div class="sticky-txt">
								   <em class="tg-featuretext">'.esc_html__('Featured').'</em>
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
	<?php docdirect_prepare_pagination($count_post,$atts['show_posts']);?>
<?php endif; ?>
</div>
</div>
