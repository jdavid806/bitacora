<div class='row'>
	<div class="form-group">
		<div class=" col-md-12">
			<?php
			echo form_textarea(array(
				"id" => "content",
				"name" => "content",
				"value" => $model_info->content ? $model_info->content : $model_info->default_message,
				"class" => "form-control"
			));
			?>
		</div>
	</div>
</div>
<div><strong><?php echo app_lang("avilable_variables"); ?></strong>: <?php
foreach ($variables as $variable) {
	echo html_entity_decode($variable).', ';
}
?></div>
<hr />
