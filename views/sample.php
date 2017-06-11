<?php

// i am a fake page

$dog = "Dog";

$cat = "Cat";

?>

<html>
	<title>Sample.php</title>
	<body>
		<h1>Yeah, sample.php</h1>
		<h2>Totally fine so far</h2>
	

<?php

echo "<h1>{$dog}</h1>";

$plop->logComment("So this thing happened and it was less than ideal");

try {
	throw new Exception("Tortoise");	
} catch (Exception $e) {
	echo "Actually, I've solved this exception for myself.";
}

function bum() {
	global $plop;
	echo $bum;
	$plop->logComment("So I am logging a comment and hoping for a stack trace");
	return $turd;
}
function slopDog() {
	bum();	
}

slopDog();

echo $poo;

echo "Do we get here?";

throw new Exception("Last exception, great job");

//require_once(" a big poo ");

echo "what?";

?>

	</body>
</html>

