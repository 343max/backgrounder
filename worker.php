<?php

$payload = unserialize(file_get_contents("php://input"));

$sleepTime = rand(500, 1500) * 1000;

$payload['sleep'] = $sleepTime / 1000000;

usleep($sleepTime);

echo serialize($payload);