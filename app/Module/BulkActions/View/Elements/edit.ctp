<div class="row">
	<?php $i=0;foreach ($editableEntities as $fieldEntity) : ?>

		<div class="bulk-action-field-cell form-group col-md-3">
			<?php

				echo $this->Form->input('BulkAction.no_change.' . $fieldEntity->getFieldName(), array(
					'type' => 'checkbox',
					'label' => __('No Change'),
					'div' => array(
						'class' => 'input checkbox leave-unchanged-checkbox-wrapper'
					),
					'class' => 'uniform leave-unchanged-checkbox',
					'default' => true
				));

				echo $this->Html->div(
					'bulk-action-field-wrapper',
					$this->BulkActionFields->input($fieldEntity)
				);
			?>
		</div>

	<?php $i++;endforeach; ?>
</div>