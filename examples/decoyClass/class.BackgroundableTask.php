<?php

class BackgroundableTask {
	private static $instance;
	private static $decoyInstance;

	private function __construct() {}

	/**
	 * @static
	 * @param bool $decoyObject
	 * @return BackgroundableTask
	 */
	public static function getInstance($decoyObject = TRUE) {
		if($decoyObject) {
			return self::getDecoyInstance();
		}

		if(!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @static
	 * @return BackgroundDecoy
	 */
	private static function getDecoyInstance() {
		if(!self::$decoyInstance) {
			self::$decoyInstance = new BackgroundDecoy(__CLASS__);
		}

		return self::$decoyInstance;
	}

	public function foo($a, $b) {
		sleep(1);
		$params = func_get_args();
		file_put_contents('/tmp/decoy.log', __METHOD__ . '(' . join(', ', $params) . ");\n", FILE_APPEND);
	}

	public function bar($x, $y, $z) {
		sleep(1);
		$params = func_get_args();
		file_put_contents('/tmp/decoy.log', __METHOD__ . '(' . join(', ', $params) . ");\n", FILE_APPEND);
	}

	public function fooBar($a, $b, $c) {
		sleep(1);
		$params = func_get_args();
		file_put_contents('/tmp/decoy.log', __METHOD__ . '(' . join(', ', $params) . ");\n", FILE_APPEND);
	}

}