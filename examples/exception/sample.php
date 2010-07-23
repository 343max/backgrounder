<?php

require_once(dirname(__FILE__) . '/../init.php');

// Create a new pool for our workers
$pool = new BackgroundPool('http://localhost/backgrounder/examples/exception/server.php');

$worker = new ExceptionWorker();
$pool->addAndRun($worker);

try {
	$pool->waitForAllWorkers();
} catch(Exception $exception) {
	echo 'Caught an Exception from an thread:<br>' . $exception->getMessage() . '<br>Bummer!';
}