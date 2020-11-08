<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('ObjectRenderer', 'ObjectRenderer.View/Renderer');
App::uses('Inflector', 'Utility');

class ObjectStatusRenderProcessor extends RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		$ObjectStatus = $subject->item->Properties->ObjectStatus;

		$data = $ObjectStatus->getStatusesData($subject->item);
		$config = $ObjectStatus->getStatusesConfig($subject->item);

		$Model = $subject->item->getModel();

		$ok = true;

		foreach ($data as $status) {
			$statusConfig = (isset($config[$status->name])) ? $config[$status->name] : null;

			if ($statusConfig === null || !$status->status || $statusConfig['hidden']) {
				continue;
			}

			$ok = false;

			$Field = $Model->getFieldDataEntity("ObjectStatus_{$status->name}");
			$itemKey = $output->getKey($subject->item, $Field);
			$type = $statusConfig['type'];

			$output->label([
				$itemKey => $statusConfig['title']
			]);
			if (empty($subject->clean)) {
				$output->itemTemplate([
					$itemKey => $subject->view->Labels->$type(OutputBuilder::CONTENT)
				]);
			}

			if (empty($subject->disableCallbacks)) {
				$processor = $subject->view->ObjectRenderer->getSectionProcessor($Model);

				if (!empty($processor)) {
					$Renderer = new ObjectRenderer($subject->view);
					$Renderer->runProcessors($output, [$processor], [
						'item' => $subject->item,
						'field' => $Field
					]);
				}
			}
		}

		if ($ok) {
			$output->label([__('OK')]);
			if (empty($subject->clean)) {
				$output->itemTemplate([$subject->view->Labels->success(OutputBuilder::CONTENT)]);
			}
		}

		if (!empty($subject->clean)) {
			$output->itemSeparator = ', ';
			$output->itemChunkSize = 1;
		}
	}

}