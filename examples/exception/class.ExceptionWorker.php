<?php

class ExceptionWorker implements BackgroundWorker {

	public function runThread() {
		throw new Exception('This exception was thrown by the worker process and should bubble up to the parent process where it should be handled.');
	}

	public function synchronize() {

	}

}