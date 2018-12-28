<form class="tg-formleavereview form-review new_review">
	<div class="col-sm-12"><div class="message_contact theme-notification"></div></div>
	<?php
		global $array_review;
		global $array_emoji;
	?>
	<?php foreach ($array_review as $key => $rv) {?>
    <div class="col-sm-6 rating_type">
    	<img src="<?php echo get_stylesheet_directory_uri();?>/images/review/<?php echo $rv['img'];?>.png">
    	<div class="content">
	    	<label><?php echo $rv['title'];?></label>
	    	<div class="clearfix"></div>
	    	<small class="form-text"><?php echo $rv['small'];?></small>
	    	<div class="clearfix"></div>
	    	<?php foreach ($array_emoji as $point => $name) {?>
		    	<input type="radio" id="<?php echo $name.'-'.$key;?>" name="user_rating[<?php echo $rv['name'];?>]" value="<?php echo $point;?>">
		    	<label class="drinkcard-cc <?php echo $name;?>" for="<?php echo $name.'-'.$key;?>"></label>
	    	<?php }?>
	    </div>
    </div>
	<?php }?>

    <div class="clearfix"></div>
    <div class="col-sm-12">
      <div class="form-group">
      </div>
    </div>
    <div class="col-sm-12">
      <div class="form-group">
        <input type="text" name="user_subject" class="form-control" placeholder="<?php pll_e('Review Title');?>">
      </div>
    </div>
    <div class="col-sm-12">
      <div class="form-group">
        <textarea class="form-control" name="user_description" placeholder="<?php pll_e('Review Description *');?>"></textarea>
      </div>
    </div>
    <input type="hidden" name="user_to" class="user_to" value="<?php echo esc_attr( $current_author_profile->ID );?>" />
    <div class="col-sm-12">
       	<button class="tg-btn kt_make-review" type="submit"><?php pll_e('Submit Review');?></button>
    </div>
</form>