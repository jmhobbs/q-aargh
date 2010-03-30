<p>
	Someone already has an account with the same username as your Twitter account, "<?= html::specialchars( $username ); ?>".
</p>
<p>
	Please choose a new username for Q-Aargh.
</p>
<p>
	You will still be able to log in through Twitter.
</p>

<?php
	echo form::open();
	echo form::label( 'username', 'Username:' );
	echo form::input( 'username', '' );
	echo '<br/>';
	echo form::submit( 'submit', 'Create Account', 'class="submit label-offset"' );
	echo form::close();