<?php
use App\Controllers\Security_Controller;

if (!function_exists('ma_has_permission')) {
    function ma_has_permission($permission, $staffid = '', $can = '')
    {
        $db = db_connect('default');

        if($staffid == ''){
            $ci = new Security_Controller(false);
            $staffid = $ci->login_user->id;
        }

        $db_builder = $db->table(db_prefix() . 'users');
        $db_builder->where('id', $staffid);
        $staff = $db_builder->get()->getRow();

        if ($staff->is_admin) {
            return true;
        }

        $db_builder = $db->table(db_prefix() . 'ma_permissions');
        $db_builder->where('user_id', $staffid);

        $ma_permission = $db_builder->get()->getRow();
        if($ma_permission){
            $ma_permission->permissions = json_decode($ma_permission->permissions, true);
            if (get_array_value($ma_permission->permissions, $permission)) {
                return true;
            }
        }

        return false;
    }
}

/**
 * Handles upload for expenses receipt
 * @param  mixed $id expense id
 * @return void
 */
if (!function_exists('ma_handle_asset_attachments')) {
    function ma_handle_asset_attachments($id)
    {
        if (isset($_FILES['file']) && _perfex_upload_error($_FILES['file']['error'])) {
            header('HTTP/1.0 400 Bad error');
            echo _perfex_upload_error($_FILES['file']['error']);
            die;
        }
        $path = MA_MODULE_UPLOAD_FOLDER . '/assets/' . $id . '/';
        $db = db_connect('default');

        if (isset($_FILES['file']['name'])) {
            // Get the temp file path
            $tmpFilePath = $_FILES['file']['tmp_name'];
            // Make sure we have a filepath
            if (!empty($tmpFilePath) && $tmpFilePath != '') {
                _maybe_create_upload_path($path);
                $filename    = $_FILES['file']['name'];
                $newFilePath = $path . $filename;
                // Upload the file into the temp dir
                if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $attachment   = [];
                    $attachment[] = [
                        'file_name' => $filename,
                        'filetype'  => $_FILES['file']['type'],
                        ];

                    $CI->misc_model->add_attachment_to_database($id, 'ma_asset', $attachment);
                }
            }
        }
    }
}

if (!function_exists('ma_get_category_name')) {
    function ma_get_category_name($id){
        $db = db_connect('default');
        $sql = 'select * from '.db_prefix().'ma_categories where id = "'.$id.'"';
        $category = $db->query($sql)->getRow();

        if($category){
            return $category->name;
        }else{
            return '';
        }
    }
}


if (!function_exists('ma_get_email_template_name')) {
    function ma_get_email_template_name($id){
        $db = db_connect('default');
        $sql = 'select * from '.db_prefix().'ma_email_templates where id = "'.$id.'"';
        $category = $db->query($sql)->getRow();

        if($category){
            return $category->name;
        }else{
            return '';
        }
    }
}

if (!function_exists('ma_get_asset_name')) {
    function ma_get_asset_name($id){
        $db = db_connect('default');
        $sql = 'select * from '.db_prefix().'ma_assets where id = "'.$id.'"';
        $category = $db->query($sql)->getRow();

        if($category){
            return $category->name;
        }else{
            return '';
        }
    }
}

if (!function_exists('ma_get_text_message_name')) {
    function ma_get_text_message_name($id){
        $db = db_connect('default');
        $sql = 'select * from '.db_prefix().'ma_text_messages where id = "'.$id.'"';
        $category = $db->query($sql)->getRow();

        if($category){
            return $category->name;
        }else{
            return '';
        }
    }
}


if (!function_exists('render_form_builder_field')) {
    /**
     * Used for customer forms eq. leads form, builded from the form builder plugin
     * @param  object $field field from database
     * @return mixed
     */
    function render_form_builder_field($field)
    {
        $type         = $field->type;
        $classNameCol = 'col-md-12';
        if (isset($field->className)) {
            if (strpos($field->className, 'form-col') !== false) {
                $classNames = explode(' ', $field->className);
                if (is_array($classNames)) {
                    $classNameColArray = array_filter($classNames, function ($class) {
                        return startsWith($class, 'form-col');
                    });

                    $classNameCol = implode(' ', $classNameColArray);
                    $classNameCol = trim($classNameCol);

                    $classNameCol = str_replace('form-col-xs', 'col-xs', $classNameCol);
                    $classNameCol = str_replace('form-col-sm', 'col-sm', $classNameCol);
                    $classNameCol = str_replace('form-col-md', 'col-md', $classNameCol);
                    $classNameCol = str_replace('form-col-lg', 'col-lg', $classNameCol);

                    // Default col-md-X
                    $classNameCol = str_replace('form-col', 'col-md', $classNameCol);
                }
            }
        }

        echo '<div class="' . $classNameCol . '">';
        if ($type == 'header' || $type == 'paragraph') {
            echo '<' . $field->subtype . ' class="' . (isset($field->className) ? $field->className : '') . '">' . nl2br($field->label) . '</' . $field->subtype . '>';
        } else {
            echo '<div class="form-group" data-type="' . $type . '" data-name="' . $field->name . '" data-required="' . (isset($field->required) ? true : 'false') . '">';
            echo '<label class="control-label" for="' . $field->name . '">' . (isset($field->required) ? ' <span class="text-danger">* </span> ': '') . $field->label . '' . (isset($field->description) ? ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . $field->description . '" data-placement="' . (is_rtl(true) ? 'left' : 'right') . '"></i>' : '') . '</label>';
            if (isset($field->subtype) && $field->subtype == 'color') {
                echo '<div class="input-group colorpicker-input">
         <input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text"' . (isset($field->value) ? ' value="' . $field->value . '"' : '') . ' name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" />
             <span class="input-group-addon"><i></i></span>
         </div>';
            } elseif ($type == 'text' || $type == 'number') {
                $ftype = isset($field->subtype) ? $field->subtype : $type;

                if($field->name === 'email' && is_client_logged_in()) {
                    $field->value = $GLOBALS['contact']->email;
                }

                echo '<input' . (isset($field->required) ? ' required="true"': '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' type="' . $ftype . '" name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" ' : '') . (isset($field->step) ? 'step="'. $field->step.'"' : '')  . (isset($field->min) ? 'min="'. $field->min.'"' : '') . (isset($field->max) ? 'max="'. $field->max.'"' : '')  . (isset($field->maxlength) ? 'maxlength="'. $field->maxlength.'"' : '') . '>';
            } elseif ($type == 'file') {
                $ftype = isset($field->subtype) ? $field->subtype : $type;
                echo '<input' . (isset($field->required) ? ' required="true"': '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' type="' . $ftype . '" name="' . (isset($field->multiple) ? $field->name . "[]" : $field->name ) . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" ' : '') . (isset($field->step) ? 'step="'. $field->step.'"' : ''). (isset($field->multiple) ? 'multiple="'. $field->multiple.'"' : '').'>';
            } elseif ($type == 'textarea') {
                echo '<textarea' . (isset($field->required) ? ' required="true"': '') . ' id="' . $field->name . '" name="' . $field->name . '" rows="' . (isset($field->rows) ? $field->rows : '4') . '" class="' . (isset($field->className) ? $field->className : '') . '" placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '"'. (isset($field->maxlength) ? 'maxlength="'. $field->maxlength.'"' : '') . '>'
                 . (isset($field->value) ? $field->value : '') . '</textarea>';
            } elseif ($type == 'date') {
                echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _d($field->value) : '') . '">';
            } elseif ($type == 'datetime-local') {
                echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datetimepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _dt($field->value) : '') . '">';
            } elseif ($type == 'select') {
                echo '<select' . (isset($field->required) ? ' required="true"': '') . '' . (isset($field->multiple) ? ' multiple="true"' : '') . ' class="' . (isset($field->className) ? $field->className : '') . '" name="' . $field->name . (isset($field->multiple) ? '[]' : '') . '" id="' . $field->name . '"' . (isset($field->values) && count($field->values) > 10 ? 'data-live-search="true"': '') . 'data-none-selected-text="' . (isset($field->placeholder) ? $field->placeholder : '') . '">';
                $values = [];
                if (isset($field->values) && count($field->values) > 0) {
                    foreach ($field->values as $option) {
                        echo '<option value="' . $option->value . '" ' . (isset($option->selected) ? ' selected' : '') . '>' . $option->label . '</option>';
                    }
                }
                echo '</select>';
            } elseif ($type == 'checkbox-group') {
                $values = [];
                if (isset($field->values) && count($field->values) > 0) {
                    $i = 0;
                    echo '<div class="chk">';
                    foreach ($field->values as $checkbox) {
                        echo '<div class="checkbox' . ((isset($field->inline) && $field->inline == 'true') || (isset($field->className) && strpos($field->className, 'form-inline-checkbox') !== false) ? ' checkbox-inline' : '') . '">';
                        echo '<input' . (isset($field->required) ? ' required="true"': '') . ' class="' . (isset($field->className) ? $field->className : '') . '" type="checkbox" id="chk_' . $field->name . '_' . $i . '" value="' . $checkbox->value . '" name="' . $field->name . '[]"' . (isset($checkbox->selected) ? ' checked' : '') . '>';
                        echo '<label for="chk_' . $field->name . '_' . $i . '">';
                        echo html_entity_decode($checkbox->label);
                        echo '</label>';
                        echo '</div>';
                        $i++;
                    }
                    echo '</div>';
                }
            } elseif ($type == 'radio-group') {
                if (isset($field->values) && count($field->values) > 0) {
                    $i = 0;
                    foreach ($field->values as $radio) {
                        echo '<div class="radio ' . ((isset($field->inline) && $field->inline == true) || (isset($field->className) && strpos($field->className, 'form-inline-radio') !== false) ? ' radio-inline' : '') . '">';
                        echo '  <input '. (isset($field->required) ? ' required="true"': '') . ' class="' . (isset($field->className) ? $field->className : '') . '" type="radio"';
                        echo 'name="' . $field->name . '" id="radio_' . $field->name . '_' . $i . '"';
                        echo 'value="' . $radio->value . '"' . (isset($radio->selected) ? ' checked' : '') . '>';
                       echo '<label for="radio_' . $field->name . '_' . $i . '">';
                        echo html_entity_decode($radio->label);
                        echo '</label>';
                        echo '</div>';
                        $i++;
                    }
                }
            }

            echo '</div>';
        }
        echo '</div>';
    }
}

/**
 * Generate md5 hash
 * @return string
 */
if (!function_exists('app_generate_hash')) {
    function app_generate_hash()
    {
        return md5(rand() . microtime() . time() . uniqid());
    }
}



if (!function_exists('ma_get_campaign_name')) {
    function ma_get_campaign_name($id){
        $db = db_connect('default');

        $sql = 'select * from '.db_prefix().'ma_campaigns where id = "'.$id.'"';
        $category = $db->query($sql)->getRow();

        if($category){
            return $category->name;
        }else{
            return '';
        }
    }
}


if (!function_exists('ma_lead_total_point')) {
    function ma_lead_total_point($id){
        $db = db_connect('default');
        
        $sql = 'select SUM(point) as total, lead_id from '.db_prefix().'ma_point_action_logs where lead_id = "'.$id.'" group by lead_id';
        $point = $db->query($sql)->getRow();

        if($point){
            return $point->total;
        }else{
            return 0;
        }
    }
}


if (!function_exists('ma_client_total_point')) {
    function ma_client_total_point($id){
        $db = db_connect('default');
       
        $sql = 'select SUM(point) as total, client_id from '.db_prefix().'ma_point_action_logs where client_id = "'.$id.'" group by client_id';
        $point = $db->query($sql)->getRow();

        if($point){
            return $point->total;
        }else{
            return 0;
        }
    }
}


if (!function_exists('ma_lead_total_point_by_campaign')) {
    function ma_lead_total_point_by_campaign($lead_id, $campaign_id){
        $db = db_connect('default');
       
        $sql = 'select SUM(point) as total, lead_id from '.db_prefix().'ma_point_action_logs where lead_id = "'.$lead_id.'" and campaign_id = "'.$campaign_id.'" group by lead_id';
        $point = $db->query($sql)->getRow();

        if($point){
            return $point->total;
        }else{
            return 0;
        }
    }
}


if (!function_exists('get_form_accepted_mimes')) {
    function get_form_accepted_mimes()
    {
        $allowed_extensions  = get_setting('accepted_file_formats');
        $_allowed_extensions = array_map(function ($ext) {
            return trim($ext);
        }, explode(',', $allowed_extensions));

        $all_form_ext = '';

        $all_form_ext = rtrim($allowed_extensions, ', ');

        return $all_form_ext;
    }
}


if (!function_exists('_get_invoice_value_calculation_query')) {
 function _get_invoice_value_calculation_query($invoices_table) {
        $select_invoice_value = "IFNULL(items_table.invoice_value,0)";

        $after_tax_1 = "(IFNULL(tax_table.percentage,0)/100*$select_invoice_value)";
        $after_tax_2 = "(IFNULL(tax_table2.percentage,0)/100*$select_invoice_value)";
        $after_tax_3 = "(IFNULL(tax_table3.percentage,0)/100*$select_invoice_value)";

        $discountable_invoice_value = "IF($invoices_table.discount_type='after_tax', (($select_invoice_value + $after_tax_1 + $after_tax_2) - $after_tax_3), $select_invoice_value )";

        $discount_amount = "IF($invoices_table.discount_amount_type='percentage', IFNULL($invoices_table.discount_amount,0)/100* $discountable_invoice_value, $invoices_table.discount_amount)";

        $before_tax_1 = "(IFNULL(tax_table.percentage,0)/100* ($select_invoice_value- $discount_amount))";
        $before_tax_2 = "(IFNULL(tax_table2.percentage,0)/100* ($select_invoice_value- $discount_amount))";
        $before_tax_3 = "(IFNULL(tax_table3.percentage,0)/100* ($select_invoice_value- $discount_amount))";

        $invoice_value_calculation_query = "(
                $select_invoice_value+
                IF($invoices_table.discount_type='before_tax',  (($before_tax_1+ $before_tax_2) - $before_tax_3), (($after_tax_1 + $after_tax_2) - $after_tax_3))
                - $discount_amount
               )";

        return $invoice_value_calculation_query;
    }
}