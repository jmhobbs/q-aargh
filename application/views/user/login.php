<?php
	echo form::open();
	echo form::label( 'username', Kohana::lang( 'user.username' ) . ': ' );
	echo form::input( 'username', $username );
	echo '<br/>';
	echo form::label( 'password', Kohana::lang( 'user.password' ) . ': ' );
	echo form::password( 'password' );
	echo '<br/>';
	echo form::submit( 'submit', Kohana::lang( 'user.log_in' ), 'class="submit label-offset"' );
	echo form::close();