<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-content">
				<?php
				echo $this->Form->create('RiskCalculationWarning', array(
					'url' => array('controller' => 'riskCalculations', 'action' => 'warning'),
					'class' => 'form-horizontal row-border',
					'novalidate' => true,
					'data-yjs-form' => 'risk-calculation-warning-form'
				));
				?>

				<div><?= $warning ?></div>

				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>