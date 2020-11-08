<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('SecurityService', array(
			'url' => array('controller' => 'securityServices', 'action' => 'index'),
			'class' => 'filter-form form-horizontal'
		)); ?>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Name' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'search', array(
						'label' => false,
						'div' => false,
						'class' => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Owner' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'user_id', array(
						'options' => $owners,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All Owners' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Security Service Type' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'security_service_type_id', array(
						'options' => $securityServiceTypes,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All Security Service Types' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Last audit failed' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'audits_last_failed', array(
						'options' => getStatusFilterOption(),
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Last audit missing' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'audits_last_missing', array(
						'options' => getStatusFilterOption(),
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Last maintenance missing' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'maintenances_last_missing', array(
						'options' => getStatusFilterOption(),
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Ongoing Corrective Actions' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'ongoing_corrective_actions', array(
						'options' => getStatusFilterOption(),
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Control with Issues' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'control_with_issues', array(
						'options' => getStatusFilterOption(),
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Design' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'design', array(
						'options' => getStatusFilterOption(),
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>

		<?php echo $this->Form->end(); ?>
	</div>
</div>