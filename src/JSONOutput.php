<?php

namespace DanielJHarvey\PlopCatcher;

// used for creating debugging JSON that can be saved directly to a log file

class JSONOutput {
	
	public function getOutput($data) {
		return json_encode($data);
	}
}