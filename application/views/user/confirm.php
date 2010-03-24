<?php

	echo form::open();
	echo form::label( 'code', Kohana::lang( 'user.code' ) . ':' );
	echo form::input( 'code' );
	echo "<br/>";
	echo form::submit( 'submit', Kohana::lang( 'user.confirm' ), 'class="label-offset"' );
	echo form::close();