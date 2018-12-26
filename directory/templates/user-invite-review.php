<?php
/**
 * User Invite review
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
<div class="tg-myaccount tg-haslayout">
	<div class="tg-affiliation tg-haslayout">
		<div class="tg-editprofile tg-haslayout">
			<div class="tg-otherphotos">
				<div class="tg-heading-border tg-small">				
					<h2><?php pll_e( 'invite review' );?></h2>
				</div>
				<p><?php pll_e("Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.");?></p>
				<div class="clearfix"></div>
				<?php
					$id_delete = $_GET['delete'];
					if( isset($id_delete) && $id_delete != '' ){
						
						$list_invite = get_user_meta( $user_identity, 'invite_review', true );
						$list_invite = json_decode($list_invite, true);
						$key_list = kt_search_array($list_invite, 'review_code', $id_delete);
						// var_dump($key_list); 
						if ($key_list === null) {
							$message = '<div class="alert alert-danger">'.pll__( 'This invite not exists' ).'</div>';
						}else {
							unset($list_invite[$key_list]);
							// var_dump($list_invite);
							$new_invite = json_encode($list_invite);
							update_user_meta( $user_identity, 'invite_review', $new_invite );
							$message = '<div class="alert alert-success">'.pll__( 'Remove Invite success' ).'</div>';
						}

					}
				?>
				<?php
					if(is_user_logged_in()) {
						if (isset($_POST['submit'])) {
							$patient_name = isset( $_POST['patient_name'] ) ? $_POST['patient_name'] : '';
							$headling_date = isset( $_POST['headling_date'] ) ? $_POST['headling_date'] : '';
							$review_code = isset( $_POST['review_code'] ) ? $_POST['review_code'] : '';
							$email_patient = isset( $_POST['email_patient'] ) ? $_POST['email_patient'] : '';
							if($patient_name != '' || $headling_date != '' || $review_code != '' || $email_patient != ''){
								if ( is_email( $email_patient ) ) {

									$list_invite = get_user_meta( $user_identity, 'invite_review', true );
									$list_invite = json_decode( $list_invite, true );
									// Get last id
									$last_item    = end($list_invite);
									$last_item_id = $last_item['id'];
									$new_invite = array(
										'id'  				=> ++$last_item_id,
										'patient_name' 		=> $patient_name, 
										'headling_date' 	=> $headling_date, 
										'review_code' 		=> $review_code, 
										'email_patient' 	=> $email_patient, 
										'status' 			=> 'pending', 
										);
									$list_invite[] = $new_invite;
									/*echo '<pre>';
									var_dump($list_invite);
									echo '</pre>';*/
									$subject = 'Invite review';
									$desc = $review_code;
									kt_process_email_invite_review($email_patient, $subject, $desc);

									$new_list = json_encode($list_invite);
									update_user_meta( $user_identity, 'invite_review', $new_list );
									$message = '<div class="alert alert-success">'.pll__( 'Create invite success' ).'</div>';
								}else {
									$message = '<div class="alert alert-danger">'.pll__( 'Email address is invalid' ).'</div>';
								}
							}else {
								$message = '<div class="alert alert-danger">'.pll__( 'Please fill all field' ).'</div>';
							}
						}
					}
					if( !empty( $message ) ){
						echo $message;
					}
				?>
				<form id="invite_review" method="POST" action="" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-4">
						    <div class="form-group">
						        <label for="patient_name"><?php pll_e( 'Patient Name' ); ?></label>
						        <input type="text" name="patient_name" id="patient_name" class="form-control" required data-fv-notempty-message="Patient Name is required">
						    </div>
						</div>
						<div class="col-md-4">
						    <div class="form-group">
						        <label for="hkid"><?php pll_e( 'HKID Card eg: Z7936061' ); ?></label>
						        <input type="text" name="hkid" id="hkid" class="form-control" required data-fv-notempty-message="HKID is required">
						    </div>
						</div>
						<div class="col-md-4">
						    <div class="form-group">
						        <label for="phone_number"><?php pll_e( 'Phone Number' ); ?></label>
						        <input id="teluserphone" type="text" name="phone_number" id="phone_number" class="form-control" placeholder="<?php pll_e( 'Phone Number' ); ?>">
								<script type="text/javascript">		
									jQuery(document).ready(function(e) {							
										docdirect_intl_tel_input23();
									});
								</script>
						    </div>
						</div>
					</div>
				    <div class="form-group">
				        <label for="headling_date"><?php pll_e( 'Healing date' ); ?></label>
				        <input type="text" name="headling_date" id="headling_date" class="form-control headling_date" value="" readonly>
				    </div>
				    <div class="form-group review_code_wrap">
				        <label for="review_code"><?php pll_e( 'Review code' ); ?></label>
						<div class="input-group">
					        <input type="text" name="review_code" id="review_code" class="form-control" value="" readonly="readonly">
							<div class="input-group-addon"><input type="button" class="btn btn-success gen_code" value="<?php pll_e( 'Generate code' );?>" name="submit"></div>
						</div>
				    </div>
				    <div class="form-group">
				        <label for="email_patient"><?php pll_e( 'Email patient' ); ?></label>
				        <input type="text" name="email_patient" id="email_patient" class="form-control" value="">
				    </div>
                            
					<button class="btn btn-primary invite_review" type="submit" disabled="disabled"><?php pll_e('Submit');?></button>

				</form>
			
			</div>
		</div>
		<div class="tg-editprofile tg-haslayout">
			<div class="tg-otherphotos">

				<div class="tg-heading-border tg-small non_member_title">				
					<h2><?php pll_e( 'List Invite' );?></h2>
				</div>
				<div class="clearfix"></div>
				<div class="tg-appointmenttable tg-education-detail tg-haslayout">
	                <table class="table table-hover">
	                    <thead>
	                        <tr>
	                            <th><?php pll_e('ID');?></th>
	                            <th><?php pll_e('Patient Name');?></th>
	                            <th><?php pll_e('Healing Date');?></th>
	                            <th><?php pll_e('Review Code');?></th>
	                            <th><?php pll_e('Email Patient');?></th>
								<th><?php pll_e('Status');?></th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    <?php
							$list_invite = get_user_meta( $user_identity, 'invite_review', true );
							$list_invite = json_decode($list_invite, true);
							$args 		= array('posts_per_page' => -1, 
												'author' => $user_identity,
												'post_type' => 'invite_review', 
												'post_status' => 'publish', 
												'ignore_sticky_posts' => 1,
												);
							$args['meta_query'] = $meta_query_args;
							$query 		= new WP_Query( $args );
							$posts = $query->posts; 
						?>
							<?php 
							$i=0;
							foreach($posts as $key=>$post):
								$i++;
							?>
								<tr>
	                                <td>
										<div class="checkbox">
										<?php echo $i;?>
										</div>
									</td>
	                                <td>
										<div class="checkbox">
										<?php echo $post->post_title;?>
										</div>
									</td>
	                                <td class="col_cat">
										<div class="checkbox">
										<?php echo get_post_meta($post->ID, 'headling_date', true);?>
										</div>
									</td>
	                                <td>
										<div class="checkbox">
										<?php echo get_post_meta($post->ID, 'review_code', true);?>
										</div>
									</td>
									<td>
										<div class="checkbox">
										<?php echo get_post_meta($post->ID, 'email_patient', true);?>
										</div>
									</td>
									<td>
										<div class="checkbox">
										<?php echo get_post_meta($post->ID, 'status', true);?>
										</div>
									</td>
	                            </tr> 
	                            </tr> 
							<?php
							endforeach; ?>    	
	                    </tbody>
	                </table>
	            </div>
			</div>
		</div>
	</div>
</div>
