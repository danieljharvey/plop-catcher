<?php

namespace DanielJHarvey\PlopCatcher;

class HTMLOutput {
	
	protected $colors=[
		'Comment'=>'lightgreen',
		'Error'=>'lightblue',
		'Exception'=>'lightpink'
	];

	protected $jsPath = 'js/Plops.js';
	protected $cssPath = 'css/plops.css';

	public function getOutput($data) {
		$c="<script>".$this->getJS()."</script>";
		$c.="<style>".$this->getCSS()."</style>";
		$c.=$this->drawErrorBox($data['events']);
		return $c;
	}

	public function drawErrorBox($events) {
		$c="<div id='errorBox'>";
		foreach ($events as $event) {
			$c.=$this->drawException($event);
		}
		$c.="</div>";
		return $c;
	}

	function getExceptionColour($event) {
		$type = $event['errorType'];
		if (isset($this->colors[$type])) return $this->colors[$type];
		return 'lightgrey';
	}

	function drawException($exception) {
		$color = $this->getExceptionColour($exception);
		$id = uniqid();
		$c="
		<div class='exception' onClick='plops.showTrace(\"{$id}\");' style='background-color: {$color};'>
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
				".$this->drawLines($entry['codeLines'],$entry['line'])."
			</div>";
		}
		return $c;
	}

	function drawLines($lines, $mainLineNo) {
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
}