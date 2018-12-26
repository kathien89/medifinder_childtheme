<?php
/**
 * User Security Settings
 * return html
 */

global $current_user, $wp_roles,$userdata,$post;
$dir_obj	= new DocDirect_Scripts();
$user_identity	= $current_user->ID;
$url_identity	= $user_identity;
if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
	$url_identity	= $_GET['identity'];
}
$delete_account_text 	  = fw_get_db_settings_option('delete_account_text');
$privacy		= docdirect_get_privacy_settings($url_identity);
$verify_user    = get_user_meta( $url_identity, 'verify_user', true);
$verify_user    = 'on';
?>
<div class="tg-myaccount tg-haslayout privacy-settings">
    <div class="tg-heading-border tg-small">
        <h2><?php pll_e('Privacy Settings');?></h2>
    </div>
    <div class="privacy-wraper">
    <form action="#" method="post" class="tg-form-privacy">
        <div class="form-group">  
            <div class="tg-privacy"> 
              <div class="tg-iosstylcheckbox">
                <input type="hidden" name="privacy[phone]">
                <?php if ($verify_user == 'on') {?>
                  <input type="checkbox" class="privacy-switch" <?php echo isset( $privacy['phone'] ) && $privacy['phone'] === 'on' ? 'checked':'';?>  name="privacy[phone]" id="tg-phone">
                <?php }else {?>
                  <input disabled="disabled" type="checkbox" class="privacy-switch" name="privacy[phone]" id="tg-phone">
                <?php }?>
                <label for="tg-phone" class="checkbox-label" data-private="<?php esc_attr_e('Private');?>" data-public="<?php esc_attr_e('Public');?>"></label>
              </div>
              <span class="tg-privacy-name"><?php pll_e('Phone Number');?></span>
              <p><?php esc_attr_e('Make the phone visible on my public profile');?></p>
            </div>
        </div>
        <div class="form-group">  
            <div class="tg-privacy"> 
              <div class="tg-iosstylcheckbox">
                <input type="hidden" name="privacy[email]">
                <?php if ($verify_user == 'on') {?>
                  <input type="checkbox" class="privacy-switch" <?php echo isset( $privacy['email'] ) && $privacy['email'] === 'on' ? 'checked':'';?>  name="privacy[email]" id="tg-email">
                <?php }else {?>
                  <input disabled="disabled" type="checkbox" class="privacy-switch" name="privacy[email]" id="tg-email">
                <?php }?>
                <label for="tg-email" class="checkbox-label" data-private="<?php esc_attr_e('Private');?>" data-public="<?php esc_attr_e('Public');?>"></label>
              </div>
              <span class="tg-privacy-name"><?php pll_e('Email');?></span>
              <p><?php esc_attr_e('Make the email address visible on my public profile');?></p>
            </div>
        </div>
        <div class="form-group">  
            <div class="tg-privacy"> 
              <div class="tg-iosstylcheckbox">
                <input type="hidden" name="privacy[contact_form]">
                <?php if ($verify_user == 'on') {?>
                  <input type="checkbox" class="privacy-switch" <?php echo isset( $privacy['contact_form'] ) && $privacy['contact_form'] === 'on' ? 'checked':'';?>  name="privacy[contact_form]" id="tg-contact_form">
                <?php }else {?>
                  <input disabled="disabled" type="checkbox" class="privacy-switch" name="privacy[contact_form]" id="tg-contact_form">
                <?php }?>
                <label for="tg-contact_form" class="checkbox-label" data-private="<?php esc_attr_e('Private');?>" data-public="<?php esc_attr_e('Public');?>"></label>
              </div>
              <span class="tg-privacy-name"><?php pll_e('Contact Form');?></span>
              <p><?php esc_attr_e('Make the contact form visible on my public profile');?></p>
            </div>
        </div>
   </form>
</div>