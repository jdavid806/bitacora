<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="card-body">
          <h4 class="customer-profile-group-heading"><?php echo _l('email_template'); ?></h4>
          <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
          <hr class="hr-color">
          <div class="row">
            <div class="col-md-6">
              <?php echo form_hidden('timezone', get_setting('timezone')); ?>
              <?php echo form_hidden('email_template_id', $email_template->id); ?>
              <table class="table table-striped  no-margin">
                <tbody>
                    <tr class="project-overview">
                      <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                      <td><span style="color: <?php echo html_entity_decode($email_template->color); ?>"><?php echo html_entity_decode($email_template->name); ?></span></td>
                   </tr>
                   <tr class="project-overview">
                      <td class="bold"><?php echo _l('category'); ?></td>
                      <td><?php echo ma_get_category_name($email_template->category); ?></td>
                   </tr>
                   <tr class="project-overview">
                      <?php $value = (($email_template->published == 1) ? _l('yes') : _l('no')); ?>
                      <?php $text_class = (($email_template->published == 1) ? 'text-success' : 'text-danger'); ?>
                      <td class="bold"><?php echo _l('published'); ?></td>
                      <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                   </tr>
                  </tbody>
            </table>
          </div>
          <div class="col-md-6">
              <table class="table table-striped  no-margin">
                <tbody>
                  <tr class="project-overview">
                      <td class="bold" width="30%"><?php echo _l('datecreator'); ?></td>
                      <td><?php echo _d($email_template->dateadded) ; ?></td>
                   </tr>
                   <tr class="project-overview">
                      <td class="bold"><?php echo _l('addedfrom'); ?></td>
                      <td><?php echo get_staff_full_name($email_template->addedfrom) ; ?></td>
                   </tr>
                   <tr class="project-overview">
                      <td class="bold"><?php echo _l('description'); ?></td>
                      <td><?php echo html_entity_decode($email_template->description) ; ?></td>
                   </tr>
                  </tbody>
            </table>
          </div>
        </div>

        <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#overview"><?php echo app_lang('preview'); ?></a></li>

            <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#statistics"><?php echo app_lang('statistics'); ?></a></li>

            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri('ma/leads/'.$email_template->id.'/email_template'); ?>" data-bs-target="#leads"><?php echo app_lang('leads'); ?></a></li>
        </ul>
        <div class="tab-content mt-3">
            <div role="tabpanel" class="tab-pane fade" id="overview">
              <div class="row">
                <div class="col-md-12">
                  <div class="title-button-group">
                     <a href="#" class="btn btn-default add_language">
                       <i data-feather="plus-circle" class="icon-16"></i> <?php echo _l('add_country'); ?>
                     </a>
                     <a href="#" class="btn btn-default clone_language">
                       <i data-feather="copy" class="icon-16"></i> <?php echo _l('clone_design'); ?>
                     </a>
                  </div>
                </div>
              </div>
              <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title mt-3" role="tablist">
                <?php foreach($email_template->data_design as $key => $design){ ?>
                  <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#country_<?php echo html_entity_decode($design['id']) ?>"><?php echo html_entity_decode($design['country']) ?></a></li>
                <?php } ?>
              </ul>
              <div class="tab-content mtop15">
                  <?php foreach($email_template->data_design as $key => $design){ ?>
                    <div role="tabpanel" class="tab-pane fade <?php echo ($key == 0 ? 'active' : '') ?>" id="country_<?php echo html_entity_decode($design['id']); ?>">
                        <div class="row">
                          <div class="col-md-12">
                            <div class="title-button-group">
                               <a href="<?php echo admin_url('ma/email_template_design/'.$design['id']); ?>" class="btn btn-default">
                                 <i data-feather="layout" class="icon-16"></i> <?php echo _l('design'); ?>
                               </a>
                               <a href="<?php echo admin_url('ma/delete_email_template_design/'.$design['id'].'/'.$email_template->id); ?>" class="btn btn-default _delete">
                                 <i data-feather="x-circle" class="icon-16"></i> <?php echo _l('delete'); ?>
                               </a>
                            </div>
                          </div>
                        </div>
                        <div id="EmailEditor" class="mt-3"><?php echo json_decode($design['data_html']); ?></div>
                    </div>
                  <?php } ?>
              </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="statistics">
              <div class="row">
                <div class="col-md-12 mb-3">
                  <div class="panel_s">
                    <div class="panel-body">
                      <div id="container"></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="panel_s">
                    <div class="panel-body">
                      <div id="container_campaign" class="container_campaign"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="leads"></div>
        </div>
      </div>
   </div>
</div>

<div class="modal fade" id="language-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('country')?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>

         <?php echo form_open_multipart(admin_url('ma/add_email_template_language'),array('id'=>'language-form', 'class' => 'general-form'));?>
         <?php echo form_hidden('email_template_id', $email_template->id); ?>
         <div class="modal-body">
            <div class="form-group">
              <div class="row">
                   <?php echo render_input('country','country','', 'text', array('required' => true)); ?>
              </div>
          </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('close'); ?></button>
            <button group="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<div class="modal fade" id="clone-design-modal">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('clone_design')?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>

         <?php echo form_open_multipart(admin_url('ma/clone_email_template_design'),array('id'=>'clone-design-form', 'class' => 'general-form'));?>
         <?php echo form_hidden('email_template_id', $email_template->id); ?>
         <div class="modal-body">
            <div class="form-group select-placeholder">
                <label for="from_country" class="control-label"><?php echo _l('from_country'); ?></label>
                <select name="from_country" id="from_country" class="select2 form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                   <option value=""></option>
                   <?php foreach($email_template->data_design as $key => $design){ ?>
                    <option value="<?php echo html_entity_decode($design['id']); ?>"><?php echo html_entity_decode($design['country']); ?></option>
                  <?php } ?>
                </select>
             </div>
             <div class="form-group">
                <div class="row">
                     <?php echo render_input('to_country','to_country','', 'text', array('required' => true)); ?>
                </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('close'); ?></button>
            <button group="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<?php require 'plugins/Ma/assets/js/emails/email_template_detail_js.php'; ?>
