<?php

	class User_Model extends Auth_User_Model {
		
		protected $has_many = array( 'islands', 'visits' );
		
	} // class User_Model