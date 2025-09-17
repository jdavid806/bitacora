<?php

namespace Ma\Controllers;

use App\Controllers\App_Controller;

class Ma_public extends App_Controller
{
    public function index()
    {
        show_404();
    }

    /**
     * [download_asset_file description]
     * @param  string $asset_id [description]
     * @return [type]           [description]
     */
    public function download_asset_file($asset_id = '')
    {   
        if ($asset_id != '') {
            $Ma_model = new \Ma\Models\Ma_model();
            $asset = $Ma_model->get_asset($asset_id); 
            $Ma_model->download_asset($asset_id);

            return $this->download_app_files(get_setting("ma_asset_file_path"), $asset->files);
        }else {
            die('folder not specified');
        }
    }

    /**
     * [tracking_click description]
     * @return [type] [description]
     */
    public function tracking_click(){
        $email = $this->request->getGet('email');
        $campaign = $this->request->getGet('campaign');
        $lead = $this->request->getGet('lead');
        $url = $this->request->getGet('href');
        $db = db_connect('default');

        $db_builder = $db->table(db_prefix() . 'ma_email_logs');
        $db_builder->where('email_id', $email);
        $db_builder->where('campaign_id', $campaign);
        $db_builder->where('lead_id', $lead);
        $db_builder->update(['click' => 1]);

        $db_builder = $db->table(db_prefix() . 'ma_email_click_logs');
        $db_builder->insert([
            'lead_id' => $lead,
            'campaign_id' => $campaign,
            'email_id' => $email,
            'url' => $url,
            'time' => date('Y-m-d H:i:s'),
        ]);

        header("Location: ".$url, TRUE, 301);
        exit();
    }

    /**
     * email tracking open
     * @param  [type] $hash 
     * @return [type]       
     */
    public function images($hash)
    {
        //THIS RETURNS THE IMAGE
        header('Content-Type: image/gif');
        readfile(APP_MODULES_PATH .'Ma/tracking.gif');

        $hash = str_replace('.jpg', '',$hash);

        $db = db_connect('default');

        $db_builder = $db->table(db_prefix() . 'ma_email_logs');

        $db_builder->where('hash', $hash);

        $db_builder->where('open', 0);

        $db_builder->update(['open' => 1, 'open_time' => date("Y-m-d H:i:s")]);

        die;
    }

    /**
     * email tracking click
     * @param  [type] $hash [description]
     * @return [type]       [description]
     */
    public function click($hash)
    {
        $url = $this->input->get('href');

        $db = db_connect('default');

        $db_builder = $db->table(db_prefix() . 'ma_email_logs');

        $db_builder->where('hash', $hash);
        $email_log = $db_builder->get()->getRow();

        $db_builder = $db->table(db_prefix() . 'ma_email_logs');
        $db_builder->where('hash', $hash);
        $db_builder->where('click', 0);
        $db_builder->update(['click' => 1, 'click_time' => date("Y-m-d H:i:s")]);

        $db_builder = $db->table(db_prefix() . 'ma_email_click_logs');
        $db_builder->insert([
            'client_id' => $email_log->client_id,
            'lead_id' => $email_log->lead_id,
            'campaign_id' => $email_log->campaign_id,
            'email_id' => $email_log->email_id,
            'url' => $url,
            'time' => date('Y-m-d H:i:s'),
        ]);

        header("Location: ".$url, TRUE, 301);
        die;
    }

    /**
     * email tracking download asset
     * @param  string $hash
     * @return      
     */
    public function asset($hash)
    {   
        $db = db_connect('default');
        $db_builder = $db->table(db_prefix() . 'ma_asset_logs');
        $db_builder->where('hash', $hash);
        $asset_log = $db_builder->get()->getRow();

        $path = '';
        if($asset_log){
            $db_builder = $db->table(db_prefix() . 'ma_assets');
            $db_builder->where('id', $asset_log->asset_id);
            $asset = $db_builder->get()->getRow();

            $db_builder = $db->table(db_prefix() . 'ma_asset_logs');
            $db_builder->where('hash', $hash);
            $db_builder->where('download', 0);
            $db_builder->update(['download' => 1, 'download_time' => date("Y-m-d H:i:s")]);
            if ($db->affectedRows() > 0) {
                $db_builder = $db->table(db_prefix() . 'ma_point_action_logs');
                $db_builder->insert([
                    'campaign_id' => $asset_log->campaign_id, 
                    'lead_id' => $asset_log->lead_id, 
                    'client_id' => $asset_log->client_id, 
                    'point_action_id' => 0, 
                    'point' => $asset->change_points,
                    'dateadded' => date('Y-m-d H:i:s'), 
                ]);
            }
            $Ma_model = new \Ma\Models\Ma_model();
            $Ma_model->download_asset($asset->id, $asset_log->id);

            return $this->download_app_files(get_setting("ma_asset_file_path"), $asset->files);
        }else {
            die('folder not specified');
        }
    }

    /**
     * email unsubscribe
     * @param  [type] $hash [description]
     * @return [type]       [description]
     */
    public function unsubscribe($hash)
    {
        $db = db_connect('default');
        $db_builder = $db->table(db_prefix() . 'ma_email_logs');
        $db_builder->where('hash', $hash);
        $email_log = $db_builder->get()->getRow();

        if($email_log){
            if ($email_log->lead_id) {
                $db_builder = $db->table(db_prefix() . 'clients');
                $db_builder->where('id', $email_log->lead_id);
                $db_builder->update(['ma_unsubscribed' => 1]);
            }else{
                $db_builder = $db->table(db_prefix() . 'clients');
                $db_builder->where('id', $email_log->client_id);
                $db_builder->update(['ma_unsubscribed' => 1]);
            }
        }

        return view('Ma\Views\unsubscribe');
    }
}