<?php
$settings_menu = array(
	"hr_payroll_settings" => array(
		array("name" => "income_tax_rates", "url" => "hr_payroll/income_tax_rates"),
		array("name" => "income_tax_rebates", "url" => "hr_payroll/income_tax_rebates"),
	),

);

if (hr_payroll_get_status_modules('Hr_profile') && (get_setting('integrated_hrprofile') == 1)) {
	array_push($settings_menu["hr_payroll_settings"], array("name" => "hr_records_earnings_list", "url" => "hr_payroll/hr_records_earnings_list"));
}else{
	array_push($settings_menu["hr_payroll_settings"], array("name" => "earnings_list", "url" => "hr_payroll/earnings_list"));
}

array_push($settings_menu["hr_payroll_settings"], array("name" => "salary_deductions_list", "url" => "hr_payroll/salary_deductions_list"));
array_push($settings_menu["hr_payroll_settings"], array("name" => "insurance_list", "url" => "hr_payroll/insurance_list"));
array_push($settings_menu["hr_payroll_settings"], array("name" => "payroll_columns", "url" => "hr_payroll/payroll_columns"));
array_push($settings_menu["hr_payroll_settings"], array("name" => "data_integration", "url" => "hr_payroll/data_integrations"));
array_push($settings_menu["hr_payroll_settings"], array("name" => "pdf_payslip_templates", "url" => "hr_payroll/pdf_payslip_templates"));


if($login_user->is_admin){
	array_push($settings_menu["hr_payroll_settings"], array("name" => "permission_roles", "url" => "hr_payroll/roles"));
	array_push($settings_menu["hr_payroll_settings"], array("name" => "reset_data", "url" => "hr_payroll/reset_datas"));
}

?>

<ul class="nav nav-tabs vertical settings d-block" role="tablist">
	<?php
	foreach ($settings_menu as $key => $value) {

		//collapse the selected settings tab panel
		$collapse_in = "";
		$collapsed_class = "collapsed";
		if (in_array($active_tab, array_column($value, "name"))) {
			$collapse_in = "show";
			$collapsed_class = "";
		}
		?>

		<div class="clearfix settings-anchor <?php echo html_entity_decode($collapsed_class); ?>" data-bs-toggle="collapse" data-bs-target="#settings-tab-<?php echo html_entity_decode($key); ?>">
			<?php echo app_lang($key); ?>
		</div>

		<?php
		echo "<div id='settings-tab-$key' class='collapse $collapse_in'>";
		echo "<ul class='list-group help-catagory'>";

		foreach ($value as $sub_setting) {
			$active_class = "";
			$setting_name = get_array_value($sub_setting, "name");
			$setting_url = get_array_value($sub_setting, "url");

			if ($active_tab == $setting_name) {
				$active_class = "active";
			}

			echo "<a href='" . get_uri($setting_url) . "' class='list-group-item $active_class'>" . app_lang($setting_name) . "</a>";
		}

		echo "</ul>";
		echo "</div>";
	}
	?>

</ul>