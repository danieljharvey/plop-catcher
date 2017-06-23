<?php

include_once ('../vendor/autoload.php');

use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {

	protected $fileWrapper;

	function setUp() {
		$this->fileWrapper = $this->getMockBuilder("\DanielJHarvey\FileWrapper\FileWrapper")
			->getMock();
	}

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

		$logger = new \DanielJHarvey\PlopCatcher\Logger($this->fileWrapper);

		$expected = false;

		$return = $logger->compareTracesAreSame($trace, $oldTrace);
    	
    	$this->assertEquals(
			$expected,
			$return,
			"Could not deal with weird class based exception"
    	);
    }
	
	function traceIsPartOfLoggerData() {
		return [
			[
				'/path/to/logger/blah.php',
				true
			],
			[
				'path/elsewhere/internet.php',
				false
			],
			[
				'path/to/logger/somethingelse/yeah.php',
				true
			]	
		];
	}

	/**
	* @test
	* @dataProvider traceIsPartOfLoggerData
	*/
	public function testRemoveLoggerFileTraces($filename, $expected) {
		$this->fileWrapper->expects($this->once())
			->method('dirName')
			->will($this->returnValue('path/to/logger'));
		
		$logger = new \DanielJHarvey\PlopCatcher\Logger($this->fileWrapper);

		$return = $logger->traceIsPartOfLogger($filename);

		$this->assertEquals(
			$expected,
			$return,
			"Could not identify files belonging to this project"
		);
	}
}
