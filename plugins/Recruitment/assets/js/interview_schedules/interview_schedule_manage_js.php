<script type="text/javascript">
	var addMoreCandidateInputKey;
	(function($) {
		"use strict"; 

		$(".select2").select2();
		setDatePicker("#cp_from_date_filter, #cp_to_date_filter, #interview_day");

		var InterviewServerParams = {
			"cp_from_date_filter": "[name='cp_from_date_filter']",
			"cp_to_date_filter": "[name='cp_to_date_filter']",
			"cp_manager_filter": "[name='cp_manager_filter[]']",
		};

		var table_interview = $('.table-table_interview');


		initDataTable('.table-table_interview', "<?php echo get_uri("recruitment/table_interview") ?>", '', '', InterviewServerParams);
		$.each(InterviewServerParams, function(i, obj) {
			$('select' + obj).on('change', function() {
				table_interview.DataTable().ajax.reload();
			});
		});

		$('input[name="cp_from_date_filter"]').on('change', function() {  
			table_interview.DataTable().ajax.reload();
		});
		$('input[name="cp_to_date_filter"]').on('change', function() {  
			table_interview.DataTable().ajax.reload();
		});

		addMoreCandidateInputKey = $('.list_candidates input[name*="email"]').length;
		$("body").on('click', '.new_candidates', function() {

			var data_select = {};
			data_select.count = addMoreCandidateInputKey;
			data_select.class = 'danger';
			data_select.class_btn = 'remove_candidates';
			data_select.i = 'x';
			$.get("<?php echo get_uri("recruitment/get_candidate_sample") ?>",data_select).done(function(response){
				response = JSON.parse(response);
				$('.list_candidates').append(response.html);

				$('.select_candidate_class2 .select2').select2('destroy');
				$('.select_candidate_class2 .select2').select2();

			});
			addMoreCandidateInputKey++;

		});

		$("body").on('click', '.remove_candidates', function() {
			$(this).parents('#candidates-item').remove();
		});



	})(jQuery);
	var job_position;

	function new_interview_schedule() {
		"use strict";
		$('#interview_schedules_modal').modal('show');
		$('.edit-title').addClass('hide');
		$('#additional_interview').html('');

		$('select[id="candidate"]').val('').change();
		$('select[id="interviewer"]').val('').change();
		$('input[id="is_name"]').val('').change();
		$('input[id="from_time"]').val('');
		$('input[id="to_time"]').val('');
		$('select[id="campaign"]').val('').change();
		job_position ='';
		$('input[id="email"]').val('');
		$('input[id="phonenumber"]').val('');
		$('input[id="interview_location"]').val('');


		requestGetJSON('get_candidate_sample').done(function (response) {
			addMoreCandidateInputKey = response.total_candidate;
			$('#custom_fields_items').html(response.custom_fields_html);

			$('.list_candidates').html('');
			$('.list_candidates').append(response.html);

			$('.select_candidate_class1 .select2').select2('destroy');
			$('.select_candidate_class1 .select2').select2();

		});

	}


	function edit_interview_schedule(invoker,id){
		"use strict";
		$('#interview_schedules_modal').modal('show');
		$('.add-title').addClass('hide');
		$('.edit-title').removeClass('hide');
		$('#additional_interview').html('');
		$('#additional_interview').append(hidden_input('id',id));
		$('#interview_schedules_modal input[name="is_name"]').val($(invoker).data('is_name'));

		if($(invoker).data('position') != 0 && $(invoker).data('position') != ''){
			job_position = $(invoker).data('position');

		}else{
			job_position = '';

		}
		if($(invoker).data('campaign') != 0){

			$('#interview_schedules_modal select[name="campaign"]').val($(invoker).data('campaign')).change();
		}else{
			$('#interview_schedules_modal select[name="campaign"]').val('').change();

		}

		$('#interview_schedules_modal input[name="interview_day"]').val($(invoker).data('interview_day'));
		$('#interview_schedules_modal input[name="from_time"]').val($(invoker).data('from_time'));
		$('#interview_schedules_modal input[name="to_time"]').val($(invoker).data('to_time'));
		$('#interview_schedules_modal input[name="interview_location"]').val($(invoker).data('interview_location'));

		var interviewer = $(invoker).data('interviewer');
		if(typeof(interviewer) == "string"){
			$('#interview_schedules_modal select[name="interviewer[]"]').val( ($(invoker).data('interviewer')).split(',')).change();
		}else{
			$('#interview_schedules_modal select[name="interviewer[]"]').val($(invoker).data('interviewer')).change();

		}


		$.post("<?php echo get_uri("recruitment/get_candidate_edit_interview/") ?>"+id).done(function(response) {
			response = JSON.parse(response);
			addMoreCandidateInputKey = response.total_candidate;

			$('.list_candidates').html('');
			$('.list_candidates').append(response.html);
			$('.select_candidate_class2 .select2').select2('destroy');
			$('.select_candidate_class2 .select2').select2();
			
		});
	}


	function candidate_infor_change(invoker){
		"use strict";
		var result = invoker.name.match(/\d/g);
		var data = {};
		data.interview_day = $('input[name="interview_day"]').val();
		data.from_time = $('input[name="from_time"]').val();
		data.to_time = $('input[name="to_time"]').val();
		data.candidate = invoker.value;
		data.id = $('input[name="id"]').val();


		result = result[0];

		if(invoker.value == ''){
			$('#email'+result).text('');
			$('#phonenumber'+result).text('');

		}else{

			$.post("<?php echo get_uri("recruitment/get_candidate_infor_change/") ?>"+invoker.value).done(function(response) {
				response = JSON.parse(response);
				$('#email'+result).text(response.email);
				$('#phonenumber'+result).text(response.phonenumber);

			});

			$.post("<?php echo get_uri("recruitment/check_time_interview") ?>",data).done(function(response) {
				response = JSON.parse(response);
				if(response.return == true){
					appAlert.warning(response.rs);

					$('select[name="candidate['+result+']"]').val('').change();
				}
			});
		}
	}


	function campaign_change(){
		"use strict";

		var data_select = {};
		data_select.campaign = $('select[name="campaign"]').val();

		$.post("<?php echo get_uri("recruitment/get_position_fill_data") ?>",data_select).done(function(response){
			response = JSON.parse(response);
			$("select[name='position']").html('');

			$("select[name='position']").append(response.position);

			if(job_position != 0 || job_position != ''){

				$('#interview_schedules_modal select[name="position"]').val(job_position).change();

			}
		});
	};
</script>