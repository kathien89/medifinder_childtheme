<?php
if (!defined('FW'))
    die('Forbidden');
/**
 * @var $atts
 */
$uniq_flag = fw_unique_increment();
?>
<div class="sc-blogs-carousel">
    <?php if ( !empty($atts['heading']) && !empty($atts['sub_heading']) && !empty($atts['description']) ) { ?>
        <div class="col-lg-offset-2 col-lg-8 col-md-offset-1 col-md-10 col-sm-offset-0 col-sm-12 col-xs-12">
          <div class="doc-section-head">
            <?php if ( !empty($atts['heading']) && !empty($atts['heading']) ) { ?>
            <div class="doc-section-heading">
              <?php if (!empty($atts['heading'])) { ?>
                    <h2><?php echo esc_attr($atts['heading']); ?></h2>
              <?php } ?>
              <?php if (!empty($atts['sub_heading'])) { ?>
                    <span><?php echo esc_attr($atts['sub_heading']); ?></span>
              <?php } ?>
            </div>
            <?php } ?>
            <?php if (!empty($atts['description'])) { ?>
                <div class="doc-description">
                    <p><?php echo esc_attr($atts['description']); ?></p>
                </div>
            <?php } ?>
          </div>
        </div>	
    <?php } ?>
    
    <div id="doc-blogpostslider-<?php echo esc_attr( $uniq_flag );?>" class="main_blog doc-blogpostslider doc-blogpost owl-carousel">
	<?php
        global $paged, $post;
        if (empty($paged))
            $paged = 1;
    
        $show_posts = !empty($atts['show_posts']) ? $atts['show_posts'] : -1;
        $data = !empty($atts['get_mehtod']) ? $atts['get_mehtod'] : array();
        $order = !empty($atts['order']) ? $atts['order'] : 'DESC';
        $orderby = !empty($atts['orderby']) ? $atts['orderby'] : 'ID';
        if (isset($data['gadget']) && $data['gadget'] === 'by_posts' && !empty($data['by_posts']['posts'])) {
            $posts_in['post__in'] = $data['by_posts']['posts'];
        } else if (isset($data['gadget']) && $data['gadget'] === 'by_cats' && !empty($data['by_cats']['categories'])) {
            $categories_in = $data['by_cats']['categories'];
            $tax_query['cat'] = implode(',', $categories_in);
        }
    
        $args = array('posts_per_page' => "-1",
            'post_type' => 'post',
            'order' => $order,
            'orderby' => $orderby,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        );
    
        //By Categories
        if (!empty($tax_query)) {
            $args = array_merge($args, $tax_query);
        }
    
        //By Posts 
        if (!empty($posts_in)) {
            $args = array_merge($args, $posts_in);
        }
    
        $query = new WP_Query($args);
        $count_post = $query->post_count;
    
        //Main Query	
        $args = array('posts_per_page' => $show_posts,
            'post_type' => 'post',
            'paged' => $paged,
            'order' => $order,
            'orderby' => $orderby,
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1
        );
    
        //By Categories
        if (!empty($tax_query)) {
            $args = array_merge($args, $tax_query);
        }
    
        //By Posts 
        if (!empty($posts_in)) {
            $args = array_merge($args, $posts_in);
        }
    
        $query = new WP_Query($args);
        while ($query->have_posts()) : $query->the_post();
            $width = '370';
            $height = '200';
            $thumbnail	= docdirect_prepare_thumbnail($post->ID ,$width,$height);
			$user_ID = get_the_author_meta('ID');
            
			$userprofile_media = get_the_author_meta('userprofile_media', $user_ID);
			
			if( !empty( $user_ID ) ){
				$userprofile_media = apply_filters(
					'docdirect_get_user_avatar_filter',
					 docdirect_get_user_avatar(array('width'=>150,'height'=>150), $user_ID),
					 array('width'=>150,'height'=>150) //size width,height
				);
			}
            $get_username = kt_get_title_name($user_ID).docdirect_get_username( $user_ID );
			
			if (isset($thumbnail) && $thumbnail) {
                $thumbnail = $thumbnail;
            } else {
                $thumbnail = get_template_directory_uri() . '/images/blog-370x200.png';
            }
            ?>
            <article class="item doc-post tg-post">
                
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
                        <div class="tg-displaytablecell">
                            <div class="tg-heading-border tg-small">
                                <h3><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php the_title(); ?> </a></h3>
                            </div>                            
                            <?php if (isset($atts['show_description']) && $atts['show_description'] === 'show') { ?>
                                <div class="tg-description">
                                    <p><?php docdirect_prepare_excerpt($atts['excerpt_length'],'false',''); ?></p>
                                </div>
                            <?php }?>
                        </div>
                            <?php //kt_add_post_author($post->ID);?>
                            <div class="social-share">
                                <?php docdirect_prepare_social_sharing('false','','false','',$thumbnail);?>
                            </div>
                            <?php //echo do_shortcode('[ssba-buttons]');?>
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
            </article>
            <?php
		endwhile;
		wp_reset_postdata();
    ?>
    </div>
    <script>
		jQuery(document).ready(function(e) {
			jQuery("#doc-blogpostslider-<?php echo esc_attr( $uniq_flag );?>").owlCarousel({
				items:3,
				rtl: <?php docdirect_owl_rtl_check();?>,
				nav: false,
				dots: true,
				autoplay: true,
				rewind:true,
				navText : ['<i class="doc-btnprev icon-arrows-1"></i>','<i class="doc-btnnext icon-arrows"></i>'],
				responsive:{
					0:{items:1},
					481:{items:2},
					991:{items:2},
					1200:{items:3},
					1280:{items:4},
				}
			});
        });
	</script>
</div>