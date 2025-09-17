<div class="row mt-3">
    

    <div class="col-md-6 col-sm-6  widget-container">
        <div class="card dashboard-icon-widget">
            <div class="card-body">
                <div class="widget-icon bg-coral">
                    <i data-feather="heart" class="icon-16 text-white"></i>
                </div>
                <div class="widget-details">
                    <h1><?php echo ma_lead_total_point($lead_id); ?></h1>
                    <span class="bg-transparent-white"><?php echo _l('point'); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-6  widget-container">
        <div class="card dashboard-icon-widget">
            <div class="card-body">
                <div class="widget-icon bg-success">
                    <i data-feather="layers" class="icon-16 text-white"></i>
                </div>
                <div class="widget-details">
                    <h1><?php echo count($campaigns); ?></h1>
                    <span class="bg-transparent-white"><?php echo _l('total_number_of_campaigns'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">

       <table class="table items">
            <thead>
              <tr class="project-overview">
                  <th class="text-center bold"><?php echo _l('campaign'); ?></th>
                  <th class="text-center bold"><?php echo _l('change_points'); ?></th>
               </tr>
            </thead>
            <tbody>
            <?php foreach ($campaigns as $value) { 
                $campaign_point = ma_lead_total_point_by_campaign($lead_id, $value['campaign_id']);
                ?>
                <tr class="project-overview">
                  <td class="" width="30%">
                    <a href="<?php echo admin_url('ma/campaign_detail/'.$value['campaign_id']); ?>">
                            <?php echo ma_get_campaign_name($value['campaign_id']); ?>
                         </a>
                    </td>
                  <?php $point = $campaign_point; ?>
                  <?php $text_class = (($campaign_point >= 0) ? 'text-success' : 'text-danger'); ?>
                  <td class="text-center <?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($point) ; ?></td>
               </tr>
            <?php } ?>
        </table>
    </div>
</div>