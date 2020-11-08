<div class="panel panel-flat">
	<div class="panel-heading">
		<?= $this->AdvancedFilters->renderName($AdvancedFiltersObject); ?>
	</div>

	<?= $this->AdvancedFilters->renderDescription($AdvancedFiltersObject); ?>

	<div class="panel-body">
		<?php
		$data = $AdvancedFiltersObject->getData();
		echo $this->element('AdvancedFilters.light_filter', [
			'AdvancedFiltersObject' => $AdvancedFiltersObject,
			'data' => $data
		]);
		?>
	</div>
</div>