<?php

	class Page_Controller extends Application_Controller {
	
		public function index ( $stub = null ) {
			if( is_null( $stub ) )
				Kohana::config( 'qaargh.homepage' );
			
			$page = ORM::factory( 'page' )->where( 'stub', $stub )->find();

			if( ! $page->loaded ) {
				$this->template->title = Kohana::lang( 'error.404' );
				$this->template->view = new View( 'error/404');
				return;
			}
			
			require Kohana::find_file( 'vendor','Markdown' );
			
			$this->template->title = $page->title;
			$this->template->view->content = Markdown( $page->content );
			
		}
	
	}