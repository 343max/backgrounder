<?php

ini_set('include_path', ini_get('include_path') . ':' . dirname(__FILE__) . '/../classes');

function __autoload($className) {
	require_once('class.' . $className . '.php');
}