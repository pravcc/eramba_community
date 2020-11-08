<?php
/**
 * @package       Workflows.Model
 */

App::uses('WorkflowsAppModel', 'Workflows.Model');

class WorkflowAccessModel extends WorkflowsAppModel {
	public $useTable = 'wf_access_models';
	public $displayField = 'name';

	public $actsAs = array(
		'Containable'
	);

	// get the list of models registered to access listening.
	public function getList() {
		return $this->find('list', [
			'recursive' => -1
		]);
	}
}