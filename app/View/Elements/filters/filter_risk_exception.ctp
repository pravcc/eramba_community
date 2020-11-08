<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('RiskException', array(
			'url' => array('controller' => 'riskExceptions', 'action' => 'index'),
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
		<?php echo $this->Form->end(); ?>
	</div>
</div>