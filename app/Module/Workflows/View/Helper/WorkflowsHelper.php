<?php
/**
 * @package       Workflows.Helper
 */

App::uses('ModuleBaseHelper', 'View/Helper');
App::uses('AppModule', 'Lib');
App::uses('WorkflowStageStep', 'Workflows.Model');

class WorkflowsHelper extends ModuleBaseHelper {
	public $helpers = ['Html', 'Ajax'];
	public $settings = array();

	/**
	 * Get colored label for a Stage Step type.
	 * 
	 * @param  int|string $type Step type.
	 * @return string           Label HTML.
	 */
	public function stepTypeLabel($type) {
		$labels = [
			WorkflowStageStep::STEP_TYPE_DEFAULT => 'primary',
			WorkflowStageStep::STEP_TYPE_CONDITIONAL => 'success',
			WorkflowStageStep::STEP_TYPE_ROLLBACK => 'danger'
		];

		return $this->Html->tag('span', WorkflowStageStep::stepTypes($type), [
			'class' => sprintf('label label-%s', $labels[$type])
		]);
	}

	public function getRequestUrl() {
		$url = [
			'plugin' => 'workflows',
			'controller' => 'workflowInstances',
			'action' => 'handleRequest'
		];

		// we merge the provided arguments as a url parameters
		$url = am($url, func_get_args());

		return $url;
	}

	public function manageActionBtn($title = false, $url = '#') {
		$options = [
			'class' => 'more',
			'escape' => false
		];

		if ($title === false) {
			$title = __('No Actions Available');
			$options['class'] .= ' disabled readonly';
			$options['style'] = 'pointer-events: none;';
		}
		else {
			$options['data-ajax-action'] = 'edit';
			$title .= ' ' . $this->Html->tag('i', '', [
				'class' => 'pull-right icon-angle-right'
			]);
		}

		return $this->Html->link($title, $url, $options);
	}

	/**
	 * Get url for workflow management of a single object.
	 */
	public function getItemUrl($model, $foreignKey) {
		return AppModule::instance('Workflows')->getItemUrl($model, $foreignKey);
	}

	/**
	 * Get url for workflow management of a section.
	 */
	public function getSectionUrl($model) {
		return AppModule::instance('Workflows')->getSectionUrl($model);
	}

	// public function getSectionBtn($model) {
	// 	if (is_array($model)) {
	// 		$list = array();
	// 		foreach ($model as $alias => $name) {
	// 			$list[] = $this->Html->link($name, $this->getSectionUrl($model), array(
	// 				'escape' => false
	// 			));
	// 		}

	// 		$ul = $this->Html->nestedList($list, array(
	// 			'class' => 'dropdown-menu pull-right',
	// 			'style' => 'text-align: left;'
	// 		));

	// 		$btn = '<button class="btn dropdown-toggle" data-toggle="dropdown"><i class="icon-cog"></i>' . WorkflowsModule::alias() . ' <span class="caret"></span></button>';

	// 		return $btn . $ul;
	// 	}
	// 	else {
	// 		return $this->Html->link( '<i class="icon-info-sign"></i> ' . WorkflowsModule::alias(), $this->getSectionUrl($model), array(
	// 				'class' => 'btn',
	// 				'escape' => false
	// 			)
	// 		);
	// 	}		
	// }

}
