<?php
$db = db_connect('default');
$dbprefix = get_db_prefix();

if ($db->tableExists($dbprefix . 'hrp_employees_value')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_employees_value`;');
}
if ($db->tableExists($dbprefix . 'hrp_employees_timesheets')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_employees_timesheets`;');
}
if ($db->tableExists($dbprefix . 'hrp_commissions')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_commissions`;');
}
if ($db->tableExists($dbprefix . 'hrp_salary_deductions')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_salary_deductions`;');
}
if ($db->tableExists($dbprefix . 'hrp_bonus_kpi')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_bonus_kpi`;');
}
if ($db->tableExists($dbprefix . 'hrp_staff_insurances')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_staff_insurances`;');
}
if ($db->tableExists($dbprefix . 'hrp_payslips')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_payslips`;');
}
if ($db->tableExists($dbprefix . 'hrp_payslip_details')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_payslip_details`;');
}

//delete attendance_sample_file

foreach (glob('plugins/Hr_payroll/uploads/attendance_sample_file/' . '*') as $file) {
	$file_arr = explode("/", $file);
	$filename = array_pop($file_arr);

	if (file_exists($file)) {
		if ($filename != 'index.html') {
			unlink('plugins/Hr_payroll/uploads/attendance_sample_file/' . $filename);
		}
	}

}

foreach (glob('plugins/Hr_payroll/uploads/commissions_sample_file/' . '*') as $file) {
	$file_arr = explode("/", $file);
	$filename = array_pop($file_arr);

	if (file_exists($file)) {
		if ($filename != 'index.html') {
			unlink('plugins/Hr_payroll/uploads/commissions_sample_file/' . $filename);
		}
	}

}

foreach (glob('plugins/Hr_payroll/uploads/employees_sample_file/' . '*') as $file) {
	$file_arr = explode("/", $file);
	$filename = array_pop($file_arr);

	if (file_exists($file)) {
		if ($filename != 'index.html') {
			unlink('plugins/Hr_payroll/uploads/employees_sample_file/' . $filename);
		}
	}

}

foreach (glob('plugins/Hr_payroll/uploads/file_error_response/' . '*') as $file) {
	$file_arr = explode("/", $file);
	$filename = array_pop($file_arr);

	if (file_exists($file)) {
		if ($filename != 'index.html') {
			unlink('plugins/Hr_payroll/uploads/file_error_response/' . $filename);
		}
	}

}

foreach (glob('plugins/Hr_payroll/uploads/payslip/' . '*') as $file) {
	$file_arr = explode("/", $file);
	$filename = array_pop($file_arr);

	if (file_exists($file)) {
		if ($filename != 'index.html') {
			unlink('plugins/Hr_payroll/uploads/payslip/' . $filename);
		}
	}

}

foreach (glob('plugins/Hr_payroll/uploads/payslip_excel_file/' . '*') as $file) {
	$file_arr = explode("/", $file);
	$filename = array_pop($file_arr);

	if (file_exists($file)) {
		if ($filename != 'index.html') {
			unlink('plugins/Hr_payroll/uploads/payslip_excel_file/' . $filename);
		}
	}

}

if ($db->tableExists($dbprefix . 'hrp_employees_timeshee_leaves')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_employees_timeshee_leaves`;');
}

if ($db->tableExists($dbprefix . 'hrp_payslip_pdf_templates')) {
	$db->query('DROP TABLE `'.$dbprefix .'hrp_payslip_pdf_templates`;');
}