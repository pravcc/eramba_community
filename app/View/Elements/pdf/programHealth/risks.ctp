<?php
$class = 'table table-hover table-striped';
if (isset($pdf) && $pdf) {
	$class = 'table-pdf table-pdf-list';
}

if (!isset($model)) {
	$model = 'Risk';
}
?>

<?php if ($risksCount[$model]) : ?>
	<?php
	$controller = Inflector::variable(Inflector::tableize(Inflector::pluralize($model)));
	?>
	<table class="<?php echo $class; ?>">
		<thead>
			<tr>
				<th><?php echo __('Classification'); ?></th>
				<th><?php echo __('Items'); ?></th>
				<th>
					<?php echo __('Expired Reviews'); ?>

					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => $controller,
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
				<th>
					<?php echo __('Risk Above Appetite'); ?>

					<?php
					echo $this->Html->link('<i class="icon-info-sign"></i>', array(
						'controller' => $controller,
						'action' => 'index',
						'?' => array(
							'risk_above_appetite' => true
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
			<?php
			// debug($riskClassifications);
			?>
			<?php foreach ($riskClassifications[$model]['classifications'] as $name => $items) : ?>
				<tr>
					<td>
						<?php echo $name; ?>
					</td>
					<td>
						<?php
						echo count($items[$model]);
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getTagStatusCount('Risks', $model, $items, array('expired_reviews'));
						?>
					</td>

					<td>
						<?php
						echo $this->ProgramHealth->getTagStatusCount('Risks', $model, $items, array('risk_above_appetite'));
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
					echo count($riskClassifications[$model]['noItemClassifications'][$model]);
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getTagStatusCount('Risks', $model, $riskClassifications[$model]['noItemClassifications'], array('expired_reviews'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getTagStatusCount('Risks', $model, $riskClassifications[$model]['noItemClassifications'], array('risk_above_appetite'));
					?>
				</td>
			</tr>
		</tbody>
	</table>
<?php else : ?>
	<?php
	echo $this->element('not_found', array(
		'message' => __('No Risks found.')
	));
	?>
<?php endif; ?>