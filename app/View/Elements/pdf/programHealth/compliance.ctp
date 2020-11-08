<?php
$class = 'table table-hover table-striped';
if (isset($pdf) && $pdf) {
	$class = 'table-pdf table-pdf-list';
}
?>
<?php if ($complianceCount) : ?>
	<table class="<?php echo $class; ?>">
		<thead>
			<tr>
				<th><?php echo __('Third Party'); ?></th>
				<th><?php echo __('Items'); ?></th>
				<th>
					<?php echo __('Addressed Items'); ?>
				</th>
				<th>
					<?php echo __('Overlooked Items'); ?>
				</th>
				<th>
					<?php echo __('Not Applicable Items'); ?>
				</th>
				<th>
					<?php echo __('Not Compliant Items'); ?>
				</th>
				<th>
					<?php echo __('No Controls'); ?>
				</th>
				<th>
					<?php echo __('Failed Controls'); ?>
				</th>
				<th>
					<?php echo __('Average Effectiveness'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($compliance as $item) : ?>
				<?php
				$stats = $this->ComplianceManagements->getPackagesStats($item['CompliancePackage']);
				?>
				<tr>
					<td>
						<?php echo $item['ThirdParty']['name']; ?>
					</td>
					<td>
						<?php
						echo $stats['compliance_management_count'];
						?>
					</td>
					<td>
						<?php
						echo $stats['compliance_mitigate'];
						?>
					</td>
					<td>
						<?php
						echo $stats['compliance_overlooked'];
						?>
					</td>
					<td>
						<?php
						echo $stats['compliance_not_applicable'];
						?>
					</td>
					<td>
						<?php
						echo $stats['compliance_not_compliant'];
						?>
					</td>
					<td>
						<?php
						echo $stats['compliance_without_controls'];
						?>
					</td>
					<td>
						<?php
						echo $stats['failed_controls'];
						?>
					</td>
					<td>
						<?php
						echo CakeNumber::toPercentage($stats['efficacy_average'], 0, array(
							'multiply' => false
						));
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php else : ?>
	<?php
	echo $this->element('not_found', array(
		'message' => __('No Compliance items found.')
	));
	?>
<?php endif; ?>