<?php

include_once ('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {

	public function testCompareTraces() {
		$oldTrace=[
			'file' =>'/Users/Daniel/Sites/plop-catcher/src/ErrorCatcher.php',
  			'line' => 68,
  			'function' => 'onShutdown',
			'class' => 'DanielJHarvey\PlopCatcher\Controller',
  			'type' => '->',
  			'args' => []
  		];

  		$trace = [
  			'function' => 'exceptionHandler',
			'class' => 'DanielJHarvey\PlopCatcher\ErrorCatcher',
  			'type' => '->',
			'args' => []
		];

		$logger = new \DanielJHarvey\PlopCatcher\Logger();

		$expected = false;

		$return = $logger->compareTracesAreSame($trace, $oldTrace);
    	
    	$this->assertEquals(
			$expected,
			$return,
			"Could not deal with weird class based exception"
    	);
    }
}