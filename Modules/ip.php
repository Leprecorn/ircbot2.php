<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = explode(' ', $raw);
$ex = array_pad($ex, sizeof($ex)+4, null);
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

//https://freegeoip.net/json/86.13.145.170

if ($ex[3] == $settings['plugin']['prefix'].'ip' || $ex[3] == $settings['plugin']['prefix'].'iplocate') {
	if ($ex[4] == 'host') {
		$ex[4] = gethostbyname($ex[5]);
	}
	$r = '/(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])/';
	if (preg_match($r, $ex[4], $matches)) {
		function ip_is_private($ip) {
			$pri_addrs = array(
				'10.0.0.0|10.255.255.255',
				'127.16.0.0|172.31.255.255',
				'192.168.0.0|192.168.255.255',
				'169.254.0.0|169.254.255.255',
				'127.0.0.0|127.255.255.255'
			);

			$longip = ip2long($ip);
			if ($longip != -1) {
				foreach ($pri_addrs as $pri_addr) {
					list($start, $end) = explode('|', $pri_addr);
					if ($longip >= ip2long($start) && $longip <= ip2long($start)) {
						return true;
					}
				}
			}
			return false;
		}
		$ip = $matches[0];
		if (ip_is_private($ip)) {
			$ret['method'] = 'privmsg';
			$ret['data'] = 'Cannot geo-locate private addresses';
			echo json_encode($ret);
			die();
		} else {
			$api = 'https://freegeoip.net/json/'.$ip;
			$res = json_decode(file_get_contents($api), true);

			$ret['method'] = 'privmsg';
			$ret['data'] = "\002".$res['ip']."\002 - ".$res['country_name'].', '.$res['region_name'].', '.$res['city'];
			echo json_encode($ret);
			die();
		}
	} else {
		$ret['method'] = 'privmsg';
		$ret['data'] = 'That is not a valid ip/hostname';
		echo json_encode($ret).PHP_EOL;
		$ret['data'] = $ex[4];
		echo json_encode($ret);
	}

}