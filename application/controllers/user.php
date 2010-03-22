<?php

	class User_Controller extends Application_Controller {
	
		function index () {}
	
		function logout () {
			Auth::instance()->logout();
			$this->session->set_flash( 'notice', Kohana::lang( 'user.logged_out' ) );
			url::redirect( "/user/login" );
		} // User_Controller::logout
		
		function login () {
			if( Auth::instance()->logged_in() )
				url::redirect( "/user" );
			
			$this->template->nav->links = array( Kohana::lang( 'user.log_in' ) => '/user/login' );
			
			$this->template->view->username = '';
			
			if( $post = $this->input->post() ) {
				if( ORM::factory( 'user' )->login( $post ) ) {
					$this->session->set_flash( 'notice', Kohana::lang( 'user.logged_in' ) );
					url::redirect( "/user/index" );
				}
				else {
					$this->template->view->username = $post['username'];
					if( in_array( 'required', $post->errors() ) )
						$this->session->set_flash( 'error', Kohana::lang( 'user.credentials_required' ) );
					else
						$this->session->set_flash( 'error', Kohana::lang( 'user.bad_credentials' ) );
				}
			}
		} // User_Controller::login
		
// 		function admin_create ( $username, $password ) {
// 				$user = ORM::factory( 'user' );
// 				$user->username = $username;
// 				$user->password = $password;
// 				$user->add( ORM::factory( 'role', 'admin' ) );
// 				if( $user->add( ORM::factory( 'role', 'login' ) ) and $user->save() )
// 					$this->session->set_flash( 'notice', 'User "' . html::specialchars( $user->username ) . '" created.' );
// 				else
// 					$this->session->set_flash( 'error', 'User "' . html::specialchars( $user->username ) . '" not created.' );
// 				
// 				url::redirect( '/' );
// 		}
	}