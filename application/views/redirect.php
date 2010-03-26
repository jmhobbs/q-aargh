<h1>Redirecting you to <?= $destination; ?></h1>
<p>
	If you are not redirected in a few seconds, please click <?= html::anchor( $url, 'here' ); ?>
</p>
<script type="text/javascript"> window.location = "<?= $url; ?>"; </script>