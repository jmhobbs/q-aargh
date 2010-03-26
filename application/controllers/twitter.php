<?php

	class Twitter_Controller extends Application_Controller {
	
		public function __construct () {
			parent::__construct();
			$this->twitter = new Twitter;
			$this->twitter->check_login();
		}
	
		public function index () {
			if( Auth::instance()->logged_in() )
				url::redirect( '/' );

			if( $this->twitter->check_login() )
				url::redirect( '/twitter/account' );

			$this->twitter->getRequestTokens();
			$url = $this->twitter->getAuthorizeUrl();
			
			$this->template->view = new View( 'redirect', array( 'destination' => 'Twitter', 'url' => $url ) );
		}
	
		public function complete () {
			if( $this->twitter->check_login() == False ) {
				$this->twitter->sessionRequestTokens();
				$this->twitter->tradeRequestForAccess();
				if( ! $this->twitter->storeTokens() )
					die( 'Twitter Authentication Error' );
			}
			
			if( ! empty( $this->twitter->user->user_id ) ) {
				$user = ORM::factory( 'user', $this->twitter->user->user_id );
				Auth::instance()->force_login( $user->username );
				$this->session->set_flash( 'notice', 'Logged In Via Twitter' );
				url::redirect( '/' );
			}
			else {
				url::redirect( '/twitter/account' );
			}
		}
		
		public function account () {
			if( $this->twitter->check_login() == False ) { die( 'No auth :-(' ); }

			$this->template->view->iusername = $this->twitter->user->username;
			$this->template->view->username = $this->twitter->user->username;

		}
	
	}