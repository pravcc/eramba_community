<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');

class SoftDeleteRenderProcessor extends RenderProcessor
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
				$assoc = $subject->field->getAssociationConfig('association');

				$itemKey = $output->getKey($assocItem, $subject->field);

				if ($assocItem->Properties->enabled('Utils.SoftDelete') && $assocItem->isDeleted()) {
					$output->label([$itemKey => $subject->view->Icons->render('cross3', ['class' => 'text-danger']) . OutputBuilder::CONTENT]);
				}
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