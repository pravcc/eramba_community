<div class="tabbable box-tabs box-tabs-styled">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="tab_general">
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Planned Date' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'Review.planned_date', array(
						'type' => 'text',
						'label' => false,
						'div' => false,
						'class' => 'form-control datepicker',
						'readonly' => isset($edit)?'readonly':false
					) ); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Actual Date' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'Review.actual_date', array(
						'type' => 'text',
						'label' => false,
						'div' => false,
						'class' => 'form-control datepicker'
					) ); ?>
					<span class="help-block"><?php //echo __( '...' ); ?></span>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Description' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'Review.description', array(
						'type' => 'textarea',
						'label' => false,
						'div' => false,
						'class' => 'form-control'
					) ); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __('Completed'); ?>:</label>
				<div class="col-md-10">
					<label class="checkbox">
						<?php echo $this->Form->input('Review.completed', array(
							'type' => 'checkbox',
							'label' => false,
							'div' => false,
							'class' => 'uniform'
						)); ?>
						<?php echo __('Yes'); ?>
					</label>
					<span class="help-block"></span>
				</div>
			</div>
		</div>
	</div>
</div>