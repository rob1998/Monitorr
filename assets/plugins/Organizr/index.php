<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script>
        $(function () {
            $("#ImportTabsForm").on("submit", function (e) {
                e.preventDefault();

                let $result = [];
                $('tr.tab').each(function() {
                    let $tab = $(this).data("tab");
                    if($("input[data-tab='" + $tab + "'][name='enabled']").is(":checked")) {
                        $result.push({
                            serviceTitle: $("input[data-tab='" + $tab + "'][name='serviceTitle']").val(),
                            enabled: "Yes",
                            image: $("input[data-tab='" + $tab + "'][name='image']").val(),
                            type: "Ping Only",
                            link: "Yes",
                            linkurl: $("input[data-tab='" + $tab + "'][name='linkurl']").val(),
                            checkurl: $("input[data-tab='" + $tab + "'][name='pingurl']").val()
                        });
                    }
                });

                console.log({'services': $result});
                $.ajax({
                    type: "POST",
                    url: "../../../api?v1/settings/update",
                    data: {'services': $result},
                    dataType: "json",
                    success: function (response) {
                        console.log("Succesfully imported services:");
                        console.log($result);
                        //TODO: toast notification here
                    }
                });
            });
        });
    </script>
</head>
<body>
<h1>Import Organizr Tabs</h1>
<?php
include(__DIR__ . "/../../php/functions.php");

$pluginInfo = json_decode(file_get_contents(__DIR__ . '/info.json'), 1);
if(!isset($pluginInfo['settings']) || (isset($GLOBALS['plugins']['Organizr']) && isset($pluginInfo['settings']))) {
	$organizrAPI = $GLOBALS['plugins']['Organizr']['organizrAPI'];
	$organizrURL = rtrim($GLOBALS['plugins']['Organizr']['organizrURL'], "/");
	$tabList = json_decode(file_get_contents($organizrURL . "/api/?v1/tab_list&apikey=" . $organizrAPI), true);
	if (isset($tabList['data']) && !empty($tabList['data']) && isset($tabList['data']['tabs'])) {
		$tabs = $tabList['data']['tabs'];
		echo "<form id='ImportTabsForm'><table>";
		echo "<tr><th>Import</th><th>Name</th><th>Link URL</th><th>Ping URL</th><th>Image path</th></tr>";
		foreach ($tabs as $tab) {
			echo "<tr class='tab' data-tab='" . $tab['name'] . "'>";
			$checked = ($tab['ping'] == 1) ? "checked" : "";
			echo "<td><input data-tab='" . $tab['name'] . "' type='checkbox' name='enabled' $checked></td>";
			echo "<td><input data-tab='" . $tab['name'] . "' type='text' name='serviceTitle' value='" . $tab['name'] . "'></td>";
			echo "<td><input data-tab='" . $tab['name'] . "' type='text' name='linkurl' value='" . $tab['url'] . "'></td>";
			echo "<td><input data-tab='" . $tab['name'] . "' type='text' name='pingurl' value='" . $tab['ping_url'] . "'></td>";
			$image = (strpos($tab['image'], "plugins/images/tabs/") !== false) ? strtolower(str_replace("plugins/images/tabs/", "../img/", $tab['image'])) : "";
			echo "<td><input data-tab='" . $tab['name'] . "' type='text' name='image' value='" . $image . "'></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<button type='submit'>Submit</button>";
		echo "</form>";
	} else {
		echo "No valid response received. Response:<br>";
		echo $tabList['status'] . ": " . $tabList['statusText'];
		var_dump($organizrURL . "/api/?v1/tab_list&apikey=" . $organizrAPI);
		var_dump(file_get_contents($organizrURL . "/api/?v1/tab_list&apikey=" . $organizrAPI));
		var_dump($tabList);
	}
} else {
	echo "No settings were found.";
}
?>
</body>
</html>

