<?php
echo $this->Ux->renderFlash();
?>
<div class="row">
	<div class="col-lg-12">

		<div class="widget box widget-form">
			<div class="widget-header">
				<h4>&nbsp;</h4>
			</div>
			<div class="widget-content">

				<?php
					echo $this->Form->create( 'Setting', array(
						'url' => array( 'controller' => 'settings', 'action' => 'residualRisk'),
						'class' => 'form-horizontal row-border',
						'novalidate' => true
					) );

					echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
					$submit_label = __( 'Update' );
				?>

				<div class="tabbable box-tabs box-tabs-styled">
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade in active" id="tab_general">
							<div class="row">
								<div class="col-xs-11">
									<dl class="dl-horizontal">
										<dt>
											<?php
											echo __('Granularity:');
											?>
										</dt>
										<dd>
										<?php
										echo __('When you create a risk and set a residual score you do that as a percentage of the total Risk score. This option allows you to set the scales uses for the percentage value, by default the value is 10.');
										?>
										</dd>

										<dt>&nbsp;</dt>
										<dd>&nbsp;</dd>

										<dt>&nbsp;</dt>
										<dd>
											<?php
											echo $this->Form->input('value', array(
												'options' => [
													1 => 1,
													2 => 2,
													5 => 5,
													10 => 10
												],
												'label' => false,
												'div' => false,
												'class' => 'select2 col-md-12 full-width-fix',
												'default' => 10
											));
											?>
										</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="form-actions">
					<?php echo $this->Form->submit( $submit_label, array(
						'class' => 'btn btn-primary',
						'div' => false
					) ); ?>
					&nbsp;
					<?php
					echo $this->Ajax->cancelBtn('Setting');
					?>
				</div>

				<?php echo $this->Form->end(); ?>

			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript">
	jQuery(function($) {
		Eramba.Ajax.UI.modal.setSize('modal-lg');
	});
</script>