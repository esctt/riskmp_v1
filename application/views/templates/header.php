<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="keywords" content="Risk, Risk Management, Construction, Courses, Consulting, Project, Management, Microsoft Project, MS Project, PMI, Canadian Construction Association, Building, Electrical, Mechanical, Risk costs, Contingency plans in construction, Login, Register">
        <meta name="description" content="ESCTT Inc. offers Risk Management courses, consulting and software for construction projects. Our Risk Management software, RiskMP, guides users through the risk management process in a construction project, including identification, quantification, tracking and monitoring.">		
        <title><?php echo $title ?> | RiskMP</title>
        <link rel="icon" type="image/x-icon" href="<?php echo base_url("favicon.ico") ?>" />
        <link rel="SHORTCUT ICON" type="image/x-icon" href="<?php echo base_url("favicon.ico") ?>"/>
        <link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Gafata" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url("assets/css/style.css"); ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url("assets/buttons/css/buttons.css") ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo base_url("assets/jtable/themes/jqueryui/custom/jquery-ui-1.10.3.custom.css"); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url("assets/jtable/themes/lightcolor/blue/jtable-flat.css"); ?>" rel="stylesheet" type="text/css" />
        <script src="<?php echo base_url("assets/js/jquery-1.11.0.min.js"); ?>" type="text/javascript"></script>
            <link href="<?php echo base_url("assets/css/uploadfile.css"); ?>" rel="stylesheet">    
            <script src="<?php echo base_url("assets/js/jquery.uploadfile.min.js"); ?>"></script>
        <script type="text/javascript">
            config = {
                base_url: "<?php echo base_url(); ?>"
            };
            function load_settings() {
                load_maximize();
            }
        </script>
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
    <body <?php if (!isset($hide_maximize) || !$hide_maximize) echo "onload='load_settings();'"; ?>
        style='<?php if (isset($body_background)) echo $body_background; ?>'>
<!--         Google Tag Manager 
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-WNLXV8"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-WNLXV8');</script>
         End Google Tag Manager -->
        <div class="global-wrapper">
            <div id='header' style="<?php if (isset($opacity) && $opacity) echo "opacity:0.9;" ?>">
                <?php if (!isset($hide_maximize) || !$hide_maximize) { ?>
                    <img id="img_maximize" src="<?php echo base_url('assets/images/maximize-small.png'); ?>" onclick='toggle_maximize();' alt="Maximize" height='30' width='30'>
                    <img id="img_fullscreen" src="<?php echo base_url('assets/images/fullscreen-small.png'); ?>" onclick='toggle_fullscreen();' alt="Minimize" height='30' width='30'>
                <?php } ?>
                <div id="banner" style="padding: 7px 0px;">
                    <img style="width:250px;height:135px;" width='250' height='135' src='<?php echo base_url("assets/images/logo_with_background-small.png"); ?>' alt="RiskMP Risk Management Software Background"/>
                </div>
                <div class='nav-container'>
                    <ul class="nav">
                        <li>
                            <a href="<?php echo base_url('home'); ?>">Home</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('install'); ?>">Install</a>
                        </li>
                        <?php
                        if (isset($_SESSION['username']) && $_SESSION['username'] != null) {
                            echo "<li><a href='" . base_url('dashboard') . "'>Dashboard</a></li>";
                            echo "<li><a href='" . base_url('logout') . "'>Log Out</a></li>";
                        } else {
                            echo "<li><a href='" . base_url('login') . "'>Log In</a></li>";
                            echo "<li><a href='register'>Register</a></li>";
                        }
                        ?>
                    </ul>
                </div>
            </div>