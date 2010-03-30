<?php

	class Twitter_Controller extends Application_Controller {
	
		public function __construct () {
			parent::__construct();
			require_once( Kohana::find_file( 'vendor', 'twitteroauth/twitteroauth' ) );
		}
	
		public function index () {
			if( Auth::instance()->logged_in() )
				url::redirect( '/' );

				$connection = new TwitterOAuth(
					Kohana::config( 'twitter.consumer_key' ),
					Kohana::config( 'twitter.consumer_secret' )
				);
				
				$request_token = $connection->getRequestToken();

				/* Save temporary credentials to session. */
				$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
				$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
				
				/* If last connection failed don't display authorization link. */
				switch ($connection->http_code) {
					case 200:
						$url = $connection->getAuthorizeURL($token);
						url::redirect( $url ); 
						break;
					default:
						$this->template->view = 'Could not connect to Twitter. Refresh the page or try again later.';
				}

		}
	
		public function complete () {
			
			$connection = new TwitterOAuth(
				Kohana::config( 'twitter.consumer_key' ),
				Kohana::config( 'twitter.consumer_secret' ),
				$_SESSION['oauth_token'],
				$_SESSION['oauth_token_secret']
			);

			$access_token = $connection->getAccessToken();

			/* Save the access tokens. Normally these would be saved in a database for future use. */
			$_SESSION['access_token'] = $access_token;

			unset($_SESSION['oauth_token']);
			unset($_SESSION['oauth_token_secret']);

			if ( 200 == $connection->http_code ) {
				$_SESSION['twitter_user'] = $access_token['screen_name'];
				die( "Success: " . $_SESSION['twitter_user'] );
			} else {
				die( 'Failure' );
			}

		}
		
		public function account () {
			var_dump( $_SESSION );
			die();
		}
	
	}