<?php
App::uses('WorkflowStage', 'Workflows.Model');
$WorkflowStage = ClassRegistry::init('Workflows.WorkflowStage');
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
						echo $this->Form->create('WorkflowStage', array(
							'url' => array( 'controller' => 'workflowStages', 'action' => 'edit' ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						));

						echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
						echo $this->Form->input( 'model', array( 'type' => 'hidden' ) );
						$submit_label = __( 'Edit' );
					}
					else {
						echo $this->Form->create( 'WorkflowStage', array(
							'url' => array( 'controller' => 'workflowStages', 'action' => 'add', $model ),
							'class' => 'form-horizontal row-border',
							'novalidate' => true
						) );

						$submit_label = __( 'Add' );
					}
				?>

				<div class="tabbable box-tabs box-tabs-styled">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>
						<li><a href="#tab_approvals" data-toggle="tab"><?php echo __('Owners'); ?></a></li>
						<li><a href="#tab_visualisation" data-toggle="tab"><?php echo __('Visualisation TBD'); ?></a></li>
						<li><a href="#tab_manage_edit" data-toggle="tab"><?php echo __('Allow Modifications'); ?></a></li>
						<li><a href="#tab_manage_delete" data-toggle="tab"><?php echo __('Allow Deletions'); ?></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab_general">
							<?php
							echo $this->FieldData->inputs([
								$FieldDataCollection->name,
								$FieldDataCollection->description,
								$FieldDataCollection->stage_type
							]);
							?>
						</div>

						<div class="tab-pane fade in" id="tab_approvals">
							<?php
							echo $this->Ux->getAlert(__('This part refers to the people that must approve in order to enter this stage.'), array(
								'type' => 'info'
							));

							echo $this->FieldData->inputs([
								$FieldDataCollection->ApprovalUser,
								$FieldDataCollection->ApprovalGroup,
								// $FieldDataCollection->ApprovalCustom,
								$FieldDataCollection->approval_method
							]);
							?>

						</div>

						<div class="tab-pane fade in" id="tab_visualisation">
							<?php
							echo $this->Ux->getAlert(__('By default objects in the stage can not be seen or viewed. Choose who will be able to see them while on this stage.'), array(
								'type' => 'info'
							));

							echo $this->FieldData->inputs([
								$FieldDataCollection->ManageViewUser,
								$FieldDataCollection->ManageViewGroup
							]);
							?>
						</div>

						<div class="tab-pane fade in" id="tab_manage_edit">
							<?php
							echo $this->Ux->getAlert(__('By default objects in the stage can not be edited. Choose who will be able to do modifications while on this stage.'), array(
								'type' => 'info'
							));

							echo $this->FieldData->inputs([
								$FieldDataCollection->ManageEditUser,
								$FieldDataCollection->ManageEditGroup
							]);
							?>
						</div>

						<div class="tab-pane fade in" id="tab_manage_delete">
							<?php
							echo $this->Ux->getAlert(__('By default objects in the stage can not be deleted. Choose who will be able to do deletions while on this stage.'), array(
								'type' => 'info'
							));

							echo $this->FieldData->inputs([
								$FieldDataCollection->ManageDeleteUser,
								$FieldDataCollection->ManageDeleteGroup
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
					echo $this->Ajax->cancelBtn('WorkflowStage');
					?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
	<div class="col-lg-5">
		<?php
		echo $this->element('ajax-ui/sidebarWidget', array(
			'model' => 'WorkflowStage',
			'id' => isset($edit) ? $this->data['WorkflowStage']['id'] : null
		));
		?>
	</div>
</div>
