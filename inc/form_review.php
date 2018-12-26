<form class="tg-formleavereview form-review new_review">
	<?php
		$array_review = [
			'reccomend' => [
				'img' => 'reccomend',
				'title' => pll__('Recomend to others?'),
				'small' => pll__('Would you recomend to another patient?'),
				'name' => 'recommendation'
			],
			'support' => [
				'img' => 'support',
				'title' => pll__('Supporting Staff'),
				'small' => pll__('Rate the supporting staff of your provider'),
				'name' => 'supporting_staff'
			],
			'waiting' => [
				'img' => 'waiting_time',
				'title' => pll__('Waiting time'),
				'small' => pll__('How long was your appointment waiting time'),
				'name' => 'waiting_time'
			],
			'bedside_manner' => [
				'img' => 'bedside_manner',
				'title' => pll__('Bedside Manner'),
				'small' => pll__('Would you recomend to another patient?'),
				'name' => 'bedside_manner'
			],
			'facilities' => [
				'img' => 'facilities',
				'title' => pll__('Facilities'),
				'small' => pll__('Would you recomend to another patient?'),
				'name' => 'facilities'
			],
			'accessibility' => [
				'img' => 'bedside_manner',
				'title' => pll__('Accessibility'),
				'small' => pll__('Would you recomend to another patient?'),
				'name' => 'bedside_manner'
			],
		];
		$array_emoji = ['bad', 'ok', 'good', 'great'];
		// var_dump($array_review);
	?>
	<?php foreach ($array_review as $key => $rv) {?>
    <div class="col-sm-6">
    	<img src="<?php echo get_stylesheet_directory_uri();?>/images/review/<?php echo $rv['img'];?>.png">
    	<label><?php echo $rv['title'];?></label>
    	<div class="clearfix"></div>
    	<small class="form-text text-muted"><?php echo $rv['small'];?></small>
    	<?php foreach ($array_emoji as $name) {?>
	    	<input type="radio" id="<?php echo $name.'-'.$key;?>" name="detail_rating[<?php echo $rv['name'];?>]" value="0">
	    	<label class="drinkcard-cc <?php echo $name;?>" for="<?php echo $name.'-'.$key;?>"></label>
    	<?php }?>
    </div>
	<?php }?>

    <div class="clearfix"></div>
    <div class="col-sm-6">
      <div class="form-group">
        <input type="text" name="user_subject" class="form-control" placeholder="<?php pll_e('Review Title');?>">
      </div>
    </div>
    <div class="col-sm-12">
      <div class="form-group">
        <textarea class="form-control" name="user_description" placeholder="<?php pll_e('Review Description *');?>"></textarea>
      </div>
    </div>
    <div class="col-sm-12">
                          <button class="tg-btn" type="button" data-toggle="modal" data-target=".tg-user-modal"><?php pll_e('Submit Review');?></button>
    </div>
</form>
<style type="text/css">
.drinkcard-cc{
    cursor:pointer;
    background-size:contain;
    background-repeat:no-repeat;
    display:inline-block;
    width:22px;height:22px;
    -webkit-transition: all 50ms ease-in;
       -moz-transition: all 50ms ease-in;
            transition: all 50ms ease-in;
    -webkit-filter: brightness(1) grayscale(1) opacity(.9);
       -moz-filter: brightness(1) grayscale(1) opacity(.9);
            filter: brightness(1) grayscale(1) opacity(.9);
}
.drinkcard-cc:hover{
    -webkit-filter: brightness(1.2) grayscale(.5) opacity(.9);
       -moz-filter: brightness(1.2) grayscale(.5) opacity(.9);
            filter: brightness(1.2) grayscale(.5) opacity(.9);
}
.drinkcard-cc:hover, .new_review input[type="radio"]:checked +.drinkcard-cc{
    -webkit-filter: none;
       -moz-filter: none;
            filter: none;
}
.new_review input[type="radio"] {
    margin:0;padding:0;
    -webkit-appearance:none;
       -moz-appearance:none;
            appearance:none;
}
.bad {background-image: url('<?php echo get_stylesheet_directory_uri();?>/images/review/bad_active.png')}
.ok {background-image: url('<?php echo get_stylesheet_directory_uri();?>/images/review/ok_active.png')}
.good {background-image: url('<?php echo get_stylesheet_directory_uri();?>/images/review/good_active.png')}
.great {background-image: url('<?php echo get_stylesheet_directory_uri();?>/images/review/great_active.png')}

.cc-selector-2 input[type="radio"] {
    position:absolute;
    z-index:999;
}
</style>