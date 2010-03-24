<?php

	class form extends form_Core {
	
		public static function error ( $key, $errors ) {
			if( ! array_key_exists( $key, $errors ) )
				return '';
			else
				return '<span class="form-error">' . $errors[$key] . '</span>';
		}
	
	}