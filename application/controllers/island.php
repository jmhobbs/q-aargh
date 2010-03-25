<?php

	class Island_Controller extends Application_Controller {
	
		protected $auth_required = array(
			'edit' => '*',
			'create' => '*'
		);
	
		public function index ( $code = null ) {
			$this->template->view->island = ORM::factory( 'island' )->find_by_code( $code );
			if( $this->template->view->island === false ) {
				$this->template->title = Kohana::lang( 'island.missing' );
				$this->template->view = new View( 'island/missing' );
				return;
			}
			$this->template->title = $this->template->view->island->title;
		} // Island_Controller::index
	
		public function edit ( $code = null ) {}
		
		public function create () {
		
			if( $post = $this->input->post() ) {
			
				$form =  new Validation( $post );
				$form->add_rules( 'title', 'required' );
				$form->add_rules( 'introduction', 'required' );
			
				if( $form->validate() ) {
					$island = ORM::factory( 'island' );
					$island->user_id = 1;//Auth::instance()->get_user()->id;
					$island->title = $post['title'];
					$island->introduction = $post['introduction'];
					$now = date( 'Y-m-d H:i:s' );
					$island->created = $now;
					$island->modified = $now;
					$island->save();
					
					if( $island->saved ) {
						$this->session->set_flash( 'notice', 'Created new island!' );
						url::redirect( '/sail/' . $island->code );
					}
					else {
						$this->session->set_flash( 'error', 'Failed to create new island!' );
					}
				}
				else {
					var_dump( $form->errors() );
					die();
					$this->session->set_flash( 'error', 'Error validating.' );
				}

			}
		} // Island_Controller::create
		
		public function qr ( $code = null ) {
		
			$this->template->view->island = ORM::factory( 'island' )->find_by_code( $code );
			if( $this->template->view->island === false ) {
				$this->template->title = Kohana::lang( 'island.missing' );
				$this->template->view = new View( 'island/missing' );
				return;
			}
			$this->template->title = 'QR Code For: ' . $this->template->view->island->title;
			
			$this->template->view->size = 150;
			if( $post= $this->input->post() )
				$this->template->view->size = intval( $post['size'] );
		
			$this->template->view->qrimg = false;
			if( $this->template->view->size > 1024 )
				return $this->session->set_flash( 'error', 'Size can\'t be larger than 1024.' );
			if( $this->template->view->size < 50 )
				return $this->session->set_flash( 'error', 'Size can\'t be smaller than 50.' );
		
			$this->template->view->qrimg = qr::get( $this->template->view->island, $this->template->view->size );
		
		}
	
	}