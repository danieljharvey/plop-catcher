<?php

namespace DanielJHarvey\PlopCatcher;

class Plop {
	
	// core elements
	protected $controller;
	protected $logger;
	protected $errorCatcher;
	protected $stats;

	// helpers
	protected $fileWrapper;

	// properties
	protected $enabled = false;
	protected $outputMode;
	protected $crashCallback = NULL;

	public function __construct($outputMode='HTML',$crashCallback = NULL) {
		$this->outputMode = $outputMode;
		$this->crashCallback = $crashCallback;
	}

	public function output() {
		if (!$this->enabled) return false;
		return $this->controller->output();
	}

	public function logComment($comment) {
		if (!$this->enabled) return false;
		$this->logger->logComment($comment);
	}

	protected function setUp($outputMode, $crashCallback) {
		$this->fileWrapper = new \DanielJHarvey\FileWrapper\FileWrapper;
		$this->logger = new Logger($this->fileWrapper);
		$this->stats = new Stats();
		$this->state = new State([
			'post'=>$_POST,
			'get'=>$_GET,
			'server'=>$_SERVER,
			'files'=>$_FILES
		]);
		$this->controller = new Controller($this->logger, $this->stats, $this->state, $outputMode, $crashCallback);
		$this->errorCatcher = new ErrorCatcher($this->logger, $this->controller);
		$this->errorCatcher->enable();
		$this->enabled = true;
	}

	// take over error handlers
	public function enable() {
		if ($this->enabled) return;
		$this->setUp($this->outputMode, $this->crashCallback);
		$this->enabled = true;
	}

	// return error handlers to normal
	public function disable() {
		if (!$this->enabled) return;
		$this->errorCatcher->disable();
		$this->enabled = false;
	}
}