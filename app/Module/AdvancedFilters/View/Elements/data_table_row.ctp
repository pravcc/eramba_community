<?php
App::uses('Review', 'Model');
App::uses('ReportTemplate', 'Reports.Model');
App::uses('ObjectStatusView', 'ObjectStatus.Controller/Crud/View');
App::uses('VendorAssessment', 'VendorAssessments.Model');
App::uses('Translation', 'Translations.Model');

$id = $Item->getPrimary();
$Model = $Item->getModel();
?>
<?php if (isset($BulkActions) && $BulkActions->enabled()) : ?>
	<td class="bulk-action-checkbox-cell">
		<div class="datatable-cell-content-wrapper bulk-action-checkbox-wrapper">
			<?php
			$options = [
				'type' => 'checkbox',
				'label' => false,
				'div' => false,
				'class' => 'uniform bulk-action-checkbox',
				'value' => $id,
				'hiddenField' => false
			];

			// lets disable the bulk action checkbox for admin user to forbid deletion
			if ($Model->alias == 'User' && $Item->id == ADMIN_ID) {
				$options['disabled'] = true;
			}

			echo $this->Form->input('BulkAction.apply_id][', $options);
			?>
		</div>
	</td>
<?php endif; ?>
<td class="text-center">
	<div class="datatable-cell-content-wrapper">
		<ul class="icons-list">
			<li class="dropdown">
				<?php
				$this->ItemDropdown->reset();

				$subject = new stdClass();
				$subject->view = $this;

				$itemDropdown = $this->ItemDropdown->render($Item, $subject);
				?>
				<a href="#" class="dropdown-toggle <?= ($itemDropdown === false) ? 'disabled' : '' ?>" data-toggle="dropdown">
					<i class="icon-menu7"></i>
					<?php
					$widgetUrl = [
	                    'plugin' => 'widget',
	                    'controller' => 'widget',
	                    'action' => 'index',
	                ];
					if ($this->AclCheck->check($widgetUrl)) {
						$menuNotificationsCount = 0;
						if ($Item->Properties->enabled('Widget.Widget')) {
							$menuNotificationsCount += $Item->unseenCommentsCount();
							$menuNotificationsCount += $Item->unseenAttachmentsCount();
						}
						if ($menuNotificationsCount > 0) {
							echo $this->Html->tag('span', $menuNotificationsCount, [
								'class' => ['badge', 'bg-warning-400', 'badge-top-right']
							]);
						}
					}
					?>
				</a>
				<?= $itemDropdown ?>
			</li>
		</ul>
	</div>
</td>
<?php if ((!isset($Trash) || !$Trash->isTrash()) && isset($ObjectStatus) && $ObjectStatus->isShowable()) : ?>
	<?php
	$content = $this->ObjectRenderer->render('AdvancedFilters.Cell', ['item' => $Item], [
		'ObjectStatus.ObjectStatus'
	]);
	$plainContent = $this->ObjectRenderer->render('AdvancedFilters.Cell', ['item' => $Item], [
		'ObjectStatus.ObjectStatus' => [
			'clean' => true,
			'disableCallbacks' => true
		]
	]);
	$cellContentWrapper = $this->Html->tag('div', $content, [
		'class' => 'datatable-cell-content-wrapper'
	]);
	echo $this->Html->tag('td', $cellContentWrapper, [
			'class' => ['field-cell', 'exportable-cell'],
			'data-order' => $plainContent,
			'data-search' => $plainContent,
			'data-e-column-slug' => 'object_status'
		]);
	?>
<?php endif; ?>
<?php
// Setup fixed child section rows
if ($Model->Behaviors->enabled('SubSection')) {
    $childModels = $Model->Behaviors->SubSection->getChildModels($Model);

    foreach ($childModels as $childModel) {
        $content = $this->ObjectRenderer->render('AdvancedFilters.Cell', [
            'item' => $Item,
            'childModel' => $childModel
        ], [
            'SubSection'
        ]);
        $cellContentWrapper = $this->Html->tag('div', $content, [
        	'class' => 'datatable-cell-content-wrapper'
        ]);
        echo $this->Html->tag('td', $cellContentWrapper, [
            'class' => ['field-cell', 'exportable-cell', 'text-center'],
            // 'data-order' => $plainContent,
            // 'data-search' => $plainContent,
        ]);
    }
}
?>
<?php
// Setup fixed child section rows for VendorAssessmentFeedback
if ($Model->alias == 'VendorAssessmentFeedback') {
	$cellContentWrapper = $this->Html->tag('div', $this->VendorAssessmentFeedbacks->findingsSubSectionCell($Item), [
    	'class' => 'datatable-cell-content-wrapper'
    ]);

    echo $this->Html->tag('td', $cellContentWrapper, [
        'class' => ['field-cell', 'exportable-cell', 'text-center'],
    ]);
}
?>
<?php foreach ($AdvancedFiltersObject->getShowableFields() as $field => $FilterField) : ?>
	<?= $this->AdvancedFilterRenderer->renderCell($Item, $FilterField); ?>
<?php endforeach; ?>