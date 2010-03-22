<?php

	class Island_Controller extends Application_Controller {
	
		protected $auth_required = array(
			'edit' => '*',
			'create' => '*'
		);
	
		public function index ( $code ) {
			$this->template->view->island = ORM::factory( 'island' )->find_by_code( $code );
			if( $this->template->view->island === false ) {
				$this->template->title = Kohana::lang( 'island.missing' );
				$this->template->view = new View( 'island/missing' );
				return;
			}
			$this->template->title = $this->template->view->island->title;
		} // Island_Controller::index
	
		public function edit ( $code ) {}
		
		public function create () {}
	
	}