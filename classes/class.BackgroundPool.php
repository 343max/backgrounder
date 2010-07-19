<?php

class BackgroundPool {
	private $serverUrl = '';
	private $serverHost = '';

	private $processes = array();

	public function __construct($serverUrl, $serverHost = null) {
		$this->serverUrl = $serverUrl;
		$this->serverHost = ($serverHost ? $serverHost : preg_replace('/http:\\/\\/([^\/:]+).*/', '\\1', $serverUrl));
	}

	public function addController(BackgroundController $controller) {
		$this->processes[] = $controller;
	}

	public function add(BackgroundWorker $worker) {
		$controller = new BackgroundController($this->serverUrl, $this->serverHost);
		$controller->sendRequest($worker);
		$this->addController($controller);

		return $controller;
	}

	/**
	 * @return BackgroundWorkerController
	 */
	public function nextWorkerLoop() {
		for($i = 0; $i < count($this->processes); $i++) {
			$controller = $this->processes[$i];
			if(!$controller->readResponseSnippet()) {
				array_splice($this->processes, $i, 1);

				$worker = $controller->getResponseWorker();
				$worker->synchronize();

				return $worker;
			}
		}

		return null;
	}

	/**
	 * @return BackgroundWorkerController
	 */
	public function waitForNextCompleteWorker($timeOut = 0) {
		if(count($this->processes) == 0) return null;

		$startTime = microtime(TRUE);

		while(!$finishedWorker = $this->nextWorkerLoop()) {
			if($timeOut > 0) {
				if(microtime(TRUE) - $startTime > $timeOut) return null;
			}
		}

		return $finishedWorker;
	}

	public function waitForAllWorkers($timeOut = 0) {
		$startTime = microtime(TRUE);

		while($worker = $this->waitForNextCompleteWorker($timeOut - (microtime(TRUE) - $startTime))) {
			//
		}
	}
}