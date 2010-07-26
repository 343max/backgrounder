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
		if(!BackgroundAuthentification::validate(@$_SERVER['PHP_AUTH_USER'], @$_SERVER['PHP_AUTH_PW'])) {
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: Basic realm="Service"');
			echo 'wrong password!';
			return false;
		}

		$worker = self::getWorker();

		try {
			$worker->runThread();
			self::printResult($worker);
		} catch(Exception $exception) {
			self::printResult($exception);
		}

	}

}