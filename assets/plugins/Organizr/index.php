<?php
require(__DIR__ . "/../../php/functions.php");

$pluginInfo = json_decode(file_get_contents(__DIR__ . '/info.json'), 1);
if(!isset($pluginInfo['settings']) || (isset($GLOBALS['configJSON']['plugins']['Organizr']) && isset($pluginInfo['settings']))) {
	$organizrAPI = $GLOBALS['configJSON']['plugins']['Organizr']['organizrAPI'];
	$organizrURL = rtrim($GLOBALS['configJSON']['plugins']['Organizr']['organizrURL'], "/");
	$tabList = json_decode(file_get_contents($organizrURL . "/api/?v1/tab_list&apikey=" . $organizrAPI), true);
	if (isset($tabList['data']) && !empty($tabList['data']) && isset($tabList['data']['tabs'])) {
		$tabs = $tabList['data']['tabs'];
		echo "<form id='' action='../../../api?v1/importOrganizrTabs' method='post'><table>";
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
		echo "<button tyep='submit'>Submit</button>";
		echo "</form>";
	} else {
		echo "No valid response received. Response:<br>";
		echo $tabList['status'] . ": " . $tabList['statusText'];
	}
} else {
	echo "No settings were found.";
}