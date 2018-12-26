<?php
/**
 *
 * The template part to edit articles.
 *
 * @package   Service Providers
 * @author    Themographics
 * @link      http://themographics.com/
 * @since 1.0
 */
global $current_user,
 $wp_roles,
 $userdata;
wp_enqueue_media();
$user_identity = $current_user->ID;
$url_identity = $user_identity;
if (!empty($_GET['identity'])) {
    $url_identity = $_GET['identity'];
}

$content = esc_html__('Article detail will be here', 'docdirect');
$settings = array('media_buttons' => true);
$edit_id = !empty($_GET['id']) ? intval($_GET['id']) : '';
$post_author = get_post_field('post_author', $edit_id);

$article_detail_hk = get_post_meta($edit_id, 'article_detail_hk', true);
$article_detail_cn = get_post_meta($edit_id, 'article_detail_cn', true);
$article_detail_fr = get_post_meta($edit_id, 'article_detail_fr', true);

$title_hk = get_post_meta($edit_id, 'title_hk', true);
$title_cn = get_post_meta($edit_id, 'title_cn', true);
$title_fr = get_post_meta($edit_id, 'title_fr', true);

?>
<div id="tg-content" class="tg-content edit-mode">
    <div class="tg-dashboardbox tg-businesshours">
        <?php
        if (intval($url_identity) === intval($post_author)) {
            $args = array('posts_per_page' => '-1',
                'post_type' => 'sp_articles',
                'orderby' => 'ID',
                'post_status' => 'publish',
                'post__in' => array($edit_id),
                'suppress_filters' => false
            );

            $query = new WP_Query($args);

            while ($query->have_posts()) : $query->the_post();
                global $post;
                ?>
                <div class="tg-dashboardtitle">
                    <h2><?php esc_html_e('Edit Article', 'docdirect'); ?></h2>
                </div>
                <div class="tg-servicesmodal tg-categoryModal"
                     <div class="tg-modalcontent">
                        <form class="tg-themeform tg-formamanagejobs tg-addarticle sp-dashboard-profile-form">
                            <fieldset>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="tg-upload form-group">
                                            <div class="tg-uploadhead">
                                                <span>
                                                    <h3><?php esc_html_e('Upload Featured Image', 'docdirect'); ?></h3>
                                                    <i class="fa fa-exclamation-circle"></i>
                                                </span>
                                                <i class="fa fa-upload"></i>
                                            </div>
                                            <div class="tg-box">
                                                <label class="tg-fileuploadlabel" for="tg-featuredimage">
                                                    <a href="javascript:;" id="upload-featured-image" class="tg-fileinput sp-upload-container">
                                                        <i class="fa fa-cloud-upload"></i>
                                                        <span><?php esc_html_e('Or Drag Your Files Here To Upload', 'docdirect'); ?></span>
                                                    </a> 
                                                    <div id="plupload-featured-container"></div>
                                                </label>
                                                <div class="tg-gallery">
                                                    <?php if (has_post_thumbnail()) { ?>
                                                        <div class="tg-galleryimg tg-galleryimg-item">
                                                            <figure>
                                                                <?php the_post_thumbnail(); ?>
                                                                <input type="hidden" name="attachment_id" value="<?php echo get_post_thumbnail_id(); ?>">
                                                            </figure>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <ul  class="nav nav-tabs">
                                            <?php $plugins_url = plugins_url();
                                                $flag_url =  $plugins_url.'/polylang/flags/';
                                            ?>
                                            <li class="active">
                                                <a  href="#English" data-toggle="tab">English <img src="<?php echo $flag_url;?>gb.png" /></a>
                                            </li>
                                            <li>
                                                <a href="#Traditional" data-toggle="tab">中文 (香港) <img src="<?php echo $flag_url;?>hk.png" /></a>
                                            </li>
                                            <li>
                                                <a href="#Simplified" data-toggle="tab">中文 (中国) <img src="<?php echo $flag_url;?>cn.png" /></a>
                                            </li>
                                            <li>
                                                <a href="#French" data-toggle="tab">Français <img src="<?php echo $flag_url;?>fr.png" /></a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="English" class="tab-pane fade in active">
                                                <div class="form-group">
                                                    <input type="text" value="<?php the_title(); ?>" name="article_title" class="form-control" placeholder="<?php esc_html_e('Article Title', 'docdirect'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <?php wp_editor(get_the_content(), 'article_detail', $settings); ?>
                                                </div>
                                            </div>
                                            <div id="Traditional" class="tab-pane fade in">
                                                <div class="form-group">
                                                    <input type="text" value="<?php $title_hk; ?>" name="title_hk" class="form-control" placeholder="<?php esc_html_e('Article Title', 'docdirect'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <?php wp_editor($article_detail_hk, 'article_detail_hk', $settings); ?> 
                                                </div>
                                            </div>
                                            <div id="Simplified" class="tab-pane fade in">
                                                <div class="form-group">
                                                    <input type="text" value="<?php $title_cn; ?>" name="title_cn" class="form-control" placeholder="<?php esc_html_e('Article Title', 'docdirect'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <?php wp_editor($article_detail_cn, 'article_detail_cn', $settings); ?> 
                                                </div>
                                            </div>
                                            <div id="French" class="tab-pane fade in">
                                                <div class="form-group">
                                                    <input type="text" value="<?php $title_fr; ?>" name="title_fr" class="form-control" placeholder="<?php esc_html_e('Article Title', 'docdirect'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <?php wp_editor($article_detail_fr, 'article_detail_fr', $settings); ?> 
                                                </div>
                                            </div>
                                        </div>
                                            <input type="hidden" name="current" value="<?php echo intval($post->ID); ?>">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <h2><?php esc_html_e('Tags', 'docdirect'); ?></h2>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="tg-addallowances">
                                            <div class="tg-addallowance">
                                                <div class="form-group">
                                                    <input type="text" name="article_tags" class="form-control input-feature" placeholder="<?php esc_html_e('Article Tags', 'docdirect'); ?>">
                                                    <a class="tg-btn add-article-tags" href="javascript:;"><?php esc_html_e('Add Now', 'docdirect'); ?></a>
                                                </div>
                                                <ul class="tg-tagdashboardlist sp-feature-wrap">
                                                    <?php
                                                    $terms = wp_get_post_terms($post->ID, 'article_tags');
                                                    if (!empty($terms)) {
                                                        foreach ($terms as $key => $term) {
                                                            ?>
                                                            <li>
                                                                <span class="tg-tagdashboard">
                                                                    <i class="fa fa-close delete_article_tags"></i>
                                                                    <em><?php echo esc_attr($term->name); ?></em>
                                                                </span>
                                                                <input type="hidden" name="article_tags[]" value="<?php echo esc_attr($term->slug); ?>">
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                <fieldset>
                    <h2><?php esc_html_e('Select Category', 'docdirect'); ?></h2>
                        <div class="tg-addallowances">
                        <?php 
                            $category = get_the_terms( $edit_id, 'sp_category' );
                            $arr_cats = array();  
                            foreach ( $category as $cat){
                                $arr_cats[] = $cat->term_id;
                            }
                            $category_id = $arr_cats;
                            $args = array(
                                'hide_empty' => 0   
                                );
                            $categories = get_terms( 'sp_category', $args );
                        ?>
                            <?php
                            if (!empty($categories)) {
                                foreach($categories as $category){
                                    if (!empty($category_id)) {
                                        $checked = (in_array($category->term_id, $category_id)) ? 'checked' : '' ;
                                    }
                                    echo '<label class="checkbox-inline"><input name="category_id[]" type="checkbox" '.$checked.' value="'.$category->term_id.'">'.$category->name.'</label>';
                                }
                            }
                            ?>
                        </div>
                </fieldset>
                <fieldset>
                    <h2><?php esc_html_e('Upload Associated Articles', 'docdirect'); ?></h2>
                    <div class="row">
                        <div class="col-md-2">
                            <a class='' href='javascript:;' style="position: relative;float: left;">
                                <img width="64" src="<?php echo get_stylesheet_directory_uri();?>/images/pdf-button.png" alt="" />
                                <input id="file_upload" type="file" name="__file" accept=".pdf" multiple="multiple" style='cursor: pointer;position:absolute;width:100%;height:100%;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="file_source" size="40" >
                            </a>
                        </div>
                        <div class="output_ajax col-md-10"><!-- error or success results -->
                            <div class="row">
                            <?php if( isset($edit_id) && $edit_id != '' ){?>
                                <?php 
                                    $attachment_files1 = get_post_meta($edit_id, 'attachment_file', true);
                                    if (!empty($attachment_files1)) {
                                        $attachment_files = explode( ',', $attachment_files1);
                                        foreach ($attachment_files as $file) {
                                            echo '<div class="col-sm-4">';
                                            echo '<a href="'.wp_get_attachment_url( $file ).'" title="'.get_the_title($file).'"><img src="'.get_stylesheet_directory_uri() . '/images/pdf_icon.png'.'" alt="'.get_the_title($file).'" /></a>';
                                            echo '<span class="remove_pdf" title="remove this file"><i class="fa fa-close"></i></span>';
                                            echo ' <span>'.str_replace('-', ' ', get_the_title($file)).'.pdf</span>';
                                            echo '</div>';
                                        }
                                    }
                                ?>
                            <?php }?>
                            </div>
                        </div>
                        <input class="pdf_file" type="hidden" name="attachment_file" value="<?php echo $attachment_files1;?>">
                    </div>
                </fieldset>
                            <fieldset>
                                <div id="tg-updateall" class="tg-updateall">
                                    <div class="tg-holder">
                                        <span class="tg-note"><?php esc_html_e('Click to', 'docdirect'); ?> <strong> <?php esc_html_e('Update Article Button', 'docdirect'); ?> </strong> <?php esc_html_e('to update the article.', 'docdirect'); ?></span>
                                        <?php wp_nonce_field('docdirect_article_nounce', 'docdirect_article_nounce'); ?>
                                        <a class="tg-btn process-article" data-type="update" href="javascript:;"><?php esc_html_e('Update Article', 'docdirect'); ?></a>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        } else {
            ?>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <?php DoctorDirectory_NotificationsHelper::warning(esc_html__('Restricted Access! You have not any privilege to view this page.', 'docdirect')); ?>
            </div>
        <?php } ?>
    </div>
</div>
<script type="text/template" id="tmpl-load-featured-thumb">
    <div class="tg-galleryimg tg-galleryimg-item">
    <figure>
    <img src="{{data.thumbnail}}">
    <input type="hidden" name="attachment_id" value="{{data.id}}">
    </figure>
    </div>
</script>
<script type="text/template" id="tmpl-load-article-tags">
    <li>
    <span class="tg-tagdashboard">
    <i class="fa fa-close delete_article_tags"></i>
    <em>{{data}}</em>
    </span>
    <input type="hidden" name="article_tags[]" value="{{data}}">
    </li>
</script>