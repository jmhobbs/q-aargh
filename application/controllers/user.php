<?php

	class User_Controller extends Application_Controller {
	
		function index () {}
	
		function logout () {
			Auth::instance()->logout();
			$this->session->set_flash( 'notice', 'Logged Out' );
			url::redirect( "/user/login" );
		}
		
		function login () {
			if( Auth::instance()->logged_in() )
				url::redirect( "/user" );
			
			$this->template->nav->links = array( 'Login' => '/user/login' );
			
			$this->template->view->username = '';
			
			if( $post = $this->input->post() ) {
				if( ORM::factory( 'user' )->login( $post ) ) {
					$this->session->set_flash( 'notice', 'Logged In' );
					url::redirect( "/user/index" );
				}
				else {
					$this->template->view->username = $post['username'];
					if( in_array( 'required', $post->errors() ) )
						$this->session->set_flash( 'error', 'Username and password are required.' );
					else
						$this->session->set_flash( 'error', 'Bad username or password.' );
				}
			}
		} // User_Controller::login
	}