<?php

	class Footsteps_Core {

		const HIT = 'HIT';
		const NOTE = 'NOTE';
		const LOGIN = 'LOGIN';
		const LOGOUT = 'LOGOUT';

		public static function step ( $note = '', $type = self::HIT ) {
			$footstep = ORM::factory( 'footstep' );

			$footstep->occurred = date( 'Y-m-d H:i:s' );
			$footstep->request_uri = ( isset ( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '' );
			$footstep->query_string = ( isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : '' );
			$footstep->controller = router::$controller;
			$footstep->method = router::$method;
			$footstep->sessionid = Session::instance()->id();
			$footstep->referral = ( isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '' );
			$footstep->ip = ( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '' );
			$footstep->note = $note;
			$footstep->type = $type;

			if( Auth::instance()->logged_in() ) {
				$footstep->user_username = Auth::instance()->get_user()->username;
				$footstep->user_id = Auth::instance()->get_user()->id;
			}
			else {
				$footstep->user_username = $footstep->ip;
				$footstep->user_id = 0;
			}

			$footstep->save();
			if( ! $footstep->saved )
				Kohana::log( 'error', 'Could not save footstep!' );
		}

	}
