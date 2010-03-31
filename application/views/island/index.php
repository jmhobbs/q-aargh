<?= text::auto_p( html::specialchars( $island->introduction ) ); ?>

<h2>Posts</h2>
<?php
	foreach( $island->posts() as $post ) {
		$view = new View( 'post/' . $post->type );
		$view->post = $post;
		echo $view->render();
	}