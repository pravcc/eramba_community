<div class="widget box advanced-filter-table-widget">
	<div class="widget-content no-padding pb-20">
		<div class="advanced-filter-table-wrapper">
			<?php if (!empty($errorArr)) : ?>
			<table class="advanced-filter-table table table-hover table-striped table-bordered table-highlight-head table-checkable">
				<thead>
					<tr>
						<th><?php echo __('Date')?></th>
						<th class="align-center"><?php echo __( 'Message' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $errorArr as $entry ) : ?>
						<tr>
							<td><?php echo $entry[0]; ?></td>
							<td><?php echo nl2br(trim($entry[1])); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php else : ?>
				<?= $this->Alerts->info(__('No logs found.')); ?>
			<?php endif; ?>
		</div>
	</div>
</div>