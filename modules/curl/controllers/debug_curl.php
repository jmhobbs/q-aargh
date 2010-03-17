<?php
// Bleh
class Debug_curl_Controller extends Controller
{
  public function index()
  {
	// A query?
	$search_query = 'curl';

	// Initial options we *know* we want to set from the ghetgo (some can be added, some can be taken away)
	$options = array
	(
	CURLOPT_FAILONERROR      => True,
	CURLOPT_FOLLOWLOCATION   => True,
	CURLOPT_RETURNTRANSFER   => True,
	CURLOPT_TIMEOUT          => 25,
	CURLOPT_FRESH_CONNECT    => True,
	CURLOPT_FORBID_REUSE     => True,
 	CURLOPT_POST             => False,
	CURLOPT_URL              => "http://ixmat.us/curl.htm",
	CURLOPT_SSL_VERIFYPEER   => False,
	CURLOPT_SSL_VERIFYHOST   => False
	);

	$curl    = new Curl($options);

	// Execute the instantiated CURL session and get the result
	$result = $curl->execute();

	die($result);
  }
}
