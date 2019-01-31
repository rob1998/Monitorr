<?php
include(__DIR__ . '/classes/Ping.php');
if (!is_file(__DIR__ . '../../data/datadir.json')
	|| file_get_contents(__DIR__ . '../../data/datadir.json') == ""
	|| !key_exists("datadir", json_decode(file_get_contents(__DIR__ . '../../data/datadir.json'), 1))
) {
	//invalid/unset datadir, start configuration process
	$_GET['action'] = 'config';
	include_once(__DIR__ . '/auth_check.php');
	exit();
}
$datadir = json_decode(file_get_contents(__DIR__ . '../../data/datadir.json'), 1)["datadir"];
if(!is_file($datadir . 'config.json')
	&& is_file($datadir . 'user_preferences-data.json')) {
	//no new config found, but old config. Attempt upgrade
	updateConfig($datadir);
}

//easy settings variables
$config_file = $datadir . '/config.json';
$configJSON = file_get_contents($config_file);
$preferences = json_decode($configJSON, 1)['preferences'];
$settings = json_decode($configJSON, 1)['settings'];
$services = json_decode($configJSON, 1)['services'];
$plugins = isset(json_decode($configJSON, 1)['plugins']) ? json_decode($configJSON, 1)['plugins'] : array();
$authentication = json_decode($configJSON, 1)['authentication'];

global $configJSON, $preferences, $settings, $services, $authentication;

//authentication
$monitorrAPI = $authentication['apikey'];
session_start();

// New version download information
$branch = $preferences['updateBranch'];


// location to download new version zip
$remote_file_url = 'https://github.com/Monitorr/Monitorr/zipball/' . $branch . '';
// rename version location/name
$local_file = '../../tmp/monitorr-' . $branch . '.zip'; #example: version/new-version.zip
//
// version check information
//
// url to external verification of version number as a .TXT file
$ext_version_loc = 'https://raw.githubusercontent.com/Monitorr/Monitorr/' . $branch . '/assets/js/version/version.txt';
// users local version number
// added the 'uid' just to show that you can verify from an external server the
// users information. But it can be replaced with something more simple
$vnum_loc = "../js/version/version.txt"; #example: version/vnum_1.txt

function checkAuthorization(){
    return ((!empty($_SESSION['user_name']) && ($_SESSION['user_is_logged_in'])) || (isset($_GET['apikey']) && ($_GET['apikey'] == $GLOBALS['monitorrAPI'])));
}

function sortServicesCustom($orderArray) {
	$newServicesOrder = array();
	foreach ($orderArray as $newKey => $oldKey) {
		$newServicesOrder[$newKey] = $GLOBALS["services"][$oldKey];
	}
	$config = json_decode($GLOBALS['configJSON'],1);
	$config["services"] = $newServicesOrder;
	return file_put_contents($GLOBALS['config_file'], json_encode($config, JSON_PRETTY_PRINT));
}

function sortServicesAlphabetically() {
	$config = json_decode($GLOBALS['configJSON'],1);
	$servicesArr = $GLOBALS["services"];
	usort($servicesArr, "sortServicesByName");
	$config["services"] = $servicesArr;
	return file_put_contents($GLOBALS['config_file'], json_encode($config, JSON_PRETTY_PRINT));
}

function getOfflineServices() {
	$files = glob(__DIR__ . "/../data/logs/*.json");
	$servicesNames = array();
	foreach($GLOBALS['services'] as $service) {
		$servicesNames[] = $service['serviceTitle'];
	}
	$result = array();
	foreach($files as $file){
		if(!in_array(str_replace(".json", "", basename($file)), $servicesNames)){
			unlink($file);
		} else {
			$result[] = ucfirst(file_get_contents ($file));
		}
	}
	return $result;
}

function updateSettings($config) {
	$oldConfig = json_decode($GLOBALS['configJSON'],1);
	$GLOBALS['configJSON'] = array_merge_recursive_distinct($oldConfig, $config);
	return file_put_contents($GLOBALS['config_file'], json_encode($GLOBALS['configJSON'], JSON_PRETTY_PRINT)) === strlen(json_encode($GLOBALS['configJSON'], JSON_PRETTY_PRINT));
}

//<editor-fold desc="Form Builder">
function createFormInput($title, $name, $value, $type, $options, $tooltip, $extraClass) {
	$result = "";
	$result .= "<div class='field' title='$tooltip'>";
	$result .= "<label class='label' for='$name'>$title: </label>";
	$result .= "<div class='control'>";
	switch ($type) {
		case "color":
		case "date":
		case "datetime":
		case "datetimelocal":
		case "email":
		case "fileupload":
		case "hidden":
		case "image":
		case "month":
		case "number":
		case "password":
		case "range":
		case "radio":
		case "reset":
		case "search":
		case "submit":
		case "text":
		case "time":
		case "url":
		case "week":
			$result .= "<input id='$name' class='input form-control $extraClass' name='$name' type='$type' value='$value'>";
			break;
		case "checkbox":
			$checked = $value == "true" ? "checked" : "";
			$result .= "<input id='$name' class='onoffswitch-checkbox form-control $extraClass' name='$name' type='$type' $checked>";
			$result .= "<label class='onoffswitch-label' for='$name'>";
			$result .= "<span class='onoffswitch-inner'></span>";
			$result .= "</label>";
			break;
		case "select":
			$result .= "<div class='select'>";
			$result .= "<select id='$name' class='form-control $extraClass' name='$name' value='$value'>";
			foreach ($options as $option) {
				$result .= "<option value='$option'>$option</option>";
			}
			$result .= "</select>";
			$result .= "</div>";
			break;
		default:
			break;
	}
	$result .= "</div>";
	$result .= "</div>";
	return $result;
}

function createPluginSettingsForm($plugin){
	$result = "";

	$infoFilePath = __DIR__ . "/../plugins/" . $plugin . "/info.json";
	if(is_file($infoFilePath)) {
		$pluginInfo = json_decode(file_get_contents($infoFilePath), true);
		$pluginSettings = (isset($GLOBALS['configJSON']['plugins'][$plugin]) && isset($pluginInfo['settings'])) ? $GLOBALS['configJSON']['plugins'][$plugin] : array();

		$result .= "<form id='plugin-settings' data-plugin='$plugin' method='post'>";
		$result .= "<div class='flex'>";
		foreach ($pluginInfo['settings'] as $settingName => $settingsProperties) {
			$result .= "<div class='form-group'>";

			if(!isset($pluginSettings[$settingName])) {
				$pluginSettings[$settingName] = $settingsProperties['default'];
			}

			$options = isset($settingsProperties['options']) ? $settingsProperties['options'] : "";
			$title = isset($settingsProperties['description'])? $settingsProperties['description'] : $settingName;
			$tooltip = isset($settingsProperties['tooltip']) ? $settingsProperties['tooltip'] : "";
			$setting = isset($GLOBALS['plugins'][$plugin][$settingName]) ? $GLOBALS['plugins'][$plugin][$settingName] : $pluginSettings[$settingName];
			$result .= createFormInput($title, $settingName, $setting, $settingsProperties['type'], $options, $tooltip, "");

			$result .= "</div>";
		}
		$result .= "</div>";
		$result .= "<button type='submit' class='btn btn-center'>Submit</button>";
		$result .= "</form>";

	} else {
		$result = "ERROR: no info file found for plugin '$plugin'";
	}
	$result .= "<script>$('.field').powerTip({
					placement: 's'
				});</script>";
	return $result;
}
//</editor-fold>

function getPlugins(){
	$result = array();
	$plugins = scandir(__DIR__ . "/../plugins");
	foreach ($plugins as $pluginFolder) {
		$infoFilePath = __DIR__ . "/../plugins/" . $pluginFolder . "/info.json";
		if(is_file($infoFilePath)) {
			$pluginInfo = json_decode(file_get_contents($infoFilePath), true);
			$pluginInfo['name'] = $pluginFolder;
			$result[] = $pluginInfo;
		}
	}
	return $result;
}

//Ping function
function ping($pingUrl)
{
	$ping = new Ping("");
	$ping->setTtl(128);
	$ping->setTimeout(2);
	$pings = url_to_domain($pingUrl);
	if (strpos($pings, ':') !== false) {
		$domain = explode(':', $pings)[0];
		$port = explode(':', $pings)[1];
		$ping->setHost($domain);
		$ping->setPort($port);
		$latency = $ping->ping('fsockopen');
	} else {
		$ping->setHost($pings);
		$latency = $ping->ping();
	}
	if ($latency || $latency === 0) {
		$results = $latency;
	} else {
		$results = false;
	}
	return $results;
}

//<editor-fold desc="Server Status Functions">
// get CPUload function
function _getServerLoadLinuxData()
{
    if (is_readable("/proc/stat"))
    {
        $stats = @file_get_contents("/proc/stat");

        if ($stats !== false)
        {
            // Remove double spaces to make it easier to extract values with explode()
            $stats = preg_replace("/[[:blank:]]+/", " ", $stats);

            // Separate lines
            $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
            $stats = explode("\n", $stats);

            // Separate values and find line for main CPU load
            foreach ($stats as $statLine)
            {
                $statLineData = explode(" ", trim($statLine));

                // Found!
                if
                (
                    (count($statLineData) >= 5) &&
                    ($statLineData[0] == "cpu")
                )
                {
                    return array(
                        $statLineData[1],
                        $statLineData[2],
                        $statLineData[3],
                        $statLineData[4],
                    );
                }
            }
        }
    }

    return null;
}

// Returns server load in percent (just number, without percent sign)
function getServerLoad()
{
    $load = null;

    if (stristr(PHP_OS, "win"))
    {
        $cmd = "wmic cpu get loadpercentage /all";
        @exec($cmd, $output);

        if ($output)
        {
            foreach ($output as $line)
            {
                if ($line && preg_match("/^[0-9]+\$/", $line))
                {
                    $load = $line;
                    break;
                }
            }
        }
    }
    else
    {
        if (is_readable("/proc/stat"))
        {
            // Collect 2 samples - each with 1 second period
            // See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
            $statData1 = _getServerLoadLinuxData();
            sleep(1);
            $statData2 = _getServerLoadLinuxData();

            if
            (
                (!is_null($statData1)) &&
                (!is_null($statData2))
            )
            {
                // Get difference
                $statData2[0] -= $statData1[0];
                $statData2[1] -= $statData1[1];
                $statData2[2] -= $statData1[2];
                $statData2[3] -= $statData1[3];

                // Sum up the 4 values for User, Nice, System and Idle and calculate
                // the percentage of idle time (which is part of the 4 values!)
                $cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];

                // Invert percentage to get CPU time, not idle time
                $load = 100 - ($statData2[3] * 100 / $cpuTime);
            }
        }
    }

    return round($load, 2);
}

// getRAM function
function getRamTotal()
{
    $result = 0;
    if (PHP_OS == 'WINNT') {
        $lines = null;
        $matches = null;
        exec('wmic ComputerSystem get TotalPhysicalMemory /Value', $lines);
        if (preg_match('/^TotalPhysicalMemory\=(\d+)$/', $lines[2], $matches)) {
            $result = $matches[1];
        }
    } else {
        $fh = fopen('/proc/meminfo', 'r');
        while ($line = fgets($fh)) {
            $pieces = array();
            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                $result = $pieces[1];
                // KB to Bytes
                $result = $result * 1024;
                break;
            }
        }
        fclose($fh);
    }
    // KB RAM Total
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return (double) $result;
	} else {
    return (int) $result;
	}
}

function getRamFree()
{
    $result = 0;
    if (PHP_OS == 'WINNT') {
        $lines = null;
        $matches = null;
        exec('wmic OS get FreePhysicalMemory /Value', $lines);
        if (preg_match('/^FreePhysicalMemory\=(\d+)$/', $lines[2], $matches)) {
            $result = $matches[1] * 1024;
        }
    } else {
        $fh = fopen('/proc/meminfo', 'r');
        while ($line = fgets($fh)) {
            $pieces = array();
            if (preg_match('/^MemAvailable:\s+(\d+)\skB$/', $line, $pieces)) {
                // KB to Bytes
                $result = $pieces[1] * 1024;
                break;
            }
        }
        fclose($fh);
    }
    // KB RAM Total
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		return (double) $result;
	} else {
    return (int) $result;
	}
}

function getRamPercentage(){
	$usedRam = getRamTotal() - getRamFree();
	$ramPercent = round(($usedRam / getRamTotal()) * 100);
	return $ramPercent;
}

// getHD function
function getHDFree($hdd) {
    if(isset($GLOBALS['settings'][$hdd])) {
		$disk = $GLOBALS['settings'][$hdd];
	    //hdd stat

	    $stat['hdd_free'] = round(disk_free_space($disk) / 1024 / 1024 / 1024, 2);

	    $stat['hdd_total'] = round(disk_total_space($disk) / 1024 / 1024/ 1024, 2);

	    $stat['hdd_used'] = $stat['hdd_total'] - $stat['hdd_free'];
	    $stat['hdd_percent'] = round(sprintf('%.1f',($stat['hdd_used'] / $stat['hdd_total']) * 100), 2);
	    $stat['hdd_percent'];

	    return  $stat['hdd_percent'];
    }
    return false;
}

//uptime
function getTotalUptime(){
    $uptime = shell_exec("cut -d. -f1 /proc/uptime");
    $days = floor($uptime) / 60 / 60 / 24;
    $days_padded = sprintf("%02d", $days);
    $hours = round($uptime) / 60 / 60 % 24;
    $hours_padded = sprintf("%02d", $hours);
    $mins = round($uptime) / 60 % 60;
    $mins_padded = sprintf("%02d", $mins);
    $secs = round($uptime) % 60;
    $secs_padded = sprintf("%02d", $secs);
    return "$days_padded:$hours_padded:$mins_padded";
}
//</editor-fold>


function configExists()
{
	return is_file($GLOBALS['config_file']);
}

//<editor-fold desc="Helper Functions">
function url_to_domain($url) {
	$host = parse_url($url, PHP_URL_HOST);
	$port = parse_url($url, PHP_URL_PORT);
	$path = parse_url($url, PHP_URL_PATH);

	$result = $host;
	$result .= empty($port) ? "" : ":" . $port;
	$result .=  rtrim($path, '/');;
	return $result;
}

function recurse_copy($src,$dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recurse_copy($src . '/' . $file,$dst . '/' . $file);
			}
			else {
				copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

function delTree($dir) {
	$files = array_diff(scandir($dir), array('.','..'));
	foreach ($files as $file) {
		(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
	}
	return rmdir($dir);
}

function array_merge_recursive_distinct (array & $array1, array & $array2)
{
	$merged = $array1;

	foreach ($array2 as $key => & $value)
	{
		if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
			if($key == "services"){
				foreach ($value as $r_value) {
					if(!in_array($r_value, $merged[$key])){
						$merged[$key][] = $r_value;
					}
				}
			} else {
				$merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
			}
		} else if (is_numeric($key)) {
			if (!in_array($value, $merged)) $merged[] = $value;
		} else {
			$merged[$key] = $value;
		}
	}

	return $merged;
}

function sortServicesByName($service1, $service2) {
	return strcmp(strtolower($service1["serviceTitle"]), strtolower($service2["serviceTitle"]));
}
//</editor-fold>

function getServerTime() {
	$timezone = $GLOBALS['preferences']['timezone'];
	$timestandard = (int) ($GLOBALS['preferences']['timestandard'] === "True" ? true:false);
	date_default_timezone_set($timezone);
	$dt = new DateTime("now", new DateTimeZone("$timezone"));
	$rftime = $GLOBALS['settings']['rftime'];

	// 12-hour time format:
	if ($timestandard==='True'){
		$dateTime = new DateTime();
		$dateTime->setTimeZone(new DateTimeZone($timezone));
		$timezone_suffix = '';
		$serverTime = $dt->format("D d M Y g:i:s A");
	}
	// 24-hour time format:
	else {
		$dateTime = new DateTime();
		$dateTime->setTimeZone(new DateTimeZone($timezone));
		$timezone_suffix = $dateTime->format('T');
		$serverTime = $dt->format("D d M Y H:i:s");
	}

	$response = array(
		'serverTime' => $serverTime,
		'timestandard' => $timestandard,
		'timezoneSuffix' => $timezone_suffix,
		'rftime' => $rftime
	);
	return json_encode($response);
}

function updateConfig($datadir) {
	//TODO: Maybe some smart version checking, for future config changes
	$old_preferences = json_decode(file_get_contents($datadir . 'user_preferences-data.json'), 1);
	$old_settings = json_decode(file_get_contents($datadir . 'site_settings-data.json'), 1);
	$old_services = json_decode(file_get_contents($datadir . 'services_settings-data.json'), 1);

	$new_config = json_decode(file_get_contents(__DIR__ . "/../data/default.json"), 1);
	$new_preferences = $new_config["preferences"];
	$new_settings = $new_config["settings"];
	$new_services = $new_config["services"];
	$new_authentication = $new_config["authentication"];

	$new_authentication["apikey"] = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));

	//Preferences
	$new_authentication["registrationEnabled"] = (isset($old_preferences["registration"]) && $old_preferences["registration"] != "Disabled") ? "True" : "False";
	foreach ($new_preferences as $key => $value) {
		if(isset($old_preferences[$key])) {
			$new_preferences[$key] = $old_preferences[$key];
		}
	}

	//Settings
	foreach ($new_settings as $key => $value) {
		if(isset($old_settings[$key])) {
			$new_settings[$key] = $old_settings[$key];
		}
	}

	//Services
	foreach ($old_services as $service) {
		unset($service["type"]);
		$new_services[] = $service;
	}

	$new_config["preferences"] = $new_preferences;
	$new_config["settings"] = $new_settings;
	$new_config["services"] = $new_services;
	$new_config["authentication"] = $new_authentication;
	file_put_contents($datadir . 'config.json' , json_encode($new_config, JSON_PRETTY_PRINT));
}