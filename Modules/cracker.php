<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = array_pad(explode(' ', $raw), 10, ' ');
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);

$crackerPrizes['prizes'][] = 'A Big Dick';
$crackerPrizes['prizes'][] = 'A Big Mince Pie';

// Cracker command
if (strtolower($ex[3]) == $settings['plugin']['prefix'].'cracker') {
	if ($ex[4] == null) {
		$rep['method'] = 'privmsg';
		$rep['data'] = 'You cannot use a cracker by yourself!';
		echo json_encode($rep);
		die();
	} else {
		$player[0] = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));
		$player[1] = $ex[4];

		$win = $player[rand(0,1)];
		$rep['method'] = 'privmsg';
		$rep['data'] = 'The cracker pops open! '.$win.' has the winning half!';
		echo json_encode($rep).PHP_EOL;
		$rep['data'] = 'They win '.$crackerPrizes['prizes'][array_rand($crackerPrizes['prizes'], 1)];
		echo json_encode($rep);
		die();

	}
}
?>