<?php
App::uses('RiskCalculation', 'Model');
?>

<?php
	echo $this->Form->create( 'RiskCalculation', array(
		'url' => array( 'controller' => 'riskCalculations', 'action' => 'edit' ),
		'class' => 'form-horizontal row-border',
		'novalidate' => true,
		'data-yjs-form' => $formName
	) );

	echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
	echo $this->Form->input( 'model', array( 'type' => 'hidden' ) );
	$submit_label = __( 'Update' );
?>

<div class="tabbable">
	<ul class="nav nav-tabs nav-tabs-top top-divided">
		<?php $i=0; foreach ($methods as $slug => $method) : ?>
			<?php
			if (!in_array($slug, $availableMethods))
				continue;

			$class = '';
			if ((!empty($this->data['RiskCalculation']['method']) && $this->data['RiskCalculation']['method'] == $slug) || (empty($this->data['RiskCalculation']['method']) && $i == 0)) {
				$activeSlug = $slug;
				$class = 'active';
			}
			?>

			<li class="<?php echo $class; ?>">
				<a href="#tab_<?php echo $slug; ?>" data-toggle="tab"><?php echo $method['name']; ?></a>
			</li>
		<?php $i++; endforeach; ?>
	</ul>
	<div class="tab-content">
		<?php $i=0; foreach ($methods as $slug => $method) : ?>
			<?php if (!in_array($slug, $availableMethods)) continue; ?>
			<div class="tab-pane fade in <?php if ($activeSlug==$slug) echo 'active'; ?>" id="tab_<?php echo $slug; ?>">

				<div class="row">
					<div class="col-xs-11">
						<dl class="dl-horizontal">
							<dt><?php echo __('Enable'); ?></dt>
							<dd>
								<?php
								echo $this->FieldData->input($FieldDataCollection->method, array(
									'type' => 'checkbox',
									'label' => false,
									'class' => ['risk-method-checkbox', 'uniform'],
									'value' => $slug,
									'checked' => ($slug == $activeSlug) ? true : false,
									'hiddenField' => false
								));
								?>
							</dd>

							<dt><?php echo __('Calculation Method Name'); ?></dt>
							<dd><?php echo $method['name']; ?></dd>

							<dt><?php echo __('Description'); ?></dt>
							<dd><?php echo $method['description']; ?></dd>

							<!-- <dt>&nbsp;</dt>
							<dd>&nbsp;</dd> -->

							<dt><?php echo __('Settings'); ?></dt>
							<dd>
								<?php if ($slug == RiskCalculation::METHOD_MAGERIT) : ?>
									<?php
									echo $this->Form->hidden('RiskCalculationValue.magerit.0.field', array(
										'value' => 'impact'
									));
									echo $this->Form->input('RiskCalculationValue.magerit.0.value', array(
										'options' => $riskClassificationTypes,
										'label' => __('Select which classification will be used to determine the Impact'),
										'div' => false,
										'class' => 'form-control',
										'empty' => __('Choose one')
									));
									?>
									<br />
									<?php
									echo $this->Form->hidden('RiskCalculationValue.magerit.1.field', array(
										'value' => 'likelihood'
									));
									echo $this->Form->input('RiskCalculationValue.magerit.1.value', array(
										'options' => $riskClassificationTypes,
										'label' => __('Select which classification will be used to determine the Likelihood'),
										'div' => false,
										'class' => 'form-control',
										'empty' => __('Choose one')
									));
									?>
								<?php elseif (in_array($slug, ['eramba', 'erambaMultiply'])) : ?>
									<?php
									// debug($this->data['RiskCalculationValue']);
									$selected = array();
									if (!empty($this->data['RiskCalculationValue'][$slug])) {
										foreach ($this->data['RiskCalculationValue'][$slug] as $entry) {
											$selected[] = $entry['value'];
										}
									}

									// echo $this->FieldData->input($FieldDataCollection->RiskCalculationValue, [
									// 	'options' => $riskClassificationTypesNotEmpty,
									// 	'label' => false,
									// 	'inputName' => "RiskCalculationValue.{$slug}"
									// ]);

									echo $this->Form->input("RiskCalculationValue.{$slug}", array(
										'options' => $riskClassificationTypesNotEmpty,
										'label' => __('Select at least once Risk Classification criteria in order to calculate Risk Scores'),
										'div' => false,
										'class' => 'select2 col-md-12 full-width-fix select2-offscreen',
										'multiple' => true,
										'selected' => $selected
									));

									// echo $this->FieldData->input($FieldDataCollection->RiskCalculationValue, [
									// 	'options' => $riskClassificationTypesNotEmpty,
									// 	'label' => false,
									// 	'inputName' => "RiskCalculationValue.{$slug}"
									// ]);
									?>

									<?php /*$i=0; foreach ($riskClassificationTypes as $type) : ?>
										<div class="form-group">
											<?php
											echo $this->Form->hidden('RiskCalculationValue.eramba.'.$i.'.field', array(
												'value' => 'default'
											));
											echo $this->Form->input('RiskCalculationValue.eramba.'.$i.'.value', array(
												'options' => $riskClassificationTypesNotEmpty,
												'label' => __('Risk Classification Type') . ' #' . ($i+1),
												'div' => false,
												'class' => 'form-control',
												'empty' => __('Choose one')
											));
											?>
										</div>
									<?php
									$i++; endforeach;*/
									if ($this->Form->isFieldError('RiskCalculation.values')) {
										// hack to show nicely formatted error message
										$RiskCalculation = ClassRegistry::init('RiskCalculation');
										$ValuesField = new FieldDataEntity([
											'_field' => 'values'
										], $RiskCalculation);

										echo $this->FieldData->error($ValuesField);
									}
									?>
								<?php else : ?>
									<?php echo __('None'); ?>
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
		// Eramba.Ajax.UI.modal.setSize('modal-lg');

		$(".risk-method-checkbox").on("change", function(e) {
			var $inputs = $(".risk-method-checkbox").not($(this));
			$inputs.prop("checked", false);
			$.uniform.update($inputs);
		});
	});
</script>