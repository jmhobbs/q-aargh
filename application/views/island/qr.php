<?php

	if( $qrimg ) { echo html::image( $qrimg ); }

	echo form::open();
	echo form::label( 'size', 'Size:' );
	echo form::input( 'size', $size );
	echo '<br/>';
	echo form::submit( 'submit', 'Get QR Code', 'class="submit label-offset"' );
	echo form::close();