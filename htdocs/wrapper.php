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
	.exception {
	    border: 1px gray solid;
	    padding: 11px;
	    font-family: helvetica;
	    font-size :12px;
	}
	.exception p {
		margin : 5px
		;
	}
	#errorBox, .stackTrace {
		display: none;
	}
	#errorBox.visible, .stackTrace.visible {
		display: block;
	}
</style>


<?php

$request = $_SERVER['REQUEST_URI'];
$request = ltrim($request,'/');
$parts = explode('?',$request);

$request = $parts[0];

$viewsPath = realpath(dirname(__FILE__).'/../views/');

$requestPath = $viewsPath."/".$request;

$exceptions=[];
$colors=[
	'Comment'=>'lightgreen',
	'Error'=>'lightblue',
	'Exception'=>'lightpink'
];

set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
register_shutdown_function("shutdownHandler");

if (file_exists($requestPath) && is_file($requestPath)) {
	include_once($requestPath);		
} else {
	echo "<p>No file found!</p>";
}

echo outputErrorBox();


function logException($e, $type) {
	global $exceptions;
	$data = getExceptionData($e, $type);
	$data = cleanStackTrace($data);
	array_push($exceptions, $data);
	return $exceptions;
}

function logComment($message) {
	global $exceptions;
	try {
		throw new Exception();
	} catch (Exception $e) {
		$trace = $e->getTrace();
		$commentFile = $trace[0]['file'];
		$commentLine = $trace[0]['line'];
	}

	$data=[
		'errorType'=>"Comment",
		'filename'=>$commentFile,
		'lineNo'=>$commentLine,
		'message'=>$message,
		'stackTrace'=>$trace
	];
	$data = cleanStackTrace($data);
	array_push($exceptions, $data);
	return $exceptions;
}



function errorHandler($severity, $message, $filename, $lineno) {
	if (error_reporting() == 0) {
    	return false;
  	}
  	//var_dump(debug_backtrace());
  	if (error_reporting() && $severity) {
  		try {
  			throw new ErrorException($message, 0, $severity, $filename, $lineno);	
  		} catch (ErrorException $e) {
  			logException($e, 'Error');
  		}
    	return true;
  	}
}

function exceptionHandler($exception) {
	try {
		logException($exception, 'Exception');
		echo "<h1>EXCEPTION HANDLER</h1>";
		echo outputErrorBox();	
	} catch (Exception $e) {
		echo "<h1>exception handler for the exception handler</h1>";
	}
}

function shutdownHandler() {
	$data = [
		'errorType'=>'Fatal Error',
		'filename'=>"Unknown",
		'lineNo'=>0,
		'message'=>"Shutdown",
		'stackTrace'=>[]
	];

  	$error = error_get_last();

  	if (!in_array($error['type'],[E_ERROR,E_COMPILE_ERROR])) { 
        return false;    
    }

  	if ($error !== NULL) {
  		$data['filename'] = $error["file"];
  		$data['lineNo'] = $error['line'];
    	$data['message'] = $error['message'];
   	}
   	global $exceptions;
   	array_push($exceptions, $data);
	echo outputErrorBox();	
	return true;
}

function getExceptionData($e, $type) {
	$data=[
		'errorType'=>$type,
		'filename'=>$e->getFile(),
		'lineNo'=>$e->getLine(),
		'message'=>$e->getMessage(),
		'stackTrace'=>$e->getTrace()
	];
	$data = cleanStackTrace($data);
	return $data;
}

function cleanStackTrace($data) {
	$stackTrace = $data['stackTrace'];
	if (count($stackTrace)==0) return $data;
	
	foreach ($stackTrace as $id=>$trace) {

		if (!isset($trace['line'])) {
			var_dump($trace);
		}

		if ($trace['line'] == $data['lineNo'] && $trace['file'] == $data['filename']) {
			unset($stackTrace[$id]);
		}

		if ($trace['file'] == __FILE__) {
			unset($stackTrace[$id]);
		}
	}

	$data['stackTrace'] = $stackTrace;
	return $data;
}

function outputErrorBox() {
	global $exceptions;
	$c="<div id='errorBox'>";
	foreach ($exceptions as $exception) {
		$c.=outputException($exception);
	}
	$c.="</div>";
	return $c;
}

function getExceptionColour($exception) {
	global $colors;
	$type = $exception['errorType'];
	if (isset($colors[$type])) return $colors[$type];
	return 'lightgrey';
}

function outputException($exception) {
	$color = getExceptionColour($exception);
	$id = uniqid();
	$c="
	<div class='exception' onClick='showTrace(\"{$id}\");' style='background-color: {$color};'>
		<p><b>{$exception['errorType']}</b> in <b>{$exception['filename']}</b> at line <b>{$exception['lineNo']}</b></p>
		<p><b>Message</b>: {$exception['message']}</p>
		<div id='stackTrace{$id}' class='stackTrace'>
			".displayLines(
				getOffendingLine($exception['filename'],$exception['lineNo']),
				$exception['lineNo']
			).displayStackTrace($exception['stackTrace'])."
		</div>
	</div>";
	return $c;
}

function displayStackTrace($stackTrace) {
	$c="";
	foreach ($stackTrace as $i=>$entry) {
		$c.="
		<div>
			<p>Stack trace {$i}: <b>".$entry['file']."</b>, line <b>{$entry['line']}</b></p>
			".displayLines(
			getOffendingLine($entry['file'],$entry['line']),
			$entry['line'])."
		</div>";
	}
	return $c;
}

function displayLines($lines, $mainLineNo) {
	$c="<pre><code class='language-php'>";
	foreach ($lines as $lineNo=>$line) {
		if ($lineNo == $mainLineNo) {
			$c.="<b>+{$lineNo}+ : </b>{$line}";
		} else {
			$c.="<b> {$lineNo}  : </b>{$line}";
		}
		$c.="<br>";
	}
	$c.="</code></pre>";
	return $c;
}

function getOffendingLine($filename, $line) {
	$lines = file($filename);//file in to an array

	$firstLine = $line - 3;
	if ($firstLine <0) $firstLine=0;

	$lastLine = $line + 1;
	if ($lastLine >= count($lines)) $lastLine = count($lines) - 1;

	$selectedLines=[];
	for ($i=$firstLine; $i<=$lastLine; $i++) {
		$selectedLines[$i + 1] = $lines[$i];
	}

	return $selectedLines;
}