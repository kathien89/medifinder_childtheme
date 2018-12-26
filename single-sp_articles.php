<?php
/**
 *
 * The template used for displaying default article post style
 *
 * @package   Service Providers
 * @author    themographics
 * @link      https://themeforest.net/user/themographics/portfolio
 * @since 1.0
 */
get_header();
global $post;
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        $author = get_userdata( get_the_author_meta('ID') );
                        $doctor_name     = kt_get_title_name($author->ID).$author->first_name.' '.$author->last_name;
                    }
                }
?>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <div class="tg-heading-border tg-small">
                <h3><i class="fa fa-file-text"></i><?php pll_e('Articles');?> | <?php echo $doctor_name;?></h3>
            </div>
        </div>
        <div id="tg-twocolumns" class="tg-twocolumns article-detail-page">
            <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 pull-left">
                <?php
                if (have_posts()) {
                    while (have_posts()) {
                        the_post();
                        global $post, $thumbnail, $image_alt;
                        $height = 400;
                        $width = 1180;
                        $user_ID = get_the_author_meta('ID');
                        $user_url = get_author_posts_url($user_ID);
                        $thumbnail = docdirect_prepare_thumbnail($post->ID, $width, $height);
                        $post_thumbnail_id = get_post_thumbnail_id($post->ID);

                        $udata = get_userdata($user_ID);
                        $registered = $udata->user_registered;

                        $avatar = apply_filters(
                                'docdirect_get_media_filter', docdirect_get_user_avatar(array('width' => 100, 'height' => 100), $user_ID), array('width' => 100, 'height' => 100)
                        );
						

                        $thumb_meta = array();
                        if (!empty($post_thumbnail_id)) {
                            $thumb_meta = docdirect_get_image_metadata($post_thumbnail_id);
                        }
                        $image_title = !empty($thumb_meta['title']) ? $thumb_meta['title'] : 'no-name';
                        $image_alt = !empty($thumb_meta['alt']) ? $thumb_meta['alt'] : $image_title;

                        $title_hk = get_post_meta($post->ID, 'title_hk', true);
                        $title_cn = get_post_meta($post->ID, 'title_cn', true);
                        $title_fr = get_post_meta($post->ID, 'title_fr', true);

                        $article_detail_hk = get_post_meta($post->ID, 'article_detail_hk', true);
                        $article_detail_cn = get_post_meta($post->ID, 'article_detail_cn', true);
                        $article_detail_fr = get_post_meta($post->ID, 'article_detail_fr', true);
                        
                        $author = get_userdata( get_the_author_meta('ID') );
                        $doctor_name     = kt_get_title_name($author->ID).$author->first_name.' '.$author->last_name;

                        ?>
                        <div id="tg-content" class="tg-content">
                            <article class="tg-post tg-detailpage tg-postdetail">
                                <?php if (!empty($thumbnail)) { ?>
                                    <figure class="tg-themepost-img">
                                        <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($image_alt); ?>">
                                    </figure>
                                <?php } ?>
                                <div class="tg-postcontent">
                                    <ul class="tg-postmatadata">
                                        <?php
                                            $permalink = add_query_arg( 
                                                array(
                                                    'doctor'=>  $author->user_login ,
                                                    ), esc_url( get_permalink(get_page_by_path( 'archive-article' )) 
                                                ) 
                                            );
                                        ?>
                                        <a class="articles_link" href="<?php echo $permalink; ?>"><?php echo esc_attr( $doctor_name);?> | <?php pll_e('Articles Archive');?></a>
                                        <li>
                                            <a href="javascript:;">
                                                <i class="fa fa-user"></i><span>
                                                    <?php
														esc_html_e(' Written by', 'docdirect');
														echo '&nbsp;'.esc_attr($doctor_name).'&nbsp;';
														esc_html_e('for', 'docdirect');
														echo '&nbsp;'.get_bloginfo('name');
                                                    ?>
                                                </span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:;"><?php docdirect_get_post_date($post->ID); ?></a>
                                        </li>
                                    </ul>
                                    <ul  class="nav nav-tabs">
                                        <?php $plugins_url = plugins_url();
                                            $flag_url =  $plugins_url.'/polylang/flags/';
                                        ?>
                                        <li class="active"><a  href="#English" data-toggle="tab">English <img src="<?php echo $flag_url;?>gb.png" /></a></li>
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
                                    <?php
                                    $article_detail_hk = get_post_meta($post->ID, 'article_detail_hk', true);
                                    $article_detail_cn = get_post_meta($post->ID, 'article_detail_cn', true);
                                    $article_detail_fr = get_post_meta($post->ID, 'article_detail_fr', true);
                                    ?>
                                    <div class="tab-content">
                                        <div id="English" class="tab-pane fade in active">
                                            <div class="tg-title">
                                                <h3><?php docdirect_get_post_title($post->ID); ?></h3>     
                                            </div>
                                            <div class="tg-description article-detail-wrap">
                                                <?php the_content();?>
                                            </div>
                                        </div>
                                        <div id="Traditional" class="tab-pane fade in">
                                            <div class="tg-title">
                                                <h3><?php echo $title_hk?></h3>    
                                            </div>
                                            <div class="tg-description article-detail-wrap">
                                                <?php echo $article_detail_hk?> 
                                            </div>
                                        </div>
                                        <div id="Simplified" class="tab-pane fade in">
                                            <div class="tg-title">
                                                <h3><?php echo $title_vn?></h3>  
                                            </div>
                                            <div class="tg-description article-detail-wrap">
                                                <?php echo $article_detail_cn?> 
                                            </div>
                                        </div>
                                        <div id="French" class="tab-pane fade in">
                                            <div class="tg-title">
                                                <h3><?php echo $title_fr?></h3>  
                                            </div>
                                            <div class="tg-description article-detail-wrap">
                                                <?php echo $article_detail_fr?> 
                                            </div>
                                        </div>
                                    </div>
                                    <?php
										
										wp_link_pages(array(
											'before' => '<div class="page-links"><span class="page-links-title">' . esc_html__('Pages:', 'docdirect') . '</span>',
											'after' => '</div>',
											'link_before' => '<span>',
											'link_after' => '</span>',
										));
										edit_post_link(esc_html__('Edit', 'docdirect'), '<span class="edit-link">', '</span>');
                                    ?>
                                </div>
                                    <?php if (!empty(get_post_meta($post->ID, 'attachment_file', true))) {?>
                                    <div class="tg-associated">
                                        <h2><?php pll_e('Associated Articles'); ?></h2>
                                        <?php 
                                            $attachment_files = get_post_meta($post->ID, 'attachment_file', true);
                                            $attachment_files = explode( ',', $attachment_files);
                                            echo '<div class="row">';
                                            foreach ($attachment_files as $file) {
                                                echo '<div class="col-sm-4">';
                                                echo '<a href="'.wp_get_attachment_url( $file ).'" title="'.get_the_title($file).'"><img width="54" src="'.get_stylesheet_directory_uri() . '/images/pdf_icon.png'.'" alt="'.get_the_title($file).'" /></a>';
                                                echo ' <span>'.str_replace('-', ' ', get_the_title($file)).'.pdf</span>';
                                                echo '</div>';
                                            }
                                            echo '</div>';
                                        ?>
                                    </div>
                                    <?php }?>
                            </article>
							<div class="social-share">
                                <?php //docdirect_prepare_social_sharing('false','','false','',$thumbnail);?>
                                <?php //echo do_shortcode('[ssba-buttons]');?>
								<?php echo do_shortcode('[addtoany]');?>
							</div>
                            <div class="tg-author">
                                <?php if (!empty($avatar)) { ?>
                                    <figure>
                                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                            <img src="<?php echo esc_url($avatar); ?>" alt="<?php esc_attr_e('Avatar', 'docdirect'); ?>"></a>
                                    </figure>
                                <?php } ?>
                                <div class="tg-authorcontent">
                                    <div class="tg-authorbox">
                                        <div class="tg-authorhead">
                                            <div class="tg-leftbox">
                                                <div class="tg-name">
                                                    <h4><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo $doctor_name ?></a></h4>
                                                    <span><?php esc_html_e('Author Since', 'docdirect'); ?>:&nbsp; <?php echo date_i18n(get_option('date_format'), strtotime($registered)); ?></span>
                                                </div>
                                            </div>
                                            <?php
                                            $facebook = get_the_author_meta('facebook', $user_ID);
                                            $twitter = get_the_author_meta('twitter', $user_ID);
                                            $pinterest = get_the_author_meta('pinterest', $user_ID);
                                            $linkedin = get_the_author_meta('linkedin', $user_ID);
                                            $tumblr = get_the_author_meta('tumblr', $user_ID);
                                            $google = get_the_author_meta('google', $user_ID);
                                            $instagram = get_the_author_meta('instagram', $user_ID);
                                            $skype = get_the_author_meta('skype', $user_ID);
                                            ?>
                                            <div class="tg-rightbox">
                                                <?php
                                                if (!empty($facebook) || !empty($twitter) || !empty($pinterest) || !empty($linkedin) || !empty($tumblr) || !empty($google) || !empty($instagram) || !empty($skype)
                                                ) {
                                                    ?>
                                                    <ul class="tg-socialicons">
                                                        <?php if (isset($facebook) && !empty($facebook)) { ?>
                                                            <li class="tg-facebook">
                                                                <a href="<?php echo esc_url(get_the_author_meta('facebook', $user_ID)); ?>">
                                                                    <i class="fa fa-facebook"></i>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (isset($twitter) && !empty($twitter)) { ?>
                                                            <li class="tg-twitter">
                                                                <a href="<?php echo esc_url(get_the_author_meta('twitter', $user_ID)); ?>">
                                                                    <i class="fa fa-twitter"></i>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (isset($pinterest) && !empty($pinterest)) { ?>
                                                            <li class="tg-pinterest">
                                                                <a href="<?php echo esc_url(get_the_author_meta('pinterest', $user_ID)); ?>">
                                                                    <i class="fa fa-pinterest-p"></i>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (isset($linkedin) && !empty($linkedin)) { ?>
                                                            <li class="tg-linkedin">
                                                                <a href="<?php echo esc_url(get_the_author_meta('linkedin', $user_ID)); ?>">
                                                                    <i class="fa fa-linkedin"></i>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (isset($tumblr) && !empty($tumblr)) { ?>
                                                            <li class="tg-tumblr">
                                                                <a href="<?php echo esc_url(get_the_author_meta('tumblr', $user_ID)); ?>">
                                                                    <i class="fa fa-tumblr"></i>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (isset($google) && !empty($google)) { ?>
                                                            <li class="tg-googleplus">
                                                                <a href="<?php echo esc_url(get_the_author_meta('google', $user_ID)); ?>">
                                                                    <i class="fa fa-google-plus"></i>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (isset($instagram) && !empty($instagram)) { ?>
                                                            <li class="tg-dribbble">
                                                                <a href="<?php echo esc_url(get_the_author_meta('instagram', $user_ID)); ?>">
                                                                    <i class="fa fa-instagram"></i>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (isset($skype) && !empty($skype)) { ?>
                                                            <li  class="tg-skype">
                                                                <a href="<?php echo esc_url(get_the_author_meta('skype', $user_ID)); ?>">
                                                                    <i class="fa fa-skype"></i>
                                                                </a>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="tg-description">
                                            <p><?php echo nl2br(get_the_author_meta('description', $user_ID)); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <?php
                                // if( !empty( $enable_comments ) && $enable_comments === 'enable' ){  
                                    if (comments_open() || get_comments_number()) :
                                        comments_template();
                                    endif;
                                // }
                            ?>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 pull-right">
                <aside id="tg-sidebar" class="tg-sidebar">
                    <?php get_sidebar('articles');?>
                </aside>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
