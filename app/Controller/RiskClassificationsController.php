<?php
App::uses('AppController', 'Controller');

class RiskClassificationsController extends AppController
{
	public $helpers = [];
	public $components = [
		'Paginator',
		'Crud.Crud' => [
			'actions' => [
				'index' => [
					'className' => 'AdvancedFilters.AdvancedFilters',
					'enabled' => true
				],
				'add' => [
					'enabled' => true,
					'saveMethod' => 'saveAssociated',
				],
				'edit' => [
					'enabled' => true,
					'saveMethod' => 'saveAssociated',
				],
				'delete' => [
					'enabled' => true,
					'view' => 'delete'
				],
			],
			'listeners' => ['Api', 'ApiPagination', '.SubSection']
		],
	];

	protected $_appControllerConfig = [
		'components' => [
		],
		'helpers' => [
		],
		'elements' => [
		]
	];

	public function beforeFilter()
	{
		$this->Crud->enable(['index', 'add', 'edit', 'delete', 'history', 'restore']);

		parent::beforeFilter();

		$this->title = __('Risk Classifications');
		$this->subTitle = __('');
	}

	public function index() {
		$this->subTitle = __('You will apply this classification to your Risks.');

		$this->Crud->addListener('AdvancedFilters', 'AdvancedFilters.AdvancedFilters');
		$this->Crud->addListener('Trash', 'Trash.Trash');
		$this->Crud->addListener('FieldData', 'FieldData.FieldData');

		return $this->Crud->execute();
	}

	public function _deleteBeforeRender(CakeEvent $event) {
		if (!empty($event->subject->request->params['pass'][0])) {
			$id = $event->subject->request->params['pass'][0];
			$isUsed = $this->RiskClassification->isUsed($id);
			$this->set('isUsed', $isUsed);
			$this->set('calculationRestrictDelete', $this->RiskClassification->calculationRestrictDelete($id));

			if ($isUsed) {
				$this->Modals->changeConfig('footer.buttons.deleteBtn.visible', false);
			}
		}
	}

	public function delete($id = null) {
		$this->title = __('Delete a Risk Classification');

		$this->Crud->on('beforeRender', [$this, '_deleteBeforeRender']);

		return $this->Crud->execute();
	}

	public function add() {
		$this->title = __('Create a Risk Classification');
		$this->initAddEditSubtitle();

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);
		$this->Crud->on('afterSave', [$this, '_afterSave']);

		return $this->Crud->execute();
	}

	public function edit($id = null) {
		$this->title = __('Edit a Risk Classification');
		$this->initAddEditSubtitle();

		if (isset($this->request->data['__warning_proceeded']) && !empty($this->request->data['__warning_proceeded'])) {
			$this->request->data = $this->Session->read('RiskClassificationWarningFormData');
			$this->request->data['__warning_proceeded'] = true;
			$this->Session->delete('RiskClassificationWarningFormData');
		}

		$id = (int) $id;
		// ddd($this->request->data);
		// if (!empty($this->request->data)) {
		// 	$id = (int) $this->request->data['RiskClassification']['id'];
		// }

		$data = $this->RiskClassification->find('first', array(
			'conditions' => array(
				'RiskClassification.id' => $id
			),
			'recursive' => 0
		));

		// if (empty($data)) {
		// 	throw new NotFoundException();
		// }

		$isUsed = $this->RiskClassification->isUsed($id);
		$this->set('isUsed', $isUsed);

		// if ($this->request->is('post') || $this->request->is('put')) {
		// 	$type = $data['RiskClassificationType']['name'];
		// 	$name = $data['RiskClassification']['name'];
		// 	$value = $data['RiskClassification']['value'];
		// 	$newValue = $this->request->data['RiskClassification']['value'];
		// 	if ($value != $newValue) {
		// 		if (empty($this->request->data['__warning_proceeded'])) {
		// 			$this->Session->write('RiskClassificationWarningFormData', $this->request->data);
		// 			$this->set('warning', __('This classification is used by %d risks, if you proceed we will update the risk score for this risks.', $isUsed));
		// 			$this->set('classificationId', $id);

		// 			return $this->render('warning');
		// 		}

		// 		$args = array($type, $name, $value, $newValue);
		// 		$msg = __('The classification "%1$s - %2$s - %3$s" applied on this risk has got its value updated to "%4$s" and therefore this risk has been updated from %5$s to %6$s');
		// 		$this->RiskClassification->addRiskScoreEvent($msg, $args);
		// 	}
		// }

		$this->Crud->on('beforeSave', [$this, '_beforeSave']);
		$this->Crud->on('afterSave', [$this, '_afterSave']);

		return $this->Crud->execute();
	}

	public function _beforeSave(CakeEvent $event)
	{
		if (!empty($this->request->data['RiskClassification']['risk_classification_type_id'])) {
			unset($this->request->data['RiskClassificationType']);
		}
	}

	public function _afterSave(CakeEvent $event) {
		if (!empty($event->subject->success) && !empty($event->subject->id)) {
			$this->RiskClassification->resaveScores($event->subject->id);

			if ($event->subject->created == false) {
				$this->RiskClassification->removeRiskScoreListener();
			}
		}
	}

	private function initAddEditSubtitle() {
		$this->subTitle = __('Usually there\'s many assets around in a organization. Trough classification (according to your needs) you will be able to set priorities and profile them in a way their treatment and handling is systematic. Btw, this is a basic requirement for most Security related regulations.');
	}

	public function history($id)
	{
		return $this->Crud->execute();
	}

	public function restore($autidId)
	{
		return $this->Crud->execute();
	}
}
