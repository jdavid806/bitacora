<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
      
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>

          <div class="_hidden_inputs _filters _tasks_filters">
              <?php
              echo form_hidden('last_30_days');
              echo form_hidden('this_month');
              echo form_hidden('this_quarter');
              echo form_hidden('this_year');
              echo form_hidden('last_month');
              echo form_hidden('last_quarter');
              echo form_hidden('last_year');
              ?>
          </div>

          <div class="title-button-group btn-group pull-right mleft4 btn-with-tooltip-group _filter_data mt-4" data-toggle="tooltip" data-title="<?php echo app_lang('filter_by'); ?>">
            <span class="dropdown inline-block">
              <button id="btn_filter" class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
              <i class="fa fa-filter" aria-hidden="true"></i> <?php echo app_lang('last_30_days'); ?>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" role="menu">
                <li role="presentation">
                  <a href="#" data-cview="last_30_days" onclick="dashboard_custom_view('last_30_days','<?php echo app_lang("last_30_days"); ?>','last_30_days'); return false;" class="dropdown-item"><?php echo app_lang('last_30_days'); ?></a>
                </li>
                <li role="presentation">
                  <a href="#" data-cview="this_month" onclick="dashboard_custom_view('this_month','<?php echo app_lang("this_month"); ?>','this_month'); return false;" class="dropdown-item"><?php echo app_lang('this_month'); ?></a>
                </li>
                <li role="presentation">
                  <a href="#" data-cview="this_quarter" onclick="dashboard_custom_view('this_quarter','<?php echo app_lang("this_quarter"); ?>','this_quarter'); return false;" class="dropdown-item"><?php echo app_lang('this_quarter'); ?></a>
                </li>
                <li role="presentation">
                  <a href="#" data-cview="this_year" onclick="dashboard_custom_view('this_year','<?php echo app_lang("this_year"); ?>','this_year'); return false;" class="dropdown-item"><?php echo app_lang('this_year'); ?></a>
                </li>
                <li role="presentation">
                  <a href="#" data-cview="last_month" onclick="dashboard_custom_view('last_month','<?php echo app_lang("last_month"); ?>','last_month'); return false;" class="dropdown-item"><?php echo app_lang('last_month'); ?></a>
                </li>
                <li role="presentation">
                  <a href="#" data-cview="last_quarter" onclick="dashboard_custom_view('last_quarter','<?php echo app_lang("last_quarter"); ?>','last_quarter'); return false;" class="dropdown-item"><?php echo app_lang('last_quarter'); ?></a>
                </li>
                <li role="presentation">
                  <a href="#" data-cview="last_year" onclick="dashboard_custom_view('last_year','<?php echo app_lang("last_year"); ?>','last_year'); return false;" class="dropdown-item"><?php echo app_lang('last_year'); ?></a>
                </li>
                <li class="presentation"><hr></li>
                 <?php $current_year = date('Y');
                    $y0 = (int)$current_year;
                    $y1 = (int)$current_year - 1;
                    $y2 = (int)$current_year - 2;
                    $y3 = (int)$current_year - 3;
                    $y4 = (int)$current_year - 4;
                 ?>
                <li role="presentation" data-filter-group="group-date">
                    <a href="#" data-cview="financial_year_<?php echo html_entity_decode($y0); ?>" onclick="dashboard_custom_view('financial_year_<?php echo html_entity_decode($y0); ?>','<?php echo app_lang("financial_year").': '.$y0; ?>','financial_year_<?php echo html_entity_decode($y0); ?>'); return false;" class="dropdown-item"><?php echo html_entity_decode($y0); ?></a>
                </li>
                <li role="presentation" data-filter-group="group-date">
                    <a href="#" data-cview="financial_year_<?php echo html_entity_decode($y1); ?>" onclick="dashboard_custom_view('financial_year_<?php echo html_entity_decode($y1); ?>','<?php echo app_lang("financial_year").': '.$y1; ?>','financial_year_<?php echo html_entity_decode($y1); ?>'); return false;" class="dropdown-item"><?php echo html_entity_decode($y1); ?></a>
                </li>
                <li role="presentation" data-filter-group="group-date">
                    <a href="#" data-cview="financial_year_<?php echo html_entity_decode($y2); ?>" onclick="dashboard_custom_view('financial_year_<?php echo html_entity_decode($y2); ?>','<?php echo app_lang("financial_year").': '.$y2; ?>','financial_year_<?php echo html_entity_decode($y2); ?>'); return false;" class="dropdown-item"><?php echo html_entity_decode($y2); ?></a>
                </li>
                <li role="presentation" data-filter-group="group-date">
                    <a href="#" data-cview="financial_year_<?php echo html_entity_decode($y3); ?>" onclick="dashboard_custom_view('financial_year_<?php echo html_entity_decode($y3); ?>','<?php echo app_lang("financial_year").': '.$y3; ?>','financial_year_<?php echo html_entity_decode($y3); ?>'); return false;" class="dropdown-item"><?php echo html_entity_decode($y3); ?></a>
                </li>
                <li role="presentation" data-filter-group="group-date">
                    <a href="#" data-cview="financial_year_<?php echo html_entity_decode($y4); ?>" onclick="dashboard_custom_view('financial_year_<?php echo html_entity_decode($y4); ?>','<?php echo app_lang("financial_year").': '.$y4; ?>','financial_year_<?php echo html_entity_decode($y4); ?>'); return false;" class="dropdown-item"><?php echo html_entity_decode($y4); ?></a>
                </li>
              </ul>
            </span>
          </div>
        </div>
        <div class="card-body">
        <div class="clearfix"></div>
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
                <div id="lead_chart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="panel_s">
              <div class="panel-body">
                <div id="form_submit_chart"></div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="panel_s">
              <div class="panel-body">
                <div id="email_template_chart"></div>
              </div>
            </div>
          </div>
        </div>
        
    </div>
  </div>
</div>

<?php require('plugins/Ma/assets/js/dashboard/manage_js.php'); ?>
