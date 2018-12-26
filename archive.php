<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Doctor Directory
 */
get_header();

$section_width	 = 'col-lg-8 col-md-8 col-sm-12 col-xs-12';
?>

<div class="container">
  <div class="row">
	<div class="tg-inner-content haslayout">
		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 aside sidebar-section <?php echo sanitize_html_class($aside_class); ?>" id="sidebar">
			<aside id="tg-sidebar" class="tg-sidebar tg-haslayout">
				<?php do_action('widget-ads');?>
				<?php echo fw_ext_sidebars_show('blue'); ?>
				<?php 
					if ( is_tag() ) {
						dynamic_sidebar('Sidebar');
					}
				?>
			</aside>
		</div>
		<div class="<?php echo esc_attr( $section_width );?> page-section">
			<div class="tg-view tg-blog-list">
				<?php
				global $paged;
				$tg_get_excerpt	= get_option('rss_use_excerpt');
				if (is_author()) {
					global $author;
					$userdata = get_userdata($author);
				}
				if (category_description() || is_tag() || (is_author() && isset($userdata->description) && !empty($userdata->description))) {
					echo '<article class="widget orgnizer">';
					if (is_author()) {
						
						$userprofile_media	= get_the_author_meta( 'userprofile_media', $author->ID );
						?>
						<figure>
							<?php if ( !empty( $userprofile_media ) ) { ?>
								<div class="author-img">
									<img src="<?php echo esc_url($userprofile_media)?>" alt="<?php esc_attr_e('Author Avatar','docdirect');?>" />
								</div>
							<?php } ?>
						</figure>
						<div class="left-sp">
							<h5><a><?php echo esc_attr($userdata->display_name); ?></a></h5>
							<p><?php echo balanceTags($userdata->description, true); ?></p>
						</div>
						<?php
					} elseif (is_category()) {
						$category_description = category_description();
						if (!empty($category_description)) {
							?>
							<div class="left-sp">
								<p><?php //echo category_description(); ?></p>
							</div>
						<?php } ?>
						<?php
					} elseif (is_tag()) {
						$tag_description = tag_description();
						if (!empty($tag_description)) {
							?>
							<div class="left-sp">
								<p><?php echo apply_filters('tag_archive_meta', $tag_description); ?></p>
							</div>
							<?php
						}
					}
					echo '</article>';
				}
		
				if (empty($paged)) {
					$paged = 1;
				}
		
				if (!isset($_GET["s"])) {
					$_GET["s"] = '';
				}
		
				$taxonomy = 'category';
				$taxonomy_tag = 'post_tag';
				$args_cat = array();
		
				if (is_author()) {
					$args_cat = array('author' => $wp_query->query_vars['author']);
					$post_type = array('post');
				} elseif (is_date()) {
					if (is_month() || is_year() || is_day() || is_time()) {
						$args_cat = array('m' => $wp_query->query_vars['m'], 'year' => $wp_query->query_vars['year'], 'day' => $wp_query->query_vars['day'], 'hour' => $wp_query->query_vars['hour'], 'minute' => $wp_query->query_vars['minute'], 'second' => $wp_query->query_vars['second']);
					}
					$post_type = array('post');
				} else if (is_category()) {
					$taxonomy = 'category';
					$args_cat = array();
					$category_blog = $wp_query->query_vars['cat'];
					$post_type = 'post';
					$args_cat = array('cat' => "$category_blog");
				} else if ((isset($wp_query->query_vars['taxonomy']) && !empty($wp_query->query_vars['taxonomy']))) {
					$taxonomy = $wp_query->query_vars['taxonomy'];
					$taxonomy_category = '';
					$taxonomy_category = $wp_query->query_vars[$taxonomy];
		
					$taxonomy = 'category';
					$args_cat = array();
					$post_type = 'post';
				}  else if (is_tag()) {
					$taxonomy = 'category';
					$args_cat = array();
					$tag_blog = $wp_query->query_vars['tag'];
					$post_type = 'post';
					$args_cat = array('tag' => "$tag_blog");
				} else {
					$taxonomy = 'category';
					$args_cat = array();
					$post_type = 'post';
				}
				$args = array(
					'post_type' => $post_type,
					'paged' => $paged,
					'post_status' => 'publish',
					'order' => 'ASC',
				);
				?>

	   
				<?php
				$args = array_merge($args_cat, $args);
				$custom_query = new WP_Query($args);
				if ($custom_query->have_posts()):
					echo '<div class="row">';
					while ($custom_query->have_posts()) : $custom_query->the_post();
						global $post;
						$width = '470';
						$height = '205';
						$title_limit = 1000;
						$thumbnail = docdirect_prepare_thumbnail($post->ID, $width, $height);
						$image_src = docdirect_prepare_thumbnail($post->ID, 'full');
						$stickyClass	= '';
						
						if( is_sticky() && !is_singular() ) {
							$stickyClass	= 'sticky';
						}
						
						docdirect_init_share_script();
						
						$no_mediaClass	= '';
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
										<div class="tg-description">
											<?php docdirect_prepare_excerpt(300,'false',''); ?>
										</div>
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
	
						<?php
					endwhile;
					echo '</div>';
				else:
					esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'docdirect');
				endif;
				echo '<div class="col-md-12">';
				$qrystr = '';
				if ($wp_query->found_posts > get_option('posts_per_page')) {
					if (function_exists('docdirect_prepare_pagination')) {
						echo docdirect_prepare_pagination(wp_count_posts()->publish, get_option('posts_per_page'));
					}
				}
				echo '</div>';
				?>
			</div>
		</div>
	</div>
  </div>
</div>
<?php get_footer(); ?>
