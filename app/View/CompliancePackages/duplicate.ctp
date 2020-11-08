<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">

				<?php
				echo $this->Ux->getAlert(__('With the exception of attachments, everything else will be copied to the new package (compliance packages, controls, policies, Etc).'), array(
					'type' => 'info'
				));

				echo $this->Form->create('ThirdParty', array(
					'url' => array('controller' => 'compliancePackages', 'action' => 'duplicate'),
					'class' => 'form-horizontal row-border',
					'novalidate' => true
				));
				
				$submit_label = __('Duplicate');
				?>

				<div class="form-group form-group-first">
					<label class="col-md-2 control-label"><?php echo __('Compliance Package'); ?>:</label>
					<div class="col-md-10">
						<?php
						echo $this->Form->input('compliance_package_regulator_id', array(
							'label' => false,
							'div' => false,
							'class' => 'select2 full-width-fix',
							'data-placeholder' => __('Choose one'),
							'empty' => array(''=>'')
						));
						?>
						<span class="help-block"><?php echo __('What is the Compliance Package you are duplicating.'); ?>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-2 control-label"><?php echo __('Name'); ?>:</label>
					<div class="col-md-10">
						<?php
						echo $this->Form->input('name', array(
							'label' => false,
							'div' => false,
							'class' => 'form-control'
						));
						?>
						<span class="help-block"><?php echo __('What will be the name of this new package.'); ?></span>
					</div>
				</div>

				<div class="form-actions">
					<?php echo $this->Form->submit($submit_label, array(
						'class' => 'btn btn-primary',
						'div' => false
					)); ?>
					&nbsp;
					<?php echo $this->Html->link(__('Cancel'), array(
						'action' => 'index'
					), array(
						'class' => 'btn btn-inverse'
					)); ?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
</div>
