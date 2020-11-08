<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('PolicyException', array(
			'url' => array('controller' => 'policyExceptions', 'action' => 'index'),
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
				<label class="col-md-4 control-label"><?php echo __( 'Third Party' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'third_party_id', array(
						'options' => $third_parties,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All Third Parties' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Status' ); ?>:</label>
				<div class="col-md-8">
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
				<label class="col-md-4 control-label"><?php echo __( 'Expired' ); ?>:</label>
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
			<div class="form-group">
				<label class="col-md-4 control-label"><?php echo __( 'Requester' ); ?>:</label>
				<div class="col-md-8">
					<?php echo $this->Form->input( 'user_id', array(
						'options' => $users,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All Requesterers' )
					) ); ?>
				</div>
			</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>