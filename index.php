<?php
include('assets/php/functions.php');
include('assets/php/auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">

<!--
__  __             _ _
|  \/  |           (_) |
| \  / | ___  _ __  _| |_ ___  _ __ _ __
| |\/| |/ _ \| '_ \| | __/ _ \| '__| '__|
| |  | | (_) | | | | | || (_) | |  | |
|_|  |_|\___/|_| |_|_|\__\___/|_|  |_|
		made for the community
by @seanvree, @wjbeckett, @rob1998 and @jonfinley
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
    <meta name="author" content="Monitorr">
    <meta name="version" content="php">
    <meta name="theme-color" content="#464646"/>
    <meta name="theme_color" content="#464646"/>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles -->
    <link href="assets/css/monitorr.css" rel="stylesheet">
    <link href="assets/data/custom/custom.css" rel="stylesheet">

    <script src="assets/js/jquery.min.js"></script>

    <!-- top loading bar function: -->
    <script src="assets/js/pace.js"></script>

    <script src="assets/js/monitorr.main.js"></script>

    <title><?php echo $GLOBALS['preferences']['sitetitle']; ?></title>


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

    <script src="assets/js/clock.js"></script>
    <script src="assets/data/custom.js"></script>

    <!-- services status update function: -->
    <script type="text/javascript">
        $(document).ready(function () {
            $(":checkbox").change(function () {
                if ($(this).is(':checked')) {
                    statusCheck();
                }
            });
        });
    </script>

    <!-- marquee offline function: -->
    <script>

        var nIntervId2;
        var onload;

        $(document).ready(function () {

            $(":checkbox").change(function () {

                var current = -1;
                var onload;

                function updateSummary() {

                    console.log('Service offline check START');

                    rfsysinfo =
					<?php
						$rfsysinfo = $GLOBALS['settings']['rfsysinfo'];
						echo $rfsysinfo;
						?>

                        $.ajax({
                            type: 'POST',
                            url: 'assets/php/marquee.php',
                            data: {
                                current: current
                            },

                            timeout: 7000,
                            success: function (data) {
                                if (data) {
                                    result = $.parseJSON(data);
                                    console.log(result);
                                    $("#summary").fadeOut(function () {
                                        $(this).html(result[0]).fadeIn();
                                    });
                                    current = result[1];
                                }

                                else {
                                    current = -1;
                                    $("#summary").hide();
                                }
                            },
                            error: function (x, t, m) {
                                if (t === "timeout") {
                                    console.log("ERROR: marquee timeout");
                                    $('#ajaxmarquee').html('<i class="fa fa-fw fa-exclamation-triangle"></i>');
                                } else {
                                }
                            }
                        });
                }

                if ($(this).is(':checked')) {
                    updateSummary();
                    nIntervId2 = setInterval(updateSummary, rfsysinfo);
                    console.log("Auto refresh: Enabled | Interval: <?php echo $rfsysinfo; ?> ms");
                } else {
                    clearInterval(nIntervId2);
                    console.log("Auto refresh: Disabled");
                }
            });
            $('#buttonStart :checkbox').attr('checked', 'checked').change();
        });

    </script>

</head>

<body onload="showpace()" class="fade-out">


<!-- Append marquee alert if service is down: -->
<div id="summary"></div>

<!-- Ajax timeout indicator: -->
<div id="ajaxtimeout">

    <div id="ajaxtimestamp" title="Analog clock timeout. Refresh page."></div>
    <div id="ajaxmarquee" title="Offline marquee timeout. Refresh page."></div>

</div>

<div id="header">

    <div id="left" class="Column">
        <div id="clock">
            <canvas id="canvas" width="120" height="120"></canvas>
            <!-- <div class="dtg"></div> -->
            <div id="timer"></div>
        </div>
    </div>

    <div id="center">

        <div id="centertext">
            <a class="navbar-brand" href="
                        <?php
			echo $GLOBALS['preferences']['siteurl'];
			?>">
				<?php
				echo $GLOBALS['preferences']['sitetitle'];
				?>
            </a>
        </div>

        <div id="toggle">
            <table id="slidertable">
                <tr title="Toggle auto-refresh. Interval: <?php echo $rfsysinfo; ?> ms ">
                    <th id="textslider">
                        Auto Refresh:
                    </th>
                    <th id="slider">
                        <label class="switch" id="buttonStart">
                            <input type="checkbox">
                            <span class="slider round"></span>
                        </label>
                    </th>
                </tr>
            </table>
        </div>

    </div>

    <div id="right" class="Column">

        <div id="stats" class="container centered">

            <div id="cpu" class="col-md-2 col-centered double-val-label">
                <span class="">CPU</span>
                <span class="value">%</span>
            </div>

            <div id="ram" class="col-md-2 col-centered double-val-label">
                <span class="">RAM</span>
                <span class="value">%</span>
            </div>

            <div id="uptime" class="col-md-2 col-centered double-val-label">
                <span class="primary">uptime</span>
                <span class="value"></span>
            </div>

            <div id="ping" class="col-md-2 col-centered double-val-label">
                <span class="">ping</span>
                <span class="value"> ms</span>
            </div>

            <div id="hd" class="col-md-2 col-centered double-val-label">
                <span id='hdlabel1' <?php if(!isset($settings['disk1'])) echo "style='display:none'";?>>
                    HD
                </span>
                <span id='hdpercent1' <?php if(!isset($settings['disk1'])) echo "style='display:none'";?>>
                    %
                </span>
                <span id='hdlabel2' <?php if(!isset($settings['disk2'])) echo "style='display:none'";?>>
                    HD
                </span>
                <span id='hdpercent2' <?php if(!isset($settings['disk2'])) echo "style='display:none'";?>>
                    %
                </span>
                <span id='hdlabel3' <?php if(!isset($settings['disk3'])) echo "style='display:none'";?>>
                    HD
                </span>
                <span id='hdpercent3' <?php if(!isset($settings['disk3'])) echo "style='display:none'";?>>
                    %
                </span>
            </div>

        </div>

    </div>

</div>

<!-- Loading modal indicator:
<div id="modalloadingindex" class="modalloadingindex" title="Monitorr is checking services.">

    <p class="modaltextloadingindex">Monitorr is loading ...</p>

</div>
 -->

<div id="services" class="container">

    <div class="row">
        <div id="statusloop">
            <?php
                foreach ($services as $key => $service) {
	                if ($service['enabled'] == "Yes") {
                        ?>
		                <div id="service-<?php echo str_replace(" ", "-", $service['serviceTitle']);?>"  class="col-lg-4" >
                            <div id="pingindicator-<?php echo str_replace(" ", "-", $service['serviceTitle'])?>" class="pingindicator">
                                <div class="pingcircle"></div>
                            </div>

                            <div class="servicetile <?php if ($service['link'] == "No") echo "nolink";?>" data-location="<?php echo $service['linkurl'];?>" style="display: block">

                                <img id="<?php echo str_replace(" ", "-", $service['serviceTitle']);?>-service-img" src="assets/img/<?php echo $service['image'];?>" class="serviceimg" alt='<?php echo str_replace(" ", "-", $service['serviceTitle']);?>'>
                                <div class="servicetitle">
                                    <?php echo $service['serviceTitle'];?>
                                </div>
                                <div id="status-<?php echo str_replace(" ", "-", $service['serviceTitle']);?>">Loading</div>
                            </div>
                        </div>
                    <?php
	                } else {
		                // Remove offline log file if disabled://
		                $servicefile = ($service['serviceTitle']) . '.offline.json';
		                $fileoffline = '../data/logs/' . $servicefile;

		                if (is_file($fileoffline)) {
			                rename($fileoffline, '../data/logs/offline.json.old');
		                }
	                }
                }
            ?>
            <!-- loop data goes here -->
        </div>
    </div>

</div>

<div id="footer">

    <script src="assets/js/update_auto.js" async></script>

    <div id="settingslink">
        <a class="footer a" href="settings.php" target="_blank" title="Monitorr Settings"><i class="fa fa-fw fa-cog"></i>Monitorr Settings </a>
    </div>

    <p><a class="footer a" href="https://github.com/monitorr/Monitorr" target="_blank" title="Monitorr Repo"> Monitorr </a> | <a class="footer a" href="https://github.com/Monitorr/Monitorr/releases" target="_blank" title="Monitorr Releases"> <?php echo file_get_contents("assets/js/version/version.txt"); ?> </a></p>

    <div id="version_check_auto"></div>

</div>

</body>

</html>