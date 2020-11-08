<div class="widget box widget-closed">
	<div class="widget-header">
		<h4><?php echo __('Classification'); ?></h4>
		<div class="toolbar no-padding">
			<div class="btn-group">
				<span class="btn btn-xs widget-collapse"><i class="icon-angle-up"></i></span>
			</div>
		</div>
	</div>
	<div class="widget-content" style="display:none;">
		<?php //if (!empty($risk['RiskClassification'])) : ?>
		<?php if (!empty($riskClassificationData['joinIds'][$risk[$model]['id']])) : ?>
		<table class="table table-hover table-striped">
			<thead>
				<tr>
					<th><?php echo __('Type'); ?></th>
					<th><?php echo __('Name'); ?></th>
					<th><?php echo __('Criteria'); ?></th>
					<th><?php echo __('Value'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php //foreach ($risk['RiskClassification'] as $classification) : ?>
				<?php foreach ($riskClassificationData['joinIds'][$risk[$model]['id']] as $classificationId) : ?>
					<?php
					$classification = $riskClassificationData['formattedData'][$classificationId];
					?>
					<tr>
						<td><?php echo $classification['RiskClassificationType']['name']; ?></td>
						<td><?php echo $classification['RiskClassification']['name']; ?></td>
						<td><?php echo $classification['RiskClassification']['criteria']; ?></td>
						<td><?php echo $classification['RiskClassification']['value']; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
			<?php
			echo $this->element('not_found', array(
				'message' => __('No Classifications found.')
			));
			?>
		<?php endif; ?>
	</div>
</div>