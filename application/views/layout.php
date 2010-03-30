<html>
	<head>
		<title><?= Kohana::lang( 'general.sitename' ); ?> <?= html::specialchars( $title ); ?></title>
		<?= html::stylesheet( 'css/main', 'screen', true ); ?>
		<?= html::script( 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js' ) ?>
		<?= html::script( 'vendor/markitup/jquery.markitup.js' ) ?>
		<?= html::script( 'vendor/markitup/markdown/set.js' ) ?>
		<?= html::stylesheet( 'vendor/markitup/skins/markitup/style.css', 'screen', true ); ?>
		<?= html::stylesheet( 'vendor/markitup/markdown/style.css', 'screen', true ); ?>
		<script type="text/javascript">
			$( document ).ready( function() { $(".markitup").markItUp( mySettings ); } );
		</script>
	</head>
	<body>
		<div id="container">
			<div id="header">
				<?= html::anchor( "/", html::image( array( 'src' => 'img/pirate.png', 'with' => 128, 'height' => 128 ), array( 'id' => 'the_captain' ) ), array( 'style' => "float: left;" ) ); ?>
				<h1><?= $title; ?></h1>
			</div>
			<div id="content">
				<?= $menu ?>
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
				<p>
					<?= $view ?>
				</p>
			</div>
			<div id="footer">
				<div id="footer-guts">
					<a href="http://github.com/jmhobbs/qaargh" target="_blank"><?= Kohana::lang( 'general.sitename' ); ?></a> v<?= Kohana::config( 'qaargh.version' ); ?>
				</div>
			</div>
		</div>
	</body>
</html>