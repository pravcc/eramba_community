<?php
App::uses('AppHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');
App::uses('RiskException', 'Model');

class RiskExceptionsHelper extends AppHelper
{
	public $helpers = ['Html', 'FieldData.FieldData'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		$this->helpers[] = 'ErambaTime';
		$this->helpers[] = 'NotificationSystem';
		$this->helpers[] = 'Taggable';

		parent::__construct($view, $settings);
		$this->settings = $settings;
	}

	public function getStatuses($item, $showAll = true) {
		$item = $this->processItemArray($item, 'RiskException');
		$statuses = array();

		if ($item['RiskException']['status'] == RISK_EXCEPTION_CLOSED) {
			$statuses[] = $this->getLabel(__('Closed'), 'success');
		}

		if ($item['RiskException']['status'] == RISK_EXCEPTION_OPEN) {
			$statuses[] = $this->getLabel(__('Open'), 'success');
		}

		if ($item['RiskException']['expired'] == ITEM_STATUS_EXPIRED) {
			$statuses[] = $this->getLabel(__('Expired'), 'danger');
		}
		
		$options = array();
		if ($showAll) {
			//$statuses = array_merge($statuses, $this->NotificationSystem->getStatuses($item));
		}
		else {
			//$options['inline'] = false;
		}
		
		return $this->processStatuses($statuses, $options);
	}

	/**
	 * Show tags for a risk exception.
	 */
	public function getTags($item) {
		return $this->Taggable->showList($item, [
			'notFoundCallback' => [$this->Taggable, 'notFoundBlank']
		]);
	}

	public function closureDateField(FieldDataEntity $Field)
	{
		$FieldDataCollection = ClassRegistry::init('RiskException')->getFieldCollection();

		$out = $this->FieldData->input($FieldDataCollection->closure_date_toggle, [
			'class' => [
				'risk-exception-closure-date-toggle'
			]
		]);

        $out .= $this->FieldData->input($Field, [
        	'class' => [
        		'risk-exception-closure-date'
        	]
        ]);

        $out .= $this->Html->scriptBlock("
			jQuery(function($) {
				$(\".risk-exception-closure-date-toggle\").on(\"change\", function(e) {
					if ($(this).is(\":checked\")) {
						$(\".risk-exception-closure-date-toggle\").attr(\"checked\", true);
						$(\".risk-exception-closure-date\").attr(\"disabled\", \"disabled\");
					}
					else {
						$(\".risk-exception-closure-date-toggle\").attr(\"checked\", false);
						$(\".risk-exception-closure-date\").removeAttr(\"disabled\");
					}
				}).trigger(\"change\");
			});
		");

        return $out;
	}

}
