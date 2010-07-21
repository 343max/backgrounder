<?php

require_once(dirname(__FILE__) . '/../../init.php');

$startTime = microtime(TRUE);

// Create a new pool for our workers
$pool = new BackgroundPool('http://localhost/backgrounder/examples/headless/server.php');

$worker = new HeadlessWorker();
$pool->addAndRun($worker);

// now our background process is running, even when this process exits

echo 'completed in ' . (microtime(TRUE) - $startTime) . ' tail -f /tmp/background.log to see some results.';