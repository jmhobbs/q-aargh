<?php

	class Island_Controller extends Application_Controller {
	
		protected $auth_required = array(
			'edit' => '*',
			'create' => '*',
			'post' => '*'
		);
	
		public function index ( $code = null ) {
			$this->template->view->island = ORM::factory( 'island' )->find_by_code( $code );
			if( $this->template->view->island === false ) {
				$this->template->title = Kohana::lang( 'island.missing' );
				$this->template->view = new View( 'island/missing' );
				return;
			}
			$this->template->title = $this->template->view->island->title;
			$this->template->robots = '<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />';
			
			// Increment view counter
			if( ! Auth::instance()->logged_in() or Auth::instance()->get_user()->id != $this->template->view->island->user_id ) {
				$this->template->view->island->views++;
				$this->template->view->island->save();
			}
			
			// Increment first visit counter
			if( Auth::instance()->logged_in() ) {
				$id = Auth::instance()->get_user()->id;
				if( $this->template->view->island->user_id != $id ) {
					$visit = ORM::factory( 'visit' )->where( 'user_id', $id )->find();
					if( ! $visit->loaded ) {
						$visit->user_id = $id;
						$visit->island_code = $this->template->view->island->code;
						$visit->visited = date( 'Y-m-d H:i:s' );
						$visit->save();
					}
				}
			}
			
		} // Island_Controller::index
	
		public function post ( $code = null ) {
			$island = ORM::factory( 'island' )->find_by_code( $code );
			if( $island === false ) {
				$this->template->title = Kohana::lang( 'island.missing' );
				$this->template->view = new View( 'island/missing' );
				return;
			}
			
			if( empty( $_REQUEST['comment'] ) ) {
				$this->session->set_flash( 'error', 'No comment!' );
				url::redirect( '/island/index/' . $code );
			}
			
			$text_post = ORM::factory( 'text_post' );
			$text_post->content = $_REQUEST['comment'];
			$text_post->save();
			
			$post = ORM::factory( 'post' );
			$post->island_code = $code;
			$post->user_id = Auth::instance()->get_user()->id;
			$post->posted = date( 'Y-m-d H:i:s' );
			$post->post_id = $text_post->id;
			$post->type = 'text';
			$post->save();
			
			$this->session->set_flash( 'notice', 'Added your comment.' );
			url::redirect( '/island/index/' . $code );
		}
	
		public function edit ( $code = null ) {}
		
		public function create () {
		
			if( $post = $this->input->post() ) {
			
				$form =  new Validation( $post );
				$form->add_rules( 'title', 'required' );
				$form->add_rules( 'introduction', 'required' );
			
				if( $form->validate() ) {
					$island = ORM::factory( 'island' );
					$island->user_id = Auth::instance()->get_user()->id;
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
		
		} // Island_Controller::qr
	
	}