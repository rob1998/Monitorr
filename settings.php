<?php
include('assets/php/functions.php');
include('assets/php/auth_check.php');
?>

<!DOCTYPE html>
<html lang="en">

<!--
     Monitorr | settings page
https://github.com/Monitorr/Monitorr
-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="manifest" href="webmanifest.json">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
    <link rel="apple-touch-icon" href="favicon.ico">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Monitorr">

    <link type="text/css" href="assets/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-powertip/1.2.0/css/jquery.powertip.min.css" integrity="sha256-tQC/8JHEz1GGG+MXJ7gWQ1UaLCp7DbL/ziBr1mCgrkc=" crossorigin="anonymous" />
    <link type="text/css" href="assets/css/monitorr.css" rel="stylesheet">
    <link type="text/css" href="assets/data/custom.css" rel="stylesheet">

    <meta name="theme-color" content="#464646"/>
    <meta name="theme_color" content="#464646"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-powertip/1.2.0/jquery.powertip.min.js" integrity="sha256-RuTci6MrGVbYOF2ZVUDL4c66qw3KHrU+YYgX8ATuGIw=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="assets/js/pace.js" async></script>
    <script src="assets/js/monitorr.main.js"></script>
    <!-- <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script> -->



    <!-- sync config with javascript -->
    <script>
        let settings = <?php echo json_encode($GLOBALS['settings']);?>;
        let preferences = <?php echo json_encode($GLOBALS['preferences']);?>;
        let services = <?php echo json_encode($GLOBALS['services']);?>;
        let current_rflog = settings.rflog;
    </script>

    <!-- UI clock functions: -->
    <script>
		<?php
		//initial values for clock:
		$timezone = $GLOBALS['preferences']['timezone'];
		$dt = new DateTime("now", new DateTimeZone("$timezone"));
		$timeStandard = (int)($GLOBALS['preferences']['timestandard']);
		$rftime = $GLOBALS['settings']['rftime'];
		$timezone_suffix = '';
		if (!$timeStandard) {
			$dateTime = new DateTime();
			$dateTime->setTimeZone(new DateTimeZone($timezone));
			$timezone_suffix = $dateTime->format('T');
		}
		$serverTime = $dt->format("D d M Y H:i:s");
		?>
        let serverTime = "<?php echo $serverTime;?>";
        let timeStandard = <?php echo $timeStandard;?>;
        let timeZone = "<?php echo $timezone_suffix;?>";
        let rftime = <?php echo $GLOBALS['settings']['rftime'];?>;
    </script>

    <title>
		<?php
		echo $preferences['sitetitle'];
		?>
        | Settings
    </title>

    <script>
        $(function () {
            switch (window.location.hash) {
                case "#user-preferences":
                    load_preferences();
                    break;
                case "#monitorr-settings":
                    load_settings();
                    break;
                case "#monitorr-authentication":
                    load_authentication();
                    break;
                case "#services-configuration":
                    load_services();
                    break;
                case "#registration":
                    load_registration();
                    break;
                case "#plugins":
                    load_plugins();
                    break;
                default:
                    load_info();
            }
        });
    </script>

    <script src="assets/data/custom.js"></script>
    <script src="assets/js/clock.js" async></script>
</head>

<body>

<script>
    document.body.className += ' fade-out';
    $(function () {
        $('body').removeClass('fade-out');
    });
</script>

<div id="settingscolumn" class="settingscolumn">

    <div id="settingsbrand">
        <div class="navbar-brand">
			<?php
			echo $preferences['sitetitle'];
			?>
        </div>
    </div>

    <div id="summary"></div>

    <div class="Column left">
        <div id="clock">
            <canvas id="canvas" width="120" height="120"></canvas>
            <div class="dtg" id="timer"></div>
        </div>
    </div>

    <div id="wrapper" class="left">

        <!-- Sidebar -->
        <nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">

            <div class="settingstitle">
                Settings
            </div>

            <ul class="nav sidebar-nav">

                <li class="sidebar-nav-item" data-item="info">
                    <a href="#info" onclick="load_info()"><i class="fa fa-fw fa-info"></i>Info</a>
                </li>
                <li class="sidebar-nav-item" data-item="user-preferences">
                    <a href="#user-preferences" onclick="load_preferences()"><i class="fa fa-fw fa-user"></i>User Preferences</a>
                </li>
                <li class="sidebar-nav-item" data-item="monitorr-settings">
                    <a href="#monitorr-settings" onclick="load_settings()"><i class="fa fa-fw fa-cog"></i>Monitorr Settings</a>
                </li>
                <li class="sidebar-nav-item" data-item="monitorr-authentication">
                    <a href="#monitorr-authentication" onclick="load_authentication()"><i class="fa fa-fw fa-lock"></i>Authentication</a>
                </li>
                <li class="sidebar-nav-item" data-item="services-configuration">
                    <a href="#services-configuration" onclick="load_services()"><i class="fa fa-fw fa-book"></i>Services Configuration</a>
                </li>
                <li class="sidebar-nav-item" data-item="registration">
                    <a href="#registration" onclick="load_registration()"><i class="fas fa-user-plus"></i>Registration</a>
                </li>
                <li class="sidebar-nav-item" data-item="plugins">
                    <a href="#plugins" onclick="load_plugins()"><i class="fas fa-user-plus"></i>Plugins</a>
                </li>
				<?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_is_logged_in']) && !empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) { ?>
                    <li class="sidebar-nav-item" data-item="log-out">
                        <a href="settings.php?action=logout"><i class="fas fa-sign-out-alt"></i>Log Out</a>
                    </li>
				<?php } ?>
                <li class="sidebar-nav-item" data-item="monitorr">
                    <a href="index.php"><i class="fa fa-fw fa-home"></i>Monitorr</a>
                </li>

            </ul>

        </nav>

    </div>

    <div id="version">

        <script src="assets/js/update.js" async></script>

        <p><a class="footer a" href="https://github.com/monitorr/Monitorr" target="_blank" title="Monitorr Repo">
                Monitorr </a> | <a class="footer a" href="https://github.com/Monitorr/monitorr/releases" target="_blank"
                                 title="Monitorr Releases"> <?php echo file_get_contents("assets/js/version/version.txt"); ?> </a>
        </p>

        <div id="version_check_auto"></div>

        <div id="reginfo">

			<?php

			if (!configExists()) {
				echo "Config file NOT present";
			} else {
				echo 'Config file present';
			}

			?>

        </div>

    </div>

</div>
<div class="settings-title">
    <div id="setttings-page-title" class="navbar-brand">
    </div>
</div>
<div id="includedContent">

    <script>
    </script>

</div>

<div id="footer" class="settings-footer">

    <!-- Checks for Monitorr application update on page load & "Check for update" click: -->
    <script src="assets/js/update.js" async></script>

    <div id="monitorrid">
        <a href="https://github.com/monitorr/monitorr" title="Monitorr GitHub repo" target="_blank"
           class="footer">Monitorr </a> |
        <a href="https://github.com/Monitorr/monitorr/releases" title="Monitorr releases" target="_blank" class="footer">
            Version: <?php echo file_get_contents("assets/js/version/version.txt"); ?></a> |
        <a href="settings.php" title="Monitorr Settings" target="_blank" class="footer">Settings</a>
		<?php if (isset($_SESSION['user_name']) && isset($_SESSION['user_is_logged_in']) && !empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) {
			echo " | <a href='index.php?action=logout' title='Log out' class='footer'></i>Logout</a>";
		} ?>
        <br>
    </div>

</div>
</body>

</html>
