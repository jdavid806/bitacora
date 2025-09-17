<script>

	function uploadfilecsv(event){
		'use strict';

		if(($("#file_csv").val() != '') && ($("#file_csv").val().split('.').pop() == 'xlsx')){
			var formData = new FormData();
			formData.append("file_csv", $('#file_csv')[0].files[0]);
			formData.append("rise_csrf_token", $('input[name="rise_csrf_token"]').val());
			formData.append("leads_import", $('input[name="leads_import"]').val());

			//show box loading
			var html = '';
			html += '<div class="Box">';
			html += '<span>';
			html += '<span></span>';
			html += '</span>';
			html += '</div>';
			$('#box-loading').html(html);
			$(event).attr( "disabled", "disabled" );

			$.ajax({ 

				url: "<?php echo get_uri("hr_payroll/import_attendance_excel") ?>",
				method: 'post', 
				data: formData, 
				contentType: false, 
				processData: false

			}).done(function(response) {
				response = JSON.parse(response);
				//hide boxloading
				$('#box-loading').html('');
				$(event).removeAttr('disabled')

				$("#file_csv").val(null);
				$("#file_csv").change();
				$("#page-content").find("#file_upload_response").html();

				if($("#page-content").find("#file_upload_response").html() != ''){
					$("#page-content").find("#file_upload_response").empty();
				};

				if(response.total_rows){
					$( "#file_upload_response" ).append( "<h5><?php echo _l("_Result") ?></h5><h6><?php echo app_lang('import_line_number') ?> :"+response.total_rows+" </h6>" );
				}
				if(response.total_row_success){
					$( "#file_upload_response" ).append( "<h6><?php echo app_lang('import_line_number_success') ?> :"+response.total_row_success+" </h6>" );
				}
				if(response.total_row_false){
					$( "#file_upload_response" ).append( "<h6><?php echo app_lang('import_line_number_failed') ?> :"+response.total_row_false+" </h6>" );
				}
				if(response.total_row_false > 0)
				{
					$( "#file_upload_response" ).append( '<a href="'+response.site_url+'\\'+response.filename+'" class="btn btn-warning"  ><?php echo app_lang('hr_download_file_error') ?></a>' );
				}
				if(response.total_rows < 1){
					appAlert.warning(response.message);
				}
			});
			return false;
		}else if($("#file_csv").val() != ''){
			appAlert.warning("<?php echo app_lang('_please_select_a_file') ?>");
		}

	}

	function dowload_contract_excel(){
		'use strict';

		var formData = new FormData();
		formData.append("rise_csrf_token", $('input[name="rise_csrf_token"]').val());
		formData.append("month_attendance", $('input[name="month_attendance"]').val());
		$.ajax({ 

			url: "<?php echo get_uri("hr_payroll/create_attendance_sample_file") ?>",
			method: 'post', 
			data: formData, 
			contentType: false, 
			processData: false
		}).done(function(response) {
			response = JSON.parse(response);
			if(response.success == true){

				appAlert.success("<?php echo app_lang('create_attendance_file_success') ?>");

				$('.staff_contract_download').removeClass('hide');
				$('.staff_contract_create').addClass('hide');

				$('.staff_contract_download').attr({target: '_blank', 
					href  : response.site_url+'\\'+response.filename});

			}else{
				appAlert.warning("<?php echo app_lang('create_attendance_file_false') ?>");
			}
		});
	}

	$('#month_attendance').on('change', function() {
		'use strict';

		$('.staff_contract_download').addClass('hide');
		$('.staff_contract_create').removeClass('hide');

	});
</script>