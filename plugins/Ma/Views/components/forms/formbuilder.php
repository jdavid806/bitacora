
<?php if (isset($form)) {
    echo form_hidden('form_id', $form->id);
} ?>
<div id="page-content" class="page-wrapper clearfix general-form">
    <?php echo form_hidden('site_url', base_url()); ?>
    <?php echo form_hidden('admin_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
               <?php if (isset($form)) { ?>
                  <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                     <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#tab_form_build"><?php echo app_lang('form_builder'); ?></a></li>
                     <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#tab_form_information"><?php echo app_lang('form_information'); ?></a></li>
                     <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#tab_form_integration"><?php echo app_lang('form_integration_code'); ?></a></li>
                   </ul>

                  <?php } ?>
                  <div class="tab-content mt-3">
                     <?php if (isset($form)) { ?>
                        
                     <div role="tabpanel" class="tab-pane fade" id="tab_form_build">
                        <div id="build-wrap"></div>
                     </div>
                     <div role="tabpanel" class="tab-pane fade" id="tab_form_integration">
                        <p><?php echo _l('form_integration_code_help'); ?></p>
                        <textarea class="form-control" rows="2"><iframe width="600" height="850" src="<?php echo site_url('ma_forms/wtl/'.$form->form_key); ?>" frameborder="0" allowfullscreen></iframe></textarea>
                        <h4 class="mtop15 font-medium bold">Share direct link</h4>
                        <p>
                          <span class="label label-default">
                            <a href="<?php echo site_url('ma_forms/wtl/'.$form->form_key).'?styled=1'; ?>" target="_blank">
                              <?php echo site_url('ma_forms/wtl/'.$form->form_key).'?styled=1'; ?>
                            </a>
                          </span>
                          <br />
                          <br />
                          <span class="label label-default">
                            <a href="<?php echo site_url('ma_forms/wtl/'.$form->form_key).'?styled=1&with_logo=1'; ?>" target="_blank">
                              <?php echo site_url('ma_forms/wtl/'.$form->form_key).'?styled=1&with_logo=1'; ?>
                            </a>
                          </span>
                        </p>
                          <hr />
                          <p class="bold mtop15">When placing the iframe snippet code consider the following:</p>
                          <p class="<?php if(strpos(site_url(),'http://') !== false){echo 'bold text-success';} ?>">1. If the protocol of your installation is http use a http page inside the iframe.</p>
                          <p class="<?php if(strpos(site_url(),'https://') !== false){echo 'bold text-success';} ?>">2. If the protocol of your installation is https use a https page inside the iframe.</p>
                          <p>None SSL installation will need to place the link in non ssl eq. landing page and backwards.</p>
                     </div>
                     <?php } ?>
                     <div role="tabpanel" class="tab-pane fade <?php if (!isset($form)) { echo ' active show'; } ?>" id="tab_form_information">
                        <?php if (!isset($form)) { ?>
                        <h4 class="font-medium-xs bold no-mtop"><?php echo _l('form_builder_create_form_first'); ?></h4>
                        <hr />
                        <?php } ?>
                        
                        <?php $id = (isset($form) ? $form->id : ''); ?>
                        <?php echo form_open(get_uri('ma/form/'.$id), array('id'=>'form_info')); ?>
                        <div class="row">
                           <div class="col-md-6">
                              <?php $value = (isset($form) ? $form->name : ''); ?>
                              <?php echo render_input('name', 'form_name', $value); ?>
                              <div class="form-group select-placeholder">
                                 <label for="language" class="control-label"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('form_lang_validation_help'); ?>"></i> <?php echo _l('form_lang_validation'); ?></label>
                                 <select name="language" id="language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <?php foreach ($languages as $availableLanguage) {
                                     ?>
                                    <option value="<?php echo html_entity_decode($availableLanguage); ?>"<?php if ((isset($form) && $form->language == $availableLanguage) || (!isset($form) && get_setting('active_language') == $availableLanguage)) {
                                         echo ' selected';
                                     } ?>><?php echo ucfirst($availableLanguage); ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                              <?php $value = (isset($form) ? $form->submit_btn_name : 'Submit'); ?>
                              <?php echo render_input('submit_btn_name', 'form_btn_submit_text', $value); ?>
                              <?php $value = (isset($form) ? $form->success_submit_msg : ''); ?>
                              <?php echo render_textarea('success_submit_msg', 'form_success_submit_msg', $value); ?>
                             
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" name="allow_duplicate" id="allow_duplicate" <?php if (isset($form) && $form->allow_duplicate == 1 || !isset($form)) {
                                     echo 'checked';
                                 } ?> class="form-check-input">
                                 <label for="allow_duplicate"><?php echo sprintf(_l('form_allow_duplicate'), 'lead'); ?></label>
                              </div>
                              <div class="duplicate-settings-wrapper row<?php if (isset($form) && $form->allow_duplicate == 1 || !isset($form)) {
                                     echo ' hide';
                                 } ?>">
                                 <div class="col-md-12">
                                    <hr />
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="track_duplicate_field"><?php echo _l('track_duplicate_by_field'); ?></label><br />
                                       <select class="select2 track_duplicate_field" data-width="100%" name="track_duplicate_field" id="track_duplicate_field" data-none-selected-text="">
                                          <option value=""></option>
                                          <?php foreach ($db_fields as $field) {
                                     ?>
                                          <option value="<?php echo html_entity_decode($field->name); ?>"<?php if (isset($form) && $form->track_duplicate_field == $field->name) {
                                         echo ' selected';
                                     }
                                     if (isset($form) && $form->track_duplicate_field_and == $field->name) {
                                         echo 'disabled';
                                     } ?>><?php echo html_entity_decode($field->label); ?></option>
                                          <?php } ?>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label for="track_duplicate_field_and"><?php echo _l('and_track_duplicate_by_field'); ?></label><br />
                                       <select class="select2 track_duplicate_field_and" data-width="100%" name="track_duplicate_field_and" id="track_duplicate_field_and" data-none-selected-text="">
                                          <option value=""></option>
                                          <?php foreach ($db_fields as $field) {
                                     ?>
                                          <option value="<?php echo html_entity_decode($field->name); ?>"<?php if (isset($form) && $form->track_duplicate_field_and == $field->name) {
                                         echo ' selected';
                                     }
                                     if (isset($form) && $form->track_duplicate_field == $field->name) {
                                         echo 'disabled';
                                     } ?>><?php echo html_entity_decode($field->label); ?></option>
                                          <?php } ?>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                               <div class="form-group">
                                  <div class="row">
                                    <?php $value = isset($form) ? $form->lead_status : ''; ?>
                                      <label for="lead_status_id" class=""><?php echo app_lang('status'); ?></label>
                                      <div class="">
                                          <?php
                                          foreach ($statuses as $status) {
                                              $lead_status[$status->id] = $status->title;
                                          }

                                          echo form_dropdown("lead_status", $lead_status, array($value), "class='select2'");
                                          ?>
                                      </div>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <div class="row">
                                    <?php $value = isset($form) ? $form->lead_source : ''; ?>
                                      <label for="lead_source" class=""><?php echo app_lang('source'); ?></label>
                                      <div class="">
                                          <?php
                                          $lead_source = array();

                                          foreach ($sources as $source) {
                                              $lead_source[$source->id] = $source->title;
                                          }

                                          echo form_dropdown("lead_source", $lead_source, array($value), "class='select2'");
                                          ?>
                                      </div>
                                  </div>
                              </div>

                              <?php

                                 $selected = '';
                                 foreach ($members as $staff) {
                                     if (isset($form) && $form->responsible == $staff['id']) {
                                         $selected = $staff['id'];
                                     }
                                 }
                                 ?>
                              <?php echo render_select('responsible', $members, array('id', array('first_name', 'last_name')), 'leads_import_assignee', $selected); ?>
                              <hr />
                              <label for="" class="control-label"><?php echo _l('notification_settings'); ?></label>
                              <div class="clearfix"></div>
                              <div class="checkbox checkbox-primary">
                                 <input type="checkbox" name="notify_lead_imported" id="notify_lead_imported" <?php if (isset($form) && $form->notify_lead_imported == 1 || !isset($form)) {
                                     echo 'checked';
                                 } ?> class="form-check-input">
                                 <label for="notify_lead_imported"><?php echo _l('leads_email_integration_notify_when_lead_imported'); ?></label>
                              </div>
                              <div class="select-notification-settings<?php if (isset($form) && $form->notify_lead_imported == '0') {
                                     echo ' hide';
                                 } ?>">
                              <hr />
                              <div class="radio radio-primary radio-inline">
                                 <input type="radio" name="notify_type" value="specific_staff" id="specific_staff" <?php if (isset($form) && $form->notify_type == 'specific_staff' || !isset($form)) {
                                     echo 'checked';
                                 } ?> class="form-check-input">
                                 <label for="specific_staff"><?php echo _l('specific_staff_members'); ?></label>
                              </div>
                              <div class="radio radio-primary radio-inline">
                                 <input type="radio" name="notify_type" id="roles" value="roles" <?php if (isset($form) && $form->notify_type == 'roles') {
                                     echo 'checked';
                                 } ?> class="form-check-input">
                                 <label for="roles"><?php echo _l('staff_with_roles'); ?></label>
                              </div>
                              <div class="radio radio-primary radio-inline">
                                 <input type="radio" name="notify_type" id="assigned" value="assigned" <?php if (isset($form) && $form->notify_type == 'assigned') {
                                     echo 'checked';
                                 } ?> class="form-check-input">
                                 <label for="assigned"><?php echo _l('notify_assigned_user'); ?></label>
                              </div>
                              <div class="clearfix mtop15"></div>
                              <div id="specific_staff_notify" class="<?php if (isset($form) && $form->notify_type != 'specific_staff') {
                                     echo 'hide';
                                 } ?>">
                                 <?php
                                    $selected = array();
                                    if (isset($form) && $form->notify_type == 'specific_staff') {
                                        $selected = unserialize($form->notify_ids);
                                    }
                                    ?>
                                 <?php echo render_select('notify_ids_staff[]', $members, array('id', array('first_name', 'last_name')), 'leads_email_integration_notify_staff', $selected, array('multiple'=>true)); ?>
                              </div>
                              <div id="role_notify" class="<?php if (isset($form) && $form->notify_type != 'roles' || !isset($form)) {
                                        echo 'hide';} ?>">
                                 <?php
                                    $selected = array();
                                    if (isset($form) && $form->notify_type == 'roles') {
                                        $selected = unserialize($form->notify_ids);
                                    }
                                    ?>
                                 <?php echo render_select('notify_ids_roles[]', $roles, array('id', array('title')), 'leads_email_integration_notify_roles', $selected, array('multiple'=>true)); ?>
                              </div>
                              </div>
                           </div>
                        </div>
                        <hr>
                        <div class="btn-bottom-toolbar text-right">
                           <button type="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
                        </div>
                        <?php echo form_close(); ?>
                     </div>
                  </div>


      </div>
   </div>
</div>

<?php require 'plugins/Ma/assets/js/components/_form_js_formatter_js.php';?>