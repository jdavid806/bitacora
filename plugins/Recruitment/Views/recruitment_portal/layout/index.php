<?php

$dir = 'ltr';
if ((get_setting("rtl"))) {
	$dir = 'rtl';
}

helper('cookie');
if(isset($login_user)){
	$left_menu_minimized = get_cookie("left_menu_minimized");
}else{
	$left_menu_minimized = true;

}
?>
<!DOCTYPE html>
<html lang="en" dir="<?php echo html_entity_decode($dir); ?>">
<?php echo view('Recruitment\Views\\recruitment_portal\layout\includes/head'); ?>

<body class="<?php echo html_entity_decode($left_menu_minimized) ? "sidebar-toggled" : ""; ?>">

	<?php
	if ($topbar) {
		echo view($topbar);
	}

	?>

	<div id="left-menu-toggle-mask">
		<!-- left menu -->
		<div class="sidebar sidebar-off">
			<?php
			if(isset($login_user)){
				$user = $login_user->id;
			}
			$dashboard_link = '';
			?>
			<a class="sidebar-toggle-btn hide" href="#">
				<i data-feather="menu" class="icon mt-1 text-off"></i>
			</a>

			<a class="sidebar-brand brand-logo" href="<?php echo html_entity_decode($dashboard_link); ?>"><img class="dashboard-image" src="<?php echo get_logo_url(); ?>" /></a>
			<a class="sidebar-brand brand-logo-mini" href="<?php echo html_entity_decode($dashboard_link); ?>"><img class="dashboard-image" src="<?php echo get_favicon_url(); ?>" /></a>
			<?php if(isset($login_user)){ ?>
				<div class="sidebar-scroll">
					<ul id="sidebar-menu" class="sidebar-menu">
						<?php

						$sidebar_menu["job"] = array(
							"name" => "job",
							"url" => "recruitment_portal",
							"class" => "list",
							"submenu" => [],
							"position" => 1,
						);
						$sidebar_menu["profile"] = array(
							"name" => "profile",
							"url" => "recruitment_portal/profile",
							"class" => "user",
							"submenu" => [],
							"position" => 2,
						);

						$sidebar_menu["applied_job"] = array(
							"name" => "re_applied_jobs",
							"url" => "recruitment_portal/applied_jobs",
							"class" => "check-square",
							"submenu" => [],
							"position" => 3,
						);
						$sidebar_menu["interview_schedule"] = array(
							"name" => "interview_schedules",
							"url" => "recruitment_portal/interview_schedules",
							"class" => "calendar",
							"submenu" => [],
							"position" => 4,
						);


						foreach ($sidebar_menu as $main_menu) {
							if (isset($main_menu["name"])) {
								$submenu = get_array_value($main_menu, "submenu");
								$expend_class = $submenu ? " expand " : "";
								$active_class = isset($main_menu["is_active_menu"]) ? "active" : "";

								$submenu_open_class = "";
								if ($expend_class && $active_class) {
									$submenu_open_class = " open ";
								}

								$badge = get_array_value($main_menu, "badge");
								$badge_class = get_array_value($main_menu, "badge_class");
								$target = (isset($main_menu['is_custom_menu_item']) && isset($main_menu['open_in_new_tab']) && $main_menu['open_in_new_tab']) ? "target='_blank'" : "";
								?>

								<li class="<?php echo html_entity_decode($active_class . " " . $expend_class . " " . $submenu_open_class . " " ); ?> main">
									<a <?php echo html_entity_decode($target); ?> href="<?php echo isset($main_menu['is_custom_menu_item']) ? $main_menu['url'] : get_uri($main_menu['url']); ?>">
										<i data-feather="<?php echo ($main_menu['class']); ?>" class="icon"></i>
										<span class="menu-text <?php echo isset($main_menu['custom_class']) ? $main_menu['custom_class'] : ""; ?>"><?php echo isset($main_menu['is_custom_menu_item']) ? $main_menu['name'] : app_lang($main_menu['name']); ?></span>
										<?php
										if ($badge) {
											echo "<span class='badge rounded-pill $badge_class'>$badge</span>";
										}
										?>
									</a>
									<?php
									if ($submenu) {
										echo "<ul>";
										foreach ($submenu as $s_menu) {
											if (isset($s_menu['name'])) {
												$sub_menu_target = (isset($s_menu['is_custom_menu_item']) && isset($s_menu['open_in_new_tab']) && $s_menu['open_in_new_tab']) ? "target='_blank'" : "";
												?>
												<li>
													<a <?php echo html_entity_decode($sub_menu_target); ?> href="<?php echo isset($s_menu['is_custom_menu_item']) ? $s_menu['url'] : get_uri($s_menu['url']); ?>">
														<i data-feather='minus' width='12'></i>
														<span><?php echo isset($s_menu['is_custom_menu_item']) ? $s_menu['name'] : app_lang($s_menu['name']); ?></span>
													</a>
												</li>
												<?php
											}
										}
										echo "</ul>";
									}
									?>
								</li>
								<?php
							}
						}
						?>
					</ul>
				</div>
			<?php } ?>
		</div><!-- sidebar menu end -->


		<!-- left menu -->

		<div class="page-container overflow-auto">
			<div id="pre-loader">
				<div id="pre-loade" class="app-loader"><div class="loading"></div></div>
			</div>
			<div class="scrollable-page main-scrollable-page">
				<?php
				if (isset($content_view) && $content_view != "") {
					echo view($content_view);
				}

				app_hooks()->do_action('app_hook_layout_main_view_extension');
				?>
			</div>
			<?php
			if ($topbar == "includes/public/topbar") {
				echo view("includes/footer");
			}
			?>

		</div>
	</div>

	<?php echo view('modal/index'); ?>
	<?php echo view('modal/confirmation'); ?>
	<?php echo view("includes/summernote"); ?>
	
<?php require 'plugins/Recruitment/assets/js/recruitment_portals/layout/index_js.php';?>


</body>
</html>