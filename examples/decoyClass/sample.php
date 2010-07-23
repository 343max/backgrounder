<?php

require_once('../init.php');

BackgroundDecoy::$serverUrl = 'http://localhost/backgrounder/examples/decoyClass/server.php';

$object = BackgroundableTask::getInstance();
$object->foo('hallo', 'welt');
$object->bar(1, 2, 3);
$object->fooBar('just', 'another', 'call');

unset($object);

?>
<p>tail -f /tmp/decoy.log</p>
<p>Take a look at sample.php: there is not much more then a singleton with 2 calls. These two calls will be executed instantanious, even each of them takes a second to run.</p>
<p>BackgroundDecoy will take them transparently to the Background. Even your IDE won't see the difference, so auto completion and everything is still working.</p>
<p>The magic lies in the BackgroundableTask::getInstance() and BackgroundableTask::getDecoyInstance() methods.</p>