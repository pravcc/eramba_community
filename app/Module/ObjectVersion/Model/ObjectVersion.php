<?php
App::uses('ObjectVersionAppModel', 'ObjectVersion.Model');
App::uses('ClassRegistry', 'Utility');
App::uses('AclObjectExtras', 'Visualisation.Lib');

class ObjectVersion extends ObjectVersionAppModel {
	public $useTable = false;
	public $Audit = null;

	public function addMissingVersioning() {
		$Audit = ClassRegistry::init('ObjectVersion.Audit');
		$models = ClassRegistry::init('Visualisation.VisualisationSetting')->getModelAliases();
		$models[] = 'Threat';
		$models[] = 'Vulnerability';

		$ret = true;
		foreach ($models as $modelName) {
			$ids = $Audit->hasRevision($modelName);
			$Model = ClassRegistry::init($modelName);

			if (!$Model->Behaviors->enabled('Auditable')) {
				continue;
			}

			$Auditable = $Model->Behaviors->Auditable;

			$addIds = $Model->find('list', [
				'conditions' => [
					$Model->alias . '.id !=' => $ids
				],
				'fields' => [$Model->alias . '.id'],
				'recursive' => -1
			]);

			foreach ($addIds as $itemId) {
				$Model->id = $itemId;
				$ret &= $Auditable->afterSave($Model, true, []);
			}
		}

		return $ret;
	}
}