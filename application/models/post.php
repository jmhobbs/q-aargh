<?php

	class Post_Model extends ORM {
		protected $belongs_to = array( 'user' );
	
		public function get_post () {
			return ORM::factory( $this->type . '_post', $this->post_id )->find();
		}
	}