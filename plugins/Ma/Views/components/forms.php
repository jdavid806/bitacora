<div class="page-title clearfix">
   <div class="title-button-group">
      <a href="<?php echo admin_url('ma/form'); ?>" class="btn btn-default">
        <i data-feather="plus-circle" class="icon-16"></i> <?php echo _l('add'); ?>
      </a>
   </div>
 </div>

<div class="row general-form">
  <div class="col-md-3">
        <?php echo render_select('category',$categories,array('id','name'),'category', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
  </div>
</div>
<hr class="hr-panel-heading">
<div class="row">
	<div class="col-md-12">
		<?php render_datatable(array(
       _l('id'),
       _l('name'),
       _l('total_submissions'),
       _l('datecreator'),
        '<i data-feather="menu" class="icon-16"></i>'
       ),'form'); ?>
	</div>
</div>
<div class="clearfix"></div>
<?php require 'plugins/Ma/assets/js/components/form_js.php';?>
