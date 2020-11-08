<div class="widget box widget-form">
	<div class="widget-header">&nbsp;</div>

	<div class="widget-content">
		<?php
		echo $this->Form->create('VisualisationShare', array(
			'url' => array(
				'plugin' => 'visualisation',
				'controller' => 'visualisation',
				'action' => 'share',
				$model, $foreign_key
			),
			'class' => 'form-horizontal row-border',
			'novalidate' => true
		));
		echo $this->Form->input( 'model', array( 'type' => 'hidden' ) );
		echo $this->Form->input( 'foreign_key', array( 'type' => 'hidden' ) );
		?>

		<div class="tabbable box-tabs box-tabs-styled">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane fade in active" id="tab_general">
					<div class="alert alert-info">
						<?php
						echo __('The user accounts you select in this menu will be able to see (and modify, etc) this object once you saved. Email notifications might take up to an hour to arrive.')
						?>
					</div>

					<?php
					echo $this->FieldData->input($FieldDataCollection->SharedUser, [
						// 'default' => $existsExtracted
					]);
					?>
				</div>
			</div>
			
		</div>

		<div class="form-actions">
			<?php
			echo $this->Form->submit(__('Share'), array(
				'class' => 'btn btn-primary',
				'div' => false
			));
			?>
			&nbsp;
			<?php
			echo $this->Ajax->cancelBtn('VisualisationShare');
			?>
		</div>

		<?php echo $this->Form->end(); ?>
	</div>
</div>