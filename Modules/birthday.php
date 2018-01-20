<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = array_pad(explode(' ', $raw), 10, ' ');
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);

if ($ex[3] == $settings['plugin']['prefix'].'bd' || $ex[3] == $settings['plugin']['prefix'].'birthday') {
	$allowed[] = ':iCherry!iCherry@iCherry.users.quakenet.org';
	$allowed[] = ':Underdose!~Dosed@Underdose.users.quakenet.org';
	if ($ex[4] == 'add') {
		if (in_array($ex[0], $allowed)) {
			$nick = strtolower($ex[5]);
			$day = $ex[6];
			$month = $ex[7];
			if (in_array($month, array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11'))) {
				if (preg_match('(((01|03|05|07|08|10|12)\.(0[1-9]|[1-2][0-9]|3[0-1]))|(02\.(0[1-9]|[1-2][0-9]))|((04|06|09|11)\.(0[1-9]|[1-2][0-9]|30)))', $month.'.'.$day)) {
					$bdays = json_decode(file_get_contents('./Modules/config/birthday.json'), true);
					if (array_key_exists($nick, $bdays)) {
						$ret['method'] = 'privmsg';
						$ret['data'] = 'That nickname already has a birthday associated with it!';
						echo json_encode($ret);
						die();
					} else {
						$bdays[$nick] = $month.'.'.$day;
						file_put_contents('./Modules/config/birthday.json', json_encode($bdays));
						$ret['method'] = 'privmsg';
						$ret['data'] = 'Added '.$nick.' to birthday database!';
						echo json_encode($ret);
						die();
					}
				} else {
					$ret['method'] = 'privmsg';
					$ret['data'] = 'Invalid month/day combination: '.$month.'.'.$day;
					echo json_encode($ret);
					die();
				}
			} else {
				$ret['method'] = 'privmsg';
				$ret['data'] = 'Invalid month';
				echo json_encode($ret);
				die();
			}
		} else {
			$ret['method'] = 'privmsg';
			$ret['data'] = 'You are not allowed to add birthdays';
			echo json_encode($ret);
			die();
		}
	} elseif ($ex[4] == 'lookup' || $ex[4] == 'search') {
		$bdays = json_decode(file_get_contents('./Modules/config/birthday.json'), true);
		if (array_key_exists(strtolower($ex[5]), $bdays)) {
			$bday = explode('.', $bdays[strtolower($ex[5])]);
			$di = $bday[1];
			$mi = $bday[0];
			$dateObj = DateTime::createFromFormat('!m', $mi);
			$month = $dateObj->format('F');
			if (date('m') < $mi) {
				// it is coming up
				$yi = date('Y');
			} else {
				// already had
				$yi = date('Y');
				$yi = $yi+1;
			}
			$now = new DateTime('now');
			$then = new DateTime($yi.'-'.$mi.'-'.$di);
			$diff = $now->diff($then);
			$diff = $diff->format('%a days');

			$ret['method'] = 'privmsg';
			$ret['data'] = 'Birthday on '.$bday[1].' '.$month.', in '.$diff;
			echo json_encode($ret);
			die();
		} else {
			$ret['method'] = 'privmsg';
			$ret['data'] = 'Unable to find '.$ex[5].' in database';
			echo json_encode($ret);
			die();
		}
	} elseif ($ex[4] == 'del' || $ex[4] == 'delete') {
		if (in_array($ex[0], $allowed)) {
			$bdays = json_decode(file_get_contents('./Modules/config/birthday.json'), true);
			if (array_key_exists(strtolower($ex[5]), $bdays)) {
				unset($bdays[strtolower($ex[5])]);
				file_put_contents('./Modules/config/birthday.json', json_encode($bdays));
				$ret['method'] = 'privmsg';
				$ret['data'] = 'Deleted '.$nick.' from birthday database!';
				echo json_encode($ret);
				die();
			}
		} else {
			$ret['method'] = 'privmsg';
			$ret['data'] = 'You are not allowed to delete birthdays';
			echo json_encode($ret);
			die();
		}
	} else {
		$ret['method'] = 'privmsg';
		$ret['data'] = 'Unrecognised command: '.$ex[4];
		echo json_encode($ret);
		die();
	}
}

if ($ex[3] == $settings['plugin']['prefix'].'help') {
	if ($ex[4] == 'birthday') {
		$ret['method'] = 'privmsg';

		$ret['data'] = 'Set/Find someones birthday';
		echo json_encode($ret).PHP_EOL;
		$ret['data'] = '!birthday (add nickname dd mm)|(del nick)|(search nick)';
		echo json_encode($ret).PHP_EOL;
		$ret['data'] = 'Example: !birthday search iCherry';
		echo json_encode($ret);
		die();
	}
}
?>