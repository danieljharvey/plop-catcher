<?php

namespace DanielJHarvey\PlopCatcher;

class State {
	
	protected $state;

	public function __construct($state) {
		$this->state = $state;
	}

	public function getState() {
		return $this->state;
	}
}