<?php
App::uses('RenderProcessor', 'ObjectRenderer.View/Renderer');
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('FieldsIterator', 'ItemData.Lib');

class SectionRenderProcessor extends RenderProcessor
{
	public function render(OutputBuilder $output, $subject)
	{
		$this->_dispatchFieldRender($output, $subject);
	}

	protected function _dispatchFieldRender(OutputBuilder $output, $subject)
	{
		if (!empty($subject->field)) {
			$fn = Inflector::variable($subject->field->getFieldName());

			if (method_exists($this, $fn)) {
				call_user_func([$this, $fn], $output, $subject);
			}
		}
	}

	protected function _statusFilter(OutputBuilder $output, $subject, $label, $url)
	{
		$value = $subject->item->Properties->FieldData->value($subject->item, $subject->field);

		if (!$value) {
			return;
		}

		$output->itemAction([
			$output->getKey($subject->item, $subject->field) => [
				'label' => $label,
				'url' => $url
			]
		]);
	}

	protected function _statusFilterAll(OutputBuilder $output, $subject, $label, $url)
	{
		$FieldsIterator = new FieldsIterator($subject->item, $subject->field);
        foreach ($FieldsIterator as $key => $value) {
			if (!$value['raw']) {
				return;
			}

			$output->itemAction([
				$key => [
					'label' => $label,
					'url' => (is_callable($url)) ? call_user_func($url, $subject, $value) : $url
				]
			]);
		}
	}
}