<?php

namespace Ma\Controllers;

use App\Controllers\Security_Controller;

class Ma extends Security_Controller
{

    protected $Ma_model;

    function __construct() {
        parent::__construct();
        $this->Ma_model = new \Ma\Models\Ma_model();
        app_hooks()->do_action('app_hook_ma_init');
    }

    /**
     * Dashboard
     * @return view
     */
    public function dashboard(){
        $data['title'] = _l('dashboard');
        
        return $this->template->rander('Ma\Views\dashboard/manage', $data);
    }

    /**
     * @return view
     */
    public function segments(){
        $data['title'] = _l('segments');

        $data['group'] = $this->request->getGet('group');

        if($data['group'] == ''){
            $data['group'] = 'list';
        }

        if ($data['group'] == 'chart') {
            $data['data_segment_pie'] = $this->Ma_model->get_data_segment_pie_chart($data);
            $data['data_segment_column'] = $this->Ma_model->get_data_segment_column_chart($data);
        }

        $data['categories'] = $this->Ma_model->get_category('', 'segment');
        
        $data['view'] = 'Ma\Views\segments/includes/' . $data['group'];

        return $this->template->rander('Ma\Views\segments/manage', $data);
    }

    /**
     * setting
     * @return view
     */
    public function setting()
    {
        $data          = [];
        $data['group'] = $this->request->getGet('group');

        $data['tab'][] = 'category';
        $data['tab'][] = 'ma_email_templates';
        $data['tab'][] = 'permissions';
        $data['tab'][] = 'email_configuration';
        
        if ($data['group'] == '') {
            $data['group'] = 'category';
        }

        $data['members'] = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "status" => "active"))->getResultArray();
        $data['title']        = _l($data['group']);
        $data['tabs']['view'] = 'Ma\Views\settings/' . $data['group'];

        return $this->template->rander('Ma\Views\settings/manage', $data);
    }

    /**
     * category table
     * @return json
     */
    public function category_table(){
           
            $select = [
                'id',
                'name',
                'type',
                'description'
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_categories';
            $join         = [];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['name'];
                $row[] = _l($aRow['type']);
                $row[] = $aRow['description'];

                /*options*/
                $edit = '';
                $edit .= '<li role="presentation"><a href="#" onclick="edit_category(' . $aRow['id'] . '); return false" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_category/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';


                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'. $edit . $delete. '</ul>
                </span>';
                $row[] = $_data;

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or edit category
     * @return json
     */
    public function category(){
        $data = $this->request->getPost();
        $message = '';

        if($data['id'] == ''){
            $success = $this->Ma_model->add_category($data);
            if($success){
                $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('category')));
            }
        }else{
            $id = $data['id'];
            unset($data['id']);
            $success = $this->Ma_model->update_category($data, $id);
            if ($success) {
                $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('category')));
            }
        }

        app_redirect('ma/setting?group=category');
    }

    /**
     * delete category
     * @param  integer $id
     * @return
     */
    public function delete_category($id)
    {
        $success = $this->Ma_model->delete_category($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('category')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/setting?group=category'));
    }

    /**
     * get data category
     * @param  integer $id 
     * @return json     
     */
    public function get_data_category($id){
        $category = $this->Ma_model->get_category($id);

        echo json_encode($category);
    }

    /**
     * stage table
     * @return json
     */
    public function stage_table(){
            $select = [
                'name',
                'category',
                'weight',
                'published',
                'id',
            ];

            $where = [];

            // Filter by custom groups
            $categorys   = $this->Ma_model->get_category('', 'stage');

            $categoryIds = [];
            $category_names = [];
            foreach ($categorys as $category) {
                if ($this->request->getPost('stage_category_' . $category['id'])) {
                    array_push($categoryIds, $category['id']);
                }
                $category_names[$category['id']] = $category['name'];
            }

            if (count($categoryIds) > 0) {
                array_push($where, 'AND category IN (' . implode(', ', $categoryIds) . ')');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_stages';
            $join         = [
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'ma_stages.id as id', 'color']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<span style="color: '. $aRow['color'] .'">'.$aRow['name'].'</span>';

                $row[] = $categoryOutput;
                $row[] = $aRow['weight'];
                $row[] = ma_get_category_name($aRow['category']);

                $checked = '';
                if ($aRow['published'] == 1) {
                    $checked = 'checked';
                }
                $_data = '<div class=" form-check form-switch onoffswitch">
                <input type="checkbox" class="form-check-input " data-switch-url="' . site_url('ma/change_stage_published').'" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                <label class="form-check-label onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                </div>';

                $_data .= '<span class="hide">' . ($checked == 'checked' ? app_lang('is_active_export') : app_lang('is_not_active_export')) . '</span>';

                $row[] = $_data;

                $view = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/stage_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $edit = '';
                $edit .= '<li role="presentation"><a href="#" onclick="edit_stage('.$aRow['id'].'); return false;" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_stage/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or edit stage
     * @return json
     */
    public function stage(){
        $data = $this->request->getPost();

        $message = '';
        
        if($data['id'] == ''){
            $success = $this->Ma_model->add_stage($data);
            if($success){
                $message = _l('added_successfully', _l('stage'));
            }
        }else{
            $id = $data['id'];
            unset($data['id']);
            $success = $this->Ma_model->update_stage($data, $id);
            if ($success) {
                $message = _l('updated_successfully', _l('stage'));
            }
        }

        app_redirect('ma/stages');
    }

    /**
     * delete stage
     * @param  integer $id
     * @return
     */
    public function delete_stage($id)
    {
        $success = $this->Ma_model->delete_stage($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('stage')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/stages'));
    }

    /**
     * get data stage
     * @param  integer $id 
     * @return json     
     */
    public function get_data_stage($id){
        $stage = $this->Ma_model->get_stage($id);

        echo json_encode($stage);
    }

    /**
     * stage management
     * @return view
     */
    public function stages(){
        $data['title'] = _l('stages');
        $data['group'] = $this->request->getGet('group');

        if($data['group'] == ''){
            $data['group'] = 'list';
        }

        if ($data['group'] == 'chart') {
            $data['data_stage_pie'] = $this->Ma_model->get_data_stage_pie_chart($data);
            $data['data_stage_column'] = $this->Ma_model->get_data_stage_column_chart($data);
        }

        $data['categories'] = $this->Ma_model->get_category('', 'stage');
        
        $data['view'] = 'Ma\Views\stages/includes/' . $data['group'];

        
        return $this->template->rander('Ma\Views\stages/manage', $data);
    }

    /**
     * segment table
     * @return json
     */
    public function segment_table(){
            $select = [
                'name',
                'id',
                'category',
                'published',
                'description',
            ];

            $where = [];

            // Filter by custom groups
            $categorys   = $this->Ma_model->get_category('', 'segment');

            $categoryIds = [];
            $category_names = [];
            foreach ($categorys as $category) {
                if ($this->request->getPost('segment_category_' . $category['id'])) {
                    array_push($categoryIds, $category['id']);
                }
                $category_names[$category['id']] = $category['name'];
            }

            if (count($categoryIds) > 0) {
                array_push($where, 'AND category IN (' . implode(', ', $categoryIds) . ')');
            }


            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_segments';
            $join         = [
        ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['color']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<span style="color: '. $aRow['color'] .'">'.$aRow['name'].'</span>';
                $row[] = $categoryOutput;
                $row[] = count($this->Ma_model->get_lead_by_segment($aRow['id']));

                if(isset($category_names[$aRow['category']])){
                    $row[] = $category_names[$aRow['category']];

                }else{
                    $row[] = '';
                }

                $checked = '';
                if ($aRow['published'] == 1) {
                    $checked = 'checked';
                }
                $_data = '<div class=" form-check form-switch onoffswitch">
                <input type="checkbox" class="form-check-input " data-switch-url="' . site_url('ma/change_segment_published').'" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                <label class="form-check-label onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                </div>';

                $_data .= '<span class="hide">' . ($checked == 'checked' ? app_lang('is_active_export') : app_lang('is_not_active_export')) . '</span>';

                $row[] = $_data;

                $view = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/segment_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $edit = '';
                $edit .= '<li role="presentation"><a href="' . get_uri('ma/segment/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_segment/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or update segment
     * @return view
     */
    public function segment($id = ''){
        if ($this->request->getPost()) {
            $data                = $this->request->getPost();
            if($id == ''){
                $success = $this->Ma_model->add_segment($data);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('segment')));
                }

                app_redirect(('ma/segment_detail/' . $success));
            }else{
                $success = $this->Ma_model->update_segment($data, $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('segment')));
                }

                app_redirect(('ma/segment_detail/' . $id));
            }
        }

        if($id != ''){
            $data['segment'] = $this->Ma_model->get_segment($id);
        }

        $data['categories'] = $this->Ma_model->get_category('', 'segment');

        $data['title'] = _l('segment');

        return $this->template->rander('Ma\Views\segments/segment', $data);
    }

    /**
     * delete segment
     * @param  integer $id
     * @return
     */
    public function delete_segment($id)
    {
        
        $success = $this->Ma_model->delete_segment($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('segment')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/segments'));
    }

    /**
     * component
     * @return view
     */
    public function components()
    {
        
        $data          = [];
        $data['group'] = $this->request->getGet('group');

        $data['tab'][] = 'assets';
        $data['tab'][] = 'forms';
        
        if ($data['group'] == '') {
            $data['group'] = 'assets';
        }

        if ($data['group'] == 'assets') {
            $data['categories'] = $this->Ma_model->get_category('', 'asset');
        }else{
            $data['categories'] = $this->Ma_model->get_category('', 'form');
        }

        $data['title']        = _l($data['group']);
        $data['tabs']['view'] = 'Ma\Views\components/' . $data['group'];

        return $this->template->rander('Ma\Views\components/manage', $data);
    }

    /**
     * add or edit form
     * @param  integer
     * @return view
     */
    public function form($id = '')
    {
        if ($this->request->getPost()) {
            if ($id == '') {
                $data = $this->request->getPost();
                $id   = $this->Ma_model->add_form($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('web_to_lead_form')));
                    app_redirect(('ma/form/' . $id));
                }
            } else {
                $success = $this->Ma_model->update_form($id, $this->request->getPost());
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('web_to_lead_form')));
                }
                app_redirect(('ma/form/' . $id));
            }
        }

        $data['formData'] = [];
        $data['title'] = _l('web_to_lead');

        if ($id != '') {
            $data['form'] = $this->Ma_model->get_form([
                'id' => $id,
            ]);
            $data['title']    = $data['form']->name . ' - ' . _l('web_to_lead_form');
            $data['formData'] = $data['form']->form_data;
        }

        $data['statuses'] = $this->Lead_status_model->get_details()->getResult();
        $data['sources'] = $this->Lead_source_model->get_details()->getResult();
        $data['roles'] = $this->Roles_model->get_details()->getResultArray();
        $data['members'] = $this->Users_model->get_all_where(array("deleted" => 0, "user_type" => "staff", "status" => "active"))->getResultArray();

        $data['languages'] = get_language_list();

        $db_fields = [];
        $fields    = [
            'company_name',
            'email',
            'phone',
            'address',
            'city',
            'state',
            'country',
            'zip',
            'website',
        ];

        $className = 'form-control';

        foreach ($fields as $f) {
            $_field_object = (object)[];
            
            $type          = 'text';
            $subtype       = '';
            if ($f == 'email') {
                $subtype = 'email';
            } elseif ($f == 'description' || $f == 'address') {
                $type = 'textarea';
            } elseif ($f == 'country') {
                $type = 'select';
            }

            if ($f == 'company_name') {
                $label = _l('name');
            }  else {
                $label = _l($f);
            }

            $field_array = [
                'subtype'   => $subtype,
                'type'      => $type,
                'label'     => $label,
                'className' => $className,
                'name'      => $f,
            ];

            if ($f == 'company_name') {
                $field_array['required'] = true;
            }

            $_field_object->label    = $label;
            $_field_object->name     = $f;
            $_field_object->fields   = [];
            $_field_object->fields[] = $field_array;
            $db_fields[]             = $_field_object;
        }
        $data['bodyclass'] = 'web-to-lead-form';
        $data['db_fields'] = $db_fields;
        return $this->template->rander('Ma\Views\components/forms/formbuilder', $data);
    }

    /**
     * save form data
     * @return json
     */
    public function save_form_data()
    {
        $data = $this->request->getPost();

        // form data should be always sent to the request and never should be empty
        // this code is added to prevent losing the old form in case any errors
        if (!isset($data['formData']) || isset($data['formData']) && !$data['formData']) {
            echo json_encode([
                'success' => false,
            ]);
            die;
        }

        // If user paste with styling eq from some editor word and the Codeigniter XSS feature remove and apply xss=remove, may break the json.
        $data['formData'] = preg_replace('/=\\\\/m', "=''", $data['formData']);

        $db = db_connect('default');
        $db_builder = $db->table(db_prefix() . 'ma_forms');
        $db_builder->where('id', $data['id']);
        $db_builder->update([
            'form_data' => $data['formData'],
        ]);

        if ($db->affectedRows() > 0) {
            echo json_encode([
                'success' => true,
                'message' => sprintf(_l('updated_successfully'), _l('web_to_lead_form')),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    /**
     * form table
     * @return json
     */
    public function form_table(){
            $aColumns = ['id', 'name', '(SELECT COUNT(id) FROM '.get_db_prefix().'clients WHERE '.get_db_prefix().'clients.from_ma_form_id = '.get_db_prefix().'ma_forms.id)', 'dateadded', 'language'];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_forms';

            $where = [];
            
            if ($this->request->getPost('category')) {
                $category = $this->request->getPost('category');
                array_push($where, 'AND category IN (' . implode(', ', $category) . ')');
            }

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], $where, ['form_key', 'id']);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0 ; $i < count($aColumns) ; $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . get_uri('ma/form/' . $aRow['id']) . '">' . $_data . '</a>';
                    } elseif ($aColumns[$i] == 'dateadded') {
                        $_data = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($_data) . '">' . _d($_data) . '</span>';
                    }elseif ($aColumns[$i] == 'language') {
                        $view = '';
                        $edit = '';
                        $delete = '';
                       
                        $view = '<li role="presentation"><a href="' . get_uri('ma_forms/wtl/' . $aRow['form_key']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                        
                        $edit = '';
                        $edit .= '<li role="presentation"><a href="' . get_uri('ma/form/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                        $delete = '';
                        $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_form/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                        $_data = '
                        <span class="dropdown inline-block">
                        <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                        <i data-feather="tool" class="icon-16"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                        </span>';
                    }

                    $row[] = $_data;


                }
                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or edit asset
     *
     * @param      string  $id     The identifier
     */
    public function asset($id = ''){
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            
            if ($id == '') {
                $target_path = get_setting("ma_asset_file_path");
                $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "ma_asset");

                $data["files"] = $files_data;

                $id = $this->Ma_model->add_asset($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('asset')));
                    app_redirect(('ma/asset_detail/'.$id));
                }

                app_redirect(('ma/components?group=assets'));
            }
            
            $success = $this->Ma_model->update_asset($data, $id);
            if ($success) {
                $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('asset')));
            }
            app_redirect(('ma/asset_detail/'.$id));
        }

        if ($id == '') {
            $title = sprintf(_l('add_new'), _l('asset'));
        } else {
            $data['asset'] = $this->Ma_model->get_asset($id);

            if (!$data['asset']) {
                blank_page(_l('asset_not_found'));
            }

            $title = sprintf(_l('edit'), _l('asset'));
        }

        $data['category'] = $this->Ma_model->get_category('', 'asset');
        $data['upload_url'] = get_uri("projects/upload_file");
        $data['validation_url'] = get_uri("projects/validate_project_file");

        $data['title']      = $title;
        return $this->template->rander('Ma\Views\components/assets/asset', $data);
    }

    /**
     * add asset attachment
     * @param integer
     * @return json
     */
    public function add_asset_attachment($id)
    {
        ma_handle_asset_attachments($id);
        echo json_encode([
            'url' => get_uri('ma/asset_detail/' . $id),
        ]);
    }

    /**
     * delete asset
     * @param  integer $id
     * @return
     */
    public function delete_asset($id)
    {
        $success = $this->Ma_model->delete_asset($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('asset')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/components?group=assets'));
    }

    /**
     * asset table
     * @return json
     */
    public function asset_table(){
            $aColumns = [
                get_db_prefix().'ma_assets.id as id', 
                get_db_prefix().'ma_assets.name as name', 
                get_db_prefix().'ma_categories.name as category_name', 
                get_db_prefix().'ma_assets.dateadded as dateadded',
                get_db_prefix().'ma_assets.description as description',
            ];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_assets';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'ma_categories ON ' . get_db_prefix() . 'ma_categories.id = ' . get_db_prefix() . 'ma_assets.category'
            ];

            $where = [];

            if ($this->request->getPost('category')) {
                $category = $this->request->getPost('category');
                array_push($where, 'AND category IN (' . implode(', ', $category) . ')');
            }


            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['id'];

                $_data = $aRow['name'];
                $_data = '<a href="' . get_uri('ma/asset_detail/' . $aRow['id']) . '">' . $_data . '</a>';

                $row[] = $_data;
                $row[] = $aRow['category_name'];

                $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($aRow['dateadded']) . '">' . _d($aRow['dateadded']) . '</span>';

                $view = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/asset_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $edit = '';
                $edit .= '<li role="presentation"><a href="' . get_uri('ma/asset/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_asset/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * point
     * @return view
     */
    public function points()
    {
        $data          = [];
        $data['group'] = $this->request->getGet('group');

        $data['tab'][] = 'point_actions';
        
        if ($data['group'] == '') {
            $data['group'] = 'point_actions';
        }

        if ($data['group'] == 'point_actions') {
            $data['categories'] = $this->Ma_model->get_category('', 'point_action');
        }else{
            $data['categories'] = $this->Ma_model->get_category('', 'point_trigger');
        }
        
        $data['title']        = _l($data['group']);
        $data['tabs']['view'] = 'points/' . $data['group'];

        return $this->template->rander('Ma\Views\points/point_actions', $data);
    }

    /**
     * add or edit point action
     * @param  integer
     * @return view
     */
    public function point_action($id = '')
    {
        if ($this->request->getPost()) {
            if ($id == '') {
                $data = $this->request->getPost();
                $id   = $this->Ma_model->add_point_action($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('point_action')));
                    app_redirect(('ma/point_action_detail/' . $id));
                }
            } else {
                $success = $this->Ma_model->update_point_action($this->request->getPost(), $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('point_action')));
                }
                app_redirect(('ma/point_action_detail/' . $id));
            }
        }

        if ($id != '') {
            $data['point_action'] = $this->Ma_model->get_point_action($id);
        }
        $data['title']    = _l('point_action');
        $data['bodyclass'] = 'point-action';
        $data['category'] = $this->Ma_model->get_category('', 'point_action');

        return $this->template->rander('Ma\Views\points/point_actions/point_action', $data);
    }

    /**
     * add or edit point trigger
     * @param  string
     * @return view
     */
    public function point_trigger($id = '')
    {
        if ($this->request->getPost()) {
            if ($id == '') {
                $data = $this->request->getPost();
                $id   = $this->Ma_model->add_point_trigger($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('point_trigger')));
                    app_redirect(('ma/point_trigger/' . $id));
                }
            } else {
                $success = $this->Ma_model->update_point_trigger($this->request->getPost(), $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('point_trigger')));
                }
                app_redirect(('ma/point_trigger/' . $id));
            }
        }

        if ($id != '') {
            $data['point_trigger'] = $this->Ma_model->get_point_trigger($id);
        }
        $data['title']    = _l('point_trigger');
        $data['bodyclass'] = 'point-trigger';
        $data['category'] = $this->Ma_model->get_category('', 'point_trigger');

        return $this->template->rander('Ma\Views\points/point_triggers/point_trigger', $data);
    }

    /**
     * point_action table
     * @return json
     */
    public function point_actions_table(){
            $aColumns = [
                get_db_prefix().'ma_point_actions.id as id', 
                get_db_prefix().'ma_point_actions.name as name',
                 get_db_prefix().'ma_categories.name as category_name', 
                get_db_prefix().'ma_point_actions.dateadded as dateadded',
                get_db_prefix().'ma_point_actions.description as description',
            ];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_point_actions';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'ma_categories ON ' . get_db_prefix() . 'ma_categories.id = ' . get_db_prefix() . 'ma_point_actions.category'
            ];
            $where = [];
            if ($this->request->getPost('category')) {
                $category = $this->request->getPost('category');
                array_push($where, 'AND category IN (' . implode(', ', $category) . ')');
            }


            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['id'];

                $_data = $aRow['name'];
                $_data = '<a href="' . get_uri('ma/point_action/' . $aRow['id']) . '">' . $_data . '</a>';

                $row[] = $_data;
                $row[] = $aRow['category_name'];

                $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($aRow['dateadded']) . '">' . _d($aRow['dateadded']) . '</span>';


                $view = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/point_action_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $edit = '';
                $edit .= '<li role="presentation"><a href="' . get_uri('ma/point_action/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_point_action/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * point_trigger table
     * @return json
     */
    public function point_triggers_table(){
            $aColumns = [
                get_db_prefix().'ma_point_triggers.id as id', 
                get_db_prefix().'ma_point_triggers.name as name', 
                get_db_prefix().'ma_categories.name as category_name', 
                get_db_prefix().'ma_point_triggers.dateadded as dateadded'];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_point_triggers';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'ma_categories ON ' . get_db_prefix() . 'ma_categories.id = ' . get_db_prefix() . 'ma_point_triggers.category'
            ];


            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['id'];

                $_data = $aRow['name'];
                $_data = '<a href="' . get_uri('ma/point_trigger/' . $aRow['id']) . '">' . $_data . '</a>';
                $_data .= '<div class="row-options">';
                $_data .= '<a href="' . get_uri('ma/point_trigger/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                $_data .= ' | <a href="' . get_uri('ma/delete_point_trigger/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                $_data .= '</div>';
               

                $row[] = $_data;
                $row[] = $aRow['category_name'];

                $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($aRow['dateadded']) . '">' . _d($aRow['dateadded']) . '</span>';

                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * delete point_action
     * @param  integer $id
     * @return
     */
    public function delete_point_action($id)
    {
        
        $success = $this->Ma_model->delete_point_action($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('point_action')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/points?group=point_actions'));
    }

    /**
     * delete point_trigger
     * @param  integer $id
     * @return
     */
    public function delete_point_trigger($id)
    {

        $success = $this->Ma_model->delete_point_trigger($id);
        $message = '';
        if ($success) {
            $message = _l('deleted', _l('point_trigger'));
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('point_trigger')));
        } else {
            $message = _l('can_not_delete');
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/points?group=point_triggers'));
    }

    /**
     * channel
     * @return view
     */
    public function channels()
    {
        
        $data          = [];
        $data['group'] = $this->request->getGet('group');

        $data['tab'][] = 'emails';
        
        if ($data['group'] == '') {
            $data['group'] = 'emails';
        }

        if ($data['group'] == 'emails') {
            $data['categories'] = $this->Ma_model->get_category('', 'email');
        }else{
            $data['categories'] = $this->Ma_model->get_category('', 'sms');
        }

        $data['title']        = _l($data['group']);
        $data['tabs']['view'] = 'Ma\Views\channels/' . $data['group'];

        return $this->template->rander('Ma\Views\channels/manage', $data);
    }

    /**
     * add or edit marketing message
     * @param  integer
     * @return view
     */
    public function marketing_message($id = '')
    {
        if ($this->request->getPost()) {
            if ($id == '') {
                $data = $this->request->getPost();
                $id   = $this->Ma_model->add_marketing_message($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('marketing_message')));
                    app_redirect(('ma/marketing_message/' . $id));
                }
            } else {
                $success = $this->Ma_model->update_marketing_message($this->request->getPost(), $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('marketing_message')));
                }
                app_redirect(('ma/marketing_message/' . $id));
            }
        }

        if ($id != '') {
            $data['marketing_message'] = $this->Ma_model->get_marketing_message($id);
        }
        $data['title']    = _l('marketing_message');
        $data['bodyclass'] = 'point-trigger';
        $data['category'] = $this->Ma_model->get_category('', 'marketing_message');
        $data['email_templates'] = [];

        return $this->template->rander('Ma\Views\channels/marketing_messages/marketing_message', $data);
    }

    /**
     * delete marketing_message
     * @param  integer $id
     * @return
     */
    public function delete_marketing_message($id)
    {

        $success = $this->Ma_model->delete_marketing_message($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('marketing_message')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/channels?group=marketing_messages'));
    }

    /**
     * add or edit email
     * @param  integer
     * @return view
     */
    public function email($id = '')
    {
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            if ($id == '') {
                $id   = $this->Ma_model->add_email($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('email')));
                    app_redirect(('ma/email_detail/' . $id));
                }
            } else {
                $success = $this->Ma_model->update_email($data, $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('email')));
                }
                app_redirect(('ma/email_detail/' . $id));
            }
        }

        if ($id != '') {
            $data['email'] = $this->Ma_model->get_email($id);
        }
        $data['title']    = _l('email');
        $data['bodyclass'] = 'point-trigger';
        $data['category'] = $this->Ma_model->get_category('', 'email');
        $data['segments'] = $this->Ma_model->get_segment();
        $data['email_templates'] = $this->Ma_model->get_email_template();
        $data['assets'] = $this->Ma_model->get_asset();
        $data['languages'] = get_language_list();

        return $this->template->rander('Ma\Views\channels/emails/email', $data);
    }

    /**
     * delete email
     * @param  integer $id
     * @return
     */
    public function delete_email($id)
    {

        $success = $this->Ma_model->delete_email($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('email')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/channels?group=emails'));
    }

    /**
     * marketing_message table
     * @return json
     */
    public function marketing_messages_table(){
            $aColumns = [
                get_db_prefix().'ma_marketing_messages.id as id', 
                get_db_prefix().'ma_marketing_messages.name as name', 
                get_db_prefix().'ma_categories.name as category_name', 
                get_db_prefix().'ma_marketing_messages.dateadded as dateadded'];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_marketing_messages';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'ma_categories ON ' . get_db_prefix() . 'ma_categories.id = ' . get_db_prefix() . 'ma_marketing_messages.category'
            ];


            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['id'];

                $_data = $aRow['name'];
                $_data = '<a href="' . get_uri('ma/marketing_message/' . $aRow['id']) . '">' . $_data . '</a>';
                $_data .= '<div class="row-options">';
                $_data .= '<a href="' . get_uri('ma/marketing_message/' . $aRow['id']) . '">' . _l('edit') . '</a>';
                $_data .= ' | <a href="' . get_uri('ma/delete_marketing_message/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
                $_data .= '</div>';
               

                $row[] = $_data;
                $row[] = $aRow['category_name'];

                $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($aRow['dateadded']) . '">' . _d($aRow['dateadded']) . '</span>';

                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * email table
     * @return json
     */
    public function email_table(){
            $aColumns = [
                get_db_prefix().'ma_emails.id as id', 
                get_db_prefix().'ma_emails.name as name', 
                get_db_prefix().'ma_categories.name as category_name', 
                get_db_prefix().'ma_emails.dateadded as dateadded',
                get_db_prefix().'ma_emails.description as description',
            ];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_emails';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'ma_categories ON ' . get_db_prefix() . 'ma_categories.id = ' . get_db_prefix() . 'ma_emails.category'
            ];

            $where = [];

            if ($this->request->getPost('category')) {
                $category = $this->request->getPost('category');
                array_push($where, 'AND category IN (' . implode(', ', $category) . ')');
            }


            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['id'];

                $_data = $aRow['name'];
                $_data = '<a href="' . get_uri('ma/email_detail/' . $aRow['id']) . '">' . $_data . '</a>';

                $row[] = $_data;
                $row[] = $aRow['category_name'];

                $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($aRow['dateadded']) . '">' . _d($aRow['dateadded']) . '</span>';

                $view = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/email_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $edit = '';
                $edit .= '<li role="presentation"><a href="' . get_uri('ma/email/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_email/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * text_message table
     * @return json
     */
    public function text_messages_table(){
            $aColumns = [
                get_db_prefix().'ma_text_messages.id as id', 
                get_db_prefix().'ma_text_messages.name as name', 
                get_db_prefix().'ma_categories.name as category_name', 
                get_db_prefix().'ma_text_messages.dateadded as dateadded',
                get_db_prefix().'ma_text_messages.description as description'
            ];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_text_messages';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'ma_categories ON ' . get_db_prefix() . 'ma_categories.id = ' . get_db_prefix() . 'ma_text_messages.category'
            ];


            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['id'];

                $_data = $aRow['name'];
                $_data = '<a href="' . get_uri('ma/text_message_detail/' . $aRow['id']) . '">' . $_data . '</a>';

                $row[] = $_data;
                $row[] = $aRow['category_name'];

                $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($aRow['dateadded']) . '">' . _d($aRow['dateadded']) . '</span>';

                $view = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/text_message_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $edit = '';
                $edit .= '<li role="presentation"><a href="' . get_uri('ma/text_message/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_text_message/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or edit text message
     * @param  integer
     * @return view
     */
    public function text_message($id = '')
    {
        if ($this->request->getPost()) {
            if ($id == '') {
                $data = $this->request->getPost();
                $id   = $this->Ma_model->add_text_message($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('text_message')));
                    app_redirect(('ma/text_message_detail/' . $id));
                }
            } else {
                $success = $this->Ma_model->update_text_message($this->request->getPost(), $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('text_message')));
                }
                app_redirect(('ma/text_message_detail/' . $id));
            }
        }

        if ($id != '') {
            $data['text_message'] = $this->Ma_model->get_text_message($id);
        }

        $data['title']    = _l('text_message');
        $data['bodyclass'] = 'point-trigger';
        $data['category'] = $this->Ma_model->get_category('', 'text_message');
        $data['segments'] = $this->Ma_model->get_segment();
        $data['languages'] = get_language_list();
        $data['available_merge_fields'] = array("COMPANY_NAME", "LOGO_URL", "LEAD_NAME", "LEAD_PHONE", "LEAD_WEBSITE", "LEAD_ADDRESS", "LEAD_CITY", "LEAD_STATE", "LEAD_ZIP", "LEAD_COUNTRY", "CLIENT_NAME", "CLIENT_PHONE", "CLIENT_WEBSITE", "CLIENT_ADDRESS", "CLIENT_CITY", "CLIENT_STATE", "CLIENT_ZIP", "CLIENT_COUNTRY");

        return $this->template->rander('Ma\Views\settings/text_messages/text_message', $data);
    }

    /**
     * delete text_message
     * @param  integer $id
     * @return
     */
    public function delete_text_message($id)
    {
        $success = $this->Ma_model->delete_text_message($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('text_message')));
        } else {
            $this->session->setFlashdata("success_message", _l('can_not_delete'));
        }

        app_redirect(('ma/setting?group=text_messages'));
    }

    /**
     * campaign management
     * @return view
     */
    public function campaigns(){
        $data['title'] = _l('campaigns');

        $data['group'] = $this->request->getGet('group');

        if($data['group'] == ''){
            $data['group'] = 'list';
        }

        if ($data['group'] == 'chart') {
            $data['data_campaign_pie'] = $this->Ma_model->get_data_campaign_pie_chart($data);
            $data['data_campaign_column'] = $this->Ma_model->get_data_campaign_column_chart($data);
        }

        $data['categories'] = $this->Ma_model->get_category('', 'campaign');
        
        $data['view'] = 'Ma\Views\campaigns/includes/' . $data['group'];

        
        return $this->template->rander('Ma\Views\campaigns/manage', $data);
    }

    /**
     * campaign table
     * @return json
     */
    public function campaign_table(){
            $select = [
                'name',
                'category',
                'published',
                'id',
            ];

            $where = [];

            // Filter by custom groups
            $categorys   = $this->Ma_model->get_category('', 'campaign');

            $categoryIds = [];
            $category_names = [];
            foreach ($categorys as $category) {
                if ($this->request->getPost('campaign_category_' . $category['id'])) {
                    array_push($categoryIds, $category['id']);
                }
                $category_names[$category['id']] = $category['name'];
            }

            if (count($categoryIds) > 0) {
                array_push($where, 'AND category IN (' . implode(', ', $categoryIds) . ')');
            }

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_campaigns';
            $join         = [
        ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'color']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];
                $categoryOutput = '<span style="color: '. $aRow['color'] .'">'.$aRow['name'].'</span>';

               
                $row[] = $categoryOutput;
                $row[] = ma_get_category_name($aRow['category']);

                $checked = '';
                if ($aRow['published'] == 1) {
                    $checked = 'checked';
                }
                $_data = '<div class=" form-check form-switch onoffswitch">
                <input type="checkbox" class="form-check-input " data-switch-url="' . site_url('ma/change_campaign_published').'" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
                <label class="form-check-label onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
                </div>';

                $_data .= '<span class="hide">' . ($checked == 'checked' ? app_lang('is_active_export') : app_lang('is_not_active_export')) . '</span>';

                $row[] = $_data;

                $view = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/campaign_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $edit = '';
                $edit .= '<li role="presentation"><a href="' . get_uri('ma/campaign/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_campaign/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * add or update campaign
     * @return view
     */
    public function campaign($id = ''){
        if ($this->request->getPost()) {
            $data                = $this->request->getPost();

            if($id == ''){
               
                $success = $this->Ma_model->add_campaign($data);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('campaign')));
                }

                app_redirect(('ma/campaign_detail/' . $success));
            }else{
               
                $success = $this->Ma_model->update_campaign($data, $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('campaign')));
                }

                app_redirect(('ma/campaign_detail/' . $id));
            }
        }

        if($id != ''){
            $data['campaign'] = $this->Ma_model->get_campaign($id);
        }

        $data['categories'] = $this->Ma_model->get_category('', 'campaign');

        $data['title'] = _l('campaign');

        return $this->template->rander('Ma\Views\campaigns/campaign', $data);
    }

    /**
     * add or edit email template
     * @param  integer
     * @return view
     */
    public function email_template($id = '')
    {
        if ($this->request->getPost()) {
            if ($id == '') {
                $data = $this->request->getPost();

                $id   = $this->Ma_model->add_email_template($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('email_template')));

                    app_redirect(('ma/email_template_detail/' . $id));
                }
            } else {
                $success = $this->Ma_model->update_email_template($this->request->getPost(), $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('email_template')));
                }
                app_redirect(('ma/email_template_detail/' . $id));
            }
        }

        if ($id != '') {
            $data['email_template'] = $this->Ma_model->get_email_template($id);
        }
        $data['title']    = _l('email_template');
        $data['bodyclass'] = 'point-trigger';
        $data['category'] = $this->Ma_model->get_category('', 'email_template');
        $data['segments'] = $this->Ma_model->get_segment();
        $data['languages'] = get_language_list();

        return $this->template->rander('Ma\Views\settings/email_templates/email_template', $data);
    }

    /**
     * view segment
     * @return view
     */
    public function segment_detail($id){
        $data['segment'] = $this->Ma_model->get_segment($id);
        $data['lead_by_segment'] = $this->Ma_model->get_lead_by_segment($id);
        $data['campaign_by_segment'] = $this->Ma_model->get_campaign_by_segment($id);
        
        $data['title'] = _l('segment');

        return $this->template->rander('Ma\Views\segments/segment_detail', $data);
    }

    /**
     * view stage
     * @return view
     */
    public function stage_detail($id){
        $data['stage'] = $this->Ma_model->get_stage($id);
        $data['lead_by_stage'] = $this->Ma_model->get_lead_by_stage($id);
        $data['campaign_by_stage'] = $this->Ma_model->get_campaign_by_stage($id);

        $data['title'] = _l('stage');

        return $this->template->rander('Ma\Views\stages/stage_detail', $data);
    }

    /**
     * change segment published
     * @param  integer
     * @param  string
     */
    public function change_segment_published($id, $status)
    {
        $this->Ma_model->change_segment_published($id, $status);
    }

    /**
     * Gets the data segment chart.
     * @return json data chart
     */
    public function get_data_segment_chart() {
        $data_segment_pie = $this->Ma_model->get_data_segment_pie_chart();
        $data_segment_column = $this->Ma_model->get_data_segment_column_chart();
        echo json_encode([
            'data_segment_pie' => $data_segment_pie,
            'data_segment_column' => $data_segment_column
        ]);
        die();
    }

    /**
     * segment kanban
     */
    public function segment_kanban()
    {
        $categories   = $this->Ma_model->get_category('', 'segment');

        $categoryIds = [];
        $category_names = [];
        foreach ($categories as $category) {
            if ($this->request->getGet('segment_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_return = [];
        foreach ($categories as $key => $category) {
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }
            $node = $category;
            $node['segments'] = $this->Ma_model->do_segment_kanban_query($category['id'], 1, 'category='.$category['id']);
            $node['total_pages'] = ceil($this->Ma_model->do_segment_kanban_query($category['id'], 1, 'category='.$category['id'], true)/10);

            $data_return[] = $node;
        }
        return $this->template->view('Ma\Views\segments/includes/segment_kanban', ['data' => $data_return]);
    }

    /**
     * update segment category
     */
    public function update_segment_category()
    {
        if ($this->request->getPost()) {
            $this->Ma_model->update_segment_category($this->request->getPost());
        }
    }

    /**
     * segment kanban load more }
     */
    public function segment_kanban_load_more()
    {
        $category     = $this->request->getGet('category');
        $page       = $this->request->getGet('page');
        $from_date = '';
        $to_date = '';
       
        $segments = $this->Ma_model->do_segment_kanban_query($category, $page);
        foreach ($segments as $segment) {
            return $this->template->rander('Ma\Views\ma/segments/includes/_segment_kanban_card', ['segment' => $segment, 'category' => $category]);
        }
    }

    /**
     * view campaign
     * @return view
     */
    public function campaign_detail($id){
        $this->Ma_model->get_lead_by_campaign($id);
        $data['campaign'] = $this->Ma_model->get_campaign($id);
        $data['point_actions'] = $this->Ma_model->get_object_by_campaign($id, 'point_action', 'object');
        $data['emails'] = $this->Ma_model->get_object_by_campaign($id, 'email', 'object');
        $data['sms'] = $this->Ma_model->get_object_by_campaign($id, 'sms', 'object');

        $data['stages'] = $this->Ma_model->get_object_by_campaign($id, 'stage', 'object');
        $data['segments'] = $this->Ma_model->get_object_by_campaign($id, 'segment', 'object');

        $data['title'] = _l('campaign');

        return $this->template->rander('Ma\Views\campaigns/campaign_detail', $data);
    }

    /**
     * workflow builder
     * @return view
     */
    public function workflow_builder($id){
        $data['campaign'] = $this->Ma_model->get_campaign($id);

        $data['title'] = _l('workflow_builder');

        $data['is_edit'] = true;

        return $this->template->rander('Ma\Views\campaigns/workflow_builder', $data);
    }

    /**
     * workflow builder save
     * @return redirect
     */
    public function workflow_builder_save(){
        $data = $this->request->getPost();
        $success = $this->Ma_model->workflow_builder_save($data);
        if($success){
            $message = _l('updated_successfully', _l('workflow'));
        }

        app_redirect(('ma/campaign_detail/' . $data['campaign_id']));
    }

    /**
     * change campaign published
     * @param  integer
     * @param  string
     */
    public function change_campaign_published($id, $status)
    {
        $this->Ma_model->change_campaign_published($id, $status);
    }
    
    /**
     * Gets the data campaign chart.
     * @return json data chart
     */
    public function get_data_campaign_chart() {
        $data_campaign_pie = $this->Ma_model->get_data_campaign_pie_chart();
        $data_campaign_column = $this->Ma_model->get_data_campaign_column_chart();
        echo json_encode([
            'data_campaign_pie' => $data_campaign_pie,
            'data_campaign_column' => $data_campaign_column
        ]);
        die();
    }

    /**
     * campaign kanban
     */
    public function campaign_kanban()
    {
        $categories   = $this->Ma_model->get_category('', 'campaign');

        $categoryIds = [];
        $category_names = [];
        foreach ($categories as $category) {
            if ($this->request->getGet('campaign_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_return = [];
        foreach ($categories as $key => $category) {
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }
            $node = $category;
            $node['campaigns'] = $this->Ma_model->do_campaign_kanban_query($category['id'], 1, 'category='.$category['id']);
            $node['total_pages'] = ceil($this->Ma_model->do_campaign_kanban_query($category['id'], 1, 'category='.$category['id'], true)/10);

            $data_return[] = $node;
        }

        return $this->template->view('Ma\Views\campaigns/includes/campaign_kanban', ['data' => $data_return]);
    }

    /**
     * update campaign category
     */
    public function update_campaign_category()
    {
        if ($this->request->getPost()) {
            $this->Ma_model->update_campaign_category($this->request->getPost());
        }
    }

    /**
     * campaign kanban load more }
     */
    public function campaign_kanban_load_more()
    {
        $category     = $this->request->getGet('category');
        $page       = $this->request->getGet('page');
        $from_date = '';
        $to_date = '';
       
        $campaigns = $this->Ma_model->do_campaign_kanban_query($category, $page);
        foreach ($campaigns as $campaign) {
            return $this->template->rander('Ma\Views\ma/campaigns/includes/_campaign_kanban_card', ['campaign' => $campaign, 'category' => $category]);
        }
    }

    /**
     * change stage published
     * @param  integer
     * @param  string
     */
    public function change_stage_published($id, $status)
    {
        $this->Ma_model->change_stage_published($id, $status);
    }
    
    /**
     * Gets the data stage chart.
     * @return json data chart
     */
    public function get_data_stage_chart() {
        $data_stage_pie = $this->Ma_model->get_data_stage_pie_chart();
        $data_stage_column = $this->Ma_model->get_data_stage_column_chart();
        echo json_encode([
            'data_stage_pie' => $data_stage_pie,
            'data_stage_column' => $data_stage_column
        ]);
        die();
    }

    /**
     * stage kanban
     */
    public function stage_kanban()
    {
        $categories   = $this->Ma_model->get_category('', 'stage');

        $categoryIds = [];
        $category_names = [];
        foreach ($categories as $category) {
            if ($this->request->getGet('stage_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_return = [];
        foreach ($categories as $key => $category) {
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }
            $node = $category;
            $node['stages'] = $this->Ma_model->do_stage_kanban_query($category['id'], 1, 'category='.$category['id']);
            $node['total_pages'] = ceil($this->Ma_model->do_stage_kanban_query($category['id'], 1, 'category='.$category['id'], true)/10);

            $data_return[] = $node;
        }

        return $this->template->view('Ma\Views\stages/includes/stage_kanban', ['data' => $data_return]);
    }

    /**
     * update stage category
     */
    public function update_stage_category()
    {
        if ($this->request->getPost()) {
            $this->Ma_model->update_stage_category($this->request->getPost());
        }
    }

    /**
     * stage kanban load more }
     */
    public function stage_kanban_load_more()
    {
        $category     = $this->request->getGet('category');
        $page       = $this->request->getGet('page');
        $from_date = '';
        $to_date = '';
       
        $stages = $this->Ma_model->do_stage_kanban_query($category, $page);
        foreach ($stages as $stage) {
            return $this->template->rander('Ma\Views\ma/stages/includes/_stage_kanban_card', ['stage' => $stage, 'category' => $category]);
        }
    }

    /**
     * get workflow node html
     * @return view
     */
    public function get_workflow_node_html(){
        $data = $this->request->getPost();

        switch ($data['type']) {
            case 'flow_start':
                $data['segments'] = $this->Ma_model->get_segment('', 'published = 1');
                $data['forms'] = $this->Ma_model->get_forms();
                $data['customer_groups'] = $this->Client_groups_model->get_all()->getResultArray();

                break;
            case 'sms':
                $data['sms'] = $this->Ma_model->get_sms();
                break;
            case 'email':
                $data['emails'] = $this->Ma_model->get_email();
                break;
            case 'action':
                $data['segments'] = $this->Ma_model->get_segment('', 'published = 1');
                $data['stages'] = $this->Ma_model->get_stage();
                $data['point_actions'] = $this->Ma_model->get_point_action();
                break;
            default:
                // code...
                break;
        }


        return view('Ma\Views\campaigns/workflow_node/'.$data['type'], $data);
    }

    /**
     * delete campaign
     * @param  integer $id
     * @return
     */
    public function delete_campaign($id)
    {

        $success = $this->Ma_model->delete_campaign($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('campaign')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/campaigns'));
    }

    /**
     * delete form
     * @param  integer $id
     * @return
     */
    public function delete_form($id)
    {

        $success = $this->Ma_model->delete_form($id);
        $message = '';
        if ($success) {
            $message = _l('deleted', _l('form'));
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('form')));
        } else {
            $message = _l('can_not_delete');
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/components?group=forms'));
    }

    /**
     * email template design save
     * @return redirect
     */
    public function email_template_design_save(){
        $data = $this->request->getPost();

        $success = $this->Ma_model->email_template_design_save($data);
        if($success){
            $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('template')));
        }

        app_redirect(('ma/email_template_detail/' . $data['email_template_id']));
    }

    /**
     * workflow builder
     * @return view
     */
    public function email_template_design($id){
        $data['email_template_design'] = $this->Ma_model->get_email_template_design($id);
        $data['available_merge_fields'] = array("COMPANY_NAME", "LOGO_URL", "LEAD_NAME", "LEAD_PHONE", "LEAD_WEBSITE", "LEAD_ADDRESS", "LEAD_CITY", "LEAD_STATE", "LEAD_ZIP", "LEAD_COUNTRY", "CLIENT_NAME", "CLIENT_PHONE", "CLIENT_WEBSITE", "CLIENT_ADDRESS", "CLIENT_CITY", "CLIENT_STATE", "CLIENT_ZIP", "CLIENT_COUNTRY");

        $data['title'] = _l('email_template');

        $data['is_edit'] = true;

        return $this->template->rander('Ma\Views\settings/email_templates/email_template_design', $data);
    }

    /**
     * view email template
     * @return view
     */
    public function email_template_detail($id){
        $data['email_template'] = $this->Ma_model->get_email_template($id);
        $data['lead_by_email_template'] = $this->Ma_model->get_lead_by_email_template($id);
        $data['campaign_by_email_template'] = $this->Ma_model->get_campaign_by_email_template($id);
        $data['languages'] = get_language_list();

        $data['title'] = _l('email_template');

        return $this->template->rander('Ma\Views\settings/email_templates/email_template_detail', $data);
    }

    /**
     * email templates table
     * @return json
     */
    public function email_templates_table(){
           
            $aColumns = [
                get_db_prefix().'ma_email_templates.id as id', 
                get_db_prefix().'ma_email_templates.name as name', 
                get_db_prefix().'ma_categories.name as category_name', 
                get_db_prefix().'ma_email_templates.dateadded as dateadded'];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_email_templates';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'ma_categories ON ' . get_db_prefix() . 'ma_categories.id = ' . get_db_prefix() . 'ma_email_templates.category'
            ];


            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['id'];

                $_data = $aRow['name'];
                $_data = '<a href="' . get_uri('ma/email_template_detail/' . $aRow['id']) . '">' . $_data . '</a>';

                $row[] = $_data;
                $row[] = $aRow['category_name'];

                $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($aRow['dateadded']) . '">' . _d($aRow['dateadded']) . '</span>';

                $view = '';
                $clone = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/email_template_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $clone = '';
                $clone .= '<li role="presentation"><a href="#" onclick="clone_template(' . $aRow['id'] . '); return false" class="dropdown-item"><span data-feather="copy" class="icon-16"></span> ' . app_lang('clone') . '</a></li>';

                $edit = '';
                $edit .= '<li role="presentation"><a href="' . get_uri('ma/email_template/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_email_template/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view .$clone. $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * delete email template
     * @param  integer $id
     * @return
     */
    public function delete_email_template($id)
    {
        $success = $this->Ma_model->delete_email_template($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('email_template')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/setting?group=ma_email_templates'));
    }

    /**
     * view asset
     * @return view
     */
    public function asset_detail($id){
        $data['asset'] = $this->Ma_model->get_asset($id);
        $data['title'] = _l('asset');

        return $this->template->rander('Ma\Views\components/assets/asset_detail', $data);
    }

    /**
     * Gets the data asset chart.
     * @return json data chart
     */
    public function get_data_asset_chart($asset_id = '') {
        $data_asset_download = $this->Ma_model->get_data_asset_download_chart($asset_id);
        echo json_encode([
            'data_asset_download' => $data_asset_download
        ]);
        die();
    }

    /**
     * Gets the data email template chart.
     * @return json data chart
     */
    public function get_data_email_template_chart($email_template_id = '') {
        $data_email_template = $this->Ma_model->get_data_email_template_chart($email_template_id);

        $data_email_template_by_campaign = [];
        if($email_template_id != ''){
            $data_email_template_by_campaign = $this->Ma_model->get_data_email_template_by_campaign_chart($email_template_id);
        }

        echo json_encode([
            'data_email_template' => $data_email_template,
            'data_email_template_by_campaign' => $data_email_template_by_campaign,
        ]);
        die();
    }

    /**
     * view point action
     * @return view
     */
    public function point_action_detail($id){
        $data['point_action'] = $this->Ma_model->get_point_action($id);
        $data['title'] = _l('point_action');

        return $this->template->rander('Ma\Views\points/point_actions/point_action_detail', $data);
    }

     /**
     * Gets the data point action chart.
     * @return json data chart
     */
    public function get_data_point_action_chart($point_action_id = '') {
        $data_point_action = $this->Ma_model->get_data_point_action_chart($point_action_id);
        $data_point_action_by_campaign = [];
        if($point_action_id != ''){
            $data_point_action_by_campaign = $this->Ma_model->get_data_point_action_by_campaign_chart($point_action_id);
        }
        echo json_encode([
            'data_point_action' => $data_point_action,
            'data_point_action_by_campaign' => $data_point_action_by_campaign,
        ]);
        die();
    }

    /**
     * get data dashboard
     * @return json
     */
    public function get_data_dashboard(){
        $data_filter = $this->request->getGet();

        $data['data_form_submit'] = $this->Ma_model->get_data_form_submit_chart('', $data_filter);
        $data['data_email_template'] = $this->Ma_model->get_data_email_chart('', $data_filter);
        $data['data_lead'] = $this->Ma_model->get_data_lead_chart($data_filter);

        echo json_encode($data);
    }

    /**
     * Gets the data segment chart.
     * @return json data chart
     */
    public function get_data_segment_detail_chart($segment_id) {
        $data_segment_detail = $this->Ma_model->get_data_segment_detail_chart($segment_id);
        $data_segment_campaign_detail = $this->Ma_model->get_data_segment_by_campaign_chart($segment_id);
        echo json_encode([
            'data_segment_detail' => $data_segment_detail,
            'data_segment_campaign_detail' => $data_segment_campaign_detail,
        ]);
        die();
    }

    /**
     * Gets the data stage chart.
     * @return json data chart
     */
    public function get_data_stage_detail_chart($stage_id) {
        $data_stage_detail = $this->Ma_model->get_data_stage_detail_chart($stage_id);
        $data_stage_campaign_detail = $this->Ma_model->get_data_stage_by_campaign_chart($stage_id);
        echo json_encode([
            'data_stage_detail' => $data_stage_detail,
            'data_stage_campaign_detail' => $data_stage_campaign_detail,
        ]);
        die();
    }

    /**
     * Gets the data campaign chart.
     * @return json data chart
     */
    public function get_data_campaign_detail_chart($campaign_id = '') {
        $data_email = $this->Ma_model->get_data_campaign_email_chart($campaign_id);
        $data_segment = $this->Ma_model->get_data_campaign_segment_chart($campaign_id);
        $data_stage = $this->Ma_model->get_data_campaign_stage_chart($campaign_id);
        $data_text_message = $this->Ma_model->get_data_campaign_text_message_chart($campaign_id);
        $data_point_action = $this->Ma_model->get_data_campaign_point_action_chart($campaign_id);
        echo json_encode([
            'data_email' => $data_email,
            'data_segment' => $data_segment,
            'data_stage' => $data_stage,
            'data_text_message' => $data_text_message,
            'data_point_action' => $data_point_action,
        ]);
        die();
    }

    /**
     * view text message
     * @return view
     */
    public function text_message_detail($id){
        $data['text_message'] = $this->Ma_model->get_text_message($id);
        $data['lead_by_text_message'] = $this->Ma_model->get_lead_by_text_message($id);
        $data['campaign_by_text_message'] = $this->Ma_model->get_campaign_by_text_message($id);
        

        $data['title'] = _l('text_message');

        return $this->template->rander('Ma\Views\settings/text_messages/text_message_detail', $data);
    }

    /**
     * Gets the data text message chart.
     * @return json data chart
     */
    public function get_data_text_message_chart($text_message_id = '') {
        $data_text_message = $this->Ma_model->get_data_text_message_chart($text_message_id);

        $data_text_message_by_campaign = [];
        if($text_message_id != ''){
            $data_text_message_by_campaign = $this->Ma_model->get_data_text_message_by_campaign_chart($text_message_id);
        }

        echo json_encode([
            'data_text_message' => $data_text_message,
            'data_text_message_by_campaign' => $data_text_message_by_campaign,
        ]);
        die();
    }

    /**
     * Reports
     * @return 
     */
    public function reports(){
        $data['title'] = _l('reports');
        
        return $this->template->rander('Ma\Views\reports/manage', $data);
    }

    /**
     * report campaign
     * @return view
     */
    public function campaign_report(){
        $data['title'] = _l('campaign_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Ma\Views\reports/includes/campaign_report', $data);
    }

    /**
     * report asset
     * @return view
     */
    public function asset_report(){
        $data['title'] = _l('asset_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Ma\Views\reports/includes/asset_report', $data);
    }

    /**
     * asset download table
     * @return json
     */
    public function asset_download_table()
    {

            $select = [
                get_db_prefix() . 'ma_assets.name as name',
                'ip',
                'browser_name',
                'time',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_asset_download_logs';
            $join         = ['JOIN ' . get_db_prefix() . 'ma_assets ON ' . get_db_prefix() . 'ma_assets.id = ' . get_db_prefix() . 'ma_asset_download_logs.asset_id'];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'ma_assets.id as id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . get_uri('ma/asset_detail/' . $aRow['id']) . '" class="">' . $aRow['name'] . '</a>';
                $row[] = '<span class="text text-success">' . $aRow['ip'] . '</span>';
                $row[] = '<span class="text text-success">' . $aRow['browser_name'] . '</span>';
                $row[] = '<span class="text text-success">' . $aRow['time'] . '</span>';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * report lead and point
     * @return view
     */
    public function lead_and_point_report(){
        $data['title'] = _l('contact_and_point_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Ma\Views\reports/includes/lead_and_point_report', $data);
    }

    /**
     * point action log table
     * @return json
     */
    public function point_action_log_table()
    {

            $select = [
                get_db_prefix() . 'ma_point_actions.id as log_id',
                get_db_prefix() . 'ma_point_actions.name as name',
                get_db_prefix() . 'clients.company_name as contact_name',
                get_db_prefix() . 'custom_field_values.value as lead_email',
                'point',
                get_db_prefix() . 'ma_point_action_logs.dateadded as dateadded',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_point_action_logs';
            $join         = [
                'LEFT JOIN ' . get_db_prefix() . 'ma_point_actions ON ' . get_db_prefix() . 'ma_point_actions.id = ' . get_db_prefix() . 'ma_point_action_logs.point_action_id',
                'LEFT JOIN ' . get_db_prefix() . 'clients ON ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'ma_point_action_logs.lead_id or '.get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'ma_point_action_logs.client_id',
                'LEFT JOIN ' . get_db_prefix() . 'custom_fields ON ' . get_db_prefix() . 'custom_fields.related_to = "leads" AND ' . get_db_prefix() . 'custom_fields.field_type = "email"',
                'LEFT JOIN ' . get_db_prefix() . 'custom_field_values ON ' . get_db_prefix() . 'custom_fields.id = ' . get_db_prefix() . 'custom_field_values.custom_field_id AND ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'custom_field_values.related_to_id AND ' . get_db_prefix() . 'custom_field_values.related_to_type = "leads"',
                'LEFT JOIN ' . db_prefix() . 'users ON ' . db_prefix() . 'users.client_id = ' . db_prefix() . 'clients.id AND is_primary_contact = 1',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'ma_point_actions.id as id', db_prefix() . 'users.email as client_email', 'lead_id', 'IF(lead_id != 0,"lead","client") as type',]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . get_uri('ma/point_action_detail/' . $aRow['id']) . '" class="">' . $aRow['name'] . '</a>';

                if($aRow['lead_id'] != 0){
                    $row[] = _l('lead');
                    $row[] = $aRow['contact_name'];
                    $row[] = $aRow['lead_email'];
                }else{
                    $row[] = _l('client');
                    $row[] = $aRow['contact_name'];
                    $row[] = $aRow['client_email'];
                }

                $text_class = 'text-success';
                if($aRow['point'] < 0){
                    $text_class = 'text-danger';
                }

                $row[] = '<span class="text '.$text_class.'">' . $aRow['point'] . '</span>';

                $row[] = $aRow['dateadded'];
                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * form log table
     * @return json
     */
    public function form_log_table()
    {
            $select = [
                get_db_prefix() . 'ma_forms.name as name',
                get_db_prefix() . 'clients.company_name as lead_name',
                get_db_prefix() . 'custom_field_values.value as email',
                get_db_prefix() . 'clients.created_date as dateadded',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'clients';
            $join         = [
                'JOIN ' . get_db_prefix() . 'ma_forms ON ' . get_db_prefix() . 'ma_forms.id = ' . get_db_prefix() . 'clients.from_ma_form_id',
                'LEFT JOIN ' . get_db_prefix() . 'custom_fields ON ' . get_db_prefix() . 'custom_fields.related_to = "leads" AND ' . get_db_prefix() . 'custom_fields.field_type = "email"',
                'LEFT JOIN ' . get_db_prefix() . 'custom_field_values ON ' . get_db_prefix() . 'custom_fields.id = ' . get_db_prefix() . 'custom_field_values.custom_field_id AND ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'custom_field_values.related_to_id AND ' . get_db_prefix() . 'custom_field_values.related_to_type = "leads"',
                ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'ma_forms.id as id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = '<a href="' . get_uri('ma/form/' . $aRow['id']) . '" class="">' . $aRow['name'] . '</a>';
                $row[] = $aRow['lead_name'];
                $row[] = $aRow['email'];
                
                $row[] = $aRow['dateadded'];
                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * Gets the data form chart.
     * @return json data chart
     */
    public function get_data_form_chart($form_id = '') {
        $data_form = $this->Ma_model->get_data_form_chart($form_id);
       
        echo json_encode([
            'data_form' => $data_form,
        ]);
        die();
    }

    /**
     * report form
     * @return view
     */
    public function form_report(){
        $data['title'] = _l('form_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Ma\Views\reports/includes/form_report', $data);
    }

    /**
     * report email
     * @return view
     */
    public function email_report(){
        $data['title'] = _l('email_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Ma\Views\reports/includes/email_report', $data);
    }

    /**
     * email log table
     * @return json
     */
    public function email_log_table()
    {
            $select = [
                get_db_prefix() . 'ma_email_logs.id as id',
                get_db_prefix() . 'ma_email_logs.dateadded as dateadded',
                get_db_prefix() . 'ma_email_templates.name as name',
                get_db_prefix() . 'clients.company_name as contact_name',
                get_db_prefix() . 'custom_field_values.value as lead_email',
                'delivery',
                'open',
                'click',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_email_logs';
            $join         = [
                'JOIN ' . get_db_prefix() . 'ma_email_templates ON ' . get_db_prefix() . 'ma_email_templates.id = ' . get_db_prefix() . 'ma_email_logs.email_template_id',
                'JOIN ' . get_db_prefix() . 'clients ON ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'ma_email_logs.lead_id or ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'ma_email_logs.client_id',
                'LEFT JOIN ' . get_db_prefix() . 'custom_fields ON ' . get_db_prefix() . 'custom_fields.related_to = "leads" AND ' . get_db_prefix() . 'custom_fields.field_type = "email"',
                'LEFT JOIN ' . get_db_prefix() . 'custom_field_values ON ' . get_db_prefix() . 'custom_fields.id = ' . get_db_prefix() . 'custom_field_values.custom_field_id AND ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'custom_field_values.related_to_id AND ' . get_db_prefix() . 'custom_field_values.related_to_type = "leads"',
                'LEFT JOIN ' . db_prefix() . 'users ON ' . db_prefix() . 'users.client_id = ' . db_prefix() . 'clients.id AND is_primary_contact = 1',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'ma_email_templates.id as id', get_db_prefix() . 'users.email as client_email', 'lead_id', 'IF(lead_id != 0,"lead","client") as type',]);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = $aRow['dateadded'];
                $row[] = '<a href="' . get_uri('ma/email_template_detail/' . $aRow['id']) . '" class="">' . $aRow['name'] . '</a>';

                if($aRow['lead_id'] != 0){
                    $row[] = _l('lead');
                    $row[] = $aRow['contact_name'];
                    $row[] = $aRow['lead_email'];
                }else{
                    $row[] = _l('client');
                    $row[] = $aRow['contact_name'];
                    $row[] = $aRow['client_email'];
                }

                $value = (($aRow['delivery'] == 1) ? _l('yes') : _l('no'));
                $text_class = (($aRow['delivery'] == 1) ? 'text-success' : 'text-danger');
                $row[] = '<span class="text '.$text_class.'">' . $value . '</span>';

                $value = (($aRow['open'] == 1) ? _l('yes') : _l('no'));
                $text_class = (($aRow['open'] == 1) ? 'text-success' : 'text-danger');
                $row[] = '<span class="text '.$text_class.'">' . $value . '</span>';

                $value = (($aRow['click'] == 1) ? _l('yes') : _l('no'));
                $text_class = (($aRow['click'] == 1) ? 'text-success' : 'text-danger');
                $row[] = '<span class="text '.$text_class.'">' . $value . '</span>';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * report sms
     * @return view
     */
    public function sms_report(){
        $data['title'] = _l('sms_report');
        $data['from_date'] = date('Y-01-01');
        $data['to_date'] = date('Y-m-d');
        return $this->template->rander('Ma\Views\reports/includes/sms_report', $data);
    }

    /**
     * sms log table
     * @return json
     */
    public function sms_log_table()
    {

            $select = [
                get_db_prefix() . 'ma_sms_logs.dateadded as dateadded',
                get_db_prefix() . 'ma_text_messages.name as name',
                get_db_prefix() . 'clients.company_name as lead_name',
                get_db_prefix() . 'clients.phone as phonenumber',
            ];
            $where = [];

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_sms_logs';
            $join         = [
                'JOIN ' . get_db_prefix() . 'ma_text_messages ON ' . get_db_prefix() . 'ma_text_messages.id = ' . get_db_prefix() . 'ma_sms_logs.text_message_id',
                'JOIN ' . get_db_prefix() . 'clients ON ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'ma_sms_logs.lead_id',
            ];

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'ma_text_messages.id as id']);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];

                $row[] = _d($aRow['dateadded']);
                $row[] = '<a href="' . get_uri('ma/text_message_detail/' . $aRow['id']) . '" class="">' . $aRow['name'] . '</a>';
                $row[] = $aRow['lead_name'];
                $row[] = $aRow['phonenumber'];

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * { ma_sms setting by admin }
     * 
     * @return redirect
     */
    public function ma_sms_setting(){
        
            $data = $this->request->getPost();
            $success = $this->Ma_model->ma_sms_setting($data);
            if($success){
                $this->session->setFlashdata("success_message", _l('updated_successfully'));
            }else{
                $this->session->setFlashdata("error_message", _l('no_data_has_been_updated'));
            }
            app_redirect(('ma/setting?group=sms_configuration'));
        
    }

    /**
     * Gets the preview.
     *
     * @param        $id     The identifier
     */
    public function get_email_template_preview($id = ''){
        $html = '';
        if($id != ''){
            $email_template = $this->Ma_model->get_email_template($id);
            if($email_template){
                $html .= '<div class="panel_s">
                    <div class="panel-body">
                     <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title mt-3" role="tablist">';
                            foreach($email_template->data_design as $key => $design){
                                $html .= '<li><a class="'.($key == 0 ? 'active' : '') .'" role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#country_'.$design['id'].'">'.$design['country'].'</a></li>';
                            }
                    $html .= '</ul>
                      <div class="tab-content mtop15">';
                          foreach($email_template->data_design as $key => $design){
                    $html .= '<div role="tabpanel" class="tab-pane fade '.($key == 0 ? 'active show' : '').'" id="country_'.$design['id'].'">
                                <div class="wrapper">
                                  <div class="col-md-12">
                                    <div id="EmailEditor">'.json_decode($design['data_html']).'</div>
                                  </div>
                                </div>
                            </div>';
                          }
                $html .= '</div>
                    </div>
                </div>';
            }
        }
        
        echo html_entity_decode($html);
        die;
    }

    /**
     * email builder
     * @return view
     */
    public function email_design($id){
        $data['email_design'] = $this->Ma_model->get_email_design($id);

        $data['available_merge_fields'] = array("COMPANY_NAME", "LOGO_URL", "LEAD_NAME", "LEAD_PHONE", "LEAD_WEBSITE", "LEAD_ADDRESS", "LEAD_CITY", "LEAD_STATE", "LEAD_ZIP", "LEAD_COUNTRY", "CLIENT_NAME", "CLIENT_PHONE", "CLIENT_WEBSITE", "CLIENT_ADDRESS", "CLIENT_CITY", "CLIENT_STATE", "CLIENT_ZIP", "CLIENT_COUNTRY");

        $data['title'] = _l('email');

        $data['is_edit'] = true;

        return $this->template->rander('Ma\Views\channels/emails/email_design', $data);
    }

    /**
     * view email template
     * @return view
     */
    public function email_detail($id){
        $data['email'] = $this->Ma_model->get_email($id);
        $data['lead_by_email'] = $this->Ma_model->get_lead_by_email($id);
        $data['campaign_by_email'] = $this->Ma_model->get_campaign_by_email($id);
        $data['languages'] = get_language_list();

        $data['title'] = _l('email');

        return $this->template->rander('Ma\Views\channels/emails/email_detail', $data);
    }

    /**
     * Gets the data email chart.
     * @return json data chart
     */
    public function get_data_email_chart($email_id = '') {
        $data_email = $this->Ma_model->get_data_email_chart($email_id);

        $data_email_by_campaign = [];
        if($email_id != ''){
            $data_email_by_campaign = $this->Ma_model->get_data_email_by_campaign_chart($email_id);
        }

        echo json_encode([
            'data_email' => $data_email,
            'data_email_by_campaign' => $data_email_by_campaign,
        ]);
        die();
    }

    /**
     * email design save
     * @return redirect
     */
    public function email_design_save(){
        $data = $this->request->getPost();
        
        $success = $this->Ma_model->email_design_save($data);
        if($success){
            $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('template')));
        }

        app_redirect(('ma/email_detail/' . $data['email_id']));
    }

    /**
     * add or edit sms
     * @param  integer
     * @return view
     */
    public function sms($id = '')
    {
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            if ($id == '') {
                $id   = $this->Ma_model->add_sms($data);
                if ($id) {
                    $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('sms')));
                    app_redirect(('ma/sms_detail/' . $id));
                }
            } else {
                $success = $this->Ma_model->update_sms($data, $id);
                if ($success) {
                    $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('sms')));
                }
                app_redirect(('ma/sms_detail/' . $id));
            }
        }

        if ($id != '') {
            $data['sms'] = $this->Ma_model->get_sms($id);
        }
        $data['title']    = _l('sms');
        $data['bodyclass'] = 'point-trigger';
        $data['category'] = $this->Ma_model->get_category('', 'sms');
        $data['segments'] = $this->Ma_model->get_segment();
        $data['text_messages'] = $this->Ma_model->get_text_message();
        $data['languages'] = get_language_list();

        return $this->template->rander('Ma\Views\channels/sms/sms', $data);
    }

    /**
     * Gets the preview.
     *
     * @param        $id     The identifier
     */
    public function get_sms_template_preview($id = ''){
        $html = '';
        if($id != ''){
            $text_message = $this->Ma_model->get_text_message($id);
            if($text_message){
                $html = $text_message->description;
            }
        }
        
        echo html_entity_decode($html);
        die;
    }

    /**
     * delete sms
     * @param  integer $id
     * @return
     */
    public function delete_sms($id)
    {

        $success = $this->Ma_model->delete_sms($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('sms')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/channels?group=sms'));
    }

    /**
     * view sms template
     * @return view
     */
    public function sms_detail($id){
        $data['sms'] = $this->Ma_model->get_sms($id);
        $data['lead_by_sms'] = $this->Ma_model->get_lead_by_sms($id);
        $data['campaign_by_sms'] = $this->Ma_model->get_campaign_by_sms($id);


        $data['title'] = _l('sms');

        return $this->template->rander('Ma\Views\channels/sms/sms_detail', $data);
    }

    /**
     * Gets the data sms chart.
     * @return json data chart
     */
    public function get_data_sms_chart($sms_id = '') {
        $data_sms = $this->Ma_model->get_data_sms_chart($sms_id);

        $data_sms_by_campaign = [];
        if($sms_id != ''){
            $data_sms_by_campaign = $this->Ma_model->get_data_sms_by_campaign_chart($sms_id);
        }

        echo json_encode([
            'data_sms' => $data_sms,
            'data_sms_by_campaign' => $data_sms_by_campaign,
        ]);
        die();
    }

    /**
     * sms table
     * @return json
     */
    public function sms_table(){
            $aColumns = [
                get_db_prefix().'ma_sms.id as id', 
                get_db_prefix().'ma_sms.name as name', 
                get_db_prefix().'ma_categories.name as category_name', 
                get_db_prefix().'ma_sms.dateadded as dateadded',
                get_db_prefix().'ma_sms.description as description',
            ];

            $sIndexColumn = 'id';
            $sTable       = get_db_prefix().'ma_sms';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'ma_categories ON ' . get_db_prefix() . 'ma_categories.id = ' . get_db_prefix() . 'ma_sms.category'
            ];

            $where = [];

            if ($this->request->getPost('category')) {
                $category = $this->request->getPost('category');
                array_push($where, 'AND category IN (' . implode(', ', $category) . ')');
            }

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row = [];
                $row[] = $aRow['id'];

                $_data = $aRow['name'];
                $_data = '<a href="' . get_uri('ma/sms/' . $aRow['id']) . '">' . $_data . '</a>';

                $row[] = $_data;
                $row[] = $aRow['category_name'];

                $row[] = '<span class="text-has-action is-date" data-toggle="tooltip" data-title="' . _d($aRow['dateadded']) . '">' . _d($aRow['dateadded']) . '</span>';

                $view = '';
                $edit = '';
                $delete = '';
               
                $view = '<li role="presentation"><a href="' . get_uri('ma/sms_detail/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
                
                $edit = '';
                $edit .= '<li role="presentation"><a href="' . get_uri('ma/sms/' . $aRow['id']) . '" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_sms/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';

                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
                </span>';
                $row[] = $_data;

                $row['DT_RowClass'] = 'has-row-options';

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * send example email
     * @param  integer
     * @return redirect
     */
    public function send_example_email($email_id){
        $sent_to_email = $this->request->getPost('send_to_email');
        $email_design_id = $this->request->getPost('email_design_id');

        $email = $this->Ma_model->get_email($email_id);
        $success = $this->Ma_model->ma_send_email($sent_to_email, $email, [], '', $email_design_id);
        
        if($success){
            $this->session->setFlashdata("success_message", _l('send_email_successfully'));
        }else{
            $this->session->setFlashdata("error_message", _l('send_email_failed'));
        }

        app_redirect(('ma/email_detail/' . $email_id));
    }

    /**
     * clone email template
     * @return redirect
     */
    public function clone_email_template(){
        $data = $this->request->getPost();
        $id = $this->Ma_model->clone_email_template($data);

        if($id){
            $this->session->setFlashdata("success_message", _l('clone_successfully'));

            app_redirect(('ma/email_template_detail/' . $id));
        }

        app_redirect(('ma/setting?group=ma_email_templates'));
    }

    /**
     * ma run campaign
     * @param  [type] $id
     */
    public function ma_run_campaign($id){
        $this->Ma_model->run_campaigns($id);
        die;
      
    }

    /* load lead list view tab */

    function leads($rel_id, $rel_type) {

        $view_data['lead_statuses'] = $this->Lead_status_model->get_details()->getResult();
        $view_data['lead_sources'] = $this->Lead_source_model->get_details()->getResult();
        $view_data['rel_id'] = $rel_id;
        $view_data['rel_type'] = $rel_type;

        return $this->template->view("Ma\Views\leads/lead_table", $view_data);
    }

    /* list of leads, prepared for datatable  */

    public function leads_list_data() {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "custom_fields" => $custom_fields,
            "leads_only" => true,
            "rel_type" => $this->request->getPost('rel_type'),
            "rel_id" => $this->request->getPost('rel_id'),
            "status" => $this->request->getPost('status'),
            "source" => $this->request->getPost('source'),
            "start_date" => $this->request->getPost("start_date"),
            "end_date" => $this->request->getPost("end_date"),
            "custom_field_filter" => $this->prepare_custom_field_filter_values("leads", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $list_data = $this->Ma_model->get_leads_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->leads_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }


    /* prepare a row of lead list table */

    private function leads_make_row($data, $custom_fields) {
        //primary contact 
        $image_url = get_avatar($data->contact_avatar);
        $contact = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->primary_contact";
        $primary_contact = get_lead_contact_profile_link($data->primary_contact_id, $contact);

        //lead owner
        $owner = "-";
        if ($data->owner_id) {
            $owner_image_url = get_avatar($data->owner_avatar);
            $owner_user = "<span class='avatar avatar-xs mr10'><img src='$owner_image_url' alt='...'></span> $data->owner_name";
            $owner = get_team_member_profile_link($data->owner_id, $owner_user);
        }

        $row_data = array(
            anchor(get_uri("leads/view/" . $data->id), $data->company_name),
            $data->primary_contact ? $primary_contact : "",
            $owner,
            $data->created_date,
            format_to_date($data->created_date, false),
        );

        $row_data[] = js_anchor($data->lead_status_title, array("style" => "background-color: $data->lead_status_color", "class" => "badge js-selection-id", "data-id" => $data->id, "data-value" => $data->lead_status_id, "data-act" => "update-lead-status"));

        $row_data[] = ma_lead_total_point($data->id);

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }


        return $row_data;
    }

    /* upload a file */

    function asset_upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for ticket */

    function validate_asset_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* download files by zip */

    function download_asset_files($id) {

        $files = $this->Ma_model->get_asset($id)->files;

        return $this->download_app_files(get_setting("ma_asset_file_path"), $files);
    }

    /**
     * add or edit permission
     * @return json
     */
    public function permission(){
        $data = $this->request->getPost();
        $message = '';
        if($data['id'] == ''){
            $success = $this->Ma_model->add_permission($data);
            if($success){
                $this->session->setFlashdata("success_message", sprintf(_l('added_successfully'), _l('permission')));
            }
        }else{
            $id = $data['id'];
            unset($data['id']);
            $success = $this->Ma_model->update_permission($data, $id);
            if ($success) {
                $this->session->setFlashdata("success_message", sprintf(_l('updated_successfully'), _l('permission')));
            }
        }

        app_redirect('ma/setting?group=permissions');
    }

    /**
     * get data permission
     * @param  integer $id 
     * @return json     
     */
    public function get_data_permission($id){
        $permission = $this->Ma_model->get_permission($id);

        echo json_encode($permission);
    }

    /**
     * permission table
     * @return json
     */
    public function permission_table(){
           
            $select = [
                get_db_prefix() . 'ma_permissions.id as id',
                'CONCAT('.get_db_prefix() . 'users.first_name," ",'.get_db_prefix() . 'users.last_name) as name',
                get_db_prefix() . 'roles.title as role_name',
                get_db_prefix() . 'ma_permissions.id'
            ];

            $where = [];
            $from_date = '';
            $to_date   = '';

            $aColumns     = $select;
            $sIndexColumn = 'id';
            $sTable       = get_db_prefix() . 'ma_permissions';
            $join         = [
            'LEFT JOIN ' . get_db_prefix() . 'users ON ' . get_db_prefix() . 'users.id = ' . get_db_prefix() . 'ma_permissions.user_id',
            'LEFT JOIN ' . get_db_prefix() . 'roles ON ' . get_db_prefix() . 'users.role_id = ' . get_db_prefix() . 'roles.id'
            ];
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, []);

            $output  = $result['output'];
            $rResult = $result['rResult'];

            foreach ($rResult as $aRow) {
                $row   = [];

                $row[] = $aRow['id'];
                $row[] = $aRow['name'];
                $row[] = $aRow['role_name'];

                /*options*/
                $edit = '';
                $edit .= '<li role="presentation"><a href="#" onclick="edit_permission(' . $aRow['id'] . '); return false" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

                $delete = '';
                $delete .= '<li role="presentation"><a href="' . get_uri('ma/delete_permission/' . $aRow['id']) . '" class="dropdown-item"><i data-feather="x" class="icon-16"></i> ' . app_lang('delete') . '</a></li>';


                $_data = '
                <span class="dropdown inline-block">
                <button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
                <i data-feather="tool" class="icon-16"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" role="menu">'. $edit . $delete. '</ul>
                </span>';
                $row[] = $_data;

                $output['aaData'][] = $row;
                $output['data'][] = $row;
            }

            echo json_encode($output);
            die();
    }

    /**
     * segment change category
     * @return json
     */
    function segment_change_category() {
        $category = $this->request->getPost('category');

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');


        $save_id = $this->Ma_model->segment_change_category($id, $category);

        if ($save_id) {
            
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    /**
     * campaign change category
     * @return json
     */
    function campaign_change_category() {
        $category = $this->request->getPost('category');

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');


        $save_id = $this->Ma_model->campaign_change_category($id, $category);

        if ($save_id) {
            
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    /**
     * stage change category
     * @return json
     */
    function stage_change_category() {
        $category = $this->request->getPost('category');

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');


        $save_id = $this->Ma_model->stage_change_category($id, $category);

        if ($save_id) {
            
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    /**
     * delete permission
     * @param  integer $id
     * @return
     */
    public function delete_permission($id)
    {
        $success = $this->Ma_model->delete_permission($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('permission')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/setting?group=permissions'));
    }

    /**
     * email template add language
     * @return redirect
     */
    public function add_email_template_language(){
        $data = $this->request->getPost();
        
        $success = $this->Ma_model->add_email_template_language($data);
        if($success){
            $message = _l('added_successfully', _l('language'));
        }

        app_redirect(('ma/email_template_detail/' . $data['email_template_id']));
    }

    /**
     * clone email template design
     * @return redirect
     */
    public function clone_email_template_design(){
        $data = $this->request->getPost();
        
        $success = $this->Ma_model->clone_email_template_design($data);
        if($success){
            $message = _l('clone_successfully');
        }

        app_redirect(('ma/email_template_detail/' . $data['email_template_id']));
    }

    /**
     * delete email template language
     * @param  integer $id
     * @return
     */
    public function delete_email_template_design($id, $email_template_id)
    {
        if (!has_permission('ma_setting', '', 'edit')) {
            access_denied('ma_setting');
        }

        $success = $this->Ma_model->delete_email_template_design($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('language')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/email_template_detail/'.$email_template_id));
    }

    /**
     * email add language
     * @return redirect
     */
    public function add_email_language(){
        $data = $this->request->getPost();
        
        $success = $this->Ma_model->add_email_language($data);
        if($success){
            $message = _l('added_successfully', _l('language'));
        }

        app_redirect(('ma/email_detail/' . $data['email_id']));
    }

    /**
     * clone email design
     * @return redirect
     */
    public function clone_email_design(){
        $data = $this->request->getPost();
        
        $success = $this->Ma_model->clone_email_design($data);
        if($success){
            $message = _l('clone_successfully');
        }

        app_redirect(('ma/email_detail/' . $data['email_id']));
    }

    /**
     * delete email language
     * @param  integer $id
     * @return
     */
    public function delete_email_design($id, $email_id)
    {
        if (!has_permission('ma_setting', '', 'edit')) {
            access_denied('ma_setting');
        }

        $success = $this->Ma_model->delete_email_design($id);
        $message = '';
        if ($success) {
            $this->session->setFlashdata("success_message", sprintf(_l('deleted'), _l('language')));
        } else {
            $this->session->setFlashdata("error_message", _l('can_not_delete'));
        }

        app_redirect(('ma/email_detail/'.$email_id));
    }

    /* load client list view tab */

    public function clients($rel_id, $rel_type) {

        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;
        $view_data['groups_dropdown'] = json_encode($this->_get_groups_dropdown_select2_data(true));
        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown(true);
        $view_data['rel_id'] = $rel_id;
        $view_data['rel_type'] = $rel_type;

        return $this->template->view("Ma\Views\clients/client_table", $view_data);
    }

    /* list of clients, prepared for datatable  */

   public function clients_list_data() {

        $options = array(
            "custom_field_filter" => $this->prepare_custom_field_filter_values("clients", $this->login_user->is_admin, $this->login_user->user_type),
            "group_id" => $this->request->getPost("group_id"),
            "rel_type" => $this->request->getPost('rel_type'),
            "rel_id" => $this->request->getPost('rel_id'),
            "show_own_clients_only_user_id" => $this->show_own_clients_only_user_id(),
            "quick_filter" => $this->request->getPost("quick_filter"),
            "created_by" => $this->request->getPost("created_by"),
            "client_groups" => $this->allowed_client_groups
        );
        $list_data = $this->Ma_model->get_clients_details($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->client_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of client list table */

    private function client_make_row($data) {


        $image_url = get_avatar($data->contact_avatar);
        $contact = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->primary_contact";
        $primary_contact = get_client_contact_profile_link($data->primary_contact_id, $contact);

        $group_list = "";
        if ($data->client_groups) {
            $groups = explode(",", $data->client_groups);
            foreach ($groups as $group) {
                if ($group) {
                    $group_list .= "<li>" . $group . "</li>";
                }
            }
        }

        if ($group_list) {
            $group_list = "<ul class='pl15'>" . $group_list . "</ul>";
        }


        $due = 0;
        if ($data->invoice_value) {
            $due = ignor_minor_value($data->invoice_value - $data->payment_received);
        }

        $row_data = array($data->id,
            anchor(get_uri("clients/view/" . $data->id), $data->company_name),
            $data->primary_contact ? $primary_contact : "",
            $group_list,
            to_decimal_format($data->total_projects),
            to_currency($data->invoice_value, $data->currency_symbol),
            to_currency($data->payment_received, $data->currency_symbol),
            to_currency($due, $data->currency_symbol)
        );
        $row_data[] = ma_client_total_point($data->id);
       
        return $row_data;
    }

    /* load lead list view tab */

    function lead_campaign_tab($lead_id) {
        $view_data['lead_id'] = $lead_id;
        $view_data['campaigns'] = $this->Ma_model->get_campaigns_by_lead($lead_id);

        return $this->template->view("Ma\Views\leads/lead_campaign_tab", $view_data);
    }

    function save_email_settings() {
        $settings = array("ma_smtp_type","ma_email_sent_from_address", "ma_email_sent_from_name", "ma_email_protocol", "ma_email_smtp_host", "ma_email_smtp_port", "ma_email_smtp_user", "ma_email_smtp_pass", "ma_email_smtp_security_type");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (!$value) {
                $value = "";
            }

            if ($setting == "ma_email_smtp_pass") {
                if ($value === "******") {
                    $value = get_setting('ma_email_smtp_pass');
                } else {
                    $value = encode_id($value, "ma_email_smtp_pass");
                }
            }

            $this->Settings_model->save_setting($setting, $value);
        }

        $test_email_to = $this->request->getPost("ma_send_test_mail_to");
        if ($test_email_to) {
            $email_config = Array(
                'charset' => 'utf-8',
                'mailType' => 'html'
            );
            if ($this->request->getPost("ma_email_protocol") === "smtp") {
                $email_config["protocol"] = "smtp";
                $email_config["SMTPHost"] = $this->request->getPost("ma_email_smtp_host");
                $email_config["SMTPPort"] = $this->request->getPost("ma_email_smtp_port");
                $email_config["SMTPUser"] = $this->request->getPost("ma_email_smtp_user");

                $ma_email_smtp_pass = $this->request->getPost("ma_email_smtp_pass");
                if ($ma_email_smtp_pass === "******") {
                    $ma_email_smtp_pass = decode_password(get_setting('ma_email_smtp_pass'), "ma_email_smtp_pass");
                }
                $email_config["SMTPPass"] = $ma_email_smtp_pass;
                $email_config["SMTPCrypto"] = $this->request->getPost("ma_email_smtp_security_type");
                if ($email_config["SMTPCrypto"] === "none") {
                    $email_config["SMTPCrypto"] = "";
                }
            }

            $email = \CodeIgniter\Config\Services::email();
            $email->initialize($email_config);

            $email->setNewline("\r\n");
            $email->setCRLF("\r\n");
            $email->setFrom($this->request->getPost("ma_email_sent_from_address"), $this->request->getPost("ma_email_sent_from_name"));

            $email->setTo($test_email_to);
            $email->setSubject("Test message");
            $email->setMessage("This is a test message to check mail configuration.");

            if ($email->send()) {
                echo json_encode(array("success" => true, 'message' => app_lang('test_mail_sent')));
                return false;
            } else {
                log_message('error', $email->printDebugger());
                echo json_encode(array("success" => false, 'message' => app_lang('test_mail_send_failed')));
                return false;
            }
        }
        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }
}  