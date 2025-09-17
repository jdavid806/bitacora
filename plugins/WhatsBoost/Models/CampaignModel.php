<?php



namespace WhatsBoost\Models;



use CodeIgniter\Model;



class CampaignModel extends Model

{

    protected $table = 'wb_campaigns';



    protected $allowedFields = ['name', 'rel_type', 'template_id', 'scheduled_send_time', 'send_now', 'header_params', 'body_params', 'footer_params', 'filename', 'pause_campaign', 'select_all', 'trigger', 'bot_type', 'is_bot_active', 'is_bot', 'created_at'];



    public function __construct()

    {

        parent::__construct();

    }



    public function get($id = '')

    {

        if (is_numeric($id)) {

            return $this->db->table(get_db_prefix().'wb_campaigns')->select(

                get_db_prefix().'wb_campaigns.*,'.

                    get_db_prefix().'wb_templates.template_name as template_name,'.

                    get_db_prefix().'wb_templates.template_id as tmp_id,'.

                    get_db_prefix().'wb_templates.header_params_count,'.

                    get_db_prefix().'wb_templates.body_params_count,'.

                    get_db_prefix().'wb_templates.footer_params_count,'.

                    'CONCAT("[", GROUP_CONCAT('.get_db_prefix().'wb_campaign_data.rel_id SEPARATOR ","), "]") as rel_ids,'

            )

                ->join(get_db_prefix().'wb_templates', get_db_prefix().'wb_templates.id = '.get_db_prefix().'wb_campaigns.template_id')

                ->join(get_db_prefix().'wb_campaign_data', get_db_prefix().'wb_campaign_data.campaign_id = '.get_db_prefix().'wb_campaigns.id', 'LEFT')

                ->where(get_db_prefix().'wb_campaigns.id', $id)->get()->getRowArray();

        }



        return $this->db->table(get_db_prefix().'wb_campaigns')->get();

    }



    public function saveTemplates($data)

    {

        $data['header_params'] = json_encode($data['header_params'] ?? []);

        $data['body_params']   = json_encode($data['body_params'] ?? []);

        $data['footer_params'] = json_encode($data['footer_params'] ?? []);



        $insert = $update = false;

        if (empty($data['id'])) {

            $insert = $this->insert($data);

            $id     = $this->getInsertID();

        } else {

            $update = $this->set($data)->where('id', $data['id'])->update();

            $id     = $data['id'];

        }



        $status  = ($insert || $update);

        $message = app_lang('something_went_wrong');



        if ($status) {

            wbHandleUploadFile($id, $data, 'template');

            $message = ($insert) ? app_lang('template_bot_create_successfully') : app_lang('template_bot_update_successfully');

        }



        return [

            'success' => $status,

            'type'    => ($status) ? 'success' : 'danger',

            'message' => $message,

        ];

    }



    public function getTemplateBots($id)

    {

        if (!empty($id)) {

            return $this->where(['id' => $id, 'is_bot' => 1])->first();

        }



        return $this->where(['is_bot' => 1])->findAll();

    }



    public function pause_resume_campaign($id)

    {

        $campaign = $this->get($id);

        $update   = $this->set(['pause_campaign' => (1 == $campaign['pause_campaign'] ? 0 : 1)])->where('id', $id)->update();



        return [

            'message'     => $update && 1 == $campaign['pause_campaign'] ? app_lang('campaign_resumed') : app_lang('campaign_paused'),

            'recirect_to' => get_uri('whatsboost/campaigns/view/'.$id),

        ];

    }



    public function delete_campaign_file($id)

    {

        $campaign = $this->get($id);

        $type     = (1 == $campaign['is_bot']) ? 'template' : 'campaign';



        $update = $this->set(['filename' => null])->where('id', $id)->update();

        $path   = getcwd().'/files/whatsboost/'.$type.'/'.$campaign['filename'];



        if ($update && file_exists($path)) {

            unlink($path);

        }



        return [

            'message'         => ($update) ? app_lang('image_deleted_successfully') : app_lang('error_occurred'),

            'recirect_to'     => (1 == $campaign['is_bot']) ? get_uri('whatsboost/bots/template/'.$id) : get_uri('whatsboost/campaigns/campaign/'.$id),

        ];

    }



    public function getTemplateBotsByRelType($relType, $message)

    {

        $builder = $this->db->table(get_db_prefix().'wb_campaigns');

        $builder->select(

            get_db_prefix().'wb_campaigns.id as campaign_table_id,'.

            get_db_prefix().'wb_campaigns.*,'.

            get_db_prefix().'wb_templates.*'

        );

        $builder->join(get_db_prefix().'wb_templates', get_db_prefix().'wb_campaigns.template_id = '.get_db_prefix().'wb_templates.id', 'left');

        $builder->where("INSTR('".$message."', `trigger`)", null, false);

        $builder->where(['rel_type' => $relType, 'is_bot' => 1, 'is_bot_active' => 1]);



        return $builder->get()->getResultArray();

    }



    public function cloneTemplateBot($type, $id)

    {

        $bot_data = $this->getTemplateBots($id);

        $bot_data['id'] = '';

        if (!empty($bot_data['filename'])) {

            $new_file_name = prepareNewFileName($bot_data['filename']);

            $bot_data['filename'] = copy(getcwd().'/files/whatsboost/template/'. $bot_data['filename'], getcwd().'/files/whatsboost/template/' . $new_file_name) ? $new_file_name : '';

        }

        $insert = $this->insert($bot_data);

        return $this->getInsertID();

    }

}

