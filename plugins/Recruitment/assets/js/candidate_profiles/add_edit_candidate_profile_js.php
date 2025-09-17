<script type="text/javascript">

	(function($) {
		"use strict";

		$("#wizard-picture").change(function(){
			readURL(this);
		});
		
		$(".select2").select2();
		setDatePicker("#birthday,#days_for_identity");

		$("#generate_password").on('click', function () {

            $("#password").val(getRndomString(8));
        });

		$("#show_hide_password").on('click', function () {
			
            var $target = $("#password"),
                    type = $target.attr("type");
            if (type === "password") {
                $(this).attr("title", "<?php echo app_lang("hide_text"); ?>");
                $(this).html("<span data-feather='eye-off' class='icon-16'></span>");
                feather.replace();
                $target.attr("type", "text");
            } else if (type === "text") {
                $(this).attr("title", "<?php echo app_lang("show_text"); ?>");
                $(this).html("<span data-feather='eye' class='icon-16'></span>");
                feather.replace();
                $target.attr("type", "password");
            }
        });


		$('input[name="email"]').on('change', function() {
			candidate_email_exists();
		});

		var addMoreWorkexperienceInputKey = $('.work_experience input[name*="from_date"]').length;
		$("body").on('click', '.new_work_experience', function() {
			if ($(this).hasClass('disabled')) { return false; }

			var newattachment = $('.work_experience').find('#work_experience-item').eq(0).clone().appendTo('.work_experience');
			newattachment.find('button[role="button"]').remove();

			newattachment.find('input[id="from_date[0]"]').removeClass('init-datepicker-from-date0').addClass('init-datepicker-from-date'+addMoreWorkexperienceInputKey);
			newattachment.find('input[id="to_date[0]"]').removeClass('init-datepicker-to-date0').addClass('init-datepicker-to-date'+addMoreWorkexperienceInputKey);

			newattachment.find('input[id="from_date[0]"]').attr('name', 'from_date[' + addMoreWorkexperienceInputKey + ']').val('');
			newattachment.find('input[id="from_date[0]"]').attr('id', 'from_date[' + addMoreWorkexperienceInputKey + ']').val('');

			newattachment.find('input[id="to_date[0]"]').attr('name', 'to_date[' + addMoreWorkexperienceInputKey + ']').val('');
			newattachment.find('input[id="to_date[0]"]').attr('id', 'to_date[' + addMoreWorkexperienceInputKey + ']').val('');

			newattachment.find('input[id="company[0]"]').attr('name', 'company[' + addMoreWorkexperienceInputKey + ']').val('');
			newattachment.find('input[id="company[0]"]').attr('id', 'company[' + addMoreWorkexperienceInputKey + ']').val('');

			newattachment.find('input[id="position[0]"]').attr('name', 'position[' + addMoreWorkexperienceInputKey + ']').val('');
			newattachment.find('input[id="position[0]"]').attr('id', 'position[' + addMoreWorkexperienceInputKey + ']').val('');

			newattachment.find('input[id="contact_person[0]"]').attr('name', 'contact_person[' + addMoreWorkexperienceInputKey + ']').val('');
			newattachment.find('input[id="contact_person[0]"]').attr('id', 'contact_person[' + addMoreWorkexperienceInputKey + ']').val('');

			newattachment.find('input[id="salary[0]"]').attr('name', 'salary[' + addMoreWorkexperienceInputKey + ']').val('');
			newattachment.find('input[id="salary[0]"]').attr('id', 'salary[' + addMoreWorkexperienceInputKey + ']').val('');

			newattachment.find('input[id="reason_quitwork[0]"]').attr('name', 'reason_quitwork[' + addMoreWorkexperienceInputKey + ']').val('');
			newattachment.find('input[id="reason_quitwork[0]"]').attr('id', 'reason_quitwork[' + addMoreWorkexperienceInputKey + ']').val('');

			newattachment.find('textarea[id="job_description[0]"]').attr('name', 'job_description[' + addMoreWorkexperienceInputKey + ']').val('');
			newattachment.find('textarea[id="job_description[0]"]').attr('id', 'job_description[' + addMoreWorkexperienceInputKey + ']').val('');

			newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
			newattachment.find('button[name="add"]').removeClass('new_work_experience').addClass('remove_work_experience').removeClass('btn-success').addClass('btn-danger');

			setDatePicker(".init-datepicker-from-date"+addMoreWorkexperienceInputKey);
			setDatePicker(".init-datepicker-to-date"+addMoreWorkexperienceInputKey);

			addMoreWorkexperienceInputKey++;

		});

		$("body").on('click', '.remove_work_experience', function() {
			$(this).parents('#work_experience-item').remove();
		});

		var addMoreSkillInputKey = $('.skill textarea[name*="skill_name"]').length;
		$("body").on('click', '.new_skill', function() {
			if ($(this).hasClass('disabled')) { return false; }

			var newattachment = $('.skill').find('#skill-item').eq(0).clone().appendTo('.skill');
			newattachment.find('button[role="button"]').remove();

			newattachment.find('textarea[id="skill_name[0]"]').attr('name', 'skill_name[' + addMoreSkillInputKey + ']').val('');
			newattachment.find('textarea[id="skill_name[0]"]').attr('id', 'skill_name[' + addMoreSkillInputKey + ']').val('');

			newattachment.find('textarea[id="skill_description[0]"]').attr('name', 'skill_description[' + addMoreSkillInputKey + ']').val('');
			newattachment.find('textarea[id="skill_description[0]"]').attr('id', 'skill_description[' + addMoreSkillInputKey + ']').val('');

			newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
			newattachment.find('button[name="add"]').removeClass('new_skill').addClass('remove_skill').removeClass('btn-success').addClass('btn-danger');
			addMoreSkillInputKey++;
			setDatePicker(" .datePickerInput");


		});

		$("body").on('click', '.remove_skill', function() {
			$(this).parents('#skill-item').remove();
		});

		var addMoreLiteracyInputKey = $('.list_literacy input[name*="from_date"]').length;
		$("body").on('click', '.new_literacy', function() {
			if ($(this).hasClass('disabled')) { return false; }

			var newattachment = $('.list_literacy').find('#literacy-item').eq(0).clone().appendTo('.list_literacy');
			newattachment.find('button[role="button"]').remove();

			newattachment.find('input[id="literacy_from_date[0]"]').removeClass('init-datepicker-literacy-from-date0').addClass('init-datepicker-literacy-from-date'+addMoreLiteracyInputKey);
			newattachment.find('input[id="literacy_to_date[0]"]').removeClass('init-datepicker-literacy-to-date0').addClass('init-datepicker-literacy-to-date'+addMoreLiteracyInputKey);


			newattachment.find('input[id="literacy_from_date[0]"]').attr('name', 'literacy_from_date[' + addMoreLiteracyInputKey + ']').val('');
			newattachment.find('input[id="literacy_from_date[0]"]').attr('id', 'literacy_from_date[' + addMoreLiteracyInputKey + ']').val('');

			newattachment.find('input[id="literacy_to_date[0]"]').attr('name', 'literacy_to_date[' + addMoreLiteracyInputKey + ']').val('');
			newattachment.find('input[id="literacy_to_date[0]"]').attr('id', 'literacy_to_date[' + addMoreLiteracyInputKey + ']').val('');

			newattachment.find('input[id="diploma[0]"]').attr('name', 'diploma[' + addMoreLiteracyInputKey + ']').val('');
			newattachment.find('input[id="diploma[0]"]').attr('id', 'diploma[' + addMoreLiteracyInputKey + ']').val('');

			newattachment.find('input[id="training_places[0]"]').attr('name', 'training_places[' + addMoreLiteracyInputKey + ']').val('');
			newattachment.find('input[id="training_places[0]"]').attr('id', 'training_places[' + addMoreLiteracyInputKey + ']').val('');

			newattachment.find('input[id="specialized[0]"]').attr('name', 'specialized[' + addMoreLiteracyInputKey + ']').val('');
			newattachment.find('input[id="specialized[0]"]').attr('id', 'specialized[' + addMoreLiteracyInputKey + ']').val('');

			newattachment.find('input[id="training_form[0]"]').attr('name', 'training_form[' + addMoreLiteracyInputKey + ']').val('');
			newattachment.find('input[id="training_form[0]"]').attr('id', 'training_form[' + addMoreLiteracyInputKey + ']').val('');

			newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
			newattachment.find('button[name="add"]').removeClass('new_literacy').addClass('remove_literacy').removeClass('btn-success').addClass('btn-danger');

			setDatePicker(".init-datepicker-literacy-from-date"+addMoreLiteracyInputKey);
			setDatePicker(".init-datepicker-literacy-to-date"+addMoreLiteracyInputKey);

			addMoreLiteracyInputKey++;


		});

		$("body").on('click', '.remove_literacy', function() {
			$(this).parents('#literacy-item').remove();
		});

		var addMoreFamilyinforInputKey = $('.list_family_infor input[name*="relationship"]').length;
		$("body").on('click', '.new_family_infor', function() {
			if ($(this).hasClass('disabled')) { return false; }

			var newattachment = $('.list_family_infor').find('#family_infor-item').eq(0).clone().appendTo('.list_family_infor');
			newattachment.find('button[role="button"]').remove();

			newattachment.find('input[id="fi_birthday[0]"]').removeClass('init-datepicker-fi-birthday0').addClass('init-datepicker-fi-birthday'+addMoreFamilyinforInputKey);

			newattachment.find('input[id="relationship[0]"]').attr('name', 'relationship[' + addMoreFamilyinforInputKey + ']').val('');
			newattachment.find('input[id="relationship[0]"]').attr('id', 'relationship[' + addMoreFamilyinforInputKey + ']').val('');

			newattachment.find('input[id="name[0]"]').attr('name', 'name[' + addMoreFamilyinforInputKey + ']').val('');
			newattachment.find('input[id="name[0]"]').attr('id', 'name[' + addMoreFamilyinforInputKey + ']').val('');

			newattachment.find('input[id="fi_birthday[0]"]').attr('name', 'fi_birthday[' + addMoreFamilyinforInputKey + ']').val('');
			newattachment.find('input[id="fi_birthday[0]"]').attr('id', 'fi_birthday[' + addMoreFamilyinforInputKey + ']').val('');

			newattachment.find('input[id="job[0]"]').attr('name', 'job[' + addMoreFamilyinforInputKey + ']').val('');
			newattachment.find('input[id="job[0]"]').attr('id', 'job[' + addMoreFamilyinforInputKey + ']').val('');

			newattachment.find('input[id="address[0]"]').attr('name', 'address[' + addMoreFamilyinforInputKey + ']').val('');
			newattachment.find('input[id="address[0]"]').attr('id', 'address[' + addMoreFamilyinforInputKey + ']').val('');

			newattachment.find('input[id="phone[0]"]').attr('name', 'phone[' + addMoreFamilyinforInputKey + ']').val('');
			newattachment.find('input[id="phone[0]"]').attr('id', 'phone[' + addMoreFamilyinforInputKey + ']').val('');

			newattachment.find('button[name="add"] i').removeClass('fa-plus').addClass('fa-minus');
			newattachment.find('button[name="add"]').removeClass('new_family_infor').addClass('remove_family_infor').removeClass('btn-success').addClass('btn-danger');

			setDatePicker(".init-datepicker-fi-birthday"+addMoreFamilyinforInputKey);
			
			addMoreFamilyinforInputKey++;

		});

		$("body").on('click', '.remove_family_infor', function() {
			$(this).parents('#family_infor-item').remove();
		});

		$("input[data-type='currency']").on({
			keyup: function() {        
				formatCurrency($(this));
			},
			blur: function() { 
				formatCurrency($(this), "blur");
			}
		});

	})(jQuery);

	function readURL(input) {
		"use strict";
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	function formatNumber(n) {
		"use strict";

		return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
	}
	function formatCurrency(input, blur) {
		"use strict";
		var input_val = input.val();
		if (input_val === "") { return; }
		var original_len = input_val.length; 
		var caret_pos = input.prop("selectionStart");
		if (input_val.indexOf(".") >= 0) {
			var decimal_pos = input_val.indexOf(".");
			var left_side = input_val.substring(0, decimal_pos);
			var right_side = input_val.substring(decimal_pos);
			left_side = formatNumber(left_side);
			right_side = formatNumber(right_side);
			right_side = right_side.substring(0, 2);
			input_val = left_side + "." + right_side;
		} else {

			input_val = formatNumber(input_val);
			input_val = input_val;
		}
		input.val(input_val);
		var updated_len = input_val.length;
		caret_pos = updated_len - original_len + caret_pos;
		input[0].setSelectionRange(caret_pos, caret_pos);
	}

	function candidate_email_exists() {
		'use strict';
		
		var data = {};
		data.email = $('input[name="email"]').val();
		data.id = $('input[name="candidateid"]').val();

		if($('input[name="email"]').val() != ''){

			$.post("<?php echo get_uri("recruitment/candidate_email_exists") ?>", data).done(function(response) {
				response = JSON.parse(response);
				if(!response.success){
					appAlert.warning(response.message);
				}else{
					appAlert.success("<?php echo app_lang('hr_valid_email') ?>", { duration: 1});
					
				}
			}).fail(function(data) {
				appAlert.warning(data.responseText);
			});

		}
	}
	

</script>