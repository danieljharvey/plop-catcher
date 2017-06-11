<?php

namespace DanielJHarvey\PlopCatcher;

// used for creating debugging JSON that can be saved directly to a log file

class JSONOutput {
	
	protected $logger;

	public function __construct(Logger $logger) {
		$this->logger = $logger;
	}

	public function getOutput() {
		$events = $this->logger->getEvents();
		return json_encode($events);
	}
}