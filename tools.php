<?php

/**
* Tools for Twitch channel management
*
* Obtain channel information and set channel information for a given approved channel of Twitch.tv
*
* LICENSE: This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
*
* @category   Tools
* @copyright  Copyright (c) 2016 Robert Thayer (http://www.gamergadgets.net
* @version    1.0
* @link       http://www.gamergadgets.net
*/

// Global Variables
$OAUTH = ''; // Requires an OAUTH token for the channel. Review documentation here to obtain: https://github.com/justintv/Twitch-API/blob/master/authentication.md
$channel = '';

/**
* Obtain details about a channel from Twitch.tv API.
* Does not take any parameters.
* Returns a $details array that contains:
* Channel Title
* Channel Game
* Number of followers
* Boolean of whether channel is online.
* Number of viewers.
**/
function getChannelDetails()
{
	global $channel;
    $details = [];
    // Make API Call
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.twitch.tv/kraken/channels/' . $channel,
        CURLOPT_HTTPHEADER => array('Accept: application/vnd.twitchtv.v3+json')
    ));
    $resp = curl_exec($ch);
    
    if(!curl_exec($ch)){
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    } else {
        $json = json_decode($resp, true);
    }
    curl_close($ch);
    
    // Title & Game and Follower count
    $details['title'] = $json['status'];
    $details['game'] = $json['game'];
    $details['followers'] = $json['followers'];
    
    // Online status
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.twitch.tv/kraken/streams/' . $channel,
        CURLOPT_HTTPHEADER => array('Accept: application/vnd.twitchtv.v3+json')
    ));
    $resp = curl_exec($ch);
    if(!curl_exec($ch)){
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    } else {
        $json = json_decode($resp, true);
    }
    curl_close($ch);
    
    if (!$json['stream']==null) {
        $details['online'] = true;
        $details['viewers'] = $json['stream']['viewers'];
    } else {
        $details['online'] = false;
        $details['viewers'] = 0;
    }
    
    return $details;
}

/**
* Obtain details about the followers of a channel from Twitch.tv API.
* Does not take any parameters.
* Returns an array that contains:
* Follower information
**/
function getFollowers()
{
    global $channel;
    // Get list of followers
    // Make API Call
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.twitch.tv/kraken/channels/' . $channel . '/follows',
        CURLOPT_HTTPHEADER => array('Accept: application/vnd.twitchtv.v3+json')
    ));
    $resp = curl_exec($ch);
    if(!curl_exec($ch)){
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    } else {
        $json = json_decode($resp, true);
    }
    curl_close($ch);
    
    return $json;
}

/**
* Obtain list of 10 most recent broadcasts from a Twitch.tv channel
* Does not take any parameters.
* Returns an array of video objects.
**/
function getVideos()
{
    global $channel;
    $videos= [];
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.twitch.tv/kraken/channels/' . $channel . '/videos?broadcasts=true',
        CURLOPT_HTTPHEADER => array('Accept: application/vnd.twitchtv.v3+json')
    ));
    $resp = curl_exec($ch);
    if(!curl_exec($ch)){
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    } else {
        $json = json_decode($resp, true);
    }
    curl_close($ch);
    
    return $json;
}

/**
* Obtain list of 10 most recent highlights from a Twitch.tv channel
* Does not take any parameters.
* Returns an array of video objects.
**/
function getHighlights()
{
    global $channel;
    $videos= [];
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.twitch.tv/kraken/channels/' . $channel . '/videos',
        CURLOPT_HTTPHEADER => array('Accept: application/vnd.twitchtv.v3+json')
    ));
    $resp = curl_exec($ch);
    if(!curl_exec($ch)){
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    } else {
        $json = json_decode($resp, true);
    }
    curl_close($ch);
    
    return $json;
}


/**
* Submits a new title and game name to the Twitch.tv API to update channel information
* Parameters: $title: The desired title. $game: The desired game name.
* Does not return.
**/
function setChannelTitle($title, $game)
{
    global $OAUTH;
    global $channel;    
    
    $postvars = ['channel'=>array(
        'status' => $title,
        'game' => $game
        )
     ];
    
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.twitch.tv/kraken/channels/' . $channel,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_HTTPHEADER => array('Accept: application/vnd.twitchtv.v3+json', 'Authorization: OAuth ' . $OAUTH),
        CURLOPT_POSTFIELDS => http_build_query($postvars)
    ));
    $resp = curl_exec($ch);
    if(!curl_exec($ch)){
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    } else {
        $json = json_decode($resp, true);
    }
    curl_close($ch);
}
?>