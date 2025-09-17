<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('asset_id', $asset->id); ?>
                      <?php echo form_hidden('timezone', get_setting('timezone')); ?>
                      
                      <table class="table table-striped table-margintop">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><span style="color: <?php echo html_entity_decode($asset->color); ?>"><?php echo html_entity_decode($asset->name); ?></span></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($asset->category) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($asset->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($asset->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('download_url'); ?></td>
                              <td>
                                <div class="row">
                                  <div class="col-md-9">
                                    <?php echo render_input('link_register','', site_url('ma_public/download_asset_file/'.$asset->id), 'text', ['readonly' => true]); ?>
                                 </div>
                                  <div class="col-md-3">
                                    <a href="javascript:void(0)" onclick="copy_public_link(); return false;" class="btn btn-warning btn-with-tooltip text-white" data-toggle="tooltip" title="<?php echo _l('copy_link'); ?>" data-placement="bottom"><span data-feather="copy" class="icon-16"></span></a>
                                  </div>
                                </div>
                              </td>
                           </tr>
                            <tr class="project-overview">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($asset->description) ; ?></td>
                           </tr>
                          
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <div class="comment-image-box clearfix">

                    <?php
                    $files = unserialize($asset->files);
                    $total_files = count($files);
                    $file_path = get_setting("ma_asset_file_path");
                    echo view("includes/timeline_preview", array("files" => $files, 'file_path' => $file_path));


                    if ($total_files) {
                        $download_caption = app_lang('download');
                        if ($total_files > 1) {
                            $download_caption = sprintf(app_lang('download_files'), $total_files);
                        }
                        echo "<i data-feather='paperclip' class='icon-16'></i>";
                        echo anchor(get_uri("ma/download_asset_files/" . $asset->id), $download_caption, array("class" => "float-end", "title" => $download_caption));
                    }
                    ?>
                </div>
                  </div>
                </div>
                <div class="horizontal-scrollable-tabs preview-tabs-top">
                  <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                    <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                    <div class="horizontal-tabs">
                      <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                          <li role="presentation" class="active">
                             <a href="#chart_statistics" aria-controls="chart_statistics" role="tab" id="tab_expiry_date" data-toggle="tab">
                                <?php echo _l('chart_statistics'); ?>
                             </a>
                          </li>
                      </ul>
                      </div>
                  </div>
                  <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="chart_statistics">
                      <div id="container_download_chart"></div>
                    </div>
                  </div>


      </div>
   </div>
</div>
<?php require 'plugins/Ma/assets/js/components/asset_detail_js.php';?>
