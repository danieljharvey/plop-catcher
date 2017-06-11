<?php

namespace DanielJHarvey\PlopCatcher;

class Stats {

	protected $startTime;

	public function __construct() {
		$this->setStartTime();
	}

	protected function setStartTime() {
		$this->startTime = microtime(true);
	}

	protected function getElapsedTime() {
		$time = microtime(true);
		return $time - $this->startTime;
	}

	public function getStats() {
		return [
			'elaspedTime'=>$this->getElapsedTime()
		];
	}

}