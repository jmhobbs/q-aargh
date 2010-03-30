<?php

	class Page_Controller extends Application_Controller {
	
		public function index ( $stub = null ) {
			if( is_null( $stub ) )
				$stub = Kohana::config( 'qaargh.homepage' );
			
			$stub = str_replace( '.', '', strtolower( $stub ) );
			
			try {
				$this->template->view = new View( 'page/' . $stub );
				$this->template->title = Kohana::lang( 'pages.' . $stub ); 
			}
			catch ( Exception $e ) {
				$this->template->title = Kohana::lang( 'error.404' );
				$this->template->view = new View( 'error/404');
				return;
			}
			
		}
	
	}