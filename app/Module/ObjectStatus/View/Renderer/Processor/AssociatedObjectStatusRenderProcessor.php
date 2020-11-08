<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('CellOutput', 'AdvancedFilters.View/Renderer/Output');
App::uses('ObjectStatusRenderProcessor', 'ObjectStatus.View/Renderer/Processor');

class AssociatedObjectStatusRenderProcessor extends RenderProcessor
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

			foreach ($assocItems as $assocItem) {
				//if there is no ObjectStatus return
				if (!$assocItem->getModel()->Behaviors->enabled('ObjectStatus.ObjectStatus')
					|| ($assocItem->Properties->enabled('Utils.SoftDelete') && $assocItem->isDeleted())
				) {
					continue;
				}

				$assoc = $subject->field->getAssociationConfig('association');

				$itemKey = $output->getKey($assocItem, $subject->field);

				$popoverContent = $subject->view->ObjectRenderer->render('AdvancedFilters.Cell', ['item' => $assocItem], [
					'ObjectStatus.ObjectStatus' => [
						'disableCallbacks' => true
					]
				]);

				$output->itemTemplate([
					$itemKey => $subject->view->Popovers->top(OutputBuilder::CONTENT, $popoverContent, $subject->view->ObjectStatus->icon() . ' ' . __('Status'), ['class' => 'assoc-object-status-popover'])
				]);

				$itemStatus = $assocItem->Properties->ObjectStatus->getItemStatus($assocItem);

				$output->label([//primitive-dot
					$itemKey => $subject->view->ObjectStatus->icon(['class' => "text-$itemStatus"]) . ' ' . OutputBuilder::CONTENT
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