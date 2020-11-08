<?php
App::uses('AppHelper', 'View/Helper');
App::uses('CakeText', 'Utility');

class TabsComponentHelper extends AppHelper
{
	public $settings = [];
	public $helpers = ['Form', 'Html'];

	protected $_hash = '';
	protected $_active = null;

	public function render($options = [])
	{
		$options = array_merge([
			'nav' => [],
			'content' => [],
			'active' => null
		], $options);

		$this->_generateHash();
		$this->_active = $options['active'];

		$nav = $this->_nav($options['nav']);

		$content = $this->Html->div('tab-content', $this->_content($options['content']));

		return $this->Html->div('tabbable', $nav . $content);
	}

	protected function _nav($nav)
	{
		$this->navIndex = 0;
		
		$result = "";
		foreach ($nav as $navItemKey => $navItemVal) {
			$navItemLabel = "";
			$navItemOptions = [];
			if (is_string($navItemVal)) {
				$navItemLabel .= $navItemVal;
			} else {
				$navItemLabel .= $navItemVal['label'];
				$navItemOptions = array_merge($navItemOptions, $navItemVal['navItemOptions']);
			}

			$navItemContent = $this->Html->link($navItemLabel, '#' . $this->_getHash() . '-content-tab-' . $navItemKey, [
				'data-tab-btn-name' => $navItemKey,
				'data-toggle' => 'tab',
				'escape' => false
			]);

			if ($this->_isActive($navItemKey)){
				if (!isset($navItemOptions['class'])) {
					$navItemOptions['class'] = '';
				}

				$navItemOptions['class'] .= ' active';
			}

			$result .= $this->Html->tag('li', $navItemContent, $navItemOptions);
		}
		
		return $this->Html->tag('ul', $result, [
			'class' => 'nav nav-tabs nav-tabs-top top-divided'
		]);
	}

	protected function _content($content)
	{
		$ret = null;

		$i = 0;
		foreach ($content as $key => $item) {
			if (is_callable($item)) {
				$out = call_user_func_array($item, []);
			}
			else {
				$out = $item;
			}

			$options = [
				'id' => $this->_getHash() . '-content-tab-' . $key,
				'data-tab-content-name' => $key
			];

			$class = 'tab-pane';
			if ($this->_isActive($key)){
				$class .= ' active';
			}

			$ret .= $this->Html->div($class, $out, $options);

			$i++;
		}

		return $ret;
	}

	protected function _isActive($key)
	{
		if ($this->_active !== null && $this->_active == $key) {
			return true;
		}

		return false;
	}

	protected function _generateHash()
	{
		$this->_hash = CakeText::uuid();
	}

	protected function _getHash()
	{
		return $this->_hash;
	}
}