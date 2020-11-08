<?php
App::uses('ObjectStatusHelper', 'ObjectStatus.View/Helper');

// get model
$Model = $AdvancedFiltersObject->getModel();

// render active filters
$activeFilters = $this->ObjectRenderer->render('ObjectRenderer.Base', ['filterObject' => $AdvancedFiltersObject], [
	'AdvancedFilters.ActiveFilters'
]);

if (!empty($activeFilters)) {
	echo $this->Html->div('pt-5', $activeFilters);
}
?>
<table class="table report-filter">
	<thead>
		<tr>
			<?php if (ObjectStatusHelper::isShowable($Model)) : ?>
				<th>
					<?= $this->ObjectStatus->icon() . '&nbsp;' . __('Status') ?>
				</th>
			<?php endif; ?>
			<?php foreach ($AdvancedFiltersObject->getShowableFields() as $field => $FilterField) : ?>
				<?= $this->Html->tag(
					'th',
					$FilterField->getLabel()
				);
				?>
			<?php endforeach; ?>
		</tr>
	</thead>

	<?php if ($data !== null) : ?>
		<tbody>
			<?php foreach ($data as $Item) : ?>
				<tr>
					<?php if (ObjectStatusHelper::isShowable($Model)) : ?>
						<?php
						$content = $this->ObjectRenderer->render('AdvancedFilters.Cell', ['item' => $Item], [
							'ObjectStatus.ObjectStatus' => [
								'disableCallbacks' => true
							]
						]);
						echo $this->Html->tag('td', $content, [
							'class' => ['field-cell'],
						]);
						?>
					<?php endif; ?>
					<?php foreach ($AdvancedFiltersObject->getShowableFields() as $field => $FilterField) : ?>
						<?php
						$TraverserData = traverser($Item, $FilterField);

						$content = '';
						$searchContent = '';

						if (!empty($TraverserData['ItemDataEntity'])) {
							$processors = [
								'Default',
								'AdvancedFilters.FilterItem',
								'ObjectStatus.AssociatedObjectStatus',
								'Utils.SoftDelete',
								'CustomFields.CustomFields',
								'RiskScore',
								$this->ObjectRenderer->getSectionProcessor($TraverserData['ItemDataEntity']->getModel())
							];

							$params = [
								'item' => $TraverserData['ItemDataEntity'],
								'field' => $TraverserData['FieldDataEntity']
							];

							$output = $this->ObjectRenderer->getOutput('AdvancedFilters.Cell', $params, $processors);

							$output->setRenderScope(['text', 'template']);

							$content = $output->render();
						}
						
						echo $this->Html->tag('td', $content, [
							'class' => ['field-cell'],
						]);
						?>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	<?php endif; ?>
</table>