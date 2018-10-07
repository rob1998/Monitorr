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
$api_key = isset($_GET['api_key']) ? $_GET['api_key'] : "";
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

//<editor-fold desc="API functions">
switch ($function) {
	case 'v1_getPing':
		switch ($method) {
			case 'POST':
				$result['status'] = 'success';
				$result['statusText'] = 'success';
				$result['data'] = ping($_POST['url']);
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
				$result['status'] = 'success';
				$result['statusText'] = 'success';
				$ping = ping($settings['pinghost'] . ":" . $settings['pingport']);
				if(!$ping) $ping = "?";

				$result['data'] = array(
					"serverLoad" => getServerLoad(),
					"ramPercentage" => getRamPercentage(),
					"totalUptime" => getTotalUptime(),
					"pingTime" => $ping,
					"disk1Usage" => (isset($settings['disk1']) ? getHDFree("disk1") : "?"),
					"disk2Usage" => (isset($settings['disk2']) ? getHDFree("disk2") : "?"),
					"disk3Usage" => (isset($settings['disk3']) ? getHDFree("disk3") : "?"),
				);
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