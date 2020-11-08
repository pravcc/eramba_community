<?php
App::uses('Component', 'Controller');
App::uses('Crumb', 'Lib/Breadcrumbs');

class BreadcrumbsComponent extends Component
{
	protected $_crumbs = [];

	public function add($title, $link = null)
	{
		$crumb = new Crumb($title, $link);

		$this->_crumbs[] = $crumb;
	}

	public function getCrumbs()
	{
		return $this->_crumbs;
	}

	public function getLastCrumb()
	{
		$crumbs = $this->getCrumbs();

		if (empty($crumbs)) {
			return null;
		}

		return end($crumbs);
	}

	public function beforeRender(Controller $controller)
	{
		$controller->set('crumbs', $this->getCrumbs());
	}


}