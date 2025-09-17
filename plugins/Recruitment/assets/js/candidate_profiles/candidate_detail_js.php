<script>
	var SetRatingStar = function() {
		"use strict";
		var $star_rating = $('.star-rating .fa');
		return $star_rating.each(function() {
			if (parseInt($star_rating.siblings('input[name="rating['+$(this).data('id')+']"]').val()) >= parseInt($(this).data('rating'))) {
				return $(this).removeClass('fa-star-o').addClass('fa-star');
			} else {
				return $(this).removeClass('fa-star').addClass('fa-star-o');
			}
		});
	};
	var SetRatingViewStar = function() {
		"use strict";
		var $star_rating_view = $('.star-rating-view .fa');
		return $star_rating_view.each(function() {
			if (parseInt($star_rating_view.siblings('input.rating-value').val()) >= parseInt($(this).data('rating'))) {
				return $(this).removeClass('fa-star-o').addClass('fa-star');
			} else {
				return $(this).removeClass('fa-star').addClass('fa-star-o');
			}
		});
	};
	(function($) {
		"use strict";
		
		$(".select2").select2();
		setDatePicker("#birthday_filter");

		$('#toggle_popup_approval').on('click', function() {
			$('#popup_approval').toggle();
		});

		$('.star-rating .fa').on('click', function() {
			$('.star-rating .fa').siblings('input[name="rating['+$(this).data('id')+']"]').val($(this).data('rating'));
			return SetRatingStar();
		});
		SetRatingViewStar();
		SetRatingStar();
	})(jQuery);

	function send_mail_candidate(){
		"use strict";
		$('#mail_modal').modal('show');

	}

	function sendmail(){
		"use strict";
		$('.modal-title').html('');
		$('.modal-title').append('<span><?php echo app_lang('send_mail'); ?></span>');
		$('#care_rs').html('');
		$('#care_rs').append('<div class="form-group" app-field-wrapper="care_result"><small class="req text-danger">* </small><label for="care_result" class="control-label"><?php echo app_lang('send_mail_rs') ?></label><input type="text" id="care_result" name="care_result" class="form-control" value=""></div>');

		$('#type_care').html('');
		$('#type_care').append('<input type="hidden" name="type" value="send_mail">');

		$('#care_modal').modal('show');
		setDatePicker("#care_time");

	}
	function call(){
		"use strict";
		$('.modal-title').html('');
		$('.modal-title').append('<span><?php echo app_lang('call'); ?></span>');
		$('#care_rs').html('');
		$('#care_rs').append('<div class="form-group" app-field-wrapper="care_result"><small class="req text-danger">* </small><label for="care_result" class="control-label"><?php echo app_lang('number_of_minutes_to_call') ?></label><input type="number" id="care_result" name="care_result" class="form-control" value=""></div>');

		$('#type_care').html('');
		$('#type_care').append('<input type="hidden" name="type" value="call">');

		$('#care_modal').modal('show');
		
		setDatePicker("#care_time");

	}
	function test(){
		"use strict";
		$('.modal-title').html('');
		$('.modal-title').append('<span><?php echo app_lang('test'); ?></span>');
		$('#care_rs').html('');
		$('#care_rs').append('<div class="form-group" app-field-wrapper="care_result"><small class="req text-danger">* </small><label for="care_result" class="control-label"><?php echo app_lang('result') ?></label><input type="text" id="care_result" name="care_result" class="form-control" value=""></div>');

		$('#type_care').html('');
		$('#type_care').append('<input type="hidden" name="type" value="test">');

		$('#care_modal').modal('show');
		setDatePicker("#care_time");

	}
	function interview(){
		"use strict";
		$('.modal-title').html('');
		$('.modal-title').append('<span><?php echo app_lang('interview'); ?></span>');
		$('.modal-title').append('<span><?php echo app_lang('test'); ?></span>');
		$('#care_rs').html('');
		$('#care_rs').append('<div class="form-group" app-field-wrapper="care_result"><small class="req text-danger">* </small><label for="care_result" class="control-label"><?php echo app_lang('result') ?></label><input type="text" id="care_result" name="care_result" class="form-control" value="" required></div>');

		$('#type_care').html('');
		$('#type_care').append('<input type="hidden" name="type" value="interview">');

		$('#care_modal').modal('show');
		setDatePicker("#care_time");
	
	}

	function submit_care_candidate(){
		"use strict";
		var data = $('#care_candidate-form').serialize();
		var url = $('#care_candidate-form').action;

		$.post("<?php echo get_uri("recruitment/care_candidate") ?>", data).done(function(response) {
			response = JSON.parse(response);
				appAlert.success(response.mess);

			$('#care_modal').modal('hide');
		});
	}

	function submit_rating_candidate(){
		"use strict";
		var data = $('#rating-modal').serialize();

		$.post("<?php echo get_uri("recruitment/rating_candidate") ?>", data).done(function(response) {
			response = JSON.parse(response);
			$('.star-rating-view input[name="rating"]').val(response.rate);
			SetRatingViewStar();
			SetRatingStar();
				appAlert.success(response.mess);

			$('#candidate_rating').modal('hide');
		});
	}

	function close_modal_preview(){
		"use strict";
		
		$('._project_file').modal('hide');
	}

	function delete_candidate_attachment(id) {
		"use strict";
			$.get("<?php echo get_uri("recruitment/delete_candidate_attachment/") ?>" + id).done(function(success) {
				if (success) {
					$("#candidate_pv_file").find('[data-attachment-id="' + id + '"]').remove();
				}
			}).fail(function(error) {
				appAlert.error(error.responseText);

			});
	}


	function change_status_candidate(invoker,id){
		"use strict";

		$.post("<?php echo get_uri("recruitment/change_status_candidate/") ?>"+invoker.value+'/'+id).done(function(reponse){
			reponse = JSON.parse(reponse);
			window.location.href = "<?php echo get_uri("recruitment/candidate/") ?>"+id;
				appAlert.success(reponse.result);

		});
	}
	function open_rating_dialog(){
		"use strict";
		$('#candidate_rating').modal('show');
	}

	function re_status_mark_as(status, task_id, type) {
		"use strict"; 

		var taskModalVisible = $('#task-modal').is(':visible');
		$("body").append('<div class="dt-loader"></div>');

		$.get("<?php echo get_uri("recruitment/re_status_mark_as/") ?>"  + status + '/' + task_id + '/' + type).done(function(response) {
			response = JSON.parse(response);
			$("body").find('.dt-loader').remove();
			if (response.success === true || response.success == 'true') {
				appAlert.success(response.message);

				location.reload();
			}
		});
	}
</script>