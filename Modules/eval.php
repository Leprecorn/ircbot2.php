<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = explode(' ', $raw);
$ex = array_pad($ex, sizeof($ex)+4, null);
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

if ($ex[3] == $settings['plugin']['OPprefix'].'eval') {
	$allowed[] = ':iCherry!iCherry@iCherry.users.quakenet.org';
	if (in_array($ex[0], $allowed)) {
		$cmd = '';
		for ($i = 4; $ex[$i] != null; $i++) {
			$cmd .= ' '.$ex[$i];
		}
		$ret['method'] = 'eval';
		$ret['data'] = $cmd;
		die();
	}
}

?>