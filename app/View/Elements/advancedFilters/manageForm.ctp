<?php 
// debug($activeFilter);
?>

<div class="panel-group" id="advanced-filters-manage" role="tablist" aria-multiselectable="true">
	
	<?php if (!empty($activeFilter) && $activeFilter['AdvancedFilter']['user_id'] == $logged['id']) : ?>
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="advanced-filters-heading-edit">
				<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#advanced-filters-manage" href="#advanced-filters-collapse-edit" aria-expanded="true" aria-controls="advanced-filters-collapse-edit">
						<strong><?php echo __('Edit active filter') . ': ' . $activeFilter['AdvancedFilter']['name']; ?></strong>
					</a>
				</h4>
			</div>
			<div id="advanced-filters-collapse-edit" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="advanced-filters-heading-edit">
				<div class="panel-body" id="">
					<?php echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . 'createForm', array('advancedFilterEdit' => true)); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="panel panel-default">
		<div class="panel-heading" role="tab" id="advanced-filters-heading-create">
			<h4 class="panel-title">
				<a role="button" data-toggle="collapse" data-parent="#advanced-filters-manage" href="#advanced-filters-collapse-create" aria-expanded="true" aria-controls="advanced-filters-collapse-create">
					<strong><?php echo __('Save current filter'); ?></strong>
				</a>
			</h4>
		</div>
		<div id="advanced-filters-collapse-create" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced-filters-heading-create">
			<div class="panel-body">
				<?php echo $this->element(ADVANCED_FILTERS_ELEMENT_PATH . 'createForm'); ?>
			</div>
		</div>
	</div>

</div>