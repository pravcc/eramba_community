<?php
class SystemRecordsController extends AppController {
	public $helpers = array( 'Html', 'Form' );
	public $components = array( 'Session', 'CsvView.CsvView', 'Search.Prg');

	public function index($model = null, $foreign_key = null) {
		$this->set( 'title_for_layout', __( 'System Records' ) );
		$this->set( 'subtitle_for_layout', __( 'Every transaction on the system is recorded and can be found here. Optionally you can also search for records on the object (a risk, control, etc.) directly by using the functionality "system records" on the action cell of the object.' ) );

		$this->set('filterArgs', $this->SystemRecord->filterArgs);
		$this->Prg->commonProcess();
		$filterConditions = $this->SystemRecord->parseCriteria($this->Prg->parsedParams());

		$conditions = array();
		if (!empty($model)) {
			$conditions['SystemRecord.model'] = $model;
		}

		if (!empty($foreign_key)) {
			$conditions['SystemRecord.foreign_key'] = $foreign_key;
		}

		$this->paginate = array(
			'conditions' => am($filterConditions, $conditions),
			'fields' => array(
				'SystemRecord.id',
				'SystemRecord.model',
				'SystemRecord.model_nice',
				'SystemRecord.foreign_key',
				'SystemRecord.item',
				'SystemRecord.notes',
				'SystemRecord.workflow_status',
				'SystemRecord.workflow_comment',
				'SystemRecord.type',
				'SystemRecord.ip',
				'SystemRecord.user_id',
				'SystemRecord.created',
				'User.name',
				'User.surname'
			),
			'order' => array('SystemRecord.created' => 'DESC'),
			'limit' => $this->getPageLimit(),
			'recursive' => 0
		);

		$data = $this->paginate( 'SystemRecord' );
		$this->set( 'data', $data );

		$this->set('backUrl', null);
		if (!empty($model) && !empty($foreign_key)) {
			$this->set('backUrl', $this->getIndexUrl($model, $foreign_key));
		}

		$this->initOptions();
	}

	private function initOptions() {
		$types = array(
			1 => __('Insert'),
			2 => __('Update'),
			3 => __('Delete'),
			4 => __('Login'),
			5 => __('Wrong Login')
		);

		$this->set( 'types', $types );

		$workflowStatuses = getWorkflowStatuses();

		$this->set('workflowStatuses', $workflowStatuses);
	}

	/**
	 * CSV Export for this model data.
	 */
	public function export() {
		$results = $this->SystemRecord->find('all', array(
			'recursive' => -1
		) );
		$this->response->download( 'system_record.csv' );
		$this->CsvView->quickExport($results);
	}

}
