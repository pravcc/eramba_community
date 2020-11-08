<div class="row">
	<div class="col-md-12">
		<?php echo $this->Form->create('ThirdPartyRisk', array(
			'url' => array('controller' => 'thirdPartyRisks', 'action' => 'index'),
			'class' => 'filter-form form-horizontal'
		)); ?>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Search' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'third_party_search', array(
						'label' => false,
						'div' => false,
						'class' => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label"><?php echo __( 'Third Party' ); ?>:</label>
				<div class="col-md-10">
					<?php echo $this->Form->input( 'third_party_id', array(
						'options' => $tps,
						'label' => false,
						'div' => false,
						'class' => 'form-control',
						'empty' => __( 'All Third Parties' )
					) ); ?>
				</div>
			</div>
		<?php echo $this->Form->end(); ?>
	</div>
</div>