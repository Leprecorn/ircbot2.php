<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = array_pad(explode(' ', $raw), 10, ' ');
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

if ($ex[0] != ':Esor!esor@Esor.users.quakenet.org') {
if ($ex[3] == $settings['plugin']['prefix'].'we' || $ex[3] == $settings['plugin']['prefix'].'weather') {

	$cityCode = $ex[4];
	$countryCode = $ex[5];

	if ($cityCode == null) {
		$ret['method'] = 'privmsg';
		$ret['data'] = 'No city specified';
		echo json_encode($ret);
		die();
	} else {
		if ($countryCode = null) {
			$countryCode = 'GB';
		}

		$Apikey = $settings['plugin']['googleApiKey'];
		$api  = 'https://maps.googleapis.com/maps/api/geocode/json?key='.$Apikey.'&region='.$countryCode.'&address='.$cityCode;

		$location = json_decode(file_get_contents($api), true)['results'][0]['geometry']['location'];
		unset($Apikey);
		unset($api);

		$Apikey = $settings['plugin']['wundergroundApiKey'];
		$api = 'http://api.wunderground.com/api/'.$Apikey.'/geolookup/q/'.$location['lat'].','.$location['lng'].'.json';
		$response = json_decode(file_get_contents($api), true)['location'];
		$countryCode = $response['country_iso3166'];
		$cityCode = str_replace(' ', '_', $response['city']);


		$api = 'http://api.wunderground.com/api/2bb895cea6cfb7d8/geolookup/conditions/q/'.$countryCode.'/'.$cityCode.'.json';
		$response = json_decode(file_get_contents($api), true)['current_observation'];
		$rep  = '';

		$rep .= $response['display_location']['full'];
		$rep .= ' - '.$response['weather'].', '.$response['temp_c'].'°C ('.$response['temp_f'].'°F)';
		$rep .= ', '.$response['relative_humidity'].' humidity';
		$rep .= ' - Wind: '.$response['wind_string'];

		$ret['method'] = 'privmsg';
		$ret['data'] = $rep;
		echo json_encode($ret);
		die();
	}
}
}
if ($ex[3] == $settings['plugin']['prefix'].'help') {
	if ($ex[4] == 'weather') {
		$ret['method'] = 'privmsg';

		$ret['data'] = 'Lookup the weather of a location';
		echo json_encode($ret).PHP_EOL;
		$ret['data'] = 'Example: !weather CityOfLondon';
		echo json_encode($ret);
		die();
	}
} 

?>