<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">

				<?php
					echo $this->Form->create( 'Setting', array(
						'url' => array( 'controller' => 'settings', 'action' => 'resetDashboards'),
						'class' => 'form-horizontal row-border'
					) );
				?>

				<div class="form-group  form-group-first">
					<label class="col-md-2 control-label"><?php echo __( 'From beginning of time' ); ?>:</label>
					<div class="col-md-10">
						<?php $input = $this->Form->input('from_beginning', array(
								'type' => 'checkbox',
								'label' => false,
								'div' => false,
								'class' => 'uniform',
								'hiddenField' => false
							));
							echo $this->Html->tag('label', $input, array(
								'escape' => false,
								'class' => 'checkbox'
							));
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label"><?php echo __( 'Reset Dashboard from' ); ?>:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input( 'from', array(
							'type' => 'text',
							'label' => false,
							'div' => false,
							'class' => 'form-control datepicker'
						) ); ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-2 control-label"><?php echo __( 'Reset Dashboard to' ); ?>:</label>
					<div class="col-md-10">
						<?php echo $this->Form->input( 'to', array(
							'type' => 'text',
							'label' => false,
							'div' => false,
							'class' => 'form-control datepicker'
						) ); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-2 control-label">&nbsp;</label>
					<div class="col-md-10">
						<span class="help-block"><?php echo __( 'Choose which items to reset from the list below' ); ?></span>
					</div>
				</div>
				<?php foreach ($allowedResets as $key => $value): ?>
					<div class="form-group">
						<label class="col-md-2 control-label"><?php echo $value; ?>:</label>
						<div class="col-md-10">
							<?php $input = $this->Form->input('Model.'.$key, array(
									'type' => 'checkbox',
									'label' => false,
									'div' => false,
									'class' => 'uniform',
									'hiddenField' => false
								));
								echo $this->Html->tag('label', $input, array(
									'escape' => false,
									'class' => 'checkbox'
								));
							?>
						</div>
					</div>
				<?php endforeach; ?>

				<div class="form-actions">
					<?php echo $this->Form->submit( __('Reset'), array(
						'class' => 'btn btn-primary',
						'div' => false
					) ); ?>
					&nbsp;
					<?php echo $this->Html->link( __( 'Cancel' ), array(
						'action' => 'index'
					), array(
						'class' => 'btn btn-inverse'
					) ); ?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(function($) {
		function setFromField(){
			if ($("#SettingFromBeginning").prop('checked')) {
				$("#SettingFrom").attr('disabled', true);
				$("#SettingFrom").val('');
			}
			else{
				$("#SettingFrom").attr('disabled', false);
			}
		}

		setFromField();
		$("#SettingFromBeginning").on('click', function(){
			setFromField();
		});
	});
</script>