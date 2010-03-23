<?php

	echo form::open();
	echo form::label( 'email', Kohana::lang( 'user.email' ) . ': ' );
	echo form::input( 'email', $email );
	echo '<br/>';
	echo form::label( 'username', Kohana::lang( 'user.username' ) . ': ' );
	echo form::input( 'username', $username );
	echo '<br/>';
	echo form::label( 'password', Kohana::lang( 'user.password' ) . ': ' );
	echo form::password( 'password' );
	echo '<br/>';
	echo form::submit( 'submit', Kohana::lang( 'user.sign_up' ), 'class="submit label-offset"' );
	echo form::close();