<?php

namespace DanielJHarvey\PlopCatcher;

class Controller {

	protected $outputMode = 'HTML';
	protected $validOutputModes = ['HTML', 'ARRAY', 'JSON'];

	protected $crashCallback = NULL; // callback function when system fails

	protected $logger;
	protected $stats;

	public function __construct(Logger $logger, Stats $stats, $outputMode = 'HTML', $crashCallback = NULL) {
		$this->logger = $logger;
		$this->stats = $stats;

		if (in_array($outputMode,$this->validOutputModes)) {
			$this->outputMode = $outputMode;
		}
		$this->crashCallback = $crashCallback;
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
			$callback($output);
		}
	}

	protected function getData() {
		return [
			'stats'=>$this->stats->getStats(),
			'events'=>$this->logger->getEvents()
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