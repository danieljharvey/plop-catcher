<link rel="stylesheet" href="/css/solarized-light.css">
<script src="/js/prism.js"></script>
<script>

	document.addEventListener("keypress", function(event) {
	    var isShift = event.shiftKey
	    if (event.keyCode == 13 && isShift) {
	        toggleHelpBox();
	    }
	});

	function toggleHelpBox() {
		var errorBox = document.getElementById('errorBox');
		if (errorBox.classList.contains('visible')) {
			// hide it
			errorBox.classList.remove('visible');
		} else {
			// show it
			errorBox.classList.add('visible');
		}
	}

	function showTrace(id) {
		var stackTrace = document.getElementById('stackTrace' + id);
		if (stackTrace.classList.contains('visible')) {
			// hide it
			stackTrace.classList.remove('visible');
		} else {
			// show it
			stackTrace.classList.add('visible');
		}
	}

</script>
<style>
	#errorBox .exception {
	    border: 1px gray solid;
	    padding: 11px;
	    font-family: helvetica;
	    font-size :12px;
	}
	#errorBox .exception p {
		margin : 5px
		;
	}
	#errorBox, #errorBox .stackTrace {
		display: none;
	}
	#errorBox.visible, #errorBox .stackTrace.visible {
		display: block;
	}
</style>


<?php

include('../vendor/autoload.php');

$request = $_SERVER['REQUEST_URI'];
$request = ltrim($request,'/');
$parts = explode('?',$request);

$request = $parts[0];

$viewsPath = realpath(dirname(__FILE__).'/../views/');

$requestPath = $viewsPath."/".$request;

$logger = new \DanielJHarvey\PlopCatcher\Logger;
$errorCatcher = new \DanielJHarvey\PlopCatcher\ErrorCatcher($logger);
$errorCatcher->enable();

if (file_exists($requestPath) && is_file($requestPath)) {
	include_once($requestPath);		
} else {
	echo "<p>No file found!</p>";
}
echo "What?";

$jsonOutput = new \DanielJHarvey\PlopCatcher\JSONOutput($logger);

echo "<br><br>";
echo $jsonOutput->getOutput();
echo "<br><br>";

$output = $jsonOutput->getOutput();
echo "<script>console.log({$output})</script>";

$htmlOutput = new \DanielJHarvey\PlopCatcher\HTMLOutput($logger);
echo $htmlOutput->getOutput();


//echo outputErrorBox($logger);
