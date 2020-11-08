<?php if (!empty($tableData['data'])) : ?>
	<table class="table table-hover table-striped table-bordered table-highlight-head">
		<thead>
			<tr>
				<?php foreach ($tableData['titles'] as $title) : ?>
					<th>
						<?php echo $title; ?>
					</th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>

			<?php foreach ($tableData['data'] as $row): ?>
				<tr>
					<?php foreach ($row as $value) : ?>
						<td>
							<?php echo $value; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<?php
	$notFound = isset($notFound) ? $notFound : __('No items found.');
	echo $this->Ux->getAlert($notFound, array(
		'type' => 'info'
	));
	?>
<?php endif; ?>