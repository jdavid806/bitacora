
<script>
function send_mail_candidate(argument, job_detail_id, company_name){
"use strict";
  $('#mail_modal').modal("show");

  var description = '';
  description += "<?php echo app_lang('I_saw_this_job_opening_from'); ?>";
  description += ' "'+company_name+'" ';
  description += "<?php echo app_lang('and_thought_you_might_find_it_interesting'); ?><br>";
  description +='&#9755; <a href="<?php echo get_uri("recruitment_portal/job_detail/") ?>'+job_detail_id+'"><?php echo app_lang("click_here_to_go_to_the_page_to_check_it_out"); ?></a> ';

  $('#mail_candidate-form input[name="subject"]').val('');
  $('#mail_candidate-form textarea[name="content"]').val(description);

}
</script>