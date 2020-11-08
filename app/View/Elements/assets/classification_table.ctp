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
		<?php if (!empty($assetClassificationData['joinAssets'][$asset['Asset']['id']])) : ?>
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
				<?php foreach ($assetClassificationData['joinAssets'][$asset['Asset']['id']] as $classificationId) : ?>
					<?php
					$classification = $assetClassificationData['formattedData'][$classificationId];
					?>
					<tr>
						<td><?php echo $classification['AssetClassificationType']['name']; ?></td>
						<td><?php echo $classification['AssetClassification']['name']; ?></td>
						<td><?php echo $classification['AssetClassification']['criteria']; ?></td>
						<td><?php echo $classification['AssetClassification']['value']; ?></td>
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