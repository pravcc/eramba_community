<?php
App::uses('AppHelper', 'View/Helper');

class AssetsHelper extends AppHelper {
	public $helpers = ['Html', 'Ajax', 'Eramba', 'FieldData.FieldData', 'Limitless.Alerts'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	public function assetClassificationField(FieldDataEntity $Field)
	{
		$classifications = $this->_View->get('classifications');
		
		if (!empty($classifications)) {
			return $this->_View->element('assets/asset_classifications/asset_classification_fields', [
				'classifications' => $this->_View->get('classifications')
			]);
		} else {
			return $this->Alerts->info(__('There are no asset classifications (Settings / Classifications) configured and therefore there is no need to classify this asset'));
		}
	}

	public function businessUnitsField(FieldDataEntity $Field)
	{
		$options = [
			'class' => [
				'eramba-auto-complete'
			],
			'id' => 'business-unit-id',
			'data-url' => '/assets/getLegals',
			'data-request-key' => 'buIds',
			'data-assoc-input' => '#legal-id'
		];

		return $this->FieldData->input($Field, $options);
	}

	public function legalField(FieldDataEntity $Field)
	{
		$options = [
			'id' => 'legal-id'
		];

		return $this->FieldData->input($Field, $options);
	}

	public function reviewField(FieldDataEntity $Field)
	{
		return $this->FieldData->input($Field, [
			'disabled' => !empty($this->_View->get('edit')) ? true : false
		]);
	}

	public function actionList($item, $options = []) {
		$reviewUrl = array(
			'plugin' => null,
			'controller' => 'reviews',
			'action' => 'index',
			'Asset',
			$item['Asset']['id']
		);

		$this->Ajax->addToActionList(__('Reviews'), $reviewUrl, 'search', 'index');

		$exportUrl = array(
			'controller' => 'assets',
			'action' => 'exportPdf',
			$item['Asset']['id']
		);

		$this->Ajax->addToActionList(__('Export PDF'), $exportUrl, 'file', false);

		$options = am([
			AppModule::instance('Visualisation')->getAlias() => true
		], $options);

		return parent::actionList($item, $options);
	}

	public function getStatusArr($item, $allow = '*') {
		$item = $this->Eramba->processItemArray($item, 'Asset');
		$statuses = array();

		if ($this->Eramba->getAllowCond($allow, 'expired_reviews') && $item['Asset']['expired_reviews'] == RISK_EXPIRED_REVIEWS) {
			$statuses[$this->getStatusKey('expired_reviews')] = array(
				'label' => __('Missing Asset Review'),
				'type' => 'warning'
			);
		}

		$inherit = array(
			'SecurityIncidents' => array(
				'model' => 'SecurityIncident',
				'config' => array('ongoing_incident')
			)
		);

		if ($this->Eramba->getAllowCond($allow, INHERIT_CONFIG_KEY)) {
			$statuses = am($statuses, $this->getInheritedStatuses($item, $inherit));
		}

		return $statuses;
	}

	public function getStatuses($item, $options = array()) {
		$options = $this->Eramba->processStatusOptions($options);

		$statuses = $this->getStatusArr($item, $options['allow']);
		return $this->Eramba->styleStatuses($statuses, $options);
	}

}
