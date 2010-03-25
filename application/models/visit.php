<?php

	class Visit_Model extends ORM {
		
		protected $belongs_to = array( 'user', 'island' );
		
	} // class Visit_Model