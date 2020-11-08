<?php
App::uses('DashboardKpi', 'Dashboard.Model');
?>

<?php
if (isset($edit)) {
	echo $this->Form->create( 'DashboardKpi', array(
		'url' => array('plugin' => 'dashboard', 'controller' => 'dashboardKpis', 'action' => 'edit'),
		'novalidate' => true,
		'data-yjs-form' => $formName
	) );

	echo $this->Form->input( 'id', array( 'type' => 'hidden' ) );
	$submit_label = __( 'Edit' );
}
else {
	echo $this->Form->create( 'DashboardKpi', array(
		'url' => array('plugin' => 'dashboard', 'controller' => 'dashboardKpis', 'action' => 'add', $dashboardKpiType),
		'novalidate' => true,
		'data-yjs-form' => $formName
	) );

	$submit_label = __( 'Add' );
}
?>

<div class="tabbable">
	<ul class="nav nav-tabs nav-tabs-top top-divided">
		<li class="active"><a href="#tab_general" data-toggle="tab"><?php echo __('General'); ?></a></li>
		<li><a href="#tab_thresholds" data-toggle="tab"><?php echo __('Thresholds'); ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="tab_general">
			<?php
			$titleOptions = [];
			if ($dashboardKpiCategory != DashboardKpi::CATEGORY_OWNER) {
				$titleOptions = [
					'readonly' => true
				];
			}
			echo $this->FieldData->inputs([
				$FieldDataCollection->title
			], $titleOptions);

			echo $this->Form->input('DashboardKpi.type', [
				'type' => 'hidden',
				'value' => $dashboardKpiType
			]);

			echo $this->Form->input('DashboardKpi.category', [
				'type' => 'hidden',
				'value' => $dashboardKpiCategory
			]);

			if ($dashboardKpiCategory == DashboardKpi::CATEGORY_OWNER) {
				echo $this->FieldData->input([$Attribute->foreign_key, 0]);

				echo $this->FieldData->input([$Attribute->model, 0], [
					'type' => 'hidden',
					'value' => 'AdvancedFilter'
				]);
			}

			if ($this->Form->isFieldError('attributes')) {
				echo $this->Html->div('form-group form-group-first', $this->Html->div('col-md-10 col-md-offset-2', $this->Form->error('attributes')));
			}
			?>
		</div>
		<div class="tab-pane fade in" id="tab_thresholds">
			<?=
			$this->element('Dashboard.thresholds');
			?>
		</div>
	</div>
</div>

<?php echo $this->Form->end(); ?>