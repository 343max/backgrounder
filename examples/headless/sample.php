<?php

require_once(dirname(__FILE__) . '/../../init.php');

$startTime = microtime(TRUE);

// Create a new pool for our workers
$pool = new BackgroundPool('http://localhost/backgrounder/examples/headless/server.php');

$worker = new HeadlessWorker();
$pool->add($worker);

#$pool->waitForAllWorkers(2);