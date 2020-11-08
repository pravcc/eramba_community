<?php
App::uses('AppHelper', 'View/Helper');
App::uses('AppModule', 'Lib');

abstract class ModuleBaseHelper extends AppHelper {
	public $helpers = ['Html', 'Ux'];

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$name = substr(get_class($this), 0, -6);
		$this->module = AppModule::instance($name);
	}

	public function getSectionBtn($model) {
		if (is_array($model)) {
			$list = array();
			foreach ($model as $alias => $name) {
				$list[] = $this->Html->link($name, $this->module->getSectionUrl($model), array(
					'escape' => false
				));
			}

			$ul = $this->Html->nestedList($list, array(
				'class' => 'dropdown-menu pull-right',
				'style' => 'text-align: left;'
			));

			$btn = '<button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i>' . $this->module->getName() . ' <span class="caret"></span></button>';

			return $btn . $ul;
		}
		else {
			return $this->Html->link( '<i class="icon-info-sign"></i> ' . $this->module->getName(), $this->module->getSectionUrl($model), array(
					'class' => 'btn',
					'escape' => false
				)
			);
		}		
	}

}
