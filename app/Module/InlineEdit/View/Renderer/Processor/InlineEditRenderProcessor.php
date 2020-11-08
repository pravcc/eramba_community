<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');

class InlineEditRenderProcessor extends RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		if (!$subject->field->isInlineEditable() || $subject->field->isType(FieldDataEntity::FIELD_TYPE_HIDDEN)) {
			return;
		}

		$uuid = (!empty($subject->uuid)) ? $subject->uuid : CakeText::uuid();

		$output->cellAction([
			'label' => __('Edit'),
			'url' => '#',
			'options' => [
				'escape' => false,
				// 'class' => 'inline-edit-trigger',
				'data-yjs-request' => 'inlineEdit/open/target::#popover-' . $uuid,
				'data-yjs-use-loader' => 'false',
				'data-yjs-event-on' => 'click',
			]
		]);

		$formContainer = $subject->view->Html->div('inline-edit-form-container', '&nbsp;', [
			'id' => $uuid,
			'data-yjs-request' => 'crud/showForm',
			'data-yjs-target' => "#{$uuid}",
			'data-yjs-datasource-url' => $this->getDataSource($subject, $uuid),
			'data-yjs-event-on' => 'init',
		]);

		$popover = $subject->view->Popovers->top('', $formContainer, null, [
			'id' => "popover-{$uuid}",
			'element' => 'div',
			'class' => 'inline-edit-popover',
			'trigger' => 'click',
		]);

		$output->cellTemplate($popover . OutputBuilder::CONTENT);
	}

	protected function getDataSource($subject, $uuid)
	{
		return Router::url(
			$subject->item->getModel()->getMappedRoute([
				'action' => 'edit',
				$subject->item->getPrimary(),
				$subject->field->getFieldName(),
				$uuid,
				'?' => [
					'inlineEdit' => true
				]
			])
		);
	}
}