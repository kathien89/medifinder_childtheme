<?php
/**
 * User Add Blog
 * return html
 */

global $current_user, $wp_roles,$userdata,$post;
$dir_obj	= new DocDirect_Scripts();
$user_identity	= $current_user->ID;
$url_identity	= $user_identity;
if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
	$url_identity	= $_GET['identity'];
}
$delete_account_text 		= fw_get_db_settings_option('delete_account_text');
$profile_status = get_user_meta($url_identity , 'profile_status' , true);
?>
<?php
$db_directory_type	 = get_user_meta( $user_identity, 'directory_type', true);
    $terms = get_the_terms($db_directory_type, 'group_label');
    $current_group_label_slug = $terms[0]->slug;
    $user_premium = get_user_meta($user_identity , 'user_premium' , true);
    if($current_group_label_slug != 'medical-centre') {
        $current_option = get_option( $user_premium, true );
    }else {
        $current_option = get_option( 'company_'.$user_premium, true );
    }
?>
<?php if($current_option['articles'] != ''){?>
<div class="tg-myaccount tg-haslayout">
	<div class="tg-editprofile tg-haslayout">
		<div class="tg-formsection tg-haslayout">
			<div class="tg-heading-border tg-small">
				<?php
					$id_delete = $_GET['delete'];
					if( isset($id_delete) && $id_delete != '' ){
						$post_author = get_post_field( 'post_author', $id_delete );
						if ($post_author == $user_identity) {
							wp_delete_post($id_delete);
							$message = '<div class="alert alert-success">'.pll__( 'Delete article success' ).'</div>';
						}else {
							$message = '<div class="alert alert-danger">'.pll__( 'This post is not yours' ).'</div>';
						}
					}
				?>
				<?php
					$id_edit = $_GET['edit'];
					if( isset($id_edit) && $id_edit != '' ){
						$name = pll__( 'Edit Article' );
						$post_author = get_post_field( 'post_author', $id_edit );
						if ($post_author != $user_identity) {
							$message = '<div class="alert alert-danger">'.pll__( 'This post is not yours' ).'</div>';
						} else {
							$post_title = get_the_title($id_edit);
							$post_featured_image = get_post_thumbnail_id( $id_edit );
							$post_desc = get_post_field('post_content', $id_edit);

							$category = get_the_terms( $id_edit, 'category' );
							$arr_cats = array();  
							foreach ( $category as $cat){
							    $arr_cats[] = $cat->term_id;
							}
							$category_id = $arr_cats;

							$posttags = get_the_tags($id_edit);
							if ($posttags) {
								$arr_tags = array();
							  	foreach($posttags as $tag) {
							    	$arr_tags[] = $tag->name;
							  	}
							}
							$tag = implode(",", $arr_tags);
						}
					}else {
						$name = pll__( 'Add Article' );
					}
				?>
				<h2><?php echo $name;?></h2>
			</div>
			<div class="clearfix"></div>
			<?php
				if(is_user_logged_in()) {
					if (isset($_POST['submit']) || isset($_POST['update'])) {
						$post_title = isset( $_POST['post_title'] ) ? $_POST['post_title'] : '';
						$post_featured_image = isset( $_POST['post_featured_image'] ) ? $_POST['post_featured_image'] : '';
						$post_desc = $_POST['post_desc'];
						$category_id = isset( $_POST['category_id'] ) ? $_POST['category_id'] : '';
						$tag = isset( $_POST['tag'] ) ? $_POST['tag'] : '';
						/*echo '<pre>';
						var_dump($_POST);
						echo '</pre>';*/
						if( !empty( $post_title ) ){
							/*if ( !empty( $post_featured_image) ){*/
								if (isset($_POST['update'])) {
									// Update post 
								  	$args = array(
								      	'ID'           => $id_edit,
										'post_type' => 'post',
										'post_title' => $post_title,
										'post_content' => $post_desc,
										'post_status' => 'publish',
										'post_author' => $user_id,
										'tax_input' => array()
								  	);

									if( !empty( $category_id ) ){
										$args['tax_input']['category'] = $category_id;
									}

									if( !empty( $tag ) ) {
										$args['tax_input']['post_tag'] = $tag;
									}
									if (!empty($_POST['attachment_file'])) {
										update_post_meta($id_edit, 'attachment_file', $_POST['attachment_file']);
									}
									
									// Update the post into the database
								  	wp_update_post( $args );

									set_post_thumbnail( $id_edit, $post_featured_image );


									$message = '<div class="alert alert-success">'.pll__( 'Update article success' ).'</div>';
								}else {
									$user_id = get_current_user_id();
									$args = array(
										'post_type' => 'post',
										'post_title' => $post_title,
										'post_content' => $post_desc,
										'post_status' => 'publish',
										'post_author' => $user_id,
										'tax_input' => array()
									);

									if( !empty( $category_id ) ){
										$args['tax_input']['category'] = $category_id;
									}

									if( !empty( $tag ) ) {
										$args['tax_input']['post_tag'] = $tag;
									}

									$post_id = wp_insert_post( $args );

									if (!empty($_POST['attachment_file'])) {
										update_post_meta($post_id, 'attachment_file', $_POST['attachment_file']);
									}

									set_post_thumbnail( $post_id, $post_featured_image );

									$message = '<div class="alert alert-success">'.pll__( 'Add article success' ).'</div>';
								}
							/*}else {
								$message = '<div class="alert alert-danger">'.pll__( 'Feature Image is required' ).'</div>';
							}*/
						}else {
							$message = '<div class="alert alert-danger">'.pll__( 'Title is required' ).'</div>';
						}
					}
				}
				if( !empty( $message ) ){
					echo $message;
				}
			?>
			<div class="">
				<form id="submit_blog" method="POST" action="" enctype="multipart/form-data">

				    <div class="form-group">
				        <label for="post_title"><?php pll_e( 'Post Title' ); ?></label>
				        <input type="text" name="post_title" id="post_title" class="form-control"  value="<?php echo esc_attr( $post_title ) ?>" data-validation="required"">
				    </div>
					<div class="form-group">
						<label for="post_featured_image"><?php pll_e( 'Select Featured Image' ); ?></label>
						<input type="hidden" name="post_featured_image" id="post_featured_image" class="form-control" value="<?php echo esc_attr( $post_featured_image ) ?>" data-validation="required"">
							<?php
								if(!empty($post_featured_image)) {
									$url = wp_get_attachment_image_src($post_featured_image, 'thumbnail');
									$img .= '<img src="'.$url[0].'" />';
									// $url_image = $url[0];
								}else {

									$url_image = get_template_directory_uri().'/images/user150x150.jpg';
									$img .= '<img src="'.$url_image.'" />';
								}
							?>
						<div class="clearfix"></div>					
                        <figure class="tg-docimg"> 
                            <span class="user-avatar featured-image-wrap"><?php echo $img;?></span>
                            <a href="javascript:;" id="upload-profile-avatar1" class="tg-uploadimg upload-avatar featured-image"><i class="fa fa-upload"></i></a> 
                            <div id="plupload-container"></div>
                        </figure>
					</div>
			   		<div class="form-group">
				        <label for="post_desc;?>" ><?php _e( 'Content' ); ?></label>
				        <?php
				        	$content = $post_desc;
							// editor_id cannot have brackets and must be lowercase
							$editor_id = 'post_desc';
							// textarea_name in array can have brackets!
							$settings = array('textarea_name' => 'post_desc', 'editor_height' => '200');
							wp_editor($content, $editor_id, $settings);
				        ?>
			        	<p class="description"><?php pll_e( 'Input description.' ); ?></p>
			        </div>
					<div class="form-group">
				        <label for="attachment_file"><?php pll_e( 'Upload Associated Articles' ); ?></label>
						<div class="clearfix"></div>
						<div class="col-md-2">
	                	<a class='' href='javascript:;' style="position: relative;float: left;">
							<img width="64" src="<?php echo get_stylesheet_directory_uri();?>/images/pdf-button.png" alt="" />
							<input id="file_upload" type="file" name="__file" accept=".pdf" multiple="multiple" style='cursor: pointer;position:absolute;width:100%;height:100%;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;' name="file_source" size="40" >
						</a>
						</div>
						<div class="output_ajax col-md-10"><!-- error or success results -->
							<div class="row">
							<?php if( isset($id_edit) && $id_edit != '' ){?>
								<?php 
                                	$attachment_files1 = get_post_meta($id_edit, 'attachment_file', true);
                                	if (!empty($attachment_files1)) {
	                                	$attachment_files = explode( ',', $attachment_files1);
	                                	foreach ($attachment_files as $file) {
	                                        echo '<div class="col-sm-4">';
	                                		echo '<a href="'.wp_get_attachment_url( $file ).'" title="'.get_the_title($file).'"><img width="52" src="'.get_stylesheet_directory_uri() . '/images/pdf_icon.png'.'" alt="'.get_the_title($file).'" /></a>';
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
				    <div class="form-group">
				        <label for="post_title"><?php pll_e( 'Select Category' ); ?></label>
				        <div class="clearfix"></div>
				        <?php 
						$args = array(
						    'type' => 'post',
						    'hide_empty' => 0   
						    );
						$categories = get_categories( $args );
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
				    <!--<div class="form-group">
				        <label for="post_tag"><?php //pll_e( 'Input Tag' ); ?></label>
                    	<input type="text" name="tag" id="post_tag" class="form-control" value="<?php //echo $tag;?>" data-role="tagsinput" />
				    </div>-->

					<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>
						<?php
						if( isset($id_edit) && $id_edit != '' ){
							?>
							<input type="submit" class="btn btn-success post-form" value="<?php pll_e( 'Update' );?>" name="update">
							<?php
						}else {
							?>
							<input type="submit" class="btn btn-primary post-form" value="<?php pll_e( 'Submit' );?>" name="submit">
							<?php
						}
					    ?>
				</form>
			</div>
		</div>

		<div class="tg-formsection tg-haslayout list_post">
			<div class="tg-heading-border tg-small">
				<h2><?php pll_e('Article Archive');?></h2>
			</div>
			<div class="tg-education-detail tg-haslayout">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php pll_e('Title');?></th>
                            <th><?php pll_e('Categories');?></th>
                            <th><?php pll_e('Tag');?></th>
                            <th><?php pll_e('Date');?></th>
							<th><?php pll_e('Action');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
	                    $args = array(
							'post_type' => 'post',
							'author' => $current_user->ID,
						);
						$ListPost = get_posts($args);
					?>
						<?php 
						foreach($ListPost as $post):
						//setup_postdata( $post );		
						?>
							<tr>
                                <td>
									<div class="checkbox">
										<a class="title" target="_blank" href="<?php echo get_the_permalink($post->ID);?>" ><?php echo get_the_title($post->ID);?></a>
									</div>
								</td>
                                <td class="col_cat">
									<div class="checkbox">
										<?php
											$category = get_the_terms( $post->ID, 'category' );   
											$arr_cats = array();  
											foreach ( $category as $cat){
											    $arr_cats[] = $cat->name;
											}
											echo implode(", ", $arr_cats);
										?>
									</div>
								</td>
                                <td>
									<div class="checkbox">
										<?php
											$posttags = get_the_tags();
											if ($posttags) {
												$arr_tags = array();
											  	foreach($posttags as $tag) {
											    	$arr_tags[] = $tag->name;
											  	}
											}
											echo implode(", ", $arr_tags);
										?>
									</div>
								</td>
								<td>
									<div class="checkbox">
										<?php echo get_the_time('F j Y', $post->ID);?>
									</div>
								</td>
								<td>
									<div class="checkbox">
										<?php
											$dir_profile_page = '';
											if (function_exists('fw_get_db_settings_option')) {
								                $dir_profile_page = fw_get_db_settings_option('dir_profile_page', $default_value = null);
								            }

											$profile_page = isset($dir_profile_page[0]) ? $dir_profile_page[0] : '';
											$edit_link = add_query_arg( 
																array(
																	'ref'=>   urlencode( 'addblog' ) ,
																	'identity'=>   urlencode( $user_identity ) ,
																	'edit'=>   $post->ID ), 
																	esc_url( get_permalink($profile_page) 
																) 
															);
											$delete_link = add_query_arg( 
																array(
																	'ref'=>   urlencode( 'addblog' ) ,
																	'identity'=>   urlencode( $user_identity ),
																	'delete'=>   $post->ID   ), 
																	esc_url( get_permalink($profile_page) 
																) 
															);
										?>
										<a href="<?php echo $edit_link;?>" >Edit</a> /
										<a href="<?php echo $delete_link;?>" >Delete</a>
									</div>
								</td>
                            </tr> 
						<?php endforeach; ?>    	
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>
<?php }else {?>
<div class="tg-myaccount tg-haslayout">
	<?php kt_button_upgrade_premium();?>
</div>
<?php }?>