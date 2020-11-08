<div class="row">
	<div class="col-xs-12">

		<div class="header-separator"></div>
		<div class="header">
			<div class="subtitle">
				<h2>
					<?php echo __('Reviews'); ?>
				</h2>
			</div>
		</div>

	</div>
</div>
<div class="row">
	<div class="col-xs-12">

		<div class="body">
			<?php if ( ! empty($item['Review'])) : ?>

				<?php foreach ($item['Review'] as $review) : ?>
					<div class="item">
						<table class="quadruple-column uneven">
							<tr>
								<th>
									<?php echo __('Planned date'); ?>
								</th>
								<th>
									<?php echo __('Actual date'); ?>
								</th>
								<th>
									<?php echo __('Reviewer'); ?>
								</th>
								<th>
									<?php echo __('Description'); ?>
								</th>
							</tr>
							
							<tr>
								<td><?php echo $review['planned_date']; ?></td>
								<td><?php echo $review['actual_date']; ?></td>
								<td><?php echo !empty($review['User']) ? $review['User']['full_name'] : '-'; ?></td>
								<td><?php echo $review['description']; ?></td>
							</tr>
						</table>
					</div>

					<!-- <div class="separator"></div> -->
				<?php endforeach ; ?>

			<?php else : ?>
				<div class="item">
					<?php
					echo $this->Html->div('alert', 
						__('No reviews found.')
					);
					?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</div>