<?php
/*
RESPONSIVITY TOOL
A tool that allows to make responsivity arrangements easier for web projects. You can also send a link to your clients to show them how their websites look on specific devices or some custom screen sizes.

Version: 0.0.7
GitHub: https://github.com/bilaltas/Responsivity-Tool
Example: https://www.bilaltas.net/responsivity/
*/



	require_once( 'responsivity-class.php' );
	$responsivity = new ResponsivityTool();

	$device_entered = empty($_GET['device']) ? true : false;

?>
<!DOCTYPE html>
<html>
	<head>

		<base href="<?=$responsivity->base?>">

		<title>Responsivity</title>
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<link rel="shortcut icon" href="responsivity-favicon.png" />
		<link rel="stylesheet" id="fontawesome-css" href="font-awesome/css/font-awesome.min.css" type="text/css" media="all">

	</head>
	<body>

		<div id="container">

			<?php

				if (!isset($_GET['device'])) $_GET['device'] = array();
				foreach ($_GET['device'] as $device_name) {

					// Put the device
					$responsivity->put_device($device_name);

					// If rotatable, add landscape mode
					if ( $device_name != "Custom" && $responsivity->devices[$device_name]["rotate"] ) $responsivity->put_device($device_name, true);

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

			<!-- SHARER -->
			<div id="sharer" class="inactive">
				<a href="mailto:?Subject=Here is the responsive view of the site&amp;Body=Link: <?=$responsivity->site_url.urlencode($_SERVER['REQUEST_URI'])?>"><i class="fa fa-envelope"></i></a>
			</div>

			<!-- OPTIONS -->
			<div id="optioner" class="<?php echo $device_entered ? "active" : "inactive"; ?>">
				<i class="fa fa-gear" aria-hidden="true"></i>

				<?php $responsivity->print_form(); ?>

			</div>

		</div>


		<script src="<?=$responsivity->jQuery_file?>"></script>
		<script src="js/script.js"></script>
	</body>
</html>