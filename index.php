<?php

	define("VERSION", "0.0.1")

require_once( 'responsivity-devices.php' );

	$site_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
	$site = isset($_GET['page']) && strip_tags($_GET['page']) != "" ? $site_url.strip_tags($_GET['page']) : $site_url;
	$full_height_mode = isset($_GET['full_height_mode']) && strip_tags($_GET['full_height_mode']) ? true : false;
	$show_devices = isset($_GET['show_devices']) && strip_tags($_GET['show_devices']) ? true : false;
	$show_titles = isset($_GET['show_titles']) && strip_tags($_GET['show_titles']) ? true : false;

?>
<!DOCTYPE html>
<html>
	<head>

		<title>Responsivity</title>
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<link rel="shortcut icon" href="responsivity-favicon.png" />
		<link rel="stylesheet" id="fontawesome-css" href="font-awesome/css/font-awesome.min.css" type="text/css" media="all">

	</head>
	<body>

		<div id="container" data-site="<?=$site?>">

			<?php

				if (!isset($_GET['device'])) $_GET['device'] = array();
				foreach ($_GET['device'] as $device_name) {

					// Put the device
					put_device($device_name);

					// If rotatable, add landscape mode
					if ( $device_name != "Custom" && $resp_devices[$device_name]["rotate"] ) put_device($device_name, true);

				}

			?>

		</div>


		<div id="functions">

			<!-- STOPPER -->
			<div id="stopper" class="inactive">
				<i class="fa fa-thumb-tack" aria-hidden="true"></i>
			</div>

			<!-- RELOADER -->
			<div id="reloader" class="inactive">
				<i class="fa fa-refresh" aria-hidden="true"></i>
			</div>

			<!-- OPTIONS -->
			<div id="optioner" class="inactive">
				<i class="fa fa-gear" aria-hidden="true"></i>
				<form id="optioner-form" action="" method="get" accept-charset="utf-8">

					<h2>Page</h2>
			    	<label for="resp_page">
				    	<?=$site_url?><input type="text" name="page" value="<?=isset($_GET['page']) ? $_GET['page'] : ""?>" placeholder="Enter the page extension">
			    	</label><br/>


					<h2>Options</h2>
			    	<label for="resp_full_height_mode">
				    	<input type="checkbox" id="resp_full_height_mode" name="full_height_mode" value="true" <?=isset($_GET['full_height_mode']) && $_GET['full_height_mode'] ? "checked" : ""?>> Full Height Mode
			    	</label><br/>

			    	<label for="resp_show_devices">
				    	<input type="checkbox" id="resp_show_devices" name="show_devices" value="true" <?=isset($_GET['show_devices']) && $_GET['show_devices'] ? "checked" : ""?>> Show Device Frames
			    	</label><br/>

			    	<label for="resp_show_titles">
				    	<input type="checkbox" id="resp_show_titles" name="show_titles" value="true" <?=isset($_GET['show_titles']) && $_GET['show_titles'] ? "checked" : ""?>> Show Titles
			    	</label><br/>


					<h2>Devices</h2>
				    <?php
					    $device_counter = 0;
					    $device_entered = empty($_GET['device']) ? true : false;
						foreach ($resp_devices as $resp_device => $resp_info) {

							$sizes = "(".$resp_info["width"]." x ".$resp_info["height"].")";
							if ($resp_device == "Current Screen") $sizes = '<span class="current-size"></span>';

							if ( $device_entered ) {
								$resp_device_prefered = $resp_info["prefer"];
							} else {
								$resp_device_prefered = ( in_array($resp_device, $_GET['device']) ) ? "checked" : "";
							}
					?>

					<label for="device-<?=$device_counter?>">
				    	<input type="checkbox" id="device-<?=$device_counter?>" name="device[]" value="<?=$resp_device?>" <?=$resp_device_prefered?>> <b><?=$resp_device?></b> <?=$sizes?>
			    	</label><br/>

					<?php
							$device_counter++;
						}
					?>
			    	<label for="device-custom">
				    	<input type="checkbox" id="device-custom" name="device[]" value="Custom" <?=isset($_GET['device-custom']) && $_GET['device-custom'] ? "checked" : ""?>> <b>Custom size</b>
				    	<input type="number" id="device-custom-width" name="device-custom[width]" <?=isset($_GET['device-custom']['width']) ? 'value="'.$_GET['device-custom']['width'].'"' : "disabled"?> placeholder="width" min="100"> x
				    	<input type="number" id="device-custom-height" name="device-custom[height]" <?=isset($_GET['device-custom']['height']) ? 'value="'.$_GET['device-custom']['height'].'"' : "disabled"?> placeholder="height" min="100">
			    	</label><br/>

			    	<button id="resp-submit" style="width: 100%;">Update</button>

			    </form>
			</div>

		</div>


		<script src="js/jquery-2.2.2.min.js"></script>
		<?=$full_height_mode ? '<script src="js/iframe-height.js"></script>':''?>
		<script src="js/script.js"></script>
	</body>
</html>


<?php
function put_device($device_name, $rotate = false) {
	global $site, $show_devices, $full_height_mode, $show_titles, $resp_devices;

	if ($rotate) {
		$width  = $resp_devices[$device_name]["height"];
		$height = $resp_devices[$device_name]["width"];
		$title  = $device_name." Landscape";
		$color  = $resp_devices[$device_name]["color"];
	} elseif ($device_name == "Current Screen") {
		$width  = "100%";
		$height = "100%";
		$title  = "Current Screen";
		$color  = "black";
	} elseif ($device_name == "Custom") {
		$width  = $_GET['device-custom']['width'];
		$height  = $_GET['device-custom']['height'];
		$title  = "Custom Device";
		$color  = "black";
	} else {
		$width  = $resp_devices[$device_name]["width"];
		$height = $resp_devices[$device_name]["height"];
		$title  = $device_name;
		$color  = $resp_devices[$device_name]["color"];
	}


	if ($full_height_mode) $show_devices =  false;
	$size = "(".$width." x ".$height.")";

	if ( ($width == 320 && $height == 568) || ($height == 320 && $width == 568) ) { $device = "iphone"; }
	else if ( ($width == 768 && $height == 1024) || ($height == 768 && $width == 1024) ) { $device = "ipad"; }
	else if ( ($width == 1280 && $height == 800) || ($width == 1440 && $height == 900) || ($width == 1920 && $height == 1200) ) { $device = "macbook"; }
	else if ( ($width == 1920 && $height == 1080) || ($width == 2560 && $height == 1440) ) { $device = "imac"; }
	else if ( ($width == '100%' && $height = '100%') ) { $device = "macbook current-screen"; $size = "<span class='current-size'></span>"; }
	else { $device = "other"; }

	if ( $width < $height ) { $position = "portrait"; } else { $position = "landscape"; }
	if ( $device == "macbook" || $device == "imac" || $device == "macbook current-screen" ) $position = "portrait";
	$refresh_link = "<i class='fa fa-refresh ind-reload' aria-hidden='true'></i>";
	$focus_link = "<i class='fa fa-crosshairs ind-focus' aria-hidden='true'></i>";
?>
	<div class="device <?=$device?> <?=$position?> <?=$color?><?=$show_devices ? "" : " no-device"?><?=$full_height_mode && !$show_devices ? " no-shadow" : ""?><?=$show_titles ? " show-title" : ""?>">
		<?=$show_titles ? "<h2>$title $size $refresh_link $focus_link</h2>" : ""?>
		<div class="front-cam"></div>
		<div class="ear-speaker"></div>

		<div class="screen"><iframe src="<?=$site?>" width="<?=$width?>" height="<?=$height?>" scrolling="<?=$full_height_mode ? "no" : "auto"?>" title="<?=$title?>"></iframe></div>

		<div class="home-button"></div>
		<div class="computer-bottom" style="display: none;"></div>
		<div class="computer-holder" style="display: none;"></div>

	</div>
<?php
}
?>