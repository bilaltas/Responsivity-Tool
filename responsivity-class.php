<?php

class ResponsivityTool {

	var $devices;
	var $site_url;
	var $page_url;
	var $full_url;

	var $show_devices;
	var $full_height_mode;
	var $show_titles;

	var $jQuery_file;
	var $base;


	function __construct() {

		// Get the device info
		$this->devices = $this->get_devices();

		// Get the site info
		$this->site_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
		$this->page_url = basename($_SERVER['PHP_SELF']) == "index.php" && isset($_GET['page']) && strip_tags($_GET['page']) != "" ? strip_tags($_GET['page']) : "";
		$this->full_url = $this->site_url.$this->page_url;


		// Add an argument to remove admin bar
		$query = parse_url($this->full_url, PHP_URL_QUERY);
		$this->full_url .= ($query ? '&' : '?').'responsivity_frame=1';


		// Current device options
		$this->show_devices = isset($_GET['show_devices']) && strip_tags($_GET['show_devices']) ? true : false;
		$this->full_height_mode = isset($_GET['full_height_mode']) && strip_tags($_GET['full_height_mode']) ? true : false;
		$this->show_titles = isset($_GET['show_titles']) && strip_tags($_GET['show_titles']) ? true : false;


		// Check if this is a WP Plugin
		$this->jQuery_file = "js/jquery-2.2.2.min.js";
		$this->base = "";
		if ( file_exists("../../../../wp-config.php") || file_exists("wp-config.php") ) {
			$this->jQuery_file = "/wp-includes/js/jquery/jquery.js?ver=1.12.4";
			$this->base = "/wp-content/plugins/responsivity/viewer/";

		}

	}


	function get_devices() {

		require_once( 'responsivity-devices.php' );
		return $resp_devices;

	}


	function print_form($action = "", $target = "_self") {

		echo '<form id="optioner-form" action="'.$action.'" method="get" accept-charset="utf-8" target="'.$target.'">';

			$this->print_page_input();
			$this->print_options();
			$this->print_devices();
			$this->print_button();

		echo "</form>";

	}


	function print_page_input() {
?>

		<h2>Page</h2>
		<label for="resp_page">
	    	<?=$this->site_url?><input type="text" name="page" value="<?=$this->page_url?>" placeholder="URL extension (Optional)">
		</label><br/><br/>

<?php
	}


	function print_options() {

	    $device_entered = empty($_GET['device']) ? true : false;

	    if ($device_entered)
		    $this->show_devices = $this->show_titles = true;
?>

		<h2>Options</h2>
    	<label for="resp_full_height_mode">
	    	<input type="checkbox" id="resp_full_height_mode" name="full_height_mode" value="true" <?=$this->full_height_mode ? "checked" : ""?>> Full Height Mode
    	</label><br/>

    	<label for="resp_show_devices">
	    	<input type="checkbox" id="resp_show_devices" name="show_devices" value="true" <?=$this->show_devices ? "checked" : ""?>> Show Device Frames
    	</label><br/>

    	<label for="resp_show_titles">
	    	<input type="checkbox" id="resp_show_titles" name="show_titles" value="true" <?=$this->show_titles ? "checked" : ""?>> Show Titles
    	</label><br/><br/>

<?php
	}


	function print_devices($devices = array()) {

		if ( count($devices) == 0 ) $devices = $this->devices;

		$device_entered = empty($_GET['device']) ? true : false;
?>

		<h2>Devices</h2>
	    <?php
			foreach ($devices as $resp_device => $resp_info) {

				$sizes = "(".$resp_info["width"]." x ".$resp_info["height"].")";
				if ($resp_device == "Current Screen") $sizes = '<span class="current-size"></span>';

				if ( $device_entered ) {
					$resp_device_prefered = $resp_info["prefer"];
				} else {
					$resp_device_prefered = ( in_array($resp_device, $_GET['device']) ) ? "checked" : "";
				}
		?>

		<label>
	    	<input type="checkbox" name="device[]" value="<?=$resp_device?>" <?=$resp_device_prefered?>> <b><?=$resp_device?></b> <?=$sizes?>
    	</label><br/>

		<?php
			}
		?>
    	<label for="device-custom">
	    	<input type="checkbox" id="device-custom" name="device[]" value="Custom" <?=isset($_GET['device-custom']) && $_GET['device-custom'] ? "checked" : ""?>> <b>Custom size</b>
	    	<input type="number" id="device-custom-width" name="device-custom[width]" <?=isset($_GET['device-custom']['width']) ? 'value="'.$_GET['device-custom']['width'].'"' : "disabled"?> placeholder="width" min="100"> x
	    	<input type="number" id="device-custom-height" name="device-custom[height]" <?=isset($_GET['device-custom']['height']) ? 'value="'.$_GET['device-custom']['height'].'"' : "disabled"?> placeholder="height" min="100">
    	</label><br/><br/>

<?php
	}


	function print_button() {

		$device_entered = empty($_GET['device']) ? true : false;

?>

		<button id="resp-submit" class="button button-primary"><?=$device_entered ? "Open Responsivity Tool" : "Update"?></button>

<?php
	}


	function put_device($device_name, $rotate = false) {
		global $show_devices, $full_height_mode, $show_titles;


		if ($rotate) {
			$width  = $this->devices[$device_name]["height"];
			$height = $this->devices[$device_name]["width"];
			$title  = $device_name." Landscape";
			$color  = $this->devices[$device_name]["color"];
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
			$width  = $this->devices[$device_name]["width"];
			$height = $this->devices[$device_name]["height"];
			$title  = $device_name;
			$color  = $this->devices[$device_name]["color"];
		}


		if ($this->full_height_mode) $this->show_devices =  false;
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
		<div class="device <?=$device?> <?=$position?> <?=$color?><?=$this->show_devices ? "" : " no-device"?><?=$this->full_height_mode && !$this->show_devices ? " no-shadow full-height" : ""?><?=$this->show_titles ? " show-title" : ""?>">
			<?=$this->show_titles ? "<h2>$title $size $refresh_link $focus_link</h2>" : ""?>
			<div class="front-cam"></div>
			<div class="ear-speaker"></div>

			<div class="screen"><iframe src="<?=$this->full_url?>" width="<?=$width?>" height="<?=$height?>" scrolling="<?=$this->full_height_mode ? "no" : "auto"?>" title="<?=$title?>"></iframe></div>

			<div class="home-button"></div>
			<div class="computer-bottom" style="display: none;"></div>
			<div class="computer-holder" style="display: none;"></div>

		</div>
	<?php
	}


}