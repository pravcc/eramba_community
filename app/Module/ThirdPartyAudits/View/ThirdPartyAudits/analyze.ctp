<?php
$this->Html->addCrumb($auditData['ComplianceAudit']['auditee_title'], false);

// if audit is finished
if ($auditData['ComplianceAudit']['status'] == COMPLIANCE_AUDIT_STOPPED) {
	if ($successFeedback) {
		echo $this->Eramba->getNotificationBox(__('Thank you for your responses, the questionnaire has been completed and submitted to its owner.'));
	}
	else {
		echo $this->Html->div(
			'alert alert-danger', 
			'<i class="icon-exclamation-sign"></i> ' . __('The link for this request has expired and no action is required because the request is complete.')
		);
	}
	
	return true;
}

if (!empty($auditeeInstructions)) {
	echo $this->Eramba->getNotificationBox('<br>' . $auditeeInstructions);
}
?>

<?php if (!empty($data)) : ?>
		
		<div class="row">
			<div class="col-sm-12">
				<div id="auditee-feedback-stats" class="alert alert-warning">
				</div>
			</div>
		</div>	

		<div>
			<?php
			if ($auditData['ComplianceAudit']['status'] != COMPLIANCE_AUDIT_STOPPED) {
				// array('controller' => 'complianceAudits', 'action' => 'provideAllFeedbacks', $auditId)
				echo $this->Form->create();

				echo $this->Html->div('clearfix', $this->Form->submit(__('Submit'), array(
					'class' => 'btn btn-primary bs-popover',
					'style' => 'margin-bottom: 15px;',
					'data-trigger' => 'hover',
					'data-placement' => 'top',
					'data-original-title' => __('Submit Audit'),
					'data-content' => __('Notify auditor all questions has been answered.')
				)), array('escape' => false));

				echo $this->Form->end();
			}
			?>
		</div>	

		<div class="widget box">
			<div class="widget-header">
				<h4><?php echo __('List of Compliance Items associated with your name that need feedback'); ?></h4>
			</div>
			<div class="widget-content">
					<table class="table table-hover table-striped">
						<thead>
							<tr>
								<th><?php echo __( 'Item ID' ); ?></th>
								<th><?php echo __( 'Item Name' ); ?></th>
								<th><?php echo __( 'Your Responses' ); ?></th>

								<?php if (!empty($auditData['ComplianceAudit']['show_findings'])) : ?>
									<th class="align-center">
										<?php echo __('Findings'); ?>
									</th>
								<?php endif; ?>

								<th class="align-center"><?php echo __('Action'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($data as $item) : ?>
							<?php
							// debug($item);
							$packageItem = $item['ComplianceAuditSetting']['CompliancePackageItem'];
							$profileClass = (!empty($item['ComplianceAuditSetting']['ComplianceAuditFeedbackProfile'])) ? ' has-profile' : '';
							?>
							<tr>
								<td><?php echo $packageItem['item_id']; ?></td>
								<td>
									<?php 
									$itemName = array();
									$nameMaxLength = 80;
									if (!empty($item['ComplianceAuditSetting']['ComplianceAudit']['show_analyze_title'])) {
										$label = '<strong>' . __('Title') . ':</strong> ';
										// if (strlen($packageItem['name']) > $nameMaxLength) {
										// 	$itemName[] = $label . $this->Eramba->getTruncatedTooltip($packageItem['name'], array(
										// 		'title' => __('Title'),
										// 		'placement' => 'right',
										// 		'trunceteLength' => $nameMaxLength,
										// 		'class' => 'display-inline-block',
										// 		'icon' => false
										// 	));
										// }
										// else {
											$itemName[] = $label . $packageItem['name'];
										// }
									}
									if (!empty($item['ComplianceAuditSetting']['ComplianceAudit']['show_analyze_description'])) {
										$label = '<strong>' . __('Description') . ':</strong> ';
										// if (strlen($packageItem['description']) > $nameMaxLength) {
										// 	$itemName[] = $label . $this->Eramba->getTruncatedTooltip($packageItem['description'], array(
										// 		'title' => __('Description'),
										// 		'placement' => 'right',
										// 		'trunceteLength' => $nameMaxLength,
										// 		'class' => 'display-inline-block',
										// 		'icon' => false
										// 	));
										// }
										// else {
											$itemName[] = $label . $packageItem['description'];
										// }
									}
									if (!empty($item['ComplianceAuditSetting']['ComplianceAudit']['show_analyze_audit_criteria'])) {
										$label = '<strong>' . __('Audit Criteria') . ':</strong> ';
										// if (strlen($packageItem['audit_questionaire']) > $nameMaxLength) {
										// 	$itemName[] = $label . $this->Eramba->getTruncatedTooltip($packageItem['audit_questionaire'], array(
										// 		'title' => __('Audit Criteria'),
										// 		'placement' => 'right',
										// 		'trunceteLength' => $nameMaxLength,
										// 		'class' => 'display-inline-block',
										// 		'icon' => false
										// 	));
										// }
										// else {
											$itemName[] = $label . $packageItem['audit_questionaire'];
										// }
									}
									echo implode('<br>', $itemName);
									?>
								</td>

								<td class="auditee-feedback <?php echo $profileClass; ?>" id="auditee-feedback-<?php echo $item['ComplianceAuditSetting']['id']; ?>">
									<?php
									if (!empty($item['ComplianceAuditSetting']['ComplianceAuditFeedbackProfile'])) {
										echo $this->element('ThirdPartyAudits.auditee_feedback', array('setting' => $item, 'showAddLink' => true));
									}
									?>
								</td>

								<?php if (!empty($auditData['ComplianceAudit']['show_findings'])) : ?>
									<td class="align-center">
										<?php
										$actionsHtml = false;

										if (!empty($formatFindingsCount[$item['ComplianceAuditSetting']['id']])) {
											$exportUrl = array(
												'plugin' => 'thirdPartyAudits',
												'controller' => 'thirdPartyAudits',
												'action' => 'auditeeExportFindings',
												$item['ComplianceAuditSetting']['id']
											);
											
											$this->Ajax->addToActionList(__('Export Findings'), $exportUrl, 'file', false);

											$actionsHtml = $this->Ajax->getUserDefinedActionList(array(
												'item' => $item
											));
										}

										echo getEmptyValue($actionsHtml);
										?>
									</td>
								<?php endif; ?>

								<td class="align-center">
									<?php
									echo $this->Ajax->getActionList($item['ComplianceAuditSetting']['id'], array(
										'style' => 'icons',
										'edit' => false,
										'trash' => false,
										'records' => false,
										'model' => 'ComplianceAuditSetting',
										'item' => $item
									));
									?>
								</td>
							</tr>
							<?php endforeach ; ?>
						</tbody>
					</table>

			</div>
		</div>

		<script type="text/javascript">
		$(function() {
			function auditeeFeedback(elem) {
        		App.blockUI($('#auditee-feedback-' + elem.data('setting-id')));
				$.ajax({
					url: elem.attr('href'),
					type: 'GET',
				}).done(function(response) {
					$('#auditee-feedback-' + elem.data('setting-id')).html(response);
        			App.unblockUI($('#auditee-feedback-' + elem.data('setting-id')));
				});
			}

			$('.auditee-feedback-add').off('click.Eramba').on('click.Eramba', function() {
				auditeeFeedback($(this));
				return false;
			});

			function auditeeFeedbacksStats() {
				App.blockUI($('#auditee-feedback-stats'));
				$.ajax({
					url: "<?= Router::url([
						'plugin' => 'thirdPartyAudits',
						'controller' => 'thirdPartyAudits',
						'action' => 'auditeeFeedbackStats',
						$auditId
					]) ?>",
					type: 'GET',
				}).done(function(response) {
					$('#auditee-feedback-stats').html(response);
        			App.unblockUI($('#auditee-feedback-stats'));
				});
			}

			auditeeFeedbacksStats();

			$("#content").on("Eramba.ComplianceAudits.auditeeFeedback", function() {
				auditeeFeedbacksStats();
			});
		});
		</script>

	<?php //echo $this->element( CORE_ELEMENT_PATH . 'pagination' ); ?>
<?php else : ?>
	<?php echo $this->element( 'not_found', array(
		'message' => __( 'No Compliance Packages found.' )
	) ); ?>
<?php endif; ?>