<?php

	class Application_Controller extends Template_Controller {

		public $template = 'layout';
		//public $auto_render = false;

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
				$this->template->view = new View( 'errors/missing_view' );
			}

		}

		public function __call( $method, $arguments ) {
			$this->template->title = "404";
			$this->template->view = new View( 'errors/404');
		}
	}