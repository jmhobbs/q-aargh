<?php

	class Island_Model extends ORM {
	
		protected $belongs_to = array( 'user' );
	
		public static function find_by_code ( $code ) {
			$island = ORM::factory( 'island' )->where( 'code', $code )->find();
			if( true === $island->loaded )
				return $island;
			else
				return false;
		}
	
	}