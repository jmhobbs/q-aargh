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
			
			if( 200 == $connection->http_code) {
					$url = $connection->getAuthorizeURL( $token );
					url::redirect( $url ); 
			}
			else {
				$this->session->set_flash( 'error', 'Server error using your Twitter credentials. Please try again.' );
				url::redirect( '/user/login' );
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

			unset( $_SESSION['oauth_token'] );
			unset( $_SESSION['oauth_token_secret'] );

			if ( 200 == $connection->http_code ) {
				$twitter_user = ORM::factory( 'twitter_user', $access_token['screen_name'] );
				if( $twitter_user->loaded ) {
					$user = ORM::factory( 'user', $twitter_user->user_id );
					Auth::instance()->force_login( $user->username );
					$this->session->set_flash( 'notice', 'Logged you in via Twitter!' );
					url::redirect( '/user' );
				}
				else {
					if( Auth::instance()->logged_in() ) {
						$twitter_user->username = $access_token['screen_name'];
						$twitter_user->user_id = Auth::instance()->get_user()->id;
						$twitter_user->save();
						$this->session->set_flash( 'notice', 'Linked your Twitter account!' );
						url::redirect( '/user' );
					}
					else {
						$user = ORM::factory( 'user' )->where( 'username', $access_token['screen_name'] )->find();
						if( ! $user->loaded ) {
							$user->email = '';
							$user->username = $access_token['screen_name'];
							$user->password = sha1( $access_token['screen_name'] . time() . Kohana::config( 'qaargh.confirm_salt' ) );
							$user->add( ORM::factory( 'role', 'login' ) );
							$user->add( ORM::factory( 'role', 'twitter' ) );
							$user->save();
							
							$twitter_user->username = $access_token['screen_name'];
							$twitter_user->user_id = $user->id;
							$twitter_user->save();
							
							Auth::instance()->force_login( $user->username );
							$this->session->set_flash( 'notice', 'Created your account via Twitter!' );
							url::redirect( '/user' );
						}
						else {
							$_SESSION['twitter_user'] = $access_token['screen_name'];
							url::redirect( '/twitter/account' );
						}
					}
				}
			}
			else {
				$this->session->set_flash( 'error', 'Server error using your Twitter credentials. Please try again.' );
				url::redirect( '/user/login' );
			}

		} // Twitter_Controller::complete

		public function account () {
			$this->template->view->username = $_SESSION['twitter_user'];
			if( $post = $this->input->post() ) {
				$user = ORM::factory( 'user' )->where( 'username', $post['username'] )->find();
				if( ! $user->loaded ) {
					$user->email = '';
					$user->username = $post['username'];
					$user->password = sha1( $post['username']. time() . Kohana::config( 'qaargh.confirm_salt' ) );
					$user->add( ORM::factory( 'role', 'login' ) );
					$user->add( ORM::factory( 'role', 'twitter' ) );
					$user->save();
					
					$twitter_user = ORM::factory( 'twitter_user', $_SESSION['twitter_user'] );
					$twitter_user->username = $_SESSION['twitter_user'];
					$twitter_user->user_id = $user->id;
					$twitter_user->save();
					
					Auth::instance()->force_login( $user->username );
					$this->session->set_flash( 'notice', 'Created your account via Twitter!' );
					url::redirect( '/user' );
					unset( $_SESSION['twitter_user'] );
				}
				else {
					$this->session_set_flash( 'error', 'Sorry, "' . html::specialchars( $post['username'] ) . '" is taken.' );
				}
			}
		}

	}