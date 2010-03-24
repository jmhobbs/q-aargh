<h2>My Islands</h2>

<p>
	<?= html::anchor( '/island/create', '+ New Island' ); ?>
</p>

<table>
	<tr>
		<th>Title</th>
		<th>Created</th>
		<th>Modified</th>
		<th>Views</th>
		<th>Link</th>
		<th>QR Code</th>
	</tr>
	<?php foreach( $islands as $island ) : ?>
	<tr<?= text::alternate( '', ' class="odd"' ) ?>>
		<td><?= html::specialchars( $island->title ) ?></td>
		<td><?= $island->created ?></td>
		<td><?= $island->modified ?></td>
		<td><?= $island->views ?></td>
		<td><?= html::anchor( $island->get_link(), $island->get_link() ) ?></td>
		<td class="last"><?= html::anchor( qr::get( $island ), 'QR Code' ) ?></td>
	</tr>
	<?php endforeach; ?>
</table>