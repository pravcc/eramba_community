<?php
App::uses('RiskAppetite', 'Model');
?>

<?php
	echo $this->Form->create( 'RiskAppetite', array(
		'url' => array( 'controller' => 'riskAppetites', 'action' => 'edit', 1 ),
		'class' => 'form-horizontal row-border',
		'novalidate' => true,
		'data-yjs-form' => $formName
	) );

	echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
?>

<div class="tabbable">
	<ul class="nav nav-tabs nav-tabs-top top-divided">
		<?php $i=0; foreach ($methods as $slug => $method) : ?>
			<?php
			$class = '';
			if ((!empty($this->data['RiskAppetite']['method']) && $this->data['RiskAppetite']['method'] == $slug) || (empty($this->data['RiskAppetite']['method']) && $i == 0)) {
				$activeSlug = $slug;
				$class = 'active';
			}
			?>

			<li class="<?php echo $class; ?>">
				<a href="#tab_<?php echo $slug; ?>" data-toggle="tab"><?php echo $method; ?></a>
			</li>
		<?php $i++; endforeach; ?>
	</ul>
	<div class="tab-content">
		<?php $i=0; foreach ($methods as $slug => $method) : ?>
			<div class="tab-pane fade in <?php if ($activeSlug == $slug) echo 'active'; ?>" id="tab_<?php echo $slug; ?>">

				<div class="row">
					<div class="col-xs-11">
						<dl class="dl-horizontal">
							<dt><?php echo __('Enable'); ?></dt>
							<dd>
								<?php
								$options = [
									'label' => false,
									'class' => ['risk-method-checkbox', 'uniform'],
									'value' => $slug,
									'checked' => ($slug == $activeSlug) ? true : false,
								];

								if ($slug == RiskAppetite::TYPE_THRESHOLD && $classificationTypeCount < 2) {
									$options['disabled'] = true;
								}

								echo $this->FieldData->input($FieldDataCollection->method, $options);	
								?>
							</dd>

							<dt><?php echo __('Appetite Method Name'); ?></dt>
							<dd><?php echo $method; ?></dd>

							<!-- <dt>&nbsp;</dt>
							<dd>&nbsp;</dd> -->

							<dt><?php echo __('Settings'); ?></dt>
							<dd>
								<?php if ($slug == RiskAppetite::TYPE_INTEGER) : ?>
									<?php
									echo $this->element('risk_appetites/integer_settings');
									?>
								<?php endif; ?>

								<?php if ($slug == RiskAppetite::TYPE_THRESHOLD) : ?>
									<?php
									echo $this->element('risk_appetites/threshold_settings');
									?>
								<?php endif; ?>
							</dd>
						</dl>
					</div>

				</div>
			</div>
		<?php $i++; endforeach; ?>
	</div>
</div>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
	jQuery(function($) {
		$(".risk-method-checkbox").on("change", function(e) {
			var $inputs = $(".risk-method-checkbox").not($(this));
			$inputs.prop("checked", false);
			$.uniform.update($inputs);
		});
	});
</script>