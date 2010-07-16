<?php

class backgrounderWorker {
	private $fp = null;
	private $httpResponse = '';
	
	public function sendRequest($payload) {
		$serializedPayload = serialize($payload);

		$request = array(
			"POST http://localhost/backgrounder/worker.php HTTP/1.0",
			"Content-Length: " . strlen($serializedPayload),
			"",
			$serializedPayload,
			"",
			"",
			""
		);

		$this->fp = fsockopen('localhost', 80);
		#stream_set_write_buffer($this->fp, 0);
		stream_set_blocking($this->fp, 0);
		fwrite($this->fp, join("\r\n", $request));
	}

	/**
	 * Reads the next few kbytes - returns true as long as there is data
	 * @return bool
	 */
	public function readResponseSnippet() {
		if(!$this->fp) return false;

		if(feof($this->fp)) {
			fclose($this->fp);
			$this->fp = null;
			return false;
		}

		$streamMetaData = stream_get_meta_data($this->fp);

		$unreadBytes = $streamMetaData['unread_bytes'];

		if($unreadBytes != 0) var_dump($unreadBytes);#flush();$unreadBytes++;

		#if($unreadBytes > 0) {
			$this->httpResponse .= fread($this->fp, 8192);
		#}

		return true;
	}

	public function getHttpResponse() {
		return $this->httpResponse;
	}

	private function getRawResponse() {
		$httpResponse = $this->getHttpResponse();
		return substr($httpResponse, stripos($httpResponse, "\r\n\r\n") + 4);
	}

	public function getResponse() {
		return unserialize(trim($this->getRawResponse()));
	}

	public function hasUnprocessedData() {
		if(!$this->fp) return false;

		return !feof($this->fp);
	}

	public function waitForResult() {
		while ($this->readResponseSnippet()) {
			//
		}

		return $this->getResponse();
	}
}

class backgrounderPool {
	private $process = array();

	public function add(backgrounderWorker $worker) {
		$this->process[] = $worker;
	}

	/**
	 * @return backgrounderWorker
	 */
	private function getNextResult() {
		for($i = 0; $i < count($this->process); $i++) {
			$worker = $this->process[$i];
			if(!$worker->readResponseSnippet()) {
				array_splice($this->process, $i, 1);
				return $worker;
			}
		}

		return null;
	}

	/**
	 * @return backgrounderWorker
	 */
	public function waitForNextResult($timeOut = 0) {
		if(count($this->process) == 0) return null;

		$startTime = microtime(TRUE);

		
		while(!$finishedWorker = $this->getNextResult()) {
			if($timeOut) {
				if(microtime(TRUE) - $startTime > $timeOut) return null;
			}
		}

		return $finishedWorker;
	}
}

$startTime = microtime(TRUE);

//$backgrounders = array();
$pool = new backgrounderPool();

for($i = 0; $i < 30; $i++) {
	$backgrounder = new backgrounderWorker();
	$backgrounder->sendRequest(array('i' => $i));
	$pool->add($backgrounder);
}

echo 'all requests sent: ' . (microtime(TRUE) - $startTime);

while($worker = $pool->waitForNextResult()) {
	$response = $worker->getResponse();
	echo $response['i'] . ': ' . $response['sleep'] . '<br>';
	flush();
}

echo 'done: ' . (microtime(TRUE) - $startTime);
