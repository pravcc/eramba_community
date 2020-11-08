<?php
App::uses('OutputBuilder', 'ObjectRenderer.View/Renderer');
App::uses('UserFieldsBehavior', 'UserFields.Model/Behavior');

class CellOutput extends OutputBuilder
{
	public $itemSeparator = '';
	public $itemChunkSize = 3;

	protected $_preventDuplicitItems = false;

	protected $_settings = [
 		'item' => [
 			// [
 			// 	'label' => [
				// ],
 			// 	'template' => [
 			// 	],
 			// 	'actions' => [
 			// 		[
 			// 			'label' => '',
 			// 			'url' => '',
 			// 			'options' => [] 
 			// 		]
 			// 	],
 			// ]
 		],
 		'cell' => [
 			'template' => [],
 			'actions' => []
 		]
	];

	protected $_renderScope = ['text', 'template', 'actions'];

	public function preventDuplicitItems($prevent = true)
	{
		$this->_preventDuplicitItems = $prevent;
	}

	public function setRenderScope($scope)
	{
		$this->_renderScope = $scope;
	}

	public function render()
	{
		$items = [];

		foreach ($this->_settings['item'] as $key => $item) {
			$content = $this->_renderItem($item);

			if (!$this->_preventDuplicitItems || ($this->_preventDuplicitItems && !in_array($content, $items))) {
				$items[] = $content;
			}
		}

		return $this->_renderCell($this->_settings['cell'], $items);
	}

	protected function _renderItem($item)
	{
		$output = '';

		foreach ($item['label'] as $template) {
			$output = $this->fetchContent($template, $output);
		}

		if (!empty($item['actions']) && in_array('actions', $this->_renderScope)) {
			$output .= ' ' . $this->_renderItemActions($item['actions']);
		}

		if (!empty($item['template']) && in_array('template', $this->_renderScope)) {
			foreach ($item['template'] as $template) {
				$output = $this->fetchContent($template, $output);
			}
		}

		return $output;
	}

	protected function _renderCell($cell, $items)
	{
		$output = '';

		$chunks = array_chunk($items, $this->itemChunkSize);
		foreach ($chunks as $key => $chunk) {
			$chunks[$key] = implode($chunk);
		}

		$output = implode($this->itemSeparator, $chunks);

		if (in_array('actions', $this->_renderScope)) {
			$output .= $this->_renderCellActions($this->_settings['cell']['actions']);
		}

		if (!empty($cell['template']) && in_array('template', $this->_renderScope)) {
			foreach ($cell['template'] as $template) {
				$output = $this->fetchContent($template, $output);
			}
		}

		return $output;
	}

	protected function _renderItemActions($actions)
	{
		$output = '';

		if (empty($actions)) {
			return $output;
		}

		$list = [];

		foreach ($actions as $action) {
			$options = (!empty($action['options'])) ? $action['options'] : [];
			$list[] = $this->_View->Html->link($action['label'], $action['url'], $options);
		}

		$ul = $this->_View->Html->nestedList($list, [
			'class' => 'dropdown-menu dropdown-menu-xs'
		]);

		$caret = $this->_View->Html->tag('span', '', [
			'class' => 'caret',
		]);

		$btn = $this->_View->Html->tag('span', $caret, [
			'data-toggle' => 'dropdown',
			'aria-expanded' => false,
			'escape' => false,
		]);

		$output = $this->_View->Html->tag('span', $btn . $ul, [
			'escape' => false,
		]);

		return $output;
	}

	protected function _renderCellActions($actions)
	{
		$output = '';

		if (empty($actions)) {
			return $output;
		}

		$list = [];

		foreach ($actions as $action) {
			$options = (!empty($action['options'])) ? $action['options'] : [];
			$list[] = $this->_View->Html->link($action['label'], $action['url'], $options);
		}

		$ul = $this->_View->Html->nestedList($list, [
			'class' => 'dropdown-menu dropdown-menu-xs'
		]);

		$caret = $this->_View->Html->tag('span', '', [
			'class' => 'caret',
			'style' => 'color: #000000'
		]);

		$btn = $this->_View->Html->link($caret, '#', [
			'class' => 'label label-striped dropdown-toggle',
			'data-toggle' => 'dropdown',
			'aria-expanded' => false,
			'escape' => false,
			'style' => 'padding: 0 2px;'
		]);

		$output = $this->_View->Html->div('cell-action-dropdown', $this->_View->Html->div('btn-group', $btn . $ul));

		return $output;
	}

	public function label($label)
	{
		if ($label === null) {
			$label = '';
		}

		$label = (array) $label;

		foreach ($label as $key => $value) {
			$value = (string) $value;
			if (empty($this->_settings['item'][$key]['label']) || !in_array($value, $this->_settings['item'][$key]['label'])) {
				$this->_settings['item'][$key]['label'][] = $value;
			}
		}
	}

	public function itemTemplate($template)
	{
		$template = (array) $template;

		foreach ($template as $key => $value) {
			if (empty($this->_settings['item'][$key]['template']) || !in_array($value, $this->_settings['item'][$key]['template'])) {
				$this->_settings['item'][$key]['template'][] = $value;
			}
		}
	}

	public function cellTemplate($template)
	{
		$this->_settings['cell']['template'][] = $template;
	}

	public function cellAction($action)
	{
		$this->_settings['cell']['actions'][] = $action;
	}

	public function itemAction($action)
	{
		if (isset($action['url'])) {
			$action = [$action];
		}

		foreach ($action as $key => $value) {
			$labels = Hash::extract($this->_settings, 'item.' . $key . '.actions.{n}.label');

			if (empty($labels) || !in_array($value['label'], $labels)) {
				$this->_settings['item'][$key]['actions'][] = $value;
			}
		}
	}

	public static function getKey($Item, $Field, $sufix = '')
	{
		$id = (is_numeric($Item) || is_string($Item)) ? $Item : $Item->getPrimary();

		if (!empty($Field->config('UserField')) && $Item instanceof ItemDataEntity) {
			if (strpos($Item->getModel()->alias, 'Group') !== false) {
				$id = UserFieldsBehavior::getGroupIdPrefix() . $id;
			}
			else {
				$id = UserFieldsBehavior::getUserIdPrefix() . $id;
			}
		}

		return "{$id}_{$Field->getFieldName()}_{$sufix}";
	}
}