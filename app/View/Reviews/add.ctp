<?php
echo $this->Html->script('tinymce/tinymce.min', array('inline' => true));

$modelLabel = ClassRegistry::init($relatedModel)->label(['singular' => true]);
$isRiskReview = in_array($reviewModel, ['RiskReview', 'ThirdPartyRiskReview', 'BusinessContinuityReview', 'AssetReview']);
$updatesTab = __('%s Updates', $modelLabel);
$updatesHelper = __('Update the %s details on the next tab.', $modelLabel);
?>

<div class="row">
	<div class="col-lg-7">
		<div class="widget box widget-form">
			<div class="widget-header">
				<h4>&nbsp;</h4>
			</div>
			<div class="widget-content">

				<?php
					if (isset($edit)) {
						echo $this->Form->create( $reviewModel, array(
							'url' => array( 'controller' => 'reviews', 'action' => 'edit', $id ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						) );

						echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
						echo $this->Form->input( 'foreign_key', array( 'type' => 'hidden' ) );
						echo $this->Form->input( 'model', array( 'type' => 'hidden' ) );
						$submit_label = __( 'Edit' );
					}
					else {
						echo $this->Form->create( $reviewModel, array(
							'url' => array( 'controller' => 'reviews', 'action' => 'add', $relatedModel, $foreign_key ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						) );

						$submit_label = __( 'Add' );
					}
				?>

				<div class="tabbable box-tabs box-tabs-styled">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>

						<?php if ($reviewModel == 'SecurityPolicyReview') : ?>
							<li class=""><a href="#tab_policy_updates" data-toggle="tab"><?php echo $updatesTab; ?></a></li>
						<?php endif; ?>

						<?php if ($isRiskReview) : ?>
							<li><a href="#tab_risk_updates" data-toggle="tab"><?php echo $updatesTab; ?></a></li>
						<?php endif; ?>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab_general">
							<div class="form-group">
								<label class="col-md-2 control-label"><?php echo __( 'Planned Date' ); ?>:</label>
								<div class="col-md-10">
									<?php
									echo $this->Form->input( 'planned_date', am([
										'type' => 'text',
										'label' => false,
										'div' => false,
										'class' => 'form-control datepicker',
									], $this->Reviews->disableFieldParams('planned_date')));

									if ($this->Form->isFieldError($reviewModel . '.planned_date')) {
										// echo $this->Form->error($reviewModel . '.planned_date');
									}
									?>
									<span class="help-block"><?php echo __( 'This is the date when the Risk was supposed to get a review. If this is an ad-hoc review (you clicked on "Add new") then this date will be empty and most likely should be completed with todays day.' ); ?></span>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-2 control-label"><?php echo __( 'Actual Date' ); ?>:</label>
								<div class="col-md-10">
									<?php echo $this->Form->input( 'actual_date', am([
										'type' => 'text',
										'label' => false,
										'div' => false,
										'class' => 'form-control datepicker',
									], $this->Reviews->disableFieldParams()));

									if ($this->Form->isFieldError($reviewModel . '.actual_date')) {
										// echo $this->Form->error($reviewModel . '.actual_date');
									}
									?>
									<span class="help-block"><?php echo __( 'This is the date when the Risk actually got updated. If this is an ad-hoc review (you clicked on "Add new") then this date will be empty and most likely should be completed with todays day.' ); ?></span>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-2 control-label"><?php echo __( 'Description' ); ?>:</label>
								<div class="col-md-10">
									<?php
									echo $this->Form->input( 'description', am([
										'type' => 'textarea',
										'label' => false,
										'div' => false,
										'class' => 'form-control',
									], $this->Reviews->disableFieldParams()));
									?>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-2 control-label"><?php echo __('Completed?'); ?>:</label>
								<div class="col-md-10">
									<label class="checkbox">
										<?php
										echo $this->Form->input('completed', array(
											'type' => 'checkbox',
											'label' => false,
											'div' => false,
											'class' => 'uniform'
										));
										?>
										<?php echo __('Yes'); ?>
									</label>
									<span class="help-block"><?php echo __( 'Unless you click on this checkbox the review will not be considered completed.<br><br>
										IMPORTANT: is very useful to keep reviews evidence, you may store them as attachment to this Review by using the right of this form (if you created an ad-hoc review you will need to first save this form and then attach using the "attachments" icon.)

									' ); ?></span>
								</div>
							</div>

							<?php if ($isRiskReview || $reviewModel == 'SecurityPolicyReview') : ?>
								<div class="form-group">
									<div class="col-md-12">
									<?php
									echo $this->Ux->getAlert($updatesHelper, array(
										'type' => 'info'
									));
									?>
									</div>
								</div>
							<?php endif; ?>
						</div>

						<?php
						$nextReviewOptions = array(
							'type' => 'text',
							'label' => false,
							'div' => false,
							'class' => 'form-control datepicker',
							'readonly' => $this->Reviews->isFieldDisabled()
						);

						if (!empty($futureReview)) {
							$nextReviewOptions['default'] = $futureReview[$reviewModel]['planned_date'];
						}
						?>
						<?php if ($isRiskReview) : ?>
							<div class="tab-pane fade in" id="tab_risk_updates">
								<div class="form-group">
									<label class="col-md-2 control-label"><?php echo __('Next Review Date'); ?>:</label>
									<div class="col-md-10">
										<?php
										echo $this->Form->input($relatedModel . '.review', $nextReviewOptions);
										?>
										<span class="help-block">
											<?php
											echo __('Enter the date for the next Risk review, remember that review dates can only be updated with "reviews", this means that if you edit the Risk the review date wont be a field you can edit.');
											?>
										</span>
										<?php if (!empty($futureReview)) : ?>
											<?php
											echo $this->Ux->getAlert(
												__('We found another review with a future planned date, we suggest you use that same date in order to avoid creating another review.', $futureReview[$reviewModel]['planned_date']),
												['type' => 'info']
											);
											?>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endif; ?>

						<?php if ($reviewModel == 'SecurityPolicyReview') : ?>
							<div class="tab-pane fade in" id="tab_policy_updates">
								
								<div class="form-group">
									<label class="col-md-2 control-label"><?php echo __('New Version'); ?>:</label>
									<div class="col-md-10">
										<?php
										if (!empty($edit)) {
											$versionVal = (!empty($this->request->data['SecurityPolicy']['version'])) ? $this->request->data['SecurityPolicy']['version'] : $this->request->data[$reviewModel]['version'];
										}
										else {
											$versionVal = (!empty($prevReview)) ? $prevReview['SecurityPolicyReview']['version'] : $mainItem['SecurityPolicy']['version'];
										}
										echo $this->Form->input('SecurityPolicy.version', array(
											'type' => 'text',
											'label' => false,
											'div' => false,
											'class' => 'form-control',
											'default' => $versionVal,
											'readonly' => $this->Reviews->isFieldDisabled()
											
										));

										if ($this->Form->isFieldError('SecurityPolicy.version')) {
											// echo $this->Form->error('SecurityPolicy.version');
										}
										?>
										<span class="help-block">
											<?php echo __( 'Enter the document new version.' ); ?>
										</span>
										<?php if (!empty($prevReview)) : ?>
											<?php
											echo $this->Ux->getAlert(__('The last version for this document is %s', $prevReview['SecurityPolicyReview']['version']), ['type' => 'info']);
											?>
										<?php endif; ?>
									</div>
								</div>

								<div class="form-group">
									<label class="col-md-2 control-label"><?php echo __('Next Review Date'); ?>:</label>
									<div class="col-md-10">
										<?php
										echo $this->Form->input('SecurityPolicy.next_review_date', $nextReviewOptions);
										?>
										<span class="help-block">
											<?php
											echo __( 'Enter the date in which this document should be reviewed again. Based on this date you enter here another row will be included on the system for review (you can later remove them if needed).' );
											?>
										</span>
										<?php if (!empty($futureReview)) : ?>
											<?php
											echo $this->Ux->getAlert(
												__('We found another review with a future planned date, we suggest you use that same date in order to avoid creating another review.', $futureReview['SecurityPolicyReview']['planned_date']),
												['type' => 'info']
											);
											?>
										<?php endif; ?>
									</div>
								</div>

								<?php if ($mainItem['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_URL) : ?>
									<div class="form-group">
										<label class="col-md-2 control-label"><?php echo __('URL'); ?>:</label>
										<div class="col-md-10">
											<?php
											echo $this->Form->input('SecurityPolicy.url', array(
												'type' => 'textarea',
												'label' => false,
												'div' => false,
												'class' => 'form-control',
												'default' => $mainItem['SecurityPolicy']['url'],
												'readonly' => $this->Reviews->isFieldDisabled()
											));

											if ($this->Form->isFieldError('SecurityPolicy.url')) {
												// echo $this->Form->error('SecurityPolicy.url');
											}
											?>
										</div>
									</div>
								<?php elseif ($mainItem['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_ATTACHMENTS) : ?>
									<div class="form-group">
										<div class="col-md-12">
										<?php
										echo $this->Ux->getAlert(__('Dont forget to upload the policy as an attachment to this review.'), array(
											'type' => 'info'
										));
										?>
										</div>
									</div>
								<?php elseif ($mainItem['SecurityPolicy']['use_attachments'] == SECURITY_POLICY_USE_CONTENT) : ?>
									<div class="form-group">
										<label class="col-md-2 control-label"><?php echo __('Document Content'); ?>:</label>
										<div class="col-md-10">
											<div class="tinymce-wrapper">
												<br />
												<?php
												echo $this->element('securityPolicies' . DS . 'policy_editor', array(
													'fieldName' => 'SecurityPolicy.description',
													'default' => $mainItem['SecurityPolicy']['description'],
													'disabled' => $this->Reviews->isFieldDisabled()
												));

												if ($this->Form->isFieldError('SecurityPolicy.description')) {
													// echo $this->Form->error('SecurityPolicy.description');
												}
												?>
												<span class="help-block"><?php echo __('Update the description of the Policy'); ?></span>
											</div>
										</div>
									</div>
								<?php endif; ?>
								
								<?php
								echo $this->Form->input('SecurityPolicy.id', array(
									'type' => 'hidden',
									'value' => $mainItem['SecurityPolicy']['id']
								));
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<div class="form-actions">
					<?php echo $this->Form->submit( $submit_label, array(
						'class' => 'btn btn-primary',
						'div' => false
					) ); ?>
					&nbsp;
					<?php
					echo $this->Html->link(__('Cancel'), '#', array(
						'data-dismiss' => 'modal',
						'class' => 'btn btn-inverse'
					));

					?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<?php
		echo $this->element('ajax-ui/sidebarWidget', array(
			'model' => $reviewModel,
			'id' => isset($edit) ? $this->data[$reviewModel]['id'] : null
		));
		?>
	</div>
</div>