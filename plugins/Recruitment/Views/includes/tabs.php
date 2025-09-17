<?php
$settings_menu = array(
	"recruitment_settings" => array(
		array("name" => "job_positions", "url" => "recruitment/job_positions"),
		array("name" => "evaluation_criterias", "url" => "recruitment/evaluation_criterias"),
		array("name" => "evaluation_forms", "url" => "recruitment/evaluation_forms"),
		array("name" => "on_boarding_process", "url" => "recruitment/on_boardings"),
		array("name" => "skills", "url" => "recruitment/skills"),
		array("name" => "companies", "url" => "recruitment/companies"),
		array("name" => "industries", "url" => "recruitment/industries"),
		array("name" => "generals", "url" => "recruitment/generals"),
	),

);

if($login_user->is_admin){
	array_push($settings_menu["recruitment_settings"], array("name" => "permission_roles", "url" => "recruitment/roles"));
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