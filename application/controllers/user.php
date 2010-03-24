<?php

	class User_Controller extends Application_Controller {
	
		protected $auth_required = array(
			'islands' => '*'
		);
	
		function index ( $username = null ) {
		
			$this->template->view->username = $username;
			$this->template->title = Kohana::lang( 'user.view' );
		
			if( is_null( $username ) )
				$user = Auth::instance()->get_user();
			else
				$user = ORM::factory( 'user' )->where( 'username', $username )->find();
			
			if( false === $user || ! $user->loaded ) {
				$this->template->view = new View( 'user/missing' );
				$this->template->view->username = $username;
				return;
			}
		}
	
		function logout () {
			Auth::instance()->logout();
			$this->session->set_flash( 'notice', Kohana::lang( 'user.logged_out' ) );
			url::redirect( "/user/login" );
		} // User_Controller::logout
		
		function login () {
			if( Auth::instance()->logged_in() )
				url::redirect( "/user" );

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
		
		function create () {
			$this->template->title = Kohana::lang( 'user.sign_up' );
			
			$this->template->view->errors = array();
			$this->template->view->username = '';
			$this->template->view->email = '';
			
			if( $post = $this->input->post() ) {
				
				$this->template->view->email = $post['email'];
				$this->template->view->username = $post['username'];
				
				$form =  new Validation( $post );
				$form->add_rules( 'email', 'required', 'valid::email' );
				$form->add_rules( 'username', 'required' );
				$form->add_rules( 'password', 'required' );
				
				if( $form->validate() ) {
					$user = ORM::factory( 'user' );
					$user->email = $post['email'];
					$user->username = $post['username'];
					$user->password = $post['password'];

					if( $user->save() ) {
						$prop = ORM::factory( 'user_property' );
						$prop->user_id = $user->id;
						$prop->key = 'confirm';
						$prop->value = sha1( $user->id . time() . Kohana::config( 'qaargh.confirm_salt' ) );
						$prop->save();
						//! \todo Send confirmation email!
						$this->session->set_flash( 'notice', 'User Created' );
						url::redirect( "/user/login" );
					}
				}
				else {
					$this->template->view->errors = $form->errors( 'form_errors' );
				}
			}
			
		} // User_Controller::create
		
		function islands () {
			$this->template->view->islands = ORM::factory( 'island' )->where( 'user_id', Auth::instance()->get_user()->id )->find_all();
		}
		
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