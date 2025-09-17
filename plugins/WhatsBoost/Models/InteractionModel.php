<?php

namespace WhatsBoost\Models;

use App\Models\Clients_model;
use App\Models\Users_model;
use CodeIgniter\Model;

class InteractionModel extends Model
{
    protected $table = 'wb_interactions';

    protected $allowedFields = ['name', 'receiver_id', 'last_message', 'last_msg_time', 'wa_no', 'wa_no_id', 'time_sent', 'type', 'type_id', 'interaction_id', 'sender_id', 'url', 'message', 'status', 'message_id', 'staff_id', 'type', 'is_read', 'ref_message_id', 'agent'];

    protected $Clients_model;

    protected $Users_model;

    public function __construct()
    {
        parent::__construct();
        $this->Clients_model = new Clients_model();
        $this->Users_model   = new Users_model();
    }

    /**
     * Get all interaction messages from the database.
     *
     * @return array Array of interaction messages
     */
    public function get_interactions()
    {
        // Fetch interactions ordered by time_sent in descending order
        $interactions = $this->orderBy('time_sent', 'DESC')->get()->getResultArray();

        // Fetch messages for each interaction
        foreach ($interactions as &$interaction) {

            $interaction['agent'] = (is_string($interaction['agent'])) ? json_decode($interaction['agent']) : $interaction['agent'];
            if (!empty($interaction['agent']->agent_id) && is_array($interaction['agent']->agent_id)) {
                $agent_ids = $interaction['agent']->agent_id;
                $agent_name = implode(',', array_map('wbGetStaffFullName', $agent_ids));
            }

            $interaction['agent_icon'] = '';

            if (!empty($interaction['agent']->agent_id) && (is_array($interaction['agent']->agent_id) || is_object($interaction['agent']->agent_id))) {
                foreach ($interaction['agent']->agent_id as $agent_id) {
                    $interaction['agent_icon'] .= '<span class="avatar-xs avatar me-1" data-container="body" data-bs-toggle="tooltip" title="' . wbGetStaffFullName($agent_id) . '" data-bs-placement="bottom"><img src="' . wbGetStaffProfileImage($agent_id) . '" alt=""></span>';
                }
            }


            $interaction['agent_name'] = [
                'agent_name' => $agent_name ?? '',
                'assign_name' => !empty($interaction['agent']->assign_id) ? wbGetStaffFullName($interaction['agent']->assign_id) : '',
            ];

            $interaction_id = $interaction['id'];
            $messages       = $this->get_interaction_messages($interaction_id);
            $this->map_interaction($interaction);
            $interaction['messages'] = $messages;

            // Fetch staff name for each message in the interaction
            foreach ($interaction['messages'] as &$message) {
                if (!empty($message['staff_id'])) {
                    $staff                 = $this->Users_model->where(['id' => $message['staff_id'], 'user_type' => 'staff'])->get()->getRowArray();
                    $message['staff_name'] = $staff['first_name'].' '.$staff['last_name'];
                } else {
                    $message['staff_name'] = null;
                }

                // Check if URL is already a base name
                if ($message['url'] && false === strpos($message['url'], '/')) {
                    // If URL doesn't contain "/", consider it as a file name
                    // Assuming base URL is available
                    $message['asset_url'] = base_url('files/whatsboost/'.$message['url']);
                } else {
                    // Otherwise, use the URL directly
                    $message['asset_url'] = $message['url'] ?? null;
                }
            }
        }

        return $interactions;
    }

    /**
     * Insert a new interaction message into the database.
     *
     * @param array $data Data to be inserted
     *
     * @return int Insert ID
     */
    public function insert_interaction($data)
    {
        $existing_interaction = $this->where(['receiver_id' => $data['receiver_id'], 'wa_no' => $data['wa_no'], 'wa_no_id' => $data['wa_no_id']])->get()->getRow();

        if ($existing_interaction) {
            // Existing interaction found with matching 'receiver_id' and 'wa_no'
            $this->set($data)->where('id', $existing_interaction->id)->update();

            return $existing_interaction->id;
        }
        // No existing interaction found with matching 'receiver_id' and 'wa_no'
        $this->insert($data);

        return $this->getInsertID();
    }

    /**
     * Get all interaction messages for a specific interaction ID.
     *
     * @param int $interaction_id ID of the interaction
     *
     * @return array Array of interaction messages
     */
    public function get_interaction_messages($interaction_id)
    {
        return $this->db->table(get_db_prefix().'wb_interaction_messages')->where('interaction_id', $interaction_id)->orderBy('time_sent', 'asc')->get()->getResultArray();
    }

    /**
     * Insert a new interaction message into the database.
     *
     * @param array $data Data to be inserted
     *
     * @return int Insert ID
     */
    public function insert_interaction_message($data)
    {
        // Assuming wb_interaction_messages' is the table name
        $this->db->table(get_db_prefix().'wb_interaction_messages')->insert($data);

        return $this->table(get_db_prefix().'wb_interaction_messages')->getInsertID();
    }

    /**
     * Get the ID of the last message for a given interaction.
     *
     * @param int $interaction_id ID of the interaction
     *
     * @return int ID of the last message
     */
    public function get_last_message_id($interaction_id)
    {
        $this->db->table(get_db_prefix().'wb_interaction_messages')->selectMax('id')->where('interaction_id', $interaction_id);
        $query  = $this->get();
        $result = $query->row_array();

        return $result['id'];
    }

    /**
     * Update the status of a message in the database.
     *
     * @param int    $interaction_id ID of the interaction
     * @param string $status         Status to be updated
     *
     * @return void
     */
    public function update_message_status($interaction_id, $status)
    {
        $this->db->table(get_db_prefix().'wb_interaction_messages')->set(['status' => $status])->where('message_id', $interaction_id)->update();
    }

    /**
     * Map interaction data to entities based on receiver ID.
     *
     * @param array $interaction interaction data
     *
     * @return void
     */
    public function map_interaction($interaction)
    {
        if (null === $interaction['type'] || null === $interaction['type_id'] || empty($interaction['type_id'])) {
            $interaction_id = $interaction['id'];
            $receiver_id    = $interaction['receiver_id'];
            $customer       = $this->Clients_model->where(['phone' => $receiver_id, 'is_lead' => '0', 'deleted' => 0])->get()->getRow();
            $lead           = $this->Clients_model->where(['phone' => $receiver_id, 'is_lead' => '1', 'deleted' => 0])->get()->getRow();
            $staff          = $this->Users_model->where(['phone' => $receiver_id, 'user_type' => 'staff', 'deleted' => 0])->get()->getRow();

            $entity = null;
            $type   = null;

            if ($customer) {
                $entity = $customer->id;
                $type   = 'contacts';
            } elseif ($staff) {
                $entity = $staff->staffid;
                $type   = 'staff';
            } else {
                $type   = 'leads';
                $entity = (!empty($lead)) ? $lead->id : app_hooks()->apply_filters('ctl_auto_lead_creation', $receiver_id, $interaction['name']);
            }

            $data = [
                'type'        => $type,
                'type_id'     => $entity,
                'wa_no'       => $interaction['wa_no'] ?? get_setting('wb_default_phone_number'),
                'receiver_id' => $receiver_id,
            ];

            $existing_interaction = $this->where('id', $interaction_id)->get()->getRow();

            if ($existing_interaction) {
                $this->set($data)->where('id', $interaction_id)->update();
            } else {
                $data['id'] = $interaction_id;
                $this->insert($data);
            }
        }

        if (null === $interaction['wa_no'] || null === $interaction['wa_no_id']) {
            $interaction_id = $interaction['id'];

            // Use null coalescing operator to provide default values if 'wa_no' or 'wa_no_id' is null
            $wa_no    = $interaction['wa_no'] ?? get_setting('wb_default_phone_number');
            $wa_no_id = $interaction['wa_no_id'] ?? get_setting('wb_phone_number_id');

            // Prepare data for update
            $data = [
                'wa_no'    => $wa_no,
                'wa_no_id' => $wa_no_id,
            ];

            // Check if the interaction exists
            $existing_interaction = $this->where('id', $interaction_id)->get()->getRow();

            if ($existing_interaction) {
                // Update the existing interaction
                $this->set($data)->where('id', $interaction_id)->update();
            }
        }

        $agent_data =  (is_string($interaction['agent'])) ? json_decode($interaction['agent']) : $interaction['agent'];

        $agent_id = $agent_data->agent_id ?? 0;
        $data = [
            'assign_id' => 0,
            'agent_id'  => $agent_id
        ];

        if ($interaction['type'] == 'leads') {
            $options       = ['id' => $interaction['type_id'], 'is_lead' => 1, 'deleted' => 0];
            $rel_data      = $this->Clients_model->get_details($options)->getRowArray();
            $asign_id      = $rel_data['owner_id'];

            $data = [
                'assign_id' => $asign_id,
                'agent_id'  => $agent_id
            ];
        }
        $this->set('agent', json_encode($data))->where('id', $interaction['id'])->update();
    }

    public function chat_mark_as_read($id)
    {
        return $this->db->table(get_db_prefix() . 'wb_interaction_messages')->set(['is_read' => 1])->where(['interaction_id' => $id])->update();
    }

    public function get_interaction($id, $type = '', $type_id = '')
    {
        if (!empty($type_id)) {
            return $this->where('type', $type)->where('type_id', $type_id)->where(['id' => $id])->get()->getRowArray();
        }
        return $this->where(['id' => $id])->get()->getRowArray();
    }

    public function delete_chat($id)
    {
        return $this->where('id', $id)->delete();
    }

    public function add_assign_staff($post_data)
    {
        $staff_id = $post_data['staff_id'];
        $interaction_id = $post_data['interaction_id'];
        $interaction = $this->get_interaction($interaction_id);
        $asign_id = 0;
        if ($interaction && $interaction['type'] == 'leads') {
            $options       = ['id' => $interaction['type_id'], 'is_lead' => 1, 'deleted' => 0];
            $rel_data      = $this->Clients_model->get_details($options)->getRowArray();
            $asign_id      = $rel_data['owner_id'];
        }
        $data = [
            'assign_id' => $asign_id,
            'agent_id'  => $staff_id
        ];
        return $this->set('agent', json_encode($data))->where('id', $interaction['id'])->update();
    }
}
