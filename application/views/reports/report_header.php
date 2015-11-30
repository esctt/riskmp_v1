<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo 'Report' ?> - RiskMP</title>
        <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url("assets/jtable/print-themes/jqueryui/custom/jquery-ui-1.10.3.custom.css"); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url("assets/jtable/print-themes/lightcolor/blue/jtable.css"); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url("assets/css/report.css"); ?>" rel="stylesheet" type="text/css" />
        <script src="<?php echo base_url("assets/js/jquery-1.11.0.min.js"); ?>" type="text/javascript"></script>
        <script src="<?php echo base_url("assets/js/jquery-ui.js"); ?>" type="text/javascript"></script>
        <script src="<?php echo base_url("assets/jtable/jquery.jtable.min.js"); ?>" type="text/javascript"></script>
        <script type="text/javascript">
            var project_for_cal = "<?php echo $project_data['project_id']; ?>";
            config = {
                base_url: "<?php echo base_url(); ?>"
            };
            function load_settings() {
                load_maximize();
            }
        </script>
        <script src="<?php echo base_url('assets/js/table_helper.min.js'); ?>" type="text/javascript"></script>
        <!--BEGIN GOOGLE ANALYTICS CODE -->
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-4810046-8', 'auto');
          ga('require', 'displayfeatures');
          ga('require', 'linkid', 'linkid.js');
          ga('send', 'pageview');

        </script>
        <!--END GOOGLE ANALYTICS CODE-->
    </head>
    <body style="margin-left:auto;margin-right:auto;">
        <div id='report-header' style='width:100%;'>

        </div>