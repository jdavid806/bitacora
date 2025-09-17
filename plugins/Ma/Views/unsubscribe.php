<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
  <title><?php echo _l('unsubscribe'); ?></title>
  <?php 
    $css_files = array(
        "plugins/Ma/assets/css/custom.css",
    );
    load_css($css_files);
   ?>
</head>
<body id="unsubscribe" class="padding-50">
  <div class="container-fluid">
      <div class="col-md-6 col-md-offset-3 form-col well">
        <div id="container" class="bg-light">
          <h4>Unsubscribed successfully</h4>       
          <p>You has been unsubscribed.</p>
          <div>
            <p>You will no longer receive emails from this site.</p>
          </div>
        </div>    
      </div>
  </div>
</body>
</html>