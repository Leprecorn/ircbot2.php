<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = array_pad(explode(' ', $raw), 10, ' ');
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

if ($ex[3] == $settings['plugin']['prefix'].'emote' || $ex[3] == $settings['plugin']['prefix'].'emoteC') {
	$dictionary = json_decode(file_get_contents('./Modules/config/emotes.json'), true);
	if ($ex[3] == $settings['plugin']['prefix'].'emoteC') {
		if ($ex[4] == 'list') {
			$list = '';
			foreach ($dictionary as $name => $value) {
				$list .= $name.' ';
			}
			$ret['method'] = 'privmsg';
			$ret['data'] = $list;
			echo json_encode($ret);
			die();
		} else {
			$allowed[] = ':iCherry!iCherry@iCherry.users.quakenet.org';
			if (in_array($ex[0], $allowed)) {
				if ($ex[4] == 'add') {
					$toAdd = $ex[6];
					$toAdd = urlencode($toAdd);
					$dictionary[$ex[5]] = $toAdd;
					file_put_contents('./Modules/config/emotes.json', json_encode($dictionary));
					$ret['method'] = 'privmsg';
					$ret['data'] = 'Done! added '.$ex[5].' '.$dictionary[$ex[5]];
					echo json_encode($ret);
					die();
				} elseif ($ex[4] == 'delete') {
					if (array_key_exists($ex[5], $dictionary)) {
						unset($dictionary[$ex[5]]);
						file_put_contents('./Modules/config/emotes.json', json_encode($dictionary));
						$ret['method'] = 'privmsg';
						$ret['data'] = 'Done! deleted '.$ex[5];
						echo json_encode($ret);
						die();
					}
				}
			}
		}
	}
	// https://www.url-encode-decode.com
	if (array_key_exists($ex[4], $dictionary)) {
		$ret['method'] = 'privmsg';
		$ret['data'] = urldecode($dictionary[$ex[4]]);
		echo json_encode($ret);
		die();
	} elseif ($ex[4] == 'raw') {
		if (array_key_exists($ex[5], $dictionary)) {
			$ret['method'] = 'privmsg';
			$ret['data'] = $dictionary[$ex[5]];
			echo json_encode($ret);
			die();
		}
	} else {
		$ret['method'] = 'privmsg';
		$ret['data'] = 'Unable to find that symbol';
		echo json_encode($ret);
		die();
	}
}