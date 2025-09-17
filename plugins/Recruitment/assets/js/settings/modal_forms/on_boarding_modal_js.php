<script>
	var sub_group_value ='';
	var addMoreVendorsInputKey;
	
	$(document).ready(function () {
		"use strict";
		
		var uploadUrl = "<?php echo get_uri("recruitment/upload_file"); ?>";
		var validationUri = "<?php echo get_uri("recruitment/validate_onboarding_file"); ?>";
		
		var options = {};
		options.maxFiles = 1;
		var dropzone = attachDropzoneWithForm("#items-dropzone", uploadUrl, validationUri, options);

		$("#add_on_boarding-form").appForm({
			ajaxSubmit: false,
			onSuccess: function (result) {
				if (window.refreshAfterUpdate) {
					window.refreshAfterUpdate = false;
					location.reload();
				} else {
					$("#on_boarding-table").appTable({newData: result.data, dataId: result.id});
				}
			}
		});

		$("#add_on_boarding-form .select2").select2();


		$("body").on('change', 'select[name="send_to"]', function () {
			"use strict";
			var send_to = $('select[name="send_to"]').val();
			var $html = '';

			if(send_to != 'candidate'){

				$.post("<?php echo get_uri("recruitment/change_send_to/"); ?>"+send_to).done(function(response) {
					response = JSON.parse(response);
					$('#email_to_div').removeClass('hide');
					if(response.type == 'staff'){
						$('select[name="email_to[]"]').attr('required','true');
						$('select[name="email_to[]"]').attr('data-rule-required','true');
						$('select[name="email_to[]"]').html('');
						$html = '';                        
						$.each(response.list, function() {
							$html += '<option value="'+ this.email +'">'+ this.first_name+' '+this.last_name +'</option>';
						});
						$('select[name="email_to[]"]').append($html);
					}else{
						$('select[name="email_to[]"]').attr('required','true');
						$('select[name="email_to[]"]').attr('data-rule-required','true');
						$('select[name="email_to[]"]').html('');
						$html = '';                        
						$.each(response.list, function() {
							$html += '<option value="'+ this.id +'">'+ this.title +'</option>';
						});
						$('select[name="email_to[]"]').append($html);
					}
					$('select[name="email_to[]"]').select2('destroy');
					$('select[name="email_to[]"]').select2();

				}); 
			}else{
				$('#email_to_div').addClass('hide');
				$('select[name="email_to[]"]').removeAttr('required');
				$('select[name="email_to[]"]').removeAttr('data-rule-required');
			}        
		});


	});
</script>