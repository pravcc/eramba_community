<?php
App::uses('VisualisationSetting', 'Workflows.Model');
$VisualisationSetting = ClassRegistry::init('Visualisation.VisualisationSetting');
?>
<div class="row">
	<div class="col-md-12">
		<div class="widget box widget-form">
			<div class="widget-header">
				<h4>&nbsp;</h4>
			</div>
			<div class="widget-content">

				<?php
					if (isset($edit)) {
						echo $this->Form->create('VisualisationSetting', array(
							'url' => array( 'controller' => 'visualisationSettings', 'action' => 'edit', $model ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						));

						echo $this->Form->input( 'model', array( 'type' => 'hidden' ) );
						$submit_label = __( 'Edit' );
					}
					else {
						echo $this->Form->create( 'VisualisationSetting', array(
							'url' => array( 'controller' => 'visualisationSettings', 'action' => 'add', $model ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						) );

						$submit_label = __( 'Add' );
					}
				?>

				<div class="tabbable box-tabs box-tabs-styled">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab_general">
							<?= $this->FieldData->input($FieldDataCollection->status, [
								'id' => 'visualisation-setting-status'
							]);
							?>
							<?= $this->FieldData->input($FieldDataCollection->ExemptedUser, [
								'id' => 'visualisation-setting-users'
							]); ?>
							<script>
								$(document).ready(function()
								{
									$('#visualisation-setting-status').on('change', function()
									{
										var $visualisationSettingUsersField = $('#visualisation-setting-users');
										$visualisationSettingUsersField.select2();
										if($(this).is(':checked')) {
											$visualisationSettingUsersField.select2('readonly', false);
										} else {
											$visualisationSettingUsersField.select2('readonly', true);
										}
									}).trigger('change');
								});
							</script>
						</div>
					</div>
				</div>

				<div class="form-actions">
					<?php echo $this->Form->submit( $submit_label, array(
						'class' => 'btn btn-primary',
						'div' => false
					) ); ?>
					&nbsp;
					<?php
					echo $this->Ajax->cancelBtn('VisualisationSetting');
					?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(function($) {
		Eramba.Ajax.UI.modal.setSize('modal-lg');
	});
</script>