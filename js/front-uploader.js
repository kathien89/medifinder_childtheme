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
			// var ext = img.substring(img.lastIndexOf('.'));
			// img = img.replace( ext, '-150x150'+ext );
			$('.featured-image-wrap').html( '<img src="'+img+'" class="img-responsive"/>' );
		});
	});

	$(document).on( 'click', '#kt_upload-profile-avatar', function(e) {
		e.preventDefault();

		var frameArgs = {
			multiple: false,
			title: 'Select Photo',
		    library: {
		            type: [ 'image' ]
		    },
		};

		handle_images( frameArgs, function( selection ){
			model = selection.first();
			$('.userprofile_media').val( model.id );
			var img = model.attributes.url;
			var ext = img.substring(img.lastIndexOf('.'));
			img = img.replace( ext, '-270x270'+ext );
			jQuery('.tg-docimg .user-avatar').find('img').attr( 'src', img );
			jQuery('.tg-docprofile-img').find('img').attr('src', img);
		});
	});

	$(document).on( 'click', '#kt_upload-profile-banner', function(e) {
		e.preventDefault();

		var frameArgs = {
			multiple: false,
			title: 'Select Photo',
		    library: {
		            type: [ 'image' ]
		    },
		};

		handle_images( frameArgs, function( selection ){
			model = selection.first();
			$('.userprofile_banner').val( model.id );
			var img = model.attributes.url;
			// var ext = img.substring(img.lastIndexOf('.'));
			// img = img.replace( ext, '-270x270'+ext );
			jQuery('.tg-docimg .user-banner').find('img').attr( 'src', img );
		});
	});

	$(document).on( 'click', '#kt_upload-profile-company_logo', function(e) {
		e.preventDefault();

		var frameArgs = {
			multiple: false,
			title: 'Select Photo',
		    library: {
		            type: [ 'image' ]
		    },
		};

		handle_images( frameArgs, function( selection ){
			model = selection.first();
			$('.userprofile_company_logo').val( model.id );
			var img = model.attributes.url;
			// var ext = img.substring(img.lastIndexOf('.'));
			// img = img.replace( ext, '-270x270'+ext );
			jQuery('.user-company_logo').find('img').attr( 'src', img );
		});
	});

	$(document).on( 'click', '#kt_upload-profile-banner_mobile', function(e) {
		e.preventDefault();

		var frameArgs = {
			multiple: false,
			title: 'Select Photo',
		    library: {
		            type: [ 'image' ]
		    },
		};

		handle_images( frameArgs, function( selection ){
			model = selection.first();
			$('.userprofile_banner_mobile').val( model.id );
			var img = model.attributes.url;
			var ext = img.substring(img.lastIndexOf('.'));
			img = img.replace( ext, '-270x270'+ext );
			jQuery('.tg-docimg .user-banner_mobile').find('img').attr( 'src', img );
		});
	});

	$(document).on( 'click', '#vkl_attach-gallery', function(e) {
		e.preventDefault();

		var frameArgs = {
			multiple: true,
			title: 'Select Photo',
		    library: {
		            type: [ 'image' ]
		    },
		};

		handle_images( frameArgs, function( selection ){
			li_length = parseInt($('.kt_doc-user-gallery li').not('.more_slots, .kt_target').length);
			// alert(li_length);
			max = parseInt($('#plupload-container-gallery').data('max'));
			con_lai = max - li_length;
			if (selection.length > con_lai) {
				jQuery.sticky('max total images: '+max, {classList: 'important', speed: 200, autoclose: 3000});
				return false;
			}else {
				var galleryThumbnail = "";
				selection.each(function(attachment) {
					var obj_attachment = attachment.toJSON();
					// console.log(obj_attachment);
					var img = obj_attachment.url;
					var ext = img.substring(img.lastIndexOf('.'));
					img = img.replace( ext, '-270x270'+ext );
					var item = [];
					item['url'] = img;
					item['attachment_id'] = obj_attachment.id;
					// console.log(item);
					var load_gallery = wp.template( 'load-gallery' );
					var _thumb	= load_gallery(item);
				 	galleryThumbnail += '<li class="gallery-item gallery-thumb-item" id="thumb-' + obj_attachment.id + '">' + _thumb + '</li>';
				});
				// jQuery('.kt_doc-user-gallery').append(galleryThumbnail);
				jQuery(galleryThumbnail).insertBefore('.kt_doc-user-gallery .kt_target');
				li_length = parseInt($('.kt_doc-user-gallery li').not('.more_slots, .kt_target').length);
				if (li_length >= max) {
					$('#plupload-container-gallery').closest('li').hide();
					$('.more_slots').show();
				}
			}
		});
	});

	
});