<head>
    <?php echo view('Recruitment\Views\\recruitment_portal\layout\includes/meta'); ?>
    <?php echo view('Recruitment\Views\\recruitment_portal\layout\includes/helper_js'); ?>
    <?php echo view('Recruitment\Views\\recruitment_portal\layout\includes/plugin_language_js'); ?>

    <?php
    $Users_model = model('Recruitment\Models\Candidates_model');
    $login_user_id = $Users_model->login_user_id();
    $personal_rtl_support = get_setting('user_' . $login_user_id . '_personal_rtl_support');
    ?>

    <?php
    //We'll merge all css and js into sigle files. If you want to use the css separately, you can use it.

    $css_files = array(
        "assets/bootstrap/css/bootstrap.min.css",
        "assets/js/select2/select2.css", //don't combine this css because of the images path
        "assets/js/select2/select2-bootstrap.min.css",
        "assets/css/app.all.css",
    );

    if ((get_setting("rtl") && !$personal_rtl_support) || $personal_rtl_support == "yes") {
        array_push($css_files, "assets/css/rtl.css");
    }

    array_push($css_files, "assets/css/custom-style.css"); //add to last. custom style should not be merged

    load_css($css_files);

    load_js(array(
        "assets/js/app.all.js"
    ));
    ?>

    <?php echo view("Recruitment\Views\\recruitment_portal\layout\includes/csrf_ajax"); ?>
    
    <?php app_hooks()->do_action('app_hook_head_extension'); ?>
    
    <?php echo view("includes/custom_head"); ?>

</head>