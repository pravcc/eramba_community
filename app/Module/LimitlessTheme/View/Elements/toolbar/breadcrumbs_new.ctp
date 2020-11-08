<?php
foreach ($crumbs as $key => $crumb) {
	$this->Html->addCrumb($crumb->title(), $crumb->link());
}

echo $this->Html->getCrumbList([
		'separator' => '',
		'lastClass' => 'active',
		'id' => 'breadcrumbs',
		'class' => 'breadcrumb',
		'firstClass' => ''
	],
	[
	    'text' => $this->Html->tag('i', '', ['class' => 'icon-home2  position-left']) . __('Dashboard'),
	    'url' => array('plugin' => null, 'controller' => 'pages', 'action' => 'dashboard', 'admin' => false),
	    'escape' => false
	]);
?>