<?php

namespace DanielJHarvey\PlopCatcher;

class ErrorCatcher {
	
	protected $active = false;
	protected $logger;
	protected $controller;

	public function __construct(Logger $logger, Controller $controller) {
		$this->logger = $logger;
		$this->controller = $controller;
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
			$this->controller->onShutdown();
		} catch (\Exception $e) {
			// something's gone wrong in shutdown
			echo "<p class='red'>Exception triggered in Custom Exception Handler, cannot handle</p>";
		}
	}

	public function shutdownHandler() {
	  	$error = error_get_last();

	  	// if just shutting down normally, leave it to it
	  	if (!in_array($error['type'],[E_ERROR,E_COMPILE_ERROR])) { 
	        return false;    
	    }
	    $this->logger->logShutdown($error);
	    $this->controller->onShutdown(); // possibly do a last minute output of something
	    return true;
	}

}