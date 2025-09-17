<div class='row'>
	<div class="col-md-8">
		
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
	<div class="col-md-4">

		<?php if(isset($variables)){ ?>
			<p class="bold mtop10 text-left"><strong><?php echo app_lang("avilable_variables"); ?></strong></p>
			<div class=" avilable_merge_fields mtop15 ">
				<ul class="list-group">
					<?php
					foreach($variables as $field){

						echo '<li class="list-group-item"><b>'.$field['name'].'</b>  <p class="float-right text-uppercase" onclick="insert_merge_field(this); return false">'.$field['key'].'</p></li>';
					}
					?>
				</ul>
			</div>
		<?php } ?>

</div>
</div>

<hr />
<script type="text/javascript">
	function insert_merge_field(field) {
		return ;
	}
</script>
