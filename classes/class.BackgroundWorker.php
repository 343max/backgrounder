<?php

interface BackgroundWorker {
	public function runThread();
	public function synchronize();
}