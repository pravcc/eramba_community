<?php
App::uses('ModuleBase', 'Lib');
App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');
App::uses('AppErrorHandler', 'Error');

class ObjectStatusModule extends ModuleBase {

	public static function modelNames()
	{
		$models = [
			'RiskException', // this needs to be before risks
			'Asset',
			'AssetReview',
			'AwarenessProgram',
			'BusinessContinuity',
			'BusinessContinuityPlan',
			'BusinessContinuityPlanAudit',
			'BusinessContinuityPlanAuditImprovement',
			'BusinessContinuityReview',
			'BusinessContinuityTask',
			'BusinessUnit',
			'ComplianceAnalysisFinding',
			'ComplianceAudit',
			'ComplianceAuditFeedback',
			'ComplianceAuditFeedbackProfile',
			'ComplianceAuditSetting',
			'ComplianceException',
			'ComplianceFinding',
			'ComplianceFindingStatus',
			'ComplianceManagement',
			'CompliancePackage',
			'CompliancePackageInstance',
			'CompliancePackageItem',
			'DataAsset',
			'DataAssetGdpr',
			'DataAssetInstance',
			'DataAssetSetting',
			'Goal',
			'GoalAudit',
			'GoalAuditImprovement',
			'Group',
			'Legal',
			'PolicyException',
			'Process',
			'ProgramIssue',
			'ProgramScope',
			'Project',
			'ProjectAchievement',
			'ProjectExpense',
			'Risk',
			'RiskReview',
			'SecurityIncident',
			'SecurityIncidentStage',
			'SecurityIncidentStagesSecurityIncident',
			'SecurityPolicy',
			'SecurityPolicyReview',
			'SecurityService',
			'SecurityServiceAudit',
			'SecurityServiceAuditImprovement',
			'SecurityServiceIssue',
			'SecurityServiceMaintenance',
			'ServiceContract',
			'TeamRole',
			'ThirdParty',
			'ThirdPartyRisk',
			'ThirdPartyRiskReview',
			
		];

		if (AppModule::loaded('AccountReviews')) {
			$models += [
				'AccountReviews.AccountReview',
				'AccountReviews.AccountReviewFeed',
				'AccountReviews.AccountReviewFeedback',
				'AccountReviews.AccountReviewFinding',
				'AccountReviews.AccountReviewPull'
			];
		}

		if (AppModule::loaded('VendorAssessments')) {
			$models += [
				'VendorAssessments.VendorAssessment',
				'VendorAssessments.VendorAssessmentFeedback',
				'VendorAssessments.VendorAssessmentFinding'
			];
		}

		return $models;
	}
/**
 * Sync all Object Statuses in all models.
 * 
 * @return boolean success
 */
	public function syncAllStatuses($modelNames = [])
	{
		if (empty($modelNames)) {
			$modelNames = self::modelNames();
		}
		
		$ret = true;

		$modelIds = [];

		foreach ($modelNames as $modelName) {
			try {
				$Model = ClassRegistry::init($modelName);

				if ($Model->Behaviors->enabled('ObjectStatus.ObjectStatus') && $Model->hasObjectStatuses() && !empty($Model->useTable)) {
					$modelIds[$modelName] = $this->_getItemsList($Model);
					$ret &= $Model->triggerObjectStatus(null, $modelIds[$modelName], ['trigger' => false, 'inherited' => false, 'regularTrigger' => true]);
				}
			}
			catch(Exception $e) {
				// log the error
				AppErrorHandler::logException($e);
			}
		}

		foreach ($modelNames as $modelName) {
			try {
				$Model = ClassRegistry::init($modelName);

				if ($Model->Behaviors->enabled('ObjectStatus.ObjectStatus') && $Model->hasObjectStatuses() && !empty($Model->useTable)) {
					$ret &= $Model->triggerObjectStatus(null, $modelIds[$modelName], ['trigger' => false, 'inherited' => true, 'regularTrigger' => true]);
				}
			}
			catch(Exception $e) {
				// log the error
				AppErrorHandler::logException($e);
			}
		}

		return $ret;
	}

/**
 * Get list of all item ids in current model.
 * 
 * @param  Model $Model model instance
 * @return array list of ids
 */
	protected function _getItemsList($Model)
	{
		$ids = [];

		$data = $Model->find('list', [
			'recursive' => -1
		]);
		if (!empty($data)) {
			$ids = array_keys($data);
		}

		return $ids;
	}
}
