<?php
/**
 * @package       Workflows.Helper
 */

App::uses('ModuleBaseHelper', 'View/Helper');
App::uses('AppModule', 'Lib');

class VisualisationHelper extends ModuleBaseHelper {
	public $helpers = ['Html', 'Ajax'];
	public $settings = array();

	public function getSectionLink($model, $label = null) {
		if ($label === null) {
			$label = __('Edit');
		}

		return $this->Html->link($label, $this->module->getSectionUrl($model), array(
				'class' => '',
				'escape' => false,
				'data-ajax-action' => 'edit'
			)
		);
	}

	public function getSectionBtn($model) {
		if (is_array($model)) {
			$list = array();
			foreach ($model as $alias => $name) {
				$list[] = $this->Html->link($name, $this->module->getSectionUrl($model), array(
					'escape' => false,
					'data-ajax-action' => 'edit'
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
					'escape' => false,
					'data-ajax-action' => 'edit'
				)
			);
		}		
	}
}
