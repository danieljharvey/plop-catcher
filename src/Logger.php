<?php

namespace DanielJHarvey\PlopCatcher;

class Logger {
	
	protected $events=[];
	protected $fileWrapper;

	public function __construct(\DanielJHarvey\FileWrapper\FileWrapper $fileWrapper) {
		$this->fileWrapper = $fileWrapper;
	}

	function getEvents() {
		return array_reverse($this->events);
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
			'message'=>"Shutdown",
			'stackTrace'=>[]
		];

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
		if (count($stackTrace)==0) return $stackTrace;

		foreach ($stackTrace as $id=>$trace) {

			if (!isset($trace['line'])) {
				unset($stackTrace[$id]);
				continue;
			}

			if ($this->traceIsPartOfLogger($trace['file'])) {
				unset($stackTrace[$id]);
				continue;
			}

			if ($this->isTraceDuplicate($trace, $id, $stackTrace)) {
				unset($stackTrace[$id]);
				continue;
			}

			if ($trace['file'] == __FILE__) {
				unset($stackTrace[$id]);
				continue;
			}
		}

		return array_values($stackTrace);
	}

	protected function isTraceDuplicate($trace, $id, $stackTrace) {
		foreach ($stackTrace as $oldID=>$oldTrace) {
			// only compare to previous entries so we don't compare with ourselves
			if ($oldID >= $id) continue;
			if ($this->compareTracesAreSame($trace, $oldTrace)) return true;
			
		}
		return false;
	}

	public function traceIsPartOfLogger($file) {
		$currentDir = $this->fileWrapper->dirName(__FILE__);
		if (strpos($file, $currentDir)!==false) return true;
		return false;
	}

	function compareTracesAreSame($trace, $oldTrace) {
		if (!isset($trace['file'])) return false;
		if (!isset($oldTrace['file'])) return false;

		if ($trace['file']==$oldTrace['file'] && $trace['line']==$oldTrace['line']) return true;
	}

}
