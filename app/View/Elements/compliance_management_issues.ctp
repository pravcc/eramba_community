<tbody>
<?php foreach ( $compliance_packages as $compliance_package ) : ?>
	<?php foreach ( $compliance_package['CompliancePackageItem'] as $compliance_package_item ) : ?>

		<?php if(!isset($type)):?>
		
			<?php if ( empty( $compliance_package_item['ComplianceManagement'] ) ) continue; ?>

			<?php if (!empty($compliance_package_item['ComplianceManagement']['ComplianceException'])) : ?>
				<?php foreach ($compliance_package_item['ComplianceManagement']['ComplianceException'] as $exception) : ?>
					<tr>
						<td><?php echo $compliance_package_item['item_id'] . ' - ' . $compliance_package_item['name']; ?></td>
						<td><?php echo __( 'Compliance Exception' ); ?></td>
						<td><?php echo $exception['title']; ?></td>
						<td><?php echo $this->ComplianceExceptions->getStatuses(['ComplianceException' => $exception]); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php foreach ( $compliance_package_item['ComplianceManagement']['SecurityService'] as $security_service ) : ?>
				<?php
				$ret = $this->SecurityServices->getStatusArr($security_service);
				if (empty($ret)) {
					continue;
				}
				
				/*if (($security_service['audits_all_done'] &&
						$security_service['audits_last_passed']) &&
						$security_service['maintenances_all_done'] &&
						$security_service['maintenances_last_passed']) {
					continue;
				}*/
				?>
				<tr>
					<td><?php echo $compliance_package_item['item_id'] . ' - ' . $compliance_package_item['name']; ?></td>
					<td><?php echo __( 'Security Service' ); ?></td>
					<td><?php echo $security_service['name']; ?></td>
					<td>
						<?php
						echo $this->SecurityServices->getStatuses($security_service);
						?>
						<?php //echo $this->SecurityServices->statusLabels($security_service); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php foreach ( $compliance_package_item['ComplianceManagement']['SecurityPolicy'] as $security_policy ) : ?>
				<?php
				$notification = '';
				if ( $security_policy['status'] == SECURITY_POLICY_DRAFT ) {
					$notification = '<span class="label label-warning">' . __( 'Draft' ) . '</span>';
				}
				?>
				<?php if ( $notification ) : ?>
				<tr>
					<td><?php echo $compliance_package_item['item_id'] . ' - ' . $compliance_package_item['name']; ?></td>
					<td><?php echo __( 'Security Policy' ); ?></td>
					<td><?php echo $security_policy['index']; ?></td>
					<td><?php echo $notification; ?></td>
				</tr>
				<?php endif; ?>
			<?php endforeach; ?>

		<?php elseif($type == 'not_applicable'):?>

			<?php if ( empty( $compliance_package_item['ComplianceManagement'] ) ) continue; ?>
			<?php if($compliance_package_item['ComplianceManagement']['compliance_treatment_strategy_id'] == COMPLIANCE_TREATMENT_NOT_APPLICABLE): ?>
				<tr>
					<td><?php echo $compliance_package_item['item_id'] . ' - ' . $compliance_package_item['name']; ?></td>
				</tr>
			<?php endif; ?>

		<?php elseif($type == 'not_compliant'):?>

			<?php if ( empty( $compliance_package_item['ComplianceManagement'] ) ) continue; ?>
			<?php if($compliance_package_item['ComplianceManagement']['compliance_treatment_strategy_id'] == COMPLIANCE_TREATMENT_NOT_COMPLIANT): ?>
				<tr>
					<td><?php echo $compliance_package_item['item_id'] . ' - ' . $compliance_package_item['name']; ?></td>
				</tr>
			<?php endif; ?>

		<?php elseif($type == 'not_edited') : ?>

			<?php if ( empty( $compliance_package_item['ComplianceManagement'] ) ): ?>
				<tr>
					<td><?php echo $compliance_package_item['item_id'] . ' - ' . $compliance_package_item['name']; ?></td>
				</tr>
			<?php endif; ?>

		<?php endif; ?>

	<?php endforeach; ?>
<?php endforeach; ?>
</tbody>