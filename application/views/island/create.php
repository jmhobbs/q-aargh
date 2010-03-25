<?php

	echo form::open();
	echo form::label( 'title', 'Title:' );
	echo form::input( 'title' );
	echo '<br/>';
	echo form::label( 'introduction', 'Introduction:' );
	echo form::textarea( 'introduction', '', 'class="markitup"' );
	echo '<br/>';
	echo form::submit( 'submit', 'Create!', 'class="submit label-offset"' );
	echo form::close();