<?php

/*
 *    Twitter Oauth Config
 *        you are going to have to edit details into here
 *        for the library to work right!
 *
 */

// To get consumer key/secret you need to visit http://www.twitter.com/oauth_clients and create an app
// Consumer key from twitter
$config['consumer_key'] = 'MYKEY';
// Consumer Secret from twitter
$config['consumer_secret'] = 'MYSECRETKEY';

// database table in which to store the user keys
$config['database_table'] = 'twitter_users';

// whether to use native Curl OR the Kohana Curl module
$config['use_kcurl_library'] = False;

// Sets whether to use a cookie to persist logins
$config['use_cookie'] = True;

// storage cookie details
$config['cookie'] = array(
               'name'   => 'KTwitter_Cookie',
               'expire' => '86500',
               'domain' => '.example.com',
               'path'   => '/');