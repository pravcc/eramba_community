<?php
App::uses('RenderProcessor', 'View/Renderer/Processor');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');

class ClassificationRenderProcessor extends RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		$field = $subject->field;
		$fieldName = $field->getFieldName();    

		$assocItems = $subject->item->{$fieldName};
		foreach ($assocItems as $assocItem) {
			$itemKey = $output->getKey($assocItem, $field);
			$value = $this->_classificationLabel($assocItem);

			$output->label([
				$itemKey => $value
			]);
		}
	}

	protected function _classificationLabel($subject)
	{
		$typeModel = $subject->getModel()->name . 'Type';

		$classification = $subject->name;
		$type = '';
		if (!empty($subject->{$typeModel})) {
			$type = $subject->{$typeModel}->name;
		}

		return ($type !== '') ? sprintf('%s (%s)', $type, $classification) : $classification;
	}
}