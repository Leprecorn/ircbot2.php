<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = explode(' ', $raw);
$ex = array_pad($ex, sizeof($ex)+4, null);
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

if ($ex[3] == $settings['plugin']['prefix'].'yts' || $ex[3] == $settings['plugin']['prefix'].'youtubesearch') {
	//
}