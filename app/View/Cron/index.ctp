<div class="row">
	<div class="col-md-12">
		<div class="widget">
			<div class="btn-toolbar">
				<div class="btn-group">
				</div>
				<?php echo $this->AdvancedFilters->getViewList($savedFilters, $filter['model'], true); ?>

				<?php echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . 'headerRight'); ?>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="widget">
			<?php if (!empty($data)) : ?>
				<table class="table table-hover table-bordered table-highlight-head table-larger-head">
					<thead>
						<tr>
							<th>
								<?php echo __('Date'); ?>
							</th>
							<th>
								<?php echo __('Type'); ?>
							</th>
							<th>
								<?php echo __('Execution Time'); ?>
							</th>
							<th>
								<?php echo __('Status'); ?>
							</th>

							<th>
								<?php echo __('Message'); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($data as $cron) : ?>
							<tr>
								<td>
									<?php echo date(DATE_FORMAT, strtotime($cron['Cron']['created'])); ?>
								</td>
								<td>
									<?php echo getCronTypes()[$cron['Cron']['type']]; ?>
								</td>
								<td>
									<?php
									$executionTimeLabel = $this->Crons->getExecutionTimeLabel($cron['Cron']['execution_time']);

									if (!empty($executionTimeLabel)) {
										echo $executionTimeLabel;
									}
									else {
										echo $this->Eramba->getEmptyValue();
									}
									?>
								</td>
								<td>
									<?php echo $this->Eramba->getLabel(getCronStatuses()[$cron['Cron']['status']], $cron['Cron']['status']); ?>
								</td>

								<td>
									<?php echo $this->Ux->text($cron['Cron']['message']); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

			<?php else : ?>
				<?php
				echo $this->element('not_found', array(
					'message' => __('Cron jobs not found.')
				));
				?>
			<?php endif; ?>

			<?php
			echo $this->element(CORE_ELEMENT_PATH . 'pagination');
			?>

		</div>
	</div>

</div>