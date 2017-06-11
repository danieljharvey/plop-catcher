<?php

include('../vendor/autoload.php');

$plop = new \DanielJHarvey\PlopCatcher\Plop('HTML',function($html) {
	outputHTML($html);
});

$plop->enable();

// testable code here:
echo "
<html>
	<title>Plop</title>
	<body>
		<h1>yeah</h1>
		<p>Blah</p>
	</body>
</html>";

echo $nonExistantVariable;

$plop->logComment("STUFF ETC");

throw new Exception("dsfjsdofjoisdojfi");

// test code ends

outputHTML($plop->output());

function outputHTML($html) {
	echo $html;
}

function outputArray($array) {
	var_dump($array);
}

function outputJSON($json) {
	echo "<script>console.log({$json})</script>";
}
