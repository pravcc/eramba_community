<?php
//NOTE: this widget is also used in PolicyException section
if (empty($widgetTitle)) {
	$widgetTitle = __('Assets');
}
?>
<div class="widget box widget-closed">
	<div class="widget-header">
		<h4><?php echo $widgetTitle; ?></h4>
		<div class="toolbar no-padding">
			<div class="btn-group">
				<span class="btn btn-xs widget-collapse"><i class="icon-angle-up"></i></span>
			</div>
		</div>
	</div>
	<div class="widget-content" style="display:none;">
		<?php if (!empty($data)) : ?>
		<table class="table table-hover table-striped">
			<thead>
				<tr>
					<th><?php echo __( 'Name' ); ?></th>
					<th><?php echo __( 'Parent Assets' ); ?></th>
					<th><?php echo __( 'Description' ); ?></th>
					<th><?php echo __( 'Label' ); ?></th>
					<th><?php echo __( 'Liabilities' ); ?></th>
					<th><?php echo __('Classification'); ?></th>
					<th><?php echo __( 'Status' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data as $assetId) : ?>
					<?php
					$asset = $assetData['formattedData'][$assetId];
					?>
					<tr>
						<td>
							<?php
							echo $this->Html->link( $asset['Asset']['name'], array(
								'controller' => 'assets',
								'action' => 'index',
								'?' => array(
									'id' => $asset['Asset']['id']
								)
							));
							?>
						</td>
						<td>
							<?php
							App::uses('Hash', 'Utility');

							$parents = Hash::combine($asset['RelatedAssets'], '{n}.id', '{n}.name');
							
							$parentLinks = array();
							foreach ($parents as $parentId => $parentName) {
								$parentLinks[] = $this->Html->link($parentName, array(
									'controller' => 'assets',
									'action' => 'index',
									'?' => array(
										'id' => $parentId
									)
								)); 
							}

							echo getEmptyValue(implode(', ', $parentLinks));
							?>
						</td>
						<td><?php echo $this->Eramba->getEmptyValue($asset['Asset']['description']); ?></td>
						<td><?php echo ! empty( $asset['AssetLabel'] ) ? $asset['AssetLabel']['name'] : ''; ?></td>
						<td>
							<?php
							$legals = array();
							foreach ($asset['Legal'] as $legal) {
								$legals[] = $legal['name'];
							}
							echo implode(', ', $legals);
							?>
						</td>
						<td>
							<?php
							echo $this->element('assets/classification_table_cell', array(
								'assetId' => $assetId
							));
							?>
						</td>
						<td>
							<?php
							echo $this->Assets->getStatuses($asset);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
			<?php echo $this->element( 'not_found', array(
				'message' => __( 'No Assets found.' )
			) ); ?>
		<?php endif; ?>
	</div>
</div>