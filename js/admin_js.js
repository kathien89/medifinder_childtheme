jQuery(document).ready(function($){

	function handle_images( frameArgs, callback ){
		var SM_Frame = wp.media( frameArgs );

		SM_Frame.on( 'select', function() {

			callback( SM_Frame.state().get('selection') );
			SM_Frame.close();
		});

		SM_Frame.open();	
	}

	$(document).on( 'click', '.featured-image', function(e) {
		e.preventDefault();

		var frameArgs = {
			multiple: false,
			title: 'Select Featured Image',
		    library: {
		            type: [ 'image' ]
		    },
		};

		handle_images( frameArgs, function( selection ){
			model = selection.first();
			$('#post_featured_image').val( model.id );
			var img = model.attributes.url;
			var ext = img.substring(img.lastIndexOf('.'));
			img = img.replace( ext, '-150x150'+ext );
			$('.featured-image-wrap').html( '<img src="'+img+'" class="img-responsive"/>' );
		});
	});

	//Add Video
	jQuery(document).on('click','.add-new-video',function(){

		var max_video = $(this).data('max');
		var load_video = wp.template( 'load-video' );
		var counter	= jQuery( '.all_videos > div' ).length;
		if (counter +1 >= max_video) {
			$(this).parent().hide();
		}
		var load_video	= load_video(counter);
		jQuery( '.all_videos' ).append(load_video);
		
	
	});
	jQuery(document).on('click','.remove_video',function(){

		$(this).parent().remove();
		
		var max_video = $('.add-new-video').data('max');
		var counter	= jQuery( '.all_videos > div' ).length;
		if (counter < max_video) {
			$('.add-new-video').parent().show();
		}
	
	});

	jQuery(document).on('click','.add-new-schedule',function(){
		var load_schedule_tpl = wp.template( 'load-schedule' );
		var counter	= jQuery( '#form-docschedule > .schedule' ).length;
		var list	= load_schedule_tpl(counter);
		jQuery( this ).before(list);
		
	});

	jQuery(document).on('click', '.btn_remove_schedule', function() {

        // $("body").css("cursor", "wait");
		var _this = $(this);
		closest = _this.closest('.schedule');
    	nextAll = closest.nextAll('.schedule');
		nextAll.each(function(index){
			var _this = $(this);
			_this.find('input, textarea').attr('name', function(i, val) {
			    return val.replace(/\d+/, function(n) {
			        return --n;
			    })
			});
			_this.find('.parent-heading').find('h3').text(function(i, val) {
			    return val.replace(/\d+/, function(n) {
			        return --n;
			    })
			});
		});
    	closest.remove();
		
    	// $("body").css("cursor", "default");

    });


	function runajax(form_selector) {
		var current_user_id = jQuery('.current_user_id').val();
		// console.log('aloo');	
		var s_div = form_selector.find('#search_string');
		var btn_div = form_selector.find('.search_user_btn');
    	var s_val = form_selector.find('#search_string').val();
    	form_data = form_selector.serialize();
    	response_div = form_selector.find('.response');
    	response_div.html('').css('visibility', 'hidden');
    	if ( $.trim(s_val) != '' ) {
		    
			// $('body').append(loder_html);

      		jQuery.ajax({
	            type: "POST",
	            url: ajaxurl,
	            data: form_data + '&current_user_id=' + current_user_id + '&action=search_user',
            	dataType: "json",
	            success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();
	                response_div.html(response.data).css('visibility', 'visible');
	            }
	        });
    	}
	}

    $('#search_string').keyup(function() {
    	var form_selector = $(this).closest('form');
	    setTimeout(function(){
	    	runajax(form_selector);
	    }, 1000 );
	});

	$('body').on('click', '.search_user_btn', function(e) {
   		e.preventDefault;
        var _this = jQuery(this);
    	var form_selector = $(this).closest('form');
    	response_div = $('.response');
    	var s_val = $('#search_string').val();
		var current_user_id = jQuery('.current_user_id').val();
        var dataString = 'search_string=' + s_val + '&current_user_id=' + current_user_id + '&action=search_user';

    	if ( $.trim(s_val) != '' ) {

			_this.parent().append("<i class='fa fa-spinner fa-spin'></i>");
        
      		jQuery.ajax({
	            type: "POST",
	            url: ajaxurl,
	            data: dataString,
            	dataType: "json",
	            success: function(response) {
	            	_this.parent().find('i').remove();
					jQuery('body').find('.docdirect-site-wrap').remove();
	                response_div.html(response.data).show();
	            }
	        });
      	}
    });	

	$('body').on('click', '.close_response_wrap', function() {
    	$(this).closest('.response').hide();
	});

	$(document).on('click', '#tabadd .btn.post-form', function(e) {
	    e.preventDefault(); 
	    // alert('clicked'); 
		var current_url      = document.URL;     // Returns full URL
		var _this = $(this);
		var post_title = _this.find('input[name=post_title]').val();
		var tagline = _this.find('input[name=tagline]').val();
		var email = _this.find('input[name=email]').val();
		var specialties = _this.find('textarea[name=specialties]').val();
		var group_aff = _this.find('select[name=group_aff]').val();
		var post_featured_image = _this.find('input[name=post_featured_image]').val();

		// $('body').append(loder_html);
		jQuery.ajax({
	            type: "POST",
	            url: ajaxurl,
	            data: {
	            	'post_title': post_title,
	            	'tagline': tagline,
	            	'email': email,
	            	'specialties': specialties,
	            	'group_aff': group_aff,
	            	'post_featured_image': post_featured_image,
	            	'action': 'submit_aff'
	            },
            	dataType: "json",
	            success: function(response) {
					// jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						setTimeout(function(){location.href = current_url} , 1000); 
					}else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
	            }
	        });
	});

	$('body').on('click', '.edit_group', function() {
		var _this = $(this);
		var type_group = $(this).data('group');
    	var article = $(this).closest('.tg-doctor-profile1');
    	var user_to = article.attr('id').match(/user-(\d+)/)[1];
    	var post_id = parseInt(article.data('post_id'));

    	var button_gr = $(this).closest('.button_gr');
        // console.log(user_to);
        if ( $.trim(user_to) != '' ) {
			// $('body').append(loder_html);
      		jQuery.ajax({
	            type: "POST",
	            url: ajaxurl,
	            data: {
	            	'post_id': post_id,
	            	'type_group': type_group,
	            	'action': 'edit_group'
	            },
            	dataType: "json",
	            success: function(response) {
					// jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						
						setTimeout(function(){window.location.reload();} , 2000);
					}else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
	            }
	        });
    	}
    });
	$('body').on('click', '.remove_approve_btn', function() {

		//Process dadtabase item
		var _this = $(this);
    	var article = $(this).closest('.tg-doctor-profile1');
    	var post_id = article.data('post_id');
				    	
		jQuery.confirm({
			'title': 'Delete Affiliation',
			'message': 'Are you sure you want to delete this Affiliation?',
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
				        
						// $('body').append(loder_html);
			      		jQuery.ajax({
				            type: "POST",
				            url: ajaxurl,
				            data: {
				            	'post_id': post_id,
				            	'action': 'remove_approve_aff'
				            },
			            	dataType: "json",
				            success: function(response) {
								jQuery('body').find('.docdirect-site-wrap').remove();
				                article.remove();
								jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
								
				            }
				        });
					}
				},
				'No': {
					'class': 'gray',
					'action': function () {
						return false;
					}	// Nothing to do in this case. You can as well omit the action property.
				}
			}
		});
    });

	$('body').on('click', '.request_aff', function() {
		var _this = $(this);
		var type_group = $(this).data('group');
    	var article = $(this).closest('.tg-doctor-profile1');
    	// var waiting_list = $('.waiting_list.list_user').children('div');
    	var user_to = article.attr('id').match(/user-(\d+)/)[1];

		var current_user_id = jQuery('.current_user_id').val();

    	var button_gr = $(this).closest('.button_gr');
        // console.log(user_to);
        if ( $.trim(user_to) != '' ) {
			// $('body').append(loder_html);
      		jQuery.ajax({
	            type: "POST",
	            url: ajaxurl,
	            data: {
	            	'current_user_id': current_user_id,
	            	'user_to': user_to,
	            	'type_group': type_group,
	            	'action': 'admin_request_aff'
	            },
            	dataType: "json",
	            success: function(response) {
					// jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						article.remove();
						setTimeout(function(){window.location.reload();} , 2000);
					}else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
	            }
	        });
    	}
    });

	$(document).on('click', 'form#submit_aff .btn-primary.post-form, div#tabadd .btn-primary.post-form', function(e) {
	    e.preventDefault(); 
	    // alert('clicked'); 
		var current_url      = document.URL;     // Returns full URL
		var _this = $(this).parents('#tabadd');
		var post_title = _this.find('input[name=post_title]').val();
		var tagline = _this.find('input[name=tagline]').val();
		var email = _this.find('input[name=aff_email]').val();
		var specialties = _this.find('textarea[name=specialties]').val();
		var group_aff = _this.find('select[name=group_aff]').val();
		var post_featured_image = _this.find('input[name=post_featured_image]').val();

		// $('body').append(loder_html);
		jQuery.ajax({
	            type: "POST",
	            url: ajaxurl,
	            data: {
	            	'post_title': post_title,
	            	'tagline': tagline,
	            	'email': email,
	            	'specialties': specialties,
	            	'group_aff': group_aff,
	            	'post_featured_image': post_featured_image,
	            	'action': 'submit_aff'
	            },
            	dataType: "json",
	            success: function(response) {
					// jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						setTimeout(function(){location.href = current_url} , 1000); 
					}else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
	            }
	        });
	});


	//Date Picker
	jQuery('.discount-pickr').datetimepicker({
	  timepicker:false,
	  format:'F j, Y'
	});


jQuery.docdirect_init_map = function(map_lat,map_lng){

	var mapwrapper = jQuery('#location-pickr-map');

	if(typeof(scripts_vars) != "undefined" && scripts_vars !== null) {
		var dir_latitude	 = scripts_vars.dir_latitude;
		var dir_longitude	 = scripts_vars.dir_longitude;
		var dir_map_type	 = scripts_vars.dir_map_type;
		var dir_close_marker		= scripts_vars.dir_close_marker;
		var dir_cluster_marker		= scripts_vars.dir_cluster_marker;
		var dir_map_marker			= scripts_vars.dir_map_marker;
		var dir_cluster_color		= scripts_vars.dir_cluster_color;
		var dir_zoom				= scripts_vars.dir_zoom;
		var dir_map_scroll			= scripts_vars.dir_map_scroll;
	} else{
		var dir_latitude	 		= 51.5001524;
		var dir_longitude			= -0.1262362;
		var dir_map_type	 		= 'ROADMAP';
		var dir_zoom				= 12;
		var dir_map_scroll			= false;
	}
	
	if(dir_map_type == 'ROADMAP'){
		var map_id = google.maps.MapTypeId.ROADMAP;
	} else if(dir_map_type == 'SATELLITE'){
		var map_id = google.maps.MapTypeId.SATELLITE;
	} else if(dir_map_type == 'HYBRID'){
		var map_id = google.maps.MapTypeId.HYBRID;
	} else if(dir_map_type == 'TERRAIN'){
		var map_id = google.maps.MapTypeId.TERRAIN;
	} else {
		var map_id = google.maps.MapTypeId.ROADMAP;
	}
	
	var scrollwheel	= true;
	
	if( dir_map_scroll == 'false' ){
		scrollwheel	= false;
	}

	mapwrapper.gmap3({
	  map:{
		  options:{
			panControl: false,
			scaleControl: false,
			navigationControl: false,
			draggable:true,
			scrollwheel: scrollwheel,
			streetViewControl: false,
			center:[map_lat,map_lng],
			zoom:  parseInt(dir_zoom),
			mapTypeId: map_id,
			mapTypeControl: true,
			mapTypeControlOptions: {
			  style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
			  position: google.maps.ControlPosition.RIGHT_BOTTOM
			},
			zoomControl: true,
			zoomControlOptions: {
			  position: google.maps.ControlPosition.LEFT_BOTTOM
			},
		  },
		  callback:function(){
			setTimeout(function(){
				jQuery.docdirect_map_fallback();
			},300);
		  }
	  },
	  marker:{
		values:[{
			latLng:[map_lat,map_lng],
		}],
		options:{
		  draggable: true
		},
		events:{
			dragend: function(marker){
				jQuery('#location-latitude').val(marker.getPosition().lat());
				jQuery('#location-longitude').val(marker.getPosition().lng());
			},
		},
	  }

	});
};


});