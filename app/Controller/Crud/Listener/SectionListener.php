<?php
App::uses('CrudListener', 'Crud.Controller/Crud');
App::uses('SectionView', 'Controller/Crud/View');

/**
 * Section Listener
 */
class SectionListener extends CrudListener
{
	/**
	 * Caching property that holds list of children models for a certain model.
	 * 
	 * @var array
	 */
	protected $_childrenByModel = [];

	public function implementedEvents()
	{
		return [
			'Crud.beforeHandle' => ['callable' => 'beforeHandle', 'priority' => 50],
			'Crud.beforeRender' => ['callable' => 'beforeRender', 'priority' => 9],
		];
	}

	public function beforeHandle(CakeEvent $e)
	{
		// nothing urgent yet
	}

	/**
	 * Return list of sections on the same level as current one.
	 * 
	 * @return array
	 */
	public function getNeighbourghs()
	{
		$currentModel = $this->_model();

		$neighbourghs = [];
		if ($currentModel->hasMethod('parentModel')) {

			// if ($currentModel == 'DataAssetInstance') 
			$parentModel = $currentModel->parentModel();

			$neighbourghs = $this->getChildrenByModel($parentModel);

			// we unset the current model from the set of neighbourghs
			$currentModelKey = array_search($currentModel->alias, $neighbourghs);
			if ($currentModelKey !== false) {
				unset($neighbourghs[$currentModelKey]);
			}
		}

		return $neighbourghs;
	}

	/**
	 * Get list of children models based on a certain model.
	 * 
	 * @param  string $checkModel Model alias to check.
	 * @return array
	 */
	public function getChildrenByModel($checkModel)
	{
		if (isset($this->_childrenByModel[$checkModel])) {
			return $this->_childrenByModel[$checkModel];
		}

		$models = [
			'Legal',
			'Asset',
			'AssetReview',
			'SecurityService',
			'SecurityServiceIssue',
			'SecurityServiceAudit',
			'SecurityServiceMaintenance',
			'SecurityPolicy',
			'SecurityPolicyReview',
			'SecurityIncident',
			'SecurityIncidentStagesSecurityIncident',
			'RiskReview',
			'ThirdPartyRiskReview',
			'BusinessContinuityReview',
			'Risk',
			'ThirdPartyRisk',
			'BusinessContinuity',
			'BusinessUnit',
			'Process',
			'ServiceContract',
			'RiskException',
			'PolicyException',
			'ComplianceException',
			'Project',
			'ProjectExpense',
			'ProjectAchievement',
			'ProgramScope',
			'ProgramIssue',
			'TeamRole',
			'Goal',
			'GoalAudit',
			'ComplianceAnalysisFinding',
			'BusinessContinuityPlan',
			'BusinessContinuityPlanAudit',
			'BusinessContinuityTask',
			'User',
			'Group',
			'Queue',
			'Cron',
			'OauthConnector',
			'LdapConnector',
			'DataAsset',
			'AwarenessProgram',
			'AwarenessProgramActiveUser',
			'AwarenessProgramIgnoredUser',
			'AwarenessProgramCompliantUser',
			'AwarenessProgramNotCompliantUser',
			'AwarenessReminder',
			'CompliancePackageRegulator',
			'CompliancePackageItem',
			'SamlConnector'
		];

		if (AppModule::loaded('VendorAssessments')) {
			$models[] = 'VendorAssessment';
			$models[] = 'VendorAssessmentFinding';
			$models[] = 'VendorAssessmentFeedback';
		}

		if (AppModule::loaded('AccountReviews')) {
			$models[] = 'AccountReviews';
			$models[] = 'AccountReviewsFinding';
		}

		if (AppModule::loaded('Mapping')) {
			$models[] = 'ComplianceManagementMappingRelation';
		}

		$children = [];
		foreach ($models as $model) {
			// load model
			App::uses($model, 'Model');

			$Model = ClassRegistry::init($model);
			
			// if model is based on inheritance interface (is child)
			// and has parent model the same as method argument
			if ($Model->hasMethod('parentModel') && $Model->parentModel() == $checkModel) {
				$children[] = $Model->alias;
			}
		}

		// write to class' cache property and return
		return $this->_childrenByModel[$checkModel] = $children;
	}

	/**
	 * Before render callback that sets all required data into the view.
	 * 
	 * @param  CakeEvent $e
	 * @return void
	 */
	public function beforeRender(CakeEvent $e)
	{
		$SectionView = new SectionView($e->subject);

		$this->_controller()->set('Section', $SectionView);
	}

}
