<?php

namespace DanielJHarvey\PlopCatcher;

// used for returning an array of debug information that can be used in PHP

class ArrayOutput {
	
	protected $logger;

	public function __construct(Logger $logger) {
		$this->logger = $logger;
	}	

	public function getOutput() {
		return $this->logger->getEvents();
	}
}