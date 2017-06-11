<link rel="stylesheet" href="/css/solarized-light.css">
<script src="/js/prism.js"></script>

<?php

include('../vendor/autoload.php');

$request = $_SERVER['REQUEST_URI'];
$request = ltrim($request,'/');
$parts = explode('?',$request);

$request = $parts[0];

$viewsPath = realpath(dirname(__FILE__).'/../views/');

$requestPath = $viewsPath."/".$request;

$plop = new \DanielJHarvey\PlopCatcher\Plop('ARRAY',function($array) {
	outputArray($array);
});

$plop->enable();

if (file_exists($requestPath) && is_file($requestPath)) {
	include_once($requestPath);		
} else {
	echo "<p>No file found!</p>";
}
echo "What?";

outputArray($plop->output());

function outputPlops($html) {
	echo "I am a local function outputting this code";
	echo $html;
}

function outputArray($array) {
	var_dump($array);
}
