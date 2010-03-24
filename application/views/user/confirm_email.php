<h1>Ahoy!</h1>
<p>
	Someone (hopefully you) created an account on <?= html::link( '/', url::site() ); ?>.
</p>
<p>
	If this was you, you'll need to confirm your account by visiting the link below.
</p>
<p>
	<?= html::link( '/user/confirm/' . $code ); ?>
</p>
<p>
	If you the link does not work, you can visit <?= html::link( '/user/confirm' ); ?> and use the following confirm code.
</p>
<p>
	<?= $code; ?>
</p>
<p>
	If you did <b>not</b> create this account, please ignore this message.
</p>