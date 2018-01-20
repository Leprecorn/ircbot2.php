<?php

require_once './data/irc.class.php';
require_once "./data/default.php";

if (!set_time_limit(0)) {
	output("Could not set time limit to 0", "Error");
}

$confDir = 'config';
$logDir = 'logs';


$settings = parse_ini_file('./'.$confDir.'/settings.ini', true, INI_SCANNER_TYPED);
$assets = json_decode(file_get_contents('./'.$confDir.'/assets.json'), true);

$channels = $assets['channels'];
$modules = $assets['modules'];

$bot = new irc();

output("Creating connection to ".$settings['connection']['server']." on port ".$settings['connection']['port'], "info");
$bot->connect($settings['connection']['server'], $settings['connection']['port']);
output("Connected!", "info");

output("Sending Registration information", 'info');
$bot->register($settings['name']['nick'], $settings['name']['user'], $settings['name']['real']);
output("Registered!", "info");

$running = true;
while ($running)
{
	$raw = $bot->recv();
	$ex = array_pad(explode(' ', $raw), 10, ' ');
	$ex[3] = substr($ex[3], 1);
	$channel = strtolower($ex[2]);
	$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

	output("< $raw");

	if ($settings['debug']['logging'] == '1') {
		if (!is_dir('./'.$logDir)) {
			mkdir('./'.$logDir.'/');
		}
		if (!is_dir('./'.$logDir.'/'.date('Y'))) {
			mkdir('./'.$logDir.'/'.date('Y').'/');
		}
		if (!is_dir('./'.$logDir.'/'.date('Y').'/'.date('m'))) {
			mkdir('./'.$logDir.'/'.date('Y').'/'.date('m'));
		}
		file_put_contents('./'.$logDir.'/'.date('Y').'/'.date('m').'/'.date('d'), $raw."\n", FILE_APPEND);
	}

	if ($ex[0] == "ERROR") {
		die();
		die();
	} elseif ($ex[1] === '376' || $ex[1] === '422') {
		if ($settings['auth']['toggle']) {
			foreach ($settings['auth']['commands'] as $key => $command) {
				$bot->send($command);
				sleep(1);
			}
		}
		foreach ($channels as $chan => $dat) {
			$bot->joinChannel($chan, $dat['key']);
		}
	}

	// Admin commands
	if ($ex[0] == ':iCherry!iCherry@iCherry.users.quakenet.org') {
		if ($ex[3] == $settings['plugin']['OPprefix']) {
			if ($ex[4] == 'die') {
				$bot->die();
				die();
			}
			if ($ex[4] == 'reload') {
				$settings = parse_ini_file('./'.$confDir.'/settings.ini', true, INI_SCANNER_TYPED);

				$assets = json_decode(file_get_contents('./'.$confDir.'/assets.json'), true);
				$channels = $assets['channels'];
				$modules = $assets['modules'];
				output("Reloaded settings!", "info");
				$bot->privmsg($channel, "Reloaded Settings");
			}
		}
	}

	// Modules
	foreach ($modules as $prog => $cmds) {
		foreach ($cmds as $script) {
			$run = $prog.' ./Modules/'.$script.' '.escapeshellarg($raw).' '.escapeshellarg(json_encode($settings));
			exec($run, $out);
			foreach ($out as $key => $value) {
				$resp = json_decode($value, true);
				switch ($resp['method']) {
					case 'privmsg':
						$bot->privmsg($channel, $resp['data']);
						break;

					case 'send':
						$bot->send($resp['data']);
						break;

					case 'eval':
						eval($resp['data']);
						break;
					
					default:
						output('Script returned invalid method type'.'warning');
						break;
				}
			}
			unset($out);
		}
	}
}
