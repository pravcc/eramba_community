<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('BusinessContinuity', array(
			'url' => array('controller' => 'businessContinuities', 'action' => 'index'),
			'class' => 'filter-form form-horizontal'
		)); ?>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Search' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'business_unit_search', array(
						'label' => false,
						'div' => false,
						'class' => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Business Unit' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'business_unit_id', array(
						'options' => $bus,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All Business Units' )
					) ); ?>
				</div>
			</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>