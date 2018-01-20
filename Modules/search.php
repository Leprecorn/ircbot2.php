<?php

$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = explode(' ', $raw);
$ex = array_pad($ex, sizeof($ex)+4, null);
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

/*
TODO: google search // NO API
TODO: image search // NO API
*/

// https://www.googleapis.com/youtube/v3/search?key=AIzaSyAK7BFFSZQInrMkS8z1RpyQPOybE_dSUdA&part=snippet&maxResults=1&safeSearch=moderate&type=video&q=pickle%20party

// Youtube Search
if ($ex[3] == $settings['plugin']['prefix'].'yts' || $ex[3] == $settings['plugin']['prefix'].'youtubesearch' || $ex[3] == $settings['plugin']['prefix'].'yt') {
	$search = '';
	for ($i = 4; $ex[$i] != null; $i++) {
		$search .= ' '.$ex[$i];
	}

	if ($search != '') {
		$q = trim($search);

		$apiKey = $settings['plugin']['googleApiKey'];

		$params = array(
			"key" => $apiKey,
			"part" => 'snippet',
			"maxResults" => '1',
			"safeSearch" => 'moderate',
			"type" => 'video',
			"q" => $q
		);
		$api = 'https://www.googleapis.com/youtube/v3/search?'.http_build_query($params, null, '&', PHP_QUERY_RFC3986);

		$snippet = json_decode(file_get_contents($api), true);
		$id = $snippet["items"][0]['id']['videoId'];

		$params['id'] = $id;
		$params['part'] = 'snippet';
		unset($params['maxResults']);
		unset($params['safeSearch']);
		unset($params['type']);
		unset($params['q']);
		$snippet = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videos?'.http_build_query($params, null,'&',PHP_QUERY_RFC3986)), true);
		$params['part'] = 'contentDetails';
		$content = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/videos?'.http_build_query($params, null,'&',PHP_QUERY_RFC3986)), true);

		if ($snippet['pageInfo']['totalResults'] < 1) {
			$title = 'An Error Occured';
		} else {
			function ISO8601ToSeconds($ISO8601) {
			    $interval = new \DateInterval($ISO8601);

			    return ($interval->d * 24 * 60 * 60) +
			        ($interval->h * 60 * 60) +
			        ($interval->i * 60) +
			        $interval->s;
			}

			function secondsToDur($s) {
				$ret = '';
				if ($s >= 86400) {
					$n = $s / 86400;
					$d = floor($n);
					$t['d'] = round($d);
					$s = ($n = $d)*86400;
					$ret .= $t['d'].' Days ';
				}
				if ($s >= 3600) {
					$n = $s / 3600;
					$h = floor($n);
					$t['h'] = round($h);
					$s = ($n - $h)*3600;
					$ret .= $t['h'].' Hours ';
				}
				if ($s >= 60) {
					$n = $s / 60;
					$m = floor($n);
					$t['m'] = round($m);
					$s = ($n - $m)*60;
					$ret .= $t['m'].' Minutes ';
				}
				$t['s'] = round($s);
				$ret .= $t['s'].' Seconds';
				return $ret;
			}
			$title  = "\002".html_entity_decode($snippet['items'][0]['snippet']['title'])."\002";
			$title .= " - \002".secondsToDur(ISO8601ToSeconds($content['items'][0]['contentDetails']['duration']))."\002";
			$title .= " By \002".html_entity_decode($snippet['items'][0]['snippet']['channelTitle'])."\002";
			$title .= " on \002".date('jS F Y',strtotime($snippet['items'][0]['snippet']['publishedAt'])). "\002";
			$title .= " | https://youtu.be/".$id;
		}
		$ret['method'] = 'privmsg';
		$ret['data'] = $title;
		echo json_encode($ret);
		die();
	} else {
		die();
	}
}


if ($ex[3] == $settings['plugin']['prefix'].'help') {
	if ($ex[4] == 'youtubesearch') {
		//
	}
}


?>