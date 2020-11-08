<?php
App::uses('AppHelper', 'View/Helper');
App::uses('ClassRegistry', 'Utility');
App::uses('ComplianceException', 'Model');

class ComplianceExceptionsHelper extends AppHelper
{
	public $helpers = ['Html', 'FieldData.FieldData'];
	public $settings = array();
	
	public function __construct(View $view, $settings = array()) {
		$this->helpers[] = 'ErambaTime';
		$this->helpers[] = 'Taggable';
		parent::__construct($view, $settings);

		$this->settings = $settings;
	}

	/**
	 * @deprecated
	 */
	public function getStatuses($item) {
		$item = $this->processItemArray($item, 'ComplianceException');
		$statuses = array();

		if ($item['ComplianceException']['status'] == COMPLIANCE_EXCEPTION_CLOSED) {
			$statuses[] = $this->getLabel(__('Closed'), 'success');
		}

		if ($item['ComplianceException']['status'] == COMPLIANCE_EXCEPTION_OPEN) {
			$statuses[] = $this->getLabel(__('Open'), 'success');
		}

		if ($item['ComplianceException']['expired'] == ITEM_STATUS_EXPIRED) {
			$statuses[] = $this->getLabel(__('Expired'), 'danger');
		}

		return $this->processStatuses($statuses);
	}

	/**
	 * Show tags for a compliance exception.
	 */
	public function getTags($item) {
		return $this->Taggable->showList($item, [
			'notFoundCallback' => [$this->Taggable, 'notFoundBlank']
		]);
	}

	public function closureDateField(FieldDataEntity $Field)
	{
		$FieldDataCollection = ClassRegistry::init('ComplianceException')->getFieldCollection();

		$out = $this->FieldData->input($FieldDataCollection->closure_date_toggle, [
			'class' => [
				'compliance-exception-closure-date-toggle'
			]
		]);

        $out .= $this->FieldData->input($Field, [
        	'class' => [
        		'compliance-exception-closure-date'
        	]
        ]);

        $out .= $this->Html->scriptBlock("
			jQuery(function($) {
				$(\".compliance-exception-closure-date-toggle\").on(\"change\", function(e) {
					if ($(this).is(\":checked\")) {
						$(\".compliance-exception-closure-date-toggle\").attr(\"checked\", true);
						$(\".compliance-exception-closure-date\").attr(\"disabled\", \"disabled\");
					}
					else {
						$(\".compliance-exception-closure-date-toggle\").attr(\"checked\", false);
						$(\".compliance-exception-closure-date\").removeAttr(\"disabled\");
					}
				}).trigger(\"change\");
			});
		");

        return $out;
	}
}