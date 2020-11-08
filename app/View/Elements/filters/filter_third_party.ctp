<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('ThirdParty', array(
			'url' => array('controller' => 'thirdParties', 'action' => 'index'),
			'class' => 'filter-form form-horizontal'
		)); ?>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Name' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'name', array(
						'label' => false,
						'div' => false,
						'class' => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Type' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'tp_type', array(
						'options' => $tp_types,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All types' )
					) ); ?>
				</div>
			</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>