<?php
include(__DIR__ . '/classes/Ping.php');

if (!is_file(__DIR__ . '../../data/datadir.json')
	|| file_get_contents(__DIR__ . '../../data/datadir.json') == ""
	|| !key_exists("datadir", json_decode(file_get_contents(__DIR__ . '../../data/datadir.json'), 1))
	|| !is_file(json_decode(file_get_contents(__DIR__ . '../../data/datadir.json'), 1)['datadir'] . 'config.json')
) {
	//invalid/unset datadir, start configuration process
	$_GET['action'] = 'config';
	include_once(__DIR__ . '/auth_check.php');
	exit();
}

// Data Dir
$datadir_json = json_decode(file_get_contents(__DIR__ . '../../data/datadir.json'), 1);
$datadir = $datadir_json['datadir'];


$config_file = $datadir . '/config.json';
$preferences = json_decode(file_get_contents($config_file), 1)['preferences'];
$settings = json_decode(file_get_contents($config_file), 1)['settings'];
$services = json_decode(file_get_contents($config_file), 1)['services'];
$authentication = json_decode(file_get_contents($config_file), 1)['authentication'];


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

// register variable for getServerLoad()



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

function getHDClass($percentage){
	// Dynamic icon colors for badges:

	$hdok = $GLOBALS['settings']['hdok'];
	$hdwarn = $GLOBALS['settings']['hdwarn'];

	if ($percentage < $hdok) {
		$hdClass = 'success';
	} elseif (($percentage >= $hdok) && ($percentage < $hdwarn)) {
		$hdClass = 'warning';
	} else {
		$hdClass = 'danger';
	}

	return $hdClass;
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


function ping($pings)
{
	$type = gettype($pings);
	$ping = new Ping("");
	$ping->setTtl(128);
	$ping->setTimeout(2);
	switch ($type) {
		case "array":
			$results = [];
			foreach ($pings as $k => $v) {
				if (strpos($v, ':') !== false) {
					$domain = explode(':', $v)[0];
					$port = explode(':', $v)[1];
					$ping->setHost($domain);
					$ping->setPort($port);
					$latency = $ping->ping('fsockopen');
				} else {
					$ping->setHost($v);
					$latency = $ping->ping();
				}
				if ($latency || $latency === 0) {
					$results[$v] = $latency;
				} else {
					$results[$v] = false;
				}
			}
			break;
		case "string":
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
			break;
	}
	return $results;
}

// Dynamic icon colors for badges
function getRamClass($percentage)
{
	$ramok = $GLOBALS['settings']['ramok'];
	$ramwarn = $GLOBALS['settings']['ramwarn'];


	if ($percentage < $ramok) {
		$ramClass = 'success';
	} elseif (($percentage >= $ramok) && ($percentage < $ramwarn)) {
		$ramClass = 'warning';
	} else {
		$ramClass = 'danger';
	}
	return$ramClass;
}
function getCPUClass($percentage)
{
	$cpuok = $GLOBALS['settings']['cpuok'];
	$cpuwarn = $GLOBALS['settings']['cpuwarn'];

	if ($percentage < $cpuok) {
		$cpuClass = 'success';
	} elseif (($percentage >= $cpuok) && ($percentage < $cpuwarn)) {
		$cpuClass = 'warning';
	} else {
		$cpuClass = 'danger';
	}
	return $cpuClass;
}
function getPingClass($pingTime){
	$pingok = $GLOBALS['settings']['pingok'];
	$pingwarn = $GLOBALS['settings']['pingwarn'];

	if(strpos($pingTime, '?') !== false) return 'danger';

    if ($pingTime < $pingok) {
	    $pingclass = 'success';
    } elseif (($pingTime >= $pingok) && ($pingTime < $pingwarn)) {
	    $pingclass = 'warning';
    } else {
	    $pingclass = 'danger';
    }
    return $pingclass;
}



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

function configExists()
{
	return is_file($GLOBALS['config_file']);
}
?>
