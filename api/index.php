<?php
//<editor-fold desc="setup">
$generationTime = -microtime(true);
//include functions
require_once(__DIR__ . '/../assets/php/functions.php');
//Set result array
$result = array();
//Get request method
$method = $_SERVER['REQUEST_METHOD'];
$pretty = isset($_GET['pretty']) ? true : false;
$function = (key($_GET) ? str_replace("/", "_", key($_GET)) : false);
//Exit if $function is blank
if ($function === false) {
	$result['status'] = "error";
	$result['statusText'] = "No API Path Supplied";
	exit(json_encode($result));
}
$result['request'] = key($_GET);
$result['params'] = $_POST;
//</editor-fold>

$referredByHomepage = true; //TODO or request is coming from homepage (necessary if user if not logged in)

//<editor-fold desc="API functions">
switch ($function) {
	case 'v1_getPing':
		switch ($method) {
			case 'POST':
                if(checkAuthorization() || $referredByHomepage) {
                    $result['status'] = 'success';
                    $result['statusText'] = 'success';
                    $service = $_POST['service'];
	                $key = array_search($service, array_column($GLOBALS['services'], 'serviceTitle'));
	                $pingUrl = $GLOBALS['services'][$key]['checkurl'];
                    $result['data'] = ping($pingUrl);
                    if(!$result['data']) {
	                    file_put_contents(__DIR__ . "/../assets/data/logs/" . $service . ".json", $service . " is offline since " . date("h:i:sa"));
                    } else {
	                    if(file_exists(__DIR__ . "/../data/logs/" . $service . ".json")) unlink(__DIR__ . "/../data/logs/" . $service . ".json");
                    }
                } else {
                    $result['status'] = 'error';
                    $result['statusText'] = 'API/Token invalid or not set';
                    $result['data'] = null;
                }
				break;
			default:
				$result['status'] = 'error';
				$result['statusText'] = 'The function requested is not defined for method: ' . $method;
				break;
		}
		break;
	case 'v1_getPlugins':
		switch ($method) {
			case 'GET':
				if(checkAuthorization()) {
					$result['status'] = 'success';
					$result['statusText'] = 'success';
					$result['data'] = getPlugins();
				} else {
					$result['status'] = 'error';
					$result['statusText'] = 'API/Token invalid or not set';
					$result['data'] = null;
				}
				break;
			default:
				$result['status'] = 'error';
				$result['statusText'] = 'The function requested is not defined for method: ' . $method;
				break;
		}
		break;
	case 'v1_getSystemBadges':
		switch ($method) {
			case 'GET':
				if(checkAuthorization()) {
					$result['status'] = 'success';
					$result['statusText'] = 'success';
					$ping = ping($GLOBALS['settings']['pinghost'] . ":" . $GLOBALS['settings']['pingport']);
					if (!$ping) $ping = "?";

					$result['data'] = array(
						"serverLoad" => getServerLoad(),
						"ramPercentage" => getRamPercentage(),
						"totalUptime" => getTotalUptime(),
						"pingTime" => $ping,
						"disk1Usage" => (isset($GLOBALS['settings']['disk1']) ? getHDFree("disk1") : "?"),
						"disk2Usage" => (isset($GLOBALS['settings']['disk2']) ? getHDFree("disk2") : "?"),
						"disk3Usage" => (isset($GLOBALS['settings']['disk3']) ? getHDFree("disk3") : "?"),
					);
				} else {
					$result['status'] = 'error';
					$result['statusText'] = 'API/Token invalid or not set';
					$result['data'] = null;
				}
				break;
			default:
				$result['status'] = 'error';
				$result['statusText'] = 'The function requested is not defined for method: ' . $method;
				break;
		}
		break;
	case 'v1_formBuilder_settingsForm_plugin':
		switch ($method) {
			case 'POST':
				if(checkAuthorization()) {
					$result['status'] = 'success';
					$result['statusText'] = 'success';
					$result['data'] = createPluginSettingsForm($_POST['plugin']);
				} else {
					$result['status'] = 'error';
					$result['statusText'] = 'API/Token invalid or not set';
					$result['data'] = null;
				}
				break;
			default:
				$result['status'] = 'error';
				$result['statusText'] = 'The function requested is not defined for method: ' . $method;
				break;
		}
		break;
	case 'v1_services_get_offline':
		switch ($method) {
			case 'GET':
				if(checkAuthorization() || $referredByHomepage) {
					$result['status'] = 'success';
					$result['statusText'] = 'success';
					$result['data'] = getOfflineServices();
				} else {
					$result['status'] = 'error';
					$result['statusText'] = 'API/Token invalid or not set';
					$result['data'] = null;
				}
				break;
			default:
				$result['status'] = 'error';
				$result['statusText'] = 'The function requested is not defined for method: ' . $method;
				break;
		}
		break;
	case 'v1_settings_get':
		switch ($method) {
			case 'GET':
				if(checkAuthorization()) {
					$result['status'] = 'success';
					$result['statusText'] = 'success';
					$result['data'] = json_decode(file_get_contents($config_file), 1);
				} else {
					$result['status'] = 'error';
					$result['statusText'] = 'API/Token invalid or not set';
					$result['data'] = null;
				}
				break;
			default:
				$result['status'] = 'error';
				$result['statusText'] = 'The function requested is not defined for method: ' . $method;
				break;
		}
		break;
	case 'v1_services_sort_alphabetically':
		switch ($method) {
			case 'GET':
				if(checkAuthorization()) {
					$result['status'] = 'success';
					$result['statusText'] = 'success';
					$result['data'] = sortServicesAlphabetically();
				} else {
					$result['status'] = 'error';
					$result['statusText'] = 'API/Token invalid or not set';
					$result['data'] = null;
				}
				break;
			default:
				$result['status'] = 'error';
				$result['statusText'] = 'The function requested is not defined for method: ' . $method;
				break;
		}
		break;
	case 'v1_settings_update':
		switch ($method) {
			case 'POST':
				if(checkAuthorization()) {
					$result['status'] = 'success';
					$result['statusText'] = 'success';
					$result['data'] = updateSettings($_POST);
				} else {
					$result['status'] = 'error';
					$result['statusText'] = 'API/Token invalid or not set';
					$result['data'] = null;
				}
				break;
			default:
				$result['status'] = 'error';
				$result['statusText'] = 'The function requested is not defined for method: ' . $method;
				break;
		}
		break;
	default:
		//No Function Available
		$result['status'] = 'error';
		$result['statusText'] = 'function requested is not defined';
		break;
}
//</editor-fold>

//<editor-fold desc="result formatting">
//Set Default Result
if (!$result) {
	$result['status'] = "error";
	$result['error'] = "An error has occurred";
}
$result['generationDate'] = microtime(true);
$generationTime += microtime(true);
$result['generationTime'] = (sprintf('%f', $generationTime) * 1000) . 'ms';
//return JSON array
if ($pretty) {
	echo '<pre>' . json_encode($result, JSON_PRETTY_PRINT) . '</pre>';
} else {
	exit(json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG));
}
//</editor-fold>