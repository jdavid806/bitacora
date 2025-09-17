<script>
	
	function uploadfilecsv(){
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

				url: "<?php echo get_uri("hr_profile/import_employees_excel") ?>", 
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
					$( "#file_upload_response" ).append( "<h5>Result</h5><h6><?php echo _l('import_line_number') ?> :"+response.total_rows+" </h6>" );
				}
				if(response.total_row_success){
					$( "#file_upload_response" ).append( "<h6><?php echo _l('import_line_number_success') ?> :"+response.total_row_success+" </h6>" );
				}
				if(response.total_row_false){
					$( "#file_upload_response" ).append( "<h6><?php echo _l('import_line_number_failed') ?> :"+response.total_row_false+" </h6>" );
				}
				if(response.total_row_false > 0)
				{
					$( "#file_upload_response" ).append( '<a href="'+response.site_url+'\\'+response.filename+'" class="btn btn-warning text-white"  ><?php echo _l('hr_download_file_error') ?></a>' );
					
				}
				if(response.total_rows < 1){
					appAlert.warning(response.message);
				}
			});
			return false;
		}else if($("#file_csv").val() != ''){
			appAlert.warning("<?php echo _l("_please_select_a_file") ?>");

		}
	}


	function staff_export_item(){
		"use strict";
		var data = {};
		data.sample_file = 'true';

		$(event).addClass('disabled');

			setTimeout(function() {

				$.post("<?php echo get_uri("hr_profile/create_staff_sample_file") ?>", data).done(function(response) {
					response = JSON.parse(response);
					if(response.success == true){
						appAlert.success("<?php echo _l("create_sample_file_success") ?>");

						$('#dowload_items').removeClass('hide');
						$('.hr_export_staff').addClass('hide');

						$('#dowload_items').attr({target: '_blank', 
							href  : response.site_url+'\\'+response.filename});

					}else{
						appAlert.warning("<?php echo _l("create_sample_file_fails") ?>");

					}

				}).fail(function(data) {


				});
			}, 200);
	}

</script>