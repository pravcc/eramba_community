<?php
App::uses('WorkflowSetting', 'Workflows.Model');
$WorkflowSetting = ClassRegistry::init('Workflows.WorkflowSetting');
?>
<div class="row">
	<div class="col-md-7">
		<div class="widget box widget-form">
			<div class="widget-header">
				<h4>&nbsp;</h4>
			</div>
			<div class="widget-content">

				<?php
					if (isset($edit)) {
						echo $this->Form->create('WorkflowSetting', array(
							'url' => array( 'controller' => 'workflowSettings', 'action' => 'edit', $id, $model ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						));

						echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
						echo $this->Form->input( 'model', array( 'type' => 'hidden' ) );
						$submit_label = __( 'Edit' );
					}
					else {
						echo $this->Form->create( 'WorkflowSetting', array(
							'url' => array( 'controller' => 'workflowSettings', 'action' => 'add', $model ),
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
							<?php
							echo $this->FieldData->inputs([
								$FieldDataCollection->status,
								$FieldDataCollection->name,
								$FieldDataCollection->OwnerUser,
								$FieldDataCollection->OwnerGroup
							]);
							?>
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
					echo $this->Ajax->cancelBtn('WorkflowSetting');
					?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<?php
		echo $this->element('ajax-ui/sidebarWidget', array(
			'model' => 'WorkflowSetting',
			'id' => isset($edit) ? $this->data['WorkflowSetting']['id'] : null
		));
		?>
	</div>
</div>
