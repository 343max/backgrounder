<?php

class BackgroundServer {

	/**
	 * @static
	 * @throws Exception
	 * @return BackgroundWorker
	 */
	public static function getWorker() {
		$postData = file_get_contents("php://input");
		$worker = unserialize($postData);

		if(!$worker) {
			throw new Exception('Could not unserialize parameters: ' . $postData);
		}

		return $worker;
	}

	public static function printResult($worker) {
		echo serialize($worker);
	}
	
	public static function serve() {
		$worker = self::getWorker();

		$worker->runThread();

		self::printResult($worker);
	}

}