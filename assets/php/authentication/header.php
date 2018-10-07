<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">

<!--
                LOGARR
    by @seanvree, @jonfinley, and @rob1998
        https://github.com/Monitorr
-->

<head>

    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="webmanifest.json">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico"/>
    <link rel="apple-touch-icon" href="favicon.ico">

    <meta name="Monitorr" content="Monitorr: Self-hosted, single-page, log consolidation tool."/>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/monitorr.css"/>
    <link rel="stylesheet" href="assets/data/custom.css"/>

    <meta name="robots" content="NOINDEX, NOFOLLOW">

    <title><?php echo $GLOBALS['preferences']['sitetitle']; ?></title>

    <script src="assets/js/jquery.min.js"></script>

    <script src="assets/js/pace.js" async></script>

    <script src="assets/js/jquery.blockUI.js"></script>

    <script src="assets/js/jquery.highlight.js" async></script>

    <script src="assets/js/monitorr.main.js"></script>
    <script src="assets/js/jquery.mark.min.js" async></script>

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

</head>

<body id="body" style="color: #FFFFFF;">

<div id="ajaxtimestamp" title="Analog clock timeout. Refresh page."></div>

<div class="header">

    <div id="left" class="Column">

        <div id="clock">
            <canvas id="canvas" width="120" height="120"></canvas>
            <div class="dtg" id="timer"></div>
        </div>

    </div>

    <!-- CHANGE ME // REMOVE "ON CLICK"? -->

    <div id="logo" class="Column">
        <img src="assets/img/monitorr.png" alt="Monitorr" title="Reload Monitorr" onclick="window.location.reload(true);">
    </div>

    <div id="right" class="Column">
    </div>

</div>
