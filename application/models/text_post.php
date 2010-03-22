<?php

	class Text_Post_Model extends ORM {
	
		protected $belongs_to = array( 'user', 'island' );

	}