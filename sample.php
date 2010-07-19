<?php

require_once(dirname(__FILE__) . '/init.php');

$startTime = microtime(TRUE);

// Create a new pool for our workers
$pool = new BackgroundPool('http://localhost/backgrounder/sampleServer.php');

// initialize some worker classes, add them to the pool and fire up the requests to start the process
for($i = 0; $i < 10; $i++) {
	$worker = new SampleWorker($i);
	$pool->add($worker);
}

// some benchmarks
echo 'all requests sent: ' . (microtime(TRUE) - $startTime) . '<br>';

// wait for our workers to complete
$pool->waitForAllWorkers();

// final benchmark
echo 'done: ' . (microtime(TRUE) - $startTime);
