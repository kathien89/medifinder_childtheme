<?php
/**
 * User Schedules
 * return html
 */


global $current_user, $wp_roles,$userdata,$post;
$user_identity= $current_user->ID;
$db_schedules	= array();
$db_schedules = get_user_meta( $user_identity, 'schedules', true);

$checked	= '';
if( isset( $db_schedules['all'] ) && $db_schedules['all'] === 'on' ) {
	$checked	= 'checked';
}
$schedules	= docdirect_get_week_array();
$count = count($db_schedules);
// update_user_meta( $user_identity, 'schedules', '');
?>
<div class="tg-docschedule tg-haslayout">
	<div class="tg-heading-border tg-small">
		<h3><?php pll_e('update Schedule');?></h3>
	</div>
    <p><strong><?php pll_e('Note: Leave fields empty to show  day closed.');?></strong></p>
    
	<form class="form-docschedule" id="form-docschedule">
		<fieldset class="row">

			<div class="col-xs-12">
				<div class="form-group">
					<a href="javascript:;" class="btn btn-success btn_add_schedule"><?php pll_e('Add schedule');?></a>
				</div>
			</div>
        	
			<?php 
			for ($i=0; $i < $count ; $i++) { ?>
				<div class="schedule">
					<div class="col-xs-12">
						<div class="tg-heading-border tg-small">
							<h4>Schedule #<?php echo $j=$i+1;?></h4>
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-9">
						<div class="form-group">
							<?php $hospital_name = $db_schedules[$i]['hospital_name'];?>
							<input type="text" name="schedules[<?php echo esc_attr( $i );?>][hospital_name]" value="<?php echo esc_attr( $hospital_name );?>" class="form-control" placeholder="<?php pll_e('Hospital Name');?>">
						</div>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-3 text-right">
						<div class="form-group">			
							<a href="javascript:;" class="btn btn-danger btn_remove_schedule"><i class="fa fa-remove"></i></a>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php
					if( isset( $schedules ) && !empty( $schedules ) ) {
						echo '<div class="row">';
						foreach( $schedules as $key => $value )	{
							
							$start_time	= isset( $db_schedules[$i][$key.'_start'] ) ? $db_schedules[$i][$key.'_start'] : '';
							$end_time	= isset( $db_schedules[$i][$key.'_end'] ) ? $db_schedules[$i][$key.'_end'] : '';
							
						?>
						<div class="col-xs-6 col-sm-12">
							<div class="col-md-2 col-sm-12 col-xs-12">
								<div class="form-group">
									<label><?php echo esc_attr( $value );?></label>
								</div>
							</div>
							<div class="col-md-5 col-sm-6 col-xs-12">
								<div class="form-group">
									<input type="text" name="schedules[<?php echo esc_attr( $i );?>][<?php echo esc_attr( $key );?>_start]" value="<?php echo esc_attr( $start_time );?>" class="form-control schedule-pickr" placeholder="<?php pll_e('start time');?>">
									<i class="fa fa-clock-o"></i> </div>
							</div>
							<div class="col-md-5 col-sm-6 col-xs-12">
								<div class="form-group">
									<input type="text" name="schedules[<?php echo esc_attr( $i );?>][<?php echo esc_attr( $key );?>_end]" value="<?php echo esc_attr( $end_time );?>" class="form-control schedule-pickr" placeholder="<?php pll_e('end time');?>">
									<i class="fa fa-clock-o"></i> </div>
							</div>
						</div>
					<?php }
						echo '</div>';
					}?>
				</div>
			<?php }?>

			<div class="col-sm-offset-2 col-sm-10 append_desc">
                
                <div class="form-group">
                    <select name="time_format" class="form-control">
                        <option value="12hour"><?php pll_e("Show Time in 12-hour clock");?></option>
                        <option value="24hour"><?php pll_e("Show Time in 24-hour clock");?></option>
                    </select>    
                </div>
				<div class="button-wrapper"><button type="submit" class="tg-btn pull-left update-schedules"><?php pll_e('update');?></button></div>
			</div>
		</fieldset>
	</form>
</div>
<script>
	jQuery(document).ready(function(e) {
        //Time Picker
		jQuery('.schedule-pickr').datetimepicker({
		  datepicker:false,
		  format:'H:i'
		});
    });
</script>
