<script>

	var signaturePad;
	var croppedCtx;

	(function($) {
		"use strict";

		var data_send_mail = {};
		<?php if(isset($send_mail_approve)){ 
			?>
			data_send_mail = <?php echo json_encode($send_mail_approve); ?>;
			data_send_mail.rel_id = <?php echo html_entity_decode($goods_receipt->id); ?>;
			data_send_mail.rel_type = '1';
			data_send_mail.addedfrom = <?php echo html_entity_decode($goods_receipt->addedfrom); ?>;

			$.get("<?php echo get_uri("warehouse/send_mail") ?>", data_send_mail).done(function(response){
				response = JSON.parse(response);

			}).fail(function(error) {

			});
		<?php } ?>

		SignaturePad.prototype.toDataURLAndRemoveBlanks = function() {
			var canvas = this._ctx.canvas;
			 // First duplicate the canvas to not alter the original
			 var croppedCanvas = document.createElement('canvas');
			 croppedCtx = croppedCanvas.getContext('2d');

			 croppedCanvas.width = canvas.width;
			 croppedCanvas.height = canvas.height;
			 croppedCtx.drawImage(canvas, 0, 0);

			 // Next do the actual cropping
			 var w = croppedCanvas.width,
			 h = croppedCanvas.height,
			 pix = {
			 	x: [],
			 	y: []
			 },
			 imageData = croppedCtx.getImageData(0, 0, croppedCanvas.width, croppedCanvas.height),
			 x, y, index;

			 for (y = 0; y < h; y++) {
			 	for (x = 0; x < w; x++) {
			 		index = (y * w + x) * 4;
			 		if (imageData.data[index + 3] > 0) {
			 			pix.x.push(x);
			 			pix.y.push(y);

			 		}
			 	}
			 }
			 pix.x.sort(function(a, b) {
			 	return a - b
			 });
			 pix.y.sort(function(a, b) {
			 	return a - b
			 });
			 var n = pix.x.length - 1;

			 w = pix.x[n] - pix.x[0];
			 h = pix.y[n] - pix.y[0];
			 var cut = croppedCtx.getImageData(pix.x[0], pix.y[0], w, h);

			 croppedCanvas.width = w;
			 croppedCanvas.height = h;
			 croppedCtx.putImageData(cut, 0, 0);

			 return croppedCanvas.toDataURL();
			};

			var canvas = document.getElementById("signature");
			signaturePad = new SignaturePad(canvas, {
				maxWidth: 2,
				onEnd:function(){
					signaturePadChanged();
				}
			});

			$('#identityConfirmationForm').submit(function() {
				signaturePadChanged();
			});


		})(jQuery);


		function signaturePadChanged() {
			"use strict";

			var input = document.getElementById('signatureInput');
			var $signatureLabel = $('#signatureLabel');
			$signatureLabel.removeClass('text-danger');

			if (signaturePad.isEmpty()) {
				$signatureLabel.addClass('text-danger');
				input.value = '';
				return false;
			}

			$('#signatureInput-error').remove();
			var partBase64 = signaturePad.toDataURLAndRemoveBlanks();
			partBase64 = partBase64.split(',')[1];
			input.value = partBase64;
		}


		function signature_clear(){
			"use strict";

			var canvas = document.getElementById("signature");
			var signaturePad = new SignaturePad(canvas, {
				maxWidth: 2,
				onEnd:function(){

				}
			});
			signaturePad.clear();
			$('input[name="signature"]').val('');

		}

		$('#accept_action').on('click', function(){
			'use strict';
			signature_clear();

			$('#identityConfirmationModal').modal('show');
			$('.sign_by').html('');
			$('.sign_by').append(hidden_input('sign_by','company'));
		})

		$('#staff_accept_action').on('click', function(){
			'use strict';
			signature_clear();

			$('#identityConfirmationModal').modal('show');
			$('.sign_by').html('');
			$('.sign_by').append(hidden_input('sign_by','staff'));
		})
		

		function sign_request(id){
			"use strict";
			var signature_val = $('input[name="signature"]').val();
			if(signature_val.length > 0){
				change_request_approval_status(id,1, true);
				$('.sign_request_class').prop('disabled', true);
				$('.sign_request_class').html('<?php echo _l('wait_text'); ?>');
				$('.clear').prop('disabled', true);
			}else{
				appAlert.warning("<?php echo _l('please_sign_the_form') ?>");
				$('.sign_request_class').prop('disabled', false);
				$('.clear').prop('disabled', false);
			}
		}

		function change_request_approval_status(id, status, sign_code){
			"use strict";

			var data = {};
			data.rel_id = id;
			data.rel_type = '2';
			data.sign_by = $('input[name="sign_by"]').val();

			data.approve = status;

			if(status == 1){

				if(sign_code == true){
					data.signature = $('input[name="signature"]').val();
				}

				$.post("<?php echo get_uri("hr_profile/staff_contract_sign/") ?>" + id, data).done(function(response){
					response = JSON.parse(response); 
					if (response.success === true || response.success == 'true') {
						appAlert.success(response.message);
						window.location.reload();
					}
				});
			}

		}


	</script>
