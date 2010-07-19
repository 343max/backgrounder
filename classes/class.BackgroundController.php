<?php

class BackgroundController {
	private $serverUrl = '';
	private $serverHost = '';
	private $socketPointer = null;
	private $httpResponse = '';

	public function __construct($serverUrl, $serverHost = null) {
		$this->serverUrl = $serverUrl;
		$this->serverHost = ($serverHost ? $serverHost : preg_replace('/http:\\/\\/([^\/:]+).*/', '\\1', $serverUrl));
	}

	public function sendRequest(BackgroundWorker $object) {
		$serializedParameters = serialize($object);

		$request = array(
			"POST " . $this->serverUrl . " HTTP/1.0",
			"Content-Length: " . strlen($serializedParameters),
			"",
			$serializedParameters,
		);

		$this->socketPointer = fsockopen($this->serverHost, 80);
		stream_set_blocking($this->socketPointer, 0);
		fwrite($this->socketPointer, join("\r\n", $request));
	}

	/**
	 * Reads the next few kbytes - returns true as long as there is data
	 * @return bool
	 */
	public function readResponseSnippet() {
		if(!$this->socketPointer) return false;

		if(feof($this->socketPointer)) {
			fclose($this->socketPointer);
			$this->socketPointer = null;
			return false;
		}

		$this->httpResponse .= fread($this->socketPointer, 8192);
		
		return true;
	}

	public function getHttpResponse() {
		return $this->httpResponse;
	}

	private function getRawResponse() {
		$httpResponse = $this->getHttpResponse();
		return substr($httpResponse, stripos($httpResponse, "\r\n\r\n") + 4);
	}

	public function getResponseWorker() {
		$worker = unserialize(trim($this->getRawResponse()));

		if(!$worker) {
			throw new Exception('Could not unserialize response: ' . $this->getRawResponse());
		}

		return $worker;
	}

	public function hasUnprocessedData() {
		if(!$this->socketPointer) return false;

		return !feof($this->socketPointer);
	}

	public function waitForResult() {
		while ($this->readResponseSnippet()) {
			//
		}

		return $this->getResponseWorker();
	}
}

