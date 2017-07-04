<?php

namespace DanielJHarvey\PlopCatcher;

class HTMLOutput {
	
	protected $cssClasses=[
		'Comment'=>'plopComment',
		'Error'=>'plopError',
		'Exception'=>'plopException',
		'Fatal Error'=>'plopFatal'
	];

	protected $jsPath = 'js/Plops.js';
	protected $cssPath = 'css/plops.css';

	public function getOutput($data) {
		$c="<script>".$this->getJS()."</script>";
		$c.="<style>".$this->getCSS()."</style>";
		$errorBox = $this->drawErrorBox($data);
		if (!$errorBox) return false; // if no events, don't show anything
		$c.= $errorBox;
		return $c;
	}

	public function drawErrorBox($data) {
		$events = $data['events'];
		if (count($events)==0) return false;
		$c="<div id='errorBox'>";
		$c.= $this->drawTitleBar($data);
		foreach ($events as $event) {
			$c.=$this->drawException($event);
		}
		$c.="</div>";
		return $c;
	}

	protected function drawTitleBar($data) {
		$events = $data['events'];
		$stats = $data['stats'];
		$count = count($events);
		return "
			<div class='titleBar'>
				".$this->drawHideBox()."
				<p onClick='plops.toggleHelpBox();'>
					<b>PHP Error Console ({$count})</b>: press shift + enter to toggle
				</p>
				".$this->drawEventsByType($events)."
				".$this->drawTimeStats($stats)."
			</div>";
	}

	protected function drawEventsByType($events) {
		$types = [];
		foreach ($events as $event) {
			$errorType = $event['errorType'];
			if (!array_key_exists($errorType,$types)) {
				$types[$errorType] = 1;
			} else {
				$types[$errorType]++;
			}
		}
		$c="";
		foreach ($types as $type=>$count) {
			$c.=$this->drawErrorTypeBox($type, $count);
		}
		return $c;
	}

	protected function drawErrorTypeBox($type, $count) {
		$cssClass = $this->getErrorTypeClass($type);
		$typeString = $type;
		if ($count>1) $typeString.="s";
		$id = 'toggle'.$cssClass;
		$onClick = "plops.toggleCategory(\"{$cssClass}\");";
		return "
			<p onClick='{$onClick}' id='{$id}' class='toggle {$cssClass}'>
				<b>{$count} {$typeString}</b>
			</p>";
	}

	protected function drawTimeStats($stats) {
		$c="";
		foreach ($stats as $type=>$stat) {
			$time = round($stat,6)*1000;
			$c.="<p><b>{$type}</b>:{$time}ms</p>";
		}
		return $c;
	}

	protected function drawHideBox() {
		return "<div class='plopsToggleBox' onClick='plops.toggleHelpBox();'>X</div>";
	}

	function getErrorTypeClass($type) {
		if (isset($this->cssClasses[$type])) return $this->cssClasses[$type];
		return 'plopOther';
	}

	function drawException($exception) {
		$cssClass = $this->getErrorTypeClass($exception['errorType']);
		$id = uniqid();
		$c="
		<div class='exception {$cssClass}' onClick='plops.showTrace(\"{$id}\");'>
			<p><b>{$exception['errorType']}</b> in <b>{$exception['file']}</b> at line <b>{$exception['line']}</b></p>
			<p><b>Message</b>: {$exception['message']}</p>
			<div id='stackTrace{$id}' class='stackTrace'>
				".$this->drawStackTrace($exception['stackTrace'])."
			</div>
		</div>";
		return $c;
	}

	function drawStackTrace($stackTrace) {
		$c="";
		foreach ($stackTrace as $i=>$entry) {
			$c.="
			<div>
				<p>Stack trace {$i}: <b>".$entry['file']."</b>, line <b>{$entry['line']}</b></p>
				".$this->drawLines($entry['file'],$entry['line'])."
			</div>";
		}
		return $c;
	}

	function drawLines($file, $mainLineNo) {
		$lines = $this->getCodeLines($file, $mainLineNo);
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

	protected function getJS() {
		$jsPath = dirname(__FILE__)."/".$this->jsPath;
		return $this->getFileContents($jsPath);
	}

	protected function getCSS() {
		$cssPath = dirname(__FILE__)."/".$this->cssPath;
		return $this->getFileContents($cssPath);
	}

	protected function getFileContents($path) {
		if (!file_exists($path)) return false;
		if (!is_file($path)) return false;
		return file_get_contents($path);
	}

	protected function getCodeLines($filename, $lineNo) {
		$lines = $this->readFileAsArray($filename);
		if (!$lines) return false;
		return $this->parseCodeLines($lines, $lineNo);
	}

    protected function readFileAsArray($filename) {
    	if (!$filename) return false;
    	return file($filename); //file in to an array
    }

    protected function parseCodeLines($lines, $lineNo) {

		$firstLine = $lineNo - 3;
		if ($firstLine <0) $firstLine=0;

		$lastLine = $lineNo + 1;
		if ($lastLine >= count($lines)) $lastLine = count($lines) - 1;

		$selectedLines=[];
		for ($i=$firstLine; $i<=$lastLine; $i++) {
			$selectedLines[$i + 1] = $lines[$i];
		}

		return $selectedLines;
	}
}