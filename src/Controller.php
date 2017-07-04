<?php

namespace DanielJHarvey\PlopCatcher;

class Controller {

	protected $outputMode = 'HTML';
	protected $validOutputModes = ['HTML', 'ARRAY', 'JSON'];

	protected $crashCallback = NULL; // callback function when system fails
	protected $destructCallback = NULL; // callback function when this object is destructing

	protected $logger;
	protected $stats;
	protected $state;

	protected $hasOutput = false;

	public function __construct(Logger $logger, Stats $stats, State $state, $outputMode = 'HTML', $crashCallback = NULL, $destructCallback = NULL) {
		$this->logger = $logger;
		$this->stats = $stats;
		$this->state = $state;

		if (in_array($outputMode,$this->validOutputModes)) {
			$this->outputMode = $outputMode;
		}
		$this->crashCallback = $crashCallback;
		$this->destructCallback = $destructCallback;
	}

	public function output() {
		$output = $this->getOutputClass();
		$data = $this->getData();
		return $output->getOutput($data);
	}

	public function onShutdown() {
		if ($this->crashCallback) {
			$data = $this->getData();
			$output=$this->output($data);
			$callback = $this->crashCallback;
			$this->hasOutput = true;
			$callback($output);
		}
	}

	public function onDestruct() {
		if ($this->hasOutput) return false; // don't need this if already done crashCallback
		if ($this->destructCallback) {
			$data = $this->getData();
			$output=$this->output($data);
			$callback = $this->destructCallback;
			$this->hasOutput = true;
			$callback($output);
		}
	}

	protected function getData() {
		return [
			'stats'=>$this->stats->getStats(),
			'events'=>$this->logger->getEvents(),
			'state'=>$this->state->getState()
		];
	}

	protected function getOutputClass() {
		if ($this->outputMode == 'HTML') {
			return new HTMLOutput();
		} else if ($this->outputMode == 'JSON') {
			return new JSONOutput();
		} else if ($this->outputMode == 'ARRAY') {
			return new ArrayOutput();
		} else {
			throw new Exception("Invalid output type selected!");
		}
	}
}