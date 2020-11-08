<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('SecurityPolicy', array(
			'url' => array('controller' => 'securityPolicies', 'action' => 'index'),
			'class' => 'filter-form form-horizontal'
		)); ?>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo __( 'Search' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'search', array(
						'label' => false,
						'div' => false,
						'class' => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo __( 'Status' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'status', array(
						'options' => $statuses,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' ),
						// 'default' => 1
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo __( 'Document Type' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'document_type', array(
						'options' => $documentTypes,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo __( 'Author' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'author_id', array(
						'options' => $users,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo __( 'Collaborator' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'collaborator_id', array(
						'options' => $users,
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