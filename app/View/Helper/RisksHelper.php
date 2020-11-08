<?php
App::uses('AppHelper', 'View/Helper');
App::uses('RiskAppetite', 'Model');
App::uses('RiskClassification', 'Model');

class RisksHelper extends AppHelper {
	public $helpers = ['Html', 'FieldData.FieldData', 'Form', 'FormReload'];
	public $settings = [];
	
	public function __construct(View $view, $settings = []) {
		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function riskClassificationField(FieldDataEntity $Field)
	{
		$assoc = $Field->getAssociationConfig();
		$type = $assoc['conditions'][$assoc['with'] . '.type'];
		$appetiteMethod = $this->_View->get('appetiteMethod');

		if ($type == RiskClassification::TYPE_TREATMENT && $appetiteMethod != RiskAppetite::TYPE_THRESHOLD) {
			return null;
		}

		$classModel = 'RiskClassification';
		if ($type == RiskClassification::TYPE_TREATMENT) {
			$classModel = 'RiskClassificationTreatment';
		}

		$options = [
			'classifications' => $this->_View->get('classifications'),
			// 'justLoaded' => true,
			'model' => $Field->getModelName(),
			'type' => $type,
			'element' => '#risk-classification-type-'.$type.'-fields',
			'classModel' => $classModel
		];

		$options = array_merge($options, $this->_View->get('riskCalculationData')[$classModel]);

		$model = $Field->getModelName();

		return $this->Html->div('form-group risk-classification-reload', $this->_View->element('risks/risk_classifications/classifications_ajax', $options), [
			'id' => 'risk-classification-type-'.$type.'-fields',
			'escape' => false,
			'data-yjs-request' => 'crud/submitForm',
			'data-yjs-event-on' => 'none',
			'data-yjs-target' => 'self',
			'data-yjs-datasource-url' => Router::url([
				'action' => 'processClassifications',
				'?' => [
					'classModel' => $classModel,
					'type' => $type
				]
			]),
			'data-yjs-forms' => '#RiskEditForm|#RiskAddForm|#ThirdPartyRiskEditForm|#ThirdPartyRiskAddForm|#BusinessContinuityEditForm|#BusinessContinuityAddForm'
		]);

		// $action = $this->_View->get('Section')->getAction();

		// $options = [];
		// if ($action instanceof EditCrudAction) {
		// 	$options['disabled'] = true;
		// }

		// return $this->FieldData->input($Field, $options);
	}

	public function reviewField(FieldDataEntity $Field)
	{
		$action = $this->_View->get('Section')->getAction();

		$options = [];
		if ($action instanceof EditCrudAction) {
			$options['disabled'] = true;
		}

		return $this->FieldData->input($Field, $options);
	}

	public function residualScoreField(FieldDataEntity $Field)
	{
		$appetiteMethod = $this->_View->get('appetiteMethod');

		$options = [];
		if ($appetiteMethod == RiskAppetite::TYPE_THRESHOLD) {
			$options['div'] = [
				'class' => 'hidden'
			];
		}

		return $this->FieldData->input($Field, $options);
	}

	public function assetField(FieldDataEntity $Field)
	{
		// $options = [
		// 	'class' => [
		// 		'related-risk-item-input',
		// 		'risk-classifications-trigger'
		// 	],
		// 	'id' => 'risk-asset-id'
		// ];

		$script = $this->_getAssetScript();

		$options = [
			'id' => 'risk-asset-id',
			'data-yjs-request' => 'app/triggerRequest/.risk-classification-reload',
			'data-yjs-event-on' => 'change',
			'data-yjs-use-loader' => 'false'
		];

		return $this->FieldData->input($Field, $options) . $script;
	}

	protected function _getAssetScript()
	{
		return $this->Html->scriptBlock("
			$(function() {
				$('#risk-asset-id').erambaAutoComplete({
					url: '/risks/getThreatsVulnerabilities',
					requestKey: ['assocIds'],
					requestType: 'POST',
					responseKey: ['threats', 'vulnerabilities'],
					assocInput: '#risk-threat-id, #risk-vulnerability-id'
				});
			});
		");
	}

	public function threatField(FieldDataEntity $Field)
	{
		$options = [
			'id' => 'risk-threat-id'
		];

		return $this->FieldData->input($Field, $options);
	}

	public function vulnerabilityField(FieldDataEntity $Field)
	{
		$options = [
			'id' => 'risk-vulnerability-id'
		];

		return $this->FieldData->input($Field, $options);
	}

	public function riskScoreField(FieldDataEntity $Field)
	{
		$options = [
			'id' => 'risk-score-input'
		];

		return $this->FieldData->input($Field, $options);
	}

	public function securityServiceField(FieldDataEntity $Field)
	{
		$options = [
			'class' => ['eramba-auto-complete'],
			'id' => 'compensating_controls',
			'data-url' => '/risks/getPolicies',
			'data-request-key' => 'controlIds',
			'data-assoc-input' => '#procedure-documents, #policy-documents, #standard-documents'
		];

		return $this->FieldData->input($Field, $options);
	}

	public function riskMitigationStrategyField(FieldDataEntity $Field)
	{
		$options = array_merge([
			'id' => 'risk_mitigation_strategy',
		], $this->FormReload->triggerOptions());

		return $this->FieldData->input($Field, $options);
	}

	public function actionList($item, $options = []) {
		$reviewUrl = array(
			'plugin' => null,
			'controller' => 'reviews',
			'action' => 'index',
			'Risk',
			$item['Risk']['id']
		);

		$this->Ajax->addToActionList(__('Reviews'), $reviewUrl, 'search', 'index');

		$exportUrl = array(
			'controller' => 'risks',
			'action' => 'exportPdf',
			$item['Risk']['id']
		);

		$this->Ajax->addToActionList(__('Export PDF'), $exportUrl, 'file', false);

		return parent::actionList($item, $options);
	}

	public function getStatusArr($item, $allow = '*', $modelName = 'Risk') {
		$item = $this->Status->processItemArray($item, $modelName);
		$statuses = array();

		if ($this->Status->getAllowCond($allow, 'expired_reviews') && $item[$modelName]['expired_reviews']) {
			$statuses[$this->Status->getStatusKey('expired_reviews')] = array(
				'label' => __('Risk Review Expired'),
				'type' => 'warning'
			);
		}

		$appetiteConds = $this->_View->get('appetiteMethod') == RiskAppetite::TYPE_INTEGER;
		$appetiteConds &= $this->Status->getAllowCond($allow, 'risk_above_appetite');
		$appetiteConds &= $item[$modelName]['risk_above_appetite'];
		if ($appetiteConds) {
			$statuses[$this->Status->getStatusKey('risk_above_appetite')] = array(
				'label' => __('Risk Above Appetite'),
				'type' => 'danger'
			);
		}

		$inherit = array(
			'Projects' => array(
				'model' => 'Project',
				'config' => array('expired')
			),
			'SecurityIncidents' => array(
				'model' => 'SecurityIncident',
				'config' => array('ongoing_incident')
			),
			'PolicyExceptions' => array(
				'model' => 'RiskException',
				'config' => array('expired')
			),
			/*'Goals' => array(
				'model' => 'Goal',
				'config' => array('metric_last_missing', 'ongoing_corrective_actions')
			),*/
			'SecurityServices' => array(
				'model' => 'SecurityService',
				'config' => array(
					'audits_last_passed',
					'audits_last_missing',
					'maintenances_last_missing',
					'ongoing_corrective_actions',
					'security_service_type_id',
					'control_with_issues'
				)
			),
			/*'BusinessContinuityPlans' => array(
				'model' => 'BusinessContinuityPlan',
				'config' => array(
					'audits_last_passed',
					'audits_last_missing',
					'ongoing_corrective_actions',
					'security_service_type_id'
				)
			),*/
		);

		if ($modelName == 'Risk') {
			$inherit['Assets'] = array(
				'model' => 'Asset',
				'config' => array('expired_reviews')
			);
		}

		if ($this->Status->getAllowCond($allow, INHERIT_CONFIG_KEY)) {
			$statuses = am($statuses, $this->getInheritedStatuses($item, $inherit));
		}

		return $statuses;
	}

	public function getHeaderClass($item, $modelName, $allow = true) {
		$statuses = $this->getStatusArr($item, $allow, $modelName);
		$type = $this->Eramba->getColorType($statuses);
		$class = $this->Eramba->processHeaderType($type);

		return $class;
	}

	public function getStatuses($item, $modelName = 'Risk', $options = array()) {
		$options = $this->Status->processStatusOptions($options);
		
		$statuses = $this->getStatusArr($item, $options['allow'], $modelName);
		
		return $this->Status->styleStatuses($statuses/*, $opts*/);
	}

	public function getGranularityList() {
		$granularity = Configure::read('Eramba.Settings.RISK_GRANULARITY');

		if ($granularity === null) {
			trigger_error('Failed to read Risk Granularity setting from the Configure class!');
			return false;
		}

		$current = 0;
		$list = [];
		while ($current <= 100) {
			$list[$current] = CakeNumber::toPercentage($current, 0);
			$current = $current + $granularity;
		}

		return $list;
	}

	public function exceptionsExpired($exceptions) {
		foreach ($exceptions as $exception) {
			if ($this->isExpired($exception['expiration'], $exception['status'])) {
				return $this->Html->tag('span', __('Exception Expired'), array('class' => 'label label-danger'));
			}
		}
	}

}