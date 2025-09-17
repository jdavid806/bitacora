<?php
$settings_menu = array(
	"hr_profile_settings" => array(
		array("name" => "contract_types", "url" => "hr_profile/contract_types"),
		array("name" => "salary_types", "url" => "hr_profile/salary_types"),
		array("name" => "allowance_types", "url" => "hr_profile/allowance_types"),
		array("name" => "workplaces", "url" => "hr_profile/workplaces"),
		array("name" => "type_of_trainings", "url" => "hr_profile/type_of_trainings"),
		array("name" => "reception_staffs", "url" => "hr_profile/reception_staffs"),
		array("name" => "contract_templates", "url" => "hr_profile/contract_templates"),
		array("name" => "procedure_retires", "url" => "hr_profile/procedure_retires"),
		array("name" => "prefix_numbers", "url" => "hr_profile/prefix_numbers"),
	),

);

if($login_user->is_admin){
	array_push($settings_menu["hr_profile_settings"], array("name" => "permission_roles", "url" => "hr_profile/roles"));
	array_push($settings_menu["hr_profile_settings"], array("name" => "reset_datas", "url" => "hr_profile/reset_datas"));
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