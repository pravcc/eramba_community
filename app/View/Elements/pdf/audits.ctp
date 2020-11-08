<div class="row">
	<div class="col-xs-12">

		<div class="header">
			<div class="subtitle">
				<h2>
					<?php
					if (isset($title) && !empty($title)) {
						echo $title;
					}
					else {
						echo __('Audit Information');
					}
					?>
				</h2>
			</div>
		</div>

	</div>
</div>

<div class="row">
	<div class="col-xs-12">

		<div class="body">
			<?php foreach ($item['GoalAudit'] as $audit) : ?>
				<div class="item">
					<table>
						<tr>
							<th>
								<?php echo __('Audit metric'); ?>
							</th>
						</tr>
						
						<tr>
							<td>
								<?php
								echo $this->Eramba->getEmptyValue(nl2br($audit['audit_metric_description']));
								?>
							</td>
						</tr>
					</table>
				</div>

				<div class="item">
					<table>
						<tr>
							<th>
								<?php echo __('Audit success criteria'); ?>
							</th>
						</tr>
						
						<tr>
							<td>
								<?php
								echo $this->Eramba->getEmptyValue(nl2br($audit['audit_success_criteria']));
								?>
							</td>
						</tr>
					</table>
				</div>

				<div class="item">
					<table class="triple-column">
						<tr>
							<th>
								<?php echo __('Planned audit date'); ?>
							</th>
							<th>
								<?php echo __('Actual start date'); ?>
							</th>
							<th>
								<?php echo __('Actual end date'); ?>
							</th>
						</tr>
						
						<tr>
							<td>
								<?php echo $audit['planned_date']; ?>
							</td>
							<td>
								<?php echo $audit['start_date']; ?>
							</td>
							<td>
								<?php echo $audit['end_date']; ?>
							</td>
						</tr>
					</table>
				</div>

				<div class="item">
					<table class="triple-column">
						<tr>
							<th>
								<?php echo __('Result'); ?>
							</th>
							<th>
								<?php echo __('Owner'); ?>
							</th>
							<th>
								<?php echo __('Status'); ?>
							</th>
						</tr>
						
						<tr>
							<td>
								<?php
								if ($audit['result'] !== null) {
									echo getAuditStatuses($audit['result']);
								}
								else {
									echo '-';
								}
								?>
							</td>
							<td>
								<?php
								if (!empty($audit['User'])) {
									echo $audit['User']['name'] . ' ' . $audit['User']['surname'];
								}
								else {
									echo '-';
								}
								?>
							</td>
							<td>
								<?php echo $this->SecurityServiceAudits->getStatuses($audit, 'GoalAudit'); ?>
							</td>
						</tr>
					</table>
				</div>

				<div class="item">
					<table>
						<tr>
							<th>
								<?php echo __('Conclusion'); ?>
							</th>
						</tr>
						
						<tr>
							<td>
								<?php
								echo $this->Eramba->getEmptyValue(nl2br($audit['result_description']));
								?>
							</td>
						</tr>
					</table>
				</div>

				<div class="separator"></div>
			<?php endforeach; ?>

		</div>
	</div>
</div>