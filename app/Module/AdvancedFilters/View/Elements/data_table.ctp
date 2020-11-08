<?php
App::uses('CakeText', 'Utility');
App::uses('Router', 'Routing');

$data = $AdvancedFiltersObject->getData();
$id = $AdvancedFiltersObject->getId();
$Model = $AdvancedFiltersObject->getModel();

if ($AdvancedFiltersObject->isDbFilter) {
	$manageUrl = Router::url([
		'plugin' => 'advanced_filters',
		'controller' => 'advancedFilters',
		'action' => 'edit',
		$id,
		'?' => array_merge($this->request->query, [
			'advanced_filter' => 1
		])
	]);
}
else {
	$manageUrl = Router::url([
		'plugin' => 'advanced_filters',
		'controller' => 'advancedFilters',
		'action' => 'add',
		$Model->alias,
		'?' => array_merge($this->request->query, [
			'advanced_filter' => 1
		])
	]);
}
?>
<div data-edit-url="<?= $manageUrl ?>">
	<table class="table datatable-custom datatable-scrollable" id="datatable-<?= $id; ?>">
		<thead>
			<tr>
				<?php if (isset($BulkActions) && $BulkActions->enabled()) : ?>
					<th class="bulk-action-checkbox-cell data-table-fixed" data-orderable="false" data-e-column-resizable="false">
						<div class="datatable-cell-content-wrapper bulk-action-checkbox-wrapper">
							<?php
							echo $this->Form->input('BulkAction.apply_all', array(
								'type' => 'checkbox',
								'label' => false,
								'div' => false,
								'class' => 'uniform bulk-action-checkbox bulk-action-check-all-checkbox'
							));
							?>
						</div>
					</th>
				<?php endif; ?>

				<th class="text-center data-table-fixed" data-orderable="false" data-e-column-resizable="false" width="90"><div class="datatable-cell-content-wrapper"><?= __('Actions'); ?></div></th>
				<?php if ((!isset($Trash) || !$Trash->isTrash()) && isset($ObjectStatus) && $ObjectStatus->isShowable()) : ?>
					<th class="data-table-fixed exportable-cell" data-e-column-slug="object_status"><div class="datatable-cell-content-wrapper"><?= $this->ObjectStatus->icon() . ' ' . __('Status') ?></div></th>
				<?php endif; ?>

				<?php
				// Setup fixed child section rows
				if ($Model->Behaviors->enabled('SubSection')) :
					$childModels = $Model->Behaviors->SubSection->getChildModels($Model);

					foreach ($childModels as $childModel) : ?>
						<th class="data-table-fixed text-center" data-orderable="false">
							<div class="datatable-cell-content-wrapper">
								<?= $Model->{$childModel}->label(); ?>
							</div>
						</th>
					<?php endforeach;
				endif;
				?>

				<?php if ($Model->alias == 'VendorAssessmentFeedback') : ?>
					
					<th class="data-table-fixed text-center" data-orderable="false">
						<div class="datatable-cell-content-wrapper">
							<?= __('Findings') ?>
						</div>
					</th>
				
				<?php endif; ?>


				<?php foreach ($AdvancedFiltersObject->getShowableFields() as $field => $FilterField) : ?>
					<?php
					//
					//
					// Start th actions
					$thActions = "";
					$thActions .= $this->Html->tag('button', false, [
						'class' => 'setting_btn sorting_btn'
					]);
					if ($FilterField->getFieldDataObject()->isType(FieldDataEntity::FIELD_TYPE_TEXTAREA) ||
						$FilterField->getFieldDataObject()->isType(FieldDataEntity::FIELD_TYPE_TEXT)) {
						$thActions .= $this->Html->tag('button', '<i class="icon-shrink"></i>', [
							'title' => __('Wrap text'),
							'class' => 'setting_btn wrapping_btn',
							'data-yjs-request' => 'index-filters/toggleColumnWrapping/columnSlug::' . $FilterField->getFieldName(),
							'data-yjs-event-on' => 'click',
							'data-yjs-use-loader' => 'false',
							'data-yjs-app-data-filter-object-id' => $AdvancedFiltersObject->getId()
						]);
					}
					// End th actions
					//
					
					$cellContentWrapper = $this->Html->tag('div', $thActions . ' ' . $FilterField->getLabel(), [
						'class' => 'datatable-cell-content-wrapper'
					]);
					echo $this->Html->tag(
						'th',
						$cellContentWrapper,
						[
							'class' => ['exportable-cell'],
							'data-e-column-slug' => $FilterField->getFieldName()
						]
					);
					?>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data as $Item) : ?>
				<?php
				$passParams = implode('/', $this->request->params['pass']);
				$uuid = CakeText::uuid();
				$query = array_merge([
					'advanced_filter' => true,
					'inlineReload' => $Item->getPrimary(),
				], $AdvancedFiltersObject->buildQueryParams());

				$rowUrl = Router::url(array_merge(['?' => $query], $this->request->params['pass']));

				$class = "item-row-{$Item->getModel()->alias}-{$Item->getPrimary()}";

				//CompliancePackageItem hot fix
				if ($Item->getModel()->alias == 'CompliancePackageItem') {
					$CompliancePackage = $Item->CompliancePackage;

					if (!empty($CompliancePackage)) {
						$class .= " item-row-{$CompliancePackage->getModel()->alias}-{$CompliancePackage->getPrimary()}";
					}
				}
				?>
				<tr
					id="item-row-<?= $uuid ?>"
					class="<?= $class ?>"
					data-yjs-request="index-filters/initRow"
					data-yjs-target="#item-row-<?= $uuid ?>"
					data-yjs-datasource-url="<?= $rowUrl ?>"
					data-yjs-event-on="false"
					data-yjs-use-loader="false"
					data-yjs-app-data-filter-object-id="<?= $AdvancedFiltersObject->getId(); ?>"
				>
					<?= $this->element('AdvancedFilters.data_table_row', [
						'Item' => $Item,
						'AdvancedFiltersObject' => $AdvancedFiltersObject
					]) ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
	$filterId = $AdvancedFiltersObject->getId();
	?>
	<div class="datatable-custom-elems">
		<div class="dataTables_length" id="datatable-<?= $filterId; ?>_length">
			<label>
				<span><?= __('Show'); ?>:</span>
				<?php
				$options = [
					10,
					// 20,
					25,
					50,
					100
				];
				?>
				<select 
					name="datatable-<?= $filterId; ?>_length" 
					aria-controls="datatable-<?= $filterId; ?>" 
					tabindex="-1"
					data-yjs-request="index-filters/setLimit/id::<?= $filterId; ?>"
					data-yjs-target="none"
					data-yjs-event-on="change"
					data-yjs-use-loader="false">
					<?php foreach ($options as $option) : ?>
						<?php
						$selected = null;
						if ($AdvancedFiltersObject->getLimit() == $option) {
							$selected = 'selected="selected"';
						}
						?>
						<option value="<?= $option; ?>" <?= $selected; ?>><?= $option; ?></option>
					<?php endforeach; ?>
					<!-- <option value="100">100</option> -->
				</select>
			</label>
		</div>

		<div class="datatable-custom-footer">
			<div class="dataTables_info" id="datatable-<?= $filterId; ?>_info" role="status" aria-live="polite">
				<?php
				if ($AdvancedFiltersObject->getCount() != 0) { 
					$pageCount = $AdvancedFiltersObject->getPageCount();
					$currentPage = $AdvancedFiltersObject->getCurrentPage();
					$limit = $AdvancedFiltersObject->getLimit();
					$count = $AdvancedFiltersObject->getCount();

					$from = ($limit * ($currentPage-1)) + 1;
					$to = $from + $limit - 1;
					if ($count < $to) {
						$to = $count;
					}
					echo __('Showing %s to %s of %s entries', $from, $to, $count);
				} else {
					echo __('Showing 0 entries');
				}
				?>
			</div>
				
			<?php if ($AdvancedFiltersObject->getPageCount() != 0) : ?>
				<div class="dataTables_paginate paging_simple_numbers" id="datatable-<?= $filterId; ?>_paginate">
					<?php
					$idx = 0;
					$class = 'paginate_button previous';
					if ($AdvancedFiltersObject->getCurrentPage() == 1) {
						$class .= ' disabled';
					}

					$prevPage = $AdvancedFiltersObject->getCurrentPage() - 1;
					if ($prevPage < 1) {
						$prevPage = 1;
					}
					?>
					<a
						class="<?= $class; ?>"
						aria-controls="datatable-<?= $filterId; ?>"
						data-dt-idx="<?= $idx; ?>"
						tabindex="0" id="datatable-<?= $filterId; ?>_previous"
						<?php if (!($AdvancedFiltersObject->getCurrentPage() == 1)) : ?>
							data-yjs-request="index-filters/setPage/page::<?= $prevPage; ?>/id::<?= $filterId; ?>"
							data-yjs-target="none"
							data-yjs-event-on="click"
							data-yjs-use-loader="false"
						<?php endif; ?>
						>←</a>

					<span>

						<?php
						$interval = 4;

						$fromPage = 1;
						if ($AdvancedFiltersObject->getCurrentPage() - 3 <= 1) {
							$fromPage = 1;
						} else {
							$fromPage = $AdvancedFiltersObject->getCurrentPage() - 3;
							$showFirst = true;
						}

						if ($AdvancedFiltersObject->getCurrentPage() + 3 >= $AdvancedFiltersObject->getPageCount()) {
							$toPage = $AdvancedFiltersObject->getPageCount();
						} else {
							$toPage = $AdvancedFiltersObject->getCurrentPage() + 3;
							$showLast = true;
						}
						?>

						<?php if (isset($showFirst)) : ?>
							<?php
							$idx++;

							echo $this->AdvancedFilterPagination->renderPageLink(
								$filterId,
								1,
								$idx, 
								false
							);
							?>
							<span class="ellipsis">…</span>
						<?php endif; ?>

						<?php for ($i = $fromPage; $i <= $toPage; $i++) : ?>
							<?php
							$idx++;

							$active = false;
							if ($AdvancedFiltersObject->getCurrentPage() == $i) {
								$active = true;
							}

							echo $this->AdvancedFilterPagination->renderPageLink($filterId, $i, $idx, $active);
							?>
						<?php endfor; ?>

						<?php if (isset($showLast)) : ?>
							<span class="ellipsis">…</span>
							<?php
							$idx++;

							echo $this->AdvancedFilterPagination->renderPageLink(
								$filterId,
								$AdvancedFiltersObject->getPageCount(),
								$idx, 
								false
							);
							?>
						<?php endif; ?>
					</span>

					<?php
					$class = 'paginate_button next';
					if ($AdvancedFiltersObject->getCurrentPage() == $AdvancedFiltersObject->getPageCount()) {
						$class .= ' disabled';
					}

					$nextPage = $AdvancedFiltersObject->getCurrentPage() + 1;
					if ($nextPage > $AdvancedFiltersObject->getPageCount()) {
						$nextPage = $AdvancedFiltersObject->getPageCount();
					}
					?>
					<a
						class="<?= $class; ?>"
						aria-controls="datatable-<?= $filterId; ?>"
						data-dt-idx="<?= $idx; ?>"
						tabindex="0"
						id="datatable-<?= $filterId; ?>_next"
						<?php if (!($AdvancedFiltersObject->getCurrentPage() == $AdvancedFiltersObject->getPageCount())) : ?>
							data-yjs-request="index-filters/setPage/page::<?= $nextPage; ?>/id::<?= $filterId; ?>"
							data-yjs-target="none"
							data-yjs-event-on="click"
							data-yjs-use-loader="false"
						<?php endif; ?>
						>→</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
