<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldsIterator', 'ItemData.Lib');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('Portal', 'Model');

class UserRenderProcessor extends SectionRenderProcessor
{
	public function systemLogs(OutputBuilder $output, $subject)
	{
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

        foreach ($FieldsIterator as $key => $value) {
        	$url = $subject->view->AdvancedFilters->filterUrl('userSystemLogs', [
				'user_id' => $subject->item->getPrimary(),
			]);

			$link = $subject->view->Html->link(OutputBuilder::CONTENT, $url);

			$output->label([
				$key => __('Audit Trails')
			]);
			$output->itemTemplate([
				$key => $link
			]);
		}
	}

	public function portal(OutputBuilder $output, $subject)
	{
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

        foreach ($FieldsIterator as $key => $value) {
			$output->label([
				$key => Portal::portals($value['raw'])
			]);
		}
	}
}
