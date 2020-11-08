<?php
$_Audits = $historyClass->getAudits();
?>

<?php if (empty($_Audits)): ?>
	<?= $this->Alerts->info(__('There is no history recorded for this object yet.')); ?>

	<?php
	return true;
	?>
<?php endif; ?>

<div class="timeline timeline-center">
	<div class="timeline-container">

		<?php $i = 0; foreach ($_Audits as $Audit) : ?>
			<?php
			$Event = $Audit->getEventClass();
			?>
			<div class="<?= $this->ObjectVersionAudit->getTimelineClass($Event, $i); ?>">
				<div class="timeline-icon">
					<?= $this->ObjectVersionAudit->getTimelineIcon($Event); ?>
				</div>

				<div class="panel timeline-content">

					<div class="panel-heading heading-divided mb-0" <?= !$Audit->hasAuditDelta(true) ? 'style="border-bottom: 0; margin-bottom: 0px;"' : ''; ?>>
						
						<h6 class="panel-title">
							<?= $Event->getLabel(); ?>

							<?php if ($Audit->getVersion()): ?>
								<small>(<?php echo __('version #%s', $Audit->getVersion()); ?>)</small>
							<?php endif; ?>
						</h6>
						<?php
						// we dont allow restoration of a last version
						if ($i != 0 || $Event->isDeleted()):
						?>
							<div class="heading-elements">
								<ul class="icons-list">
									<li class="dropdown">
										<a href="#" class="dropdown-toggle" data-toggle="dropdown">
											<i class="icon-circle-down2"></i>
										</a>

										<ul class="dropdown-menu dropdown-menu-right">
											<li>
												<?php
												$restoreUrl = Router::url([
													'action' => 'restore',
													$Audit->getId()
												]);

												echo $this->Html->link('<i class="icon-spinner11"></i> ' . __('Restore'), '#', [
													'data-yjs-request' => 'app/submitForm',
													'data-yjs-target' => 'modal',
													'data-yjs-modal-id' => $modal->getModalId(),
													'data-yjs-on-modal-success' => "close", 
													'data-yjs-datasource-url' => $restoreUrl,
													'data-yjs-event-on' => 'click',
													'data-yjs-on-success-reload' => "#main-toolbar|#main-content",
													'data-yjs-on-modal-close' => '@reload-parent',
													'class' => 'ajax-restore-link',
													'escape' => false
												]);
												?>
											</li>
										</ul>
									</li>
			                	</ul>
		                	</div>

						<?php endif; ?>

					</div>

					<?php if ($Audit->hasAuditDelta(true)): ?>
						<div class="panel-body pt-10 pb-10">

							<?php
							$_AuditDeltas = $Audit->getAuditDeltas(true);
							?>

							<div class="panel-group" id="accordion<?= $Audit->getId(); ?>">
								<div class="panel panel-white">
									<div class="panel-heading">
										<h6 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion<?= $Audit->getId(); ?>" href="#accordion-group<?= $Audit->getId(); ?>">
												<?= __('Changes'); ?>
												<span class="badge badge-danger pull-right"><?php echo count($_AuditDeltas); ?></span>
											</a>
										</h6>
									</div>
									<div id="accordion-group<?= $Audit->getId(); ?>" class="panel-collapse collapse">
										<div class="panel-body">
											<?php
											echo $this->Alerts->success(__('Changed items are in green.'));
											?>
											<?php foreach ($_AuditDeltas as $AuditDelta): ?>
												<?php if ($AuditDelta->isEmpty()) continue; ?>
												
												<div class="row">
													<div class="col-md-4 text-left">
														<?php echo $AuditDelta->getFieldLabel($historyClass->getModel()); ?>
													</div>
													<div class="col-md-8 text-right">
														<?php if (!$Event->isDeleted()/* && $AuditDelta->hasNewValue()*/): ?>
														<code class="alert-success audit-delta-change">
															<?php echo getEmptyValue($AuditDelta->getNewLabel()); ?>
														</code>
													<?php endif; ?>

													<?php if (!$Event->isCreated() && $AuditDelta->hasOldValue()): ?>
														<code class="alert-danger audit-delta-change">
															<?php echo getEmptyValue($AuditDelta->getOldLabel()); ?>
														</code>
													<?php endif; ?>
													</div>
												</div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<div class="panel-footer pl-20 pr-20">
						<?php if ($Event->isRestored()): ?>
							<span class="text-muted">
								<i class="icon-spinner11"></i>&nbsp;

								<?php
								if ($Audit->hasRestoredVersion()) {
									echo __('Restored from version #%s', $Audit->getRestoredVersion());
								}
								else {
									echo __('Restored from an unspecified revision');
								}

								?>
							</span>
						<?php endif; ?>

						<span class="text-muted">
							<i class="icon-watch2"></i>&nbsp;<?php echo $Audit->getTimeAgo(); ?>
						</span>
						<span class="text-muted ml-5">
							<i class="icon-user"></i>&nbsp;<?php echo $Audit->getDescription(); ?>
						</span>
					</div>

				</div>
			</div>
		<?php $i++; endforeach; ?>
	</div>
</div>