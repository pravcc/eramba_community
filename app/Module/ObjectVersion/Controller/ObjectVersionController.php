<?php
App::uses('ObjectVersionAppController', 'ObjectVersion.Controller');
App::uses('ObjectVersionHistory', 'ObjectVersion.Lib');
App::uses('ObjectVersionRestore', 'ObjectVersion.Lib');
App::uses('Audit', 'ObjectVersion.Model');
App::uses('ClassRegistry', 'Utility');

class ObjectVersionController extends ObjectVersionAppController {
	public $components = array( 'Session', 'Ajax');

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function history($model, $foreignKey) {
		if (empty($model)) {
			throw new NotFoundException();
		}

		$this->set('title_for_layout', __('History Timeline'));
		
		$historyClass = new ObjectVersionHistory($model, $foreignKey);
		$this->set('historyClass', $historyClass);
		$this->set('showHeader', true);

		//
        // Init modal
        $this->Modals->init();
        if (count($this->Modals->getBreadcrumbs()) == 0) {
        	$TargetModel = ClassRegistry::init($model);
        	if (!empty($TargetModel)) {
        		$this->Modals->addBreadcrumb($TargetModel->label(), true);
        	}
        }
        //
	}

	public function restore($auditId) {
		$this->Audit = ClassRegistry::init('ObjectVersion.Audit');

		$audit = $this->Audit->find('first', array(
			'conditions' => array(
				'Audit.id' => $auditId
			),
			'recursive' => -1
		));

		if (empty($audit)) {
			throw new NotFoundException();
		}

		$this->_auditRestore($audit, true);

		$this->history($audit['Audit']['model'], $audit['Audit']['entity_id']);
		$this->render('history');
	}

	protected function _auditRestore($audit, $reportMessage = false) {
		$restoreClass = new ObjectVersionRestore($audit['Audit']['id']);

		$ret = $restoreClass->restore();

		if ($ret) {
			$ret &= (boolean) $this->_associatedRestore($audit);

			if ($reportMessage) {
				if ($restoreClass->isRestoredDataValid()) {
					$this->Session->setFlash(__('Object was successfully restored.'), FLASH_OK);
				}
				else {
					$this->Session->setFlash(__('Object was restored but its data failed validation. Please edit and re-save the object to manually resolve possible issues.'), FLASH_WARNING);
				}
				
				// in case everything went fine during the restore but revisions are identical
				if ($restoreClass->hasChanges() === false && $restoreClass->isRestoredDataValid()) {
					$this->Flash->set(__('Current revision and the revision you chose to restore are identical, there is no need to add another one.'));
				}

				if (!$ret) {
					$this->Session->setFlash(__('Some of associated object have not been restored correctly.'), FLASH_WARNING);
				}
				else {
					$this->Ajax->setState('success');
				}
			}
		}
		elseif ($reportMessage) {
			$this->Session->setFlash(__('Error occured while trying to restore the object. Please try it again.'), FLASH_ERROR);
			$this->Ajax->setState('error');
		}

		return $ret;
	}

	protected function _associatedRestore($audit) {
		$Model = ClassRegistry::init($audit['Audit']['model']);

		$ret = true;

		if (!$Model->Behaviors->enabled('AssociativeDelete')) {
			return $ret;
		}

		$ids = $Model->getAllAssociatedIds(null, $audit['Audit']['entity_id']);

		foreach ($ids as $assocModel => $assocIds) {
			foreach ($assocIds as $assocId) {
				$deletedItemExists = $Model->{$assocModel}->find('count', [
					'conditions' => [
						$Model->{$assocModel}->alias . '.id' => $assocId,
						$Model->{$assocModel}->alias . '.deleted' => true,
					]
				]);

				$audit = $this->Audit->find('first', array(
					'conditions' => array(
						'Audit.entity_id' => $assocId,
						'Audit.model' => $Model->{$assocModel}->name
					),
					'order' => [
						'Audit.created' => 'DESC',
					],
					'recursive' => -1
				));

				if ($deletedItemExists && !empty($audit)) {
					$ret &= (boolean) $this->_auditRestore($audit);

					//trigger object status
	                if ($Model->{$assocModel}->Behaviors->enabled('ObjectStatus')) {
	                    $ret &= (boolean) $Model->{$assocModel}->triggerObjectStatus();
	                }
				}
			}
		}

		return $ret;
	}

}