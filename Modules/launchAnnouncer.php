<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = array_pad(explode(' ', $raw), 10, ' ');
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

// https://launchlibrary.net/1.3/launch?next=1&mode=verbose

if ($ex[3] == $settings['plugin']['prefix'].'la' || $ex[3] == $settings['plugin']['prefix'].'launch') {
	if ($ex[4] >= 1) {
		$next = $ex[4];
	} else {
		$next = 1;
	}
	$nn = $next-1;
	$api = 'https://launchlibrary.net/1.3/launch?next='.$next.'&mode=verbose';
	$apiR = json_decode(file_get_contents($api), true);

	$name = $apiR['launches'][$nn]['name'];
	$start = $apiR['launches'][$nn]['wsstamp'];
	$end = $apiR['launches'][$nn]['westamp'];
	$vidURLs = $apiR['launches'][$nn]['vidURLs'];
	unset($apiR);

	$title = "\002".$name."\002";
	$title .= " Window: \002".date('d:m:Y H:i:s',$start)."\002 to \002".date('d:m:Y G:i:s', $end)."\002";

	if (sizeof($vidURLs) >= 1) {
		$title .= " - ".$vidURLs[0];
	} else {
		$title .= " - N/A";
	}

	$ret['method'] = 'privmsg';
	$ret['data'] =$title;
	echo json_encode($ret);
	die();
}
if ($ex[3] == $settings['plugin']['prefix'].'help') {
	if ($ex[4] == 'launch') {
		$ret['method'] = 'privmsg';

		$ret['data'] = 'Get the next rocket launch: name, launch window, video url';
		echo json_encode($ret).PHP_EOL;
		$ret['data'] = 'Example: !launch';
		echo json_encode($ret);
		die();
	}
}