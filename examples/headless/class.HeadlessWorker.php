<?php

class HeadlessWorker implements BackgroundWorker {

	public function runThread() {
		ignore_user_abort('1');

		for($i = 0; $i < 20; $i++) {
			file_put_contents('/tmp/background.log', "Loop #$i\n", FILE_APPEND);
			usleep(500000);
		}
	}

	public function synchronize() {
		throw new Exception('HeradlessWorkers must never be synchronized');
	}

}