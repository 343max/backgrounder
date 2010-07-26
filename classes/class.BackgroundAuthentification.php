<?php

class BackgroundAuthentification {
	static public $hashSeed = '';

	static public function getRandomUsername() {
		return 'BackgroundUser' . rand(0, 1000000) . microtime(true);
	}

	static public function getPasswordForUser($userName) {
		if(!self::$hashSeed) throw new Exception(__CLASS__ . '::$hashSeed not initailized');

		return sha1($userName . self::$hashSeed);
	}

	static public function authorizationHttpHeader() {
		$userName = self::getRandomUsername();
		$password = self::getPasswordForUser($userName);

		return "Authorization: Basic " . base64_encode($userName . ':' . $password);
	}

	static public function validate($userName, $password) {
		return self::getPasswordForUser($userName) == $password;
	}
}