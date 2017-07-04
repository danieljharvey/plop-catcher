<?php

include('../vendor/autoload.php');

$plop = new \DanielJHarvey\PlopCatcher\Plop('HTML',function($html) {
	ob_clean();
	echo "
		<html>
			<body style='margin: 0px;'>
				<h1>There was an unrecoverable error!</h1>
			</body>
		</html>";
	outputHTML($html);
},
function($html) {
	outputHTML($html);
});

$plop->enable();

// testable code here:
echo "
<html>
	<title>Plop</title>
	<body style='margin: 0px;'>
		<h1>yeah</h1>
		<p>Blah</p>
	</body>
</html>";

echo $nonExistantVariable;

$plop->logComment("STUFF ETC");

$directory = new RecursiveDirectoryIterator(dirname(__FILE__));

throw new Exception("dsfjsdofjoisdojfi");

//require_once("fjsdjfjsdoijoi");

// test code ends

//outputHTML($plop->output());

function outputHTML($html) {
	echo $html;
}
/*
function outputArray($array) {
	var_dump($array);
}

function outputJSON($json) {
	echo "<script>console.log({$json})</script>";
}
*/