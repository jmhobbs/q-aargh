<fieldset class="island-stats">
	<legend>Island Stats</legend>
	<label>Owner:</label> <?= html::anchor( '/user/view/' . html::specialchars( $island->user->username ), html::specialchars( $island->user->username ) ); ?><br/>
	<label>Visits:</label> <?= $island->visits() ?><br/>
	<label>Views:</label> <?= $island->views ?><br/>
</fieldset>

<h2>Message From The Founder</h2>
<?= text::auto_p( html::specialchars( $island->introduction ) ); ?>

<h2>Subsequent Posts</h2>
<?php
	foreach( $island->posts() as $post ) {
		$view = new View( 'post/' . $post->type );
		$view->post = $post;
		echo $view->render();
	}