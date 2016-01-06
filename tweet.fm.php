<?php

/**
 * http://www.last.fm/api/show/user.getRecentTracks
 * 
 */

require_once('vendor/autoload.php');

require_once('config.php');

$apiURL = sprintf("%s?method=%s&user=%s&api_key=%s&format=%s", LAST_FM_API_ROOT_URL, LAST_FM_API_METHOD, LAST_FM_USER_NAME, LAST_FM_API_KEY, LAST_FM_API_FORMAT);

$apiResultRaw = file_get_contents($apiURL);

$apiResult = json_decode($apiResultRaw);

$tracks = $apiResult->recenttracks->track;

// foreach($tracks as $i => $track)
//$track = $tracks[0];

while ($track = array_shift($tracks))
{
	$artist = $track->artist->{"#text"};
	$name = $track->name;
	$mbid = $track->mbid;
	$album = $track->album->{"#text"};
	$uts = $track->date->uts;
	$date = $track->date->{"#text"};
	$nowplaying = $track->{"@attr"}->nowplaying;
	
	if ($nowplaying) continue;
	
	break;
}

$log = sprintf("%d,%s\n", $uts, $mbid);

file_put_contents(LOG_FILE_PATH, $log, FILE_APPEND);

$tweet = sprintf("♪ %s - %s", $name, $artist);

// Twitter

$config = array(
		'access_token' => array(
				'token'  => TWITTER_ACCESS_TOKEN,
				'secret' => TWITTER_ACCESS_TOKEN_SECRET,
		),
		'oauth_options' => array(
				'consumerKey' => TWITTER_API_KEY,
				'consumerSecret' => TWITTER_API_SECRET,
		),
		'http_client_options' => array(
				'adapter' => 'Zend\Http\Client\Adapter\Curl',
				'curloptions' => array(
						CURLOPT_SSL_VERIFYHOST => false,
						CURLOPT_SSL_VERIFYPEER => false,
				),
		),
);

$twitter = new ZendService\Twitter\Twitter($config);

$res = $twitter->statuses->update($tweet);

//var_dump($res);