<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Inflector', 'Utility');
App::uses('ItemObjectRenderer', 'ObjectRenderer.View/Renderer');
App::uses('ItemDataEntity', 'FieldData.Model/FieldData');
App::uses('ObjectRenderer', 'ObjectRenderer.View/Renderer');

class ObjectRendererHelper extends AppHelper
{
	public $helpers = [];

	public function render($output, $params, $processors)
	{
		return $this->getOutput($output, $params, $processors)->render();
	}

	public function getOutput($output, $params, $processors)
	{
		$Renderer = new ObjectRenderer($this->_View);

		$Renderer->setOutputClass($output);

		return $Renderer->getOutput($params, $processors);
	}

	public static function getSectionProcessor($Model)
	{
		list($plugin, $model) = pluginSplit($Model->modelFullName(), true);
		
		$processor = "{$plugin}{$model}";

		if (!ObjectRenderer::processorExists($processor)) {
			return false;
		}

		return $processor;
	}

	// public function create(ItemDataEntity $Item)
	// {
	// 	list($plugin, $model) = pluginSplit($Item->getModel()->modelFullName(), true);
	// 	$class = "{$model}Renderer";

	// 	$folder = Inflector::pluralize($model);

	// 	App::uses($class, "{$plugin}View/{$folder}/Renderer");

	// 	if (!class_exists($class)) {
	// 		$class = 'ItemObjectRenderer';
	// 	}

	// 	return new $class($this->_View, $Item);
	// }

	// public function renderField($Item, $Field, $options = [])
	// {
	// 	App::uses('FieldRenderer', 'FieldData.View/Renderer');

	// 	$Renderer = new FieldRenderer($this->_View);

	// 	if (!empty($options['inlineEdit'])) {
	// 		$Renderer->addProcessor('InlineEdit');
	// 	}

	// 	$Renderer->addProcessor('AdvancedFilters.FilterItem');
	// 	$Renderer->addProcessor('ObjectStatus.AssociatedObjectStatus');

	// 	return $Renderer->render([
	// 		'item' => $Item,
	// 		'field' => $Field
	// 	]);
	// }

	// public function renderObjectStatus($Item, $options = [])
	// {
	// 	// App::uses('ObjectRenderer', 'FieldData.View/Renderer');

	// 	$Renderer = new ObjectRenderer($this->_View);
	// 	$Renderer->setOutput('AdvancedFilters.Cell');
	// 	$Renderer->addProcessor('ObjectStatus.ObjectStatus');

	// 	$params = [
	// 		'item' => $Item
	// 	];

	// 	if (!empty($options['disableCallbacks'])) {
	// 		$params['disableCallbacks'] = true;
	// 	}

	// 	return $Renderer->render($params);
	// }
}