<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('ComplianceException', array(
			'url' => array('controller' => 'complianceExceptions', 'action' => 'index'),
			'class' => 'filter-form form-horizontal'
		)); ?>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Search' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'search', array(
						'label' => false,
						'div' => false,
						'class' => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Status' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'status', array(
						'options' => $statuses,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Requester' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'author_id', array(
						'options' => $users,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All requesters' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Expired' ); ?>:</label>
				<div class="col-md-10">
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