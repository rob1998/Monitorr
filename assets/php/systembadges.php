<?php
require(__DIR__ . '/functions.php');
$ping = ping($settings['pinghost'] . ":" . $settings['pingport']);
if(!$ping) $ping = "?";

$result = array(
	"serverLoad" => getServerLoad(),
	"ramPercentage" => getRamPercentage(),
	"totalUptime" => getTotalUptime(),
	"pingTime" => $ping,
	"disk1Usage" => (isset($settings['disk1']) ? getHDFree("disk1") : "?"),
	"disk2Usage" => (isset($settings['disk2']) ? getHDFree("disk2") : "?"),
	"disk3Usage" => (isset($settings['disk3']) ? getHDFree("disk3") : "?"),
);
echo json_encode($result);