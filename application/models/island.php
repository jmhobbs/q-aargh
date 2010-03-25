<?php

	class Island_Model extends ORM {
	
		protected $belongs_to = array( 'user' );
		protected $has_many = array( 'visits' );
	
		public static function find_by_code ( $code ) {
			$island = ORM::factory( 'island' )->where( 'code', $code )->find();
			if( true === $island->loaded )
				return $island;
			else
				return false;
		}
	
		public function save () {
			if( empty( $this->code ) )
				$this->code = sha1( $this->title . $this->created . $this->user_id );
			parent::save();
		}

		public function get_link () { return url::site( '/sail/' . $this->code ); }

	}