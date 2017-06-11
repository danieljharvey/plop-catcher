<?php

namespace \DanielJHarvey\PlopCatcher;

class ArrayOutput extends Output {
	
	protected $logger;

	public function __construct(Logger $logger) {
		$this->logger = $logger;
	}	

	public function getOutput() {
		return $this->logger->getEvents();
	}
}