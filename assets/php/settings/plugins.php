<?php
include('../functions.php');
include('../auth_check.php');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link type="text/css" href="../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link type="text/css" href="../../css/monitorr.css" rel="stylesheet">
    <link type="text/css" href="../../data/custom.css" rel="stylesheet">

    <meta name="theme-color" content="#464646"/>
    <meta name="theme_color" content="#464646"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="../../js/monitorr.main.js"></script>
    <!-- sync config with javascript -->
    <script>
        let settings = <?php echo json_encode($GLOBALS['settings']);?>;
        let preferences = <?php echo json_encode($GLOBALS['preferences']);?>;
        let services = <?php echo json_encode($GLOBALS['services']);?>;
        let current_rflog = settings.rflog;
    </script>
    <script>
        $(function () {
            createPluginList("#plugin-list");
        });
    </script>
</head>
<body>
    <div id='plugin-list'></div>
    <div id='plugin-modal-overlay'>
        <div id='plugin-modal'></div>
    </div>
</body>
</html>
