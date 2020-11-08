<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('FieldDataEntity', 'FieldData.Model/FieldData');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('ItemDataCollection', 'FieldData.Model/FieldData');
App::uses('FieldsIterator', 'ItemData.Lib');

class DefaultRenderProcessor extends RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);

		foreach ($FieldsIterator as $key => $value) {
			$label = $value['nice'];
			$template = OutputBuilder::CONTENT;
			$wrapBox = true;

			// toggle switch with custom on/off options
			if ($subject->field->isType(FieldDataEntity::FIELD_TYPE_TOGGLE) && $subject->field->hasOptionsCallable()) {
				$template = $subject->view->Labels->default(OutputBuilder::CONTENT);
				$wrapBox = false;
			}
			elseif ($subject->field->isType(FieldDataEntity::FIELD_TYPE_DATE)
				|| ($subject->field->isType(FieldDataEntity::FIELD_TYPE_TOGGLE) && !$value['raw'])
				|| ($subject->field->isType(FieldDataEntity::FIELD_TYPE_OBJECT_STATUS) && !$value['raw'])
			) {
				$template = $subject->view->Labels->default(OutputBuilder::CONTENT);
				$wrapBox = false;
			}
			elseif ($subject->field->isType(FieldDataEntity::FIELD_TYPE_TOGGLE) && $value['raw']
				|| ($subject->field->isType(FieldDataEntity::FIELD_TYPE_OBJECT_STATUS) && $value['raw'])
			) {
				$template = $subject->view->Labels->success(OutputBuilder::CONTENT);
				$wrapBox = false;
			}
			elseif ($subject->field->isType(FieldDataEntity::FIELD_TYPE_TEXTAREA)
				|| $subject->field->isType(FieldDataEntity::FIELD_TYPE_TEXT)
			) {
				$truncateLength = 50;

				// if (empty($subject->disableTextTruncate) && $truncateLength <= strlen($label)) {
					$template = $subject->view->Popovers->top(
						OutputBuilder::CONTENT,
						$subject->view->Html->div('content-break-words', $label)
					);

					// $label = $subject->view->Content->truncate($label, $truncateLength, [
					// 	'popover' => false
					// ]);
				// }
				
				$output->itemTemplate([$key => $subject->view->Html->tag('span', OutputBuilder::CONTENT, ['class' => 'text-content-wrapper', 'escape' => false])]);
			}

			$output->label([$key => $label]);
			$output->itemTemplate([$key => $template]);

			if (($subject->field->isAssociated() || $subject->item instanceof ItemDataCollection) && $wrapBox) {
				$output->itemTemplate([
					$key => $subject->view->Html->tag('span', OutputBuilder::CONTENT, [
						'class' => 'content-box'
					])
				]);
			}
		}
	}
}