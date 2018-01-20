<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = explode(' ', $raw);
$ex = array_pad($ex, sizeof($ex)+4, null);
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

if ($ex[3] == $settings['plugin']['prefix'].'drumroll') {
	$ret['method'] = 'privmsg';
	$ret['data'] = 'Ladies and gentlemen!';
	echo json_encode($ret).PHP_EOL;

	$ret['method'] = 'eval';
	$ret['data'] = 'sleep(1);';
	echo json_encode($ret).PHP_EOL;

	$ret['method']= 'privmsg';
	$ret['data'] = 'The moment you have all been waiting for!';
	echo json_encode($ret).PHP_EOL;

	$ret['method'] = 'eval';
	$ret['data'] = 'sleep(1);';
	echo json_encode($ret).PHP_EOL;

	$ret['method']= 'privmsg';
	$ret['data'] = '*drumroll*';
	echo json_encode($ret);
	die();
}
if ($ex[3] == $settings['plugin']['prefix'].'peel') {
	$ret['method'] = 'privmsg';
	$ret['data'] = "\001ACTION peels some ".$ex[4]." \001";
	echo json_encode($ret);
	die();
}
if ($ex[3] == $settings['plugin']['prefix'].'person') {
	$people = array(
		"gm" => "993",
		"joe" => "917"
	);
	if (array_key_exists($ex[4], $people)) {
		$ret['method'] = 'privmsg';
		$ret['data'] = "!quote ".$people[$ex[4]];
		echo json_encode($ret);
		die();
	} else {
		$ret['method'] = 'privmsg';
		$ret['data'] = 'No such person';
		echo json_encode($ret);
		die();
	}
}

// */
