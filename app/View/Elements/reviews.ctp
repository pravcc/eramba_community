<div class="widget box widget-closed">
	<div class="widget-header">
		<h4><?php echo __('Reviews'); ?></h4>
		<div class="toolbar no-padding">
			<div class="btn-group">
				<span class="btn btn-xs widget-collapse"><i class="icon-angle-up"></i></span>
			</div>
		</div>
	</div>
	<div class="widget-content" style="display:none;">
		<?php if (!empty($item['Review'])) : ?>
			<table class="table table-hover table-striped">
				<thead>
					<tr>
						<th>
							<?php echo __('Planned Date'); ?>
						</th>
						<th>
							<?php echo __('Actual Date'); ?>
						</th>
						<th>
							<?php echo __('Description'); ?>
						</th>
						<th>
							<?php echo __('Reviewer'); ?>
						</th>
						<th>
							<?php echo __('Completed'); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($item['Review'] as $review) : ?>
					<tr>
						<td>
							<?php
							echo $this->Eramba->getEmptyValue($review['planned_date']);
							?>
						</td>
						<td>
							<?php
							echo $this->Eramba->getEmptyValue($review['actual_date']);
							?>
						</td>
						<td>
							<?php
							echo $this->Eramba->getEmptyValue($review['description']);
							?>
						</td>
						<td>
							<?php
							echo $review['User']['full_name'];
							?>
						</td>
						<td>
							<?php
							if ($review['completed'] == REVIEW_COMPLETE) {
								echo __('Yes');
							}
							else {
								echo __('No');
							}
							?>
						</td>
					</tr>
					<?php endforeach ; ?>
				</tbody>
			</table>
		<?php else : ?>
			<?php echo $this->element( 'not_found', array(
				'message' => __( 'No Reviews found.' )
			) ); ?>
		<?php endif; ?>
	</div>
</div>