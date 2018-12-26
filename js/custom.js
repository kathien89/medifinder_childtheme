jQuery(document).ready(function($){

	var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
   		
   	var mini_loder_html	= '<div class="docdirect-loader-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	
	$('.warning_mes').hide();
	$('#doctor_approve_booking input[type=radio][name=location]').on('change', function() {
		if ($(this).hasClass('default')) {
    		$('.warning_mes').hide();
		}else {
    		$('.warning_mes').show();
		}
    });

	setTimeout(function() {
	    $('#mfesecure-ts-image').hide();
	}, 15000 );
	
	jQuery('body').on('click','.yourself',function(e){
		jQuery.sticky('You cannot book yourself', {classList: 'important', speed: 200, autoclose: 5000});
	});
	
	jQuery('.tg-reviews.tg-reviewlisting').on('click','.button .expand',function(e){
		$('.tg-reviews.tg-reviewlisting .reply_hidden').hide();
		$(this).parent().toggleClass('open').siblings('.star_detail').toggle();
	});
	jQuery('.tg-reviews.tg-reviewlisting').on('click','.button .reply_btn',function(e){
		$('.button .expand').removeClass('open');
		if ($(this).hasClass('open_reply_form')) {			
			reply_div = $('#reply_review');
			reply_div.show();
			$(this).parent().siblings('.reply_hidden').append(reply_div);
		}
		$(this).parent().siblings('.reply_hidden').toggle();
	});
	
	//Delete banner
	jQuery('.tg-docimg').on('click','.del-banner_mobile',function(e){
		e.preventDefault();
		var _this 	= jQuery(this);
		var dataString = 'action=docdir_delete_user_banner_mobile';
		jQuery('body').append(loder_html);
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				if( response.type == 'error' ) {
					jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
				} else{
					_this.parents('.tg-docimg').find('.user-banner_mobile img').attr('src', response.avatar);
					_this.parents('.tg-docimg').find('.del-banner_mobile').hide();
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
				}
			}
		});
		return false;
	});


    jQuery(document).on('change', '.start_time select', function (event) {
	    var value_s = this.value;
	    console.log(value_s);
	    $(".end_time select option").each(function () {
            if ( this.value <= value_s ) {
                this.disabled = true;
            }else {
                this.disabled = false;
            }
        });
	});
    jQuery(document).on('change', '.end_time select', function (event) {
	    var value_s = this.value;
	    console.log(value_s);
	    $(".start_time select option").each(function () {
            if ( this.value >= value_s ) {
                this.disabled = true;
            }else {
                this.disabled = false;
            }
        });
	});

   	if( $( window ).width() == 768 ) {
   		if($('#tg-sidebar').length > 0){
			var window_offset = $('#tg-sidebar').offset().top;
			var hh = $('#tg-sidebar').height();
			var endof = window_offset+hh;
			console.log(endof);
			$('.tg-userdetail > div:last-child > div').each(function(index){
				if (!$(this).hasClass('tg-haslayout')) {
					this_offset = $(this).offset().top + $(this).height();
					console.log(this_offset);
					var parent = $(this).parent();
					if (this_offset >= endof) {
						// $(this).addClass('vkl').before('</div><div class="col-xs-12">');
						$(this).nextAll().andSelf().wrapAll("<div class='col-xs-12 wrap_end'></div>");
						// $(this).prependTo('.wrap_end');
						$('.wrap_end').insertAfter('.tg-userdetail > div:last-child');

						return false;
					}
				}
			});
   		}
	}

	$('#aboutab .desc').each(function(){
		var _length = $(this).find('p').length;
		console.log(_length);
		if (_length > 5) {
			$(this).find('p').eq(5).before('<a class="more_button" href="javascript:;">'+confirm_vars.more_text+'</a>');
			$(this).find('.more_button').nextAll().hide();
		}
	});

	jQuery(document).on('click', '#aboutab .desc .more_button', function (event) {
		$(this).nextAll().toggle();
	});

	jQuery(document).on('click', '.open_approve_modal', function (event) {

		id = $(this).data('id');
		$('#doctor_approve_booking').find('.kt_get-process').attr('data-id', id);
		$('.tg-approve-booking').modal('toggle');

    });

	jQuery(document).on('submit', '.add_practice', function(e){
	    e.preventDefault();
	    var redirect = $(this).data('redirect');
    	form_data = $(this).serialize();
		$('body').append(loder_html);
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: form_data,
        	dataType: "json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				
				if (response.type == 'success') {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000,position: 'top-right',});
					// setTimeout(function(){location.href = redirect;} , 2000);				
				} else {
					jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
				}
            }
        });
    });
	jQuery(document).on('click', '.edit_practice', function (event) {

		key = $(this).data('key');
		$('body').append(loder_html);
		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: 'key='+key+'&action=load_edit_practice',
        	dataType: "json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				$('.add_practice').html(response.data);
            }
        });

    });
	jQuery(document).on('click', '.delete_practice', function (event) {

		key = $(this).data('key');
		var title = confirm_vars.delete_practice.title;
		var message = confirm_vars.delete_practice.message;
		jQuery.confirm({
			'title': title,
			'message': message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						
						$('body').append(loder_html);
						jQuery.ajax({
				            type: "POST",
				            url: scripts_vars.ajaxurl,
				            data: 'key='+key+'&action=delete_practice',
				        	dataType: "json",
				            success: function(response) {
								jQuery('body').find('.docdirect-site-wrap').remove();
								if (response.type == 'success') {
									jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000,position: 'top-right',});
									setTimeout(function(){window.location.reload();} , 2000);				
								} else {
									jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
								}
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
	jQuery(document).on('click', '.onoffswitch', function (event) {
		event.preventDefault();
		key = $(this).find('input[type=checkbox]').attr('id');
		if (!$(this).closest('tr').hasClass('active')) {
			$('body').append(loder_html);
			jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: 'key='+key+'&action=change_active_practice',
	        	dataType: "json",
	            success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000,position: 'top-right',});
						// setTimeout(function(){window.location.reload();} , 2000);				
					} else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
	            }
	        });
		}

    });


	var src = $('.kt_class').data('image-src');
	$('.kt_class').parallax({imageSrc: src});

    jQuery(document).on('change', '.sort_by, .order_by', function (event) {
        jQuery(".form-sort-articles").submit();
    });
	var fav_nothing	= scripts_vars.fav_nothing;
	/* ---------------------------------------
     Remove to favorites
     --------------------------------------- */
	jQuery(document).on('click', '.kt_remove-wishlist', function (event) {
		event.preventDefault();
						var _this	= jQuery(this);
						var wl_id	= _this.data('wl_id');

		var title = confirm_vars.delete_favorites.title;
		var message = confirm_vars.delete_favorites.message;
		jQuery.confirm({
			'title': title,
			'message': message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {						
						_this.html('<i class="fa fa-refresh fa-spin"></i>');
						_this.addClass('loading');
						
						jQuery.ajax({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: 'wl_id='+wl_id+'&action=docdirect_remove_wishlist',
							dataType: "json",
							success: function (response) {
								_this.removeClass('loading');
								_this.find('i.fa-spin').remove();
								
								if (response.type == 'success') {
									jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000,position: 'top-right',});
									_this.closest('article').remove();
									if( jQuery( '.tg-lists .tg-doctor-profile1' ).length < 1 ){
										jQuery( '.tg-lists' ).html('<div class="tg-list"><p>'+fav_nothing+'</div>');
									}
									
								} else {
									jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
								}
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

    /*****************************************
     * Add Article Tags
     ***************************************/
    jQuery(document).on('click', '.add-article-tags', function (e) {
        e.preventDefault();
        var _this 		= jQuery(this);
        var _input 		= jQuery('.input-feature');
		var _inputval 		= jQuery('.input-feature').val();
		if( _inputval ){
			var load_tags = wp.template('load-article-tags');
			var load_tags = load_tags(_inputval);
			_this.parents('.tg-addallowances').find('.sp-feature-wrap').append(load_tags);
			_input.val('');
		}
        
    });

	jQuery(document).on('submit', '.form_add_slot_time', function(e){
	    e.preventDefault();
		var _this 	= jQuery(this);
		// var cus_start_date 	= _this().find('input[name=start_date]');
    	form_data = $(this).serialize();
		$('body').append(loder_html);
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: form_data,
        	dataType: "json",
            success: function(response) {
				// jQuery('body').find('.docdirect-site-wrap').remove();
				if (response.repeat == 'custom') {
					if( response.type == 'error' ) {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					} else{
	    				$('#calendar').fullCalendar('renderEvent', response.event_data, true); // stick? = true

						_this.find('.custom_time_slots').val(JSON.stringify(response.timeslot));
						_this.find('.custom_time_slot_details').val(JSON.stringify(response.timeslot_details));

						$('.kt_custom-slots-main').find('.custom-timeslots-dates_wrap').append('<div id="'+response.event_data.id+'" class="custom-time-periods">\
							<input type="hidden" class="custom_time_slots" name="custom_time_slots" value="" />\
		                    <input type="hidden" class="custom_time_slot_details" name="custom_time_slot_details" value="" />\
		                    <input type="hidden" name="cus_start_date" value="'+response.date_formate+'" />\
		                    <input type="hidden" name="cus_end_date" value="'+response.date_formate+'" />\
		                    <input type="hidden" name="disable_appointment" value="enable" />\
							</div>');
						$('.kt_custom-slots-main').find('.custom-time-periods').last().find('.custom_time_slots').val(JSON.stringify(response.timeslot));
						$('.kt_custom-slots-main').find('.custom-time-periods').last().find('.custom_time_slot_details').val(JSON.stringify(response.timeslot_details));
						//data custom slots
						var data_events = response.events;
						var data = JSON.stringify(jQuery('.kt_custom-slots-main').serializeObject());
						// console.log(data);

						var custom_timeslots_object	= data;
						var dataString = 'custom_timeslots_object='+custom_timeslots_object+'&action=docdirect_save_custom_slots';
						
						jQuery.ajax({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: dataString,
							dataType:"json",
							success: function(response) {
		        				$('.tg-add-slot_time').modal('hide');
								jQuery('body').find('.docdirect-site-wrap').remove();
								if( response.message_type == 'error' ) {
									jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
								} else{
									// _this.parents('.tg-daytimeslot').find('.timeslots-data-area').html(response.slots_data);
									jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
						
								}

							}
						});
					}
				}else {
					jQuery('body').find('.docdirect-site-wrap').remove();
					if( response.type == 'error' ) {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					} else{
	        			$('.tg-add-slot_time').modal('hide');
    					$('#calendar').fullCalendar('renderEvent', response.event_data, true); // stick? = true
						// _this.parents('.tg-daytimeslot').find('.timeslots-data-area').html(response.slots_data);
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
			
					}
				}
            }
        });
    });

	jQuery(document).on('submit', '.form_edit_slot_time', function(e){
	    e.preventDefault();
		var _this 	= jQuery(this);
    	form_data = $(this).serialize();
		$('body').append(loder_html);
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: form_data,
        	dataType: "json",
            success: function(response) {

				if (response.repeat == 'custom') {
					if( response.type == 'error' ) {
						jQuery('body').find('.docdirect-site-wrap').remove();
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					} else{					
	                    $('#calendar').fullCalendar('removeEvents', response.old_event);
	    				$('#calendar').fullCalendar('renderEvent', response.event_data, true); // stick? = true

						$('.kt_custom-slots-main').find('#'+response.old_event).last().find('.custom_time_slots').val(JSON.stringify(response.timeslot));
						$('.kt_custom-slots-main').find('#'+response.old_event).last().find('.custom_time_slot_details').val(JSON.stringify(response.timeslot_details));					
        				$('.kt_custom-slots-main').find('#'+response.old_event).attr('id', response.event_data.id);

						var data = JSON.stringify(jQuery('.kt_custom-slots-main').serializeObject());
						// console.log(data);

						var custom_timeslots_object	= data;
						var dataString = 'custom_timeslots_object='+custom_timeslots_object+'&action=docdirect_save_custom_slots';
						
						jQuery.ajax({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: dataString,
							dataType:"json",
							success: function(response) {
		        				$('.tg-add-slot_time').modal('hide');
								jQuery('body').find('.docdirect-site-wrap').remove();
								if( response.message_type == 'error' ) {
									jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
								} else{
									// _this.parents('.tg-daytimeslot').find('.timeslots-data-area').html(response.slots_data);
									jQuery.sticky(confirm_vars.update_slot, {classList: 'success', speed: 200, autoclose: 5000});
						
								}

							}
						});
			
					}
				}else {
					jQuery('body').find('.docdirect-site-wrap').remove();
					if( response.type == 'error' ) {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					} else{					
	                    $('#calendar').fullCalendar('removeEvents', response.old_event);
	    				$('#calendar').fullCalendar('renderEvent', response.event_data, true); // stick? = true
	                	$('#modal_editevent').modal('hide');
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
			
					}
				}
			}
		});
    });

	jQuery(document).on('change','.donotshowagain',function(e){
		/*if(this.checked) alert('checked box');
        else alert('unchecked box');*/
        $('.form_edit_slot_time .delete_slot, .closeon').toggleClass('non-confirm');
	});

	jQuery(document).on('click','.form_edit_slot_time .delete_slot',function(e){
	    e.preventDefault();
		var _this 	= jQuery(this);
		ds = _this.closest('form').find('input[name=start_date]').val();
		time = _this.closest('form').find('input[name=old_slot]').val();
		id_event = _this.closest('form').find('input[name=id_event]').val();
		repeat = _this.closest('form').find('input[name=repeat]').val();
      	var title = scripts_vars.delete_slot;
      	var message = scripts_vars.delete_slot_message;
		var dontshow_msag = confirm_vars.dontshow_msag;

		if (_this.hasClass('non-confirm')) {
			
		            jQuery('body').append(loder_html);
	              	if (repeat == 'repeat') {
	        			var dataString = 'id_event='+id_event+'&date='+ds+'&time='+time+'&repeat=repeat&action=remove_time_slot';
		                jQuery.ajax({
		                  type: "POST",
		                  url: scripts_vars.ajaxurl,
		                  data: dataString,
		                  dataType:"json",
		                  success: function(response) {
		                    jQuery('body').find('.docdirect-site-wrap').remove();
		                    if( response.type == 'error' ) {
		                      jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
		                    } else{
		                	  $('#modal_editevent').modal('hide');
		                      $('#calendar').fullCalendar('removeEvents', id_event);
		                      jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
		                    }
		                  }
		                });
	              	}else {
	              		console.log(id_event);
		                $('#calendar').fullCalendar('removeEvents', id_event);
						$('.kt_custom-slots-main').find('#'+id_event).remove();

						var data = JSON.stringify(jQuery('.kt_custom-slots-main').serializeObject());
						// console.log(data);

						var custom_timeslots_object	= data;
						var dataString = 'custom_timeslots_object='+custom_timeslots_object+'&action=docdirect_save_custom_slots';
						
						jQuery.ajax({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: dataString,
							dataType:"json",
							success: function(response) {
		        				$('.tg-add-slot_time').modal('hide');
								jQuery('body').find('.docdirect-site-wrap').remove();
								if( response.message_type == 'error' ) {
									jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
								} else{
									// _this.parents('.tg-daytimeslot').find('.timeslots-data-area').html(response.slots_data);
									jQuery.sticky(confirm_vars.delete_slot, {classList: 'success', speed: 200, autoclose: 5000});
						
								}

							}
						});
	              	}
		} else {
	        jQuery.confirm({
	          'title': title,
	          'message': message+'<div class="checkbox"><div class="doc-checkbox1"><label><input name="dsma" type="checkbox" class="donotshowagain">'+dontshow_msag+'</label></div></div>',				
	          'buttons': {
	            'Yes': {
	              'class': 'blue',
	              'action': function () {
	  
		            jQuery('body').append(loder_html);
	              	if (repeat == 'repeat') {
	        			var dataString = 'id_event='+id_event+'&date='+ds+'&time='+time+'&repeat=repeat&action=remove_time_slot';
		                jQuery.ajax({
		                  type: "POST",
		                  url: scripts_vars.ajaxurl,
		                  data: dataString,
		                  dataType:"json",
		                  success: function(response) {
		                    jQuery('body').find('.docdirect-site-wrap').remove();
		                    if( response.type == 'error' ) {
		                      jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
		                    } else{
		                	  $('#modal_editevent').modal('hide');
		                      $('#calendar').fullCalendar('removeEvents', id_event);
		                      jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
		                    }
		                  }
		                });
	              	}else {
	              		console.log(id_event);
		                $('#calendar').fullCalendar('removeEvents', id_event);
						$('.kt_custom-slots-main').find('#'+id_event).remove();

						var data = JSON.stringify(jQuery('.kt_custom-slots-main').serializeObject());
						// console.log(data);

						var custom_timeslots_object	= data;
						var dataString = 'custom_timeslots_object='+custom_timeslots_object+'&action=docdirect_save_custom_slots';
						
						jQuery.ajax({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: dataString,
							dataType:"json",
							success: function(response) {
		        				$('.tg-add-slot_time').modal('hide');
								jQuery('body').find('.docdirect-site-wrap').remove();
								if( response.message_type == 'error' ) {
									jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
								} else{
									// _this.parents('.tg-daytimeslot').find('.timeslots-data-area').html(response.slots_data);
									jQuery.sticky(confirm_vars.delete_slot, {classList: 'success', speed: 200, autoclose: 5000});
						
								}

							}
						});
	              	}

	              }
	            },
	            'No': {
	              'class': 'gray',
	              'action': function () {
	                return false;
	              } // Nothing to do in this case. You can as well omit the action property.
	            }
	          }
	        });
	    }

    });

	jQuery(document).on('click','.kt_save-custom-time-slots',function(e){
 		e.preventDefault();
 		
		var current_slot1 = $(this).parents('.custom-time-periods').find('.custom_time_slots').val();
		var current_slot_details1 = $(this).parents('.custom-time-periods').find('.custom_time_slot_details').val();
		
		// var current_slot1 = current_slot1.slice(1, -1);
		// var current_slot_details1 = current_slot_details1.slice(1, -1);
		if(current_slot1 != '') {
			var current_slot = JSON.parse(current_slot1);
		}
		if(current_slot_details1 != '') {
			var current_slot_details = JSON.parse(current_slot_details1);
		}
		
		// a={"0800-0830":0,"0930-1000":0,"1000-1030":0}; 
 		// c=$.extend(res, a);
 		// alert(JSON.stringify(c));

		var _this 	= jQuery(this);
		
		var formData = JSON.stringify(jQuery(this).parents('form').serializeObject());
			
		var slot_title	  = _this.parents('.custom-timeslots-data').find('input[name=slot_title]').val();
		var start_time	  = _this.parents(".custom-timeslots-data").find('.start_time option:selected').val();
		var end_time	    = _this.parents(".custom-timeslots-data").find('.end_time option:selected').val();
		var meeting_time	= _this.parents(".custom-timeslots-data").find('.meeting_time option:selected').val();
		var padding_time	= _this.parents(".custom-timeslots-data").find('.padding_time option:selected').val();
		var cus_start_date	= _this.parents(".custom-time-periods").find('input[name=cus_start_date]').val();
		var cus_end_date	= _this.parents(".custom-time-periods").find('input[name=cus_end_date]').val();
		
		var cus_end_date	= _this.parents(".custom-time-periods").find('input[name=cus_end_date]').val();
		var cus_end_date	= _this.parents(".custom-time-periods").find('input[name=cus_end_date]').val();
		
		jQuery('body').append(loder_html);
		
		if( start_time == '' 
			|| 
			end_time == '' 
			|| 
			meeting_time == '' 
			|| 
			padding_time == '' 
		){
			jQuery('body').find('.docdirect-site-wrap').remove();
			jQuery.sticky(complete_fields, {classList: 'important', speed: 200, autoclose: 5000});
			return false;
		}
		
		if( cus_start_date == '' 
			&&
			cus_end_date == '' 
		){
			jQuery('body').find('.docdirect-site-wrap').remove();
			jQuery.sticky(custom_slots_dates, {classList: 'important', speed: 200, autoclose: 5000});
			return false;
		}

		var dataString = 'cus_start_date='+cus_start_date+'&cus_end_date='+cus_end_date+'&slot_title='+slot_title+'&start_time='+start_time+'&end_time='+end_time+'&meeting_time='+meeting_time+'&padding_time='+padding_time+'&action=docdirect_add_custom_time_slots';

		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				 
 				new_slot = $.extend(current_slot, response.timeslot);
 				new_slot_details = $.extend(current_slot_details, response.timeslot_details);
				_this.parents('.custom-time-periods').find('.custom_time_slots').val(JSON.stringify(new_slot));
				_this.parents('.custom-time-periods').find('.custom_time_slot_details').val(JSON.stringify(new_slot_details));
				response.timeslot = new_slot;
				response.timeslot_details = new_slot_details;
				kt_docdirect_get_list(response,_this);
				jQuery.sticky('Success', {classList: 'success', speed: 200, autoclose: 5000,position: 'top-right',});
						
			}
		});
		return false;

	});
	
	//Get Time Slot List
	function kt_docdirect_get_list(data,_this){
		var json_list	= JSON.stringify(data);
		var dataString = 'json_list='+json_list+'&action=docdirect_get_time_slots_list';
		
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				_this.parents('.custom-time-periods').find('.custom-timeslots-data-area').html(response.timeslot_list);
				jQuery('.bk-save-custom-slots').trigger('click');
				_this.closest('.custom-timeslots-data').find('.tg-timeslotswrapper').remove();
				jQuery('body').find('.docdirect-site-wrap').remove();
				
			}
		});
		return false;
	}


	$('.schedule .schedule-pickr').attr('readonly','');

	$('.home img').Lazy();
  /**
   * Slide left instantiation and action.
   
  var slideLeft = new Menu({
    wrapper: '#wrapper',
    type: 'slide-left',
    menuOpenerClass: '.navbar-toggle',
    maskId: '#c-mask'
  });

  var slideLeftBtn = document.querySelector('.navbar-toggle.collapsed');
  
  /*slideLeftBtn.addEventListener('click', function(e) {
    e.preventDefault;
    slideLeft.open();
  });*/
  	if ($('#c-mask').length) {
	  var slideLeft = new Menu({
	    wrapper: '#wrapper',
	    type: 'slide-left',
	    menuOpenerClass: '.navbar-toggle',
	    maskId: '#c-mask'
	  });
	}
	jQuery(document).on('click','.navbar-toggle.collapsed', function(e){
   			e.preventDefault;
			$('#c-menu--slide-left').removeClass('menu-right');
		// if ( $(window).width() < 1025 ) {
			$('.c-menu .nav-pills li').removeClass('active').parent().find('li:first-child').addClass('active');
			$('.c-menu .tab-content > div').removeClass('active in').parent().find('div:first-child').addClass('active in');
			slideLeft.open();
		// }
	});
	jQuery(document).on('click','.doc-admin .doc-user', function(e){
		if ( $(window).width() < 1025 ) {
			$('#c-menu--slide-left').removeClass('menu-right');
   			e.preventDefault;
			$('.c-menu .wrap_slideleft > .nav-pills li').removeClass('active').siblings('li:last-child').addClass('active');
			$('.c-menu .wrap_slideleft > .tab-content > div').removeClass('active in').siblings('div:last-child').addClass('active in');
			slideLeft.open();
		}else {
			$('.c-menu .wrap_slideleft > .nav-pills li').removeClass('active').siblings('li:last-child').addClass('active');
			$('.c-menu .wrap_slideleft > .tab-content > div').removeClass('active in').siblings('div:last-child').addClass('active in');
			slideLeft.open();
			/*setTimeout(function(){
				slideLeft.open();
			} , 1000);*/
		}
	});
	jQuery(document).on('click','.quick_search a', function(e){
			$('#c-menu--slide-left').removeClass('menu-right');
   			e.preventDefault;
			$('.c-menu .wrap_slideleft > .nav-pills li').removeClass('active').siblings('li.tabsearch').addClass('active');
			$('.c-menu .wrap_slideleft > .tab-content > div').removeClass('active in').siblings('div#tabsearch').addClass('active in');
			slideLeft.open();
	});

	function zzz() {
		w = $('.wrap_slideleft').width();
		if (window.innerWidth <= 1025) { 
			$('.c-menu .c-menu__close').css('left', w).css('right', 'auto');
			$('#c-menu--slide-left').removeClass('menu-right');
		}else {
			$('.c-menu.menu-right .c-menu__close').css('right', w).css('left', 'auto');
			$('#c-menu--slide-left').addClass('menu-right');
		}
	}
	zzz();
	function zzx() {
			if ($('.doc-navigationarea').length) {
				var lastScrollTop = 0;
				var maptop1 = $('#main').offset().top;
			    $(window).scroll(function(){
			    	var st = $(this).scrollTop();
			      	if ( st < lastScrollTop && st > maptop1 ) {
						if (window.innerWidth <= 600) {
				          	$('.doc-navigationarea').addClass('fixed');
				          	/*setTimeout(function(){
								$('.doc-navigationarea').addClass();
							}, 2000);*/
				        }
				    } else {
				        $('.doc-navigationarea').removeClass('fixed');
				    }
				    lastScrollTop = st;
				});
			}
	}
	zzx();
	window.onresize = function() {
		zzz();
		if (window.innerWidth <= 1025) {  
			zzz();
			$('#c-menu--slide-left').removeClass('menu-right');
		}else {
			$('#c-menu--slide-left').addClass('menu-right');
		}
		if (window.innerWidth <= 600) {  
			zzx();
		}
	}
	/*$(window).scroll(function(){
		if (window.innerWidth <= 600) {
			if ($('.doc-navigationarea').length) {
				var maptop = $('.doc-navigationarea').offset().top;
			    if ($(this).scrollTop() > maptop) {
				    // $('.doc-navigationarea').addClass('fixed').show(500).delay(1000);
			        $('.doc-navigationarea').addClass('fixed').delay(2000).queue(function(next ){
				     $(this).removeClass('fixed');
				     next();
				  })
			    }else {
					// $('.doc-navigationarea').removeClass('fixed');
			    }
			}
			if ($('.tg-section-btn').length) {
				var maptop = $('.tg-section-btn').offset().top;
			    if ($(this).scrollTop() > maptop) {
				    // $('.doc-navigationarea').addClass('fixed').show(500).delay(1000);
			        $('.tg-section-btn').addClass('fixed').delay(2000).queue(function(next ){
				     $(this).removeClass('fixed');
				     next();
				  })
			    }else {
					// $('.doc-navigationarea').removeClass('fixed');
			    }
			}
		}else {
			$('.doc-navigationarea').removeClass('fixed');

			if ($('.tg-section-btn').length) {
				var maptop = $('.tg-section-btn').offset().top;
			    if ($(this).scrollTop() > maptop) {
				    $('.tg-section-btn').addClass('fixed');
			    }else {
					// $('.tg-section-btn').removeClass('fixed');
		        	$('.tg-section-btn').removeClass('fixed');
			    }
			}
		}
	});*/
	jQuery(document).on('click','.c-menu .tab-pane .navbar-nav li.menu-item-has-children', function(e){
   			e.preventDefault;
		$(this).toggleClass('open',500);
	});

	jQuery(document).on('click','.tg-form-signup .dropdown-input-group ul li', function(e){
   		var parent_class = $(this).closest('.wrap_group');
   		if (parent_class.hasClass('company')) {
   			$('.plus_group, .last_name_group').hide();
   			$('input[name=first_name]').attr('placeholder', 'Company Name');
   		} else {
   			$('.plus_group, .last_name_group').show();
   			$('input[name=first_name]').attr('placeholder', 'First Name');
   		}
	});

	jQuery(document).on('click','.tg-form-signup .user-selection', function(e){
		var radio = $(this).find('input').val();
		if (radio == 'visitor') {
	   		$('.plus_group').hide();
	   		$('.last_name_group').show();
   			$('input[name=first_name]').attr('placeholder', 'First Name');
		}else {
			var val = parseInt($('.tg-form-signup .select_category').val());
			// alert(val);
			var ar = [125, 126, 3206, 3292]
			if ($.inArray(val, ar) != -1) {
	   			$('.plus_group, .last_name_group').hide();
	   			$('.plus_group, .last_name_group').hide();
   				$('input[name=first_name]').attr('placeholder', 'Company Name');
			}else {
	   			$('.plus_group, .last_name_group').show();
	   			$('.plus_group, .last_name_group').show();
   				$('input[name=first_name]').attr('placeholder', 'First Name');
			}
		}
	});

	// jQuery('.search-filters-wrap #byspeacialty .form-group.toggle_filter').addClass('open');
   	jQuery('.form-group.toggle_filter').nextAll('.form-group').slideUp(500);
	jQuery(document).on('click','.form-group.toggle_filter a', function(e){
   		if($(this).hasClass('open')) {
   			$(this).parent().nextAll('.form-group').slideDown(500);
   		}else {
   			$(this).parent().nextAll('.form-group').slideUp(500);
   		}
   		$(this).toggleClass('open');
   		$(this).find('i').toggleClass('fa-minus fa-plus');
	});

   	jQuery('.tg-userphotogallery ul > li:nth-child(5)').nextAll(':not(".more_button")').toggle(500);
	jQuery(document).on('click','.tg-userphotogallery .more_button a', function(e){
   		$(this).toggleClass('open');
   		$(this).find('i').toggleClass('fa-minus fa-plus');
   		$('.tg-userphotogallery ul > li:nth-child(5)').nextAll(':not(".more_button")').toggle(500);
	});

   	jQuery('.tg-presentationvideo .row > div:nth-child(3)').nextAll('div:not(".more_button")').toggle(500);
	jQuery(document).on('click','.tg-presentationvideo .more_button a', function(e){
   		$(this).toggleClass('open');
   		$(this).find('i').toggleClass('fa-minus fa-plus');
   		$('.tg-presentationvideo .row > div:nth-child(3)').nextAll('div:not(".more_button")').toggle(500);
	});
  
	//Add Price/Service list
	/*jQuery(document).on('click','.add-new-prices',function(){
		var load_prices_tpl = wp.template( 'load-prices' );
		var counter	= jQuery( '.prices_wrap > tbody' ).length;
		var list	= load_prices_tpl(counter);
		jQuery( '.prices_wrap' ).append(list);
		
	});*/

	//Edit Prices
	jQuery(document).on('click','.prices-action .edit-me',function(){
		jQuery('.prices-data').hide();
		jQuery(this).parents('tr').next('tr').find('.prices-data').toggle();
	});

	//Delete Prices
	jQuery(document).on('click','.prices-action .delete-me',function(){
		var _this	= jQuery(this);
		var title = scripts_vars.delete_prices;
		var message = scripts_vars.delete_prices_message;
		jQuery.confirm({
			'title': title,
			'message': message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						_this.parents('.prices_item').remove();
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
	
	//Prices list toggles
	jQuery('.prices-list-wrap .tg-panelcontent').hide();
	jQuery('.prices-list-wrap .tg-accordion .tg-accordionheading:first').addClass('tg-active').next().slideDown('slow');
	jQuery('.prices-list-wrap .tg-accordion .tg-accordionheading').on('click',function() {
		if(jQuery(this).next().is(':hidden')) {
			jQuery('.prices-list-wrap .tg-accordion .tg-accordionheading').removeClass('tg-active').next().slideUp('slow');
			jQuery(this).toggleClass('tg-active').next().slideDown('slow');
		}
	});


	jQuery(document).on('click','.doc-select .chosen-container, .locate-me-wrap',function(){
		$('.form-group').find('.dropdown-input-group').hide();		
	});

	jQuery(document).on('click','.doc-formsearchwidget .doc-btnarea .doc-btn.apply_filter, .tg-searcharea-v2 .tg-btn',function(e){
		e.preventDefault();
		$(this).closest('.tab-pane').siblings().remove();
		$(this).closest('form').submit();
	});

    $(".tg-areuadoctor .tg-healthcareonthego a.tg-btn").attr({
	    'href': "javascript:;", 
	    'data-toggle': "modal", 
	    'data-target': ".tg-user-modal"
	});

	$('.tg-userphotogallery a > img').parent().colorbox({ 'rel': 'gallery', maxWidth:'95%', maxHeight:'95%' });

	var delay = (function(){
	  	var timer = 0;
	  	return function(callback, ms){
	    	clearTimeout (timer);
	    	timer = setTimeout(callback, ms);
	  	};
	})();

	$('body').on('click', '.close_response_wrap', function() {
    	$(this).closest('.response').css('visibility', 'hidden');
	});

    $('#search_string').keyup(function() {
    	var form_selector = $(this).closest('form');
	    delay(function(){
	    	runajax(form_selector);
	    }, 1000 );
	});

	$('input[type=radio][name=type_category]').on('change', function() {
    	var form_selector = $(this).closest('form');
        runajax(form_selector);
    });
	$('.search_user_btn').on('click', function() {
    	var form_selector = $(this).closest('form');
        runajax(form_selector);
    });	

    $('.quickbooking .wrap_item').each(function() {
    	var cc = $(this).find('.force-scroll').length;
    	if (cc < 1) {
    		$(this).addClass('not_full');
    	}
    });

    /*****************/

	$('.direct_key_search').val('');
	jQuery(document).on('click', '.direct_search .bootstrap-tagsinput span.button_dropdown', function(e) {

		if ($(this).closest('.form-group').find('.dropdown-input-group').is(":visible")) {
			$(this).closest('.form-group').find('.dropdown-input-group').hide();
		}else {
			$('.dropdown-input-group').hide();
			$(this).closest('.form-group').find('.dropdown-input-group').toggle();
		}

    });
	jQuery(document).on('focus', '.direct_key_search', function(e) {

		$('.dropdown-input-group').hide();
		$(this).closest('.form-group').find('.dropdown-input-group').show();

    });
    if ($('.direct_key_search').closest('.form-group').find('.dropdown-input-group .dropdown-wrap').length > 0) {
		/*var chacha = $('.direct_key_search').closest('.form-group').find('.dropdown-input-group .dropdown-wrap');
	    jQuery.ajax({
	        type: "POST",
	        url: scripts_vars.ajaxurl,
	        data: 'action=direct_search_plus',
	    	// dataType: "json",
	        success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				chacha.html(response);
				// var cloner = $('.direct_search .dropdown-input-group .dropdown-wrap').html();
				$('.clone_sps').html(response);
	        }
	    });*/    	
    }
				var cloner = $('.direct_search .dropdown-input-group .dropdown-wrap').html();
				$('.clone_sps').html(cloner);
	jQuery(document).on('keyup', '.direct_key_search', function(e) {
		var _this = $(this);
		var chacha = $(this).closest('.form-group').find('.dropdown-input-group .dropdown-wrap');
		var search_string = $.trim($(this).val());
    	if(e.keyCode == 8){
	    	delay(function(){
		       	// user has pressed backspace
		    	if (search_string == '') {
					var cloner = $('.clone_sps').html();
					chacha.html(cloner);
			    	return false;
			    }
		    }, 500 );
	   	}
		if (search_string == '') {
			var cloner = $('.clone_sps').html();
			chacha.html(cloner);
		}else {
			string = 's=' + search_string;
			if($("input[name='speciality[]']").length > 0 ) {
				var blkstr = [];
				$("input[name='speciality[]']").each(function() {
		    		var id = $(this).attr('id').match(/speciality-(\d+)/)[1];
			        // alert(id);
			        blkstr.push(id);
			    });
				console.log(blkstr.join(", "));
				string += '&exclude=' + blkstr.join(", ");
			}
			// alert(exclude);
	    	delay(function(){
				// $('body').append(loder_html);
				_this.addClass('loading');
	      		jQuery.ajax({
		            type: "POST",
		            url: scripts_vars.ajaxurl,
		            data: string + '&action=direct_search',
	            	dataType: "json",
		            success: function(response) {
						// jQuery('body').find('.docdirect-site-wrap').remove();
						$('.direct_key_search').removeClass('loading');
						chacha.html(response.data);
		            }
		        });
		    }, 1000 );
	    }
	});

	jQuery(document).on('click', '.direct_search .dropdown-input-group li.select_s_keyword', function(e) {
		var text = $.trim($(this).text());
		var id = $.trim($(this).data('id'));
		var alo = '<span data-id="'+id+'" class="tag label label-info s_keyword">'+text+'<span data-role="remove"></span></span>';
		$('.direct_key_search').before(alo);
		$(this).addClass('hidden');
		$(this).closest('.form-group').append('<input id="s_keyword-'+id+'" type="hidden" name="s_keyword[]" value="'+text+'" />');
		$('.direct_key_search').val('').focus();
	});

	jQuery(document).on('click', '.direct_search .dropdown-input-group li.select_speciality', function(e) {
		var text = $.trim($(this).text());
    	var slug = $(this).data('slug');
    	var id = $(this).data('id').match(/speciality-(\d+)/)[1];
		var alo = '<span data-id="'+id+'" class="tag label label-info">'+text+'<span data-role="remove"></span></span>';
		console.log(text);
		$('.direct_key_search').before(alo);
		$(this).addClass('hidden');
		$(this).closest('.form-group').append('<input id="speciality-'+id+'" type="hidden" name="speciality[]" value="'+slug+'" />');
	});
	jQuery(document).on('click', '.direct_search .tag.label span', function(e) {
    	var id = $(this).parent().data('id');
    	if ($(this).parent().hasClass('s_keyword')) {
			$('.direct_search li#s_keyword-'+id).removeClass('hidden');
			$(this).parent().remove();
			$('.direct_search input#s_keyword-'+id).remove();
    	}else {
			$('.direct_search li#speciality-'+id).removeClass('hidden');
			$(this).parent().remove();
			$('.direct_search input#speciality-'+id).remove();
    	}
	});

	jQuery(document).on('focus', '.by_name_search > input', function(e) {

		var chacha = $(this).closest('.form-group').find('.dropdown-input-group .dropdown-wrap');
		if (chacha.children().length > 0 ){
			$(this).closest('.form-group').find('.dropdown-input-group').show();
		}

    });
	jQuery(document).on('keyup', '.by_name_search > input', function(e) {
		var _this = $(this);
		var search_string = $(this).val();
		var chacha = $(this).closest('.form-group').find('.dropdown-input-group .dropdown-wrap');
		var chacha1 = $(this).closest('.form-group').find('.dropdown-input-group');
		string = 'search_string=' + search_string + '&type=home';
		// alert(exclude);
    	if ( $.trim(search_string) != '' ) {
	    	delay(function(){
				// $('body').append(loder_html);
				_this.addClass('loading');
	      		jQuery.ajax({
		            type: "POST",
		            url: scripts_vars.ajaxurl,
		            data: string + '&action=search_user',
	            	dataType: "json",
		            success: function(response) {
						// jQuery('body').find('.docdirect-site-wrap').remove();
						_this.removeClass('loading');
						if (response.data != '') {
							chacha.html(response.data);
							chacha1.show();
						}
		            }
		        });
		    }, 500 );
	    }
	});

	jQuery(document).on('click', '.form-group .dropdown-input-group .dropdown-wrap li > span', function(){
	    $(this).find('i').toggleClass('fa-plus fa-minus');
	    $(this).siblings('.collapse').toggleClass('in');
	});

	jQuery(document).on('click', '.tg-affiliation .aff_group h2 span a', function(){
	    $(this).parent().next().show();
	});

	jQuery(document).on('submit', '.tg-affiliation .aff_group h2 form', function(e){
	    e.preventDefault();
		var _this = $(this);
		var span = _this.prev().find('span');

    	form_data = $(this).serialize();
		$('body').append(loder_html);
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: form_data + '&action=change_title_group',
        	dataType: "json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				_this.hide();
				span.text(response.data);
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
			$('body').append(loder_html);
      		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'post_id': post_id,
	            	'type_group': type_group,
	            	'action': 'edit_group'
	            },
            	dataType: "json",
	            success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();
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

	function runajax(form_selector) {
		// console.log('aloo');	
		var s_div = form_selector.find('#search_string');
		var btn_div = form_selector.find('.search_user_btn');
    	var s_val = form_selector.find('#search_string').val();
    	form_data = form_selector.serialize();
    	response_div = form_selector.find('.response');
    	response_div.html('').css('visibility', 'hidden');
    	if ( $.trim(s_val) != '' ) {
		    
			// $('body').append(loder_html);
			s_div.addClass('loading');

      		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: form_data + '&action=search_user',
            	dataType: "json",
	            success: function(response) {
					// jQuery('body').find('.docdirect-site-wrap').remove();
					s_div.removeClass('loading');
	                response_div.html(response.data).css('visibility', 'visible');
	            }
	        });
    	}
	}


	$('body').on('click', '.request_aff', function() {
		var _this = $(this);
		var type_group = $(this).data('group');
    	var article = $(this).closest('.tg-doctor-profile1');
    	var waiting_list = $('.waiting_list.list_user').children('div');
    	var user_to = article.attr('id').match(/user-(\d+)/)[1];

    	var button_gr = $(this).closest('.button_gr');
        // console.log(user_to);
        if ( $.trim(user_to) != '' ) {
			$('body').append(loder_html);
      		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'user_to': user_to,
	            	'type_group': type_group,
	            	'action': 'request_aff'
	            },
            	dataType: "json",
	            success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						
	                	button_gr.find('.aff_btn').html('<i class="fa fa-check-square"></i>');
	                	button_gr.find('.dropdown-menu').remove();
	                	waiting_list.append(response.output);
					}else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
	            }
	        });
    	}
    });

	$('body').on('click', '.aff_action_btn', function() {
		var _this = $(this);
    	var article = $(this).closest('.tg-doctor-profile1');    	
    	var type_action = _this.data('action');
    	var post_id = article.data('post_id');
		var current_url      = document.URL;     // Returns full URL
        
			$('body').append(loder_html);
      		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'post_id': post_id,
	            	'type_action': type_action,
	            	'action': 'aff_action'
	            },
            	dataType: "json",
	            success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();

					if ( type_action == 'approve' ) {
					    article.find('.approve_btn').remove();
					    article.find('.decline_btn').remove();
					    article.find('.feature-rating.user-star-rating').after('<div class="button_gr"><a class="aff_btn remove_approve_btn" href="javascript:;"><i class="fa fa-times"></i></a></div>');
					    article.appendTo(".approved_list > div");
					    setTimeout(function(){location.href = current_url} , 1000); 
					}else {
						article.remove();
					}
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						
	            }
	        });
    });


	$('body').on('click', '.remove_request_aff', function() {
		var _this = $(this);
    	var article = $(this).closest('.tg-doctor-profile1');
    	var post_id = article.data('post_id');
        
		$('body').append(loder_html);
      		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'post_id': post_id,
	            	'action': 'remove_request_aff'
	            },
            	dataType: "json",
	            success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();
	                article.remove();
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
					
	            }
	        });
    });
	
	$('body').on('click', '.remove_approve_btn', function() {

		//Process dadtabase item
		var _this = $(this);
    	var article = $(this).closest('.tg-doctor-profile1');
    	var post_id = article.data('post_id');
				    	
		var title = confirm_vars.delete_aff.title;
		var message = confirm_vars.delete_aff.message;

		jQuery.confirm({
			'title': title,
			'message': message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
				        
						$('body').append(loder_html);
			      		jQuery.ajax({
				            type: "POST",
				            url: scripts_vars.ajaxurl,
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

		/*var _this = $(this);
    	var article = $(this).closest('.tg-doctor-profile1');
    	var post_id = article.data('post_id');
        
		$('body').append(loder_html);
      		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
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
	        });*/
    });

	$('body').on('click', '.approve_btn1', function() {
		var _this = $(this);
    	var article = $(this).closest('.tg-doctor-profile1');
    	var user_to = article.attr('id').match(/user-(\d+)/)[1];
	    $("body").css("cursor", "wait");
	    _this.css("cursor", "wait");
        // console.log(user_to);
        if ( $.trim(user_to) != '' ) {
      		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'user_to': user_to,
	            	'action': 'approve_aff'
	            },
	            success: function(response) {
				    article.find('.approve_btn').remove();
				    article.find('.decline_btn').remove();
				    article.find('.feature-rating.user-star-rating').after('<a class="aff_btn remove_approve_btn" href="javascript:;"><i class="fa fa-times"></i></a>');
				    article.appendTo(".approved_list > div");
				    $("body").css("cursor", "default");
				    _this.css("cursor", "default");
	            }
	        });
    	}
    });

	$('body').on('click', '.decline_btn1', function() {
		var _this = $(this);
    	var article = $(this).closest('.tg-doctor-profile1');
    	var user_to = article.attr('id').match(/user-(\d+)/)[1];
	    $("body").css("cursor", "wait");
	    _this.css("cursor", "wait");
        // console.log(user_to);
        if ( $.trim(user_to) != '' ) {
      		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'user_to': user_to,
	            	'action': 'decline_aff'
	            },
	            success: function(response) {
	                article.remove();
				    $("body").css("cursor", "default");
				    _this.css("cursor", "default");
	            }
	        });
    	}
    });

	var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
   
	$(document).on('click', 'form#submit_aff .btn-primary.post-form, div#tabadd .btn-primary.post-form', function(e) {
	    e.preventDefault(); 
	    // alert('clicked'); 
		var current_url      = document.URL;     // Returns full URL
		var _this = $(this).closest('#tabadd');
		var post_title = _this.find('input[name=post_title]').val();
		var tagline = _this.find('input[name=tagline]').val();
		var email = _this.find('input[name=email]').val();
		var specialties = _this.find('textarea[name=specialties]').val();
		var group_aff = _this.find('select[name=group_aff]').val();
		var post_featured_image = _this.find('input[name=post_featured_image]').val();

		$('body').append(loder_html);
		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
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
					jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						setTimeout(function(){location.href = current_url} , 1000); 
					}else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
	            }
	        });
	});

	$(document).on('click', '.tg-affiliation .load_more', function(e) {
	    e.preventDefault(); 
	    // alert('clicked'); 
		var _this = $(this);
		// var user_id = $('input[name=user_to]').val();
		// var group_aff = _this.data('group');
		var length = _this.closest('.list_user').find('.tg-doctor-profile1').length;
		var div = _this.closest('.aff_group').find('.list_user > div:first-child');

		var ppp = _this.closest('.aff_group').find('input[name=posts_per_page]').val();
		var list_user = _this.closest('.aff_group').find('input[name=array]').val();

		$('body').append(loder_html);
		jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: {
	            	'list_user': list_user,
	            	'ppp': ppp,
	            	'length': length,
	            	'action': 'load_more_affiliation'
	            },
            	dataType: "json",
	            success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						div.append(response.data);
						if (response.end == 'true') {
							_this.remove();
						}
					}else {
						
					}
	            }
	        });
	});

	$('body').on('click', '.tg-doctor-profile1 a.hasmodal', function() {

		var email = $(this).data('email');
		$('.tg-invite-modal').find('input[name=email]').val(email);
		$('.tg-invite-modal').modal('show');

	});

	$('body').on('click', '.submit_invite', function() {
		var _this = $(this);
    	var form = $(this).closest('form');
    	var response_div = form.find('.response');
	    $("body").css("cursor", "wait");
	    _this.css("cursor", "wait");
    	form_data = form.serialize();
    	response_div.html('');
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: form_data + '&action=submit_invite',
            dataType: "json",
            success: function(response) {
            	// console.log(response.message);
            	response_div.html(response.message);
			    $("body").css("cursor", "default");
			    _this.css("cursor", "pointer");
				if( response.success == true ){
					//close modal
					setTimeout(
					  function() {
						$('.tg-invite-modal').modal('hide');
					  }, 3000);
				}	
            }
        });
    });

	$('body').on('click', '.make-reply', function(e) {
		e.preventDefault();
		var $this 	= jQuery(this);
		
		jQuery('.message_contact').html('').hide();
		jQuery($this).append("<i class='fa fa-refresh fa-spin'></i>");
		jQuery('.message_contact').removeClass('alert-success');
		jQuery('.message_contact').removeClass('alert-danger');

		var _this = $(this);
    	var form = $(this).closest('form');

    	var li = $(this).closest('li');
    	var post_parent = li.attr('id').match(/rv-(\d+)/)[1];

    	form_data = form.serialize();
    	
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: form_data + '&post_parent='+post_parent+'&action=make_reply',
            dataType: "json",
            success: function(response) {
            	$this.find('i').remove();
				jQuery('.message_contact').show();
				if( response.type == 'error' ) {
					jQuery('.message_contact').addClass('alert alert-danger').show();
					jQuery('.message_contact').html(response.message);
				} else{
					jQuery('.message_contact').addClass('alert alert-success').show();
					jQuery('.message_contact').html(response.message);
					if( response.html == 'refresh' ){
						window.location.reload();
					}
					form.get(0).reset();
					
				}	
            }
        });
    });

	$('body').on('click', '.btn_reply', function() {
		var _this = $(this);
		closest = _this.closest('li');
		reply_div = $('#reply_review');
		reply_div.show();
		closest.append(reply_div);

    });

	$('body').on('click', '#cancel-review-reply-link', function() {

		
		$('#reply_review').hide();


    });

	$('.sp-dashboard-profile-form input[type=file]').on('change', function(e) { 
            
		var _this = $(this);
		output_ajax = $('.sp-dashboard-profile-form .output_ajax > div');
		pdf_input = $('.sp-dashboard-profile-form .pdf_file');
		// pdf_input = _this.parent().siblings('.pdf_file');
		val = pdf_input.val();


		var pdf_file = [];
		files = e.target.files;
		$.each(files, function(i, file) {
			pdf_file.push(file);
		});

	    // select the form and submit
	    ajaxData = new FormData();
		ajaxData.append( 'action', 'ajax_filepdf');
		ajaxData.append( 'val', val);
		$.each($("input[type=file]"), function(i, obj) {
		        $.each(obj.files,function(j, file){
		            ajaxData.append('file['+j+']', file);
		        })
		});
		$('body').append(loder_html);

	    jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            // data: pdf_file+'action=ajax_filepdf',
            data: ajaxData,
            contentType: false,
            cache: false,
            processData:false,
            dataType: "json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				output_ajax.append(response.output);
				pdf_input.val(response.val);
            }
        });

	});


	$('body').on('click', '.remove_pdf', function() {

        $("body").css("cursor", "wait");
		var _this = $(this);
		href = $(this).siblings('a').attr('href');
		closest = _this.closest('.sp-dashboard-profile-form');
		removeDiv = _this.closest('.col-sm-4');
		output_ajax = closest.find('.output_ajax');
		pdf_input = closest.find('.pdf_file');
		val = pdf_input.val();
		
		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: 'href='+href+'&val='+val+'&action=remove_pdf',
            dataType: "json",
            success: function(response) {
				$("body").css("cursor", "default");
				if( response.type == 'error' ) {

				}else {
					removeDiv.remove();
					pdf_input.val(response.val);
					// console.log(response.val);
				}
			}
        });

	});


	$('body').on('click', '.btn_add_schedule', function() {
        // $("body").css("cursor", "wait");
		var _this = $(this);
		closest = _this.closest('.form-docschedule');
		length = $('.form-docschedule .schedule').length;
		append_desc = closest.find('.append_desc');
		// console.log(length);
		$('body').append(loder_html);
		
		 jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: 'length='+length+'&action=add_schedule',
            dataType: "json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				append_desc.before(response.output);
				// $("body").css("cursor", "default");
				to_top = $('.form-docschedule .append_desc').prev();
				// $(window).scrollTop(to_top.offset().top);
				$("html, body").animate({
			        scrollTop: to_top.offset().top - 50
			    }, 1000);
			    jQuery('.schedule-pickr').datetimepicker({
				  datepicker:false,
				  format:'H:i'
				});
			}
        });


    });


	$('body').on('click', '.btn_remove_schedule', function() {

        $("body").css("cursor", "wait");
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
			_this.find('.tg-heading-border').find('h4').text(function(i, val) {
			    return val.replace(/\d+/, function(n) {
			        return --n;
			    })
			});
		});
    	closest.remove();
		
    	$("body").css("cursor", "default");

    });


	$('body').on('keyup', '.find_specialities', function() {
    	var widgetfilterspecialist = $(this).closest('.doc-widgetfilterspecialist');
	    var s_val = $(this).val();
	    delay(function(){
	    		val = $.trim(s_val);
		    	// alert(s_val);
		    	widgetfilterspecialist.find('.doc-widgetcontent .doc-checkbox').each(function(index){
					text = $(this).find('label').text();
					if (text.toLowerCase().indexOf(val) >= 0) {
						$(this).show();
					}else {
						$(this).hide();
					}
				});
	    }, 1000 );
	});

	/*$(document).click(function(e) {
	    var target = e.target;
	    var closest = $('.specialities-search-wrap').closest('.doc-select');
	    if (!$(target).is(closest) && !$(target).parents().is(closest)) {
	        $('.specialities-search-wrap').hide();
	    }else {
	        $('.specialities-search-wrap').show();
	    }
	});*/
	$('body').on('click', '.close_specialities_wrap', function() {		
	    $('.specialities-search-wrap .content').hide();
    });
	$('body').on('click', '.doc-widgetheading', function() {
		$( '.specialities-search-wrap .content' ).show();
	});
	
	
	$('body').on('click', '.tg_cancel_package .confirmbox a.yes', function() {
		// alert(document.URL);
		var current_url      = document.URL;     // Returns full URL
			var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	        
			$('body').append(loder_html);
			var _this = $(this);
			
			 jQuery.ajax({
	            type: "POST",
	            url: scripts_vars.ajaxurl,
	            data: 'action=cancel_subscription',
	            dataType: "json",
	            success: function(response) {
					$('.modal.tg-confirmpopup').modal('toggle');
					jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						setTimeout(function(){location.href = current_url} , 3000); 
						// window.location = current_url;
					}else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
				}
	        });


    });
	
	$('body').on('click', '.custom_button .tg-btn', function() {

		// alert('sadgsdg');
		// return false;
		var current_val = $('.booking_date').val();
		var olddate = new Date(current_val);

		if ($(this).hasClass('prev_day')) {
			var strong_date = moment(current_val).add('days', -1).format('MMM D, dddd');
			var slot_date	= moment(current_val).add('days', -1).format('YYYY-MM-DD');
		}else if($(this).hasClass('next_day')) {
			var strong_date = moment(current_val).add('days', 1).format('MMM D, dddd');
			var slot_date	= moment(current_val).add('days', 1).format('YYYY-MM-DD');
		}else if($(this).hasClass('this_month')) {
			var now = new Date();
			var strong_date = moment().format('MMM D, dddd');
			var slot_date	= moment().format('YYYY-MM-DD');
		}else if($(this).hasClass('next_month')) {
			var strong_date = moment(current_val).add('months', 1).format('MMM D, dddd');
			var slot_date	= moment(current_val).add('months', 1).format('YYYY-MM-DD');
		}else {
			return false;
		}

		jQuery('.booking-pickr strong').html(strong_date);		
		jQuery('.booking_date').val(slot_date);

		var _this	= jQuery(this);
		var data_id	= jQuery('.online-booking').data('id');
		
		var dataString = 'slot_date='+slot_date+'&data_id='+data_id+'&action=docdirect_get_booking_step_two';
		
		jQuery('body').append(loder_html);
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				jQuery('body').find('.docdirect-loader-wrap').remove();
				jQuery('body').find('.docdirect-site-wrap').remove();
				Z_Steps.booking_step	= 2;
				jQuery('.step-two-slots .tg-timeslotswrapper').html(response.data);
				docdirect_booking_calender();
			}
		});

    });
	
	jQuery(document).on('click', '.tg-timeslotswrapper .next_avai', function() {

		var will_date = $(this).data('date');
		var strong_date = moment(will_date).format('MMM D, dddd');
		var slot_date	= moment(will_date).format('YYYY-MM-DD');
		jQuery('.booking-pickr strong').html(strong_date);		
		jQuery('.booking_date').val(slot_date);

		var data_id	= jQuery('.tg-appointmenttabcontent').data('id');
		
		jQuery('body').append(loder_html);
		var dataString = 'slot_date='+slot_date+'&data_id='+data_id+'&action=docdirect_get_booking_step_two';
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				jQuery('body').find('.docdirect-loader-wrap').remove();
				jQuery('body').find('.docdirect-site-wrap').remove();
				Z_Steps.booking_step	= 2;
				jQuery('.step-two-slots .tg-timeslotswrapper').html(response.data);
				docdirect_booking_calender();
			}
		});

    });

    // if author page
	if ($('.tg-online-booking').length) {
                        var data_id = jQuery('.online-booking').data('id');
                        var dataString = 'data_id='+data_id+'&action=docdirect_get_booking_step_two';
                        jQuery.ajax({
                            type: "POST",
                            url: scripts_vars.ajaxurl,
                            data: dataString,
                            dataType:"json",
                            success: function(response) {
                                jQuery('.step-two-slots .tg-timeslotswrapper').html(response.data);
                                
                            }
                        });
	}

	$('body').on('click', '.tg-section-btn button', function() {
		
		var _this	= jQuery(this);
		$('.tg-section-btn button').removeClass('active');
		_this.addClass('active');
		var target	= _this.data('target');
		$('html, body').animate({
                scrollTop: $("."+target).offset().top - 170
            }, 1000);


    });


	if ($('.tg-section-btn').length) {
		var maptop = $('.tg-section-map').offset().top;
		var userdetailtop = $('.tg-userratingtinfo').offset().top;
		var lastScrollTop = 0;
	    $(window).scroll(function(){
			var st = $(this).scrollTop();
	      	if ( st < lastScrollTop && (st > maptop || st > userdetailtop) ) {
	          	$('.tg-section-btn').addClass('fixed');
		    } else {
		        $('.tg-section-btn').removeClass('fixed');
		    }
			// console.log(st+'vs'+lastScrollTop);
			lastScrollTop = st;
		});
	}




});


	var loder_html	= '<div class="docdirect-loader-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
		
	/*-------------------------------------------------
	 * Appointment process
	 *
	 *-------------------------------------------------*/
	 
	//Tabs Management
	//jQuery('.booking-model-contents .tg-navdocappointment li').not('.active').addClass('disabled');
    //jQuery('.booking-model-contents .tg-navdocappointment li').not('.active').find('a').removeAttr("data-toggle");
	
	//Next step
	var Z_Steps = {};
	Z_Steps.booking_step = {};
	window.Z_Steps = Z_Steps;
	Z_Steps.booking_step	= 1;
					
	jQuery(document).on('click','.kt_bk-step-next',function(e){
			Z_Steps.booking_step	== 1
		e.preventDefault();
		var _this	= jQuery(this);
		
		var data_id	= jQuery('.tg-appointmenttabcontent').data('id');
		
		//Step 1 data
		var bk_service	= _this.parents(".tg-appointmenttabcontent").find('.bk_service option:selected').val();
		
		//Step 2 data
		var bk_subject	     = _this.parents(".tg-appointmenttabcontent").find('input[name="subject"]').val();
		var bk_name	   		= _this.parents(".tg-appointmenttabcontent").find('input[name="username"]').val();
		var bk_userphone	   = _this.parents(".tg-appointmenttabcontent").find('input[name="userphone"]').val();
		var bk_useremail	   = _this.parents(".tg-appointmenttabcontent").find('input[name="useremail"]').val();
		var bk_booking_note	= _this.parents(".tg-appointmenttabcontent").find('textarea[name="booking_note"]').val();
		
		//Check step 1
		if (bk_service ==  '') {
			jQuery.sticky('Please select service', {classList: 'important', speed: 200, autoclose: 5000});
		}else if( bk_service
			&&
			Z_Steps.booking_step	== 1
		) {

			var dataString = 'data_id='+data_id+'&action=docdirect_get_booking_step_two';

			if (jQuery('.quickbooking.booking_date').length > 0) {
				var booking_date = jQuery('.quickbooking.booking_date').val();
				var booking_time = jQuery('.quickbooking.booking_time').val();
				var dataString = 'data_id='+data_id+'&booking_date='+booking_date+'&booking_time='+booking_time+'&action=docdirect_get_booking_step_two';
			}
			jQuery('.booking-model-contents').append(loder_html);

			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType:"json",
				success: function(response) {
					jQuery('body').find('.docdirect-loader-wrap').remove();
					jQuery('body').find('.docdirect-site-wrap').remove();

					Z_Steps.booking_step	= 2;
					jQuery('.step-two-slots .tg-timeslotswrapper').html(response.data);
					docdirect_booking_calender();
					docdirect_appointment_tabs(2);
					jQuery('.bk-step-2').trigger('click');
					
				}
			});
		} else if( 
			Z_Steps.booking_step	== 2
		) {
			var is_time_checked	= jQuery('.step-two-slots input[name="slottime"]:checked').val();
			if( !is_time_checked ){
				jQuery.sticky(scripts_vars.booking_time, {classList: 'important', speed: 200, autoclose: 5000});
				return false;
			}
			
			jQuery('.booking-model-contents').append(loder_html);
			var dataString = 'data_id='+data_id+'&action=docdirect_get_booking_step_three';
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType:"json",
				success: function(response) {
					jQuery('body').find('.docdirect-loader-wrap').remove();
					jQuery('body').find('.docdirect-site-wrap').remove();

					Z_Steps.booking_step	= 3;
					jQuery('.step-three-contents').html(response.data);
					docdirect_appointment_tabs(3);
					jQuery('.bk-step-3').trigger('click');
					docdirect_intl_tel_input23();
					
				}
			});
		}  else if(
			bk_subject 
			&&
			bk_name 
			&&
			bk_userphone
			&& 
			bk_useremail
			&& 
			bk_booking_note
			&& 
			Z_Steps.booking_step	== 3
		) {
			if( !( docdirect_isValidEmailAddress(bk_useremail) ) ){
				jQuery('body').find('.docdirect-loader-wrap').remove();
				jQuery('body').find('.docdirect-site-wrap').remove();
				jQuery.sticky(scripts_vars.valid_email, {classList: 'important', speed: 200, autoclose: 5000});
				return false;
			}
			
			jQuery('.booking-model-contents').append(loder_html);
			var dataString = 'data_id='+data_id+'&action=docdirect_get_booking_step_four';
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType:"json",
				success: function(response) {
					jQuery('body').find('.docdirect-loader-wrap').remove();
					jQuery('body').find('.docdirect-site-wrap').remove();

					Z_Steps.booking_step	= 4;
					jQuery('.step-four-contents').html(response.data);
					jQuery('.bk-step-4').trigger('click');
					docdirect_intl_tel_input23();
					docdirect_appointment_tabs(4);
					
				}
			});
		}  else if( 
			Z_Steps.booking_step	== 4
		) {
			jQuery('.booking-model-contents').append(loder_html);
			var serialize_data	= jQuery('.appointment-form').serialize();
			var dataString = serialize_data+'&data_id='+data_id+'&action=docdirect_do_process_booking';
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType:"json",
				success: function(response) {
					jQuery('body').find('.docdirect-loader-wrap').remove();
					jQuery('body').find('.docdirect-site-wrap').remove();
					if (response.type == 'error') {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
						Z_Steps.booking_step	== 4
					}else {
						if (response.payment_type == 'paypal') {
							jQuery('body').append(response.form_data);
							//Z_Steps.booking_step	= 1;
							//jQuery('.booking-step-button').find('button').prop('disabled', true);
							
						} else if (response.payment_type == 'stripe') {
							
							var obj = [];
							jQuery.each(response, function(index, element) {
								obj[index] = element;
							});
							
							var handler = StripeCheckout.configure({
								key: obj.key,
								token: function(token) {
									 jQuery('body').append(loder_html);
									 jQuery.ajax({
										type: "POST",
										url: scripts_vars.ajaxurl,
										data: { 
											'action': 'docdirect_complete_booking_stripe_payment',
											
											'username': obj.username,
											'email': obj.email,
											'order_no': obj.order_no,
											'user_to': obj.user_to,
											'user_from': obj.user_from,
											'subject': obj.subject,
											'process': obj.process,
											'name': obj.name,
											'amount': obj.amount,
											'total_amount': obj.total_amount,
											'currency': obj.currency,
											'data': obj.data,
											'process': '',
											'type': obj.type,
											'payment_type': obj.payment_type,
											'token': token,
										},
										dataType: "json",
										success: function(response) {
											handler.close();
											
											jQuery('body').find('.docdirect-loader-wrap').remove();
					jQuery('body').find('.docdirect-site-wrap').remove();
											jQuery('.step-five-contents').html(response.data);
											Z_Steps.booking_step	= 1;
											jQuery('.bk-step-5').trigger('click');
											docdirect_appointment_tabs(5);
											jQuery('.step-one-contents, .step-two-contents, .step-three-contents, .step-four-contents').remove();
											jQuery('.booking-step-button').find('button').prop('disabled', true);
										}
									});
								}
							});

							handler.open({
							  name: obj.name,
							  description: obj.subject,
							  amount: obj.amount,
							  email: obj.email,
							  currency: obj.currency,
							  allowRememberMe: false,
							  opened:function(){
								//Some Action
							  },
							  closed:function(){
								//Reload
							  }
							});
							
						} else{
							jQuery('body').find('.docdirect-loader-wrap').remove();
							jQuery('body').find('.docdirect-site-wrap').remove();
							jQuery('.step-five-contents').html(response.data);
							Z_Steps.booking_step	= 1;
							jQuery('.bk-step-5').trigger('click');
							docdirect_appointment_tabs(5);
							jQuery('.step-one-contents, .step-two-contents, .step-three-contents, .step-four-contents').remove();
							jQuery('.booking-step-button').find('.bk-step-prev').remove();
							jQuery('.booking-step-button').find('.kt_bk-step-next').remove();
							// jQuery('.booking-step-button').find('.kt_bk-step-next').html(scripts_vars.finish);
							jQuery('.booking-step-button').find('.hidden').removeClass('hidden').addClass('kt_bk-step-next');
							jQuery('.booking-step-button').find('.kt_bk-step-next').removeClass('kt_bk-step-next').addClass('finish-booking');
						}	
					}
				}
			});
		} 
		
	});

	//Finish Booking
	jQuery(document).on('click','.kt_bk-step-next.finish-booking',function(e){
		window.location.reload();
	});

	jQuery(document).on('click','.remove_reply_button',function(e){
		var _this	= jQuery(this);
		li = _this.closest('li');
    	var data_id = li.attr('id').match(/rv-(\d+)/)[1];

		jQuery('body').append(loder_html);
		var dataString = 'data_id='+data_id+'&action=remove_reply';
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();

				li.remove();
				
			}
		});
	});

	jQuery(document).on('click','.submit_bookingonline',function(e){
		e.preventDefault();
		var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	    
		var _this	= jQuery(this);
		var data_id	= jQuery('.tg-appointmenttabcontent').data('id');

		var bk_service	= _this.parents(".online-booking").find('.bk_service option:selected').val();
			var is_time_checked	= jQuery('.step-two-slots input[name="slottime"]:checked').val();

		if (bk_service ==  '') {
			jQuery.sticky('Please select service', {classList: 'important', speed: 200, autoclose: 5000});
		}else if( !is_time_checked ) {
			jQuery.sticky(scripts_vars.booking_time, {classList: 'important', speed: 200, autoclose: 5000});
		}else {
			jQuery('body').append(loder_html);
			var dataString = 'data_id='+data_id+'&action=docdirect_get_booking_step_three';
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType:"json",
				success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();

					Z_Steps.booking_step	= 3;
					jQuery('.step-three-contents').html(response.data);
					docdirect_appointment_tabs(3);
					jQuery('.bk-step-3').trigger('click');
					docdirect_intl_tel_input23();
					$('.tg-appointmentpopup').modal('toggle');
					
				}
			});
		}


    });

	jQuery(document).on('change','.disable_appointment',function(e){
		e.preventDefault();
		var _this	= jQuery(this);
		var option = _this.find('option:selected').val();
		_this.removeClass('enable disable').addClass(option);
    });

	jQuery(document).on('change','.bk_service',function(e){
		e.preventDefault();
		var _this	= jQuery(this);
		var option = _this.find('option:selected').val();
		var idx = this.selectedIndex; 
		$('.bk_service option[value='+option+']').attr('selected','selected');
    });

	jQuery(document).on('change','.tg-timeslotswrapper .tg-radio input[type=radio][name=slottime]',function(e){
		e.preventDefault();
		var _this	= jQuery(this);
		var option = _this.val();
		$('.tg-radio input[type=radio][name=slottime][value='+option+']').attr('checked','checked');
    });

	jQuery(document).ready(function($) {
		jQuery('.booking-pickr').datetimepicker({
		  format:'Y-m-d',
		  minDate: new Date(),
		  timepicker:false,
		  onChangeDateTime:function(dp,$input){
			var slot_date	= moment(dp).format('YYYY-MM-DD');
			jQuery('.booking-pickr strong').html(moment(dp).format('MMM D, dddd'));
			
			jQuery('.booking_date').val(slot_date);
			
			var _this	= jQuery(this);
			// var data_id	= jQuery('.tg-appointmenttabcontent').data('id');
	        var data_id = jQuery('.online-booking').data('id');
			
			jQuery('.booking-model-contents').append(loder_html);
			var dataString = 'slot_date='+slot_date+'&data_id='+data_id+'&action=docdirect_get_booking_step_two';
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType:"json",
				success: function(response) {
					jQuery('body').find('.docdirect-loader-wrap').remove();
					Z_Steps.booking_step	= 2;
					jQuery('.step-two-slots .tg-timeslotswrapper').html(response.data);
					docdirect_booking_calender();
				}
			});
			return false;
		  }
		});
	});

	
	/*
	 * @Make Review
	 * @return{}
	*/
	jQuery(document).on('click','.kt_make-review',function(e){
		e.preventDefault();
		var $this 	= jQuery(this);
		
		var serialize_data	= $this.parents('.form-review').serialize();
		var dataString = serialize_data+'&action=docdirect_make_review';
		
		jQuery('.message_contact').html('').hide();
		jQuery($this).append("<i class='fa fa-refresh fa-spin'></i>");
		jQuery('.message_contact').removeClass('alert-success');
		jQuery('.message_contact').removeClass('alert-danger');

		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				$this.find('i').remove();
				jQuery('.message_contact').show();
				if( response.type == 'error' ) {
					jQuery('.message_contact').addClass('alert alert-danger').show();
					jQuery('.message_contact').html(response.message);
					if( response.type2 == 'modal' ) {
						$('.tg-reviewpopup').modal('toggle');
					}
				} else{
					jQuery('.message_contact').addClass('alert alert-success').show();
					jQuery('.message_contact').html(response.message);
					if( response.html == 'refresh' ){
						window.location.reload();
					}
					$this.parents('.form-review').find('.contact_form').get(0).reset();
					
				}
			}
		});
		
		return false;
		
	});

	jQuery(document).on('click','.kt_make-review_appointment',function(e){
		e.preventDefault();
		var $this 	= jQuery(this);
		
		var serialize_data	= $this.parents('.form-review').serialize();
		var dataString = serialize_data+'&action=docdirect_make_review_appointment';

		var redirect = $('.redirect').val();
		
		jQuery('.message_contact').html('').hide();
		jQuery($this).append("<i class='fa fa-refresh fa-spin'></i>");
		jQuery('.message_contact').removeClass('alert-success');
		jQuery('.message_contact').removeClass('alert-danger');

		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				$this.find('i').remove();
				jQuery('.message_contact').show();
				if( response.type == 'error' ) {
					jQuery('.message_contact').addClass('alert alert-danger').show();
					jQuery('.message_contact').html(response.message);
					if( response.type2 == 'modal' ) {
						$('.tg-reviewpopup').modal('toggle');
					}
				} else{
					jQuery('.message_contact').addClass('alert alert-success').show();
					jQuery('.message_contact').html(response.message);
					if( response.html == 'refresh' ){
						window.location = redirect;
					}
					// $this.parents('.form-review').find('.contact_form').get(0).reset();
					
				}
			}
		});
		
		return false;
		
	});
	
	function makeString($length) {
	    var text = "";
	    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	    for( var i=0; i < $length; i++ )
	        text += possible.charAt(Math.floor(Math.random() * possible.length));

	    return text;
	}
	jQuery(document).on('click','.gen_code',function(e){
		e.preventDefault();
		var _this 	= jQuery(this);
		text = makeString(16);
		_this.parent().siblings('input').val(text);
		
	});
	//Time Pciker
	jQuery('.headling_date').datetimepicker({
		  format:'Y-m-d',
	  	  // minDate: new Date(),
		  timepicker:false
	});
	

	jQuery(document).on('click', '.invite_review', function(e) {
		var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	    
		e.preventDefault();
		var _this = $(this);
    	var form = $(this).closest('form');
    	form_data = form.serialize();
		jQuery('body').append(loder_html);
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: form_data + '&action=submit_invite_review',
            dataType: "json",
            success: function(response) {          
				jQuery('body').find('.docdirect-site-wrap').remove();  	
				if (response.type == 'error') {
					jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
				}else {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
					form[0].reset();
				}
			    	
            }
        });
    });

	jQuery(document).on('click', '.remove_invite', function(e) {
		var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	    
		e.preventDefault();
		var _this = $(this);
		var tr = _this.closest('tr');
    	var post_id = $(this).data('id');
		jQuery('body').append(loder_html);
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: 'post_id='+ post_id + '&action=remove_invite',
            dataType: "json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();  	
				if (response.type == 'error') {
					jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
				}else {
            		tr.remove();
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
				}
			    	
            }
        });
    });
	jQuery(document).on('click', 'a.dropdown-button-group', function(e) {

		if ($(this).closest('.form-group').find('.dropdown-input-group').is(":visible")) {
			$(this).closest('.form-group').find('.dropdown-input-group').hide();
		}else {
			$('.dropdown-input-group').hide();
			$(this).closest('.form-group').find('.dropdown-input-group').toggle();
		}

    });

	$('body').on('click', '.dropdown-input-group .close_specialities_wrap', function() {
	    $(this).closest('.dropdown-input-group ').hide();
    });

	jQuery(document).on('click', '.dropdown-input-group li', function(e) {

		if(!$(this).closest('.form-group').hasClass('direct_search')) {
			var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
		    
			var id = $(this).data('id');
			var slug = $(this).data('slug');
			var text = $(this).text();
			$('.dropdown-input-group').hide();
			$(this).closest('.form-group').find('.dropdown-button-group').html(text);

			if( $(this).closest('.form-group').hasClass('user-types') ) {
				$(this).closest('.form-group').find('.select_category').val(id);
			}else if( $(this).closest('.form-group').hasClass('insurers') ) {
				$(this).closest('.form-group').find('.select_category').val(slug);
			}else {
				$('.specialities .dropdown-button-group').html(text+' - ');
				$('.dynamic-title').html(text);
				$(this).closest('.form-group').find('.select_category').val(slug);

				jQuery('body').append(loder_html);
		  		jQuery.ajax({
		            type: "POST",
		            url: scripts_vars.ajaxurl,
		            data: 'id='+ id + '&action=load_speacialties',
		            dataType: "json",
		            success: function(response) {
						
						jQuery('body').find('.docdirect-site-wrap').remove();
					    $('.specialities .dropdown-input-group .dropdown-wrap').html(response.data);
					    $('.specialities').css('display', 'inline-block');
					    $('.specialities .dropdown-input-group').show();

		            }
		        });
			}
		}

    });

	$('body').on('click', '.dropdown-wrap .doc-checkbox label', function() {
		// alert('alooo');
		setTimeout(function() {
	        var val = $(".dropdown-wrap input[name=speciality]").val();
	        var arr = [];
		    $('.dropdown-wrap .doc-checkbox input:checked').each(function(){        
		        var values = $(this).siblings('label').text();
		        arr.push(values);
		    });
		    // alert(arr[0]);
		    var slt = $(this).closest('.form-group.specialities').find('.dropdown-button-group');
		    if (arr.length == 0) {
		    	$('.form-group.specialities .dropdown-button-group').html('Select Specialities');
		    }else if (arr.length < 2) {
		    	var text = 'Single';
		    	$('.form-group.specialities .dropdown-button-group').html(arr[0]);
		    }else {
		    	$('.form-group.specialities .dropdown-button-group').html('Multiple Selection');
		    }
		    var length = $('input[name=speciality]').length;
		    // alert();
	    }, 300);
    });

    $(document).click(function(e) {
	    var target = e.target;
	    var closest = $(this).closest('.dropdown-input-group');
	    if (!$(target).is('.dropdown-button-group') && 
	    	!$(target).is('.dropdown-input-group') && 
	    	!$(target).is('.dropdown-input-group li') && 
	    	!$(target).parents().is('.dropdown-input-group') && 
	    	!$(target).parents().is('.form-group')
	    	) {
	        $('.dropdown-input-group').hide();
	    }/*else {
	        $('.dropdown-input-group').show();
	    }*/
	});

	jQuery(document).ready(function(){
	    jQuery('.wrap_item').jScrollPane({
	    	autoReinitialise: true
	    });
	    $('.scroll-pane').jScrollPane();
	    /*jQuery('.tg-affiliation .aff_group.tg-haslayout .list_user > div').jScrollPane({
	    	autoReinitialise: true
	    });*/
	});


    $(document).on('show.bs.modal', '.modal', function (event) {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

	$('body').on('click', '.tg-quickbooking .tg-radio label', function() {
		var disabled = $(this).siblings('input').attr('disabled');
		if (disabled != 'disabled') {
			$('.modal.tg-confirmpopup').modal('toggle');
		}

	});

	function kt_recursion() {
		jQuery('.booking-pickr').datetimepicker({
		  format:'Y-m-d',
		  minDate: new Date(),
		  timepicker:false,
		  onChangeDateTime:function(dp,$input){
			var slot_date	= moment(dp).format('YYYY-MM-DD');
			jQuery('.booking-pickr strong').html(moment(dp).format('MMM D, dddd'));
			
			jQuery('.booking_date').val(slot_date);
			
			var _this	= jQuery(this);
			// var data_id	= jQuery('.tg-appointmenttabcontent').data('id');
	        var data_id = jQuery('.online-booking').data('id');
			
			jQuery('.booking-model-contents').append(loder_html);
			var dataString = 'slot_date='+slot_date+'&data_id='+data_id+'&action=docdirect_get_booking_step_two';
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType:"json",
				success: function(response) {
					jQuery('body').find('.docdirect-loader-wrap').remove();
					Z_Steps.booking_step	= 2;
					jQuery('.step-two-slots .tg-timeslotswrapper').html(response.data);
					docdirect_booking_calender();
					kt_recursion();
				}
			});
			return false;
		  }
		});
	}

	$('body').on('click', '.quickbooking .view_full, .quickbooking .tg-radio label', function() {

		var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	   
		var _this = $(this);
		var user_id = parseInt(_this.closest('.quickbooking').find('.view_full').data('user_id'));
    	
		// $('.tg-quickbooking').modal('toggle');
        data = 'user_id='+user_id + '&action=quickbooking';
        $.fn.hasAttr = function(name) {  
		   return this.attr(name) !== undefined;
		};
		if($(this).hasAttr('data-date')) {
			fixed_date = $(this).data('date');
	        data = data+'&fixed_date='+fixed_date;
	    }

		jQuery('body').append(loder_html);

  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: data,
            dataType: "json",
            success: function(response) {

            	$('.tg-quickbooking-form').html(response.data);
            	$('.confirm_booking a.view_profile').attr('href', response.userlink);

                var data_id = jQuery('.online-booking').data('id');
                var slot_date = jQuery('.online-booking').data('slot_date');
                // var dataString = 'data_id='+data_id+'&action=docdirect_get_booking_step_two';
        		var dataString = 'slot_date='+slot_date+'&data_id='+data_id+'&action=docdirect_get_booking_step_two';
                jQuery.ajax({
                    type: "POST",
                    url: scripts_vars.ajaxurl,
                    data: dataString,
                    dataType:"json",
                    success: function(response) {
                        jQuery('.step-two-slots .tg-timeslotswrapper').html(response.data);                
						jQuery('body').find('.docdirect-site-wrap').remove();	
						$('.tg-quickbooking').modal('toggle');
						kt_recursion();
                    }
                });
            }
        });
    });

	$('body').on('click', '.confirm_booking .confirmbox a.yes', function(e) {
		if ($(this).hasClass('show_login')) {
			e.preventDefault;
		} else {
			var booking_date = $('.tg-quickbooking-form .booking_date').val();
			var booking_time = $('.tg-quickbooking-form .tg-timeslots input[name=slottime]:checked').val();

			$('.tg-quickbooking-form form input[name=booking_date]').val(booking_date);
			$('.tg-quickbooking-form form input[name=booking_time]').val(booking_time);
			$('.tg-quickbooking-form form').submit();
		}	

    });

	jQuery(document).on('click', '.make-request-btn', function(e){
	    e.preventDefault();
		var _this = $(this);
		id = _this.data('user_id');

		$('body').append(loder_html);
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: 'doctor_id=' + id + '&action=open_modal_request',
        	dataType: "json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				
				$('.tg-request-only').find('.doctor_info').html(response.output);
				$('.tg-request-only').modal('show');

            }
        });

	});

	jQuery(document).on('click', '.request-form .btn-submit', function(e){
	    e.preventDefault();
		var _this = $(this);

    	form_data = $(this).closest('form').serialize();
		$('body').append(loder_html);
  		jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: form_data + '&action=request_appoinment',
        	dataType: "json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				if (response.type == 'error') {
					jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
				}else {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
					$('.tg-request-only').modal('hide');
				}
            }
        });

	});

	var loder_html	= '<div class="docdirect-site-wrap"><div class="docdirect-loader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';
	   	
	$('body').on('click', '.verify_paypal_button', function () {
		var form = $(this).closest('.tg-form-privacy');
		paypal_username = form.find('.paypal_username').val();
		paypal_password = form.find('.paypal_password').val();
		paypal_signature = form.find('.paypal_signature').val();

		jQuery('body').append(loder_html);
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: {
            	'action' : 'verify_paypal',
            	'paypal_username' : paypal_username,
            	'paypal_password' : paypal_password,
            	'paypal_signature' : paypal_signature,
            },
            dataType:"json",
            success: function(response) {
                            
				jQuery('body').find('.docdirect-site-wrap').remove();
				if (response.type == 'error') {
					jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
				}else {
					jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
					window.location.reload();
				}

            }
        });
	});

	$('body').on('click', '.tg-popup-activetrial .confirmbox a.yes', function() {
		
		jQuery('body').append(loder_html);
        jQuery.ajax({
            type: "POST",
            url: scripts_vars.ajaxurl,
            data: {
            	'action' : 'confirm_trial',
            },
            dataType:"json",
            success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();               
				window.location = response.url;
            }
        });
	});

	// Closing a sticky
	$('.sticky-close').on('click', function () {
		$('#' + $(this).parent().attr('id')).dequeue().fadeOut('fast', function(){
			// remove element from DOM
			$(this).remove();
		});
	});

	$('.tg-deleteslot.delete-current-slot').removeClass('delete-current-slot').addClass('kt_delete-current-slot');
	//Delete Time Slot
	jQuery(document).on('change','.donotshowagain',function(e){
		/*if(this.checked) alert('checked box');
        else alert('unchecked box');*/
        $('.tg-deleteslot.kt_delete-current-slot').toggleClass('non-confirm');
	});

	jQuery(document).on('click','.kt_delete-current-slot',function(e){
		e.preventDefault();
		var _this 	= jQuery(this);
		var day 	  = _this.data('day');
		var time     = _this.data('time');
		
		if( day == '' || time == '' ){
			jQuery.sticky(system_error, {classList: 'important', speed: 200, autoclose: 5000});
			return false;
		}
		
		var dataString = 'day='+day+'&time='+time+'&action=docdirect_delete_time_slot';
		if (_this.hasClass('non-confirm')) {
			//Process dadtabase item
			jQuery('body').append(loder_html);
			
			jQuery.ajax({
				type: "POST",
				url: scripts_vars.ajaxurl,
				data: dataString,
				dataType:"json",
				success: function(response) {
					jQuery('body').find('.docdirect-site-wrap').remove();
					if( response.type == 'error' ) {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					} else{
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
						_this.parents('.tg-doctimeslot').remove();
					}
				}
			});
			return false;
		} else {
			var dontshow_msag = confirm_vars.dontshow_msag;
			jQuery.confirm({
				'title': scripts_vars.delete_slot,
				'message': scripts_vars.delete_slot_message+'<div class="checkbox"><div class="doc-checkbox1"><label><input name="dsma" type="checkbox" class="donotshowagain">'+dontshow_msag+'</label></div></div>',
				'buttons': {
					'Yes': {
						'class': 'blue',
						'action': function () {
							//Process dadtabase item
							jQuery('body').append(loder_html);
							
							jQuery.ajax({
								type: "POST",
								url: scripts_vars.ajaxurl,
								data: dataString,
								dataType:"json",
								success: function(response) {
									jQuery('body').find('.docdirect-site-wrap').remove();
									if( response.type == 'error' ) {
										jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
									} else{
										jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
										_this.parents('.tg-doctimeslot').remove();
									}
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
		}
		
		return false;
	});
	jQuery(document).on('click','.kt_delete-slot-date',function(e){
		e.preventDefault();
		
		var _this	= jQuery(this);
		jQuery.confirm({
			'title': scripts_vars.delete_slot_date,
			// 'message': scripts_vars.delete_slot_date_message,
			'message': scripts_vars.delete_slot_date_message+'<div class="checkbox"></div>',
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						_this.parents('.custom-time-periods').remove();
						jQuery('.bk-save-custom-slots').trigger('click');//Save Slots
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

	//Change Appointment Status
	jQuery(document).on('click','.kt_get-process',function(){

		var _this	= jQuery(this);
		
		var type	= _this.data('type');
		var id	  = _this.data('id');
		var location = $('#doctor_approve_booking').find('input[name=location]:checked').val();
		
		var dataString = 'location='+location+'&type='+type+'&id='+id+'&action=docdirect_change_appointment_status';
		
		if( type == 'approve' ) {
			var _title	= scripts_vars.approve_appointment;
			var _message	= scripts_vars.approve_appointment_message;
		} else{
			// var _title	= scripts_vars.cancel_appointment;
			// var _message	= scripts_vars.cancel_appointment_message;
			var _title	= confirm_vars.re_post.title;
			var _message	= confirm_vars.re_post.message;
		}
		
		jQuery.confirm({
			'title': _title,
			'message': _message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						jQuery('body').append(loder_html);
						jQuery.ajax({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: dataString,
							dataType:"json",
							success: function(response) {
								jQuery('body').find('.docdirect-site-wrap').remove();
								if( response.action_type == 'approved' ){
									$('.tg-approve-booking').modal('hide');
									$('.open_approve_modal[data-id='+id+']').siblings('.tg-btnclose').remove();
									$('.open_approve_modal[data-id='+id+']').text('Approved').prepend('<i class="fa fa-check">').addClass('appointment-actioned').removeClass('open_approve_modal');
									// _this.text('Approved').prepend('<i class="fa fa-check">').addClass('appointment-actioned').removeClass('kt_get-process');
										
								} else if( response.action_type == 'cancelled' ){
									_this.parents('tr').remove();
									_this.parents('tr').next('tr').remove();
								} 
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
	//Complete Appointment Status
	jQuery(document).on('click','.kt_action-complete',function(){

		var _this	= jQuery(this);
		
		var id	  = _this.data('id');
		
		var dataString = 'id='+id+'&action=complete_appointment_status';
		
		var title = confirm_vars.complete_appointment.title;
		var message = confirm_vars.complete_appointment.message;
		jQuery.confirm({
			'title': title,
			'message': message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						jQuery('body').append(loder_html);
						jQuery.ajax({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: dataString,
							dataType:"json",
							success: function(response) {
								jQuery('body').find('.docdirect-site-wrap').remove();
								
								_this.text('Completed').prepend('<i class="fa fa-check">').addClass('appointment-actioned').removeClass('kt_action-complete');
									
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

	//Change Appointment Status
	jQuery(document).on('click','.kt_action-confirm',function(){

		var _this	= jQuery(this);
		
		var type	= _this.data('type');
		var id	  = _this.data('id');
		
		var dataString = 'type='+type+'&id='+id+'&action=change_confirm_status';
		
		var title = confirm_vars.confirm_appointment.title;
		var message = confirm_vars.confirm_appointment.message;
		var message2 = confirm_vars.confirm_appointment.message2;

			var _title	= _title;
		if( type == 'yes' ) {
			var _message	= message;
		} else{
			var _message	= message2;
		}
		
		jQuery.confirm({
			'title': _title,
			'message': _message,
			'buttons': {
				'Yes': {
					'class': 'blue',
					'action': function () {
						jQuery('body').append(loder_html);
						jQuery.ajax({
							type: "POST",
							url: scripts_vars.ajaxurl,
							data: dataString,
							dataType:"json",
							success: function(response) {
								jQuery('body').find('.docdirect-site-wrap').remove();
								
								if( response.action_type == 'yes' ){
									_this.prepend('<i class="fa fa-check"></i>').addClass('appointment-actioned').removeClass('kt_action-confirm');
									_this.parents('td').find('.tg-btnclose').remove();
										
								} else if( response.action_type == 'no' ){
									_this.text('Cancelled').prepend('<i class="fa fa-check"></i>').addClass('appointment-actioned').removeClass('kt_action-confirm');
									_this.parents('td').find('.tg-btncheck').remove();
								} 
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

	jQuery(document).on('click', '#doctor_cancel_booking .tg-btn',function(e){
		
	    e.preventDefault();
		var _this = $(this);
		var ap_id = $('.tg-cancel-booking input[name=appointment_id]').val();
		var btn = $('.kt_cancel_booking[data-id="'+ap_id+'"]');
    	form_data = $(this).closest('form').serialize();
		var dataString = form_data+'&action=doctor_cancel_booking';
		jQuery('body').append(loder_html);
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: dataString,
			dataType:"json",
			success: function(response) {
				jQuery('body').find('.docdirect-site-wrap').remove();
				
					if (response.type == 'success') {
						jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 3000,position: 'top-right',});
						$('.tg-cancel-booking').modal('hide');
						btn.siblings('.tg-btncheck').remove();
						btn.text('Cancelled').addClass('appointment-actioned').removeClass('kt_cancel_booking');
									
						// btn.parents('tr').remove();
						// btn.parents('tr').next('tr').remove();				
					}else {
						jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
					}
			}
		});


	});

	jQuery(document).on('click','.kt_cancel_booking',function(){

		var user_to = $(this).data('user_to');
		var appointment_id = $(this).data('id');
		$('.tg-cancel-booking input[name=appointment_id]').val(appointment_id);
		$('.tg-cancel-booking').modal('show');


	});

	jQuery(document).on('click','.kt_popup_review',function(){

		var user_to = $(this).data('user_to');
		var appointment_id = $(this).data('id');
		$('.tg-formleavereview input[name=user_to]').val(user_to);
		$('.tg-formleavereview input[name=appointment_id]').val(appointment_id);
		$('.tg-review-booking').modal('show');


	});

	jQuery('.form-group.social_login').hide();
	//User active
	jQuery(document).on('click','.active-user-type',function(){
		if( jQuery(this).hasClass('visitor-type') ){
			jQuery('.form-group.social_login').show();
		}else {
			jQuery('.form-group.social_login').hide();
		}
	});


	/* ---------------------------------------
     rtegistration Ajax
     --------------------------------------- */
	jQuery('.do-registration-form').on('click', '.kt_do-register-button', function (event) {
		event.preventDefault();
		var _this	= jQuery(this);
		_this.append('<i class="fa fa-refresh fa-spin"></i>');
		
		jQuery('.registration-message').html('').hide();
		
		var _authenticationform	= _this.parents('form.do-registration-form').serialize();
		
		jQuery.ajax({
			type: "POST",
			url: scripts_vars.ajaxurl,
			data: _authenticationform + '&action=docdirect_user_registration',
			dataType: "json",
			success: function (response) {
				_this.find('i.fa-spin').remove();
				jQuery('.registration-message').show();
				
				if (response.type == 'success') {
					$('.modal.tg-user-modal').modal('hide');
					$('.modal.tg-user-reg_success').modal('show');
				} else {
					if( scripts_vars.captcha_settings === 'enable' ) {
						grecaptcha.reset(signup_reset);
					}
					jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
				}
			}
	   });

	});

jQuery(document).ready(function($){	
	
    $('.doc-bloggrid').masonry({
	  // columnWidth: 200,
	  itemSelector: '.doc-verticalaligntop'
	});
    $('.grid').masonry({
	  // columnWidth: 200,
	  itemSelector: '.tg-packageswidth'
	});
    $('.tg-blog-list').masonry({
	  // columnWidth: 200,
	  itemSelector: '.tg-post'
	});

});


//Init detail page Map Scripts
function kt_docdirect_init_detail_map_script( _data_list ){
	var dir_latitude	 = scripts_vars.dir_latitude;
	var dir_longitude	= scripts_vars.dir_longitude;
	var dir_map_type	 = scripts_vars.dir_map_type;
	var dir_close_marker		  = scripts_vars.dir_close_marker;
	var dir_cluster_marker		= scripts_vars.dir_cluster_marker;
	var dir_map_marker			= scripts_vars.dir_map_marker;
	var dir_cluster_color		 = scripts_vars.dir_cluster_color;
	var dir_zoom				  = scripts_vars.dir_zoom;;
	var dir_map_scroll			= scripts_vars.dir_map_scroll;
	var gmap_norecod			  = scripts_vars.gmap_norecod;
	var map_styles			    = scripts_vars.map_styles;


	if( _data_list.status == 'found' ){
		var response_data	= _data_list.users_list;
	    if( typeof(response_data) != "undefined" && response_data !== null ) {
			var location_center = new google.maps.LatLng(response_data[0].latitude,response_data[0].longitude);
		} else {
				var location_center = new google.maps.LatLng(dir_latitude,dir_longitude);
		}
	} else{
		var location_center = new google.maps.LatLng(dir_latitude,dir_longitude);
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
	var lock		   = 'unlock';
	
	if( dir_map_scroll == 'false' ){
		scrollwheel	= false;
		lock		   = 'lock';
	}
	
	var mapOptions = {
		center: location_center,
		zoom: parseInt( dir_zoom ),
		mapTypeId: map_id,
		scaleControl: true,
		scrollwheel: scrollwheel,
		disableDefaultUI: true,
		// clickableIcons: false
	}
	
	var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	
	var styles = docdirect_get_map_styles(map_styles);
	if(styles != ''){
		var styledMap = new google.maps.StyledMapType(styles, {name: 'Styled Map'});
		map.mapTypes.set('map_style', styledMap);
		map.setMapTypeId('map_style');
	}
		
	var bounds = new google.maps.LatLngBounds();
	
	//Zoom In
	if(  document.getElementById('doc-mapplus') ){ 
		 google.maps.event.addDomListener(document.getElementById('doc-mapplus'), 'click', function () {      
		   var current	= parseInt( map.getZoom(),10 );
		   current++;
		   if(current>20){
			   current=20;
		   }
		   map.setZoom(current);
		   jQuery(".infoBox").hide();
		});  
	}
	
	//Zoom Out
	if(  document.getElementById('doc-mapminus') ){ 
		google.maps.event.addDomListener(document.getElementById('doc-mapminus'), 'click', function () {      
			var current	= parseInt( map.getZoom(),10);
			current--;
			if(current<0){
				current=0;
			}
			map.setZoom(current);
			jQuery(".infoBox").hide();
		});  
	}
	
	//Lock Map
	if( document.getElementById('doc-lock') ){ 
		google.maps.event.addDomListener(document.getElementById('doc-lock'), 'click', function () {
			if(lock == 'lock'){
				map.setOptions({ 
						scrollwheel: true,
						draggable: true 
					}
				);
				
				jQuery("#doc-lock").html('<i class="fa fa-unlock-alt" aria-hidden="true"></i>');
				lock = 'unlock';
			}else if(lock == 'unlock'){
				map.setOptions({ 
						scrollwheel: false,
						draggable: false 
					}
				);
				
				jQuery("#doc-lock").html('<i class="fa fa-lock" aria-hidden="true"></i>');
				lock = 'lock';
			}
		});
	}

	//
	if( _data_list.status == 'found' && typeof(response_data) != "undefined" && response_data !== null ){
		jQuery('#gmap-noresult').html('').hide(); //Hide No Result Div
		var markers = new Array();
		var info_windows = new Array();
		
		for (var i=0; i < response_data.length; i++) {
			markers[i] = new google.maps.Marker({
				position: new google.maps.LatLng(response_data[i].latitude,response_data[i].longitude),
				map: map,
				icon: response_data[i].icon,
				title: response_data[i].title,
				animation: google.maps.Animation.DROP,
				visible: true
			});
		
			bounds.extend(markers[i].getPosition());
			
			var boxText = document.createElement("div");
			
			boxText.className = 'directory-detail';
			var innerHTML = "";
			boxText.innerHTML += response_data[i].html.content;
			
			var myOptions = {
				content: boxText,
				disableAutoPan: true,
				maxWidth: 0,
				alignBottom: true,
				pixelOffset: new google.maps.Size( -28, -70 ),
				zIndex: null,
				infoBoxClearance: new google.maps.Size( 1, 1 ),
				isHidden: false,
				closeBoxURL: dir_close_marker,
				pane: "floatPane",
				enableEventPropagation: false
			};
		
			var ib = new InfoBox( myOptions );
			attachInfoBoxToMarker( map, markers[i], ib );
			ib.open(map,markers[i]);

		}
		
		map.fitBounds(bounds);
		
		var listener = google.maps.event.addListener(map, "idle", function() { 
			  if (map.getZoom() > 16) {
				  map.setZoom(parseInt( dir_zoom )); 
			  	  google.maps.event.removeListener(listener); 
			  }
		});

		/* Marker Clusters */
		var markerClustererOptions = {
			ignoreHidden: true,
			styles: [{
				textColor: scripts_vars.dir_cluster_color,
				url: scripts_vars.dir_cluster_marker,
				height: 48,
				width: 48
			}]
		};
		
		var markerClusterer = new MarkerClusterer( map, markers, markerClustererOptions );
	} else{
		jQuery('#gmap-noresult').html(gmap_norecod).show();
	}
}

//Init Map Scripts
function vkl_docdirect_init_map_script( _data_list ){
	var dir_latitude	 = scripts_vars.dir_latitude;
	var dir_longitude	= scripts_vars.dir_longitude;
	var dir_map_type	 = scripts_vars.dir_map_type;
	var dir_close_marker		  = scripts_vars.dir_close_marker;
	var dir_cluster_marker		= scripts_vars.dir_cluster_marker;
	var dir_map_marker			= scripts_vars.dir_map_marker;
	var dir_cluster_color		 = scripts_vars.dir_cluster_color;
	var dir_zoom				  = scripts_vars.dir_zoom;
	var dir_map_scroll			= scripts_vars.dir_map_scroll;
	var gmap_norecod			  = scripts_vars.gmap_norecod;
	var map_styles			    = scripts_vars.map_styles;


	if( _data_list.status == 'found' ){
		var response_data	= _data_list.users_list;
	    if( typeof(response_data) != "undefined" && response_data !== null ) {
			var dir_latitude    = response_data[0].latitude;
			var dir_longitude	= response_data[0].longitude;
			var location_center = new google.maps.LatLng(dir_latitude,dir_longitude);
		} else {
			var location_center = new google.maps.LatLng(dir_latitude,dir_longitude);
		}
	} else{
		var location_center = new google.maps.LatLng(dir_latitude,dir_longitude);
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
	
	var scrollwheel	   = true;
	var lock		   = 'lock';
	
	if( dir_map_scroll == 'false' ){
		scrollwheel	= false;
		lock		   = 'unlock';
		
	}
	
	var mapOptions = {
		center: location_center,
		zoom: parseInt(dir_zoom),
		mapTypeId: map_id,
		scaleControl: true,
		scrollwheel: false,
		disableDefaultUI: true,
		draggable:scrollwheel,
		clickableIcons: false
	}
	
	var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	
	var styles = docdirect_get_map_styles(map_styles);
	if(styles != ''){
		var styledMap = new google.maps.StyledMapType(styles, {name: 'Styled Map'});
		map.mapTypes.set('map_style', styledMap);
		map.setMapTypeId('map_style');
	}
		
	var bounds = new google.maps.LatLngBounds();

	//Zoom In
	if(  document.getElementById('doc-mapplus') ){ 
		 google.maps.event.addDomListener(document.getElementById('doc-mapplus'), 'click', function () {      
		   var current= parseInt( map.getZoom(),10 );
		   current++;
		   if(current>20){
			   current=20;
		   }
		   map.setZoom(current);
		   jQuery(".infoBox").hide();
		});  
	}
	
	//Zoom Out
	if(  document.getElementById('doc-mapminus') ){ 
		google.maps.event.addDomListener(document.getElementById('doc-mapminus'), 'click', function () {      
			var current= parseInt( map.getZoom(),10);
			current--;
			if(current<0){
				current=0;
			}
			map.setZoom(current);
			jQuery(".infoBox").hide();
		});  
	}
	
	//Lock Map
	if( document.getElementById('doc-lock') ){ 
		google.maps.event.addDomListener(document.getElementById('doc-lock'), 'click', function () {
			if(lock == 'lock'){
				map.setOptions({ 
						scrollwheel: false,
						draggable: false 
					}
				);
				
				jQuery("#doc-lock").html('<i class="fa fa-lock" aria-hidden="true"></i>');
				lock = 'unlock';
			}else if(lock == 'unlock'){
				map.setOptions({ 
						scrollwheel: false,
						draggable: true 
					}
				);
				jQuery("#doc-lock").html('<i class="fa fa-unlock" aria-hidden="true"></i>');
				lock = 'lock';
			}
		});
	}
	//
	
	if( _data_list.status == 'found' && typeof(response_data) != "undefined" && response_data !== null ){
		jQuery('#gmap-noresult').html('').hide(); //Hide No Result Div
		var markers = new Array();
		var info_windows = new Array();
		var array_title = new Array();
		var clusterMarker = [];

		var spiderConfig = {
			 markersWontMove: true, 
			 markersWontHide: true, 
			 keepSpiderfied: true, 
			 circleSpiralSwitchover: 40 
		};
		
		// Create OverlappingMarkerSpiderfier instsance
		var markerSpiderfier = new OverlappingMarkerSpiderfier(map, spiderConfig);
		
		for (var i=0; i < response_data.length; i++) {
			
			array_title.push(response_data[i].title);
			markers[i] = new google.maps.Marker({
				position: new google.maps.LatLng(response_data[i].latitude,response_data[i].longitude),
				map: map,
				icon: response_data[i].icon,
				title: response_data[i].title,
				animation: google.maps.Animation.DROP,
				visible: true
			});
			
			bounds.extend(markers[i].getPosition());
			var boxText = document.createElement("div");
			boxText.className = 'directory-detail';
			var innerHTML = "";
			boxText.innerHTML += response_data[i].html.content;
			
			var myOptions = {
				content: boxText,
				disableAutoPan: true,
				maxWidth: 0,
				alignBottom: true,
				pixelOffset: new google.maps.Size( -175, -70 ),
				zIndex: null,
				closeBoxMargin: "0 0 -16px -16px",
				closeBoxURL: dir_close_marker,
				infoBoxClearance: new google.maps.Size( 1, 1 ),
				isHidden: false,
				pane: "floatPane",
				enableEventPropagation: false
			};
		
			var ib = new InfoBox( myOptions );
			attachInfoBoxToMarker( map, markers[i], ib );
			markerSpiderfier.addMarker(markers[i]);  // adds the marker to the spiderfier
		}
		
		var markerClustererOptions = {
			ignoreHidden: true,
			maxZoom: 15,
			styles: [{
				textColor: scripts_vars.dir_cluster_color,
				url: scripts_vars.dir_cluster_marker,
				height: 48,
				width: 48
			}]
		};
		
		/*// Create cluster	
		new MarkerClusterer(map, markers, markerClustererOptions);
		
		//Set center from theme settings
		if( scripts_vars.center_point === 'enable' ) {
			var dir_latitude	 = scripts_vars.dir_latitude;
			var dir_longitude	 = scripts_vars.dir_longitude;
			currCenter  = new google.maps.LatLng(dir_latitude,dir_longitude);
			google.maps.event.trigger(map, 'resize');
			map.setCenter(currCenter);
		}*/
		
		/* Marker Clusters */
		var markerClustererOptions = {
			ignoreHidden: true,
			//maxZoom: 14,
			styles: [{
				textColor: scripts_vars.dir_cluster_color,
				url: scripts_vars.dir_cluster_marker,
				height: 48,
				width: 48
			}]
		};
		
		var markerClusterer = new MarkerClusterer( map, markers, markerClustererOptions );
        // now add a click listerner for markerCluster
		google.maps.event.addListener(markerClusterer, 'clusterclick', function(cluster) {
			
			$('.info-content').parent().parent().parent().parent().remove();
			//Close active window if exists
			var markers = cluster.getMarkers();
           	var array = [];
	        var num = 0;
			var contentString = function(markers) {
	            var showInInfoWindow = "";
	            return '<div class="info-content">' +
	                        '<div id="bodyContent">' +
	                        showInInfoWindow +
	                        '</div>' +
	                    '</div>';
	        }
			var infowindow = function(contentString) {
	            return new google.maps.InfoWindow({
	                content: contentString,
	                position: cluster.getCenter()
	            });
	        }
	        var iw = infowindow(contentString(markers));
	        var infowindow = new google.maps.InfoWindow({
	        	content: '<div class="info-content"><div id="bodyContent"></div></div>',
	            position: cluster.getCenter()
	        });
           	if (map.getZoom() > 18) {

	            var array_id = [];
	            for(i=0;i<markers.length;i++) {
	                // you can add IDs, click handlers etc etc here.
			        var a = array_title.indexOf(markers[i].getTitle());
			        // alert(response_data[a].user_id);
			        array_id.push(response_data[a].user_id);
	                // showInInfoWindow +="<div>"
	                // showInInfoWindow +=markers[i].title
	                // showInInfoWindow +="</div>"
	            }
	            var list_id = array_id.join();
	            // alert(list_id);
				// jQuery('body').append(loder_html);
	            jQuery.ajax({
		            type: "POST",
		            url: scripts_vars.ajaxurl,
		            data: 'action=kt_user&list_id='+list_id,
		            // dataType: "json",
		            success: function(response) {
						jQuery('body').find('.docdirect-site-wrap').remove();
		        		infowindow.open(map);
		            	jQuery('.info-content > #bodyContent').html(response);
		            	//Store new window in global variable
		            }
		        });

		        for(i = 0; i < markers.length; i++) {		         
		            num++;
		            array.push(markers[i].getTitle() + '<br>');
		        }
	            var g_zoom = map.getZoom();
	            // var m_zoom = MarkerClusterer.getMaxZoom();
		        // alert(g_zoom);
		        // alert(markers.length);
	           /*infoWindow.setContent(markers.length + " markers<br>"+array);
	           infoWindow.setPosition(cluster.getCenter());
	           infoWindow.open(map);*/
	        }  

	    });
		
	} else{
		jQuery('#gmap-noresult').html(gmap_norecod).show();
	}
}

//Init Map Scripts
function kt_docdirect_init_map_script( _data_list ){
	var dir_latitude	 = scripts_vars.dir_latitude;
	var dir_longitude	= scripts_vars.dir_longitude;
	var dir_map_type	 = scripts_vars.dir_map_type;
	var dir_close_marker		  = scripts_vars.dir_close_marker;
	var dir_cluster_marker		= scripts_vars.dir_cluster_marker;
	var dir_map_marker			= scripts_vars.dir_map_marker;
	var dir_cluster_color		 = scripts_vars.dir_cluster_color;
	var dir_zoom				  = scripts_vars.dir_zoom;
	var dir_map_scroll			= scripts_vars.dir_map_scroll;
	var gmap_norecod			  = scripts_vars.gmap_norecod;
	var map_styles			    = scripts_vars.map_styles;


	if( _data_list.status == 'found' ){
		var response_data	= _data_list.users_list;
	    if( typeof(response_data) != "undefined" && response_data !== null ) {
			var dir_latitude    = response_data[0].latitude;
			var dir_longitude	= response_data[0].longitude;
			var location_center = new google.maps.LatLng(dir_latitude,dir_longitude);
		} else {
			var location_center = new google.maps.LatLng(dir_latitude,dir_longitude);
		}
	} else{
		var location_center = new google.maps.LatLng(dir_latitude,dir_longitude);
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
	
	var scrollwheel	   = true;
	var lock		   = 'lock';
	
	if( dir_map_scroll == 'false' ){
		scrollwheel	= false;
		lock		   = 'unlock';
		
	}
	
	var mapOptions = {
		center: location_center,
		zoom: parseInt(dir_zoom),
		mapTypeId: map_id,
		scaleControl: true,
		scrollwheel: false,
		disableDefaultUI: true,
		draggable:scrollwheel,
		// clickableIcons: false
	}
	
	var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
	
	var styles = docdirect_get_map_styles(map_styles);
	if(styles != ''){
		var styledMap = new google.maps.StyledMapType(styles, {name: 'Styled Map'});
		map.mapTypes.set('map_style', styledMap);
		map.setMapTypeId('map_style');
	}
		
	var bounds = new google.maps.LatLngBounds();

	//Zoom In
	if(  document.getElementById('doc-mapplus') ){ 
		 google.maps.event.addDomListener(document.getElementById('doc-mapplus'), 'click', function () {      
		   var current= parseInt( map.getZoom(),10 );
		   current++;
		   if(current>20){
			   current=20;
		   }
		   map.setZoom(current);
		   jQuery(".infoBox").hide();
		});  
	}
	
	//Zoom Out
	if(  document.getElementById('doc-mapminus') ){ 
		google.maps.event.addDomListener(document.getElementById('doc-mapminus'), 'click', function () {      
			var current= parseInt( map.getZoom(),10);
			current--;
			if(current<0){
				current=0;
			}
			map.setZoom(current);
			jQuery(".infoBox").hide();
		});  
	}
	
	//Lock Map
	if( document.getElementById('doc-lock') ){ 
		google.maps.event.addDomListener(document.getElementById('doc-lock'), 'click', function () {
			if(lock == 'lock'){
				map.setOptions({ 
						scrollwheel: false,
						draggable: false 
					}
				);
				
				jQuery("#doc-lock").html('<i class="fa fa-lock" aria-hidden="true"></i>');
				lock = 'unlock';
			}else if(lock == 'unlock'){
				map.setOptions({ 
						scrollwheel: false,
						draggable: true 
					}
				);
				jQuery("#doc-lock").html('<i class="fa fa-unlock" aria-hidden="true"></i>');
				lock = 'lock';
			}
		});
	}
	//
	
	
	if( _data_list.status == 'found' && typeof(response_data) != "undefined" && response_data !== null ){
		jQuery('#gmap-noresult').html('').hide(); //Hide No Result Div
		var markers = new Array();
		var info_windows = new Array();
		var array_title = new Array();

		for (var i=0; i < response_data.length; i++) {
			array_title.push(response_data[i].title);
			markers[i] = new google.maps.Marker({
				position: new google.maps.LatLng(response_data[i].latitude,response_data[i].longitude),
				map: map,
				icon: response_data[i].icon,
				title: response_data[i].title,
				animation: google.maps.Animation.DROP,
				visible: true
			});
		
			bounds.extend(markers[i].getPosition());
			
			var boxText = document.createElement("div");
			
			boxText.className = 'directory-detail';
			var innerHTML = "";
			boxText.innerHTML += response_data[i].html.content;
			
			var myOptions = {
				content: boxText,
				disableAutoPan: true,
				maxWidth: 0,
				alignBottom: true,
				pixelOffset: new google.maps.Size( -175, -70 ),
				zIndex: null,
				closeBoxMargin: "0 0 -16px -16px",
				closeBoxURL: dir_close_marker,
				infoBoxClearance: new google.maps.Size( 1, 1 ),
				isHidden: false,
				pane: "floatPane",
				enableEventPropagation: false
			};
		
			var ib = new InfoBox( myOptions );
			attachInfoBoxToMarker( map, markers[i], ib );

		}
		
		map.fitBounds(bounds);
		
		var listener = google.maps.event.addListener(map, "idle", function() { 
			  if (map.getZoom() > 16) {
				  map.setZoom(parseInt( dir_zoom )); 
			  	  google.maps.event.removeListener(listener); 
			  }
		});
		
		/* Marker Clusters */
		var markerClustererOptions = {
			ignoreHidden: true,
			//maxZoom: 14,
			styles: [{
				textColor: scripts_vars.dir_cluster_color,
				url: scripts_vars.dir_cluster_marker,
				height: 48,
				width: 48
			}]
		};
		
		var markerClusterer = new MarkerClusterer( map, markers, markerClustererOptions );
        // now add a click listerner for markerCluster
		google.maps.event.addListener(markerClusterer, 'clusterclick', function(cluster) {
			
			$('.info-content').parent().parent().parent().parent().remove();
			//Close active window if exists
			var markers = cluster.getMarkers();
           	var array = [];
	        var num = 0;
			var contentString = function(markers) {
	            var showInInfoWindow = "";
	            return '<div class="info-content">' +
	                        '<div id="bodyContent">' +
	                        showInInfoWindow +
	                        '</div>' +
	                    '</div>';
	        }
			var infowindow = function(contentString) {
	            return new google.maps.InfoWindow({
	                content: contentString,
	                position: cluster.getCenter()
	            });
	        }
	        var iw = infowindow(contentString(markers));
	        var infowindow = new google.maps.InfoWindow({
	        	content: '<div class="info-content"><div id="bodyContent"></div></div>',
	            position: cluster.getCenter()
	        });
           	if (map.getZoom() > 16) {

	            var array_id = [];
	            for(i=0;i<markers.length;i++) {
	                // you can add IDs, click handlers etc etc here.
			        var a = array_title.indexOf(markers[i].getTitle());
			        // alert(response_data[a].user_id);
			        array_id.push(response_data[a].user_id);
	                // showInInfoWindow +="<div>"
	                // showInInfoWindow +=markers[i].title
	                // showInInfoWindow +="</div>"
	            }
	            var list_id = array_id.join();
	            // alert(list_id);
	            jQuery.ajax({
		            type: "POST",
		            url: scripts_vars.ajaxurl,
		            data: 'action=kt_user&list_id='+list_id,
		            // dataType: "json",
		            success: function(response) {
		        		infowindow.open(map);
		            	jQuery('.info-content > #bodyContent').html(response);
		            	//Store new window in global variable
		            }
		        });

		        for(i = 0; i < markers.length; i++) {		         
		            num++;
		            array.push(markers[i].getTitle() + '<br>');
		        }
	            var g_zoom = map.getZoom();
	            // var m_zoom = MarkerClusterer.getMaxZoom();
		        // alert(g_zoom);
		        // alert(markers.length);
	           /*infoWindow.setContent(markers.length + " markers<br>"+array);
	           infoWindow.setPosition(cluster.getCenter());
	           infoWindow.open(map);*/
	        }  

	    });
	    
	} else{
		jQuery('#gmap-noresult').html(gmap_norecod).show();
	}
}


/*********Upload Gallery**********/
"use strict";
jQuery(document).ready(function ($) {
	var file_upload_title	= scripts_vars.file_upload_title;
	var docdirect_upload_nounce	= scripts_vars.docdirect_upload_nounce;
	var delete_message	= scripts_vars.delete_message;
	var deactivate	= scripts_vars.deactivate;
	var delete_title	= scripts_vars.delete_title;
	var deactivate_title	= scripts_vars.deactivate_title;
	var dir_datasize	= scripts_vars.dir_datasize;
	var data_size_in_kb	= scripts_vars.data_size_in_kb;
	/* initialize uploader */

	var uploaderArguments = {
		runtimes : 'html5,flash,silverlight,html4',
		browse_button: 'kt_attach-gallery',          // this can be an id of a DOM element or the DOM element itself
		file_data_name: 'docdirect_uploader',
		container: 'plupload-container-gallery',
		flash_swf_url : scripts_vars.theme_path_uri+'/images/plupload/Moxie.swf',
        silverlight_xap_url : scripts_vars.theme_path_uri+'/images/plupload/Moxie.xap',
		drop_element: 'drag-and-drop',
		multipart_params : {
			"type" : "user_gallery",
		},
		url: scripts_vars.ajaxurl + "?action=docdirect_image_uploader&nonce=" + docdirect_upload_nounce,
		filters: {
			mime_types : [
				{ title : file_upload_title, extensions : "jpg,jpeg,gif,png" }
			],
			max_file_size: data_size_in_kb,
			prevent_duplicates: true
		}
	};
	
	var GalleryUploader = new plupload.Uploader( uploaderArguments );
	GalleryUploader.init();

	/* Run after adding file */
	GalleryUploader.bind('FilesAdded', function(up, files) {
		li_length = parseInt($('.kt_doc-user-gallery li').not('.more_slots').length);
		// alert(li_length);
		max = parseInt($('#plupload-container-gallery').data('max'));
		con_lai = max - li_length;
		if (files.length > con_lai) {
			jQuery.sticky('max total images: '+max, {classList: 'important', speed: 200, autoclose: 3000});
			return false;
		}
		var html = '';
		var galleryThumbnail = "";
		plupload.each(files, function(file) {
			 galleryThumbnail += '<li class="kt_gallery-item gallery-thumb-item" id="thumb-' + file.id + '">' + '' + '</li>';
		});
		
		jQuery('.kt_doc-user-gallery').append(galleryThumbnail);
		up.refresh();
		GalleryUploader.start();
	});
	
	/* Run during upload */
	GalleryUploader.bind('UploadProgress', function(up, file) {
		jQuery(".kt_doc-user-gallery #thumb-" + file.id).html('<span class="gallery-percentage">' + file.percent + "%</span>");
	});


	/* In case of error */
	GalleryUploader.bind('Error', function( up, err ) {
		//jQuery('#errors-log-gallery').html(err.message);
		jQuery.sticky(err.message, {classList: 'important', speed: 200, autoclose: 5000});
	});


	/* If files are uploaded successfully */
	GalleryUploader.bind('FileUploaded', function ( up, file, ajax_response ) {
		var response = $.parseJSON( ajax_response.response );	
		if ( response.success ) {
			var load_gallery = wp.template( 'load-gallery' );
			var _thumb	= load_gallery(response);
			jQuery("#thumb-" + file.id).html(_thumb);
			jQuery('.kt_doc-user-gallery .tg-img-hover a').unbind('click');
			//bindThumbnailEvents();  // bind click event with newly added gallery thumb

			li_length = parseInt($('#gallery-sortable-container li').length);
			max = parseInt($('#plupload-container-gallery').data('max'));
			if (li_length >= max) {
				$('#plupload-container-gallery').closest('li').hide();
				$('.more_slots').show();
			}
		} else {
			// log response object
			jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
		}
	});

	//Delete Gallery Image
	jQuery(document).on('click','.kt_doc-user-gallery .tg-img-hover a',function(e){
		e.preventDefault();

		var $this 	= jQuery(this);
		$this.parents('.gallery-item').remove();

		li_length = parseInt($('.kt_doc-user-gallery li').not('.more_slots').length);
		max = parseInt($('#plupload-container-gallery').data('max'));
		if (li_length < max) {
			$('#plupload-container-gallery').closest('li').show();
			$('.more_slots').hide();
		}

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


});


//rewrite dashboard map
function kt_docdirect_init_map(map_lat,map_lng) {

	var mapwrapper = jQuery('#kt_location-pickr-map');

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
}

jQuery(document).ready(function(e) {

  	var placeSearch, autocomplete;
	autocomplete = {};
    autocomplete['place-autocomplete'] = new google.maps.places.Autocomplete(document.getElementById('kt_location-address'), { 
    	componentRestrictions: {country: scripts_vars.country_restrict}
    });
    google.maps.event.addListener(autocomplete['place-autocomplete'], 'place_changed', function() {
      // fillInAddress(); luvitas
    });

});

//Input Type Phone
function docdirect_intl_tel_input23(){

	$("#teluserphone").on('keyup',function(e){
	    if (e.keyCode == 32) {
	       	e.preventDefault();
	    	return false;
	    }
	});

	jQuery("#teluserphone").intlTelInput({
	  // allowDropdown: false,
	  // autoHideDialCode: false,
	  autoPlaceholder: true,
	  // dropdownContainer: "body",
	  // excludeCountries: ["us"],
	  geoIpLookup: function(callback) {
	   jQuery.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
		 var countryCode = (resp && resp.country) ? resp.country : "";
		  callback(countryCode);
	   });
	   },
	  initialCountry: ['hk'],
	  // nationalMode: false,
	  // numberType: "MOBILE",
	  // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
	  // preferredCountries: ['cn', 'jp'],
	  separateDialCode: true,
	  //utilsScript: "build/js/utils.js"
      // utilsScript: scripts_vars.theme_path_uri+'/js/booking/js/utils.js',
	});
}

jQuery(document).ready(function(e) {

	//Geo Locate
	jQuery(document).on("click",".geolocate2",function(){
		jQuery('#kt_location-pickr-map').gmap3({
		  getgeoloc:{
			callback : function(latLng){
			  if (latLng){
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({"latLng":latLng},function(data,status){
					 if (status == google.maps.GeocoderStatus.OK) {
						if (data[0]) {
							jQuery('#kt_location-pickr-map').gmap3({
							  marker:{ 
								latLng:latLng
							  },
							  map:{
								options:{
								  zoom: 11
								}
							  }
							});
							jQuery("#kt_location-address").val(data[0].formatted_address);
							jQuery("#kt_location-latitude").val(latLng.lat());
							jQuery("#kt_location-longitude").val(latLng.lng());
						}
					}
				});
			  }
			}
		  }
		});
		return false;
	});

    $('#contactForm')
        .find('[name="phone_number"]')
            .intlTelInput({
                utilsScript: scripts_vars.theme_path_uri+'/js/booking/js/utils.js',
                autoPlaceholder: true,
                preferredCountries: ['fr', 'us', 'gb']
            });

    $('.tg-form-signup')
        .formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                phone_number: {
                    validators: {
                        callback: {
                            message: 'The phone number is not valid',
                            callback: function(value, validator, $field) {
                                return value === '' || $field.intlTelInput('isValidNumber');
                            }
                        }
                    }
                }
            }
        })
        .on('success.field.fv', function(e, data) {
            if (data.fv.getInvalidFields().length > 0) {    // There is invalid field
                data.fv.disableSubmitButtons(true);
            }
        })
        // Revalidate the number when changing the country
        .on('click', '.country-list', function() {
            $('.tg-form-signup').formValidation('revalidateField', 'phone_number');
        });

    $('#invite_review')
        .formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                phone_number: {
                    validators: {
                        callback: {
                            message: 'The phone number is not valid',
                            callback: function(value, validator, $field) {
                                return value === '' || $field.intlTelInput('isValidNumber');
                            }
                        }
                    }
                },
                hkid: {
                    validators: {
                    	callback: {
                            message: 'HKID is not valid',
                            callback: function(value, validator, $field) {
                                if (!validid.hkid(value) && value != '') {
                                	return false;
                                }else {
                                	return true;
                                }
                            }
                        }
                    }
                },
                email_patient: {
                    validators: {
                        regexp: {
                            regexp: '^[^@\\s]+@([^@\\s]+\\.)+[^@\\s]+$',
                            message: 'The value is not a valid email address'
                        }
                    }
                }
            }
        })
        .on('success.field.fv', function(e, data) {
            if (data.fv.getInvalidFields().length > 0) {    // There is invalid field
                data.fv.disableSubmitButtons(false);
            }
        });


	//Geo Locate
	jQuery(document).on("click",".geolocate",function(){
		jQuery('#kt_location-pickr-map').addClass('aloooo').gmap3({
		  getgeoloc:{
			callback : function(latLng){
			  if (latLng){
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({"latLng":latLng},function(data,status){
					 if (status == google.maps.GeocoderStatus.OK) {
						if (data[0]) {
							jQuery('#kt_location-pickr-map').gmap3({
							  marker:{ 
								latLng:latLng
							  },
							  map:{
								options:{
								  zoom: 11
								}
							  }
							});
							jQuery("#kt_location-address").val(data[0].formatted_address);
							jQuery("#kt_location-latitude").val(latLng.lat());
							jQuery("#kt_location-longitude").val(latLng.lng());
						}
					}
				});
			  }
			}
		  }
		});
		return false;
	});


});