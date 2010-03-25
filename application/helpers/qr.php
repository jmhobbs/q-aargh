<?php

	class qr_Core {
	
		// Versions that we can generate.
		// Some mobile devices can only read up to v4!
		public static $versions = array(
			1 => 25,
			2 => 47,
			3 => 77,
			4 => 114,
			10 => 395,
			40 => 4296
		);
	
		public static function get ( $island, $size=150 ) {
			//! \todo UTF-8 conversion or url?
			$url = "http://chart.apis.google.com/chart?chs={$size}x{$size}&cht=qr&chl=" .urlencode( $island->get_link() ). "&choe=UTF-8%chld=L|4";

			$island = sha1( $island );
			$cache_dir = Kohana::config( 'qaargh.qr_directory' );
			$subdir = substr( $island, 0, 1);
			$cache_path = "/{$subdir}/{$island}_{$size}x{$size}.png";
			$cache_file = "{$cache_dir}{$cache_path}";
			
			if( ! file_exists( $cache_file ) ) {
				
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $url );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				$result = curl_exec( $ch );
				curl_close( $ch );
				
				if( false === $result )
					return false;
				
				if( false === file_put_contents( $cache_file, $result ) )
					return false;

			}
			
			return '/qr/' . $cache_path;
		}

	}