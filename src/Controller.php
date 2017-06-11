<?php

namespace DanielJHarvey\PlopCatcher;

class Controller {

	protected $outputMode = 'HTML';
	protected $validOutputModes = ['HTML', 'ARRAY', 'JSON'];

	protected $crashCallback = NULL; // callback function when system fails

	protected $logger;

	public function __construct(
		Logger $logger,
		$outputMode = 'HTML',
		$crashCallback = NULL) {
		$this->logger = $logger;
		if (in_array($outputMode,$this->validOutputModes)) {
			$this->outputMode = $outputMode;
		}
		$this->crashCallback = $crashCallback;
	}

	public function onShutdown() {
		if ($this->crashCallback) {
			$output=$this->output();
			$crashCallback($output);
		}
	}

	public function output() {
		$output = $this->getOutputClass();
		return $output->getOutput();
	}

	protected function getOutputClass() {
		if ($this->outputMode == 'HTML') {
			return new HTMLOutput($this->logger);
		} else if ($this->outputMode == 'JSON') {
			return new JSONOutput($this->logger);
		} else if ($this->outputMode == 'ARRAY') {
			return new ArrayOutput($this->logger);
		} else {
			throw new Exception("Invalid output type selected!");
		}
	}
}