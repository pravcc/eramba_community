<?php
$class = 'table table-hover table-striped';
if (isset($pdf) && $pdf) {
	$class = 'table-pdf table-pdf-list';
}
?>
<?php if ($assetsCount) : ?>
	<table class="<?php echo $class; ?>">
		<thead>
			<tr>
				<th><?php echo __('Classification'); ?></th>
				<th><?php echo __('Items'); ?></th>
				<th>
					<?php echo __('Expired Reviews'); ?>

					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => 'assets',
						'action' => 'index',
						'?' => array(
							'expired_reviews' => true
						)
					), array(
						'class' => 'bs-tooltip',
						'title' => __('Show list'),
						'escape' => false,
						'style' => 'text-decoration:none;'
					));
					?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($assetClassifications as $item) : ?>
				<tr>
					<td>
						<?php echo $item['AssetClassification']['name']; ?>
					</td>
					<td>
						<?php
						echo count($item['Asset']);
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getAssetStatusCount($item, array('expired_reviews'));
						?>
					</td>
				</tr>
			<?php endforeach; ?>

			<!-- No classification entry -->
			<tr>
				<td>
					<strong><?php echo __('All Others'); ?></strong>
				</td>
				<td>
					<?php
					echo count($noAssetClassification['Asset']);
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getAssetStatusCount($noAssetClassification, array('expired_reviews'));
					?>
				</td>
			</tr>
		</tbody>
	</table>
<?php else : ?>
	<?php
	echo $this->element('not_found', array(
		'message' => __('No Assets found.')
	));
	?>
<?php endif; ?>