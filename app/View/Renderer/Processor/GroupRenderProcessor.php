<?php
App::uses('SectionRenderProcessor', 'View/Renderer/Processor');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class GroupRenderProcessor extends SectionRenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		parent::render($output, $subject);
	}

	public function accessList(OutputBuilder $output, $subject)
	{
		$groupId = $subject->item->getPrimary();
		$url = '/admin/acl/aros/ajax_role_permissions/' . $groupId;
		$link = $subject->view->Html->link(OutputBuilder::CONTENT, $url);

		$itemKey = $output->getKey($subject->item, $subject->field);
		$output->label([$itemKey => __('Configure Access List')]);
		$output->itemTemplate([$itemKey => $link]);
	}

}