<?php

namespace DanielJHarvey\PlopCatcher;

class ErrorCatcher {
	
	protected $active = false;
	protected $logger;

	public function __construct(Logger $logger) {
		$this->logger = $logger;
	}

	public function getActive() {
		return $this->active;
	}

	public function enable() {
		$this->setErrorHandlers();
		$this->active = true;
	}

	public function disable() {
		$this->resetErrorHandlers();
		$this->active = false;
	}

	protected function resetErrorHandlers() {
		set_error_handler(NULL);
		set_exception_handler(NULL);
		register_shutdown_function(NULL);
	}

	protected function setErrorHandlers() {
		set_error_handler([
			$this,
			'errorHandler'
		]);
		set_exception_handler([
			$this,
			'exceptionHandler'
		]);
		register_shutdown_function([
			$this,
			"shutdownHandler"
		]);
	}	

	public function errorHandler($severity, $message, $filename, $lineno) {
		if (error_reporting() == 0) {
	    	return false;
	  	}
	  	if (error_reporting() && $severity) {
	  		try {
	  			throw new \ErrorException($message, 0, $severity, $filename, $lineno);	
	  		} catch (\ErrorException $e) {
	  			$this->logger->logException($e, 'Error');
	  		}
	    	return true;
	  	}
	}

	public function exceptionHandler($exception) {
		try {
			$this->logger->logException($exception, 'Exception');
			//echo "<h1>EXCEPTION HANDLER</h1>";
			//echo outputErrorBox();	
		} catch (\Exception $e) {
			//echo "<h1>exception handler for the exception handler</h1>";
		}
	}

	public function shutdownHandler() {
	  	$error = error_get_last();

	  	// if just shutting down normally, leave it to it
	  	if (!in_array($error['type'],[E_ERROR,E_COMPILE_ERROR])) { 
	        return false;    
	    }
	    $this->logger->logShutdown($error);
	    return true;
	}

}