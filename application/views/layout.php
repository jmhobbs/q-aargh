<html>
	<head>
		<title>Q-Aargh! <?= $title; ?></title>
		<?= html::stylesheet( 'css/main', 'screen', true ); ?>
	</head>
	<body>
		<div id="header">
			<h1><?= $title; ?></h1>
		</div>
		<?php
			$flash = $this->session->get_once( 'flash' );
			if( ! empty( $flash ) )
				echo '<div class="flash">' . $flash . '</div>';
				
			$notice_flash = $this->session->get_once( 'notice' );
			if( ! empty( $notice_flash ) )
				echo '<div class="notice-flash flash">' . $notice_flash . '</div>';
				
			$error_flash = $this->session->get_once( 'error' );
			if( ! empty( $error_flash ) )
				echo '<div class="error-flash flash">' . $error_flash . '</div>';
		?>
		<div id="content">
			<p>
				<?= $view ?>
			</p>
		</div>
	</body>
</html>