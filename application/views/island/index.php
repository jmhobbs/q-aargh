<fieldset class="island-stats">
	<legend>Island Stats</legend>
	<label>Owner:</label> <?= html::anchor( '/user/view/' . html::specialchars( $island->user->username ), html::specialchars( $island->user->username ) ); ?><br/>
	<label>Uniques:</label> <?= $island->visits() ?><br/>
	<label>Views:</label> <?= $island->views ?><br/>
</fieldset>

<h2>Message From The Founder</h2>
<div class="post text-post">
	<?= text::auto_p( html::specialchars( $island->introduction ) ); ?>
</div>

<h2>Subsequent Posts</h2>
<?php
	foreach( $island->posts() as $post ) {
		$view = new View( 'post/' . $post->type );
		$view->post = $post;
		echo $view->render();
	}
?>
<?php if( Auth::instance()->logged_in() ): ?>
<h2>Leave A Post</h2>
<?php
	echo form::open( '/island/post/' . $island->code );
	echo form::label( 'comment', 'Comment:' );
	echo form::textarea( 'comment' );
	echo '<br/>';
	echo form::submit( 'submit', 'Submit', 'class="submit label-offset"' );
	echo form::close();
?>
<?php endif; ?>