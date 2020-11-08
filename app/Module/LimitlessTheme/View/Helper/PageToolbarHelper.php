<?php
App::uses('LayoutToolbarHelper', 'LimitlessTheme.View/Helper');

class PageToolbarHelper extends LayoutToolbarHelper
{
	/**
	 * Render of all items in nested toolbar navigation.
	 *
	 * @param mixed $data Event data.
	 * @param mixed $subject Event subject.
	 * @return string Toolbar nested navigation.
	 */
	public function render($data = null, $subject = null)
	{
		$event = new CakeEvent('PageToolbar.beforeRender', $subject, $data);

		$this->_View->getEventManager()->dispatch($event);

		$items = $this->removeForbiddenItems($this->_toolbarItems);

		return $this->renderList($items, [
			'class' => ['nav', 'navbar-nav', 'navbar-page-header'],
		]);
	}
}
