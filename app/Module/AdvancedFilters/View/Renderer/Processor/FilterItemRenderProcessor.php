<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('Inflector', 'Utility');

class FilterItemRenderProcessor extends RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		if (!$subject->field->isAssociated()) {
			return;
		}

		$items = (!$subject->item instanceof ItemDataCollection) ? [$subject->item] : $subject->item;

		foreach ($items as $item) {
			$assocItems = $this->_getAssocCollection($item->{$subject->field->getAssociationModel()});

			if (empty($assocItems)) {
				continue;
			}

			$newTab = $subject->view->Html->tag('span', '<i class="icon-new-tab2"></i>', [
				'class' => 'pull-right new-tab-link',
				'escape' => false,
			]);

			$ids = [];

			foreach ($assocItems as $assocItem) {
				$assoc = $subject->field->getAssociationConfig('association');

				if (($assocItem->Properties->enabled('Utils.SoftDelete') && $assocItem->isDeleted())
					|| empty($assocItem->getModel()->hasSectionIndex())
				) {
					continue;
				}

				$itemKey = $output->getKey($assocItem, $subject->field);

				$ids[] = $assocItem->id;

				$url = $subject->view->AdvancedFilters->filterUrl(
					$assocItem->getModel()->getMappedController(),
					['id' => $assocItem->id],
					['plugin' => Inflector::underscore($assocItem->getModel()->plugin)]
				);

				$output->itemAction([
					$itemKey => [
						'label' => __('Show') . $newTab,
						'url' => $url,
						'options' => [
							'escape' => false
						]
					]
				]);
			}

			if (count($ids) > 1) {
				$url = $subject->view->AdvancedFilters->filterUrl(
					$assocItems->getModel()->getMappedController(),
					['id' => $ids],
					['plugin' => Inflector::underscore($assocItems->getModel()->plugin)]
				);

				$output->cellAction([
					'label' => __('Show') . $newTab,
					'url' => $url,
					'options' => [
						'escape' => false
					]
				]);
			}
		}
	}

	protected function _getAssocCollection($item)
	{
		$data = $item;

		if ($data instanceof ItemDataEntity) {
			$Collection = $data->getModel()->getItemDataCollection();
			$Collection->add($data);
		}
		else {
			$Collection = $data;
		}

		return $Collection;
	}
}