<?php

namespace DanielJHarvey\PlopCatcher;

class Logger {
	
	protected $events=[];

	function getEvents() {
		return $this->events;
	}

	function logException($e, $type) {
		$data = $this->getExceptionData($e, $type);
		$data['stackTrace'] = $this->cleanStackTrace($data['stackTrace']);
		array_push($this->events, $data);
		return $this->events;
	}

	function logComment($message) {
		try {
			throw new \Exception();
		} catch (\Exception $e) {
			$trace = $e->getTrace();
			$commentFile = $trace[0]['file'];
			$commentLine = $trace[0]['line'];
		}

		$data=[
			'errorType'=>"Comment",
			'file'=>$commentFile,
			'line'=>$commentLine,
			'message'=>$message,
			'stackTrace'=>$trace
		];
		$stackTrace= $this->addMainToStackTrace($data, $trace);
		$data['stackTrace'] = $this->cleanStackTrace($stackTrace);

		array_push($this->events, $data);
		return $this->events;
	}

	function logShutdown($error) {
	    $data = [
			'errorType'=>'Fatal Error',
			'file'=>"Unknown",
			'line'=>0,
			'message'=>"Shutdown"
		];
		$data['stackTrace'] = $this->addMainToStackTrace($data, []);

	  	if ($error !== NULL) {
	  		$data['file'] = $error["file"];
	  		$data['line'] = $error['line'];
	    	$data['message'] = $error['message'];
	   	}
	   	array_push($this->events, $data);
		return true;
	}

	protected function getExceptionData($e, $type) {
		$data=[
			'errorType'=>$type,
			'file'=>$e->getFile(),
			'line'=>$e->getLine(),
			'message'=>$e->getMessage()
		];
		$stackTrace = $this->addMainToStackTrace($data, $e->getTrace());
		$data['stackTrace'] = $this->cleanStackTrace($stackTrace);
		return $data;
	}

	protected function addMainToStackTrace($main, $stackTrace) {
		$mainStack=[
			'file'=>$main['file'],
			'line'=>$main['line'],
			'function'=>'Main',
			'args'=>[]
		];
		$stackTrace=array_merge([$mainStack],$stackTrace);
		return $stackTrace;
	}

	protected function cleanStackTrace($stackTrace) {
		if (count($stackTrace)==0) return $data;
		
		foreach ($stackTrace as $id=>$trace) {

			if (!isset($trace['line'])) {
				var_dump($trace);
			}

			if ($this->isTraceDuplicate($trace, $id, $stackTrace)) {
				unset($stackTrace[$id]);
				continue;
			}

			if ($trace['file'] == __FILE__) {
				unset($stackTrace[$id]);
				continue;
			}

			$codeLines = $this->getCodeLines($trace['file'], $trace['line']);
			$stackTrace[$id]['codeLines'] = $codeLines;
		}

		return array_values($stackTrace);
	}

	protected function isTraceDuplicate($trace, $id, $stackTrace) {
		foreach ($stackTrace as $oldID=>$oldTrace) {
			// only compare to previous entries so we don't compare with ourselves
			if ($oldID >= $id) continue;
			if ($trace['file']==$oldTrace['file'] && $trace['line']==$oldTrace['line']) return true;
		}
		return false;
	}

	protected function getCodeLines($filename, $lineNo) {
		$lines = $this->readFileAsArray($filename);
		if (!$lines) return false;
		return $this->parseCodeLines($lines, $lineNo);
	}

    protected function readFileAsArray($filename) {
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