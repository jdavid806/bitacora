<?php
use App\Controllers\Security_Controller;

if (!function_exists('_d')) {
	function _d($date)
	{
		if($date == '0000-00-00' || $date == '0000-00-00 00:00:00')
		{
			return '';
		}
		
		return format_to_date($date);
	}
}

if (!function_exists('_l')) {
	function _l($key)
	{
		return app_lang($key);
	}
}

if (!function_exists('admin_url')) {
	function admin_url($url)
	{
		return get_uri($url);
	}
}


if (!function_exists('render_select')) {

	function render_select($name, $options, $option_attrs = [], $label = '', $selected = '', $select_attrs = [], $form_group_attr = [], $form_group_class = '', $select_class = '', $include_blank = true)
	{	
		$html = '';
		$html .= '<div class="form-group">';
		if($label != ''){
      $html .= '<label for="'. $name .'" class="">'. app_lang($label).'</label>';
		}

		$options_dropdown = [];

		if($include_blank == true){
			$options_dropdown[''] = '-';
		}

		$required = '';
		if (isset($select_attrs['required'])) {
	        $required = 'required';
	    }

	    $_select_attrs    = '';
	    if (!isset($select_attrs['data-width'])) {
	        $select_attrs['data-width'] = '100%';
	    }
	    if (!isset($select_attrs['data-none-selected-text'])) {
	        $select_attrs['data-none-selected-text'] = _l('dropdown_non_selected_tex');
	    }
	    foreach ($select_attrs as $key => $val) {
	        // tooltips
	        if ($key == 'title') {
	            $val = _l($val);
	        }
	        $_select_attrs .= $key . '=' . '"' . $val . '" ';
	    }

	    $_select_attrs = rtrim($_select_attrs);

		foreach ($options as $option) {
        $val       = '';
        $key       = '';
        if (isset($option[$option_attrs[0]]) && !empty($option[$option_attrs[0]])) {
            $key = $option[$option_attrs[0]];
        }
        if (!is_array($option_attrs[1])) {
            $val = $option[$option_attrs[1]];
        } else {
            foreach ($option_attrs[1] as $_val) {
                $val .= $option[$_val] . ' ';
            }
        }
        $val = trim($val);

        $options_dropdown[$key] = $val;
    }

    $html .= form_dropdown($name, $options_dropdown, $selected, "class='select2 validate-hidden w-100' id='".$name."' ".$required." ".$_select_attrs);
    
    $html .= '</div>';

		return $html;
	}
}


/**
 * General function for all datatables, performs search,additional select,join,where,orders
 * @param  array $aColumns           table columns
 * @param  mixed $sIndexColumn       main column in table for bettter performing
 * @param  string $sTable            table name
 * @param  array  $join              join other tables
 * @param  array  $where             perform where in query
 * @param  array  $additionalSelect  select additional fields
 * @param  string $sGroupBy group results
 * @return array
 */
if (!function_exists('data_tables_init')) {

	function data_tables_init($aColumns, $sIndexColumn, $sTable, $join = [], $where = [], $additionalSelect = [], $sGroupBy = '', $searchAs = [])
	{	
    	$db = db_connect('default');
        $request = \Config\Services::request();
		$dataPost = $request->getPost();
		$__post      =  $dataPost;
		$havingCount = '';
	/*
	 * Paging
	 */
	$sLimit = '';
	if (isset($dataPost['start']) && (is_numeric($dataPost['start'])) && $dataPost['length'] != '-1') {
		$sLimit = 'LIMIT ' . intval($dataPost['start']) . ', ' . intval($dataPost['length']);
	}
	$_aColumns = [];
	foreach ($aColumns as $column) {
		// if found only one dot
		if (substr_count($column, '.') == 1 && strpos($column, ' as ') === false) {
			$_column = explode('.', $column);
			if (isset($_column[1])) {
				if (startsWith1($_column[0], db_prefix())) {
					$_prefix = prefixed_table_fields_wildcard($_column[0], $_column[0], $_column[1]);
					array_push($_aColumns, $_prefix);
				} else {
					array_push($_aColumns, $column);
				}
			} else {
				array_push($_aColumns, $_column[0]);
			}
		} else {
			array_push($_aColumns, $column);
		}
	}

	/*
	 * Ordering
	 */
	$nullColumnsAsLast = [];

	$sOrder = '';
	if (isset($dataPost['order']) && $dataPost['order']) {
		$sOrder = 'ORDER BY ';
		foreach ($dataPost['order'] as $key => $val) {
			$columnName = $aColumns[intval($__post['order'][$key]['column'])];
			$dir        = strtoupper($__post['order'][$key]['dir']);

			if (strpos($columnName, ' as ') !== false) {
				$columnName = strbefore1($columnName, ' as');
			}

			// first checking is for eq tablename.column name
			// second checking there is already prefixed table name in the column name
			// this will work on the first table sorting - checked by the draw parameters
			// in future sorting user must sort like he want and the duedates won't be always last
			if ((in_array($sTable . '.' . $columnName, $nullColumnsAsLast)
				|| in_array($columnName, $nullColumnsAsLast))
		) {
				$sOrder .= $columnName . ' IS NULL ' . $dir . ', ' . $columnName;
		} else {
			$sOrder .= app_hooks()->apply_filters('datatables_query_order_column', $columnName, $sTable);
		}
		$sOrder .= ' ' . $dir . ', ';
	}
	if (trim($sOrder) == 'ORDER BY') {
		$sOrder = '';
	}
	$sOrder = rtrim($sOrder, ', ');

}
	/*
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = '';
	if ((isset($__post['search'])) && $__post['search']['value'] != '') {
		$search_value = $__post['search']['value'];
		$search_value = trim($search_value);

		$sWhere             = 'WHERE (';
		$sMatchCustomFields = [];
		// Not working, do not use it
		$useMatchForCustomFieldsTableSearch = app_hooks()->apply_filters('use_match_for_custom_fields_table_search', 'false');

		for ($i = 0; $i < count($aColumns); $i++) {
			$columnName = $aColumns[$i];
			if (strpos($columnName, ' as ') !== false) {
				$columnName = strbefore1($columnName, ' as');
			}

			if (stripos($columnName, 'AVG(') !== false || stripos($columnName, 'SUM(') !== false) {
			} else {
				if (($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == 'true') {
					if (isset($searchAs[$i])) {
						$columnName = $searchAs[$i];
					}
					// Custom fields values are FULLTEXT and should be searched with MATCH
					// Not working ATM
					if ($useMatchForCustomFieldsTableSearch === 'true' && startsWith1($columnName, 'ctable_')) {
						$sMatchCustomFields[] = $columnName;
					} else {
						$sWhere .= 'convert(' . $columnName . ' USING utf8)' . " LIKE '%" . escape_str($search_value) . "%' OR ";
					}
				}
			}
		}

		if (count($sMatchCustomFields) > 0) {
			$s = escape_str($search_value);
			foreach ($sMatchCustomFields as $matchCustomField) {
				$sWhere .= "MATCH ({$matchCustomField}) AGAINST (CONVERT(BINARY('{$s}') USING utf8)) OR ";
			}
		}

		if (count($additionalSelect) > 0) {
			foreach ($additionalSelect as $searchAdditionalField) {
				if (strpos($searchAdditionalField, ' as ') !== false) {
					$searchAdditionalField = strbefore1($searchAdditionalField, ' as');
				}
				if (stripos($columnName, 'AVG(') !== false || stripos($columnName, 'SUM(') !== false) {
				} else {
					// Use index
					$sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '%" . escape_str($search_value) . "%' OR ";
				}
			}
		}
		$sWhere = substr_replace($sWhere, '', -3);
		$sWhere .= ')';
	} else {
		// Check for custom filtering
		$searchFound = 0;
		$sWhere      = 'WHERE (';
		for ($i = 0; $i < count($aColumns); $i++) {
			if (isset($__post['columns']) && ($__post['columns'][$i]) && $__post['columns'][$i]['searchable'] == 'true') {
				$search_value = $__post['columns'][$i]['search']['value'];

				$columnName = $aColumns[$i];
				if (strpos($columnName, ' as ') !== false) {
					$columnName = strbefore1($columnName, ' as');
				}
				if ($search_value != '') {
					$sWhere .= 'convert(' . $columnName . ' USING utf8)' . " LIKE '%" . escape_str($search_value) . "%' OR ";
					if (count($additionalSelect) > 0) {
						foreach ($additionalSelect as $searchAdditionalField) {
							$sWhere .= 'convert(' . $searchAdditionalField . ' USING utf8)' . " LIKE '" . escape_str($search_value) . "%' OR ";
						}
					}
					$searchFound++;
				}
			}
		}
		if ($searchFound > 0) {
			$sWhere = substr_replace($sWhere, '', -3);
			$sWhere .= ')';
		} else {
			$sWhere = '';
		}
	}

	/*
	 * SQL queries
	 * Get data to display
	 */
	$_additionalSelect = '';
	if (count($additionalSelect) > 0) {
		$_additionalSelect = ',' . implode(',', $additionalSelect);
	}
	$where = implode(' ', $where);
	if ($sWhere == '') {
		$where = trim($where);
		if (startsWith1($where, 'AND') || startsWith1($where, 'OR')) {
			if (startsWith1($where, 'OR')) {
				$where = substr($where, 2);
			} else {
				$where = substr($where, 3);
			}
			$where = 'WHERE ' . $where;
		}
	}

	$join = implode(' ', $join);

	$sQuery = '
	SELECT SQL_CALC_FOUND_ROWS ' . str_replace(' , ', ' ', implode(', ', $_aColumns)) . ' ' . $_additionalSelect . "
	FROM $sTable
	" . $join . "
	$sWhere
	" . $where . "
	$sGroupBy
	$sOrder
	$sLimit
	";

	$rResult = $db->query($sQuery)->getResultArray();

	$rResult = app_hooks()->apply_filters('datatables_sql_query_results', $rResult, [
		'table' => $sTable,
		'limit' => $sLimit,
		'order' => $sOrder,
	]);

	/* Data set length after filtering */
	$sQuery = '
	SELECT FOUND_ROWS()
	';
	$_query         = $db->query($sQuery)->getResultArray();
	$iFilteredTotal = $_query[0]['FOUND_ROWS()'];
	if (startsWith1($where, 'AND')) {
		$where = 'WHERE ' . substr($where, 3);
	}
	/* Total data set length */
	$sQuery = '
	SELECT COUNT(' . $sTable . '.' . $sIndexColumn . ")
	FROM $sTable " . $join . ' ' . $where;

	$_query = $db->query($sQuery)->getResultArray();
	$iTotal = $_query[0]['COUNT(' . $sTable . '.' . $sIndexColumn . ')'];
	/*
	 * Output
	 */
	$output = [
		'draw'                 => isset($__post['draw']) && $__post['draw'] ? intval($__post['draw']) : 0,
		'iTotalRecords'        => $iTotal,
		'iTotalDisplayRecords' => $iFilteredTotal,
		'aaData'               => [],
	];

	return [
		'rResult' => $rResult,
		'output'  => $output,
	];
	}
}

if (!function_exists('prefixed_table_fields_wildcard')) {

	function prefixed_table_fields_wildcard($table, $alias, $field)
	{
		$prefixed = prefixed_table_fields_wildcard_2($table, $alias, $field);

		return $prefixed;
	}
}

if (!function_exists('prefixed_table_fields_wildcard_2')) {
 function prefixed_table_fields_wildcard_2($table, $alias, $field)
	{

		$db = db_connect('default');

		$columns     = $db->query("SHOW COLUMNS FROM $table")->getResultArray();
		$field_names = [];
		foreach ($columns as $column) {
			$field_names[] = $column['Field'];
		}
		$prefixed = [];
		foreach ($field_names as $field_name) {
			if ($field == $field_name) {
				$prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
			}
		}

		return implode(', ', $prefixed);
	}
}


/**
 * Used in data_tables_init function to fix sorting problems when duedate is null
 * Null should be always last
 * @return array
 */
if (!function_exists('get_null_columns_that_should_be_sorted_as_last')) {
	function get_null_columns_that_should_be_sorted_as_last()
	{
	    $columns = [
	        get_db_prefix() . 'projects.deadline',
	        get_db_prefix() . 'tasks.duedate',
	        get_db_prefix() . 'contracts.dateend',
	        get_db_prefix() . 'subscriptions.date_subscribed',
	    ];

	    return app_hooks()->apply_filters('null_columns_sort_as_last', $columns);
	}
}
/**
 * Render table used for datatables
 * @param  array  $headings           [description]
 * @param  string $class              table class / added prefix table-$class
 * @param  array  $additional_classes
 * @return string                     formatted table
 */
/**
 * Render table used for datatables
 * @param  array   $headings
 * @param  string  $class              table class / add prefix eq.table-$class
 * @param  array   $additional_classes additional table classes
 * @param  array   $table_attributes   table attributes
 * @param  boolean $tfoot              includes blank tfoot
 * @return string
 */
if (!function_exists('render_datatable')) {
	function render_datatable($headings = [], $class = '', $additional_classes = [''], $table_attributes = [])
	{
	    $_additional_classes = '';
		$_table_attributes   = ' ';
		if (count($additional_classes) > 0) {
			$_additional_classes = ' ' . implode(' ', $additional_classes);
		}

		$browser = '';
		$IEfix   = '';
		if ($browser == 'Internet Explorer') {
			$IEfix = 'ie-dt-fix';
		}

		foreach ($table_attributes as $key => $val) {
			$_table_attributes .= $key . '=' . '"' . $val . '" ';
		}

		$table = '<div class="' . $IEfix . '"><table' . $_table_attributes . 'class="dt-table-loading table table-' . $class . '' . $_additional_classes . '">';
		$table .= '<thead>';
		$table .= '<tr>';
		foreach ($headings as $heading) {
			if (!is_array($heading)) {
				$table .= '<th>' . $heading . '</th>';
			} else {
				$th_attrs = '';
				if (isset($heading['th_attrs'])) {
					foreach ($heading['th_attrs'] as $key => $val) {
						$th_attrs .= $key . '=' . '"' . $val . '" ';
					}
				}
				$th_attrs = ($th_attrs != '' ? ' ' . $th_attrs : $th_attrs);
				$table .= '<th' . $th_attrs . '>' . $heading['name'] . '</th>';
			}
		}
		$table .= '</tr>';
		$table .= '</thead>';
		$table .= '<tbody></tbody>';
		$table .= '</table></div>';
		echo            $table;
	}
}

/**
 * Translated datatables language based on app languages
 * This feature is used on both admin and customer area
 * @return array
 */
if (!function_exists('get_datatables_language_array')) {
function get_datatables_language_array()
{
    $lang = [
        'emptyTable'        => preg_replace("/{(\d+)}/", app_lang('dt_entries'), app_lang('dt_empty_table')),
        'info'              => preg_replace("/{(\d+)}/", app_lang('dt_entries'), app_lang('dt_info')),
        'infoEmpty'         => preg_replace("/{(\d+)}/", app_lang('dt_entries'), app_lang('dt_info_empty')),
        'infoFiltered'      => preg_replace("/{(\d+)}/", app_lang('dt_entries'), app_lang('dt_info_filtered')),
        'lengthMenu'        => '_MENU_',
        'loadingRecords'    => app_lang('dt_loading_records'),
        'processing'        => '<div class="dt-loader"></div>',
        'search'            => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-search"></span></span>',
        'searchPlaceholder' => app_lang('dt_search'),
        'zeroRecords'       => app_lang('dt_zero_records'),
        'paginate'          => [
            'first'    => app_lang('dt_paginate_first'),
            'last'     => app_lang('dt_paginate_last'),
            'next'     => app_lang('dt_paginate_next'),
            'previous' => app_lang('dt_paginate_previous'),
        ],
        'aria' => [
            'sortAscending'  => app_lang('dt_sort_ascending'),
            'sortDescending' => app_lang('dt_sort_descending'),
        ],
    ];

    return hooks()->apply_filters('datatables_language_array', $lang);
}
}

/**
 * Function that will parse filters for datatables and will return based on a couple conditions.
 * The returned result will be pushed inside the $where variable in the table SQL
 * @param  array $filter
 * @return string
 */
if (!function_exists('prepare_dt_filter')) {
	function prepare_dt_filter($filter)
	{
	    $filter = implode(' ', $filter);
	    if (startsWith($filter, 'AND')) {
	        $filter = substr($filter, 3);
	    } elseif (startsWith($filter, 'OR')) {
	        $filter = substr($filter, 2);
	    }

	    return $filter;
	}
}
/**
 * Get table last order
 * @param  string $tableID table unique identifier id
 * @return string
 */
if (!function_exists('get_table_last_order')) {
	function get_table_last_order($tableID)
	{
	    return htmlentities(get_staff_meta(get_staff_user_id(), $tableID . '-table-last-order'));
	}
}

if (!function_exists('startsWith')) {
	function startsWith( $haystack, $needle ) {
	     $length = strlen( $needle );
	     return substr( $haystack, 0, $length ) === $needle;
	}
}


/**
 * Render date picker input for admin area
 * @param  [type] $name             input name
 * @param  string $label            input label
 * @param  string $value            default value
 * @param  array  $input_attrs      input attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $input_class      <input> additional class
 * @return string
 */
if (!function_exists('render_date_input')) {
	function render_date_input($name, $label = '', $value = '', $input_attrs = [], $form_group_attr = [], $form_group_class = '', $input_class = '')
	{
	    $input            = '';
	    $_form_group_attr = '';

	    $form_group_attr['app-field-wrapper'] = $name;

	    foreach ($form_group_attr as $key => $val) {
	        // tooltips
	        if ($key == 'title') {
	            $val = app_lang($val);
	        }
	        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
	    }

	    $_form_group_attr = rtrim($_form_group_attr);

	    if (!empty($form_group_class)) {
	        $form_group_class = ' ' . $form_group_class;
	    }
	    if (!empty($input_class)) {
	        $input_class = ' ' . $input_class;
	    }

	    $input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
	    if ($label != '') {
	        $input .= '<label for="' . $name . '" class="control-label">' . app_lang($label, '', false) . '</label>';
	    }

	    $input .= form_input(array_merge(array(
	        "id" => $name,
	        "name" => $name,
	        "value" => $value,
	        "class" => "form-control",
	        "placeholder" => app_lang($label, '', false),
	        "autocomplete" => "off",
	    ), $input_attrs));
	    
	    $input .= '</div>';

	    return $input;
	}
}


/**
 * Function that renders input for admin area based on passed arguments
 * @param  string $name             input name
 * @param  string $label            label name
 * @param  string $value            default value
 * @param  string $type             input type eq text,number
 * @param  array  $input_attrs      attributes on <input
 * @param  array  $form_group_attr  <div class="form-group"> html attributes
 * @param  string $form_group_class additional form group class
 * @param  string $input_class      additional class on input
 * @return string
 */
if (!function_exists('render_input')) {
	function render_input($name, $label = '', $value = '', $type = 'text', array $input_attrs = [], array $form_group_attr = [], $form_group_class = '', $input_class = '', $data_required = false, $data_required_msg = '')
	{
		$input            = '';
		$_form_group_attr = '';

		$form_group_attr['app-field-wrapper'] = $name;

		foreach ($form_group_attr as $key => $val) {
        // tooltips
			if ($key == 'title') {
				$val = app_lang($val);
			}
			$_form_group_attr .= $key . '=' . '"' . $val . '" ';
		}

		$_form_group_attr = rtrim($_form_group_attr);

		if (!empty($form_group_class)) {
			$form_group_class = ' ' . $form_group_class;
		}
		if (!empty($input_class)) {
			$input_class = ' ' . $input_class;
		}
		$input .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
		if ($label != '') {
			$input .= '<label for="' . $name . '" class="control-label">' . app_lang($label, '', false) . '</label>';
		}

		$input .= form_input(array_merge(array(
			"id" => $name,
			"name" => $name,
			"value" => $value,
			"class" => "form-control".$input_class,
			"placeholder" => app_lang($label),
			"autocomplete" => "off",
			"data-rule-required" => $data_required,
			"data-msg-required" => $data_required_msg == '' ? app_lang('field_required') : app_lang($data_required_msg),
		), $input_attrs), $value, '', $type);

		$input .= '</div>';

		return $input;
	}
}

/**
 * Render textarea for admin area
 * @param  [type] $name             textarea name
 * @param  string $label            textarea label
 * @param  string $value            default value
 * @param  array  $textarea_attrs      textarea attributes
 * @param  array  $form_group_attr  <div class="form-group"> div wrapper html attributes
 * @param  string $form_group_class form group div wrapper additional class
 * @param  string $textarea_class      <textarea> additional class
 * @return string
 */
if (!function_exists('render_textarea')) {
	function render_textarea($name, $label = '', $value = '', $textarea_attrs = [], $form_group_attr = [], $form_group_class = '', $textarea_class = '')
	{
	    $textarea         = '';
	    $_form_group_attr = '';
	    $_textarea_attrs  = '';
	    if (!isset($textarea_attrs['rows'])) {
	        $textarea_attrs['rows'] = 4;
	    }

	    if (isset($textarea_attrs['class'])) {
	        $textarea_class .= ' ' . $textarea_attrs['class'];
	        unset($textarea_attrs['class']);
	    }

	    foreach ($textarea_attrs as $key => $val) {
	        // tooltips
	        if ($key == 'title') {
	            $val = _l($val);
	        }
	        $_textarea_attrs .= $key . '=' . '"' . $val . '" ';
	    }

	    $_textarea_attrs = rtrim($_textarea_attrs);

	    $form_group_attr['app-field-wrapper'] = $name;

	    foreach ($form_group_attr as $key => $val) {
	        if ($key == 'title') {
	            $val = _l($val);
	        }
	        $_form_group_attr .= $key . '=' . '"' . $val . '" ';
	    }

	    $_form_group_attr = rtrim($_form_group_attr);

	    if (!empty($textarea_class)) {
	        $textarea_class = trim($textarea_class);
	        $textarea_class = ' ' . $textarea_class;
	    }
	    if (!empty($form_group_class)) {
	        $form_group_class = ' ' . $form_group_class;
	    }
	    $textarea .= '<div class="form-group' . $form_group_class . '" ' . $_form_group_attr . '>';
	    if ($label != '') {
	        $textarea .= '<label for="' . $name . '" class="control-label">' . _l($label, '', false) . '</label>';
	    }

	    $v = clear_textarea_breaks($value);
	    if (strpos($textarea_class, 'tinymce') !== false) {
	        $v = $value;
	    }

	    $textarea .= form_textarea(array_merge(array(
	                    "id" => $name,
	                    "name" => $name,
	                    "value" => set_value($name, $v),
	                    "class" => 'form-control ' . $textarea_class,
	                    "placeholder" => _l($label, '', false),
	                    "data-rich-text-editor" => true
	                ), $textarea_attrs));

	    $textarea .= '</div>';

	    return $textarea;
	}
}

/**
 * Remove <br /> html tags from string to show in textarea with new linke
 * @param  string $text
 * @param  string $replace character to replace with
 * @return string formatted text
 */
if (!function_exists('clear_textarea_breaks')) {
	function clear_textarea_breaks($text, $replace = '')
	{
	    $breaks = [
	        '<br />',
	        '<br>',
	        '<br/>',
	    ];

	    $text = str_ireplace($breaks, $replace, $text);
	    $text = trim($text);

	    return $text;
	}
}


if (!function_exists('is_admin')) {
	function is_admin()
	{
		$ci = new Security_Controller(false);
	    if($ci->login_user->is_admin == 1){
	    	return true;
	    }

	    return false;
	}
}

/**
 * Used in:
 * Search contact tickets
 * Project dropdown quick switch
 * Calendar tooltips
 * @param  [type] $userid [description]
 * @return [type]         [description]
 */
if (!function_exists('get_company_name')) {
	function get_company_name($userid, $prevent_empty_company = false)
	{
	    
	    $_userid = $userid;

	    $db = db_connect('default');
	    $db_builder = $db->table(get_db_prefix() . 'clients');
	    $client = $db_builder->select('company_name')
	        ->where('id', $_userid)
	        ->get()
	        ->getRow();
	    if ($client) {
	        return $client->company_name;
	    }

	    return '';
	}
}

if (!function_exists('get_staff_full_name')) {
	function get_staff_full_name($staffid = ''){
		if($staffid != ''){
	    	$db = db_connect('default');
	    	$db_builder = $db->table(get_db_prefix() . 'users');
	    	$db_builder->where('id', $staffid);
	    	$user = $db_builder->get()->getRow();
	    	if($user){
				return $user->first_name . " " . $user->last_name;
	    	}

			return '';
		}else{
	    	$ci = new Security_Controller(false);
			return $ci->login_user->first_name . " " . $ci->login_user->last_name;
		}
	}
}

if (!function_exists('to_sql_date')) {
	function to_sql_date($date){
		return $date;
	}
}

/**
 * Sum total from table
 * @param  string $table table name
 * @param  array  $attr  attributes
 * @return mixed
 */
if (!function_exists('sum_from_table')) {
	function sum_from_table($table, $attr = [])
	{
	    if (!isset($attr['field'])) {
	        show_error('sum_from_table(); function expect field to be passed.');
	    }

	    $db = db_connect('default');
	    $db_builder = $db->table($table);
	    if (isset($attr['where']) && is_array($attr['where'])) {
	        $i = 0;
	        foreach ($attr['where'] as $key => $val) {
	            if (is_numeric($key)) {
	                $db_builder->where($val);
	                unset($attr['where'][$key]);
	            }
	            $i++;
	        }
	        $db_builder->where($attr['where']);
	    }
	    $db_builder->selectSum($attr['field']);
	    $result = $db_builder->get()->getRow();

	    return $result->{$attr['field']};
	}
}


/**
 * Sum total credits applied for invoice
 * @param  mixed $id invoice id
 * @return mixed
 */
if (!function_exists('total_credits_applied_to_invoice')) {
	function total_credits_applied_to_invoice($id)
	{
	    return 0;
	}
}

/**
 * @since  1.0.0
 * Check whether an setting exists
 *
 * @param  string $name setting name
 *
 * @return boolean
 */
if (!function_exists('setting_exists')) {

  function setting_exists($name)
  { 
   
    $db = db_connect('default');
    $db_builder = $db->table(get_db_prefix() . 'settings');

    $count = $db_builder->where('setting_name', $name)->countAllResults();

    return $count > 0;
  }
}

/**
 * Render color picker input
 * @param  string $name        input name
 * @param  string $label       field name
 * @param  string $value       default value
 * @param  array  $input_attrs <input sttributes
 * @return string
 */
if (!function_exists('render_color_picker')) {
	function render_color_picker($name, $label = '', $value = '', $input_attrs = [])
	{
		$picker = '';
	    $picker .= '<div class="color-palet mb-3">'; 
	    $selected_color = $value != "" ? $value : "#4A8AF4";
	    $colors = array("#83c340", "#29c2c2", "#2d9cdb", "#aab7b7", "#f1c40f", "#e18a00", "#e74c3c", "#d43480", "#ad159e", "#37b4e1", "#34495e", "#dbadff");
	    $custom_color_active_class = "active";

	    foreach ($colors as $color) {
	        $active_class = "";
	        if ($selected_color === $color) {
	            $active_class = "active";
	            $custom_color_active_class = "";
	        }
	        $picker .= "<span style='background-color:" . $color . "' class='color-tag clickable mr15 " . $active_class . "' data-color='" . $color . "'></span>";
	    }

		$picker .= '<input type="color" id="custom-color" class="input-color '. $custom_color_active_class .'" name="'.$name.'" value="'. ($value != "" ? $value : "#4A8AF4") .'" />';
		$picker .= '</div>';

	    return $picker;
	}
}


if (!function_exists('startsWith1')) {
	/**
	* String ends with
	* @param  string $haystack
	* @param  string $needle
	* @return boolean
	*/
	function startsWith1($haystack, $needle)
	{
		return $needle === '' || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
}

if (!function_exists('strbefore1')) {
	function strbefore1($string, $substring)
	{
		$pos = strpos($string, $substring);
		if ($pos === false) {
			return $string;
		}

		return (substr($string, 0, $pos));
	}
}

if (!function_exists('strafter')) {
	function strafter($string, $substring)
	{
		$pos = strpos($string, $substring);
		if ($pos === false) {
			return $string;
		}

		return (substr($string, $pos + strlen($substring)));
	}
}

if (!function_exists('_escape_str')) {
	function _escape_str($str)
	{
		return str_replace("'", "''", remove_invisible_characters($str, FALSE));
	}
}

if (!function_exists('get_staff_user_id')) {
	function get_staff_user_id()
	{
		$Users_model = model("Models\Users_model");
		return $Users_model->login_user_id();
	}
}

/**
 * db prefix
 * @return [type] 
 */
if (!function_exists('db_prefix')) {
	function db_prefix() {
		$db = db_connect('default');
		return $db->getPrefix();
	}

}

/**
 * Count total rows on table based on params
 * @param  string $table Table from where to count
 * @param  array  $where
 * @return mixed  Total rows
 */
if (!function_exists('total_rows')) {
	function total_rows($table, $where = [])
	{
		$db = db_connect('default');
    	$db_builder = $db->table($table);
	    if (is_array($where)) {
	        if (sizeof($where) > 0) {
	            $db_builder->where($where);
	        }
	    } elseif (strlen($where) > 0) {
	        $db_builder->where($where);
	    }

	    return $db_builder->countAllResults();
	}
}



/**
 * For more readable code created this function to render only yes or not values for settings
 * @param  string $option_value option from database to compare
 * @param  string $label        input label
 * @param  string $tooltip      tooltip
 */
if (!function_exists('render_yes_no_option')) {
	function render_yes_no_option($option_value, $label, $tooltip = '', $replace_yes_text = '', $replace_no_text = '', $replace_1 = '', $replace_0 = '')
	{
	    ob_start(); ?>
	    <div class="form-group">
	        <label for="<?php echo html_entity_decode($option_value); ?>" class="control-label clearfix">
	            <?php echo($tooltip != '' ? '<i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l($tooltip, '', false) . '"></i> ': '') . _l($label, '', false); ?>
	        </label>
	        <div class="radio radio-primary radio-inline">
	            <input type="radio" class="form-check-input" id="y_opt_1_<?php echo html_entity_decode($option_value); ?>" name="settings[<?php echo html_entity_decode($option_value); ?>]" value="<?php echo html_entity_decode($replace_1 == '' ? 1 : $replace_1); ?>" <?php if (get_setting($option_value) == ($replace_1 == '' ? '1' : $replace_1)) {
	        echo 'checked';
	    } ?>>
	            <label for="y_opt_1_<?php echo html_entity_decode($option_value); ?>">
	                <?php echo html_entity_decode($replace_yes_text == '' ? _l('settings_yes') : $replace_yes_text); ?>
	            </label>
	        </div>
	        <div class="radio radio-primary radio-inline">
	                <input type="radio" class="form-check-input" id="y_opt_2_<?php echo html_entity_decode($option_value); ?>" name="settings[<?php echo html_entity_decode($option_value); ?>]" value="<?php echo html_entity_decode($replace_0 == '' ? 0 : $replace_0); ?>" <?php if (get_setting($option_value) == ($replace_0 == '' ? '0' : $replace_0)) {
	        echo 'checked';
	    } ?>>
	                <label for="y_opt_2_<?php echo html_entity_decode($option_value); ?>">
	                    <?php echo html_entity_decode($replace_no_text == '' ? _l('settings_no') : $replace_no_text); ?>
	                </label>
	        </div>
	    </div>
	    <?php
	    $settings = ob_get_contents();
	    ob_end_clean();
	    echo html_entity_decode($settings);
	}
}