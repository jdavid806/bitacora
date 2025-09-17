<div id="new-asset-dropzone" class="post-dropzone">
    <div id="page-content" class="page-wrapper clearfix">
        <?php echo form_hidden('site_url', get_uri()); ?>
        <div class="card">
            <div class="page-title clearfix">
                <h1><?php echo html_entity_decode($title); ?></h1>
            </div>
            <div class="card-body">
             <?php
                $form_class = '';
                if(isset($asset)){
                   echo form_hidden('is_edit','true');
                }
                ?>
            <?php $id = (isset($asset) ? $asset->id : ''); ?>
             <?php echo form_open_multipart(get_uri('ma/asset/'.$id),array('id'=>'expense-form','class'=>'general-form')) ;?>
               
                <div class="row">
                <div class="col-md-6">
                   <?php $value = (isset($asset) ? $asset->name : ''); ?>
                   <?php echo render_input('name','name',$value, 'text', array('required' => true)); ?>
                   <?php $value = (isset($asset) ? $asset->category : ''); ?>
                   <?php echo render_select('category',$category, array('id', 'name'),'category',$value, array('required' => true)); ?>
                   <?php $value = (isset($asset) ? $asset->color : ''); ?>
                   <?php echo render_color_picker('color',_l('color'),$value); ?>
                   <div class="form-group">
                     <?php
                       $selected = (isset($asset) ? $asset->published : '');
                       ?>
                     <label for="published"><?php echo _l('published'); ?></label><br />
                     <div class="radio radio-inline radio-primary">
                       <input type="radio" name="published" id="published_yes" value="1" <?php if($selected == '1'|| $selected == ''){echo 'checked';} ?> class="form-check-input">
                       <label for="published_yes"><?php echo _l("yes"); ?></label>
                     </div>
                     <div class="radio radio-inline radio-primary">
                       <input type="radio" name="published" id="published_no" value="0" <?php if($selected == '0'){echo 'checked';} ?> class="form-check-input">
                       <label for="published_no"><?php echo _l("no"); ?></label>
                     </div>
                   </div>
                   <?php
                    $description = (isset($asset) ? $asset->description : ''); 
                    ?>
                   <p class="bold"><?php echo _l('description'); ?></p>
                   <?php echo render_textarea('description','',$description); ?>
                </div>
                <div class="col-md-6">
                   <?php if(isset($asset) && $asset->attachment !== ''){ ?>
                   <div class="row">
                      <div class="col-md-12">
                         <i class="<?php echo get_mime_class($asset->filetype); ?>"></i> <a href="<?php echo admin_url('ma/download_file/ma_asset/'.$asset->id); ?>"><?php echo html_entity_decode($asset->attachment); ?></a>
                      </div>
                   </div>
                   <?php } ?>
                    <?php echo view("includes/dropzone_preview"); ?>
                    <button class="btn btn-default upload-file-button float-start me-auto btn-sm round mt-3 <?php
                    if (isset($asset) && $asset->id) {
                        echo "hide";
                    }
                    ?>" type="button" style="color:#7988a2"><i data-feather='camera' class='icon-16'></i> <?php echo app_lang("upload_file"); ?></button>
                </div>
             </div>
             <hr>
              <div class="btn-bottom-toolbar text-right">
                    <a href="<?php echo admin_url('ma/components?group=assets'); ?>" class="btn btn-default"><i data-feather="x" class="icon-16"></i> <?php echo _l('back'); ?></a>
                   <button type="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
                </div>
             <?php echo form_close(); ?>
          </div>
       </div>
    </div>
</div>
<?php require 'plugins/Ma/assets/js/components/asset_js.php';?>
