<?php

// Collects together stats about the code
// elapsedTime - ms total execution between start of logging and output
// 

namespace DanielJHarvey\PlopCatcher;

class Stats {

	protected $startTime;
	protected $lastTick;
	protected $largestTick=0;
	protected $countTicks=0;

	public function __construct() {
		$this->setStartTime();
		//$this->setTickHandler();
	}

	public function __destruct() {
		$this->resetTickHandler();
	}

	protected function setStartTime() {
		$this->startTime = microtime(true);
		$this->lastTick = $this->startTime;
	}

	protected function getElapsedTime($oldTime) {
		$time = microtime(true);
		return $time - $oldTime;
	}

	public function getStats() {
		$this->tickHandler();
		return [
			'elaspedTime'=>$this->getElapsedTime($this->startTime)/*,
			'largestTick'=>$this->largestTick,
			'averageTick'=>$this->getElapsedTime($this->startTime) / $this->countTicks*/
		];
	}

	protected function setTickHandler() {
		declare(ticks=1);
		register_tick_function([
			$this,
			'tickHandler'
		]);
	}

	protected function resetTickHandler() {
		unregister_tick_function(NULL);
	}

	public function tickHandler($message=false) {
		$this->countTicks++;
		$elapsed = $this->getElapsedTime($this->lastTick);
		if ($elapsed > $this->largestTick) {
			$this->largestTick = $elapsed;
		}
		$this->lastTick = microtime(true);
	}

}