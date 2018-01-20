<?php
$raw = $argv[1];
$settings = json_decode($argv[2], true);

$ex = array_pad(explode(' ', $raw), 10, ' ');
$ex[3] = substr($ex[3], 1);
$channel = strtolower($ex[2]);
$user = substr($ex[0], strpos($ex[0], "@") + 1);
$nick = substr(substr($ex[0], 1), 0, strpos(substr($ex[0], 1), '!'));

for ($i=3; $i < count($ex); $i++) { 
	$p = '/(http[s]?:\/\/)(?:[a-zA-Z]|[0-9]|[$-_@.&+]|[!*\(\),]|(?:%[0-9a-fA-F][0-9a-fA-F]))+/'; // Check if it is a link
	if (preg_match($p, $ex[$i])) {
		$p = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';
		if (preg_match($p, $ex[$i])) {
			// Youtube link
			preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $ex[$i], $match);
			$videoID = $match[1];
			$Apikey = $settings['plugin']['googleApiKey'];

			$apiBase  = 'https://www.googleapis.com/youtube/v3/videos?id=';
			$apiBase .= $videoID;
			$apiBase .= '&part=';

			$apiA  = $apiBase.'snippet&key=';
			$apiA .= $Apikey;
			$apiA  = json_decode(file_get_contents($apiA), true);

			$apiB  = $apiBase.'contentDetails&key=';
			$apiB .= $Apikey;
			$apiB  = json_decode(file_get_contents($apiB), true);
			if ($apiA['pageInfo']['totalResults'] < 1) {
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
				$title  = "\002".html_entity_decode($apiA['items'][0]['snippet']['title'])."\002";
				$title .= " - \002".secondsToDur(ISO8601ToSeconds($apiB['items'][0]['contentDetails']['duration']))."\002";
				$title .= " By \002".html_entity_decode($apiA['items'][0]['snippet']['channelTitle'])."\002";
				$title .= " on \002".date('jS F Y',strtotime($apiA['items'][0]['snippet']['publishedAt'])). "\002";
			}
			$ret['method'] = 'privmsg';
			$ret['data'] = $title;
			echo json_encode($ret);
			die();
		} else {

			$ch = curl_init($ex[$i]);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_NOBODY, true);

			$data = curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			if ($code = 200) {
				$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
				curl_close($ch);
				function formatBytes($bytes, $precision = 2) { 
				    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

				    $bytes = max($bytes, 0); 
				    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
				    $pow = min($pow, count($units) - 1); 

				    // Uncomment one of the following alternatives
				     $bytes /= pow(1024, $pow);
				    // $bytes /= (1 << (10 * $pow)); 

				    return round($bytes, $precision) . ' ' . $units[$pow]; 
				} 

				file_put_contents('file', file_get_contents($ex[$i], null, null, null, 100000));
				if (exif_imagetype('file') === FALSE) {
					if (mime_content_type('file') == 'text/html') {
					    $ch = curl_init();
					    curl_setopt($ch, CURLOPT_HEADER, 0);
					    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					    curl_setopt($ch, CURLOPT_URL, $ex[$i]);
					    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
						$data = curl_exec($ch);
						curl_close($ch);

						$doc = new DOMDocument();
						@$doc->loadHTML($data);
						$nodes = $doc->getElementsByTagName('title');

						$title = $nodes->item(0)->nodeValue;
						$title = html_entity_decode($title);
						$title = preg_replace('/\s+/', ' ', $title);
						$title = trim($title);

						$title = "\002".$title."\002";
						$ret['method'] = 'privmsg';
						$ret['data'] = $title;
						echo json_encode($ret);
						unlink('file');
						die();
					} else {
						$title = "Type: \002".mime_content_type('file')."\002";
						$ret['method'] = 'privmsg';
						$ret['data'] = $title;
						echo json_encode($ret);
						unlink('file');
						die();
					}
				} else {
					$info = getimagesize('file');

					$type = image_type_to_mime_type(exif_imagetype('file'));
					$width = $info[0];
					$height = $info[1];

					$title = "Type: \002".$type."\002 - \002".$width."\002x\002".$height."\002 (".formatBytes($size).")";
					$ret['method'] = 'privmsg';
					$ret['data'] = $title;
					echo json_encode($ret);
					die();
				}
			} else {
				curl_close($ch);
				$title = "An Error Occured";
				$ret['method'] = 'privmsg';
				$ret['data'] = $title;
				echo json_encode($ret);
				die();
			}
		}
		

	}
}
?>