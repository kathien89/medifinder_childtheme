<?php
/**
 * User Invoices
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
} else{
	$currency_select = 'USD';
}

$services_cats = get_user_meta($user_identity , 'services_cats' , true);
$booking_services = get_user_meta($user_identity , 'booking_services' , true);
$custom_slots = get_user_meta($user_identity , 'custom_slots' , true);
$currency_symbol	       = get_user_meta( $user_identity, 'currency_symbol', true);

$currency_symbol	= !empty( $currency_symbol ) ? ' ('.$currency_symbol.')' : '';
if( !empty( $custom_slots ) ){
	$custom_slot_list	= json_decode( $custom_slots,true );
} else{
	$custom_slot_list = array();
}

$custom_slot_list	= docdirect_prepare_seprate_array($custom_slot_list);
/*echo '<pre>';

    $default_slots = get_user_meta($user_identity , 'default_slots' , true);
    $default_slots["mon"] == 'sdagsdgsadg';
    var_dump($default_slots);
echo '</pre>';*/
?>
<div class="doc-booking-settings dr-bookings">
    <div class="tg-haslayout">
        <div class="booking-settings-data">
            <div class="tg-dashboard tg-docappointment tg-haslayout">
                <div class="tg-listing">
                  <div class="tg-heading-border tg-small">
                    <h4><i class="fa fa-plus-circle"></i><?php pll_e('Add Services','docdirect');?></h4>
                  </div>
                  <div class="row">
                    <div class="col-md-12 col-sm-12">
                      <div class="tg-subdoccategoties">
                        <button class="btn btn-primary bk-add-service-item"><i class="fa fa-plus-circle"></i><?php pll_e('Add Service','docdirect');?></button>
                        
                        <div class="bk-services-wrapper">
                        	<?php 
          								if( !empty( $booking_services ) ) {
          									foreach( $booking_services as $key => $value ){
          										?>
                                        <div class="bk-service-item">
                                            <div class="tg-subdoccategory"> 
                                                <span class="tg-catename"><?php echo esc_attr( $value['title'] );?></span> 
                                                <span class="tg-catelinks"> 
                                                    <a href="javascript:;" class="bk-edit-service"><i class="fa fa-edit"></i></a>
                                                    <a href="javascript:;"  data-type="db-delete" data-key="<?php echo esc_attr( $key );?>" class="bk-delete-service"><i class="fa fa-trash-o"></i></a>
                                                </span> 
                                                <span class="tg-serviceprice"><?php echo esc_attr( $value['price'] );?></span> 
                                            </div>
                                            <div class="tg-editcategory bk-current-service bk-elm-hide">
                                              <div class="form-group">
                                                <input type="text"  value="<?php echo esc_attr( $value['title'] );?>" class="form-control service-title" name="service_title" placeholder="<?php pll_e('Service Title','docdirect');?>">
                                              </div>
                                              <div class="form-group">
                                                <input type="text" value="<?php echo esc_attr( $value['price'] );?>" class="form-control service-price" name="service_price" placeholder="<?php esc_attr_e('Add Price','docdirect');?> <?php echo esc_attr( $currency_symbol );?>">
                                              </div>
                                              <div class="form-group tg-btnarea">
                                              <button class="tg-update  bk-service-add" data-key="<?php echo esc_attr( $key );?>" data-type="update" type="submit"><?php pll_e('Update Now','docdirect');?></button>
                                            </div></div>
                                        </div>
                                        <?php
          									}
          								}
          							?>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tg-heading-border tg-small">
                  <h4><i class="fa fa-calendar"></i><?php pll_e('Smart Calendar','docdirect');?></h4>
                </div>
                <span class="tip blue"><span></span><?php pll_e('Custom Time','docdirect');?></span>
                <span class="tip green"><span></span><?php pll_e('Repeated Weekly','docdirect');?></span>
            </div>
        </div>
      <?php 
        $my_arr = kt_get_user_calendar();
        /*echo '<pre>';
        var_dump($my_arr);
        echo '</pre>';*/
      ?>
        <div id="calendar"></div>
        <script type="text/javascript">
          jQuery(document).ready(function($) {

            $('#calendar').fullCalendar({
              allDaySlot: false,
              selectable: true,
              header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
              },
              // contentHeight: 350,
              viewRender: function( view, element ) {
                hh = $('.fc-month-view .fc-scroller.fc-day-grid-container').height();
                console.log(hh);
                if (view.name == 'agendaDay') {
                  var current_day = moment().format('YYYY-MM-DD');
                  var text_day = $('.fc-toolbar.fc-header-toolbar').find('.fc-center h2').text();
                  var this_day = moment(text_day).format('YYYY-MM-DD');
                  if (this_day == current_day) {
                    $('.fc-time-grid').find('.fc-slats tr').each(function() {
                      var current_time = moment().format('HHmm');
                      data_time = $(this).data('time');
                      var this_time = moment(current_day+' '+data_time).format('HHmm');
                      if (this_time < current_time) {
                        $(this).find('.fc-widget-content').not('.fc-time').css("background-color", "#ebebeb");
                      }
                    });
                    $('#calendar').removeClass('scrollview');
                  }
                }else {
                    $('#calendar').addClass('scrollview');
                }

              },
              dayRender: function (date, cell) {
                
                var current_date = moment().format('YYYY-MM-DD');
                var date = moment(date).format('YYYY-MM-DD');

                if(date < current_date ) {
                    cell.css("background-color", "#ebebeb");
                }else {
                  cell.append("<span class='add_time'>+ Add Time</span>");
                }
                cell.find(".add_time").click(function(e) {
                  // alert('alooo');
                });

              },
              dayClick: function(date, jsEvent, view) {
                // alert('clicked ' + date.format());
                // if (view.name == 'month') {
                //   $('#calendar').fullCalendar('changeView',  'agendaDay', date);
                // }
                var current_date = moment().format('YYYY-MM-DD HHmm');
                var this_end_date = moment(date).add(24, 'hours').format('YYYY-MM-DD HHmm');
                var this_start_date = moment(date).format('YYYY-MM-DD HHmm');
                if( (this_end_date < current_date && view.name == 'month') || (this_start_date < current_date && view.name != 'month') ) {
                  return false;
                }
                  var ds = $.fullCalendar.formatDate(date,"YYYY-MM-DD");
                  // var time_start = $.fullCalendar.formatDate(date,"HHmm");
                  // var time_end = $.fullCalendar.formatDate(date,"HHmm");
                  var time_start = moment(date).format('HHmm');
                  var time_end = moment(date).format('HHmm');
                  if (time_start==time_end) {
                    var time_end = moment(date).add(30, 'minutes').format('HHmm');
                  }
                  var text_date = moment(date).format('dddd')+'|'+moment(date).format('MMMM DD')+'|'+moment(date).format('YYYY');

                  $('#modal_addevent').find('span.text_date').text(text_date);
                  $('#modal_addevent').find('input[name=start_date]').val(ds);
                  // $('#modal_addevent').find('input[name=start_time]').val(time_start);
                  // $('#modal_addevent').find('.start_time > span').text(time_start);
                  // $('#modal_addevent').find('input[name=end_time]').val(time_end);
                  // $('#modal_addevent').find('.end_time > span').text(time_end);               
                  $('#modal_addevent').find('.start_time select').val(time_start);
                  $('#modal_addevent').find('.end_time select').val(time_end);
                  $('#modal_addevent').modal('show');
                  $(".end_time select option").each(function () {
                        this.disabled = false;
                    });
                  $(".start_time select option").each(function () {
                        this.disabled = false;
                    });
                
              },
              select: function(startDate, endDate) {
                var current_date = moment().format('YYYY-MM-DD HHmm');
                var this_day = moment(startDate).format('YYYY-MM-DD HHmm');
                if(this_day < current_date ) {
                  return false;
                }else {
                  var ds = $.fullCalendar.formatDate(startDate,"YYYY-MM-DD");
                  var de = $.fullCalendar.formatDate(endDate,"YYYY-MM-DD");
                  // var time_start = $.fullCalendar.formatDate(startDate,"HHmm");
                  // var time_end = $.fullCalendar.formatDate(endDate,"HHmm");
                  var time_start = moment(startDate).format('HHmm');
                  var time_end = moment(endDate).format('HHmm');
                  if (time_start==time_end) {
                    var time_end = moment(startDate).add(30, 'minutes');
                  }
                  var text_date = moment(startDate).format('dddd')+'|'+moment(startDate).format('MMMM DD')+'|'+moment(startDate).format('YYYY');
                  if (moment(ds)<moment(de)) {
                  }else {
                    $('#modal_addevent').find('span.text_date').text(text_date);
                    $('#modal_addevent').find('input[name=start_date]').val(ds);
                    // $('#modal_addevent').find('input[name=start_time]').val(time_start);
                    // $('#modal_addevent').find('.start_time > span').text(time_start);
                    // $('#modal_addevent').find('input[name=end_time]').val(time_end);
                    // $('#modal_addevent').find('.end_time > span').text(time_end);                  
                    $('#modal_addevent').find('.start_time select').val(time_start);
                    $('#modal_addevent').find('.end_time select').val(time_end);
                    $('#modal_addevent').modal('show');
                    $(".end_time select option").each(function () {
                        this.disabled = false;
                          if ( this.value <= time_start ) {
                              this.disabled = true;
                          }
                      });
                    $(".start_time select option").each(function () {
                        this.disabled = false;
                          if ( this.value >= time_end ) {
                              this.disabled = true;
                          }
                      });
                  }
                }
              },
              // defaultView: 'basicWeek',
              eventConstraint:{
                start: '00:00', 
                end: '24:00', 
              },
              eventOverlap: function(stillEvent, movingEvent) {
                return stillEvent.allDay && movingEvent.allDay;
              },
              editable: true,
              // selectOverlap: false,
              events: <?php echo json_encode($my_arr);?>,
              //drag and drop event
              eventDragStart: function(event) {              
                if(jQuery.inArray("repeat", event.className) == -1) {
                  return false;
                }
                var date = moment(event.start).format('YYYY-MM-DD HH:mm');
                id_change = moment(event.start).unix();
              },
              eventDrop: function(event, delta, revertFunc) {

                if(jQuery.inArray("repeat", event.className) == -1) {
                  var date = moment(event.start).format('YYYY-MM-DD HH:mm');
                  id_new = moment(event.start).unix();

                  var ds = moment(event.start).format('DD-MM-YYYY');
                  var de = moment(event.end).format('DD-MM-YYYY');
                  var time_start = moment(event.start).format('HHmm');
                  var time_end = moment(event.end).format('HHmm');
                  var slot_time = '{"'+time_start+'-'+time_end+'":0}';
                  var slot_time_details = '{"'+time_start+'-'+time_end+'":{"slot_title":null}}';
                  event_title = moment(event.start).format('HH:mm')+'-'+moment(event.end).format('HH:mm');
                  event.title = event_title;

                  $('.kt_custom-slots-main').find('#'+event.id).find('input[name=cus_start_date]').val(ds);
                  $('.kt_custom-slots-main').find('#'+event.id).find('input[name=cus_end_date]').val(ds);
                  $('.kt_custom-slots-main').find('#'+event.id).find('input[name=custom_time_slots]').val(slot_time);
                  $('.kt_custom-slots-main').find('#'+event.id).find('input[name=custom_time_slot_details]').val(slot_time_details);  
                  $('.kt_custom-slots-main').find('#'+event.id).attr('id', id_new);

                  jQuery('body').append(loder_html);
                  var data = JSON.stringify(jQuery('.kt_custom-slots-main').serializeObject());
                  var custom_timeslots_object = data;
                  var dataString = 'custom_timeslots_object='+custom_timeslots_object+'&action=docdirect_save_custom_slots';          
                  jQuery.ajax({
                    type: "POST",
                    url: scripts_vars.ajaxurl,
                    data: dataString,
                    dataType:"json",
                    success: function(response) {
                          $('#modal_addevent').modal('hide');
                      jQuery('body').find('.docdirect-site-wrap').remove();
                      if( response.message_type == 'error' ) {
                        jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                      } else{
                        // event.title = event_title;
                        $('#calendar').fullCalendar('updateEvent', event);
                        // _this.parents('.tg-daytimeslot').find('.timeslots-data-area').html(response.slots_data);
                        jQuery.sticky(confirm_vars.update_slot, {classList: 'success', speed: 200, autoclose: 5000});
                  
                      }
                      // kt_refesh_calendar(response.events);
                    }
                  });
                }
              },
              eventResizeStart: function( event, jsEvent, ui, view ) {
                var date = moment(event.start).format('YYYY-MM-DD HH:mm');
                id_change = moment(event.start).unix();
              },
              eventResizeStop: function( event, jsEvent, ui, view ) { 
              },
              eventResize: function(event, delta, minuteDelta, revertFunc) {
                if(jQuery.inArray("repeat", event.className) == -1) {
                  var date = moment(event.start).format('YYYY-MM-DD HH:mm');
                  id_new = moment(event.start).unix(); 
                  var ds = moment(event.start).format('DD-MM-YYYY');
                  var de = moment(event.end).format('DD-MM-YYYY');
                  var time_start = moment(event.start).format('HHmm');
                  var time_end = moment(event.end).format('HHmm');
                  var slot_time = '{"'+time_start+'-'+time_end+'":0}';
                  var slot_time_details = '{"'+time_start+'-'+time_end+'":{"slot_title":null}}';
                  event.title = moment(event.start).format('HH:mm')+' - '+moment(event.end).format('HH:mm');

                  $('.kt_custom-slots-main').find('#'+id_change).find('input[name=cus_start_date]').val(ds);
                  $('.kt_custom-slots-main').find('#'+id_change).find('input[name=cus_end_date]').val(ds);
                  $('.kt_custom-slots-main').find('#'+id_change).find('input[name=custom_time_slots]').val(slot_time);
                  $('.kt_custom-slots-main').find('#'+id_change).find('input[name=custom_time_slot_details]').val(slot_time_details);  
                  $('.kt_custom-slots-main').find('#'+id_change).attr('id', id_new);

                  jQuery('body').append(loder_html);
                  var data = JSON.stringify(jQuery('.kt_custom-slots-main').serializeObject());
                  var custom_timeslots_object = data;
                  var dataString = 'custom_timeslots_object='+custom_timeslots_object+'&action=docdirect_save_custom_slots';          
                  jQuery.ajax({
                    type: "POST",
                    url: scripts_vars.ajaxurl,
                    data: dataString,
                    dataType:"json",
                    success: function(response) {
                      $('#modal_addevent').modal('hide');
                      jQuery('body').find('.docdirect-site-wrap').remove();
                      if( response.message_type == 'error' ) {
                        jQuery.sticky(response.message, {classList: 'important', speed: 200, autoclose: 5000});
                      } else{
                        $('#calendar').fullCalendar('updateEvent', event);
                        // _this.parents('.tg-daytimeslot').find('.timeslots-data-area').html(response.slots_data);
                        jQuery.sticky(confirm_vars.update_slot, {classList: 'success', speed: 200, autoclose: 5000});
                  
                      }
                      // kt_refesh_calendar(response.events);
                    }
                  });
                }
              },
              eventRender: function(event, element) {
                //repeat end date
                var theDate = event.start;
                var current_year = moment(theDate).format('YYYY');
                var current_month = moment(theDate).format('MMM');
                var dowend = event.dowend;

                if (dowend != null) {
                  var month = dowend.month;
                  var year = dowend.year;
                  if (year != null && current_year == year) {
                    if (month != null && jQuery.inArray(current_month, month) == -1 ) {
                      return false;
                    }
                    /*var endDate = moment(dowend).add('months', 1);
                    if (moment(theDate) >= moment(endDate)) {
                        return false;
                    }*/
                  }else if(year != null && current_year != year) {
                    return false;
                  }
                  // alert(moment(theDate).format('M'));
                }

                //add delete button
                var el = element.html();
                element.html("<div class='wrap_event'>" +  el + "</div><span class='closeon'><i class='fa fa-close'></i></span>");
                element.find(".wrap_event").click(function(e) {

                  var date = moment(event.start).format('YYYY-MM-DD');
                  repeat = 'custom';

                  var ds = moment(event.start).format('YYYY-MM-DD');
                  var de = moment(event.end).format('YYYY-MM-DD');
                  var time_start = moment(event.start).format('HHmm');
                  var time_end = moment(event.end).format('HHmm');
                  var time = time_start+'-'+time_end;
                  var text_date = moment(event.start).format('dddd')+'|'+moment(event.start).format('MMMM DD')+'|'+moment(event.start).format('YYYY');
                  var id_event = event.id;

                  $('#modal_editevent').find('.dropdown').hide();
                  $('#modal_editevent').find(':checkbox[name^="repeat_month"]').each(function () {
                    $(this).prop("checked", false);
                  });
                  if(jQuery.inArray("repeat", event.className) !== -1) {

                    repeat = 'repeat';
                    $('#modal_editevent').find('.dropdown').show();
                    var dowend = event.dowend;
                    if (dowend != null) {
                      var month = dowend.month;
                      if (month != null){
                        $('#modal_editevent').find(':checkbox[name^="repeat_month"]').each(function () {
                          $(this).prop("checked", ($.inArray($(this).val(), month) != -1));
                        });
                      }
                    }

                  }
                  $('#modal_editevent').find('span.text_date').text(text_date);
                  $('#modal_editevent').find('input[name=start_date]').val(ds);
                  $('#modal_editevent').find('input[name=old_slot]').val(time);
                  $('#modal_editevent').find('input[name=id_event]').val(id_event);
                  $('#modal_editevent').find('input[name=repeat]').val(repeat);

                  $('#modal_editevent').find('.start_time select').val(time_start);
                  $('#modal_editevent').find('.end_time select').val(time_end);
                    $(".end_time select option").each(function () {
                              this.disabled = false;
                      });
                    var value_s = $('.end_time select').val();
                    $(".start_time select option").each(function () {
                              this.disabled = false;
                      });
                  var value_s = $('.start_time select').val();
                  $(".end_time select option").each(function () {
                        if ( this.value <= value_s ) {
                            this.disabled = true;
                        }
                    });
                  var value_s = $('.end_time select').val();
                  $(".start_time select option").each(function () {
                        if ( this.value >= value_s ) {
                            this.disabled = true;
                        }
                    });
                  $('#modal_editevent').modal('show');
                });
     
                // element.append( "<span class='closeon'><i class='fa fa-close'></i></span>" );
                element.find(".closeon").click(function(e) {
                  e.preventDefault();
                  $('.tg-add-slot_time').modal('hide');
                  // $('#calendar').fullCalendar('removeEvents',event.id);
                  var ds = moment(event.start).format('DD-MM-YYYY');
                  var de = moment(event.end).format('DD-MM-YYYY');
                  var time_start = moment(event.start).format('HHmm');
                  var time_end = moment(event.end).format('HHmm');
                  var time = time_start+'-'+time_end;
                  repeat = '';
                  if(jQuery.inArray("repeat", event.className) != -1) {
                    repeat = 'repeat';  
                  }

                  var title = scripts_vars.delete_slot;
                  var message = scripts_vars.delete_slot_message;
                  var dontshow_msag = confirm_vars.dontshow_msag;
                  if ($('.form_edit_slot_time .delete_slot').hasClass('non-confirm')) {
                            $('.tg-add-slot_time').modal('hide');
              
                            jQuery('body').append(loder_html);
                            if (repeat == 'repeat') {
                              var dataString = 'id_event='+event.id+'&date='+ds+'&time='+time+'&repeat=repeat&action=remove_time_slot';
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
                                    $('#calendar').fullCalendar('removeEvents', event.id);
                                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
                                  }
                                }
                              });
                            }else {
                              console.log(event.id);
                              $('#calendar').fullCalendar('removeEvents', event.id);
                              $('.kt_custom-slots-main').find('#'+event.id).remove();

                              var data = JSON.stringify(jQuery('.kt_custom-slots-main').serializeObject());
                              // console.log(data);

                              var custom_timeslots_object = data;
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
                    jQuery.confirm({
                      'title': title,
                      'message': message+'<div class="checkbox"><div class="doc-checkbox1"><label><input name="dsma" type="checkbox" class="donotshowagain">'+dontshow_msag+'</label></div></div>',       
                      'buttons': {
                        'Yes': {
                          'class': 'blue',
                          'action': function () {

                            $('.tg-add-slot_time').modal('hide');
              
                            jQuery('body').append(loder_html);
                            if (repeat == 'repeat') {
                              var dataString = 'id_event='+event.id+'&date='+ds+'&time='+time+'&repeat=repeat&action=remove_time_slot';
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
                                    $('#calendar').fullCalendar('removeEvents', event.id);
                                    jQuery.sticky(response.message, {classList: 'success', speed: 200, autoclose: 5000});
                                  }
                                }
                              });
                            }else {
                              console.log(event.id);
                              $('#calendar').fullCalendar('removeEvents', event.id);
                              $('.kt_custom-slots-main').find('#'+event.id).remove();

                              var data = JSON.stringify(jQuery('.kt_custom-slots-main').serializeObject());
                              // console.log(data);

                              var custom_timeslots_object = data;
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

                          }
                        },
                        'No': {
                          'class': 'gray',
                          'action': function () {
                            $('.tg-add-slot_time').modal('hide');
                            return false;
                          } // Nothing to do in this case. You can as well omit the action property.
                        }
                      }
                    });
                  }
                });
              },
              eventClick: function(event, jsEvent, view) {
                /*
                var date = moment(event.start).format('YYYY-MM-DD');
                repeat = 'custom';

                var ds = moment(event.start).format('YYYY-MM-DD');
                var de = moment(event.end).format('YYYY-MM-DD');
                var time_start = moment(event.start).format('HHmm');
                var time_end = moment(event.end).format('HHmm');
                var time = time_start+'-'+time_end;
                var text_date = moment(event.start).format('dddd')+'|'+moment(event.start).format('MMMM DD')+'|'+moment(event.start).format('YYYY');
                var id_event = event.id;

                if(jQuery.inArray("repeat", event.className) !== -1) {

                  repeat = 'repeat';

                }
                  $('#modal_editevent').find('span.text_date').text(text_date);
                  $('#modal_editevent').find('input[name=start_date]').val(ds);
                  $('#modal_editevent').find('input[name=old_slot]').val(time);
                  $('#modal_editevent').find('input[name=id_event]').val(id_event);
                  $('#modal_editevent').find('input[name=repeat]').val(repeat);

                  $('#modal_editevent').find('.start_time select').val(time_start);
                  $('#modal_editevent').find('.end_time select').val(time_end);
                  $('#modal_editevent').modal('show');*/

              }
            });

          });
        </script>
    </div>
</div>
<!----------------------------------------------
 * Main categories Templates
 * return HTML
------------------------------------------------>
<script type="text/template" id="tmpl-append-service-category">
	<div class="bk-category-item">
		<div class="tg-doccategory">
			<span class="tg-catename"><?php pll_e('Category Title','docdirect');?></span>
			<span class="tg-catelinks">
				<a href="javascript:;" class="bk-edit-category"><i class="fa fa-edit"></i></a>
				<a href="javascript:;"  data-type="new-delete" data-key="" class="bk-delete-category"><i class="fa fa-trash-o"></i></a>
			</span>
		</div>
		<div class="tg-editcategory bk-current-category bk-elm-hide">
			<div class="form-group">
				<input type="text" class="form-control service-category-title" name="categoryname" placeholder="<?php pll_e('Category Title','docdirect');?>">
			</div>
			<div class="form-group tg-btnarea">
			<button class="tg-update bk-maincategory-add" data-key="new" data-type="add" type="submit"><?php pll_e('Update Now','docdirect');?></button>
			</div>
		</div>
	</div>
</script>
<script type="text/template" id="tmpl-update-service-category">
	<div class="tg-doccategory">
		<span class="tg-catename">{{data.title}}</span>
		<span class="tg-catelinks">
			<a href="javascript:;" class="bk-edit-category"><i class="fa fa-edit"></i></a>
			<a href="javascript:;" data-type="db-delete" data-key="{{data.key}}" class="bk-delete-category"><i class="fa fa-trash-o"></i></a>
		</span>
	</div>
	<div class="tg-editcategory bk-current-category bk-elm-hide">
		<div class="form-group">
			<input type="text" data-key="{{data.key}}" value="{{data.title}}" class="form-control service-category-title" name="categoryname" placeholder="<?php pll_e('Category Title','docdirect');?>">
		</div>
		<button class="tg-update bk-maincategory-add" data-key="{{data.key}}" data-type="update" type="submit"><?php pll_e('Update Now','docdirect');?></button>
		
	</div>
</script>

<!----------------------------------------------
 * Services Templates
 * return HTML
------------------------------------------------>

<script type="text/template" id="tmpl-append-service">
	<div class="bk-service-item">
		<div class="tg-subdoccategory"> 
			<span class="tg-catename"><?php pll_e('Service Title','docdirect');?></span> 
			<span class="tg-catelinks"> 
				<a href="javascript:;" class="bk-edit-service"><i class="fa fa-edit"></i></a>
				<a href="javascript:;"  data-type="new-delete" data-key="" class="bk-delete-service"><i class="fa fa-trash-o"></i></a>
			</span> 
			<span class="tg-serviceprice">0.00</span> 
		</div>
		<div class="tg-editcategory bk-current-service bk-elm-hide">
		  <div class="form-group">
			<input type="text" class="form-control service-title" name="service_title" placeholder="<?php pll_e('Service Title','docdirect');?>">
		  </div>
		  <div class="form-group">
			<input type="text" class="form-control service-price" name="service_price" placeholder="<?php pll_e('Add Price','docdirect');?><?php echo esc_attr( $currency_symbol );?>">
		  </div>
		  <div class="form-group tg-btnarea">
		  <button class="tg-update  bk-service-add" data-key="new" data-type="add" type="submit"><?php pll_e('Update Now','docdirect');?></button>
		</div></div>
	</div>
</script>

<script type="text/template" id="tmpl-update-service">
	<div class="tg-subdoccategory"> 
		<span class="tg-catename">{{data.service_title}}</span> 
		<span class="tg-catelinks"> 
			<a href="javascript:;" class="bk-edit-service"><i class="fa fa-edit"></i></a>
			<a href="javascript:;"  data-type="db-delete" data-key="{{data.key}}" class="bk-delete-service"><i class="fa fa-trash-o"></i></a>
		</span> 
		<span class="tg-serviceprice">{{data.service_price}}</span> 
	</div>
	<div class="tg-editcategory bk-current-service bk-elm-hide">
	  <div class="form-group">
		<input type="text" value="{{data.service_title}}" class="form-control service-title" name="service_title" placeholder="<?php pll_e('Service Title','docdirect');?>">
	  </div>
	  <div class="form-group">
		<input type="text" value="{{data.service_price}}" class="form-control service-price" name="service_price" placeholder="<?php pll_e('Add Price','docdirect');?> <?php echo esc_attr( $currency_symbol );?>">
	  </div>
	  <div class="form-group tg-btnarea">
	  <button class="tg-update  bk-service-add" data-key="{{data.key}}" data-type="update" type="submit"><?php pll_e('Update Now','docdirect');?></button>
	</div></div>
</script>


<script type="text/template" id="tmpl-append-options">
	<option value=""><?php pll_e('Select category','docdirect');?></option>
	<# _.each( data , function( element, index, attr ) { #>
		<option value="{{index}}">{{element}}</option>
	<# } ); #>
</script>

<!----------------------------------------------
 * Default Time Slots
 * return HTML
------------------------------------------------>
<script type="text/template" id="tmpl-default-slots">
	<div class="tg-timeslotswrapper">
		<div class="form-group">
		  <input type="text" name="slot_title" class="form-control" name="title" placeholder="<?php esc_attr_e('Title (Optional)','docdirect');?>">
		</div>
		<div class="form-group">
		  <div class="tg-select">
			<select name="start_time" class="start_time">
			  <option><?php esc_attr_e('Start Time','docdirect');?></option>
			  <option value="0000">12:00 am</option>
			  <option value="0100">1:00 am</option>
			  <option value="0200">2:00 am</option>
			  <option value="0300">3:00 am</option>
			  <option value="0400">4:00 am</option>
			  <option value="0500">5:00 am</option>
			  <option value="0600">6:00 am</option>
			  <option value="0700">7:00 am</option>
			  <option value="0800">8:00 am</option>
			  <option value="0900">9:00 am</option>
			  <option value="1000">10:00 am</option>
			  <option value="1100">11:00 am</option>
			  <option value="1200">12:00 pm</option>
			  <option value="1300">1:00 pm</option>
			  <option value="1400">2:00 pm</option>
			  <option value="1500">3:00 pm</option>
			  <option value="1600">4:00 pm</option>
			  <option value="1700">5:00 pm</option>
			  <option value="1800">6:00 pm</option>
			  <option value="1900">7:00 pm</option>
			  <option value="2000">8:00 pm</option>
			  <option value="2100">9:00 pm</option>
			  <option value="2200">10:00 pm</option>
			  <option value="2300">11:00 pm</option>
			  <option value="2400">12:00 am (night)</option>
			</select>
		  </div>
		</div>
		<div class="form-group">
		  <div class="tg-select">
			<select name="end_time" class="end_time">
			  <option><?php esc_attr_e('End Time','docdirect');?></option>
			  <option value="0000">12:00 am</option>
			  <option value="0100">1:00 am</option>
			  <option value="0200">2:00 am</option>
			  <option value="0300">3:00 am</option>
			  <option value="0400">4:00 am</option>
			  <option value="0500">5:00 am</option>
			  <option value="0600">6:00 am</option>
			  <option value="0700">7:00 am</option>
			  <option value="0800">8:00 am</option>
			  <option value="0900">9:00 am</option>
			  <option value="1000">10:00 am</option>
			  <option value="1100">11:00 am</option>
			  <option value="1200">12:00 pm</option>
			  <option value="1300">1:00 pm</option>
			  <option value="1400">2:00 pm</option>
			  <option value="1500">3:00 pm</option>
			  <option value="1600">4:00 pm</option>
			  <option value="1700">5:00 pm</option>
			  <option value="1800">6:00 pm</option>
			  <option value="1900">7:00 pm</option>
			  <option value="2000">8:00 pm</option>
			  <option value="2100">9:00 pm</option>
			  <option value="2200">10:00 pm</option>
			  <option value="2300">11:00 pm</option>
			  <option value="2400">12:00 am (night)</option>
			</select>
		  </div>
		</div>
		<div class="form-group">
		  <div class="tg-select">
			<select name="meeting_time" class="meeting_time">
				<option><?php esc_attr_e('Meeting Time','docdirect');?></option>
				<option value="60">1 <?php esc_attr_e('hours','docdirect');?></option>
				<option value="90">1 <?php esc_attr_e('hour','docdirect');?>, 30 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="120">2 <?php esc_attr_e('hours','docdirect');?></option>
				<option value="45">45 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="30">30 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="20">20 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="15">15 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="10">10 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="5">5 <?php esc_attr_e('minutes','docdirect');?></option>
			</select>
		  </div>
		</div>
		<div class="form-group">
		  <div class="tg-select">
			<select name="padding_time" class="padding_time">
				<option><?php esc_attr_e('Padding/Break Time','docdirect');?></option>
				<option value="90">1 <?php esc_attr_e('hour','docdirect');?>, 30 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="5">5 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="10">10 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="15">15 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="20">20 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="30">30 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="45">45 <?php esc_attr_e('minutes','docdirect');?></option>
				<option value="60">1 <?php esc_attr_e('hour','docdirect');?></option>
			</select>
		  </div>
		</div>
		<div class="tg-btnbox">
		  <button type="submit" class="tg-btn save-time-slots"><?php pll_e('save','docdirect');?></button>
		  <button type="submit" class="tg-btn remove-slots-form"><?php pll_e('Cancel','docdirect');?></button>
		</div>
	  </div>
	</div>
</script>

<script type="text/template" id="tmpl-no-slots">
	<span class="tg-notimeslotmessage">
		<p><?php pll_e('NO TIME SLOTS','docdirect');?></p>
	</span>
</script>

<!----------------------------------------------
 * Custom Time Slots
 * return HTML
------------------------------------------------>
<script type="text/template" id="tmpl-custom-timelines">

  <a class="collapse_btn" href="javascript:;" data-toggle="collapse" data-target="#collapse-{{data.key}}"><i class="fa fa-caret-down"></i><?php pll_e('Start Date','docdirect');?></a>
  <div id="collapse-{{data.key}}" class="tg-daytimeslot collapse in">
	  <div class="custom-time-periods">
		<div class="tg-dayname">      
      <ul>
        <li><?php pll_e('Start Date','docdirect');?></li>
        <li><?php pll_e('End Date','docdirect');?></li>
      </ul>
      <a href="javascript:;" class="kt-btn kt_delete-slot-date">
        <i class="fa fa-close"></i><?php pll_e('Clear Date','docdirect');?>
      </a>
		</div>
        <div class="tg-timeslots tg-fieldgroup">
		  <div class="tg-timeslotswrapper">
			<div class="form-group tg-calender">
			  <input type="hidden" class="custom_time_slots" name="custom_time_slots" value="" />
              <input type="hidden" class="custom_time_slot_details" name="custom_time_slot_details" value="" />
			  <input type="text" class="form-control slots-datepickr" readonly name="cus_start_date" placeholder="<?php esc_attr_e('Start Date','docdirect');?>" />
			</div>
			<div class="form-group tg-calender">
			  <input type="text" class="form-control slots-datepickr" readonly name="cus_end_date" placeholder="<?php esc_attr_e('End Date','docdirect');?>" />
			</div>
			<div class="form-group">
			  <div class="tg-select">
				<select name="disable_appointment" class="disable_appointment enable">
					<option value="enable"><?php esc_attr_e('Enable Appointment','docdirect');?></option>
					<option value="disable"><?php esc_attr_e('Disbale Appointment','docdirect');?></option>
				</select>
			  </div>
			</div>
		  </div>
		  <div class="custom-timeslots-data-area"></div>
		  <div class="custom-timeslots-data"></div>
                        <a href="javascript:;" class="btn btn-primary add-custom-timeslots" data-key="sat"><i class="fa fa-clock-o"></i><?php pll_e('Add Time Slots','docdirect');?></a>
		</div>
	</div>
</div>
</script>

<script type="text/template" id="tmpl-custom-slots">
	<div class="tg-timeslotswrapper">
		<form action="#" method="post" class="time-slots-form">
			<div class="form-group">
			  <input type="text" name="slot_title" class="form-control" name="title" placeholder="<?php esc_attr_e('Title (Optional)','docdirect');?>">
			</div>
			<div class="form-group">
			  <div class="tg-select">
				<select name="start_time" class="start_time">
				  <option><?php esc_attr_e('Start Time','docdirect');?></option>
				  <option value="0000">12:00 am</option>
				  <option value="0100">1:00 am</option>
				  <option value="0200">2:00 am</option>
				  <option value="0300">3:00 am</option>
				  <option value="0400">4:00 am</option>
				  <option value="0500">5:00 am</option>
				  <option value="0600">6:00 am</option>
				  <option value="0700">7:00 am</option>
				  <option value="0800">8:00 am</option>
				  <option value="0900">9:00 am</option>
				  <option value="1000">10:00 am</option>
				  <option value="1100">11:00 am</option>
				  <option value="1200">12:00 pm</option>
				  <option value="1300">1:00 pm</option>
				  <option value="1400">2:00 pm</option>
				  <option value="1500">3:00 pm</option>
				  <option value="1600">4:00 pm</option>
				  <option value="1700">5:00 pm</option>
				  <option value="1800">6:00 pm</option>
				  <option value="1900">7:00 pm</option>
				  <option value="2000">8:00 pm</option>
				  <option value="2100">9:00 pm</option>
				  <option value="2200">10:00 pm</option>
				  <option value="2300">11:00 pm</option>
				  <option value="2400">12:00 am (night)</option>
				</select>
			  </div>
			</div>
			<div class="form-group">
			  <div class="tg-select">
				<select name="end_time" class="end_time">
				  <option><?php esc_attr_e('End Time','docdirect');?></option>
				  <option value="0000">12:00 am</option>
				  <option value="0100">1:00 am</option>
				  <option value="0200">2:00 am</option>
				  <option value="0300">3:00 am</option>
				  <option value="0400">4:00 am</option>
				  <option value="0500">5:00 am</option>
				  <option value="0600">6:00 am</option>
				  <option value="0700">7:00 am</option>
				  <option value="0800">8:00 am</option>
				  <option value="0900">9:00 am</option>
				  <option value="1000">10:00 am</option>
				  <option value="1100">11:00 am</option>
				  <option value="1200">12:00 pm</option>
				  <option value="1300">1:00 pm</option>
				  <option value="1400">2:00 pm</option>
				  <option value="1500">3:00 pm</option>
				  <option value="1600">4:00 pm</option>
				  <option value="1700">5:00 pm</option>
				  <option value="1800">6:00 pm</option>
				  <option value="1900">7:00 pm</option>
				  <option value="2000">8:00 pm</option>
				  <option value="2100">9:00 pm</option>
				  <option value="2200">10:00 pm</option>
				  <option value="2300">11:00 pm</option>
				  <option value="2400">12:00 am (night)</option>
				</select>
			  </div>
			</div>
			<div class="form-group">
			  <div class="tg-select">
				<select name="meeting_time" class="meeting_time">
					<option><?php esc_attr_e('Meeting Time','docdirect');?></option>
					<option value="60">1 <?php esc_attr_e('hours','docdirect');?></option>
					<option value="90">1 <?php esc_attr_e('hour','docdirect');?>, 30 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="120">2 <?php esc_attr_e('hours','docdirect');?></option>
					<option value="45">45 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="30">30 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="20">20 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="15">15 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="10">10 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="5">5 <?php esc_attr_e('minutes','docdirect');?></option>
				</select>
			  </div>
			</div>
			<div class="form-group">
			  <div class="tg-select">
				<select name="padding_time" class="padding_time">
					<option><?php esc_attr_e('Padding/Break Time','docdirect');?></option>
					<option value="90">1 <?php esc_attr_e('hour','docdirect');?>, 30 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="5">5 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="10">10 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="15">15 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="20">20 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="30">30 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="45">45 <?php esc_attr_e('minutes','docdirect');?></option>
					<option value="60">1 <?php esc_attr_e('hour','docdirect');?></option>
				</select>
			  </div>
			</div>
			<div class="tg-btnbox">
			  <button type="submit" class="tg-btn kt_save-custom-time-slots"><?php pll_e('save','docdirect');?></button>
			  <button type="submit" class="tg-btn remove-slots-form"><?php pll_e('Cancel','docdirect');?></button>
			</div>
		  </div>
		</div>
   </div>
</script>
<style type="text/css">
  
/*****Confirm box*****/
.page-template-user-profile #confirmBox {
    padding-bottom: 70px;
}
.page-template-user-profile #confirmBox .checkbox {
    bottom: 20px;
    position: absolute;
    text-align: center;
    width: 100%;
}
.page-template-user-profile #confirmBox .checkbox label {
    display: inline-block;
    float: none;
    width: auto;
}
.page-template-user-profile #confirmBox .checkbox + p {
    display: none;
}
.custom-timeslots-dates_wrap > a.collapse_btn {
  display: block;
  background: #f9f9f9;
  margin: 10px 0;
  clear: both;
  padding: 0 15px;
  line-height: 40px;
  border-bottom: 1px solid #e2e2e2;
  border-left: 5px solid #7dbb01;
  color: #505050;
  position: relative;
}
.custom-timeslots-dates_wrap > a.collapse_btn i {  
  position: absolute;
  right: 15px;
  top: 8px;
  font-size: 24px;
}

</style>
<?php
function kt_add_modal_footer_bk_schedules() {
?>
<div id="modal_editevent" class="modal fade tg-add-slot_time">
  <div class="tg-modal-content" role="document">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php pll_e( 'Edit Appointment' ) ?></h4>
      </div>
      <form action="#" method="post" class="form_edit_slot_time">
        <fieldset>
            <div class="col-xs-12">
                <span class="text_date"></span>
            </div>
            <?php
                $new_from = '0000';
                $new_to = '2400';
                $time = strtotime($new_from);
                $timeStop = strtotime($new_to);
                $output = '';
                $output2 = '';
                while($time < $timeStop) {
                    $selected = '';
                    // echo date('H:i', $time);
                    if(strtotime($from_time) == $time ) {
                      $selected = 'selected';
                    }
                    $output .= '<option '.$selected.' value="'.date('Hi', $time).'">'.date('H:i', $time).'</option>';
                    $time = strtotime('+30 minutes', $time);
                    if(strtotime($to_time) == $time ) {
                      $selected = 'selected';
                    }
                    $output2 .= '<option '.$selected.' value="'.date('Hi', $time).'">'.date('H:i', $time).'</option>';
                }
            ?>
            <div class="start_time col-xs-6">
                <label for="start_time"><?php pll_e( 'Start Time' ) ?></label>
                <select name="start_time" class="">
                  <?php echo $output;?>
                </select>
            </div>
            <div class="end_time col-xs-6">
                <label for="end_time"><?php pll_e( 'End Time' ) ?></label>                
                <select name="end_time" class="">
                  <?php echo $output;?>
                </select>
            </div>
            <div class="form-groupq boxrepeat">
              <dl class="dropdown">  
                <dt>
                  <a href="javascript:;">
                    <span class="hida"><?php pll_e('Select Months For Repeat Time');?><i class="fa fa-angle-down"></i></span>
                  </a>
                </dt>              
                <dd>
                  <div class="mutliSelect">
                    <ul>
                      <li><input id="checkAll" type="checkbox" value="" /><?php pll_e('All Months');?></li>
                      <?php
                        for ($i=1; $i <= 12; $i++) { 
                        ?>
                        <li>
                          <input type="checkbox" name="repeat_month[]" value="<?php echo date("M", strtotime('00-'.$i.'-01'));?>" />
                          <?php echo date("F", strtotime('00-'.$i.'-01'));?>
                        </li>
                        <?php
                        }
                      ?>
                    </ul>
                  </div>
                </dd>
              </dl>           
            </div>
            <div class="form-groupq col-xs-6 text-center">
              <a href="javascript:;" class="btn delete_slot"><i class="fa fa-times-circle"></i><?php pll_e('Delete Slot');?></a>
            </div>
            <div class="form-groupq col-xs-6 text-center">
              <button type="submit" class="btn"><?php pll_e('Update');?></button>
            </div>
            <input type="hidden" name="start_date" class="" id="start_date">
            <input type="hidden" name="old_slot" class="" id="old_slot">
            <input type="hidden" name="id_event" class="" value="">
            <input type="hidden" name="repeat" class="" value="">
            <input type="hidden" name="action" class="" value="update_time_slot">
          <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

        </fieldset>
      </form>
  </div>
</div>
<div id="modal_addevent" class="modal fade tg-add-slot_time">
  <div class="tg-modal-content" role="document">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title"><?php pll_e( 'Submit Appointment' ) ?></h4>
      </div>
      <div class="tg-invite-form">
        <form class="kt_custom-slots-main">
          <div class="custom-timeslots-dates_wrap">
            <?php
            global $current_user, $wp_roles,$userdata,$post;
            $user_identity  = $current_user->ID;
            $custom_slots = get_user_meta($user_identity , 'custom_slots' , true);

            $today = current_time('timestamp');
            $today_date = date('Y-m-d',$today);

            if( !empty( $custom_slots ) ){
              $custom_slot_list = json_decode( $custom_slots,true );
            } else{
              $custom_slot_list = array();
            }

            $custom_slot_list = docdirect_prepare_seprate_array($custom_slot_list);

              if( !empty( $custom_slot_list[0] ) ){

                foreach( $custom_slot_list  as $key => $value ){
                  $startDate = strtotime($value['cus_start_date']);
                  $cus_start_date = date('Y-m-d',$startDate);
                  if ( strtotime($cus_start_date) >= strtotime($today_date) ) {
                    $start_date      = !empty( $value['cus_start_date'] ) ? $value['cus_start_date'] : '';
                    $end_date        = !empty( $value['cus_end_date'] ) ? $value['cus_end_date'] : '';
                    $disable_appointment  =  !empty( $value['disable_appointment'] ) ? $value['disable_appointment'] : '';
                    $txt_end = '';
                    $vl = $value['custom_time_slots'];
                    if(!is_array($vl)) {
                      $vl = json_decode($vl, true);
                    }
                    $f_slot = array_keys($vl);
                    $time = explode('-',$f_slot[0]);                    
                    $id = strtotime($start_date.' '.$time[0]);
                    if(!empty($vl)){

                    ?>
                    <div id="<?php echo $id;?>" class="custom-time-periods">
                      <input type="hidden" class="custom_time_slots" name="custom_time_slots" value="<?php echo htmlentities(stripslashes(json_encode($value['custom_time_slots']))); ?>" />
                      <input type="hidden" class="custom_time_slot_details" name="custom_time_slot_details" value="<?php echo htmlentities(stripslashes(json_encode($value['custom_time_slot_details']))); ?>" />

                      <input type="hidden" class="form-control" name="cus_start_date" value="<?php echo esc_attr( $start_date );?>" />                  
                   
                      <input type="hidden" class="form-control" name="cus_end_date" value="<?php echo esc_attr( $end_date );?>" />

                      <input type="hidden" class="form-control" name="disable_appointment" value="<?php echo esc_attr( $disable_appointment );?>" />
                    </div>
                    <?php
                  }
                  }
                }
              }

            ?>
          </div>
        </form>
        <form action="#" method="post" class="form_add_slot_time">
        <fieldset>
            <div class="col-xs-12">
                <span class="text_date"></span>
            </div>
            <?php
                $new_from = '0000';
                $new_to = '2400';
                $time = strtotime($new_from);
                $timeStop = strtotime($new_to);
                $output = '';
                $output2 = '';
                while($time < $timeStop) {
                    $selected = '';
                    // echo date('H:i', $time);
                    if(strtotime($from_time) == $time ) {
                      $selected = 'selected';
                    }
                    $output .= '<option '.$selected.' value="'.date('Hi', $time).'">'.date('H:i', $time).'</option>';
                    $time = strtotime('+30 minutes', $time);
                    if(strtotime($to_time) == $time ) {
                      $selected = 'selected';
                    }
                    $output2 .= '<option '.$selected.' value="'.date('Hi', $time).'">'.date('H:i', $time).'</option>';
                }
            ?>
            <div class="start_time col-xs-6">
                <label for="start_time"><?php pll_e( 'Start Time' ) ?></label>
                <!-- <span class=""></span>              -->
                <select name="start_time" class="">
                  <?php echo $output;?>
                </select>
            </div>
            <div class="end_time col-xs-6">
                <label for="end_time"><?php pll_e( 'End Time' ) ?></label>
                <!-- <span class=""></span>              -->
                <select name="end_time" class="">
                  <?php echo $output2;?>
                </select>
            </div>
            <div class="form-groupq boxrepeat">
              <label for="repeat"><input type="checkbox" name="repeat" class="" id="repeat"><?php pll_e( 'Repeat Timeslot Weekly?' ) ?></label>
              <dl class="dropdown">  
                <dt>
                  <a href="javascript:;">
                    <span class="hida"><?php pll_e('Select Months For Repeat Time');?><i class="fa fa-angle-down"></i></span>
                  </a>
                </dt>              
                <dd>
                  <div class="mutliSelect">
                    <ul>
                      <li><input id="checkAll" type="checkbox" value="" /><?php pll_e('All Months');?></li>
                      <?php
                        for ($i=1; $i <= 12; $i++) { 
                        ?>
                        <li>
                          <input type="checkbox" name="repeat_month[]" value="<?php echo date("M", strtotime('00-'.$i.'-01'));?>" />
                          <?php echo date("F", strtotime('00-'.$i.'-01'));?>
                        </li>
                        <?php
                        }
                      ?>
                    </ul>
                  </div>
                </dd>
              </dl>
              <script type="text/javascript">
                jQuery(document).ready(function($) {
                  $(".dropdown").hide();
                  $('input[name=repeat]:checked').each(function() {
                    $(".dropdown").slideToggle();
                  });
                  $("#modal_addevent input[name=repeat]").change(function() {
                      if(this.checked) {
                        $("#modal_addevent .dropdown").slideToggle();
                      }else {
                        $("#modal_addevent .dropdown").slideUp();
                      }
                  });
                  $('.mutliSelect').find("#checkAll").click(function(){
                    $(this).parents('.mutliSelect').find('input:checkbox').not(this).prop('checked', this.checked);
                  });
                  $(".dropdown dt a").on('click', function() {
                    $(".dropdown dd ul").slideToggle('fast');
                  });

                  $(".dropdown dd ul li a").on('click', function() {
                    $(".dropdown dd ul").slideUp();
                  });

                  function getSelectedValue(id) {
                    return $("#" + id).find("dt a span.value").html();
                  }

                  $(document).bind('click', function(e) {
                    var $clicked = $(e.target);
                    if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
                  });
                });
              </script>
            </div>
            <div class="form-groupq col-xs-6 text-center">
              <button type="button" class="btn exit" data-dismiss="modal"><i class="fa fa-times-circle"></i><?php pll_e('Exit');?></button>
            </div>
            <div class="form-groupq col-xs-6 text-center">
              <button type="submit" class="btn"><?php pll_e('Submit');?></button>
            </div>
            <!-- <input type="hidden" name="start_time" class="" id="start_time" value="">
            <input type="hidden" name="end_time" class="" id="end_time" value=""> -->
            <input type="hidden" name="start_date" class="" id="start_date">
            <input type="hidden" name="action" class="" value="add_time_slot">
          <?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); ?>

        </fieldset>
      </form>
    </div>
  </div>
</div>

<?php
}
add_action('wp_footer', 'kt_add_modal_footer_bk_schedules');