<?php
/**
 * User Packages and settings
 * return html
 */

global $current_user, $wp_roles,$userdata,$post;
$dir_obj	= new DocDirect_Scripts();
$user_identity	= $current_user->ID;
$url_identity	= $user_identity;

if( isset( $_GET['identity'] ) && !empty( $_GET['identity'] ) ){
	$url_identity	= $_GET['identity'];
}

if (function_exists('fw_get_db_settings_option')) {
	$currency_select = fw_get_db_settings_option('currency_select');
	$currency_sign = fw_get_db_settings_option('currency_sign');
	$paypal_enable = fw_get_db_settings_option('paypal_enable');
	$enable_strip = fw_get_db_settings_option('enable_strip');
	$authorize_enable = fw_get_db_settings_option('authorize_enable');
} else{
	$currency_select = 'USD';
	$currency_sign = '$';
	$paypal_enable = '';
	$enable_strip = '';
	$authorize_enable = '';
}

if( empty( $currency_select ) ){
	$currency_select = 'USD';
	$currency_sign   = '$';
}

$current_package	= get_user_meta($url_identity, 'user_current_package', true);

if( isset( $enable_strip ) && $enable_strip === 'on' ) {
	//Strip Init
	docdirect_init_stripe_script();
}

$db_directory_type   = get_user_meta( $user_identity, 'directory_type', true);
if( isset( $db_directory_type ) && !empty( $db_directory_type ) ) {

    $terms = get_the_terms($db_directory_type, 'group_label');
    $current_group_label_slug = $terms[0]->slug;
    $list_terms = array();
    foreach ($terms as $key => $value) {
        $list_terms[] = $value->slug;
    }
}

    $today = time();
    $discount = intval(get_user_meta($user_identity,'discount',true));
    $discount_expired = get_user_meta($user_identity,'discount_expired',true);
    if (strtotime($discount_expired) >= $today) {
        $discount = ($discount > 0 && $discount <= 100) ? $discount/100 : 0 ;
    }else {
        $discount = 0;
    }
?>
<!-- <div class="tg-heading-border tg-small">
        <h3><?php //pll_e('Packages');?></h3>
    </div> -->
<div class="packages-payments">
    <?php if( isset( $current_package ) && !empty( $current_package ) ) {?>
        <?php
            $sub_title = '';
            $pac_subtitle = fw_get_db_post_option($current_package, 'pac_subtitle', true);
            if( isset( $pac_subtitle ) && !empty( $pac_subtitle ) ){
                $sub_title = ' - '. $pac_subtitle;
            }
            $user_featured = get_user_meta($url_identity, 'user_featured', true);
            $payment_profileid = get_user_meta($url_identity, 'payment_profileid', true);
            if ($user_featured != '' && $user_featured > $today) {
        ?>
        <div class="tg-bordertop tg-haslayout">
            <a class="pack" href="javascript:;">
                <span>Current - <?php echo get_the_title($current_package);?><?php echo $sub_title;?></span>
            </a>
            <?php if ($payment_profileid != '') {?>
            <a class="pack cancel_class" href="javascript:;" data-toggle="modal" data-target=".tg-confirmpopup">
                <span>Suspend Membership</span>
            </a>
            <?php }?>
        </div>
    <?php }?>
    <?php }else {
        $user_featured = get_user_meta($url_identity, 'user_featured', true);
        $left = $user_featured - $today;
        $remaining_days = ceil($left/86400);
        if ($user_featured != '' && $user_featured > $today) {
        ?>
        <div class="tg-bordertop tg-haslayout">
            <a class="pack" href="javascript:;">
                <span>Current Package - Free trial - days left <?php echo $remaining_days;?></span>
            </a>
        </div>
        <?php
        }
    }?>
    <form action="#" method="post" class="renew-package">
        <div class="row grid">		
        <?php	
            $args = array('posts_per_page' => '-1', 
				'post_type' => 'directory_packages', 
				'orderby' => 'ID', 
				'post_status' => 'publish',
				'suppress_filters' => false
			);
            if( !in_array('company', $list_terms) &&            
                !in_array('medical-centre', $list_terms) &&
                !in_array('hospital-type', $list_terms) &&           
                !in_array('scans-testing', $list_terms)
            ) {
                $vl = 'profestional';
            }else {
                if ($db_directory_type = '126' || get_the_title($db_directory_type) == 'Private Hospital' ) {
                    $vl = 'private_hospital';
                } else {
                    $vl = 'company';
                }
            }
            $args['meta_query'] = array(
                array(
                    'key'     => 'medi_package',
                    'value'   => $vl,
                    'compare'   => '='
                )
            );
            $cust_query = get_posts($args);
        
            if (isset($cust_query) && is_array($cust_query) && !empty($cust_query)) {
                $ounterpack	= 0;	
                foreach ($cust_query as $key => $pack) {
                    $active	= isset( $ounterpack ) && $ounterpack === 0 ? 'checked' : '';
                    $price = fw_get_db_post_option($pack->ID, 'price', true);
                    $price = $price - $price*$discount;
                    $duration = fw_get_db_post_option($pack->ID, 'duration', true);
                    $featured = fw_get_db_post_option($pack->ID, 'featured', true);
                    $pac_subtitle = fw_get_db_post_option($pack->ID, 'pac_subtitle', true);
                    $short_description = fw_get_db_post_option($pack->ID, 'short_description', true);
					$features = fw_get_db_post_option($pack->ID, 'features', true);
                    
					$active_package	= '';
					$active_class	= '';					
                    
                    $sl_pk = esc_attr( 'Select Plan' );
                    $sl_pk2 = '';
                    $cancel_class = '';
                    $modal = '';

                    $user_current_package = get_user_meta($url_identity, 'user_current_package', true);
                    $user_featured = get_user_meta($url_identity, 'user_featured', true);
                    $payment_profileid = get_user_meta($url_identity, 'payment_profileid', true);
                    $today = time();

                    if( isset( $current_package ) && !empty( $current_package ) ) {
                        if ($payment_profileid != '') {
                            $current_duration = fw_get_db_post_option($current_package, 'duration', true);
                            $sl_pk = esc_attr( 'Upgrade Plan' );
                            if ($current_duration > $duration) {
                                $sl_pk = esc_attr( 'Downgrade Plan' );
                            }
                        }
                    }

                    $disabled = '';
					if( isset( $current_package ) && $current_package == $pack->ID ) {
						// $active_package	= 'checked';
                        if ($user_featured != '' && $user_featured > $today) {
                            $active_class   = 'active';
                            $sl_pk2 = esc_attr( 'Current Plan' );
                            if ($payment_profileid != '') {
                                $cancel_class = 'cancel_class';
                                $sl_pk = esc_attr( 'Current Plan' );
                                $disabled = 'disabled';
                                $modal = 'data-toggle="modal" data-target=".tg-confirmpopup"';
                            }
                        }
					}
                    ?>
                    
                    <div class="col-md-4 col-sm-6 col-xs-6 tg-packageswidth">
                        <div class="tg-checkbox">
                            <input type="radio" <?php echo esc_attr( $active_package );?> <?php echo esc_attr( $disabled );?> name="packs" value="<?php echo esc_attr( $pack->ID );?>" id="pack-<?php echo esc_attr( $pack->ID );?>">
                            <label for="pack-<?php echo esc_attr( $pack->ID );?>">
                                <div class="tg-packages <?php echo esc_attr( $active_class );?>">
                                    
                                    <?php if( isset( $featured ) && !empty( $featured ) ){?>
                                        <span class="tg-featuredicon"><em class="fa fa-bolt"></em></span>
                                    <?php }?>
                                    <h2><?php echo esc_attr( get_the_title($pack->ID) );?></h2>
                                    <?php if( isset( $pac_subtitle ) && !empty( $pac_subtitle ) ){?>
                                    <h3><?php echo esc_attr( $pac_subtitle );?></h3>
                                    <?php }?>
                                    <strong><i><?php echo esc_attr( $currency_sign );?></i><?php echo esc_attr( $price );?></strong>
                                    <?php if( isset( $duration ) && !empty( $duration ) ){?>
                                        <p><?php echo esc_attr( $duration );?><?php echo ( $duration > 1 ? esc_html__( ' Days' ) : esc_html__( 'Day' ));?></p>
                                    <?php }?>
                                    <?php if( isset( $short_description ) && !empty( $short_description ) ){?>
                                        <p><?php echo esc_attr( $short_description );?></p>
                                    <?php }?>
                                    <?php if( isset( $features ) && !empty( $features ) ){?>
                                        <ul>
                                            <?php foreach( $features as $key=> $value ){?>
                                                <li><?php echo esc_attr( $value );?></li>
                                            <?php }?>
                                        </ul>
                                    <?php }?>
                                    <?php //if( $sl_pk2 != '' ){?>
                                        <!-- <span class="current_package"><?php //echo $sl_pk2;?></span> -->
                                    <?php //}?>
                                    <span class="tg-btn-invoices" <?php echo $modal;?>>
                                        <?php echo $sl_pk;?>                                        
                                    </span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <?php $ounterpack++;
                }
            }
            ?>
        </div>
        <div class="gateways-settings">
        <div class="notification_wrap"><div class="notification_text"></div></div>
        <div class="membership-price-header"><?php pll_e('Payment Options');?></div>
        <?php if( isset( $paypal_enable ) && $paypal_enable === 'on' ) {?>
            <div class="system-gateway">
                <label for="doc-payment-paypal"><input name="gateway" type="radio" id="doc-payment-paypal" value="paypal"><?php pll_e('Paypal');?></label>
            </div>
        <?php }?>
        <?php if( isset( $enable_strip ) && $enable_strip === 'on' ) {?>
            <div class="system-gateway">
                <label for="doc-payment-strip"><input name="gateway" type="radio" id="doc-payment-strip" value="stripe"><?php pll_e('Credit Card( Stripe )');?></label>
            </div>
        <?php }?>
        <?php if( isset( $authorize_enable ) && $authorize_enable === 'on' ) {?>
            <div class="system-gateway">
                <label for="doc-payment-authorize"><input name="gateway" type="radio" id="doc-payment-authorize" value="authorize"><?php pll_e('Authorize.Net');?></label>
            </div>
        <?php }?>
        <div class="system-gateway">
            <?php wp_nonce_field('docdirect_renew_nounce', 'renew-process'); ?>
            <button type="submit" class="tg-btn do-process-payment"><?php pll_e('Subscribe Now');?></button>
        </div>
    </div>
    </form>
</div>

<?php
function kt_add_modal_footer2() {
?>
<div class="modal fade tg-confirmpopup tg_cancel_package">
  <div class="tg-modal-content" role="document">
        <div class="confirmbox">
            <h5><?php pll_e( 'Do you want to cancel current package?' ) ?></h5>
            <a class="yes" href="javascript:;">Yes</a>
            <a class="" href="javascript:;" data-dismiss="modal">No</a>
        </div>
  </div>
</div>
<?php
}
add_action('wp_footer', 'kt_add_modal_footer2');