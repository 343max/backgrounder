<?php

class BackgroundWorkerQueue implements BackgroundWorker {
	private $workers = array();

	public function addWorker(BackgroundWorker $worker) {
		$this->workers[] = $worker;
	}

	public function runThread() {
		foreach($this->workers as $worker) {
			$worker->runThread();
		}
	}

	public function synchronize() {
		foreach($this->workers as $worker) {
			$worker->synchronize();
		}
	}
}