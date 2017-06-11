<?php

namespace DanielJHarvey\PlopCatcher;

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