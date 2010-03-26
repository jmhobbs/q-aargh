<?php
	session_start();
	require_once('twitteroauth/twitteroauth.php');
	require_once('config.php');
	
	if (
		empty( $_SESSION['access_token'] ) ||
		empty( $_SESSION['access_token']['oauth_token'] ) ||
		empty( $_SESSION['access_token']['oauth_token_secret'] )
	) {
		header('Location: ./clear.php');
	}

	$access_token = $_SESSION['access_token'];
	$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret'] );

	file_put_contents( '../application/tmp/TWITTER' . $access_token['oauth_token'], serialize( $_SESSION['access_token'] ) );

	header( 'Location: /user/twitter/' . $access_token['oauth_token'] );
	exit();