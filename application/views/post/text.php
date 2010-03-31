<div class="post text-post">
	<?= text::auto_p( html::specialchars( $post->get_post()->content ) ); ?>
	<div class="post-attribution">Posted By <?= html::anchor( '/user/view/' . html::specialchars( $post->user->username ), html::specialchars( $post->user->username ) ); ?></div>
</div>