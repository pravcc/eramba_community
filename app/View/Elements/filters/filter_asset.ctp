<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('Asset', array(
			'url' => array('controller' => 'assets', 'action' => 'index'),
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
				<label class="col-md-3 control-label"><?php echo __( 'Owner' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'asset_owner_id', array(
						'options' => $bu_list,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All owners' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo __( 'Guardian' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'asset_guardian_id', array(
						'options' => $bu_list,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All guardians' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo __( 'User' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'asset_user_id', array(
						'options' => $bu_list_user,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All users' )
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo __( 'Missing Asset Review' ); ?>:</label>
				<div class="col-md-9">
					<?php echo $this->Form->input( 'expired_reviews', array(
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