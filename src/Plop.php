<?php

// all hail Plop Catcher, PHP error catching and logging thing
// when building, specify which kind of output method you would like ('HTML', 'ARRAY', 'JSON')
// and two callbacks -> $crashCallback which is called on a fatal PHP error and allows logging etc
// $destructCallback which is called whenever the Plop Catcher destructs (ie, usually on PHP shutdown, assuming the object is referenced globally or in a main object)

// both are optional - spitting out the HTML of error is suitable in dev but not really useful in live, although arguably making error info available in a more subtle way for lve diagnostics can be helpful

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
	protected $destructCallback = false; // whether to automatically output on the __destructor of this object - helpful for making sure HTML logger is shown when no clear output() function

	public function __construct($outputMode='HTML',$crashCallback = NULL, $destructCallback = NULL) {
		$this->outputMode = $outputMode;
		$this->crashCallback = $crashCallback;
		$this->destructCallback = $destructCallback;
	}

	public function output() {
		if (!$this->enabled) return false;
		return $this->controller->output();
	}

	public function logComment($comment) {
		if (!$this->enabled) return false;
		$this->logger->logComment($comment);
	}

	protected function setUp($outputMode, $crashCallback, $destructCallback) {
		$this->fileWrapper = new \DanielJHarvey\FileWrapper\FileWrapper;
		$this->logger = new Logger($this->fileWrapper);
		$this->stats = new Stats();
		$this->state = new State([
			'post'=>$_POST,
			'get'=>$_GET,
			//'server'=>$_SERVER, // too much sensitive stuff to be spitting out into userland?
			'files'=>$_FILES
		]);
		$this->controller = new Controller($this->logger, $this->stats, $this->state, $outputMode, $crashCallback, $destructCallback);
		$this->errorCatcher = new ErrorCatcher($this->logger, $this->controller);
		$this->errorCatcher->enable();
		$this->enabled = true;
	}

	// take over error handlers
	public function enable() {
		if ($this->enabled) return;
		$this->setUp($this->outputMode, $this->crashCallback, $this->destructCallback);
		$this->enabled = true;
	}

	// return error handlers to normal
	public function disable() {
		if (!$this->enabled) return;
		$this->errorCatcher->disable();
		$this->enabled = false;
	}
}