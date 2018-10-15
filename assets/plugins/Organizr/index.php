<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
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
		echo "<form id='' action='../../../api?v1/importOrganizrTabs' method='post'><table>";
		echo "<tr><th>Import</th><th>Name</th><th>Link URL</th><th>Ping URL</th><th>Image path</th></tr>";
		foreach ($tabs as $tab) {
			echo "<tr>";
			$checked = ($tab['ping'] == 1) ? "checked" : "";
			echo "<td><input type='checkbox' name='' $checked></td>";
			echo "<td><input type='text' name='' value='" . $tab['name'] . "'></td>";
			echo "<td><input type='text' name='' value='" . $tab['url'] . "'></td>";
			echo "<td><input type='text' name='' value='" . $tab['ping_url'] . "'></td>";
			$image = (strpos($tab['image'], "plugins/images/tabs/") !== false) ? strtolower(str_replace("plugins/images/tabs/", "../img/", $tab['image'])) : "";
			echo "<td><input type='text' name='' value='" . $image . "'></td>";
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

