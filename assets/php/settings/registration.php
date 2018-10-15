<?php
include('../functions.php');
include('../auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link type="text/css" href="../../css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="../../css/monitorr.css" rel="stylesheet">
    <link type="text/css" href="../../data/custom.css" rel="stylesheet">

    <meta name="theme-color" content="#464646"/>
    <meta name="theme_color" content="#464646"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="../../js/jquery.blockUI.js" async></script>
    <!-- <script type="text/javascript" src="../../js/pace.js" async></script> -->

    <title>
		<?php
		$title = $GLOBALS['preferences']['sitetitle'];
		echo $title . PHP_EOL;
		?>
        | Registration
    </title>

</head>

<body>

<div id="registration-content"></div>
<script>
    $(function () {
        $('#registration-content').load('../../../settings.php?action=register #registration-container');
    });
</script>
<script src="../../js/update-settings.js" async></script>

</body>

</html>