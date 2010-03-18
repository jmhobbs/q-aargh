<?php
	echo form::open();
	echo form::label( 'username', 'Username: ' );
	echo form::input( 'username', $username );
	echo '<br/>';
	echo form::label( 'password', 'Password: ' );
	echo form::password( 'password' );
	echo '<br/>';
	echo form::submit( 'submit', 'Login', 'class="label-offset"' );
	echo form::close();