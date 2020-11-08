<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Project', array(
			'url' => array('controller' => 'projects', 'action' => 'index'),
			'class' => 'filter-form form-horizontal'
		)); ?>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Search' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'search', array(
						'label' => false,
						'div' => false,
						'class' => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Status' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'project_status_id', array(
						'options' => $statuses,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All Statuses' ),
						// 'default' => PROJECT_STATUS_ONGOING
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
				<label class="col-md-4 control-label"><?php echo __( 'Over Budget' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'over_budget', array(
						'options' => getStatusFilterOption(),
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Expired Tasks' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'expired_tasks', array(
						'options' => getStatusFilterOption(),
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Expired Project' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'expired', array(
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