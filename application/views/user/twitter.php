<h1>Welcome <?= $username; ?></h1>
<p>
	No that you are authenticated through Twitter, you have two options.
	<ul>
		<li><a href="#new">Create A New Account</a></li>
		<li><a href="#link">Link To An Existing Account</a></li>
	</ul>
</p>

<a name="new"></a>
<h2>Create A New Account</h2>
<?=
	echo form::open();
	echo form::label( 'username', 'Username: ' );
	echo form::input( 'username', $iusername );
	echo '<br/>';
	echo form::submit( 'submit', 'Create Account', 'class="submit label-offset"' );
	echo form::close();
?>

<a name="link"></a>
<h2>Link To An Existing Account</h2>
<?=
	echo form::open();
	echo form::label( 'username', 'Username: ' );
	echo form::input( 'username', $iusername );
	echo '<br/>';
	echo form::label( 'password', 'Password: ' );
	echo form::password( 'password' );
	echo '<br/>';
	echo form::submit( 'submit', 'Link Account', 'class="submit label-offset"' );
	echo form::close();
?>