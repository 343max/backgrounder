<?php

class BackgroundDecoy implements BackgroundWorker {
	private $modelClassName = '';
	private $methodCallQueue = array();

	private $serverMode = FALSE;

	private $output = '';

	static $serverUrl = '';

	public function __construct($modelClassName) {
		$this->modelClassName = $modelClassName;
	}

	public function __call($methodName, $arguments) {
		$this->methodCallQueue[] = array(
			'method' => $methodName,
			'arguments' => $arguments
		);
	}

	public function __destruct() {
		if($this->serverMode) return;

		$this->serverMode = true;

		$pool = new BackgroundPool(self::$serverUrl);
		$pool->addAndRun($this);
	}

	public function runThread() {
		ignore_user_abort(1);

		ob_start();

		$className = $this->modelClassName;
		$object = call_user_func_array($className . '::getInstance', array(FALSE));
		foreach($this->methodCallQueue as $call) {
			call_user_func_array(array($object, $call['method']), $call['arguments']);
		}

		$this->output = ob_get_contents();
		ob_end_clean();
	}

	public function synchronize() {
		echo $this->output;
	}
}