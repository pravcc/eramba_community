<div class="widget box widget-closed">
	<div class="widget-header">
		<h4><?php echo __('Treatment Security Services'); ?></h4>
		<div class="toolbar no-padding">
			<div class="btn-group">
				<span class="btn btn-xs widget-collapse"><i class="icon-angle-up"></i></span>
			</div>
		</div>
	</div>
	<div class="widget-content" style="display:none;">
		<?php if (!empty($securityServicesData['joinIds'][$riskId])) : ?>
		<table class="table table-hover table-striped">
			<thead>
				<tr>
					<th>
					        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The of the security control.' ); ?>'>
						<?php echo __('Name'); ?>
					        <i class="icon-info-sign"></i>
					        </div>
					</th>
					<th>
					        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'A brief description of what the control aims..' ); ?>'>
						<?php echo __('Objective'); ?>
					        <i class="icon-info-sign"></i>
					        </div>
					</th>
					<th>
					        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'You can use this field in any way it fits best your organisation, in some cases this role relates to the GRC individual responsible to ensure the control is well documented and tested (audits). In some other organisations this role belongs to the responsible for the control to be operated correctly. This role will be available when you create notifications under the field Custom Roles.' ); ?>'>
						<?php echo __('Owner'); ?>
					        <i class="icon-info-sign"></i>
					        </div>
					</th>
					<th>
				        <div class="bs-popover" data-trigger="hover" data-placement="top" data-original-title="<?php echo __( 'Help' ); ?>" data-content='<?php echo __( 'The available status for security services are: - "Last audit failed (red)" - when the last audit for this security service is tagged as "failed". A system record is generated on the security service when the audit was tagged as failed. - "Last audit missing (yellow)" - when the last audit for this security service is incomplete. A system record is generated on the security service when the audit day arrived and the item was not edited. - "Last maintenance missing (yellow)" - when the last maintenance for this security service is incomplete. A system record is generated on the security service when the maintenance day arrived and the item was not edited. - "Ongoing Corrective Actions (blue)" - when the last audit of this service was tagged as failed and a project has been asociated. A system record is generated on the security service when the project is assigned to the failed audit. - "Ongoing Security Incident (yellow)" - when a given securit service has a security incident with status open mapped. A system record is created when the incident has been mapped. The record has the incident title. - "Design (yellow)" - when a given security service is in status "design". When the item is set to design or production a system record is generated stated if it changed to "design" or "production".' ); ?>'>
							<?php echo __( 'Status' ); ?>
							<i class="icon-info-sign"></i>
						</div>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($securityServicesData['joinIds'][$riskId] as $securityServiceId) : ?>
					<?php
					$security_service = $securityServicesData['formattedData'][$securityServiceId];
					?>
					<tr>
						<td>
							<?php
							echo $this->Ux->getItemLink(
								$security_service['SecurityService']['name'],
								'SecurityService',
								$security_service['SecurityService']['id']
							);
							?>
						</td>
						<td>
							<?php
							echo $this->Eramba->getEmptyValue($security_service['SecurityService']['objective']);
							?>
						</td>
						<td>
							<?= $this->UserField->showUserFieldRecords($security_service['ServiceOwner']); ?>
						</td>
						<td>
							<?php
							echo $this->SecurityServices->getStatuses($security_service);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php else : ?>
			<?php echo $this->element( 'not_found', array(
				'message' => __( 'No Security Controls found.' )
			) ); ?>
		<?php endif; ?>
	</div>
</div>