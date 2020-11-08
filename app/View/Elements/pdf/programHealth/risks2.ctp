<?php
$class = 'table table-hover table-striped';
if (isset($pdf) && $pdf) {
	$class = 'table-pdf table-pdf-list';
}
?>
<?php if ($risksCount) : ?>
	<table class="<?php echo $class; ?>">
		<thead>
			<tr>
				<th><?php echo __('Classification'); ?></th>
				<th><?php echo __('Items'); ?></th>
				<th>
					<?php echo __('Expired Reviews'); ?>
				</th>
				<th>
					<?php echo __('Risk Above Appetite'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($riskClassifications as $item) : ?>
				<tr>
					<td>
						<?php echo $item['RiskClassification']['name']; ?>
					</td>
					<td>
						<?php
						echo count($item['Risk'])+count($item['ThirdPartyRisk'])+count($item['BusinessContinuity']);
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getRiskStatusCount($item, array('expired_reviews'));
						?>
					</td>
					<td>
						<?php
						echo $this->ProgramHealth->getRiskStatusCount($item, array('risk_above_appetite'));
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
					echo count($noClassification['Risk'])+count($noClassification['ThirdPartyRisk'])+count($noClassification['BusinessContinuity']);
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getRiskStatusCount($noClassification, array('expired_reviews'));
					?>
				</td>
				<td>
					<?php
					echo $this->ProgramHealth->getRiskStatusCount($noClassification, array('risk_above_appetite'));
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