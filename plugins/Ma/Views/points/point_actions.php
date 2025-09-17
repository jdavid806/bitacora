<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
            <div class="title-button-group">
               <a href="<?php echo admin_url('ma/point_action'); ?>" class="btn btn-default">
                 <i data-feather="plus-circle" class="icon-16"></i> <?php echo _l('add'); ?>
               </a>
            </div>
        </div>
        <div class="card-body">
					<div class="row general-form">
					  <div class="col-md-3">
					        <?php echo render_select('category',$categories,array('id','name'),'category', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
					  </div>
					</div>
					<hr class="hr-panel-heading">
					<div class="row">
						<div class="col-md-12">
							<?php 
								$table_data = array(
					            _l('id'),
									_l('name'),
									_l('category'),
									_l('dateadded'),
            '<i data-feather="menu" class="icon-16"></i>'
									);
								render_datatable($table_data,'point-actions');
							?>
						</div>
					</div>
        </div>
  </div>
</div>

<?php require 'plugins/Ma/assets/js/points/point_actions_manage_js.php';?>
