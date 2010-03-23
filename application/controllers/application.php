<?php

	class Application_Controller extends Template_Controller {

		public $template = 'layout';

		protected $auth_required = array();

		function __construct () {
			// Check and see if this is being run from the command line
			define( 'IS_CLI', ( 'cli' == PHP_SAPI ) );

			parent::__construct();

			$this->template->title = ucwords( router::$method );
			$this->session = Session::instance();

			try {
				$this->template->view = new View( strtolower( router::$controller . '/' . router::$method ) );
			}
			catch( Exception $e ) {
				$this->template->view = new View( 'error/missing_view' );
			}

			if( Auth::instance()->logged_in() )
				$this->template->menu = new View( 'menu/logged_in' );
			else
				$this->template->menu = new View( 'menu/logged_out' );

			// Handle built-in authorization
			if( array_key_exists( router::$method, $this->auth_required ) ) {

				// If it's in the array, you must at least be logged in.
				if( ! Auth::instance()->logged_in() ) {
					$this->session->set_flash( 'error', Kohana::lang( 'general.login_required' ) );
					url::redirect( "/user/login" );
				}
				
				$authorized = false;
				
				// If it's *, then being logged in is enough
				if( '*' == $this->auth_required[router::$method] )
					$authorized = true;

				// If it's an array, you must have one of the rights
				else if( is_array( $this->auth_required[router::$method] ) ) {
					foreach( $this->auth_required[router::$method] as $right ) {
						if( Auth::instance()->logged_in( $right ) ) {
							$authorized = false;
							break;
						}
					}
				}

				// Otherwise it's a single string right you must have
				else if( Auth::instance()->logged_in( $this->auth_required[router::$method] ) )
					$authorized = false;
				
				if( ! $authorized ) {
					$this->session->set_flash( 'error', Kohana::lang( 'general.insufficient_privileges' ) );
					url::redirect( "/user" );
				}
				
			}

		}

		public function __call( $method, $arguments ) {
			$this->template->title = Kohana::lang( 'error.404' );
			$this->template->view = new View( 'error/404');
		}
	}