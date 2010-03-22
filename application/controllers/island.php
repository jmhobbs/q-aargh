<?php

	class Island_Controller extends Application_Controller {
	
		protected $auth_required = array(
			'edit' => '*',
			/*'create' => '*'*/
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
		}
	
	}