<?php 
$Ma_model = model("Ma\Models\Ma_model"); 

$columns_data = [];
foreach ($data as $category) {
$cpicker = '';

$category_color = ($category['color'] != '' ? $category['color'] : '#323a45');


$total_pages = $category['total_pages'];
$stages = $category['stages'];
$total_stages = count($stages);

if($category['id'] == 0){
  continue;
}
foreach ($stages as $stage) {
  $exising_items = get_array_value($columns_data, $category['id']);
  if (!$exising_items) {
      $exising_items = "";
  }

  $published = '';
  if($stage['published'] == 1){
    $published = '<span class="text-success">'._l('published').'</span>';
  } 

  $item = $exising_items . ' <a href="'.get_uri('ma/stage_detail/'.$stage['id']).'" class="kanban-item d-block " data-id="'.$stage['id'].'">
  <span style="color: '. $stage['color'] .'">'.$stage['name'].'</span>
  <div class="clearfix">
    <div class="mt10 font-12 float-start">'.$published.'</div>
      <div class="mt10 font-12 float-end" title="'._l('total_lead').'"><i data-feather="users" class="icon-16"></i> '.count($Ma_model->get_lead_by_stage($stage['id'])).'</div>
  </div>
 </a>';

  $columns_data[$category['id']] = $item;
 }
 } ?>

<div id="kanban-wrapper">

<ul id="kanban-container" class="kanban-container clearfix">
  <?php foreach ($data as $category) { ?>
  <li class="kanban-col kanban-<?php echo html_entity_decode($category['id']); ?>" >
      <div class="kanban-col-title border-bottom-kanban kanban-border-bottom" style="border-bottom-color: <?php echo html_entity_decode($category['color']); ?>"> <?php echo html_entity_decode($category['name']); ?> <span class="<?php echo html_entity_decode($category['id']); ?>-task-count float-end"></span></div>
      <div  id="kanban-item-list-<?php echo html_entity_decode($category['id']); ?>" class="kanban-item-list" data-category-id="<?php echo html_entity_decode($category['id']); ?>">
          <?php echo get_array_value($columns_data, $category['id']); ?>
      </div>
  </li>
<?php } ?>
</ul>
</div>
</div>
<img id="move-icon" class="hide" src="<?php echo get_file_uri("assets/images/move.png"); ?>" alt="..." />

<?php require 'plugins/Ma/assets/js/stages/kanban_view_js.php';?>
