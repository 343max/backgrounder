<?php

class SampleWorker implements BackgroundWorker {
	private $sleepTime = 0;
	private $processNumber = 0;

	public function __construct($processNumber) {
		$this->processNumber = $processNumber;
	}

	public function runThread() {
		$this->sleepTime = rand(500, 1500) * 1000;

		usleep($this->sleepTime);
	}

	public function synchronize() {
		$sleepTime = $this->sleepTime / 1000000;
		echo 'background process ' . $this->processNumber . ' slept for ' . $sleepTime . ' seconds<br>';
	}

}