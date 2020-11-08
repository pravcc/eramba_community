<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Router', 'Routing');
App::uses('Inflector', 'Utility');
App::uses('Hash', 'Utility');
App::uses('CakeEvent', 'Event');

class LayoutToolbarHelper extends AppHelper
{
	public $helpers = ['Html', 'Icon', 'Form', 'AclCheck'];

	/**
	 * Toolbar items.
	 *
	 * @var array
	 */
	protected $_toolbarItems = [];

	public function __construct(View $View, $settings = array())
	{
		parent::__construct($View, $settings);
	}

	public function config($path)
	{
		return Hash::get($this->_toolbarItems, $path);
	}

	public function reset()
	{
		$this->_toolbarItems = [];
	}

	/**
	 * Adds a link to the toolbar array.
	 *
	 * ### Options
	 *
	 * - 'icon' Insert icon into the text.
	 * - 'notification' Insert notification text.
	 * - 'slug' Item slug.
	 * - 'parent' Parent item slug.
	 *
	 * @param string $name Link label.
	 * @param string $url Link href.
	 * @param array $options
	 * @param array $children Recursive definition of children, use same parameters.
	 * @see HtmlHelper::link() for details on $options that can be used.
	 */
	public function addItem($name, $url = null, $options = [], $children = [])
	{
		$item = $this->_buildItem(1, $name, $url, $options, $children);

		//find parent where we wanna put item
		$parent = $this->_getParent($this->_toolbarItems, $item->parent);

		if ($parent !== false) {
			$parent->children[$item->slug] = $item;
		}
		else {
			$this->_toolbarItems[$item->slug] = $item;
		}
	}

	protected function _getParent(&$data, $slug)
	{
		if ($slug === false) {
			return false;
		}

		foreach ($data as $key => $item) {
			if ($key == $slug) {
				return $item;
			}
			elseif (!empty($item->children)) {
				$parent = $this->_getParent($item->children, $slug);
				if ($parent !== false) {
					return $parent;
				}
			}
		}

		return false;
	}

	/**
	 * Builds internal item configuration.
	 *
	 * @param int $level Nesting level of item in deeper toolbar navigations.
	 * @param string $name Link label.
	 * @param string $url Link href.
	 * @param array $options
	 * @param array $children Recursive definition of children, use same parameters.
	 */
	protected function _buildItem($level, $name, $url = null, $options = [], $children = [])
	{
		$item = new stdClass();
		$item->name = $name;
		$item->slug = (isset($options['slug'])) ? $options['slug'] : strtolower(Inflector::slug($name));
		$item->parent = (isset($options['parent'])) ? $options['parent'] : false;
		$item->url = $url;
		$item->level = $level;
		$item->children = [];

		$parent = $this->_getParent($this->_toolbarItems, $item->parent);
		$level = (!empty($parent)) ? ($parent->level + 1) : $level;
		$item->level = $level;

		unset($options['slug']);
		unset($options['parent']);

		$item->liOptions = isset($options['li']) ? $options['li'] : [];
		unset($options['li']);

		$item->options = $options;

		foreach ($children as $child) {
			$params = array_merge([$level + 1], $child);
			$childItem = $this->_buildItem(...$params);
			$item->children[$childItem->slug] = $childItem;
		}

		return $item;
	}

	/**
	 * HTML render of item.
	 *
	 * @param array $item Item configuration.
	 * @return string Toolbar item/link.
	 */
	public function renderItem($item)
	{
		if (!empty($item->options['_content'])) {
			return $item->options['_content'];
		}

		$content = $item->name;

		$elemOptions = array_merge([
			'class' => [],
		], $item->liOptions);

		$linkOptions = array_merge([
			'escape' => false
		], $item->options);

		//icon
		if (!empty($item->options['icon'])) {
			$content = $this->Icon->icon($item->options['icon'], [
				'class' => ['position-left']
			]) . ' ' . $content;
		}

		//notification badge
		if (isset($item->options['notification'])) {
			$content .= $this->Html->tag('span', $item->options['notification'], [
				'class' => ['badge', 'badge-counter', 'position-right'],
			]);
		}

		//submenu
		$subnav = '';
		if (!empty($item->children)) {
			$linkOptions['data-toggle'] = 'dropdown';
			$linkOptions['class'][] = 'dropdown-toggle';

			if ($item->level <= 1) {
				$elemOptions['class'][] = 'dropdown';
			}
			else {
				$elemOptions['class'][] = 'dropdown-submenu dropdown-submenu-left';
			}
			
			$subnav = $this->renderList($item->children, [
				'class' => ['dropdown-menu', 'dropdown-menu-right']
			]);
		}

		unset($linkOptions['icon']);
		unset($linkOptions['notification']);

		if ($item->url === false) {
			$item->url = '#';
			$linkOptions['class'][] = 'cursor-not-allowed';
		}

		//link
		if (!empty($linkOptions['post'])) {
			$link = $this->Form->postLink($content, $item->url, $linkOptions);
		}
		else {
			$link = $this->Html->link($content, $item->url, $linkOptions);
		}

		//li element
		$elem = $this->Html->tag('li', $link . $subnav, $elemOptions);

		return $elem;
	}

	/**
	 * HTML render of list of items.
	 *
	 * @param array $items List of items.
	 * @param array $options
	 * @return string Toolbar list of items/links.
	 * @see HtmlHelper::tag() for details on $options that can be used.
	 */
	public function renderList($items, $options)
	{
		$content = '';

		foreach ($items as $item) {
			$content .= $this->renderItem($item);
		}

		return $this->Html->tag('ul', $content, $options);
	}

	/**
	 * Render of all items in nested toolbar navigation.
	 *
	 * @param mixed $data Event data.
	 * @param mixed $subject Event subject.
	 * @return string Toolbar nested navigation.
	 */
	public function render($data = null, $subject = null)
	{
		$event = new CakeEvent('LayoutToolbar.beforeRender', $subject, $data);

		$this->_View->getEventManager()->dispatch($event);

		$items = $this->removeForbiddenItems($this->_toolbarItems);

		return $this->renderList($items, [
			'class' => ['breadcrumb-elements'],
			'activeclass' => 'open'
		]);
	}

	/**
	 * Check for not allowed items by ACL and remove them.
	 * 
	 * @param array $items Layout toolbar items.
	 * @return array Available layout toolbar items.
	 */
	public function removeForbiddenItems($items)
	{
		$resultItems = [];

		foreach ($items as $key => $item) {
			if (!empty($item->children)) {
				$item->children = $this->removeForbiddenItems($item->children);
			}

			$allowed = true;

			if (!empty($item->url) && $item->url != '#') {
				$allowed = $this->AclCheck->check($item->url);
			}

			if (!empty($item->options['data-yjs-datasource-url'])) {
				$allowed = $this->AclCheck->check($item->options['data-yjs-datasource-url']);
			}

			if ($item->url == '#' && empty($item->options['data-yjs-datasource-url']) && empty($item->children)) {
				$allowed = false;
			}

			if ($allowed) {
				$resultItems[$key] = $item;
			}

		}

		return $resultItems;
	}

}
