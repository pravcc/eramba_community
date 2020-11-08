<?php if (!empty($item['RiskClassification'])): ?>
	<div class="row">
		<div class="col-xs-12">

			<div class="header">
				<div class="subtitle">
					<h2>
						<?php echo __('Classification'); ?>
					</h2>
				</div>
			</div>

		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="body">

				<?php if (!empty($item['RiskClassification'])) : ?>
					<div class="item">
						<table class="table-pdf table-pdf-list" style="">
							<thead>
								<tr>
									<th><?php echo __('Type'); ?></th>
									<th><?php echo __('Name'); ?></th>
									<th><?php echo __('Criteria'); ?></th>
									<th><?php echo __('Value'); ?></th>
								</tr>
							</thead>
							
							<tbody>
								<?php foreach ($item['RiskClassification'] as $classification) : ?>
								<tr>
									<td><?php echo $classification['RiskClassificationType']['name']; ?></td>
									<td><?php echo $classification['name']; ?></td>
									<td><?php echo $classification['criteria']; ?></td>
									<td><?php echo $classification['value']; ?></td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

				<?php endif; ?>

			</div>
		</div>
	</div>
<?php endif; ?>