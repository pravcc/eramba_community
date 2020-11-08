<table class="table table-hover table-striped">
	<thead>
		<tr>
			<th><?php echo __('UID'); ?></th>
			<th><?php echo __('Dates (last 3 recorded)'); ?></th>
			<th><?php echo __('Total'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($stats['compliantUsers'] as $uid) : ?>
			<?php
			$recurrences = '';
			$total = 0;
			if (!empty($stats['doneRecurrences'][$uid])) {
				$total = count($stats['doneRecurrences'][$uid]);

				//only last 3 records
				$slicedRecords = array_slice($stats['doneRecurrences'][$uid], 0, 3);
				$recurrences = implode('<br />', $slicedRecords);
			}
			?>
			<tr>
				<td><?php echo $uid; ?></td>
				<td><?php echo $recurrences; ?></td>
				<td><?php echo $total; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>