<head>
  <title><?php echo html_entity_decode($form->name); ?></title>
</head>
  <?php echo view('includes/head'); ?>
<body class="web-to-lead <?php echo html_entity_decode($form->form_key) . ($styled === '1' ? ' styled' : ''); ?>">
<div class="container-fluid">
  <div class="row">
    <div class="<?php if($col){echo html_entity_decode($col);} else {echo html_entity_decode($styled === '1' ? 'col-md-6 col-md-offset-3' : 'col-md-12');} ?> form-col">
      <?php if($with_logo) { ?>
        <div class="text-center mbot10 logo">
          <?php 
          echo get_company_logo(get_default_company_id(), "invoice"); ?>
        </div>
      <?php } ?>
      <div id="response"></div>
      <?php echo form_open_multipart(get_uri('ma_forms/wtl/'.$form->form_key),array('id'=>$form->form_key,'class'=>'disable-on-submit')); ?>
      <?php echo form_hidden('key',$form->form_key); ?>
      <div class="row">
        <?php foreach($form_fields as $field){
         render_form_builder_field($field);
       } ?>
      <div class="clearfix"></div>
      <div class="text-left col-md-12 submit-btn-wrapper">
        <button class="btn btn-success" id="form_submit" type="submit"><?php echo html_entity_decode($form->submit_btn_name); ?></button>
      </div>
    </div>

    <?php echo form_close(); ?>
  </div>
</div>
</div>

<?php require 'plugins/Ma/assets/js/components/web_to_lead_js.php';?>
