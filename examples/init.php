<?php

ini_set('include_path', ini_get('include_path') . ':' . dirname(__FILE__) . '/../classes');

BackgroundAuthentification::$hashSeed = 'change me! ' . filemtime(__FILE__) . __FILE__;

function __autoload($className) {
	require_once('class.' . $className . '.php');
}