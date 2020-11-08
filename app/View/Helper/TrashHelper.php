<?php
App::uses('AppHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');

class TrashHelper extends AppHelper {
	public $helpers = array('Html', 'Form');

	/**
	 * Additional dropdown links to trash indexes.
	 */
	public function getAdditionalTrashLinks($links) {
		$list = array();
		foreach ($links as $model => $text) {
			$_m = ClassRegistry::init($model);
			if (empty($_m->advancedFilterSettings['trash'])) {
				continue;
			}
			
			// specific details defained as array
			if (is_array($text)) {
				$list[] = $this->Html->link($text['label'], $text['url']);
			}
			else {
				$controller = controllerFromModel($model);

				$list[] = $this->Html->link($text, array(
					'controller' => $controller,
					'action' => 'trash',
					'?' => array(
						'advanced_filter' => 1
					)
				));
			}
		}

		if (empty($list)) {
			return '';
		}

		$caret = $this->Html->tag('span', false, array(
			'class' => 'caret'
		));

		// dropdown btn caret toggle
		$dropdownToggle = $this->Html->tag('button', $caret, array(
			'class' => 'btn btn-danger dropdown-toggle',
			'data-toggle' => 'dropdown',
			'escape' => false
		));

		return $dropdownToggle . $this->Html->nestedList($list, array(
			'class' => 'dropdown-menu pull-right'
		));
	}

}