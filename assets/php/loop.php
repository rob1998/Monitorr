<?php
include(__DIR__ . '/functions.php');
include(__DIR__ . '/auth_check.php');
?>

<?php foreach ($services as $key => $service) { ?>

	<?php

	if ($service['enabled'] == "Yes") {

		echo '<div class="col-lg-4">';

		if ($service['ping'] == "Enabled") {
			$pingTime = ping($service['checkurl']);

			$pingok = $settings['pingok'];
			$pingwarn = $settings['pingwarn'];

			if ($pingTime < $pingok) {
				$pingid = 'pinggreen';
			} elseif (($pingTime >= $pingok) && ($pingTime < $pingwarn)) {
				$pingid = 'pingyellow';
			} else {
				$pingid = 'pingred';
			}

			echo '<div id="pingindicator">';
			echo '<div id="' . $pingid . '" class="pingcircle" title="Ping response time: ' . $pingTime . ' ms"> </div>';
			echo "<script type='text/javascript'>";
			echo "console.log('" . $service['serviceTitle'] . " Ping time: " . $pingTime . " ms');";
			echo "</script>";
			echo '</div>';

		} else {

		}


		if ($service['link'] == "Yes") {
			echo '<a class="servicetile" href="' . $service['linkurl'] . '" target="_blank" style="display: block">';
		} else {
			echo '<div class="servicetilenolink" style="display: block; cursor: default">';
		}

		echo '<div id="serviceimg">';
		echo '<div><img id="' . strtolower($service['serviceTitle']) . '-service-img" src="assets/img/' . strtolower($service['image']) . '" class="serviceimg" alt=' . strtolower($service['serviceTitle']) . '></div>';
		echo '</div>';

		echo '<div id="servicetitle">';
		echo '<div>' . ucfirst($service['serviceTitle']) . '</div>';
		echo '</div>';

		echo '<div class="btnonline">Online</div>';

		if ($service['link'] == "Yes") {
			echo '</a>';
		} else {
			echo '</div>';
		}
		echo '</div>';
	} else {
		// Remove offline log file if disabled://
		$servicefile = ($service['serviceTitle']) . '.offline.json';
		$fileoffline = '../data/logs/' . $servicefile;

		if (is_file($fileoffline)) {
			rename($fileoffline, '../data/logs/offline.json.old');
		}
	}
} ?>
<!-- Remove loading modal after page onload: -->

<script type='text/javascript'>
    $('.pace-activity').addClass('hidepace');
    $('.modalloadingindex').addClass('hidemodal');
    console.log("Service check complete");
</script>
